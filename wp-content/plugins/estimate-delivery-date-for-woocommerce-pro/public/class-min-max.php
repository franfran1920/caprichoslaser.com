<?php
/**
 * $value_of = min_days|max_days|holiday_overwrite|holidays
 */

class pisol_min_max_holidays{

    function __construct($shipping_method_name, $zone_id = false){
        $this->method_name = $shipping_method_name;
        $this->method_id = $this->methodId();
        $this->method_instance_id = $this->methodInstanceId();

        $this->zone_id = $zone_id;
    }

    function methodId(){
        $method = explode( ":", $this->method_name );

        if(isset($method[0])){
            return $method[0];
        }else{
            return null;
        } 
    }

    function methodInstanceId(){
        $method = explode( ":", $this->method_name );

        if(isset($method[1])){
            return $method[1];
        }else{
            return null;
        } 
    }

    static function getMinMaxHolidaysValues($shipping_method_name, $zone_id = false){
        $obj = new self( $shipping_method_name, $zone_id );
        $values = array();

        $values['min_days'] = $obj->getValue('min_days');
        $values['max_days'] = $obj->getValue('max_days');

        $holiday_overwrite = $obj->getValue('overwrite_global_shipping_off_days');
        $values['holiday_overwrite'] = !empty($holiday_overwrite) ? true : false;

        $holidays = $obj->getValue('pi_days_of_week');
        $values['holidays'] = $holidays;

        $overwrite_global_shipping_off_dates = $obj->getValue('overwrite_global_shipping_off_dates');
        $values['overwrite_global_shipping_off_dates'] = !empty($overwrite_global_shipping_off_dates) ? true : false;

        $holiday_dates = $obj->getValue('holiday_dates');
        $values['holiday_dates'] = (empty($holiday_dates) || !is_array($holiday_dates)) ? array() : $holiday_dates;

       
        $shipping_cutoff_time = $obj->getValue('shipping_cutoff_time');
        $values['shipping_cutoff_time'] = empty($shipping_cutoff_time) ? '' : $shipping_cutoff_time;

        $values['shipping_method'] = $shipping_method_name;
        $values['zone_id'] = $zone_id;

        $values = apply_filters('pisol_edd_shipping_method_setting_filter', $values, $shipping_method_name, $zone_id);

        $values = self::setDefaultShippingDays($values, $shipping_method_name, $zone_id);

        $values = self::removeEstimateForMethod($values, $shipping_method_name, $zone_id);

        return $values;
    }

    static function setDefaultShippingDays($values, $shipping_method_name, $zone_id){
        if(empty($values['min_days']) || empty($values['max_days'])){
            $def_min = Pisol_edd_get_option('pi_edd_default_min_shipping_days', null);
            $def_max = Pisol_edd_get_option('pi_edd_default_max_shipping_days', null);

            if(filter_var($def_min, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1))) !== false && filter_var($def_min, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1))) !== false && $def_max >= $def_min){
                $values['min_days'] = $def_min;
                $values['max_days'] = $def_max;
            }
            $values = apply_filters('pisol_edd_default_shipping_days', $values, $shipping_method_name, $zone_id);
        }
        return $values;
    }

    static function removeEstimateForMethod($values, $shipping_method_name, $zone_id){
        $dont_show_for_methods = self::dontShowForMethods($zone_id);

        if(empty($dont_show_for_methods) || !is_array($dont_show_for_methods)) return $values;

        if(in_array($shipping_method_name, $dont_show_for_methods)) {
            $values['min_days'] = null;
            $values['max_days'] = null;
        }

        return $values;
    }

    static function dontShowForMethods($zone_id){
        $list = Pisol_edd_get_option('pi_edd_dont_show_estimate_for_method', '');

        $list_array = array();

        if(!empty($list)){
            $list_array = explode(',', $list);
            $list_array = array_map('trim', $list_array);
        }

        return apply_filters('pi_edd_list_of_estimate_disabled_method', $list_array, $zone_id);
    }

    function getValue($value_of){
        $value = $this->getValueNormalMethod($value_of);
        if(empty($value)){
            $value = $this->getValueFallBackMethod($value_of);
        }
        return $value;
    }

    function getValueNormalMethod($value_of){
        $option_name = "woocommerce_".$this->method_id."_".$this->method_instance_id."_settings";
        $present_setting = get_option($option_name, false);
        if($present_setting !== false){
            if( isset($present_setting[ $value_of ])){
                if($value_of == 'holiday_dates'){
                    return self::getHolidayDates($present_setting[ $value_of ]);
                }
                return $present_setting[ $value_of ];
            }
        }
        return null;
    }

    static function getHolidayDates($holiday_dates){
        if(is_array($holiday_dates)) return array_unique($holiday_dates);

        $dates = explode(':', $holiday_dates);

        return array_unique($dates);
    }

    function getValueFallBackMethod($value_of){
        if($this->zone_id === false){
            $shipping_zone = Pi_edd_shipping_zone::getShippingZone();
            if(is_object( $shipping_zone )){
                $this->zone_id = $shipping_zone->get_id();
            }else{
                $this->zone_id = 0;
            }
        }
        /*
        $custom_method = array(
            '12' => array(
                'pisol_extended_flat_shipping:929' => array(
                    'min_days'=>0,
                    'max_days'=>2,
                    'holiday_overwrite'=>1,
                    'holidays'=>array(1,2)
                ),
                'local_pickup:46' => array(
                    'min_days'=>0,
                    'max_days'=>2,
                    'holiday_overwrite'=>1,
                    'holidays'=>array(1,2)
                )
            )
        );

        if(isset($custom_method[$this->zone_id][$this->method_name])){
            return $custom_method[$this->zone_id][$this->method_name][$value_of];
        }
        return null;
        */

        return apply_filters('pisol_edd_dynamic_shipping_methods_values', null, $value_of, $this->method_name, $this->zone_id);

    }
}