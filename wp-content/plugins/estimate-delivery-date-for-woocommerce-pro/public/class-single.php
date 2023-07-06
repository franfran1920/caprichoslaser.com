<?php

class pisol_edd_single_product_page{
    function __construct(){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }

        if( empty($this->settings['load_single_page_estimate_by_ajax'])){
            $this->shipping_method_settings = $this->getShippingSetting();

            if(!empty($this->settings['shop_estimate_on_product_page']) && !empty($this->settings['enable_estimate_globally'])){
                add_action($this->settings['product_page_msg_position'], array($this,'estimate'));
            }
            add_shortcode('estimate_delivery_date', array($this,'shortcode'));
            add_action('wp_enqueue_scripts', array($this, 'script'));
        }

        add_filter('woocommerce_get_availability_text', [$this, 'outOfStockMsg'], 10, 2);
    }

    function getShippingSetting(){
        $method_name = pisol_edd_shipping_methods::getMethodNameForEstimateCalculation();
        $shipping_method_settings = pisol_min_max_holidays::getMinMaxHolidaysValues($method_name);
        return $shipping_method_settings;
    }

    function shortcode($arg){
        if(isset($arg['id'])){
            $product_id = $arg['id'];
            $product = wc_get_product($product_id);
            if(!is_object($product)) return;
        }else{
            global $product;
        }
        
        if(!is_object($product)) return;

        $product_id = $product->get_id();

        $estimates = $this->getEstimate($product);
        if(!$this->allEstimateNull($estimates)){
            if($product->is_type('variable')){
                if(isset($arg['message'])){
                    $estimate_msg = $this->variationMsg($estimates, $product, $arg['message']);
                }else{
                    $estimate_msg = $this->variationMsg($estimates, $product);
                }
                if(empty($estimate_msg)) return;
                
                $no_var_msg = $this->settings['no_variation_selected_message'];
                if($this->settings['show_first_variation_estimate'] == 'first-variation'){
                    $initial = reset($estimate_msg);
                }else{
                    
                    $initial = $no_var_msg;
                }

                return pisol_edd_message::variationEstimate($estimate_msg , $initial, $no_var_msg,  $product_id, true);
            }else{
                
                $estimate = isset($estimates[$product_id]) ? $estimates[$product_id] : "";
                
                if(isset($arg['message'])){
                    $msg = $arg['message'];
                }else{
                    $msg = $this->getMessage($estimate);
                }

                return pisol_edd_message::msg($estimate, $msg, $product_id, 'shortcode','');
            }
        }
    }

    function estimate(){
        global $product;
        if(!is_object($product)) return;

        $estimates = $this->getEstimate($product);
        $product_id = $product->get_id();

        if(!$this->allEstimateNull($estimates)){
            if($product->is_type('variable')){
                $estimate_msg = $this->variationMsg($estimates, $product);
                if(empty($estimate_msg)) return;
                
                $no_var_msg = $this->settings['no_variation_selected_message'];
                if($this->settings['show_first_variation_estimate'] == 'first-variation'){
                    $initial = reset($estimate_msg);
                }else{
                    
                    $initial = $no_var_msg;
                }

                echo pisol_edd_message::variationEstimate($estimate_msg , $initial, $no_var_msg, $product_id, false);
            }else{
                
                $estimate = isset($estimates[$product_id]) ? $estimates[$product_id] : "";
                $msg = $this->getMessage($estimate);
                echo pisol_edd_message::msg($estimate, $msg, $product_id, 'default','pi-edd-product');
            }
        }
    }

    function allEstimateNull($estimates){
        if(is_array($estimates)){
            $null_count = 0;
            foreach($estimates as $est){
                if($est === null){
                    $null_count++;
                }
            }

            if($null_count === count($estimates)){
                return true;
            }
        }else{
            if($estimates === null){
                return true;
            }
        }

        return false;
    }

    function variationMsg($estimates, $product, $pre_msg = ''){
        $messages =[];
        foreach((array)$estimates as $product_id => $estimate){
            if(!empty($pre_msg)){
                $msg = $pre_msg;
            }else{
                $msg = $this->getMessage($estimate);
            }
            $messages[$product_id] = pisol_edd_message::msg($estimate, $msg, $product_id, 'plain');
        }
        return $messages;
    }

    function getEstimate($product){
        if(!is_object($product)) return false;

        $estimates = pisol_edd_product::estimates($product, $this->shipping_method_settings);

        return $estimates;
    }

    function getMessage($estimate){
        if(empty($estimate)) return null;

        if(isset($estimate['in_stock']) && !$estimate['in_stock']) return $this->settings['out_off_stock_message'];
         
        $today = current_time('Y/m/d');
        $tomorrow = date('Y/m/d', strtotime($today.' +1 day'));
        $msg = "";
        if(isset($estimate['min_date']) && isset($estimate['max_date']) && !empty($estimate['min_date']) && !empty($estimate['max_date'])){
            if(empty($this->settings['show_range'])){
                $msg = $this->settings['product_page_simple_estimate_msg'];

                if($estimate['is_on_backorder'] && !empty($this->settings['product_page_simple_estimate_msg_back_order'])){
                    $msg = $this->settings['product_page_simple_estimate_msg_back_order'];
                }
            }else{
                if($estimate['min_date'] == $estimate['max_date']){
                    $msg = $this->settings['product_page_simple_estimate_msg'];
                    if($estimate['is_on_backorder']  && !empty($this->settings['product_page_simple_estimate_msg_back_order'])){
                        $msg = $this->settings['product_page_simple_estimate_msg_back_order'];
                    }
                }else{
                    $msg = $this->settings['product_page_range_estimate_msg'];
                    if($estimate['is_on_backorder'] && !empty($this->settings['product_page_range_estimate_msg_back_order'])){
                        $msg = $this->settings['product_page_range_estimate_msg_back_order'];
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

    

    function script(){
        wp_enqueue_script('pi-edd-product', plugin_dir_url( __FILE__ ) . 'js/pi-edd-product.js', array( 'jquery' ), PI_EDD_VERSION, false );

        $value = array(
            'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
            'ajaxurl'=> admin_url( 'admin-ajax.php' ),
            'showFirstVariationEstimate'=> $this->settings['show_first_variation_estimate'],
            'out_of_stock_message' => $this->settings['out_off_stock_message']
        );

        wp_localize_script( 'pi-edd-product', 'pi_edd_variable',$value);
    }

    /**
     * this will replace the default out of stock message on single product page with plugin's out of stock message
     */
    function outOfStockMsg($msg, $product){
        if(!$product->is_in_stock() && !empty($this->settings['out_off_stock_message'])){
            return $this->settings['out_off_stock_message'];
        }

        return $msg;
    }

    
}
