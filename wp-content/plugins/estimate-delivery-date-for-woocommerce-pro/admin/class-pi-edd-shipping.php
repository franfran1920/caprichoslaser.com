<?php

class Class_Pi_Edd_Shipping{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'default';

    

    private $setting_key = 'shipping_setting';


    function __construct($plugin_name){
        $this->tab_name = __('Shipping days','pi-edd');
        $this->plugin_name = $plugin_name;
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        
        add_action('woocommerce_init', array($this,'shipping_zone_to_array'));

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        
        add_action($this->plugin_name.'_tab', array($this,'tab'),1);

        $this->settings = array(
            
            array('field'=>'pi_edd_default_min_shipping_days', 'label'=>__('Default minimum days','pi-edd'),'type'=>'number', 'default'=> '','min'=>1,'step'=>1, 'desc'=>__('Enter the default minimum number of days required for shipping. This will be used if shipping days are not set for a shipping method.','pi-edd')),
            array('field'=>'pi_edd_default_max_shipping_days', 'label'=>__('Default maximum days','pi-edd'),'type'=>'number', 'default'=> '','min'=>1,'step'=>1, 'desc'=>__('Enter the default maximum number of days required for shipping. This will be used if shipping days are not set for a shipping method.','pi-edd')),
            
        );
        $this->register_settings();
        
        if(PISOL_EDD_DELETE_SETTING){
            $this->delete_settings();
        }   

       

    }

   
    
    function delete_settings(){
        foreach($this->settings as $setting){
            delete_option( $setting['field'] );
        }
    }


    function register_settings(){   

        foreach($this->settings as $setting){
            register_setting( $this->setting_key, $setting['field']);
        }
    
    }

    function tab(){
        ?>
        <a class=" pi-side-menu  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-calendar-alt"></span> <?php _e( $this->tab_name, 'pi-edd' ); ?> 
        </a>
        <?php
    }

    function tab_content(){
        ?>
         <div class="alert alert-info mt-3">
            <strong><?php _e('Set minimum and maximum shipping days for each shipping method in each shipping zone. If not set, estimated delivery will use default values. If default is not configured, no delivery estimate will be shown.','pi-edd'); ?></strong>
         </div>
         <div class="pisol-error mt-3">

         </div>
            <div class="row px-3">
                <div class="col-12 col-sm-4 ">
                    <h2 class="block p-2 bg-dark text-light text-center h6 rounded-top mb-0"><?php _e('Step 1<br> <u>Select Shipping Zone</u>','pi-edd'); ?></h2>
                    <div class="shadow p-2 rounded-bottom">
                    <?php $this->shippingZones(); ?>
                    </div>
                </div>
                <div class="col-12 col-sm-4 ">
                    <h2 class="block p-2 bg-dark text-light text-center h6 rounded-top mb-0"><?php _e('Step 2<br> <u>Choose Shipping Method</u>','pi-edd'); ?></h2>
                    <div class="shadow p-2 rounded-bottom">
                    <?php $this->shippingMethods(); ?>
                    </div>
                </div>
                <div class="col-12 col-sm-4 ">
                    <h2 class="block p-2 bg-dark text-light text-center h6 rounded-top mb-0"><?php _e('Step 3<br> <u>Set Shipping Days</u>','pi-edd'); ?></h2>
                    <div class="shadow p-2 rounded-bottom">
                    <form id="pisol-min-max-form" style="display:none;">
                    <h5 id="pisol-form-method-title"></h5>
                    <div class="form-group row align-items-center">
                        <label class="col-sm-6 "><?php _e('Min days','pi-edd'); ?> <?php pisol_help::tooltip(__('Enter the minimum number of days required for shipping through this shipping method.', 'pi-edd')); ?></label>
                        <div class="col-sm-6">
                        <input required type="number" name="min_days" class="form-control my-2" min="0" id="pisol-form-minimum">
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-sm-6 "><?php _e('Max days','pi-edd'); ?> <?php pisol_help::tooltip(__('Enter the maximum number of days required for shipping through this shipping method.', 'pi-edd')); ?></label>
                        <div class="col-sm-6">
                        <input required type="number" name="max_days" class="form-control my-2" min="0" id="pisol-form-maximum">
                        <input type="hidden" name="zone" id="pisol-form-zone">
                        <input type="hidden" name="method" id="pisol-form-method">
                        <input type="hidden" name="method_name" id="pisol-form-method-name">
                        </div>
                    </div>
                    <div class="form-group row align-items-center" id="pisol_cutoff_time_container">
                        <label class="col-sm-12 "><?php _e('Cutoff Time','pi-edd'); ?>
                        <?php pisol_help::tooltip(__('Enter the pickup cutoff time for this shipping method. If left blank, the global cutoff time will be used.', 'pi-edd')); ?>
                        </label>
                        <div class="col-sm-9">
                        <input required type="text" name="shipping_cutoff_time" class="form-control my-2" id="pisol-form-shipping_cutoff_time" readonly>
                        </div>
                        <div class="col-sm-3">
                        <a class="btn btn-primary btn-sm" id="pisol-form-clear-time">&times;</a>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-9 " for="pisol-form-overwrite_global_shipping_off_days"><?php _e('Non-Working Days','pi-edd'); ?> <?php pisol_help::tooltip(__('Specify the non-working days for this shipping method.', 'pi-edd')); ?>
                        </label>
                        <div class="col-sm-3">
                        <input type="checkbox" name="overwrite_global_shipping_off_days" class="my-2" id="pisol-form-overwrite_global_shipping_off_days">
                        </div>
                    </div>

                    <div class="form-group row align-items-center" id="non-working-days">
                        <div class="col-sm-12">
                        <select name="pi_days_of_week[]" id="pi_days_of_week" class="form-control" multiple="multiple" tabindex="-1" aria-hidden="true">
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                            <option value="7">Sunday</option>
                        </select>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-9 " for="pisol-form-overwrite_global_shipping_off_dates"><?php _e('Overwrite Holidays','pi-edd'); ?> <?php pisol_help::tooltip(__('Choose whether to overwrite the global shipping holiday dates with the holiday dates for this shipping method. If not selected, the shipping method holiday dates will be merged with the global holiday dates.', 'pi-edd')); ?>
                        </label>
                        <div class="col-sm-3">
                        <input type="checkbox" name="overwrite_global_shipping_off_dates" class="my-2" id="pisol-form-overwrite_global_shipping_off_dates">
                        </div>
                    </div>

                    <div class="form-group row align-items-center" id="non-working-dates">
                        <div class="col-sm-12">
                        <label for="holiday_dates"><?php _e('Holidays dates','pi-edd'); ?> <?php pisol_help::tooltip(__('Specify the holidays dates for this shipping method.', 'pi-edd')); ?>
                        </label>
                        </div>
                        <div class="col-sm-12">
                        <input name="holiday_dates" id="holiday_dates" class="form-control holiday_dates pi-multiple-date-selector" >
                        </div>
                    </div>
                    
                    <input type="submit" class="btn btn-primary btn-block btn-md" value="Save">
                    </form>
                    </div>
                </div>
            </div>
        <?php

        $this->setDefaultShippingDays();
    }

