<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCCS_Coupon_Helpers {

	/**
	 * Getting coupons.
	 *
	 * @since  2.0.0
	 *
	 * @param  array $args
	 *
	 * @return array
	 */
	public function get_coupons( array $args = array() ) {
		$args = wp_parse_args( $args, array(
			'posts_per_page' => -1,
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
		) );

		return get_posts( $args );
	}

	/**
	 * Get coupon ID by code.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $code
	 * @param  int    $exclude Used to exclude an ID from the check if you're checking existence.
	 *
	 * @return int
	 */
	public function wc_get_coupon_id_by_code( $code, $exclude = 0 ) {
		if ( WCCS()->WCCS_Helpers->wc_version_check() ) {
			return wc_get_coupon_id_by_code( $code, $exclude );
		}

		$ids = wp_cache_get( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $code, 'coupons' );

		if ( false === $ids ) {
			global $wpdb;
			$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_date DESC;", $code ) );
			if ( $ids ) {
				wp_cache_set( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $code, $ids, 'coupons' );
			}
		}

		$ids = array_diff( array_filter( array_map( 'absint', (array) $ids ) ), array( $exclude ) );

		return apply_filters( 'woocommerce_get_coupon_id_from_code', absint( current( $ids ) ), $code, $exclude );
	}

}
