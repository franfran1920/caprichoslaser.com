<?php

class pisol_edd_shipping_methods{

    public static function getMethodNameForEstimateCalculation(){
        
        $selected_method_name = self::getUserSelectedShippingMethodName();
        if(!empty($selected_method_name)){
            return $selected_method_name;
        }else{
            $obj = new self();
            $methods = $obj->getEddShippingMethods();
            $first_method_name = null;
            if(isset($methods) && !empty($methods) ){
                $first_method_name = $obj->createNameFromMethod(reset($methods));
            }
            return apply_filters('pisol_edd_first_method_filter', $first_method_name, $methods);
        }

        return null;
    }

    /**
     * Return all the methods of active shipping zone
     */
    public function getEddShippingMethods(){
        $shipping_zone = Pi_edd_shipping_zone::getShippingZone();
        if(is_object($shipping_zone)){
            $methods = $shipping_zone->get_shipping_methods(true);
            return $methods;
        }
        return null;
    }

    /**
     * Get the user selected shipping method
     */
    static public function getUserSelectedShippingMethodName(){
        $selected_shipping_method = array();
        if( isset( WC()->session ) ){
            $selected_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
        }

        if( isset( $selected_shipping_method[0] ) && $selected_shipping_method[0] !== false){ // flat_rate:19
            return apply_filters('pi_edd_user_selected_method', $selected_shipping_method[0]);
        }
        
        return apply_filters('pi_edd_user_selected_method', self::default_shipping_method());
    }

    function createNameFromMethod($method){
         $name = $method->id.":".$method->instance_id;
         return $name;
    }

    static function default_shipping_method(){
        $default_method = Pisol_edd_get_option('pi_edd_default_shipping_method','');

        if(empty($default_method)) return null;

        return $default_method;
    }
    
}
