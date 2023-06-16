<?php

class pisol_edd_checkout_page{
    function __construct(){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }


        if(!empty($this->settings['show_estimate_for_each_product_in_checkout']) && !empty($this->settings['enable_estimate_globally'])){
            add_filter('woocommerce_checkout_cart_item_quantity', array($this,'estimate'),10,2);
        }

        if(!empty($this->settings['show_combined_estimate_on_checkout_page']) && !empty($this->settings['enable_estimate_globally'])){
        $checkout_page_position = apply_filters('pi_edd_combined_estimate_position_checkout_page','woocommerce_review_order_after_shipping');
        add_action($checkout_page_position, array($this,'orderEstimate'));
        }
    }

    function getShippingSetting(){
        $method_name = pisol_edd_shipping_methods::getMethodNameForEstimateCalculation();
        $shipping_method_settings = pisol_min_max_holidays::getMinMaxHolidaysValues($method_name);
        return $shipping_method_settings;
    }

    function estimate($link_text, $cart_item){
        
        $product_id = $cart_item['product_id'];
        $variation_id = $cart_item['variation_id'];

        $product = wc_get_product($product_id);
    
        if(!is_object($product)) return;

        $shipping_method_settings = $this->getShippingSetting();

        $estimate = $this->getEstimate($product, $product_id, $variation_id, $shipping_method_settings);

        $msg = $this->getMessage($estimate);
        $msg = str_replace('{icon}',"", $msg);
        return $link_text.'<br>'.pisol_edd_message::msg($estimate, $msg, $product_id, 'default','pi-edd-cart');
        
    }

    function orderEstimate(){
        $shipping_method_settings = $this->getShippingSetting();
        $cart_items = WC()->cart->get_cart_contents();
        $estimate = pisol_edd_order_estimate::orderEstimate($cart_items, $shipping_method_settings);
        $msg = apply_filters('pisol_edd_order_estimate_msg', $this->getOrderMessage($estimate), $shipping_method_settings);
        $msg = str_replace('{icon}',"", $msg);
        echo pisol_edd_message::msg($estimate, $msg, 0, 'checkout','pi-edd-cart'); 
    }


    function getEstimate($product , $product_id, $variation_id, $shipping_method_settings){
        if(!is_object($product)) return false;

        $estimates = pisol_edd_product::estimates($product, $shipping_method_settings, array(array('variation_id' => $variation_id)));

        if(empty($variation_id)){
            return isset($estimates[$product_id]) ? $estimates[$product_id] : false;
        }else{
            return isset($estimates[$variation_id]) ? $estimates[$variation_id] : false;
        }
    }

    function getMessage($estimate){
        if(empty($estimate)) return null;

        $today = current_time('Y/m/d');
        $tomorrow = date('Y/m/d', strtotime($today.' +1 day'));
        $msg = "";
        if(isset($estimate['min_date']) && isset($estimate['max_date']) && !empty($estimate['min_date']) && !empty($estimate['max_date'])){
            if(empty($this->settings['show_range'])){
                $msg = $this->settings['cart_checkout_simple_estimate_msg'];
                if($estimate['is_on_backorder'] && !empty($this->settings['cart_checkout_simple_estimate_msg_back_order'])){
                    $msg = $this->settings['cart_checkout_simple_estimate_msg_back_order'];
                }
            }else{
                if($estimate['min_date'] == $estimate['max_date']){
                    $msg = $this->settings['cart_checkout_simple_estimate_msg'];
                    if($estimate['is_on_backorder'] && !empty($this->settings['cart_checkout_simple_estimate_msg_back_order'])){
                        $msg = $this->settings['cart_checkout_simple_estimate_msg_back_order'];
                    }
                }else{
                    $msg = $this->settings['cart_checkout_range_estimate_msg'];
                    if($estimate['is_on_backorder'] && !empty($this->settings['cart_checkout_range_estimate_msg_back_order'])){
                        $msg = $this->settings['cart_checkout_range_estimate_msg_back_order'];
                    }
                }
            }
        }

        if(pisol_edd_common::isSameDateEstimate($estimate['min_date'], $estimate['max_date'], $this->settings)){
            $msg = $this->settings['msg_for_same_day_delivery'];
        }

        if(pisol_edd_common::isNextDateEstimate($estimate['min_date'], $estimate['max_date'], $this->settings)){
            $msg = $this->settings['msg_for_next_day_delivery'];
        }
        
        return apply_filters('pisol_checkout_page_message_filter', $msg, $estimate, $this);
    }

    function getOrderMessage($estimate){
        if(empty($estimate)) return null;

        $today = current_time('Y/m/d');
        $tomorrow = date('Y/m/d', strtotime($today.' +1 day'));
        $msg = "";
        if(isset($estimate['min_date']) && isset($estimate['max_date']) && !empty($estimate['min_date']) && !empty($estimate['max_date'])){
            
                if($estimate['min_date'] == $estimate['max_date']){
                    $msg = $this->settings['combined_estimate_simple_msg'];
                }else{
                    $msg = $this->settings['combined_estimate_range_msg'];
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
