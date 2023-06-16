<?php

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

class pisol_edd_woo_block{

    private $extend;

    protected static $instance = null;

    const IDENTIFIER = 'pisol_edd';


    public static function get_instance( ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    function __construct(){
        $block_support = get_option('pi_edd_estimate_block_support', 0);

        if(empty($block_support)) return;
        
        add_action( 'woocommerce_blocks_loaded', [$this, 'loadData']);
        add_action( 'enqueue_block_editor_assets', [$this, 'editor']);
        add_action( 'wp_enqueue_scripts', [$this, 'front']);
        add_filter('__experimental_woocommerce_blocks_add_data_attributes_to_block', [$this, 'registerBlock']);

        add_filter( 'woocommerce_shipping_rate_label', [$this, 'shippingMethodEstimateDate'], 10, 2 );
    }

    /**
     * this should be called at end else session is not accessible
     */
    function getSettings(){
        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }

        $this->shipping_method_settings = $this->getShippingSetting();
    }

    function registerBlock( $allowed_blocks ) {
		$allowed_blocks[] = 'pisol-edd/order-estimates';
		return $allowed_blocks;
	}

    function loadData(){
        if(!class_exists('\Automattic\WooCommerce\StoreApi\StoreApi') || !class_exists('\Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema') || !class_exists('\Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema')) return;
        
        $this->extend = StoreApi::container()->get( ExtendSchema::class );
	    $this->extendData();
    }

    function getShippingSetting(){
        $method_name = pisol_edd_shipping_methods::getMethodNameForEstimateCalculation();
        $shipping_method_settings = pisol_min_max_holidays::getMinMaxHolidaysValues($method_name);
        return $shipping_method_settings;
    }

    function extendData(){
        
        $this->extend->register_endpoint_data(
			array(
				'endpoint'        => CartSchema::IDENTIFIER,
				'namespace'       => self::IDENTIFIER,
				'data_callback'   => array( $this, 'orderEstimate' ),
				'schema_type'       => ARRAY_A,
			)
		);

        $this->extend->register_endpoint_data(
			array(
				'endpoint'        => CartItemSchema::IDENTIFIER,
				'namespace'       => self::IDENTIFIER,
				'data_callback'   => array( $this, 'itemEstimate' ),
				'schema_type'       => ARRAY_A,
			)
		);
    }

    function orderEstimate(){
        /**
         * initializing setting and shipping setting 
         * it should be called here else session is null
         */
        $this->getSettings();

        $cart_items = WC()->cart->get_cart_contents();
        $estimate = pisol_edd_order_estimate::orderEstimate($cart_items, $this->shipping_method_settings);
        $msg = pisol_edd_cart_page::getOrderMessage($estimate, $this->settings);
        $msg = str_replace('{icon}',"", $msg);
        $order_estimate = pisol_edd_message::msg($estimate, $msg, 0, 'plain','pi-edd-cart');

        $show_cart_page = (boolean)$this->settings['show_estimate_for_each_product_in_cart'];
        $show_checkout_page = (boolean)$this->settings['show_estimate_for_each_product_in_checkout'];

        return ['order_estimate' => $order_estimate, 'product_estimate_cart' => $show_cart_page,  'product_estimate_checkout' => $show_checkout_page,];
    }

    function itemEstimate( $cart_item){
        /**
         * initializing setting and shipping setting 
         * it should be called here else session is null
         */
        $this->getSettings();

        $product_id = $cart_item['product_id'];
        $variation_id = $cart_item['variation_id'];

        $product = wc_get_product($product_id);
    
        if(!is_object($product)) return;

        $estimate = $this->getEstimate($product, $product_id, $variation_id);

        $msg = $this->getMessage($estimate);

        $product_estimate = pisol_edd_message::msg($estimate, $msg, $product_id, 'plain','pi-edd-cart');

        return ['product_estimate' => $product_estimate];
    }

    function editor(){

            wp_enqueue_script( 'pisol-edd-block', plugin_dir_url( __FILE__ ) . 'js/block-editor.js', array( 'wp-blocks', 'wp-element'  ), '1.0.0', true );
        
    }

    function front(){
            wp_enqueue_script( 'pisol-edd-block', plugin_dir_url( __FILE__ ) . 'js/block.js', array('wc-blocks-checkout' ), '1.0.0', true );
    }

    function getEstimate($product , $product_id, $variation_id){
        if(!is_object($product)) return false;

        $estimates = pisol_edd_product::estimates($product, $this->shipping_method_settings, array(array('variation_id' => $variation_id)));

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
        
        return apply_filters('pisol_cart_page_message_filter', $msg, $estimate, $this);
    }

    function shippingMethodEstimateDate( $label, $method){
        /**
         * initializing setting and shipping setting 
         * it should be called here else session is null
         */
        $this->getSettings();

        if(empty($this->settings['show_estimate_for_each_shipping_method'])) return $label;

        $estimate = $this->methodEstimate( $method );
        return $label.' <br/>('.$estimate.')';
    }

    function methodEstimate( $method ){
        $method_name = $method->id;

        $shipping_method_settings = $this->getShippingMethodSetting($method_name);
        
        if(!isset($this->cart_items)){
            $this->cart_items = WC()->cart->get_cart_contents();
        }
        $cart_items =  isset($this->cart_items) && !empty($this->cart_items) ? $this->cart_items : array();
        $estimate = apply_filters('pisol_edd_shipping_method_estimate',pisol_edd_order_estimate::orderEstimate($cart_items, $shipping_method_settings), $method, 0, $shipping_method_settings);
        $msg = $this->getMethodMessage($estimate);
        $msg = str_replace('{icon}',"", $msg);
        return pisol_edd_message::msg($estimate, $msg, 0, 'plain','pi-edd-cart'); 
        
    }

    function getShippingMethodSetting($method_name){
        $shipping_method_settings = pisol_min_max_holidays::getMinMaxHolidaysValues($method_name);
        return $shipping_method_settings;
    }

    function getMethodMessage($estimate){
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

pisol_edd_woo_block::get_instance();