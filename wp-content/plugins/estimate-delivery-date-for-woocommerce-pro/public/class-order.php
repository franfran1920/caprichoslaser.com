<?php

class pisol_edd_order{
    function __construct(){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }

        $this->shipping_method_settings = $this->getShippingSetting();
       
        if(!empty($this->settings['add_each_product_estimate_in_stored_order']) && !empty($this->settings['enable_estimate_globally'])){
            add_action( 'woocommerce_checkout_create_order_line_item', array($this, 'addEstimateInOrderMeta'), 10, 4 );
        }

        if(!empty($this->settings['enable_estimate_globally'])){
        add_action( 'woocommerce_checkout_update_order_meta', array($this,'updateOrderMeta') );
        }

    }

    function getShippingSetting(){
        $method_name = pisol_edd_shipping_methods::getMethodNameForEstimateCalculation();
        $shipping_method_settings = pisol_min_max_holidays::getMinMaxHolidaysValues($method_name);
        return $shipping_method_settings;
    }

    function getProductEstimate($product_id, $variable_id){
        $product = wc_get_product($product_id);
        if(!is_object($product)) return false;

        $estimates = pisol_edd_product::estimates($product, $this->shipping_method_settings, array(array('variation_id' => $variable_id)));

        if($variable_id == 0){
            $estimate = isset($estimates[$product_id]) ? $estimates[$product_id] : false;
        }else{
            $estimate = isset($estimates[$variable_id]) ? $estimates[$variable_id] :false;
        }

        return $estimate;
    }

    function addEstimateInOrderMeta($item, $cart_item_key, $values, $order){
        $product_id = $item->get_product_id();
        $variable_id = $item->get_variation_id();
        $quantity = $item['quantity'];
        
        $estimate = apply_filters('pisol_edd_order_item_estimate', $this->getProductEstimate($product_id, $variable_id), $item);

        do_action('pi_edd_item_estimate_added_in_order', $estimate, $item, $order);

        $min_estimate_date = isset($estimate['min_date']) ? $estimate['min_date'] : "";
        $max_estimate_date = isset($estimate['max_date']) ? $estimate['max_date'] : "";

        $min_estimate_days = isset($estimate['min_days']) ? $estimate['min_days'] : "";
        $max_estimate_days = isset($estimate['max_days']) ? $estimate['max_days'] : "";

        $item->add_meta_data("pi_item_min_date",$min_estimate_date);
        $item->add_meta_data("pi_item_max_date",$max_estimate_date);
        $item->add_meta_data("pi_item_min_days",$min_estimate_days);
        $item->add_meta_data("pi_item_max_days",$max_estimate_days);

        $msg = str_replace('{icon}',"",$this->getMessage($estimate));

        $compiled_msg = pisol_edd_message::msg($estimate, $msg, $product_id, 'plain',"pi-edd-product");
            
        $item->add_meta_data("pi_item_estimate_msg", $compiled_msg);
    }

    function getMessage($estimate){
        if(empty($estimate)) return null;
        
        $today = current_time('Y/m/d');
        $tomorrow = date('Y/m/d', strtotime($today.' +1 day'));
        $msg = "";
        if(isset($estimate['min_date']) && isset($estimate['max_date']) && !empty($estimate['min_date']) && !empty($estimate['max_date'])){
            if(empty($this->settings['show_range'])){
                $msg = $this->settings['archive_page_simple_estimate_msg'];
                if($estimate['is_on_backorder'] && !empty($this->settings['archive_page_simple_estimate_msg_back_order'])){
                    $msg = $this->settings['archive_page_simple_estimate_msg_back_order'];
                }
            }else{
                if($estimate['min_date'] == $estimate['max_date']){
                    $msg = $this->settings['archive_page_simple_estimate_msg'];
                    if($estimate['is_on_backorder'] && !empty($this->settings['archive_page_simple_estimate_msg_back_order'])){
                        $msg = $this->settings['archive_page_simple_estimate_msg_back_order'];
                    }
                }else{
                    $msg = $this->settings['archive_page_range_estimate_msg'];
                    if($estimate['is_on_backorder'] && !empty($this->settings['archive_page_range_estimate_msg_back_order'])){
                        $msg = $this->settings['archive_page_range_estimate_msg_back_order'];
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
        
        return $msg;
    }

    function updateOrderMeta($order_id){
        $order = wc_get_order( $order_id );
        $products = $this->products($order);
        
        $estimate = pisol_edd_order_estimate::orderEstimate($products, $this->shipping_method_settings);

        do_action('pi_edd_order_estimate_added_in_order', $estimate, $order_id);

        $order->update_meta_data( 'pi_overall_estimate_min_date', sanitize_text_field( isset($estimate['min_date']) ? $estimate['min_date'] : "" ) );
        $order->update_meta_data( 'pi_overall_estimate_min_days', sanitize_text_field( isset($estimate['min_days']) ? $estimate['min_days'] : "" ) );
        $order->update_meta_data( 'pi_overall_estimate_max_date', sanitize_text_field( isset($estimate['max_date']) ? $estimate['max_date'] : "" ) );
        $order->update_meta_data( 'pi_overall_estimate_max_days', sanitize_text_field( isset($estimate['max_days']) ? $estimate['max_days'] : "" ) );
        $order->save();
    }

    function products($order){
        if(!is_object($order)) return;
        $products = array();
        $order_items = $order->get_items();
        foreach($order_items as $item){
            $product['product_id'] = $item->get_product_id();
            $product['variation_id'] = $item->get_variation_id();
            $products[] = $product;
        }
        return $products;
    }
}

