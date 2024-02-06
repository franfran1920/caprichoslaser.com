<?php

class Class_Pi_Edd_Dynamic_Shipping_Method{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'dynamic_method';

   

    private $setting_key = 'edd_dynamic_methods';
    private $setting_key2 = 'edd_view_dynamic_methods_names';


    function __construct($plugin_name){
        $this->tab_name = __("Dynamic Method", 'pi-edd');
        $this->plugin_name = $plugin_name;
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        
        add_action('woocommerce_init', array($this,'shipping_zone_to_array'));

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }
        
        add_action($this->plugin_name.'_tab', array($this,'tab'),1);

        $this->settings = array(
            
            
            array('field'=>'pi_edd_dynamic_method_min_max'),
            
        );

        $this->settings2 = array(
            array('field'=>'pi_edd_view_shipping_method_system_name')
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

        foreach($this->settings2 as $setting2){
            register_setting( $this->setting_key2, $setting2['field']);
        }
    }

    function tab(){
        ?>
        <a class=" pi-side-menu  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-controls-skipforward"></span> <?php _e( $this->tab_name, 'pi-edd' ); ?> 
        </a>
        <?php
    }

    function tab_content(){
        $dynamic_min_max = get_option('pi_edd_dynamic_method_min_max', array());
        $dynamic_min_max = is_array($dynamic_min_max) ? $dynamic_min_max : array();
        $view_method_name = get_option('pi_edd_view_shipping_method_system_name', 0);
        ?>
            <form method="post" action="options.php"  class="pisol-setting-form">
            <?php settings_fields( $this->setting_key2 ); ?>
            <div class="my-2 p-3 bg-dark">
            <div class="row align-items-center">
                <div class="col-9"><label for="pi_edd_view_shipping_method_system_name" class="text-light">Enable this option to view Method name on checkout page (for below settings)<br><a href="https://youtu.be/FKeSkDLfSKI" target="_blank">Check out how this option will help you</a></label></div>
                <div class="col-1">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" value="1" <?php  checked($view_method_name, 1); ?> class="custom-control-input" name="pi_edd_view_shipping_method_system_name" id="pi_edd_view_shipping_method_system_name">
                        <label class="custom-control-label" for="pi_edd_view_shipping_method_system_name"></label>
                    </div>
                </div>
                <div class="col-2">
                <input type="submit" class="btn btn-primary btn-sm" value="Save" />
                </div>
            </div>
            </div>
            </form>
            <div class="alert alert-info my-3"><?php _e('Set Min Max shipping days for the dynamically added shipping methods <a href="https://www.youtube.com/watch?v=UVfrIsdO4q0&feature=emb_title" target="_blank">check out the video on how to use this feature</a>','pi-edd'); ?></div>
            <script>
                var pisol_edd_dynamic_methods_min_max = <?php echo count(array_values($dynamic_min_max)); ?>
            </script>
            <script type="text/template" id="method-template">
            <div>
            <div class="row py-3">
                <div class="col-3">
                    <input type="text" name="pi_edd_dynamic_method_min_max[{{count}}][name]" class="form-control" placeholder="Method name" required>
                </div>
                <div class="col-2">
                    <select type="text" name="pi_edd_dynamic_method_min_max[{{count}}][zone][]" class="form-control zone-selectwoo" required multiple="multiple">
                    <option value disabled>Select Zone</option>
                    <?php echo $this->shippingZones(); ?>
                    </select>
                </div>
                <div class="col-2">
                    <select type="text" name="pi_edd_dynamic_method_min_max[{{count}}][match]" class="form-control" required>
                    <option value disabled selected="true" >Matching type</option>
                    <option value="like">Like</option> 
                    <option value="exact">Exact</option> 
                    </select>
                </div>
                <div class="col-2">
                    <input type="number" step="1" name="pi_edd_dynamic_method_min_max[{{count}}][min_days]" class="form-control" placeholder="Minimum days" required title="min shipping days" min="1">
                </div>
                <div class="col-2">
                    <input type="number" step="1" name="pi_edd_dynamic_method_min_max[{{count}}][max_days]" class="form-control" placeholder="Maximum days" required title="max shipping days" min="1">
                </div>
                <div class="col-1">
                    <button class="delete-dynamic-method btn btn-secondary"><span class="dashicons dashicons-trash"></span></button>
                </div>
            </div>
            <div class="row py-3">
                <div class="col-4">
                    <input type="text" name="pi_edd_dynamic_method_min_max[{{count}}][shipping_cutoff_time]" class="form-control pisol-edd-dynamic-time-picker" placeholder="Pickup cutoff time" readonly>
                </div>
                <div class="col-2">
                <a class="btn btn-primary btn-sm pisol-edd-clear-time">&times;</a>
                </div>
            </div>
            <div class="row py-3 border-bottom">
                <div class="col-3">
                    <label><?php _e('Non working days for this shipping method','pi-edd'); ?></label>
                </div>
                <div class="col-3">
                <input type="checkbox" name="pi_edd_dynamic_method_min_max[{{count}}][overwrite_global_shipping_off_days]" class="my-2 pi_edd_dynamic_method_overwrite_shipping_off_days">
                </div>
                <div class="col-6 pi_days_of_week_container">
                    <select name="pi_edd_dynamic_method_min_max[{{count}}][pi_days_of_week][]" class="form-control pi_days_of_week" multiple="multiple" tabindex="-1" aria-hidden="true">
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

            <div class="row py-3 border-bottom">
                <div class="col-3">
                    <label><?php _e('Overwrite global Shipping holiday dates with shipping method holiday dates','pi-edd'); ?></label>
                </div>
                <div class="col-3">
                <input type="checkbox" name="pi_edd_dynamic_method_min_max[{{count}}][overwrite_global_shipping_off_dates]" class="my-2 pi_edd_dynamic_method_overwrite_shipping_off_dates">
                </div>
                <div class="col-6 pi_edd_holidays_container">
                    <strong>Shipping method holidays dates</strong>
                    <input name="pi_edd_dynamic_method_min_max[{{count}}][holiday_dates]" class="form-control holiday_dates pi-multiple-date-selector" >
                </div>
            </div>

            </div>
            </script>
            
            <a class="btn btn-primary" id="add-dynamic-method" href="javascript:void(0)"><?php _e('Add Dynamic Method','pi-edd'); ?></a>
            <form method="post" action="options.php"  class="pisol-setting-form">
            <?php settings_fields( $this->setting_key ); ?>
            <div id="dynamic-methods-container">
            <?php $this->savedValues($dynamic_min_max); ?>
            </div>
            <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
            </form>
        <?php
    }

