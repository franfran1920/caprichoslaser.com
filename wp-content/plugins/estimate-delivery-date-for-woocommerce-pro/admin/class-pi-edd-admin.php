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
class Pi_Edd_Admin {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('wp_loaded', array($this,'register_menu'));

		if(is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )){
			new Class_Pi_Edd_Option($this->plugin_name);
			new Class_Pi_Edd_Message($this->plugin_name);
			new Class_Pi_Edd_Design($this->plugin_name);
			new pisol_edd_warning_messages();
		}

	}

	
	public function register_menu(){
		if(is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )){
			$obj =	new Pi_Edd_Menu($this->plugin_name, $this->version);
			new Class_Pi_Edd_Shipping($this->plugin_name);
			new Class_Pi_Edd_Dynamic_Shipping_Method($this->plugin_name);
			new Class_Pi_Edd_Translate($this->plugin_name);
			new Class_Pi_Edd_Holidays($this->plugin_name);
			new Class_Pi_Edd_Show_Holidays($this->plugin_name);
			new Class_Pi_edd_shipping_class_option($this->plugin_name);
			new Class_Pi_Edd_Debug_Log_manager($this->plugin_name);
			new pisol_edd_access_control($this->plugin_name);
		}	
	}

	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pi_Edd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pi_Edd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pi_Edd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pi_Edd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
        
		wp_enqueue_script( 'jquery-ui-core');
		wp_enqueue_script( 'jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-sortable');
		
		wp_enqueue_script( 'selectWoo');
		wp_enqueue_script( 'pi-edd-global-js', plugin_dir_url( __FILE__ ) . 'js/pi-edd-global.js', array( 'jquery' ), $this->version );

		wp_localize_script('pi-edd-global-js', 'pi_edd_setting', array(
			'global_estimate_status' => get_option('pi_edd_default_global_estimate_status', 'enable')
		));

		$screen = get_current_screen();

		$screen_id = is_object($screen) && isset($screen->id) ? $screen->id : '';

		if($screen_id == 'edit-product' && isset($_GET['post_type']) && $_GET['post_type'] == 'product'){

			wp_enqueue_script( 'pi-edd-bulk-edit-product', plugin_dir_url( __FILE__ ) . 'js/bulk-edit-product.js', array( 'jquery' ), $this->version );
		}

	}

}
