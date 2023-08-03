<?php

/**
 * Fired during plugin activation
 *
 * @link       piwebsolution.com
 * @since      1.0.0
 *
 * @package    Pi_Edd
 * @subpackage Pi_Edd/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pi_Edd
 * @subpackage Pi_Edd/includes
 * @author     PI Websolution <rajeshsingh520@gmail.com>
 */
class Pi_Edd_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::check_date_option('pi_product_page_text',"{date}");
		self::check_date_option('pi_loop_page_text',"{date}");
		self::check_date_option('pi_cart_page_text',"{date}");
		self::check_range_option('pi_product_page_text_range','{min_date}', '{max_date}');
		self::check_range_option('pi_loop_page_text_range','{min_date}', '{max_date}');
		self::check_range_option('pi_cart_page_text_range','{min_date}', '{max_date}');
	}

	public static function check_date_option($variable, $search_for){
		$value = get_option($variable);
		if(strpos($value, $search_for) == false){
			delete_option($variable);
		}
	}

	public static function check_range_option($variable, $search_1, $search_2){
		$value = get_option($variable);
		if(strpos($value, $search_1) == false && strpos($value, $search_2) == false){
			delete_option($variable);
		}
	}

}
