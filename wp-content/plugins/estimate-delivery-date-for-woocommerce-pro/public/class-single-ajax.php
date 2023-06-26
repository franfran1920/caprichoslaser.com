<?php

class pisol_edd_single_ajax_product_page{
    function __construct(){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }

        if( !empty($this->settings['load_single_page_estimate_by_ajax'])){
            $this->shipping_method_settings = $this->getShippingSetting();

            if(!empty($this->settings['shop_estimate_on_product_page']) && !empty($this->settings['enable_estimate_globally'])){
                add_action($this->settings['product_page_msg_position'], array($this,'estimateContainer'));
            }

            add_shortcode('estimate_delivery_date', array($this,'shortcode'));
            add_action('wp_enqueue_scripts', array($this, 'script'));

            add_action('wp_ajax_nopriv_pisol_product_estimate',array($this,'productEstimate'));
            add_action('wp_ajax_pisol_product_estimate',array($this,'productEstimate'));
            add_action('wc_ajax_pisol_product_estimate',array($this,'productEstimate'));
        }
    }

    function estimateContainer(){
        global $product;
        if(!is_object($product)) return;
        $product_id = $product->get_id();
        if(!pisol_edd_product::estimateDisabled($product_id)){
            if($product->is_type('variable')){
                $no_var_msg = $this->settings['no_variation_selected_message'];
                if( $no_var_msg == ""){
                    $style = ' style="display:none;" ';
                }else{
                    $style = '';
                }
                printf('<div class="pi-edd pi-edd-ajax pi-edd-ajax-variable pi-edd-ajax-estimate-%1$s" data-product_id="%2$s" data-notselected="%3$s" %5$s>%4$s</div>',esc_attr($product_id), esc_attr($product_id), esc_attr($no_var_msg), $no_var_msg, $style);
            }else{
                printf('<div class="pi-edd pi-edd-ajax pi-edd-ajax-simple pi-edd-ajax-estimate-%s"  data-product_id="%s"></div>',esc_attr($product_id), esc_attr($product_id));
            }
        }
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

        $msg = '';
        if(isset($arg['message'])){
            $msg = $arg['message'];
        }
        
        if(!is_object($product)) return;

        if(!is_object($product)) return;
        $product_id = $product->get_id();
        if(!pisol_edd_product::estimateDisabled($product_id)){
            if($product->is_type('variable')){
                $no_var_msg = $this->settings['no_variation_selected_message'];
                if( $no_var_msg == ""){
                    $style = ' style="display:none;" ';
                }else{
                    $style = '';
                }
                return sprintf('<span class="pi-shortcode pi-edd-ajax-variable pi-edd-ajax-estimate-%1$s" data-product_id="%2$s" data-notselected="%3$s" %5$s data-message="%6$s">%4$s</span>',esc_attr($product_id), esc_attr($product_id), esc_attr($no_var_msg), $no_var_msg, $style, esc_attr($msg));
            }else{
                return sprintf('<span class="pi-shortcode pi-edd-ajax-simple"  data-product_id="%s" data-message="%s"></span>',esc_attr($product_id), esc_attr($msg));
            }
        }
    }

    function productEstimate(){
        $product_id = filter_input(INPUT_POST, 'product_id');
        $variable_id = filter_input(INPUT_POST, 'variable_id') ? filter_input(INPUT_POST, 'variable_id') : 0;
        $message = filter_input(INPUT_POST, 'message');

        if($variable_id === 'first'){
            $product = wc_get_product($product_id);
            if(!is_object($product)) die;

            $variable_id = $this->getFirstVariationId($product);
        }

        $product = wc_get_product($product_id);
        $estimate = $this->estimate($product, $variable_id, $message);
        echo $estimate;
        die;
    }

    function getFirstVariationId($product){
        $all_variations = $product->get_available_variations();
        
        $variation_id_in_stock = pisol_edd_common::selectInStockVariation($all_variations);

        return $variation_id_in_stock;
	}

    function estimate($product, $variable_id, $message = ''){
        if(!is_object($product)) return;

        $estimates = $this->getEstimate($product, $variable_id);
        if($product->is_type('variable')){
            $product_id = $product->get_id();
            return $this->variationMsg($estimates, $product_id, $variable_id, $message);
        }else{
            $product_id = $product->get_id();
            $estimate = isset($estimates[$product_id]) ? $estimates[$product_id] : "";

            if(!empty($message)){
                $msg = $message;
            }else{
                $msg = $this->getMessage($estimate);
            }
            return pisol_edd_message::msg($estimate, $msg, $product_id, 'plain','pi-edd-product');
        }
    }

    function variationMsg($estimates, $product_id, $variation_id, $preset_msg = ''){
        $message = "";
        if(isset($estimates[$variation_id])){
            if(!empty($preset_msg)){
                $msg = $preset_msg;
            }else{
                $msg = $this->getMessage($estimates[$variation_id]);
            }
            $message = pisol_edd_message::msg($estimates[$variation_id], $msg, $product_id, 'plain');
        }
        return $message;
    }

    function getEstimate($product, $variation_id){
        if(!is_object($product)) return false;

        $estimates = pisol_edd_product::estimates($product, $this->shipping_method_settings, array(array('variation_id' => $variation_id)));

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
                    if($estimate['is_on_backorder'] && !empty($this->settings['product_page_simple_estimate_msg_back_order'])){
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
        
        return apply_filters('pisol_product_page_message_filter', $msg, $estimate, $this);
    }

    function script(){
        wp_enqueue_script( 'jquery-blockui' );
        wp_enqueue_script('pi-edd-product-ajax', plugin_dir_url( __FILE__ ) . 'js/pi-edd-product-ajax.js', array( 'jquery' ), PI_EDD_VERSION, false );

        $value = array(
            'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
            'ajaxurl'=> admin_url( 'admin-ajax.php' ),
            'showFirstVariationEstimate'=> $this->settings['show_first_variation_estimate'],
            'out_of_stock_message' => $this->settings['out_off_stock_message']
        );

        wp_localize_script( 'pi-edd-product-ajax', 'pi_edd_variable',$value);
    }

    
}
