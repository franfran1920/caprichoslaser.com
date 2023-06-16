<?php

class pisol_edd_dynamic_shipping_method_support{
    function __construct(){
        add_filter('pisol_edd_dynamic_shipping_methods_values', array($this,'addingValues'),10,4);
    }

    function addingValues($value, $value_of, $method_name, $zone_id){

        /** doing this as at present we only want to set min_days and max_dys */
        if(!($value_of == 'min_days' || $value_of == 'max_days' || $value_of == 'shipping_cutoff_time' || $value_of == 'overwrite_global_shipping_off_days' || $value_of == 'pi_days_of_week' || $value_of == 'overwrite_global_shipping_off_dates' || $value_of == 'holiday_dates')) return $value;
	
        $saved_value = $this->getValue($method_name, $value_of, $zone_id);
        if($saved_value !== false) return $saved_value;
        
        return $value;   
    }

    function getValue($method_name, $value_of, $zone_id){

        $matched_rule = $this->matchedMethods($method_name, $value_of, $zone_id);

        if($matched_rule === false) return false;
        
        if($value_of == 'holiday_dates' && isset($matched_rule[$value_of]) && !empty($matched_rule[$value_of])){
            return self::getHolidayDates($matched_rule[$value_of]);
        }

        return isset($matched_rule[$value_of]) && !empty($matched_rule[$value_of]) ? $matched_rule[$value_of] : false;

        return false;

    }

    static function getHolidayDates($holiday_dates){
        if(is_array($holiday_dates)) return array_unique($holiday_dates);

        $dates = explode(':', $holiday_dates);

        return array_unique($dates);
    }

    function matchedMethods($method_name, $value_of, $zone_id){
        $saved_methods = Pisol_edd_get_option('pi_edd_dynamic_method_min_max', array());
        if(empty($saved_methods)) return false;
        
        foreach($saved_methods as $method){
            $match_type = isset($method['match']) ? $method['match'] : 'exact' ;
            
            if($match_type == 'like'){
                if(strpos($method_name, $method['name']) !== false && self::zoneMatch($zone_id, $method['zone'])){
                    return $method;
                }
            }elseif($match_type == 'exact'){
                if($method['name'] === $method_name   && self::zoneMatch($zone_id, $method['zone'])){
                    return $method;
                }
            }
        }
        return false;
    }

    static function zoneMatch($zone_id, $zone_array){
        if(empty($zone_array) || !is_array($zone_array)) return false;

        if(in_array('all',$zone_array)) return true;

        if(self::in_array($zone_id, $zone_array)) return true;

        return false;

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
}

new pisol_edd_dynamic_shipping_method_support();