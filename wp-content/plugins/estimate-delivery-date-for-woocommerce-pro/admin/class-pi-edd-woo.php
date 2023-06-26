<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       piwebsolution.com
 * @since      1.0.0
 *
 * @package    Pi_Edd
 * @subpackage Pi_Edd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pi_Edd
 * @subpackage Pi_Edd/admin
 * @author     PI Websolution <rajeshsingh520@gmail.com>
 */
class Pi_Edd_Woo {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( ) {

		add_action( 'woocommerce_init', array($this,'shipping_methods'));

	}
	

	public function shipping_methods(){
		/**
		 * alg_wc_shipping => https://wordpress.org/plugins/custom-shipping-methods-for-woocommerce/
		 */
		$methods = array('flat_rate','free_shipping','local_pickup','alg_wc_shipping');

		$shipping_methods = apply_filters('pisol_edd_backend_shipping_method_fields',$methods);

		
		if(is_array($shipping_methods)):
		foreach($shipping_methods as $method){
			add_filter( 'woocommerce_shipping_instance_form_fields_'.$method, array($this,'pi_test'),1);
		}
		endif;
	}
	
	function pi_test($field){
		$field['min_days'] = array('title'=>'Minimum Shipping Days', 'type'=>'number','default'=>1,'min'=>0, 'custom_attributes'=> array('readonly'=>'readonly'));
		$field['max_days'] = array('title'=>'Maximum Shipping Days', 'type'=>'number','default'=>1,'min'=>0,  'custom_attributes'=> array('readonly'=>'readonly'));
		return $field;
	}


}

new Pi_Edd_Woo();