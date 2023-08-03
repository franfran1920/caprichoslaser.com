<?php

class Class_Pi_Edd_Option{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'basic_setting';

   

    private $setting_key = 'pi_edd_basic_setting';

    private $shipping_zones =array();

    private $date_format = array(); 
    
   

    function __construct($plugin_name){
        $this->tab_name = __("Basic setting",'pi-edd');
        $this->plugin_name = $plugin_name;

        $this->date_format = $this->date_format();
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        

        add_action($this->plugin_name.'_tab', array($this,'tab'),3);

        add_action('woocommerce_init', array($this,'shipping_zone_to_array'));
        
        
    }

    function shipping_zone_to_array(){
        if(!is_admin()) return;
       // WC();
       if($this->this_tab == $this->active_tab){
            $zones = WC_Shipping_Zones::get_zones('json');
            $this->shipping_zones = $this->zone_to_array($zones);
       }else{
            $this->shipping_zones = array();
       }
        
        
        $this->settings = array(
            array('field'=>'pi_edd_enable_estimate', 'label'=>__('Enable estimate','pi-edd'),'type'=>'switch', 'default'=> 1,   'desc'=>__('Using this you can disable the estimate without disabling the plugin','pi-edd')),

            array('field'=>'pi_defaul_shipping_zone', 'label'=>__('Default shipping zone','pi-edd'),'type'=>'select', 'default'=> 0, 'value'=>$this->shipping_zones,  'desc'=>__('This shipping zone will be used as default to calculate the estimated delivery time, till user select there shipping address <strong>This is use full when WooCommerce Cant detect user shipping zone based on their IP, so instead of showing no date till user add full address, this will show the estimate based on the default zone, as estimate will change as the user select his zone</strong>','pi-edd')),

            array('field'=>'pi_edd_default_shipping_method', 'label'=>__('Default shipping method for estimate calculation','pi-edd'),'type'=>'text','desc'=>__('Estimate will be shown based on this shipping method till user has not made any selection ','pi-edd')),


            array('field'=>'pi_general_range', 'label'=>__('Range of delivery date','pi-edd'),'type'=>'switch', 'default'=>0,   'desc'=>__('Show a range of delivery date if you have more then one shipping class for that shipping zone (If the 2 dates in the range are coming same then it will show the single date estimate)','pi-edd')),

            array('field'=>'pi_edd_min_max', 'label'=>__('Show the best/worst estimate delivery time','pi-edd'),'type'=>'select', 'default'=>'max', 'value'=>array('min'=>'Best estimate', 'max'=>'Worst estimate'),  'desc'=>__('Select the shipping method with minimum or maximum delivery time','pi-edd')),

            array('field'=>'pi_general_date_format', 'label'=>__('Date format','pi-edd'),'type'=>'select', 'default'=>'Y/m/d',   'desc'=>__('Date format for the estimate date','pi-edd'), 'value'=>$this->date_format),
            
            /*array('field'=>'pi_estimate_in_order_detail', 'label'=>__('Show estimated date in order detail','pi-edd'),'type'=>'switch', 'default'=>0,   'desc'=>'Show estimated date range in order detail, order email'),*/

            array('field'=>'pi_shipping_breakup_time', 'label'=>__('Last shipping time of the day (order coming after this time will be shipped next date)','pi-edd'),'type'=>'text','desc'=>__('If the order is placed before this time then you can ship the product today, and so today will be counted in calculating the estimate, and if the order is placed after this time then the shipping can be done next date, so today will not be counted for estimate calculation, if you leave this blank counting will be done from next date','pi-edd')),

            array('field'=>'pi_edd_estimate_for_back_order_product', 'label'=>__('Show estimate for product on back order','pi-edd'),'type'=>'switch', 'default'=> 1,   'desc'=>__('If you don\'t want to show estimate for the product on back order you can do using this option','pi-edd')),

            array('field'=>'pi_edd_default_global_estimate_status', 'label'=>__('Global setting of enabled/disabled for all product','pi-edd'),'type'=>'select', 'default'=> 'enable',   'desc'=>__('You can make estimate as enabled or disabled for all the products, then you can go inside each of the product and overwrite the global enabled or disabled option','pi-edd'), 'value' => array('enable'=>__('Enabled','pi-edd'), 'disable'=> __('Disabled', 'pi-edd'))),

            array('field'=>'pi_edd_estimate_block_support', 'label'=>__('Enable this if your Cart or Checkout page is made using WooCommerce blocks','pi-edd'),'type'=>'switch', 'default'=> 0,   'desc'=>__('WooCommerce block are not supported directly you need to enable this setting to enable support for them','pi-edd')),

            array('field'=>'pi_days_of_week'),
            array('field'=>'pi_shop_closed_days')
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

    function zone_to_array($zones){
        $select[0] = "Select shipping zone";
        foreach($zones as $zone){
            $select[$zone['id']] =  $zone['zone_name'];
        }
        return $select;
    }


    function register_settings(){   

        foreach($this->settings as $setting){
            register_setting( $this->setting_key, $setting['field']);
        }
    
    }

    function tab(){
        ?>
        <a class=" pi-side-menu  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-admin-settings"></span> <?php _e( $this->tab_name, 'http2-push-content' ); ?> 
        </a>
        <?php
    }

    function tab_content(){
        if(count($this->shipping_zones) <= 1){
            echo '<div class="alert alert-primary mt-2">You must have shipping zones to use this setting, so create shipping zone in WooCommerce <a href="'.admin_url("admin.php?page=wc-settings&tab=shipping").'">Click here to set shipping zone</div>';
            return;
        }
       ?>
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <?php $pi_days_of_week = get_option("pi_days_of_week",array()); 
        $pi_days_of_week = is_array($pi_days_of_week) ? $pi_days_of_week : array();

        $pi_shop_closed_days = get_option("pi_shop_closed_days",array()); 
        $pi_shop_closed_days = is_array($pi_shop_closed_days) ? $pi_shop_closed_days : array();
        ?>
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_edd($setting, $this->setting_key);
            }
        ?>
        <div class="row py-4 border-bottom align-items-center bg-primary text-light">
            <div class="col-12">
            <h2 class="mt-0 mb-0 text-light font-weight-light h5">Select days to skip in counting of holidays <br>(most shipping don't work on weekends so you can select Saturday, Sunday and all the saturday and sundays will not be counted in calculating estimated shipping date)</h2>
            </div>
        </div>
        <div class="row py-4 border-bottom align-items-center ">
            <div class="col-12 col-md-5">
                <label class="h6 mb-0" for="pi_days_of_week">Select days of week when your shipping is closed:<br><span class="text-primary">E.g: If your shipping company is closed on say Saturday and Sunday then add that in this field</span></label>
            </div>
            <div class="col-12 col-md-7">
                <select name="pi_days_of_week[]" id="pi_days_of_week" class="form-control" multiple="multiple">
                    <option value="1" <?php echo (in_array(1, $pi_days_of_week) ? ' selected="selected" ': ''); ?>>Monday</option>
                    <option value="2" <?php echo (in_array(2, $pi_days_of_week) ? ' selected="selected" ': ''); ?>>Tuesday</option>
                    <option value="3" <?php echo (in_array(3, $pi_days_of_week) ? ' selected="selected" ': ''); ?>>Wednesday</option>
                    <option value="4" <?php echo (in_array(4, $pi_days_of_week) ? ' selected="selected" ': ''); ?>>Thursday</option>
                    <option value="5" <?php echo (in_array(5, $pi_days_of_week) ? ' selected="selected" ': ''); ?>>Friday</option>
                    <option value="6" <?php echo (in_array(6, $pi_days_of_week) ? ' selected="selected" ': ''); ?>>Saturday</option>
                    <option value="7" <?php echo (in_array(7, $pi_days_of_week) ? ' selected="selected" ': ''); ?>>Sunday</option>
                </select>
            </div>
        </div>
        <div class="row py-4 border-bottom align-items-center ">
            <div class="col-12 col-md-5">
                <label class="h6 mb-0" for="pi_shop_closed_days">Select days of week when your Shop is closed <br><span class="text-warning">(This will only affect your shipping estimate if the shipping start date  holiday for the Shop)</span><br><span class="text-primary">E.g: If your shop is closed on say Saturday and sunday then add that in this field</span>:</label>
            </div>
            <div class="col-12 col-md-7">
                <select name="pi_shop_closed_days[]" id="pi_shop_closed_days" class="form-control" multiple="multiple">
                    <option value="1" <?php echo (in_array(1, $pi_shop_closed_days) ? ' selected="selected" ': ''); ?>>Monday</option>
                    <option value="2" <?php echo (in_array(2, $pi_shop_closed_days) ? ' selected="selected" ': ''); ?>>Tuesday</option>
                    <option value="3" <?php echo (in_array(3, $pi_shop_closed_days) ? ' selected="selected" ': ''); ?>>Wednesday</option>
                    <option value="4" <?php echo (in_array(4, $pi_shop_closed_days) ? ' selected="selected" ': ''); ?>>Thursday</option>
                    <option value="5" <?php echo (in_array(5, $pi_shop_closed_days) ? ' selected="selected" ': ''); ?>>Friday</option>
                    <option value="6" <?php echo (in_array(6, $pi_shop_closed_days) ? ' selected="selected" ': ''); ?>>Saturday</option>
                    <option value="7" <?php echo (in_array(7, $pi_shop_closed_days) ? ' selected="selected" ': ''); ?>>Sunday</option>
                </select>
            </div>
        </div>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }

    function date_format(){
        $date = array();
        $date['Y/m/d'] = date('Y/m/d'); 
        $date['d/m/Y'] = date('d/m/Y');
        $date['d/m'] = date('d/m');
        $date['m/d/y'] = date('m/d/y');
        $date['Y-m-d'] = date('Y-m-d'); 
        $date['d-m-Y'] = date('d-m-Y');
        $date['m-d-y'] = date('m-d-y');
        $date['Y.m.d'] = date('Y.m.d'); 
        $date['d.m.Y'] = date('d.m.Y');
        $date['m.d.y'] = date('m.d.y');
        $date["M j, Y"] = date("M j, Y");
        $date["M jS, Y"] = date("M jS, Y");
        $date["jS F"] = date("jS F");
        $date["j. F"] = date("j. F");
        $date["j F"] = date("j F");
        $date["l j. F"] = date("l j. F");
        $date["l jS. F"] = date("l jS. F");
        $date["l, F j"] = date("l, F j");
        $date["l, F jS"] = date("l, F jS");
        $date["l j F"] = date("l j F");
        $date["l jS F"] = date("l jS F");
        $date["F jS"] = date("F jS");
        $date["jS M"] = date("jS M");
        $date["M jS"] = date("M jS");
        $date["M d"] = date("M d");
        $date["F d, Y"] = date("F d, Y");

        foreach ($date as $key => $val){
            $date[$key] = $val.' - ( '.$key.' )';
        }
        return $date;
    }
}

