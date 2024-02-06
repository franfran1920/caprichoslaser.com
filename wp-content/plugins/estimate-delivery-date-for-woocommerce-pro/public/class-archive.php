<?php

class pisol_edd_product_archive_page{
    function __construct(){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }


        $this->shipping_method_settings = $this->getShippingSetting();

        if(!empty($this->settings['shop_estimate_on_product_archive_page']) && !empty($this->settings['enable_estimate_globally'])){
            add_action('wp_enqueue_scripts', array($this, 'script'));
            add_action($this->settings['archive_page_msg_position'], array($this,'estimate'));
            add_action('wp_ajax_pi_edd_loop_estimate', array($this, 'getAllEstimate'));
            add_action('wp_ajax_nopriv_pi_edd_loop_estimate', array($this, 'getAllEstimate'));
            add_action('wc_ajax_pi_edd_loop_estimate', array($this, 'getAllEstimate'));
        }
    }

    function getShippingSetting(){
        $method_name = pisol_edd_shipping_methods::getMethodNameForEstimateCalculation();
        $shipping_method_settings = pisol_min_max_holidays::getMinMaxHolidaysValues($method_name);
        return $shipping_method_settings;
    }

    function estimate(){
        global $product;

        if(!is_object($product)) return;

        if(empty($this->settings['shop_estimate_of_variable_product_on_archive_page']) && $product->is_type('variable')) return;

        $product_id = $product->get_id();

        if(!$product->is_type('variable') && !$product->is_in_stock() && apply_filters('pi_edd_show_out_of_stock_msg_on_archive', true)){
            $blank_estimate = array(
                'min_date'=>'',
                'max_date'=>'',
                'min_days'=>'',
                'max_days'=>'',
                'in_stock'=>false
            );
            echo pisol_edd_message::msg($blank_estimate, $this->settings['out_off_stock_message'], $product_id, 'default','pi-edd-loop');
            return;
            
        }

        if(empty($this->settings['load_load_page_estimate_by_ajax'])){
            $estimate = $this->getEstimate($product);
            if(isset($estimate['in_stock']) && $estimate['in_stock']){
                $msg = $this->getMessage($estimate);

                echo pisol_edd_message::msg($estimate, $msg, $product_id, 'default','pi-edd-loop');
            }
        }else{
            echo $this->archivePageAjaxTemplate($product_id);
        }
    }

    function getAllEstimate(){
        if(!isset($_POST['products']) && !is_array($_POST['products'])) return;
        $estimates = array();
        $products = $_POST['products'];
        foreach($products as $product_id){
            $product = wc_get_product($product_id);
            $estimate = $this->getEstimate($product);
            if(isset($estimate['in_stock']) && $estimate['in_stock']){
                $msg = $this->getMessage($estimate);
                $composed = pisol_edd_message::msg($estimate, $msg, $product_id, 'default','pi-edd-loop');
                $estimates[$product_id] = $composed;
            }
        }
        echo json_encode($estimates);
        die;
    }

    function archivePageAjaxTemplate($product_id){
       return sprintf('<div class="pi-edd-loop-ajax" id="pi-edd-loop-ajax-id-%s"  data-product="%s"></div>', esc_attr($product_id), esc_attr($product_id));
    }


    function getEstimate($product){
        if(!is_object($product)) return false;

        $estimate = pisol_edd_product::singleEstimate($product, $this->shipping_method_settings);

        return $estimate;
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
        
        return apply_filters('pisol_archive_page_message_filter', $msg, $estimate, $this);
    }

    function script(){
        if(!empty($this->settings['load_load_page_estimate_by_ajax'])){
            wp_enqueue_script('pi-edd-loop-ajax', plugin_dir_url( __FILE__ ) . 'js/pi-edd-loop-ajax.js', array( 'jquery' ), PI_EDD_VERSION, false );
        }
    }
    
}