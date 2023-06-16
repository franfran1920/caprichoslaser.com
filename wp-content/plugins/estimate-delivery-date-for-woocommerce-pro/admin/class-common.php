<?php

class pi_edd_admin_common{

    public static function getMinMax($method, $method_name){
        $min_max = array();
        /** option format is like this woocommerce_free_shipping_23_settings */
            $option_name = "woocommerce_".$method_name."_".$method."_settings";
            $present_setting = get_option($option_name);
            $min_max['min_days'] = isset($present_setting['min_days']) ? $present_setting['min_days'] : "";
            $min_max['max_days'] = isset($present_setting['max_days']) ? $present_setting['max_days'] : "";
            
            $min_max['cutoff_overwrite'] = isset($present_setting['cutoff_overwrite']) ? $present_setting['cutoff_overwrite'] : "";
            $min_max['shipping_cutoff_time'] = isset($present_setting['shipping_cutoff_time']) ? $present_setting['shipping_cutoff_time'] : "";
            $min_max['overwrite_global_shipping_off_days'] = isset($present_setting['overwrite_global_shipping_off_days']) && !empty($present_setting['overwrite_global_shipping_off_days']) ? 1 : "";

            $min_max['pi_days_of_week'] = isset($present_setting['pi_days_of_week']) && is_array($present_setting['pi_days_of_week']) ? $present_setting['pi_days_of_week'] : array();

            $min_max['overwrite_global_shipping_off_dates'] = isset($present_setting['overwrite_global_shipping_off_dates']) && !empty($present_setting['overwrite_global_shipping_off_dates']) ? 1 : "";

            $min_max['holiday_dates'] = isset($present_setting['holiday_dates']) ? $present_setting['holiday_dates'] : '';
            
        return $min_max;
    }

    public static function formatedDate($date){
        if(empty($date)) return null;
        
        $date_format =  Pisol_edd_get_option('pi_general_date_format','Y/m/d');
        $original_timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
        $date = str_replace( '/', '-', $date );
        $translated_date = date_i18n($date_format, strtotime($date));
        date_default_timezone_set( $original_timezone );
        
        return $translated_date;
    }
    
}