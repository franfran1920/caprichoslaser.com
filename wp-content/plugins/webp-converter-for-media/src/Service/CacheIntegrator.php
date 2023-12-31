<?php

namespace WebpConverter\Service;

use WebpConverter\HookableInterface;
use WebpConverter\PluginInfo;
use WebpConverter\Settings\Option\LoaderTypeOption;

/**
 * Supports cleaning cache generated by other plugins.
 */
class CacheIntegrator implements HookableInterface {

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	public function __construct( PluginInfo $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'webpc_settings_updated', [ $this, 'clear_after_settings_save' ], 10, 2 );
		register_activation_hook( $this->plugin_info->get_plugin_file(), [ $this, 'clear_cache' ] );
		register_deactivation_hook( $this->plugin_info->get_plugin_file(), [ $this, 'clear_cache' ] );
	}

	/**
	 * @param mixed[] $current_settings  .
	 * @param mixed[] $previous_settings .
	 *
	 * @return void
	 * @internal
	 */
	public function clear_after_settings_save( array $current_settings, array $previous_settings ) {
		if ( $previous_settings[ LoaderTypeOption::OPTION_NAME ] === $current_settings[ LoaderTypeOption::OPTION_NAME ] ) {
			return;
		}

		$this->clear_cache();
	}

	/**
	 * @return void
	 * @internal
	 */
	public function clear_cache() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'breeze/breeze.php' ) ) {
			do_action( 'breeze_clear_all_cache' );
		}
		if ( is_plugin_active( 'cache-enabler/cache-enabler.php' ) ) {
			do_action( 'cache_enabler_clear_complete_cache' );
		}
		if ( is_plugin_active( 'hummingbird-performance/wp-hummingbird.php' ) ) {
			do_action( 'wphb_clear_page_cache' );
		}
		if ( is_plugin_active( 'litespeed-cache/litespeed-cache.php' ) ) {
			do_action( 'litespeed_purge', '*' );
		}
		if ( is_plugin_active( 'sg-cachepress/sg-cachepress.php' ) && function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
		}
		if ( is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) && function_exists( 'w3tc_flush_posts' ) ) {
			w3tc_flush_posts();
		}
		if ( is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ) ) {
			do_action( 'wpfc_clear_all_cache' );
		}
		if ( is_plugin_active( 'wp-optimize/wp-optimize.php' ) && function_exists( 'wpo_cache_flush' ) ) {
			wpo_cache_flush();
		}
		if ( is_plugin_active( 'wp-super-cache/wp-cache.php' ) && function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}

		wp_cache_delete( 'alloptions', 'options' );
	}
}
