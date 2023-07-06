<?php

class pisol_edd_method_estimate{
    function __construct(){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }     

        if(!empty($this->settings['show_estimate_for_each_shipping_method']) && !empty($this->settings['enable_estimate_globally'])){
            add_filter('woocommerce_after_shipping_rate', array($this,'estimate'),9999,2);
        }

    }

    function getShippingSetting($method_name){
        $shipping_method_settings = pisol_min_max_holidays::getMinMaxHolidaysValues($method_name);
        return $shipping_method_settings;
    }

    function estimate($method, $index){
        $method_name = $method->id;

        $shipping_method_settings = $this->getShippingSetting($method_name);
        
        if(!isset($this->cart_items)){
            $this->cart_items = WC()->cart->get_cart_contents();
        }
        $cart_items =  isset($this->cart_items) && !empty($this->cart_items) ? $this->cart_items : array();
        $estimate = apply_filters('pisol_edd_shipping_method_estimate',pisol_edd_order_estimate::orderEstimate($cart_items, $shipping_method_settings), $method, $index, $shipping_method_settings);
        $msg = apply_filters('pisol_edd_shipping_method_msg', $this->getMessage($estimate), $method);
        $msg = str_replace('{icon}',"", $msg);
        echo pisol_edd_message::msg($estimate, $msg, 0, 'method','pi-edd-cart'); 
        
    }


    function getMessage($estimate){
        if(empty($estimate)) return null;

        $today = current_time('Y/m/d');
        $tomorrow = date('Y/m/d', strtotime($today.' +1 day'));
        $msg = "";
        if(isset($estimate['min_date']) && isset($estimate['max_date']) && !empty($estimate['min_date']) && !empty($estimate['max_date'])){
            
                if($estimate['min_date'] == $estimate['max_date']){
                    $msg = $this->settings['shipping_method_estimate_simple_msg'];
                }else{
                    $msg = $this->settings['shipping_method_estimate_range_msg'];
                }
        }

        if(!empty($this->settings['enable_different_msg_for_same_day_estimate']) && $estimate['min_date'] == $today && $estimate['max_date'] == $today){
            $msg = $this->settings['msg_for_same_day_delivery'];
        }

        if(!empty($this->settings['enable_different_msg_for_next_day_estimate']) && $estimate['min_date'] == $tomorrow && $estimate['max_date'] == $tomorrow){
            $msg = $this->settings['msg_for_next_day_delivery'];
        }
        
        return $msg;
    }

    
}