    function setDefaultShippingDays(){
        include_once 'partials/default-shipping-days.php';
    }

    function shippingZones(){
        $zones = $this->shipping_zone_to_array();
        foreach ($zones as $key => $zone){
            echo '<a id="pi_zone_'.$key.'" href="javascript:void(0);" class="pisol-shipping-zone btn btn-primary btn-sm btn-block my-2" data-zone="'.$key.'">'.$zone.'</a>';
        }
    }

    function shipping_zone_to_array(){
         if(!is_admin()) return;

         $zones = WC_Shipping_Zones::get_zones();
         
         $this->shipping_zones = $this->zone_to_array($zones);

         $non_covered_zone =  WC_Shipping_Zones::get_zone_by("zone_id",0);
         if(is_object($non_covered_zone)){
            $non_covered_zone_name = $non_covered_zone->get_zone_name();
            $non_covered_zone_id = $non_covered_zone->get_id();
            if(!empty($non_covered_zone_name)){
                $this->shipping_zones[$non_covered_zone_id] =  $non_covered_zone_name;
            }
         }
         return $this->shipping_zones;
    }

    function zone_to_array($zones){
        $select = array();
        foreach($zones as $zone){
            $zone_obj = new WC_Shipping_Zone($zone['zone_id']);
            $methods = $zone_obj->get_shipping_methods(true);
            if(count($methods) > 0){
                $select[$zone['zone_id']] =  $zone['zone_name'];
            }
        }
        return $select;
    }

    function shippingMethods(){
        
        $zones = $this->shipping_zone_to_array();
        foreach ($zones as $zone_id => $zone){
            $methods = $this->getShippingMethod($zone_id);
            foreach($methods as $method){
                $this->method($method, $zone_id);
            }
        }
    }

    function getShippingMethod($zone_id){
        $zone_obj = new WC_Shipping_Zone($zone_id);
        $methods = $zone_obj->get_shipping_methods(true);
        return $methods;
    }

    function method($method, $zone_id){
        //print_r($method);
        $days = pi_edd_admin_common::getMinMax($method->instance_id, $method->id);
        $min_days = isset($days['min_days']) ? $days['min_days'] : '';
        $max_days = isset($days['max_days']) ? $days['max_days'] : '';
        $cutoff_overwrite = isset($days['cutoff_overwrite']) && !empty($days['cutoff_overwrite']) ? 1 : '';
        $shipping_cutoff_time = isset($days['shipping_cutoff_time']) ? $days['shipping_cutoff_time'] : '';
        $overwrite_global_shipping_off_days = isset($days['overwrite_global_shipping_off_days']) && !empty($days['overwrite_global_shipping_off_days']) ? 1 : '';

        $pi_days_of_week = isset($days['pi_days_of_week']) && is_array($days['pi_days_of_week']) ? $days['pi_days_of_week'] : array();

        $overwrite_global_shipping_off_dates = isset($days['overwrite_global_shipping_off_dates']) && !empty($days['overwrite_global_shipping_off_dates']) ? 1 : '';

        $holiday_dates = isset($days['holiday_dates']) ? $days['holiday_dates'] : '';

        echo '<a href="javascript:void(0)" id="pisol-method-'.$method->instance_id.'" data-zone="'.$zone_id.'" style="display:none; " data-method="'.$method->instance_id.'" class="pi_zone_method_'.$zone_id.' pisol-shipping-method btn btn-secondary btn-sm btn-block my-2" data-minimum="'.$min_days.'" data-maximum="'.$max_days.'" data-method_name="'.$method->id.'" data-method_title="'.esc_attr($method->title).'"  data-cutoff_overwrite="'.esc_attr($cutoff_overwrite).'" data-shipping_cutoff_time="'.esc_attr($shipping_cutoff_time).'" data-overwrite_global_shipping_off_days="'.esc_attr($overwrite_global_shipping_off_days).'" data-pi_days_of_week="'.esc_attr(wp_json_encode($pi_days_of_week)).'" data-overwrite_global_shipping_off_dates="'.esc_attr($overwrite_global_shipping_off_dates).'" data-holiday_dates="'.esc_attr($holiday_dates).'">'.$method->title.'</a>';
    }

   
}

