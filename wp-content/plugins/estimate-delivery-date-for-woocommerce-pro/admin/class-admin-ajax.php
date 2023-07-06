<?php

class Pisol_edd_admin_ajax{
    function __construct(){
        add_action('wp_ajax_pisol_update_method', array($this, 'update_method'));
    }

    function update_method(){
        $require_capability = pisol_edd_access_control::getCapability();
        if(current_user_can($require_capability) ) {
            $min_days = isset($_POST['min_days']) ? (int)$_POST['min_days'] : "";
            $max_days = isset($_POST['max_days']) ? (int)$_POST['max_days'] : "";
            $zone = isset($_POST['zone']) ? (int)$_POST['zone'] : "";
            $method = isset($_POST['method']) ? (int)$_POST['method'] : "";
            $method_name = isset($_POST['method_name']) ? $_POST['method_name'] : "";

            $method_title = filter_input(INPUT_POST, 'method_title');

            
            $shipping_cutoff_time = isset($_POST['shipping_cutoff_time']) ? sanitize_text_field($_POST['shipping_cutoff_time']) : '';

            $overwrite_global_shipping_off_days = isset($_POST['overwrite_global_shipping_off_days']) && !empty($_POST['overwrite_global_shipping_off_days']) ? 1 : '';

            $pi_days_of_week = isset($_POST['pi_days_of_week']) && is_array($_POST['pi_days_of_week']) ? $_POST['pi_days_of_week'] : array();

            $overwrite_global_shipping_off_dates = isset($_POST['overwrite_global_shipping_off_dates']) && !empty($_POST['overwrite_global_shipping_off_dates']) ? 1 : '';

            $pi_edd_holidays = isset($_POST['holiday_dates']) ? sanitize_text_field($_POST['holiday_dates']) : '';
            
            $this->saveMinMax($min_days, $max_days, $method, $method_name, $shipping_cutoff_time, $overwrite_global_shipping_off_days, $pi_days_of_week, $overwrite_global_shipping_off_dates, $pi_edd_holidays);
           
            
            echo "Settings updated successfully for <strong>{$method_title}</strong> ";
            die;
        }else{
            echo "You don't have access to modify this setting ";
            die;
        }
    }

    function saveMinMax($min_days, $max_days, $method, $method_name, $shipping_cutoff_time, $overwrite_global_shipping_off_days, $pi_days_of_week, $overwrite_global_shipping_off_dates, $pi_edd_holidays){
         /** option format is like this woocommerce_free_shipping_23_settings */
            $option_name = "woocommerce_".$method_name."_".$method."_settings";
            $present_setting = get_option($option_name);
            $present_setting['min_days'] = $min_days;
            $present_setting['max_days'] = $max_days;

            $present_setting['shipping_cutoff_time'] = $shipping_cutoff_time;
            $present_setting['overwrite_global_shipping_off_days'] = $overwrite_global_shipping_off_days;

            $present_setting['pi_days_of_week'] = $pi_days_of_week;

            $present_setting['overwrite_global_shipping_off_dates'] = $overwrite_global_shipping_off_dates;

            $present_setting['holiday_dates'] = $pi_edd_holidays;

            update_option($option_name, $present_setting);
    }
}

new Pisol_edd_admin_ajax();