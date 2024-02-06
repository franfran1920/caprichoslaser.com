<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              piwebsolution.com
 * @since             4.7.20.14
 * @package           Pi_Edd
 *
 * @wordpress-plugin
 * Plugin Name:       Estimate delivery date for Woocommerce Pro
 * Plugin URI:        https://www.piwebsolution.com/cart/?add-to-cart=879
 * Description:       WooCommerce Estimated delivery date per product, You don't have to set dates in each product, just add it in shipping method, based on shipping days and product preparation days it shows estimated shipping date per product and also considers holidays
 * Version:           4.7.20.14
 * Author URI:        https://www.piwebsolution.com/
 * Author:            PI Websolution
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pi-edd
 * Domain Path:       /languages
 * WC tested up to: 7.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Checking Pro version
 */
function pi_edd_free_check(){
	if(is_plugin_active( 'estimate-delivery-date-for-woocommerce/pi-edd.php')){
		return true;
	}
	return false;
}

if(pi_edd_free_check()){
    function pi_edd_my_free_notice() {
        ?>
        <div class="error notice">
            <p><?php _e( 'Please uninstall the FREE version of Estimate delivery date for Woocommerce then activate the PRO version', 'pi-edd' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_edd_my_free_notice' );
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}else{


/* 
    Making sure woocommerce is there 
*/


if(!is_plugin_active( 'woocommerce/woocommerce.php')){
    function pi_edd_pro_woo_error_notice() {
        ?>
        <div class="error notice">
            <p><?php _e( 'Please Install and Activate WooCommerce plugin, without that this plugin cant work', 'pi-edd' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_edd_pro_woo_error_notice' );
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}

/**
 * Declare compatible with HPOS new order table 
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );


function Pisol_edd_get_option($name, $default){
    $value = get_option($name, $default);
    return apply_filters('pisol_edd_option_filter_'.$name, $value, $name);
}

function Pisol_edd_get_post_meta($id, $name){
    $value = get_post_meta($id, $name, true);
    return apply_filters('pisol_edd_post_meta_filter_'.$name, $value, $id, $name);
}
//define('PISOL_EDD_DISABLE_PERSISTENCE_CACHE',true);
function pisol_edd_wp_cache_get($variable, $group){
    if(defined('PISOL_EDD_DISABLE_PERSISTENCE_CACHE')) return false;
    return wp_cache_get($variable,$group);
}

function pisol_edd_wp_cache_set($variable, $value, $group){
    if(defined('PISOL_EDD_DISABLE_PERSISTENCE_CACHE')) return false;
    return wp_cache_set($variable, $value, $group);
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PI_EDD_VERSION', '4.7.20.14' );
define( 'PISOL_EDD_DELETE_SETTING', false);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pi-edd-activator.php
 */
function activate_pi_edd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pi-edd-activator.php';
	Pi_Edd_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pi-edd-deactivator.php
 */
function deactivate_pi_edd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pi-edd-deactivator.php';
	Pi_Edd_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pi_edd' );
register_deactivation_hook( __FILE__, 'deactivate_pi_edd' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pi-edd.php';

add_action('init', 'pisol_edd_estimate_update_checking');
function pisol_edd_estimate_update_checking()
{
    new pisol_update_notification_v1(plugin_basename(__FILE__), PI_EDD_VERSION);
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pi_edd() {

	$plugin = new Pi_Edd();
	$plugin->run();

}
run_pi_edd();


}