<?php

class pisol_edd_debug_log{
    function __construct(){
        $this->zone_id = false;
        $this->log_variable = 'pisol_edd_method_log';
        $enable_debug = get_option('pi_edd_enable_debug',0);
        if(!empty($enable_debug)){
            $sequence = PHP_INT_MAX - 100;
            add_filter('pisol_edd_shipping_method_setting_filter', array($this, 'logShippingMethod'),$sequence,3);
        }
    }

    function logShippingMethod($values, $shipping_method_name, $zone_id){
        if(empty($values['min_days']) || empty($values['max_days'])){
            $this->addToLog($shipping_method_name, $zone_id);
        }else{
            $this->deleteFromLog($shipping_method_name, $zone_id);
        }
        return $values;
    }

    function addToLog($shipping_method_name, $zone_id){
        $zone_id = $this->getZoneId($zone_id);
        $existing_log = get_option($this->log_variable, array());
        if(!is_array($existing_log)){
            $existing_log = array();   
        }
        $index = $shipping_method_name;
        if($zone_id !== false){
            $index = $index.'|'.$zone_id;
        }
        $time = current_time('M d, Y H:i A');
        $existing_log[$index] = array('method' => $shipping_method_name, 'zone_id' => $zone_id, 'time'=> $time);

        update_option($this->log_variable, $existing_log);
    }

    function deleteFromLog($shipping_method_name, $zone_id){
        $zone_id = $this->getZoneId($zone_id);
        $existing_log = get_option($this->log_variable, array());
        if(!is_array($existing_log)){
            $existing_log = array();   
        }
        $index = $shipping_method_name;
        if($zone_id !== false){
            $index = $index.'|'.$zone_id;
        }
        unset($existing_log[$index]);

        update_option($this->log_variable, $existing_log);
    }

    function getZoneId($zone_id){
        if($zone_id === false){
            if($this->zone_id === false){
                $shipping_zone = Pi_edd_shipping_zone::getOriginalShippingZone(); 
                if(is_object( $shipping_zone )){
                    $this->zone_id = $shipping_zone->get_id();
                }else{
                    $this->zone_id = 0;
                }
                return $this->zone_id;
            }else{
                return $this->zone_id;
            }
        }

        return $zone_id;
    }
}

new pisol_edd_debug_log();