    function shippingZones($selected = array()){
        $zones = $this->shipping_zone_to_array();
        if(!is_array($selected)) $selected = array();
        $options = '';
        $options .= '<option value="all" '.(in_array('all', $selected) ? ' selected="selected" ' : '').'>All zones</option>';
        foreach ($zones as $key => $zone){
            $options .= '<option value="'.$key.'" '.(self::in_array($key, $selected) ? ' selected="selected" ' : '').'>'.$zone.'</option>';
        }
        return $options;
    }

    static function in_array($key, $array){
        // as there is bug in php in_array for 0 comparison
        foreach($array as $val){
            if($key == 0){
                if($key == $val && $val != 'all') return true;
            }else{
                if($key == $val) return true;
            }
        }
        return false;
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

    function savedValues($values){
        $count = 0;
        foreach($values as $value){

            echo $this->savedValue($value, $count);
            $count++;
        }
    }

    function savedValue($value, $count){
        $name = isset($value['name']) ? $value['name'] : "";
        $zone = isset($value['zone']) ? $value['zone'] : "";
        $match = isset($value['match']) ? $value['match'] : "";
        $min_days = isset($value['min_days']) ? $value['min_days'] : "";
        $max_days = isset($value['max_days']) ? $value['max_days'] : "";
        $shipping_cutoff_time = isset($value['shipping_cutoff_time']) ? $value['shipping_cutoff_time'] : "";
        $days_overwrite = isset($value['overwrite_global_shipping_off_days']) && !empty($value['overwrite_global_shipping_off_days']) ? ' checked="checked" ' : ''; 
        $pi_days_of_week = isset($value['pi_days_of_week']) && is_array($value['pi_days_of_week']) ? $value['pi_days_of_week'] : array();

        $date_overwrite = isset($value['overwrite_global_shipping_off_dates']) && !empty($value['overwrite_global_shipping_off_dates']) ? ' checked="checked" ' : ''; 
        $holiday_dates = isset($value['holiday_dates']) ? $value['holiday_dates'] : '';

        $html = '
        <div>
        <div class="row py-3">
                <div class="col-3">
                    <input type="text" name="pi_edd_dynamic_method_min_max['.$count.'][name]" class="form-control" placeholder="Method name" value="'.esc_attr($name).'" required>
                </div>
                <div class="col-2">
                    <select type="text" name="pi_edd_dynamic_method_min_max['.$count.'][zone][]" class="form-control zone-selectwoo" required multiple="multiple" >
                    <option value disabled>Select Zone</option>
                    '.$this->shippingZones($zone).'
                    </select>
                </div>
                <div class="col-2">
                    <select type="text" name="pi_edd_dynamic_method_min_max['.$count.'][match]" class="form-control" required>
                    <option value disabled selected="true" >Matching type</option>
                    <option value="like" '.selected('like', $match, false).'>Like</option> 
                    <option value="exact" '.selected('exact', $match, false).'>Exact</option> 
                    </select>
                </div>
                <div class="col-2">
                    <input type="number" step="1" name="pi_edd_dynamic_method_min_max['.$count.'][min_days]" class="form-control" placeholder="Minimum days" value="'.esc_attr($min_days).'" required title="min shipping days" min="1">
                </div>
                <div class="col-2">
                    <input type="number" step="1" name="pi_edd_dynamic_method_min_max['.$count.'][max_days]" class="form-control" placeholder="Maximum days" value="'.esc_attr($max_days).'" required title="max shipping days" min="1">
                </div>
                <div class="col-1">
                    <button class="delete-dynamic-method btn btn-secondary"><span class="dashicons dashicons-trash"></span></button>
                </div>
            </div>
            <div class="row py-3">
            <div class="col-4 form-group">
                <input type="text" name="pi_edd_dynamic_method_min_max['.$count.'][shipping_cutoff_time]" class="form-control pisol-edd-dynamic-time-picker" placeholder="Pickup cutoff time" value="'.esc_attr($shipping_cutoff_time).'" readonly> 
            </div>
            <div class="col-2">
            <a class="btn btn-primary btn-sm pisol-edd-clear-time">&times;</a>
            </div>
            </div>
            <div class="row py-3 border-bottom">
                <div class="col-3">
                    <label>'.__("Non working days for this shipping method","pi-edd").'</label>
                </div>
                <div class="col-3">
                <input type="checkbox" name="pi_edd_dynamic_method_min_max['.$count.'][overwrite_global_shipping_off_days]" class="my-2 pi_edd_dynamic_method_overwrite_shipping_off_days"  '.$days_overwrite.' >
                </div>
                <div class="col-6 pi_days_of_week_container">
                    <select name="pi_edd_dynamic_method_min_max['.$count.'][pi_days_of_week][]" class="form-control pi_days_of_week" multiple="multiple" tabindex="-1" aria-hidden="true">
                        <option value="1" '.(in_array(1, $pi_days_of_week) ? ' selected="selected" ' : '').' >Monday</option>
                        <option value="2" '.(in_array(2, $pi_days_of_week) ? ' selected="selected" ' : '').'>Tuesday</option>
                        <option value="3"  '.(in_array(3, $pi_days_of_week) ? ' selected="selected" ' : '').'>Wednesday</option>
                        <option value="4" '.(in_array(4, $pi_days_of_week) ? ' selected="selected" ' : '').'>Thursday</option>
                        <option value="5" '.(in_array(5, $pi_days_of_week) ? ' selected="selected" ' : '').'>Friday</option>
                        <option value="6" '.(in_array(6, $pi_days_of_week) ? ' selected="selected" ' : '').'>Saturday</option>
                        <option value="7" '.(in_array(7, $pi_days_of_week) ? ' selected="selected" ' : '').'>Sunday</option>
                    </select>
                </div>
            </div>
            <div class="row py-3 border-bottom">
                <div class="col-3">
                    <label>'. __('Overwrite global Shipping holiday dates with shipping method holiday dates','pi-edd').'</label>
                </div>
                <div class="col-3">
                <input type="checkbox" name="pi_edd_dynamic_method_min_max['.$count.'][overwrite_global_shipping_off_dates]" class="my-2 pi_edd_dynamic_method_overwrite_shipping_off_dates"  '.$date_overwrite.' >
                </div>
                <div class="col-6 pi_edd_holidays_container">
                    <strong>Shipping method holidays dates</strong>
                    <input name="pi_edd_dynamic_method_min_max['.$count.'][holiday_dates]" class="form-control holiday_dates pi-multiple-date-selector" value="'.esc_attr($holiday_dates).'" >
                </div>
            </div>
            </div>
           ';

            return $html;
    }

   
}

