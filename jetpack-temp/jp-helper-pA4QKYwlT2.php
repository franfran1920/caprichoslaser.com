<?php /* Jetpack Backup Helper Script */

// The following placeholder will be replaced by the Transport Server. If you change / reformat it, make sure to make
// changes in the placeholder replacer code too.
define( 'JP_EXPIRES', 1686881852 );

// The following placeholder will be replaced by the Transport Server. If you change / reformat it, make sure to make
// changes in the placeholder replacer code too.
define( 'JP_SECRET', '2bsY06fV87gxYsdfB94YuhubJKBNgi4f' );

// This is used / replaced when the helper script is uploaded over HTTP.
define( 'WP_PATH', '[wp_path]' );

// Error codes
define( 'COMMS_ERROR',        128 );
define( 'MYSQLI_ERROR',       129 );
define( 'MYSQL_ERROR',        130 );
define( 'NOT_FOUND_ERROR',    131 );
define( 'READ_ERROR',         132 );
define( 'INVALID_TYPE_ERROR', 133 );
define( 'MYSQL_INIT_ERROR',   134 );
define( 'CREDENTIALS_ERROR',  135 );
define( 'WRITE_ERROR',        136 );
define( 'EXPIRY_ERROR',       137 );

// disable various swift-performance-lite features that interfere with us
// this caches things so aggressively that even helper script responses over SSH are cached!
// in theory there are filters defined in the plugin that should allow us to turn it off like that
// however, I was unable to get them to do anything, so we're stuck with this jank
$_GET['swift-no-cache'] = 1;
define( 'SWIFT_PERFORMANCE_THREAD', false );


/**
 * Tests whether the helper script is being run in a CLI.
 *
 * @return bool True if the helper script is being run in a CLI, false otherwise.
 */
function is_cli() {
	return 'cli' === php_sapi_name();
}

function fatal_error( $code, $message, $http_code = 200 ) {

	if ( is_cli() ) {
		fwrite( STDERR, "\n" . json_encode( array(
			'code'    => $code,
			'message' => $message,
		) ) . "\n" );
		die( $code );
	} else {
		header( 'X-VP-Ok: 0', true, $http_code );
		header( 'X-VP-Error-Code: ' . $code );
		header( 'X-VP-Error: ' . base64_encode( $message ) );
		exit;
	}
}

function success_header() {
	if ( ! is_cli() ) {
		header( 'X-VP-Ok: 1', true );
	}
}

function authenticate( $action, $json_args, $salt, $incoming_signature ) {
	$to_sign   = "{$action}:{$json_args}:{$salt}";
	$signature = hash_hmac( 'sha1', $to_sign, JP_SECRET );

	return hash_equals( $signature, $incoming_signature );
}

function jpr_action( $action, $args ) {
	$actions = array(
		'db_results'                  => 'action_db_results',
		'db_dump'                     => 'action_db_dump',
		'db_upload'                   => 'action_db_upload',
		'db_import'                   => 'action_db_import',
		'count_files'                 => 'action_count_files',
		'ls'                          => 'action_ls',
		'grep'                        => 'action_grep',
		'stat'                        => 'action_stat',
		'test'                        => 'action_test',
		'info'                        => 'action_info',
		'paths'                       => 'action_paths',
		'cleanup_helpers'             => 'action_cleanup_helpers',
		'cleanup_restore'             => 'action_cleanup_restore',
		'walk'                        => 'action_walk',
		'flush'                       => 'action_flush',
		'trigger_jp_sync'             => 'action_trigger_jp_sync',
		'delete_tree'                 => 'action_delete_tree',
		'get_active_theme'            => 'action_get_active_theme',
		'symlink'                     => 'action_symlink',
		'validate_theme'              => 'action_validate_theme',
		'woocommerce_install'         => 'action_woocommerce_install',
		'get_file'                    => 'action_get_file',
		'enable_jetpack_sso'          => 'action_enable_jetpack_sso',
		'transfer_jetpack_connection' => 'action_transfer_jetpack_connection',
		'install_extension'           => 'action_install_extension',
		'check_file_existence'        => 'action_check_file_existence',
		'upgrade_extension'           => 'action_upgrade_extension',
		'remove_waf_blocklog'         => 'action_remove_waf_blocklog',
		'dpc_receive'                 => 'action_dpc_receive',
	);

	if ( empty( $actions[ $action ] ) ) {
		fatal_error( COMMS_ERROR, 'Invalid method', 405 );
	}

	call_user_func( $actions[ $action ], $args );
}

function get_wordpress_location() {
	if ( '[wp_' . 'path]' !== WP_PATH ) {
		return rtrim( WP_PATH, '/\\' );
	} else {
		return dirname( __DIR__ );
	}
}

function localize_path( $path ) {
	return preg_replace( '/^{\$ABSPATH\}/', get_wordpress_location(), $path );
}

function load_wp( $with_plugins = false, $error_func = 'fatal_error', $short_init = false ) {
	if ( ! defined( 'WP_INSTALLING' ) && ! $with_plugins ) {
		define( 'WP_INSTALLING', true );
	}

	if ( ! defined( 'SHORTINIT' ) && $short_init ) {
		// Stop most of WordPress from being loaded if we just want the basics.
		// see https://wpengineer.com/2449/load-minimum-of-wordpress/
		// see https://stackoverflow.com/questions/5306612/using-wpdb-in-standalone-script
		define( 'SHORTINIT', true );
	}

	$wp_directory = get_wordpress_location();
	$wp_load_path = $wp_directory . '/wp-load.php';
	if ( ! file_exists( $wp_load_path ) ) {
		call_user_func( $error_func, CREDENTIALS_ERROR, "Could not find WordPress in {$wp_directory}" );
	}

	if ( ! is_readable( $wp_load_path ) ) {
		call_user_func( $error_func, CREDENTIALS_ERROR, "Can not read wp-load.php in {$wp_directory}" );
	}

	ob_start();
	require_once( $wp_load_path );
	ob_end_clean();
}

function encode_json_with_check( $obj ) {
	$json_options = 0;
	if ( defined( 'JSON_PARTIAL_OUTPUT_ON_ERROR' ) ) {
		// since PHP 5.5.0; allows us to handle more weird characters without completely failing
		// since PHP 5.4.0; gives us better output for some unicode characters that don't seem to escape otherwise
		$json_options = JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE;
	} elseif ( defined( 'JSON_UNESCAPED_UNICODE' ) ) {
		// since PHP 5.4.0; gives us better output for some unicode characters that don't seem to escape otherwise
		$json_options = JSON_UNESCAPED_UNICODE;
	}

	$json = json_encode( $obj, $json_options );
	if ( false === $json ) {
		fatal_error( COMMS_ERROR, 'JSON error: ' . json_last_error() );
	}

	return $json;
}

function send_json_with_check( $obj, $with_newline = true, $flush_afterwards = true ) {
	$json = encode_json_with_check( $obj );

	echo $json;
	if ( $with_newline ) {
		echo "\n";
	}

	if ( $flush_afterwards ) {
		// Some webservers and PHP configurations are made to buffer the output of scripts, and we both:
		//
		// 1. Expect to get the output of various commands (e.g. SQL imports) live, and
		// 2. Don't want the webserver or some middleware (e.g. Cloudflare) to time out idle connections (which might
		//    become idle if the webserver is buffering output and not sending anything back).
		//
		flush();
	}
}

function action_test( $args ) {
	success_header();
	echo json_encode( array( 'ok' => true ) );
	exit;
}

function action_flush( $args ) {
	if ( ! empty( $args['load_full_wp'] ) ) {
		load_wp( true );
	} else {
		load_wp();
	}

	delete_option( 'rewrite_rules' );

	if ( function_exists( 'wp_cache_flush' ) ) {
		wp_cache_flush();

		success_header();
		echo json_encode( array( 'ok' => true ) );
		exit;
	}

	if ( function_exists( 'wp_cache_clean_cache' ) ) {
		global $file_prefix;
		wp_cache_clean_cache( $file_prefix, true );
	}

	fatal_error( COMMS_ERROR, 'wp_cache_flush() not loaded' );
}

function action_trigger_jp_sync( $args ) {
	load_wp( true ); // need plugins so we get the JP functions

	if ( is_callable( array( 'Automattic\Jetpack\Sync\Actions', 'do_full_sync' ) ) ) {
		\Automattic\Jetpack\Sync\Actions::do_full_sync();

		success_header();
		echo json_encode( array( 'ok' => true, 'legacy_sync_call' => false ) );
		exit;
	}

	// this call is deprecated since jetpack-7.5,
	// but we still need to use it for sites running older versions of plugin
	if ( is_callable( array( 'Jetpack_Sync_Actions', 'do_full_sync' ) ) ) {
		Jetpack_Sync_Actions::do_full_sync();

		success_header();
		echo json_encode( array( 'ok' => true, 'legacy_sync_call' => true ) );
		exit;
	}

	fatal_error( COMMS_ERROR, 'Neither Automattic\Jetpack\Sync\Actions::do_full_sync() nor Jetpack_Sync_Actions::do_full_sync() loaded' );
}

function action_upgrade_extension( $args ) {
	load_wp( true );

	$slug = $args['slug'];
	$type = $args['type'];

	if ( function_exists( 'jetpack_require_lib' ) ) {
		jetpack_require_lib( 'plugins' );
	}

	if ( class_exists( 'Automatic_Upgrader_Skin' ) ) {
		$skin = new Automatic_Upgrader_Skin();

		if ( $type === 'plugin' ) {
			$upgrader = new Plugin_Upgrader( $skin );
			$extension_path = get_plugin_path_from_slug( $slug );
		} else {
			$upgrader = new Theme_Upgrader( $skin );
			$extension_path = $slug;
		}

		$result = $upgrader->upgrade( $extension_path );

		if ( ! $result ) {
			$error_messages = print_r( $skin->get_upgrade_messages(), true );
			fatal_error( COMMS_ERROR, 'Could not upgrade extension: ' . $error_messages );
		}

		if ( is_wp_error( $result ) ) {
			fatal_error( COMMS_ERROR, 'Could not upgrade extension: ' . $result->get_error_message() );
		}

		success_header();
		echo json_encode( array( 'ok' => true ) );
		exit;
	}

	fatal_error( COMMS_ERROR, 'Automatic_Upgrader_Skin not loaded' );
}

function action_remove_waf_blocklog( $args ) {
	load_wp( true );

	$contentPath = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : $absPath . '/wp-content';

	@unlink( $contentPath . '/jetpack-waf/waf-blocklog');

	success_header();
	echo json_encode( array( 'ok' => true ) );
	exit;
}

function action_install_extension( $args ) {
	load_wp( true );

	if ( function_exists( 'jetpack_require_lib' ) ) {
		jetpack_require_lib( 'plugins' );
	}

	$slug = $args['slug'];
	$type = $args['type'];

	if ( class_exists( 'Jetpack_Automatic_Install_Skin' ) ) {
		$skin = new Jetpack_Automatic_Install_Skin();

		if ( $type === 'plugin' ) {
			$upgrader = new Plugin_Upgrader( $skin );
			$zipUrl = "https://downloads.wordpress.org/plugin/$slug.latest-stable.zip";
		} else {
			$upgrader = new Theme_Upgrader( $skin );
			$zipUrl = "https://downloads.wordpress.org/theme/$slug.latest-stable.zip";
		}

		$result = $upgrader->install( $zipUrl );

		if ( ! $result ) {
			fatal_error( COMMS_ERROR, 'Could not install extension' );
		}

		if ( is_wp_error( $result ) ) {
			fatal_error( COMMS_ERROR, 'Could not install extension: ' . $result->get_error_message() );
		}

		success_header();
		echo json_encode( array( 'ok' => true ) );
		exit;
	}

	fatal_error( COMMS_ERROR, 'Jetpack_Plugins::install_plugin() not loaded' );
}

function action_info( $args ) {
	if ( ! empty( $args['load_full_wp'] ) ) {
		load_wp( true );
	} else {
		load_wp();
	}
	global $wpdb, $wp_version, $wp_theme_directories;

	// get installed themes.
	$themes = array();
	$current_theme = wp_get_theme();
	foreach ( wp_get_themes() as $key => $theme ) {
		$themes[ $key ] = array(
			'Name' => $theme['Name'],
			'ThemeURI' => $theme->get( 'ThemeURI' ),
			'Version' => $theme['Version'],
			'Author' => $theme->get( 'Author' ), // use get() to get the raw value; array access uses display() not get()
			'AuthorURI' => $theme->get( 'AuthorURI'),
			'path' => base64_encode( $theme->get_stylesheet_directory() . '/style.css' ),
			'status' => $theme['Name'] === $current_theme['Name'] ? 'active': 'inactive',
		);
	}

	// get installed plugins.
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugins = get_plugins();

	// post-process so these are by slug too, like themes.
	$plugins_by_slug = array();
	foreach ( $plugins as $path => $plugin ) {
		if ( false === strpos( $path, '/' ) ) {
			$slug = explode( '.php', $path );
			$slug = $slug[0];
		} else {
			$slug = explode( '/', $path );
			$slug = $slug[0];
		}
		$plugins_by_slug[ $slug ] = $plugin;
		$plugins_by_slug[ $slug ]['path'] = base64_encode( WP_PLUGIN_DIR . '/' . $path );
		$plugins_by_slug[ $slug ]['status'] = is_plugin_active( $path ) ? 'active' : 'inactive';
	}

	// grab some useful constants
	$useful_constants = array( 'IS_PRESSABLE', 'VIP_GO_ENV' );
	$constant_values = array();
	foreach ( $useful_constants as $constant ) {
		if ( defined( $constant ) ) {
			$constant_values[ $constant ] = constant( $constant );
		}
	}

	// get info about foreign key constraints
	$fks = $wpdb->get_results( $wpdb->prepare( "
		SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
		FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
		WHERE REFERENCED_TABLE_SCHEMA = %s
		AND TABLE_NAME LIKE %s",
		$wpdb->dbname,
		$wpdb->esc_like( $wpdb->prefix ) . '%'
	) );

	$absPath = get_wordpress_location();
	$contentPath = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : $absPath . '/wp-content';
	$pluginsPath = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : $contentPath . '/plugins';
	$uploadsPath = wp_upload_dir()['basedir'];
	if ( ! is_dir( $uploadsPath ) ) {
		$uploads_path_option = trim( get_option( 'upload_path' ) );
		if ( ! empty( $uploads_path_option ) && is_dir( $absPath . '/' . $uploads_path_option ) ) {
			$uploadsPath = $absPath . '/' . $uploads_path_option;
		} else if ( defined( 'UPLOADS' ) && is_dir( $absPath . '/' . UPLOADS ) ) {
			$uploadsPath = $absPath . '/' . UPLOADS;
		} else {
			$uploadsPath = $contentPath . '/uploads';
		}
	}

	$theme_paths = $wp_theme_directories;
	if ( ! is_array( $wp_theme_directories ) ) {
		$theme_paths = array( $contentPath . '/themes' );
	}

	// If the content directory is considered "under" the abspath it won't be
	// explicitly walked.  If it's encountered as a symlink during normal walking
	// it will be skipped if it's not under abspath.  We resolve it so that it
	// will be explicitly walked if it resolves outside of abspath.
	if ( is_link( $contentPath ) ) {
		$symlinkContent = $contentPath;
		$contentPath = realpath( $contentPath );

		if ( strpos( $pluginsPath, $symlinkContent ) == 0 ) {
			$pluginsPath = str_replace( $symlinkContent, $contentPath, $pluginsPath );
		}
		if ( strpos( $uploadsPath, $symlinkContent ) == 0 ) {
			$uploadsPath = str_replace( $symlinkContent, $contentPath, $uploadsPath );
		}
		$idx = 0;
		foreach( $theme_paths as $theme_path ) {
			if ( strpos( $theme_path, $symlinkContent ) == 0 ) {
				$theme_paths[$idx] = str_replace( $symlinkContent, $contentPath, $theme_path );
			}
			++$idx;
		}
	}

	success_header();
	send_json_with_check( array(
		'wp_version' => $wp_version,
		'php_version' => phpversion(),
		'php_settings' => array(
			'memory_limit' => ini_get( 'memory_limit' ),
			'max_execution_time' => ini_get( 'max_execution_time' ),
		),
		'locale' => get_locale(),
		'table_prefix' => $wpdb->prefix,
		'themes' => $themes,
		'plugins' => $plugins_by_slug,
		'constants' => $constant_values,
		'foreign_keys' => $fks,
		'multisite' => is_multisite(),
		'themePaths' => array_map( 'base64_encode', $theme_paths ),
		'pluginsPath' => base64_encode( $pluginsPath ),
		'contentPath' => base64_encode( $contentPath ),
		'uploadsPath' => base64_encode( $uploadsPath ),
		'abspath' => base64_encode( $absPath ),
		'baseUrl' => get_site_url(),
	), false );
}

/**
 * This is a simplified version of info() method above that returns paths-related fields only.
 *
 * It's used by toPhpPath() function in Transport Server.
 *
 * This method applies a fallback for cases where wp-config.php file is not present on the site.
 */
function action_paths( $args ) {
	$is_wp_loaded = false;
	$absPath = get_wordpress_location();

	if ( file_exists( $absPath . '/wp-load.php' ) ) {
		global $wp_theme_directories;

		if ( ! empty( $args['load_full_wp'] ) ) {
			load_wp( true );
		} else {
			load_wp();
		}
		$is_wp_loaded = true;

		$contentPath = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : $absPath . '/wp-content';
		$pluginsPath = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : $contentPath . '/plugins';
		$uploadsPath = wp_upload_dir()['basedir'];

		$themePaths = $wp_theme_directories;
		if ( ! is_array( $wp_theme_directories ) ) {
			$themePaths = array( $contentPath . '/themes' );
		}
	}
	else {
		// apply a fallback for cases where wp-config.php is not there
		$contentPath = $absPath . '/wp-content';
		$pluginsPath = $contentPath . '/plugins';
		$uploadsPath = $contentPath . '/uploads';
		$themePaths  = array( $contentPath . '/themes' );
	}

	success_header();
	send_json_with_check( array(
		'is_wp_loaded' => $is_wp_loaded,
		'abspath' => base64_encode( $absPath ),
		'pluginsPath' => base64_encode( $pluginsPath ),
		'contentPath' => base64_encode( $contentPath ),
		'uploadsPath' => base64_encode( $uploadsPath ),
		'themePaths' => array_map( 'base64_encode', $themePaths ),
	), false );
}

function db_query( $sql, $error_func = 'fatal_error' ) {
	global $wpdb;

	if( ! is_object( $wpdb ) ) {
		/**
		 * TODO: Replace the load_wp short init call with function to scrape the DB config from wp-config.php,
		 * or load from environment variables.
		 */
		load_wp( false, $error_func, true );
	}

	if ( ! $wpdb->dbh ) {
		call_user_func( $error_func, MYSQL_INIT_ERROR, 'MySQL not initialized' );
	}

	// prepend the SQL query comment with the source
	// https://www.php.net/manual/en/function.debug-backtrace.php
	$backtrace = debug_backtrace( /* $options = */ DEBUG_BACKTRACE_IGNORE_ARGS,  /* $limit = */ 2 );
	$caller = isset( $backtrace[ 1 ] ) ? $backtrace[ 1 ][ 'function' ] : __FUNCTION__;

	// allow only alphanumeric characters and the underscore
	$caller = preg_replace( '#[^a-z0-9_]#i', '', $caller );
	$sql = "/* jp-helper {$caller}() */ {$sql}";

	if ( $wpdb->use_mysqli ) {
		$result = mysqli_query( $wpdb->dbh, $sql, MYSQLI_USE_RESULT );
		if ( ! $result ) {
			call_user_func( $error_func, MYSQLI_ERROR, mysqli_error( $wpdb->dbh ) );
		}
	} else {
		$result = mysql_unbuffered_query( $sql, $wpdb->dbh );
		if ( ! $result ) {
			call_user_func( $error_func, MYSQL_ERROR, mysql_error( $wpdb->dbh ) );
		}
	}

	return $result;
}

function action_db_results( $args ) {
	global $wpdb;

	$args = array_merge( array(
		'query' => null,
	), $args );

	$query = db_query( $args['query'] );

	success_header();

	$fields = null;
	$types = [];

	$flush_every_x_rows = 100;
	$row_number = 0;

	while ( ! empty( $row = ( $wpdb->use_mysqli ? mysqli_fetch_assoc( $query ) : mysql_fetch_assoc( $query ) ) ) ) {
		// First row; detect the names of the fields, send as an array.
		if ( empty( $fields ) ) {
			$fields = array_keys( $row );
			send_json_with_check( array_map( 'base64_encode', $fields ), true, false );

			// detect columns types to provide better typing of dumped values
			// for instance do not base64-encode numeric values in BIT columns
			if ( $wpdb->use_mysqli ) {
				foreach ( $fields as $field_mame ) {
					// @see https://www.php.net/manual/en/mysqli-result.fetch-field.php
					$info = mysqli_fetch_field( $query );

					if ( is_object( $info ) && ( $field_mame === $info->name ) ) {
						$types[$info->name] = [
							'flags'      => $info->flags,
							// for, now apply the numeric encoding to BIT columns only
							// @see https://www.php.net/manual/en/mysqli.constants.php
							'is_numeric' => in_array( $info->type, [
								16, // MYSQLI_TYPE_BIT
							] ),
						];
					}
				}
			}
		}

		$values = array_map(
			function( $field ) use ( &$row, $types ) {
				if ( is_null( $row[$field] ) ) {
					return null;
				}

				if ( !empty( $types[ $field ][ 'is_numeric'] ) ) {
					return (int) $row[$field];
				}

				return base64_encode( $row[$field] );
			},
			$fields
		);
		send_json_with_check( $values, true, false );

		++$row_number;
		if ( 0 === ( $row_number % $flush_every_x_rows ) ) {
			flush();
		}
	}

	flush();

	if ( $wpdb->use_mysqli ) {
		@mysqli_free_result( $query );
	} else {
		@mysql_free_result( $query );
	}
}

/**
 * This method is similar to action_db_results but adds a prefix "WPFIELDTYPE" with the type of the field.
 * This can be used by the received to take decisions about how to read it.
 *
 * @param array $args Array with the info of the query to run
 */
function action_db_dump( $args ) {
	global $wpdb;

	$args = array_merge( array(
		'query' => null,
	), $args );

	$query = db_query( $args['query'] );

	success_header();

	$fields = null;

	$flush_every_x_rows = 100;
	$row_number = 0;

	while ( ! empty( $row = ( $wpdb->use_mysqli ? mysqli_fetch_assoc( $query ) : mysql_fetch_assoc( $query ) ) ) ) {
		// First row; detect the names of the fields, send as an array.
		if ( empty( $fields ) ) {
			$fields = array_keys( $row );
			send_json_with_check( array_map( 'base64_encode', $fields ), true, false );
		}

		$index = 0;
		$values = [];
		foreach ( $fields as $field_key ) {
			$field = mysqli_fetch_field_direct( $query, $index );

			if ( is_null( $row[ $field_key ] ) ) {
				$values[] = null;
			} else {
				$values[] = base64_encode( 'WPFIELDTYPE' . $field->type . '_' . $row[ $field_key ] . '' );
			}
			$index++;
		}

		send_json_with_check( $values, true, false );

		++$row_number;
		if ( 0 === ( $row_number % $flush_every_x_rows ) ) {
			flush();
		}
	}

	flush();

	if ( $wpdb->use_mysqli ) {
		@mysqli_free_result( $query );
	} else {
		@mysql_free_result( $query );
	}
}

function action_db_upload( $args ) {
	$args = array_merge( array(
		'sql' => null,
	), $args );

	foreach ( explode( ";\n", $args['sql'] ) as $line ) {
		$line = trim( $line );
		if ( empty( $line ) ) {
			continue;
		}

		db_query( $line );
	}

	success_header();
	echo "Success\n";
}

function streamed_error( $code, $message ) {
	// no cli/http switch, just send the content in whatever stream this is
	send_json_with_check( array( 'error' => true, 'code' => $code, 'message' => $message ) );
	exit;
}


/**
 * action_db_import()'s "executor", i.e. class that imports SQL queries or reports statuses.
 *
 * Implementations might choose to do the actual import / report back errors in a HTTP response, or log imported
 * queries / statuses for testing purposes.
 */
interface Action_DB_Import_Executor {

	/**
	 * Import a SQL query.
	 *
	 * @param string $sql        SQL query.
	 * @param string $error_func Function to call on errors.
	 *
	 * @return bool|mysqli_result|resource Return value of either mysqli_query() or mysql_query().
	 */
	public function db_query( $sql, $error_func );

	/**
	 * Report back import status.
	 *
	 * @param mixed $obj Free-form status message to be encoded to JSON and sent back to the caller.
	 *
	 * @return void
	 */
	public function status_report( $obj );

}


/**
 * action_db_import()'s default "executor" which executes queries in MySQL and sends back JSON responses.
 */
class Action_DB_Import_Default_Executor implements Action_DB_Import_Executor {

	/** @noinspection PhpDeprecationInspection */
	public function db_query( $sql, $error_func ) {

		// Same as the db_query() global function, just doesn't use unbuffered functions to avoid the need to free the
		// result, e.g. if we were to use mysqli_query( ..., MYSQLI_USE_RESULT ), one of the queries returned
		// something, and we forgot to read/free that returned value, the subsequent queries would stop working.

		global $wpdb;

		if ( ! is_object( $wpdb ) ) {
			// load minimum wp with SHORTINIT 
			load_wp( false, $error_func, true );
		}

		if ( ! $wpdb->dbh ) {
			call_user_func( $error_func, MYSQL_INIT_ERROR, 'MySQL not initialized' );
		}

		if ( $wpdb->use_mysqli ) {
			$result = mysqli_query( $wpdb->dbh, $sql );
			if ( ! $result ) {
				call_user_func( $error_func, MYSQLI_ERROR, mysqli_error( $wpdb->dbh ) );
			}
		} else {
			$result = mysql_query( $sql, $wpdb->dbh );
			if ( ! $result ) {
				call_user_func( $error_func, MYSQL_ERROR, mysql_error( $wpdb->dbh ) );
			}
		}

		return $result;
	}

	public function status_report( $obj ) {
		send_json_with_check( $obj );
	}

}



/**
 * action_db_import()'s "stopper", i.e. class that decides when we should stop importing a part of an SQL dump.
 *
 * Implementations might count number of queries or time elapsed since the start of the import.
 */
interface Action_DB_Import_Stopper {

	/**
	 * @param string $line SQL line that was just imported.
	 *
	 * @return bool If true, SQL import should be stopped as we've imported enough for this action_db_import() call.
	 */
	public function should_stop_after_importing_line( $line );

}


/**
 * "Stopper" that counts the number of seconds elapsed, and stops importing after some time.
 */
class Action_DB_Import_Time_Stopper implements Action_DB_Import_Stopper {

	/**
	 * Number of seconds to let the import go on for.
	 *
	 * @var int
	 */
	private $seconds;

	/**
	 * UNIX timestamp on when the import has started.
	 *
	 * @var int
	 */
	private $started;

	/**
	 * Constructor.
	 *
	 * @param int $seconds Number of seconds to let the import go on for.
	 */
	public function __construct( $seconds ) {
		$this->seconds = $seconds;
		$this->started = time();
	}

	public function should_stop_after_importing_line( $line ) {
		if ( time() - $this->started >= $this->seconds ) {
			return true;
		}

		return false;
	}

}


/**
 * Import a part of the SQL dump using the "executor", stop importing when "stopper" decides so.
 *
 * Won't stop importing when executing queries between "--block-sql-start;" and "--block-sql-end;" markers.
 *
 * Calls streamed_error() on errors.
 *
 * @param resource                  $handle                File handle to read the SQL dump from.
 * @param int                       $lines_imported_so_far Number of SQL statements imported so far.
 * @param int                       $sql_dump_byte_offset  Byte offset in the dump file from which to start importing.
 * @param Action_DB_Import_Executor $executor              "Executor", i.e. the object that will execute SQL queries
 *                                                         and report statuses.
 * @param Action_DB_Import_Stopper  $stopper               "Stopper", i.e. the object that will decide when to start
 *                                                         importing the dump.
 * @param int                       $chunk_size            How many bytes to read from the SQL dump file in one go.
 *
 * @return void
 */
function do_action_db_import(
	$handle,
	$lines_imported_so_far,
	$sql_dump_byte_offset,
	$executor,
	$stopper,
	$chunk_size = 100 * 1024
) {

	$indivisible_block_start_marker = '-- block-sql-start;';
	$indivisible_block_end_marker   = '-- block-sql-end;';

	$inside_indivisible_block = false;

	$stopped_by_stopper = false;

	$buffer_remainder = '';

	fseek( $handle, $sql_dump_byte_offset );

	while ( ( ! feof( $handle ) ) && ( ! $stopped_by_stopper ) ) {

		$buffer = fread( $handle, $chunk_size );
		if ( false === $buffer ) {
			streamed_error( READ_ERROR, 'Unable to fread() from the dump file' );

			return;
		}

		$buffer = $buffer_remainder . $buffer;

		// We use regexps here to handle optional "\r\n" style newlines as well as sane "\n" newlines.
		// mysqldump generates these, sadly.
		$lines_and_offsets = preg_split( "/;\r?\n/", $buffer, - 1, PREG_SPLIT_OFFSET_CAPTURE );

		// Keep track of the offset of the next match, so that if the "stopper" decides that it's time for us to stop
		// importing after we execute the current line, the offset of the next line can be sent back to the caller for
		// the purpose of resuming the import at the right location.
		$next_offset = null;

		// Skip the last line as it will be a part of the next buffer (buffer remainder).
		for ( $x = 0; $x < count( $lines_and_offsets ) - 1; ++ $x ) {

			$current_line_and_offset = $lines_and_offsets[ $x ];
			$current_line            = trim( $current_line_and_offset[0] );

			$next_line_and_offset = $lines_and_offsets[ $x + 1 ];
			$next_offset          = $next_line_and_offset[1];

			if ( empty( $current_line ) ) {
				continue;
			}

			// Add back the ";" that the preg_split() has removed.
			$current_line .= ';';

			if ( $indivisible_block_start_marker === $current_line ) {
				$inside_indivisible_block = true;
			} elseif ( $indivisible_block_end_marker === $current_line ) {
				$inside_indivisible_block = false;
			} else {
				$executor->db_query( $current_line, 'streamed_error' );
				++ $lines_imported_so_far;
			}

			if ( $stopper->should_stop_after_importing_line( $current_line ) ) {

				if ( ! $inside_indivisible_block ) {
					$stopped_by_stopper = true;
					break;
				}
			}
		}

		// Next offset might be null if preg_split() didn't find any newlines.
		if ( ! is_null( $next_offset ) ) {
			$sql_dump_byte_offset += $next_offset;
		}

		$last_line_and_offset = end( $lines_and_offsets );
		$last_line            = $last_line_and_offset[0];

		$buffer_remainder = $last_line;
	}

	if ( $stopped_by_stopper ) {

		$executor->status_report(
			array(
				'error'    => false,
				'message'  => "Imported $lines_imported_so_far queries so far",
				'offset'   => $sql_dump_byte_offset,
				'imported' => $lines_imported_so_far,
			)
		);

	} else {

		// Import the remainder only if not stopped by the stopper, i.e. we're at the end of the dump. Let's hope the
		// remainder doesn't take too much time to import.
		$buffer_remainder = trim( $buffer_remainder );
		if ( strlen( $buffer_remainder ) > 0 ) {

			$executor->db_query( $buffer_remainder, 'streamed_error' );

			if ( ! in_array(
				$buffer_remainder,
				array(
					$indivisible_block_start_marker,
					$indivisible_block_end_marker,
				)
			) ) {
				++ $lines_imported_so_far;
			}
		}

		$executor->status_report(
			array(
				'error'    => false,
				'message'  => "Success, $lines_imported_so_far queries overall",
				'imported' => $lines_imported_so_far,
			)
		);

	}

}


/**
 * Import a part of the SQL dump, stop after a few seconds, return an offset.
 *
 * The function assumes that each and every statement is on its own line.
 *
 * Will stop importing SQL queries after it has imported 4 seconds worth of queries. However, the function will not
 * stop importing in the middle of the indivisible block, i.e. a block of SQL enclosed within "--block-sql-start;" and
 * "--block-sql-end;" markers (each on their very own line, and with the semicolon at the end).
 *
 * @param array $args Argument array:
 *                    * "importPath" (string, required) - path to the SQL dump to import;
 *                    * "imported" (integer, optional) - number of SQL statements imported so far;
 *                    * "offset" (integer, optional) - byte offset in the dump file from which to start importing.
 *
 * @return void
 * @throws Exception
 */
function action_db_import( $args ) {
	// Start reporting back right away, we stream back errors and status via the body.
	success_header();

	$stop_after_seconds = 4;

	if ( empty( $args['importPath'] ) ) {
		streamed_error( COMMS_ERROR, 'Invalid path' );
	}

	$import_path    = localize_path( base64_decode( $args['importPath'] ) );
	$lines          = ! empty( $args['imported'] ) && is_numeric( $args['imported'] ) ? $args['imported'] : 0;
	$current_offset = ! empty( $args['offset'] ) && is_numeric( $args['offset'] ) ? $args['offset'] : 0;

	if ( ! file_exists( $import_path ) ) {
		streamed_error( NOT_FOUND_ERROR, "File not found: $import_path" );
	}

	$handle = fopen( $import_path, 'rb' );
	if ( false === $handle ) {
		streamed_error( READ_ERROR, "Failed to open file $import_path for import." );
	}

	do_action_db_import(
		$handle,
		$lines,
		$current_offset,
		new Action_DB_Import_Default_Executor(),
		new Action_DB_Import_Time_Stopper( $stop_after_seconds )
	);

	fclose( $handle );
}


function clean_pathname_string( $path ) {
	// paths are arbitrary bytes, send them in base-64 so JSON doesn't choke on them
	return base64_encode( $path );
}

function get_username( $stat ) {
	$info = false;
	if ( function_exists( 'posix_getpwuid' ) ) {
		$info = posix_getpwuid( $stat['uid'] );
	}

	if ( $info ) {
		return $info['name'];
	} else {
		return $stat['uid'];
	}
}

function get_groupname( $stat ) {
	$info = false;
	if ( function_exists( 'posix_getgrgid' ) ) {
		$info = posix_getgrgid( $stat['gid'] );
	}

	if ( $info ) {
		return $info['name'];
	} else {
		return $stat['gid'];
	}
}

function get_ls_entry( &$args, $path, $file, $skip_hashes = false ) {
	$full_path = $path . '/' . $file;
	$entry = array(
		'name' => clean_pathname_string( $file ),
	);

	if ( is_link( $full_path ) ) {
		$entry['is_link'] = 1;
		$entry['canonical'] = clean_pathname_string( readlink( $full_path ) );
		$entry['absolute'] = clean_pathname_string( realpath( $full_path ) );
	} else {
		$entry['canonical'] = clean_pathname_string( realpath( $full_path ) );
		$entry['absolute'] = $entry['canonical'];
	}

	// TODO: Replace this with the special path (ie. {$PLUGINS}/...)
	$entry['relative'] = clean_pathname_string( str_replace( get_wordpress_location(), '', $full_path ) );

	if ( ! is_readable( $full_path ) ) {
		$entry['unreadable'] = true;
	}

	if ( ! empty( $args['stat'] ) ) {
		$entry['stat'] = stat( $full_path );
		$entry['stat']['username'] = get_username( $entry['stat'] );
		$entry['stat']['groupname'] = get_groupname( $entry['stat'] );
	}

	if ( ! empty( $args['lstat'] ) ) {
		$entry['stat'] = lstat( $full_path );
		$entry['stat']['username'] = get_username( $entry['stat'] );
		$entry['stat']['groupname'] = get_groupname( $entry['stat'] );
	}

	// Remove duplicate data from stat.  We reference the associative values only.
	$numeric_stat_array_total = 13;
	for ( $i = 0; $i < $numeric_stat_array_total; $i++ ) {
		unset( $entry['stat'][$i] );
	}

	if ( isset( $args['window'] ) && floatval( $args['window'] > 1 ) ) {
		// if the caller is windowing the hashes, let them know that the file is unchanged in that window
		// thus, they will not expect the hash to be set for it
		$entry['unchanged'] = ( $entry['stat']['mtime'] < floatval( $args['window'] ) );
	}

	if ( is_dir( $full_path ) ) {
		$entry['is_dir'] = 1;
	} else if ( ! $skip_hashes ) {
		if ( ! is_array( $args['hashes'] ) ) {
			if ( ! empty( $args['hashes'] ) ) {
				$args['hashes'] = array( $args['hashes'] );
			} else {
				$args['hashes'] = array();
			}
		}

		if ( ! $args['window'] || ! $entry['unchanged'] ) {
			// only hash files if the caller didn't specify a window to do that in, or if the file changed in the window
			foreach ( $args['hashes'] as $algo ) {
				if ( in_array( $algo, hash_algos(), true ) ) {
					$entry[ $algo ] = hash_file( $algo, $full_path );
				}
			}
		}
	}

	if ( ! empty( $entry['is_dir'] ) ) {
		// check if this is a WP directory
		if ( is_file( $full_path . '/wp-config.php' ) && is_dir( $full_path . '/wp-content' ) ) {
			$entry['is_wp_root'] = 1;
		}

		// check if this directory contains a donotbackup file
		if ( is_file( $full_path . '/.donotbackup' ) ) {
			$entry['do_not_backup'] = 1;
		}
	}

	return $entry;
}

function locale_safe_basename( $path ) {
	$parts = explode( '/', $path );
	$ret = $parts[ count( $parts ) - 1 ]; // last element
	if ( $ret === '' && count( $parts ) > 1 ) {
		// path ended with a slash; to match dirname() + basename() we need to return the last directory element, not blank
		// make sure we have another choice though
		$ret = $parts[ count( $parts ) - 2 ];
	}
	return $ret;
}

function action_check_file_existence( $args ) {
	$args = array_merge( array(
		'path'   => '/',
		'hashes' => array(),
	), $args );

	$path = localize_path( base64_decode( $args['path'] ) );

	if ( ! file_exists( $path ) ) {
		success_header();
		send_json_with_check( array(
			'found' => false,
		) );
		exit;
	}

	if ( ! $args['lstat'] ) {
		$args['stat'] = true;
	}
	$entry = get_ls_entry( $args, dirname( $path ), locale_safe_basename( $path ) );

	$output = array( 'found' => true );

	foreach ( $args['hashes'] as $algo ) {
			$output[ $algo ] = $entry[ $algo ];
	}

	success_header();
	send_json_with_check( $output, false );
	exit;
}

function action_stat( $args ) {
	$args = array_merge( array(
		'path'   => '/',
		'hashes' => array(),
		'window' => false,
		'lstat' => false,
	), $args );

	$path = localize_path( base64_decode( $args['path'] ) );

	if ( ! file_exists( $path ) ) {
		fatal_error( NOT_FOUND_ERROR, "File not found: {$path}" );
	}

	if ( ! $args['lstat'] ) {
		$args['stat'] = true;
	}
	$entry = get_ls_entry( $args, dirname( $path ), locale_safe_basename( $path ) );

	success_header();
	send_json_with_check( $entry, false );
	exit;
}

function delete_tree( $path ) {
	$entries_deleted = 1;

	if ( ! is_dir( $path ) ) {
		fatal_error( INVALID_TYPE_ERROR, 'Not a directory: ' . $path );
	}

	foreach ( scandir( $path ) as $name ) {
		if ( $name == '.' || $name == '..' ) {
			continue;
		}

		$child = $path . '/' . $name;
		if ( is_dir( $child ) ) {
			$entries_deleted += delete_tree( $child );
		} else {
			if ( ! @unlink( $child ) ) {
				fatal_error( WRITE_ERROR, "Failed to delete file: {$child}" );
			}
			$entries_deleted++;
		}
	}

	if ( ! @rmdir( $path ) ) {
		fatal_error( WRITE_ERROR, "Failed to delete folder: {$path}" );
	}

	return $entries_deleted;
}

function action_symlink( $args ) {
	if ( empty( $args['path'] ) ) {
		fatal_error( INVALID_TYPE_ERROR, 'Invalid path' );
	}

	if ( empty( $args['target'] ) ) {
		fatal_error( INVALID_TYPE_ERROR, 'Invalid target' );
	}

	$path = localize_path( base64_decode( $args['path'] ) ); // name of the symlink
	$target = localize_path( base64_decode( $args['target'] ) ); // where it points

	$ret = symlink( $target, $path );

	if ( ! $ret ) {
		fatal_error( WRITE_ERROR, 'Symlink failed' );
	}

	success_header();
	echo json_encode( array( 'ok' => true ) );
	exit;
}

function action_delete_tree( $args ) {
	if ( empty( $args['path'] ) ) {
		fatal_error( INVALID_TYPE_ERROR, 'Invalid path' );
	}

	$path = localize_path( base64_decode( $args['path'] ) );
	$entries_deleted = delete_tree( $path );

	success_header();
	send_json_with_check( array(
		'entries' => $entries_deleted,
	) );
	exit;
}

function action_count_files( $args ) {
	if ( empty( $args['path'] ) ) {
		fatal_error( COMMS_ERROR, 'Missing $args[\'path\']' );
	}

	$queue = [ localize_path( base64_decode( $args['path'] ) ) ];
	$result = 0;

	while ( count( $queue ) > 0 ) {
		$parent = array_shift( $queue );
		$dh = opendir( $parent );

		if ( ! $dh ) {
			// not fatal (like /filesystem/walk)
			continue;
		}

		while ( ( $child = readdir( $dh ) ) !== false ) {
			if ( '.' === $child || '..' === $child ) {
				continue;
			}

			$result++;
			$path = "$parent/$child";

			if ( is_dir( $path ) && ! is_link( $path ) ) {
				if ( ! in_array( $path, $queue ) ) {
					array_push( $queue, $path );
				}
			}
		}

		closedir( $dh );
	}

	success_header();
	send_json_with_check( [ 'count' => $result ], false );
	exit;
}

function action_ls( $args ) {
	$args = array_merge( array(
		'path'   => '/',
		'hashes' => array(),
		'stat'   => false,
		'window' => false,
		'include_special_dirs' => false,
	), $args );

	$path = localize_path( base64_decode( $args['path'] ) );

	if ( ! is_dir( $path ) ) {
		fatal_error( INVALID_TYPE_ERROR, "Not a directory: {$path}" );
	}

	$dh = opendir( $path );
	if ( ! $dh ) {
		fatal_error( READ_ERROR, "Failed to read directory: {$path}" );
	}

	success_header();
	while ( ( $file = readdir( $dh ) ) !== false ) {
		if ( ( '.' === $file || '..' === $file ) && ! $args['include_special_dirs'] ) {
			continue;
		}

		$entry = get_ls_entry( $args, $path, $file );

		send_json_with_check( $entry );
	}

	closedir( $dh );
	exit;
}

function action_grep( $args ) {
	$args = array_merge( array(
		'phrase' => '',
		'stat'   => false,
	), $args );

	$phrase = escapeshellarg( base64_decode( $args['phrase'] ) );
	$wp_path = get_wordpress_location() . '/*';
	$output = [];

	exec( "grep --recursive --files-with-matches --exclude-dir=jetpack-temp {$phrase} {$wp_path}", $output );

	if ( false === $output ) {
		fatal_error( READ_ERROR, "Failed to run grep" );
	}

	success_header();
	foreach ( $output as $file ) {
		$absolute_path = dirname( $file );
		$filename = basename( $file );

		$entry = get_ls_entry( $args, $absolute_path, $filename );

		send_json_with_check( $entry );
	}

	exit;
}

function action_walk( $args ) {
	$args = array_merge( array(
		'root'              => '/',
		'paths'             => array(),
		'hashes'            => array(),
		'stat'              => false,
		'window'            => false,
		'skip_large_hashes' => false,
		'limit'             => 0,
		'offset'            => 0,
	), $args );

	$paths = array_map( 'base64_decode', $args['paths'] );
	$root = localize_path( base64_decode( $args['root'] ) );
	$soft_limit = 3000;

	// Use execution time to scale up soft time limit
	// It's possible for ini_get to return false
	$max_execution_time    = ini_get('max_execution_time') ? intval( ini_get('max_execution_time') ) : 0;
	$soft_time_window_base = 7;
	$soft_time_window      = $max_execution_time > 30 ? floor( $max_execution_time / 30 * $soft_time_window_base ) : $soft_time_window_base;
	$soft_time_limit       = time() + 7;
	$entries               = 0;
	$first_path            = true;
	success_header();

	// TODO: Rename this through the stack to be more clear.  This deals more with aggregating file sizes
	// and we have a simiarly named client callback that works differently and is unrelated.  We also
	// $large_file_threshold that actually skips individual large file hashes.
	$skip_large_hashes = $args['skip_large_hashes'];

	// Theshold of files considered "small"; 500kb.
	// Files smaller than this are not affected by the hash limit.
	$small_file_threshold = 500 * 1024;

	// Threshold of files considered "large"; 200MB
	// This is used to explicitly exclude large files and have them individually hashed
	// so they won't impact walk limits.  Should be based on the larger 1% of files but
	// 200MB was the initial guess, can be adjusted when we have more data.
	// This is checked against regardless of $skip_large_hashes, which applies to total
	// accumulated file hashes.
	$large_file_threshold = 200 * 1024 * 1024;

	// Track how much data to hash before giving up on non-small-files.
	// CLI can have much more generous timeouts, as its executed over SSH.
	if ( is_cli() ) {
		$hash_limit = 1 * 1024 * 1024 * 1024; // 1 GB
	} else {
		$hash_limit = 200 * 1024 * 1024; // 200 MB
	}

	if ( substr( $root, -1 ) != '/' ) {
		$root .= '/';
	}

	while ( count( $paths ) > 0 && ( $first_path || time() < $soft_time_limit ) ) {
		$relative_path = array_shift( $paths );
		$absolute_path = $root . $relative_path;

		// Fetch information about the path, prepare a header for it.
		$path_details = get_ls_entry( $args, dirname( $absolute_path ), locale_safe_basename( $absolute_path ) );
		$path_details['ls'] = clean_pathname_string( $relative_path );
		$path_header = encode_json_with_check( $path_details ) . "\n";

		$dh = opendir( $absolute_path );
		if ( ! $dh ) {
			echo $path_header . json_encode( array( 'error' => 'Failed to read ' . $absolute_path ) ) . "\n";
			continue;
		}

		// Sort files for pagination
		$files = array();
		while ( false !== ( $file = readdir( $dh ) ) ) {
			if ( '.' === $file || '..' === $file ) {
				continue;
			}

			array_push( $files, $file );
		}

		sort($files);
		closedir( $dh );

		// Apply limits to first directory only, additional directories will be limited by soft_limits
		// Default limit of 0 lists all files
		if ( $first_path ) {
			$start = intval( $args['offset'] );
			if ( 0 === intval( $args['limit'] ) ) {
				$end = count( $files );
			} else {
				$end = min( $start + intval( $args['limit'] ), count( $files ) );
			}
		} else {
			$start = 0;
			$end = count( $files );
		}

		// Send a header for the first path after successfully reading an entry.
		// Don't send if we're not at the start of pagination
		// Prevents masking errors behind successful-looking headers.
		if ( $first_path && 0 === $start && ! empty( $path_header ) ) {
			echo $path_header;
			$path_header = '';
		}

		for ( $i = $start; $i < $end; $i++ ) {
			$file = $files[ $i ];

			// Figure out if this file should not be hashed (ie; if it's too big).
			$size = filesize( $absolute_path . '/' . $file );

			$skip_hash = (
				( $skip_large_hashes && $size > $small_file_threshold && $hash_limit < $size ) ||
				$size > $large_file_threshold
			);
			if ( ! $skip_hash && $hash_limit > 0 ) {
				$hash_limit -= $size;
			}

			// Apply soft-limits on all but the first path (including bailing if we hit the hash limit)
			$entries++;
			if ( ! $first_path && ( $hash_limit < 0 || $entries > $soft_limit || time() > $soft_time_limit ) ) {
				closedir( $dh );
				$entry_buffer = array();
				flush();
				return;
			}

			$entry = get_ls_entry( $args, $absolute_path, $file, $skip_hash );

			if ( $skip_hash && ! empty( $args[ 'hashes' ] ) && empty( $entry['unchanged' ] ) ) {
				$entry['hash_skipped'] = 1;
			}

			// Keep track of paths to auto-recurse into
			// not symlinks or unreadable dirs
			$is_readable = ! ( isset( $entry['unreadable'] ) && $entry['unreadable'] );
			if ( ( isset( $entry['is_dir'] ) && $entry['is_dir'] ) && $is_readable && ( isset( $entry['is_link'] ) && ! $entry['is_link'] ) && count( $paths ) < 1000 && $entries < $soft_limit ) {
				// Do not track into .donotbackup folders.
				if ( empty( $entry['do_not_backup'] ) ) {
					$explore_path = empty( $relative_path ) ? $file : $relative_path . '/' . $file;
					if ( ! in_array( $explore_path, $paths ) ) {
						array_push( $paths, $explore_path );
					}
				}
			}

			// Buffer all but the first path, to help enforce soft-limits
			if ( $first_path ) {
				send_json_with_check( $entry, true, false );
			} else{
				array_push( $entry_buffer, $entry );
			}
		}

		// If buffering, output entry buffer; finished a directory before hitting a soft-limit.
		if ( ! $first_path ) {
			echo $path_header;
			foreach ( $entry_buffer as $entry ) {
				send_json_with_check( $entry, true, false );
			}
		}

		// End of directory or just end of batch?
		$end_of_batch = false;
		if ( $end === count( $files ) ) {
			send_json_with_check( [ 'eod' => true ], true, false );
		} else {
			$end_of_batch = true;
			send_json_with_check( [ 'next' => $end ], true, false );
		}

		// Send a footer at the bottom of each directory to confirm it is complete.
		flush();

		$first_path = false;
		$entry_buffer = array();

		if ( $entries > $soft_limit || $end_of_batch ) {
			exit;
		}
	}
}

function action_cleanup_restore( $args ) {
	$files   = glob( localize_path( '{$ABSPATH}/vp-sql-upload-*.sql' ) );
	$deleted = 0;

	foreach ( $files as $file ) {
		if ( @unlink( $file ) ) {
			$deleted++;
		}
	}

	success_header();
	echo json_encode( array(
		'found'   => count( $files ),
		'deleted' => $deleted,
	) );
}

function action_cleanup_helpers( $args ) {
	$args = array_merge( array(
		'ageThreshold' => 21600, // 6 hours
	), $args );

	$dir = opendir( __DIR__ );
	if ( ! is_resource( $dir ) ) {
		fatal_error( READ_ERROR, 'Failed to open directory: ' . __DIR__ );
	}

	$self = realpath( __FILE__ );

	// Find leftover old helpers and delete them.
	$helpers_deleted = 0;
	$helpers_found = 0;
	while ( false !== ( $entry = readdir( $dir ) ) ) {
		// Skip files that don't look like helpers.
		if ( 0 != strncmp( $entry, 'jp-helper-', 10 ) ) {
			continue;
		}

		$helpers_found++;
		$full_path = realpath( implode( '/', array( __DIR__, $entry ) ) );

		// Skip entries that aren't files, or are myself.
		if ( $full_path == $self || ! is_file( $full_path ) ) {
			continue;
		}

		// Only delete helpers over the threshold
		$age = time() - filemtime( $full_path );
		if ( $age < $args['ageThreshold'] ) {
			continue;
		}

		// Check file header
		if ( file_get_contents( $full_path, false, NULL, 0, 40 ) !== '<?php /* Jetpack Backup Helper Script */' ) {
			continue;
		}

		// Finally delete.
		$helpers_deleted++;
		unlink( $full_path );
	}

	success_header();
	echo json_encode( array(
		'found'   => $helpers_found,
		'deleted' => $helpers_deleted,
	 ) );
}

function action_get_active_theme( $args ) {
	if ( ! empty( $args['load_full_wp'] ) ) {
		load_wp( true );
	} else {
		load_wp();
	}

	$theme = wp_get_theme();

	if ( $theme ) {
		success_header();
		echo json_encode( array(
			'slug' => $theme->get_template(),
			'path' => $theme->get_theme_root(),
		) );
	} else {
		fatal_error( READ_ERROR, 'wp_get_theme() failed' );
	}
}

function action_validate_theme() {
	// Forces a theme switch if necessary (we may have deleted active theme).
	load_wp( true );
	$theme_validated = validate_current_theme();

	success_header();
	send_json_with_check( array(
		'theme_validated' => $theme_validated,
	) );
	exit;
}

function action_woocommerce_install() {
	load_wp( true );

	global $wpdb;

	success_header();

	if ( class_exists( 'WC_Install' ) && method_exists( 'WC_Install', 'install' ) ) {
		$sql = sprintf( '
			ALTER TABLE `vp_backup_%swc_download_log`
			DROP FOREIGN KEY `fk_%swc_download_log_permission_id`',
			$wpdb->prefix,
			$wpdb->prefix
		);

		$wpdb->query( $sql );

		WC_Install::install();

		echo json_encode( array( 'installed' => true, 'sql' => $sql ) );
	} else {
		echo json_encode( array( 'installed' => false, 'message' => 'woocommerce not available' ) );
	}
}

function action_get_file( $args ) {
	$args = array_merge( array(
		'path'              => null,
		'previous_attempts' => [],
	), $args );

	if ( empty( $args['path'] ) ) {
		fatal_error( COMMS_ERROR, 'Invalid args', 400 );
	}

	$path = localize_path( base64_decode( $args['path'] ) );

	if ( ! file_exists( $path ) ) {
		fatal_error( NOT_FOUND_ERROR, 'File not found: ' . $args['path'] );
	}

	$handle = fopen( $path, 'r' );
	if ( ! $handle ) {
		fatal_error( READ_ERROR, 'Unable to open file' );
	}

	// Fast forward past previous attempts, checking their hashes match.
	foreach ( $args['previous_attempts'] as $attempt ) {
		fast_forward_handle( $handle, intval( $attempt->size ), $attempt->hash );
	}

	$filesize = filesize( $path );

	success_header();
	header( 'Content-Length: ' . ( $filesize - ftell( $handle ) + 1 ) );
	header( 'x-file-size: ' . $filesize );

	// Send an extra prepended byte to avoid WAFs/reverse proxies from treating the HTTP body as a truthy value.
	echo 'b';

	$result = pass_through( $handle );
	if ( false === $result ) {
		fatal_error( READ_ERROR, 'File read error' );
	}

	exit;
}

function action_enable_jetpack_sso( $args ) {
	load_wp( true );

	if ( ! \Jetpack::is_module_active( 'sso' ) ) {
		\Jetpack::activate_module( 'sso', false, false );
		if ( ! \Jetpack::is_module_active( 'sso' ) ) {
			fatal_error( WRITE_ERROR, 'Failed to activate Jetpack SSO module.' );
		}
	}

	if ( intval( get_option( 'jetpack_sso_match_by_email' ) ) !== 1 ) {
		if ( ! update_option( 'jetpack_sso_match_by_email', 1 ) ) {
			fatal_error( WRITE_ERROR, 'Failed to set Jetpack SSO to match by email.' );
		}
	}

	success_header();
	echo json_encode( [
		'ok' => true,
	] );
}

function action_transfer_jetpack_connection( $args ) {
	global $wpdb;

	// Ensure required args have been specified
	if ( empty( $args['imported_prefix'] ) || empty( $args['master_user_id'] ) ) {
		fatal_error( COMMS_ERROR, 'Imported prefix and new master_user_id required to transfer Jetpack connection' );
	}
	$imported_prefix = $args['imported_prefix'];
	$new_master_user_id = intval( $args['master_user_id'] );

	if ( ! empty( $args['load_full_wp'] ) ) {
		load_wp( true );
	} else {
		load_wp();
	}

	// Verify specified master_user_id is present and an administrator.
	$get_roles_query = $wpdb->prepare( "select meta_value from `{$imported_prefix}usermeta` where user_id = %d and meta_key like %s limit 1;", $new_master_user_id, '%capabilities' );
	$new_master_roles_serialized = $wpdb->get_var( $get_roles_query );
	if ( empty( $new_master_roles_serialized ) ) {
		fatal_error( NOT_FOUND_ERROR, 'Unable to determine roles for new master user' );
	}

	$new_master_roles = maybe_unserialize( $new_master_roles_serialized );
	if ( ! is_array( $new_master_roles ) || ! in_array( 'administrator', array_keys( $new_master_roles ) ) ) {
		fatal_error( COMMS_ERROR, 'Specified master_user_id does not have the administrator role' );
	}

	// Gather current Jetpack settings together for import.
	$jetpack_options = get_option( 'jetpack_options' );
	$jetpack_private_options = get_option( 'jetpack_private_options' );
	if ( ! is_array( $jetpack_options ) || ! is_array( $jetpack_private_options ) || empty( $jetpack_options['master_user'] ) ) {
		fatal_error( NOT_FOUND_ERROR, 'Jetpack Options not found for connection transfer' );
	}
	$old_master_user_id = intval( $jetpack_options['master_user'] );

	// Modify Jetpack Options to reflect new master_user_id.
	if ( $old_master_user_id !== $new_master_user_id ) {
		// Update master user in Jetpack Options and user tokens, if they have changed.
		$jetpack_options['master_user'] = $new_master_user_id;

		// Replace the master user id in the tokens array.
		if ( empty( $jetpack_private_options['user_tokens'][ $old_master_user_id ] ) ) {
			fatal_error( NOT_FOUND_ERROR, 'Master User tokens not found in Jetpack Private Options' );
		}

		$old_token = $jetpack_private_options['user_tokens'][ $old_master_user_id ];
		$new_token = replace_user_id_in_user_token( $old_token, $new_master_user_id );
		$jetpack_private_options['user_tokens'] = [ $new_master_user_id => $new_token ];
	}

	// Write options to the newly imported tables.
	$wpdb->update( $imported_prefix . 'options', [ 'option_value' => serialize( $jetpack_options ) ], [ 'option_name' => 'jetpack_options' ] );
	$wpdb->update( $imported_prefix . 'options', [ 'option_value' => serialize( $jetpack_private_options ) ], [ 'option_name' => 'jetpack_private_options' ] );

	success_header();
	echo json_encode( [
		'old_master_user_id' => $old_master_user_id,
		'new_master_user_id' => $new_master_user_id,
	] );
}

function fast_forward_handle( $handle, $size, $md5 ) {
	$ctx = hash_init( 'md5' );
	$read = 0;

	while ( $read < $size ) {
		$data = fread( $handle, min( 2048, $size - $read ) );
		if ( false == $data ) {
			fatal_error( READ_ERROR, 'Unable to read to fast forward point' );
		}

		$read += strlen( $data );
		hash_update( $ctx, $data );
	}

	$hash = hash_final( $ctx );
	if ( $hash !== $md5 ) {
		fatal_error( READ_ERROR, 'Hash mis-match while fast-forwarding file handle' );
	}
}

function pass_through( $handle ) {
	while ( ! feof( $handle ) ) {
		$data = fread( $handle, 2048 );
		if ( false === $data ) {
			return false;
		}

		print $data;
	}

	return true;
}

function replace_user_id_in_user_token( $user_token, $new_user_id ) {
	$user_token_without_suffix = strip_user_id_from_user_token( $user_token );
	return add_user_id_to_user_token( $user_token_without_suffix, $new_user_id  );
}

function strip_user_id_from_user_token( $user_token ) {
	$pos_of_last_period = strrpos( $user_token, '.' );
	return substr( $user_token, 0, $pos_of_last_period );
}

function add_user_id_to_user_token( $user_token_without_suffix, $user_id ) {
	return $user_token_without_suffix . '.' . $user_id;
}

function get_plugin_path_from_slug( $slug ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugins = get_plugins();

	if ( strstr( $slug, '/' ) ) {
		// The slug is already a plugin path.
		return $slug;
	}

	foreach ( $plugins as $plugin_path => $data ) {
		$path_parts = explode( '/', $plugin_path );
		if ( $path_parts[0] === $slug ) {
			return $plugin_path;
		}
	}

	return false;
}


/**
 * Wrappers for various PHP string functions that ensure that PHP will treat strings as bytes and not as characters.
 *
 * PHP might have "mbstring.func_overload" enabled, in which case string functions will treat strings as arrays of
 * (potentially multibyte) characters and not bytes, e.g.
 *
 *     strlen('😀') === 1
 *
 * The wrapper functions in the class ensure that parameter strings will always be treated as bytes:
 *
 *     DPC_Bytes::strlen('😀') === 4
 *
 * The class also normalizes some differences between PHP 5.x-7.x and 8.x.
 *
 * Given that static method names are similar to the ones used by PHP, they're prefixed with "b_" to not erroneously
 * trigger various security scanners.
 */
class DPC_Bytes {

	/**
	 * If true, "mbstring.func_overload" is enabled. If null, "mbstring.func_overload" hasn't been checked yet.
	 *
	 * @var boolean|null
	 */
	static $cached_mbstring_func_overload_enabled = null;

	/**
	 * Check if "mbstring.func_overload" is enabled.
	 *
	 * @return bool True if "mbstring.func_overload" is enabled.
	 */
	private static function mbstring_func_overload_is_enabled() {
		if ( is_null( static::$cached_mbstring_func_overload_enabled ) ) {
			static::$cached_mbstring_func_overload_enabled = false;

			if ( function_exists( 'mb_substr' ) ) {
				if ( ( (int) ini_get( 'mbstring.func_overload' ) ) > 0 ) {
					static::$cached_mbstring_func_overload_enabled = true;
				}
			}
		}

		return static::$cached_mbstring_func_overload_enabled;
	}

	/**
	 * Get string length (in bytes).
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.strlen
	 * @see https://www.php.net/manual/en/function.strlen.php
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.mb-strlen
	 * @see https://www.php.net/manual/en/function.mb-strlen.php
	 *
	 * @param string $string String being measured for length.
	 *
	 * @return int Length of the string.
	 * @throws Exception On invalid parameters.
	 */
	public static function b_strlen( $string ) {

		// Passing nulls here is deprecated in recent versions of PHP, and a null here is 99% an error.
		if ( is_null( $string ) ) {
			throw new Exception( 'Input string is null' );
		}

		if ( static::mbstring_func_overload_is_enabled() ) {
			$size = mb_strlen( $string, '8bit' );
		} else {
			$size = strlen( $string );
		}

		return $size;
	}

	/**
	 * Return part of a string (in bytes).
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.substr
	 * @see https://www.php.net/manual/en/function.substr.php
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.mb-substr
	 * @see https://www.php.net/manual/en/function.mb-substr.php
	 *
	 * @param string   $string Input string.
	 * @param int      $offset Offset.
	 * @param int|null $length Length.
	 *
	 * @return string Extracted part of string.
	 * @throws Exception On invalid parameters.
	 */
	public static function b_substr( $string, $offset, $length = null ) {

		// Passing nulls here is deprecated in recent versions of PHP, and a null here is 99% an error.
		if ( is_null( $string ) ) {
			throw new Exception( 'Input string is null' );
		}
		if ( is_null( $offset ) ) {
			throw new Exception( 'Input offset is null' );
		}

		// Behavior of substr() differs between PHP 5.x-7.x and PHP 8.x:
		//
		// * PHP 5.x-7.x: "If length is given and is 0, FALSE or NULL, an empty string will be returned";
		// * PHP 8.x: "If length is omitted or null, the substring starting from offset until the end of the string
		//   will be returned".
		//
		// Therefore, always calculate and provide length to have the unified, PHP 8.x-like behavior.
		if ( is_null( $length ) ) {
			$length = static::b_strlen( $string ) - $offset;
		}

		if ( static::mbstring_func_overload_is_enabled() ) {
			$substring = mb_substr( $string, $offset, $length, '8bit' );
		} else {
			$substring = substr( $string, $offset, $length );
		}

		return $substring;
	}

	/**
	 * Find the numeric position of the first occurrence of needle in the haystack string (in bytes).
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.strpos
	 * @see https://www.php.net/manual/en/function.strpos.php
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.mb-strpos
	 * @see https://www.php.net/manual/en/function.mb-strpos.php
	 *
	 * @param string $haystack String to search in.
	 * @param string $needle   String to search for.
	 * @param int    $offset   If specified, search will start this number of characters counted from the beginning of
	 *                         the string.
	 *
	 * @return int|false Position of where the needle exists, or false if the needle was not found.
	 * @throws Exception On invalid parameters.
	 */
	public static function b_strpos( $haystack, $needle, $offset = 0 ) {

		// Discourage deprecated pre-PHP 7.3 behavior.
		if ( ! is_string( $needle ) ) {
			throw new Exception( 'Needle is not a string: ' . print_r( $needle, true ) );
		}

		if ( static::mbstring_func_overload_is_enabled() ) {
			$position = mb_strpos( $haystack, $needle, $offset, '8bit' );
		} else {
			$position = strpos( $haystack, $needle, $offset );
		}

		return $position;
	}
}


/**
 * Encode an array / object to JSON, add some human-readable indenting.
 *
 * @param mixed $value Value that is being encoded.
 *
 * @return string|false JSON representation of the value, or false if the encoding has failed.
 */
function dpc_json_encode_pretty( $value ) {
	$json = json_encode( $value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	if ( false === $json ) {
		return false;
	}

	return $json . PHP_EOL;
}


/**
 * Signature ("magic bytes").
 *
 * Those will be the first bytes of every chunk that is being sent.
 */
const DPC_DATA_FORMAT_SIGNATURE = 'DPC';

/**
 * Version of data format.
 *
 * If you make changes to the data format, increase the version number on both reader and writer.
 *
 * Can't be bigger than 65535.
 */
const DPC_DATA_FORMAT_VERSION = 1;

/**
 * MIME type that the chunks will be sent as.
 *
 * Docs say that since PHP 5.3.4 (I think), "post_max_size = 0 will not disable the limit when the content type is
 * application/x-www-form-urlencoded or is not registered with PHP", so the MIME type has to be something that PHP is
 * aware of (i.e. can't be something fancy like "application/x-a8c-jpb-whatever").
 *
 * @see https://www.php.net/manual/en/ini.core.php#ini.post-max-size
 */
const DPC_CHUNK_MIME_TYPE = 'application/octet-stream';

/**
 * A "special" permissions value that means that permissions are not to be set.
 *
 * The max. permissions value will be 0777 (octal) / 511 (decimal), so this value has to be bigger than that but still
 * fit into the unsigned short.
 *
 * All pack()s signed types are of a machine byte order, so we can't switch to a signed type and use something like -1
 * for the value of this constant.
 */
const DPC_PERMISSIONS_UNSET = 65000;

/**
 * Maximum size (in bytes) that the Metadata could be.
 *
 * Should be able to accommodate directory that is being written to.
 */
const DPC_METADATA_JSON_MAX_SIZE = 128 * 1024;

/**
 * Maximum size (in bytes) that the State could be.
 *
 * Should be able to accommodate MAX_PATH_SIZE, MAX_SYMLINK_PATH_SIZE, and other partial data that might be
 * contained in the state.
 */
const DPC_STATE_JSON_MAX_SIZE = 128 * 1024;


/**
 * Integer entry type representations.
 */
class DPC_Supported_Entry_Types {
	const FILE      = 1;
	const DIRECTORY = 2;
	const SYMLINK   = 3;
}


/**
 * Interface that configures how various elements of the stream get encoded.
 */
interface DPC_Pack_Type {

	/**
	 * Return format argument to pack() / unpack() that will be used to encode / decode a value.
	 *
	 * @see https://www.php.net/manual/en/function.pack.php
	 *
	 * @return string format argument to pack() / unpack() to be used to encode / decode a value.
	 */
	public static function pack_argument();

	/**
	 * Return size of the encoded (pack()'ed) value in bytes.
	 *
	 * It's Bytes::strlen( pack( static::pack_argument(), ... ) ).
	 *
	 * @see https://www.php.net/manual/en/function.pack.php
	 *
	 * @return int Size of the encoded (pack()'ed) value in bytes.
	 */
	public static function byte_size();

}


/**
 * unsigned char
 */
class DPC_Unsigned_Char_Pack_Type implements DPC_Pack_Type {

	public static function pack_argument() {
		return 'C';
	}

	public static function byte_size() {
		return 1;
	}
}

/**
 * unsigned short (always 16 bit, little endian byte order)
 */
class DPC_Unsigned_Short_LE_Pack_Type implements DPC_Pack_Type {

	public static function pack_argument() {
		return 'v';
	}

	public static function byte_size() {
		return 2;
	}
}

/**
 * unsigned long (always 32 bit, little endian byte order)
 */
class DPC_Unsigned_Long_LE_Pack_Type implements DPC_Pack_Type {

	public static function pack_argument() {
		return 'V';
	}

	public static function byte_size() {
		return 4;
	}
}

/**
 * unsigned long long (always 64 bit, little endian byte order)
 */
class DPC_Unsigned_Long_Long_LE_Pack_Type implements DPC_Pack_Type {

	public static function pack_argument() {
		return 'P';
	}

	static function byte_size() {
		return 8;
	}
}


/**
 * Encoding configuration for Version.
 */
class DPC_Data_Format_Version_Pack_Type extends DPC_Unsigned_Short_LE_Pack_Type {
}

/**
 * Encoding configuration for Metadata JSON size.
 */
class DPC_Metadata_JSON_Size_Pack_Type extends DPC_Unsigned_Long_LE_Pack_Type {
}

/**
 * Encoding configuration for State JSON size.
 */
class DPC_State_JSON_Size_Pack_Type extends DPC_Unsigned_Long_LE_Pack_Type {
}

/**
 * Encoding configuration for Entry Type.
 */
class DPC_Entry_Type_Pack_Type extends DPC_Unsigned_Char_Pack_Type {
}

/**
 * Encoding configuration for Entry Path size.
 */
class DPC_Entry_Path_Size_Pack_Type extends DPC_Unsigned_Long_LE_Pack_Type {
}

/**
 * Encoding configuration for Entry Permissions.
 */
class DPC_Entry_Permissions_Pack_Type extends DPC_Unsigned_Short_LE_Pack_Type {
}

/**
 * Encoding configuration for Total file size when the Entry is a File.
 */
class DPC_Entry_File_Total_Size_Pack_Type extends DPC_Unsigned_Long_Long_LE_Pack_Type {
}

/**
 * Encoding configuration for Total file written so far size when the Entry is a File.
 */
class DPC_Entry_File_Total_Written_So_Far_Size_Pack_Type extends DPC_Unsigned_Long_Long_LE_Pack_Type {
}

/**
 * Encoding configuration for File part size when the Entry is a File.
 */
class DPC_Entry_File_Part_Size_Pack_Type extends DPC_Unsigned_Long_Long_LE_Pack_Type {
}

/**
 * Encoding configuration for Symlink pack size when the Entry is a Symlink.
 */
class DPC_Entry_Symlink_Path_Size_Pack_Type extends DPC_Unsigned_Long_LE_Pack_Type {
}



/**
 * Wrappers for functions which throw an exception on errors and warnings instead of silently continuing operation.
 *
 * PHP is pretty lax with error reporting when doing I/O operations, e.g. a typical I/O helper function returns false,
 * null, and/or emits a warning, but the whole PHP application continues operation. It's for the caller of the I/O
 * helper function to test the returned result of the function, and notice + act upon I/O errors.
 *
 * That approach isn't ideal for the DPC parsing needs, as we really want to know about each and every I/O error, and
 * either stop parsing the incoming DPC stream altogether, or try-catch the error and recover from it gracefully.
 *
 * Therefore, this class provides wrappers for some common (mostly) I/O operations that throw an exception on errors
 * instead of just returning false, null, or something else. The thrown exception then either gets caught by the
 * exception handler (try-catch block), and the error then gets sent back to the Sender as an HTTP 4xx/5xx response.
 * This wrapper class treats warnings as errors too.
 *
 * Some parameters between PHP 5.x and PHP 7.x/8.x functions are incompatible in some weird way, so not all parameters
 * are added to every wrapper function. Keep that in mind if you're editing this class.
 *
 * Given that static method names are similar to the ones used by PHP, they're prefixed with "t_" to not erroneously
 * trigger various security scanners.
 */
class DPC_Throw_On_Errors {

	/**
	 * Try to determine and return a path to a stream.
	 *
	 * Useful for debugging purposes.
	 *
	 * @param resource $stream Stream to determine the path for.
	 *
	 * @return string Path to the stream, or 'unknown' if the path couldn't be determined.
	 */
	private static function stream_file_path( $stream ) {
		$stream_file_path = 'unknown';

		if ( is_resource( $stream ) ) {
			$stream_file_path = stream_get_meta_data( $stream )['uri'];
		}

		return $stream_file_path;
	}

	/**
	 * Execute a callable, throw an exception on PHP warnings.
	 *
	 * @param callable $callable Callable to execute.
	 *
	 * @return mixed Callable's return value, if any.
	 * @throws Exception On warnings thrown by the callable.
	 * @noinspection PhpUnusedParameterInspection
	 */
	private static function throw_on_warnings( $callable ) {
		$old_error_reporting = error_reporting( - 1 );
		$old_display_errors  = ini_set( 'display_errors', 'stderr' );
		set_error_handler(
		/**
		 * Temporary error handler.
		 *
		 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.set-error-handler
		 * @see https://www.php.net/manual/en/function.set-error-handler.php
		 *
		 * @param int         $errno      Level of the error raised.
		 * @param string      $errstr     Error message.
		 * @param string|null $errfile    Filename that the error was raised in.
		 * @param int|null    $errline    Line number where the error was raised.
		 * @param array|null  $errcontext Deprecated, unused.
		 *
		 * @return mixed
		 * @throws Exception
		 */
			function ( $errno, $errstr, $errfile = null, $errline = null, $errcontext = null ) {
				throw new Exception( "$errstr (file: $errfile; line: $errline)" );
			}
		);

		$result        = null;
		$error_message = null;
		if ( PHP_MAJOR_VERSION >= 7 ) {
			// On PHP 7.x, all Exception and Error are subclasses of Throwable.
			try {
				$result = $callable();
			} catch ( Throwable $throwable ) {
				$error_message = $throwable->getMessage();
			}
		} else {
			// On PHP 5.x, there's only Exception.
			try {
				$result = $callable();
			} catch ( Exception $exception ) {
				$error_message = $exception->getMessage();
			}
		}

		restore_error_handler();
		ini_set( 'display_errors', $old_display_errors );
		error_reporting( $old_error_reporting );

		if ( ! is_null( $error_message ) ) {
			throw new Exception( $error_message );
		}

		return $result;
	}

	/**
	 * Create a temporary file, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.tmpfile
	 * @see https://www.php.net/manual/en/function.tmpfile.php
	 *
	 * @return resource File handle to the temporary file.
	 * @throws Exception When tmpfile() has thrown warnings or has failed.
	 */
	public static function t_tmpfile() {
		$temp_file_handle = static::throw_on_warnings( function () {
			return tmpfile();
		} );

		if ( false === $temp_file_handle ) {
			throw new Exception( 'Unable to tmpfile()' );
		}

		return $temp_file_handle;
	}

	/**
	 * Decode data encoded with MIME base64, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.base64-decode
	 * @see https://www.php.net/manual/en/function.base64-decode.php
	 *
	 * @param string $string Encoded data.
	 *
	 * @return string Decoded data.
	 * @throws Exception If base64_decode() failed, has thrown warnings, or on invalid parameters.
	 */
	public static function t_base64_decode( $string ) {

		// PHP 5.x will happily "decode" null input without complaining, so let's check it here.
		if ( is_null( $string ) ) {
			throw new Exception( 'Base64-encoded string is null' );
		}

		$base64_encoded_data = static::throw_on_warnings( function () use ( $string ) {
			return base64_decode( $string );
		} );

		if ( false === $base64_encoded_data ) {
			// Typically it's the input data that we're trying to decode.
			throw new Exception(
				'Unable to base64_decode( ' . DPC_Bytes::b_strlen( $string ) . ' bytes )'
			);
		}

		return $base64_encoded_data;
	}

	/**
	 * Check whether a file or directory exists, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.file-exists
	 * @see https://www.php.net/manual/en/function.file-exists.php
	 *
	 * @param string $filename Path to the file or directory.
	 *
	 * @return bool True if the file or directory specified by filename exists; false otherwise.
	 * @throws Exception On invalid parameters, or if file_exists() has thrown warnings.
	 */
	public static function t_file_exists( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for file_exists() is unset' );
		}

		return static::throw_on_warnings( function () use ( $filename ) {
			return file_exists( $filename );
		} );
	}

	/**
	 * Determine whether the filename is a symbolic link, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.is-link
	 * @see https://www.php.net/manual/en/function.is-link.php
	 *
	 * @param string $filename Path to the file.
	 *
	 * @return bool Returns true if the filename exists and is a symbolic link, false otherwise.
	 * @throws Exception On invalid parameters, or if is_link() has thrown warnings.
	 */
	public static function t_is_link( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for is_link() is unset' );
		}

		return static::throw_on_warnings( function () use ( $filename ) {
			return is_link( $filename );
		} );
	}

	/**
	 * Get file size, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.filesize
	 * @see https://www.php.net/manual/en/function.filesize.php
	 *
	 * @param string $filename Path to the file.
	 *
	 * @return int Size of the file in bytes
	 * @throws Exception On invalid parameters, or if filesize() has thrown warnings.
	 */
	public static function t_filesize( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for filesize() is unset' );
		}

		return static::throw_on_warnings( function () use ( $filename ) {
			return filesize( $filename );
		} );
	}

	/**
	 * Get file permissions, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.fileperms
	 * @see https://www.php.net/manual/en/function.fileperms.php
	 *
	 * @param string $filename Path to the file.
	 *
	 * @return int File's permissions as a numeric mode.
	 * @throws Exception On invalid parameters, or if fileperms() has thrown warnings, or has failed.
	 */
	public static function t_fileperms( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for fileperms() is unset' );
		}

		$fileperms_result = static::throw_on_warnings( function () use ( $filename ) {
			return fileperms( $filename );
		} );

		if ( false === $fileperms_result ) {
			throw new Exception( "Unable to fileperms( '$filename' )" );
		}

		return $fileperms_result;
	}

	/**
	 * Gets file owner, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.fileowner
	 * @see https://www.php.net/manual/en/function.fileowner.php
	 *
	 * @param string $filename Path to the file.
	 *
	 * @return int User ID of the owner of the file.
	 * @throws Exception On invalid parameters, if fileowner() has thrown warnings, or has failed.
	 */
	public static function t_fileowner( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for fileowner() is unset' );
		}

		$owner = static::throw_on_warnings( function () use ( $filename ) {
			return fileowner( $filename );
		} );

		if ( false === $owner ) {
			throw new Exception( "Unable to fileowner( '$filename' )" );
		}

		return $owner;
	}

	/**
	 * Get file group, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.filegroup
	 * @see https://www.php.net/manual/en/function.filegroup.php
	 *
	 * @param string $filename Path to the file.
	 *
	 * @return int Group ID of the owner of the file.
	 * @throws Exception On invalid parameters, if filegroup() has thrown warnings, or has failed.
	 */
	public static function t_filegroup( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for filegroup() is unset' );
		}

		$group = static::throw_on_warnings( function () use ( $filename ) {
			return filegroup( $filename );
		} );

		if ( false === $group ) {
			throw new Exception( "Unable to filegroup( '$filename' )" );
		}

		return $group;
	}

	/**
	 * Test for end-of-file on a file pointer, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.feof
	 * @see https://www.php.net/manual/en/function.feof.php
	 *
	 * @param resource $stream Stream resource.
	 *
	 * @return bool True if the file pointer is at EOF, otherwise false.
	 * @throws Exception If feof() has thrown warnings.
	 */
	public static function t_feof( $stream ) {
		return static::throw_on_warnings( function () use ( $stream ) {
			return feof( $stream );
		} );
	}

	/**
	 * Open file or URL, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.fopen
	 * @see https://www.php.net/manual/en/function.fopen.php
	 *
	 * @param string $filename Path to the file / stream.
	 * @param string $mode     Type of access you require to the stream.
	 *
	 * @return resource File pointer resource.
	 * @throws Exception If fopen() has thrown warnings, or has failed.
	 */
	public static function t_fopen( $filename, $mode ) {
		$handler = static::throw_on_warnings( function () use ( $filename, $mode ) {
			return fopen( $filename, $mode );
		} );

		if ( false === $handler ) {
			throw new Exception( "Unable to fopen( '$filename', '$mode' )" );
		}

		return $handler;
	}

	/**
	 * Binary-safe file read, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.fread
	 * @see https://www.php.net/manual/en/function.fread.php
	 *
	 * @param resource $stream File system pointer resource.
	 * @param int      $length Number of bytes to read.
	 *
	 * @return string String that was read.
	 * @throws Exception If fread() has thrown warnings, or has failed.
	 */
	public static function t_fread( $stream, $length ) {
		$data = static::throw_on_warnings( function () use ( $stream, $length ) {
			return fread( $stream, $length );
		} );

		if ( false === $data ) {
			throw new Exception(
				"Unable to fread( resource for '" . static::stream_file_path( $stream ) . "', $length )"
			);
		}

		return $data;
	}

	/**
	 * Binary-safe file write, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.fwrite
	 * @see https://www.php.net/manual/en/function.fwrite.php
	 *
	 * @param resource $stream File system pointer resource.
	 * @param string   $data   String that is to be written.
	 *
	 * @return int Number of bytes written.
	 * @throws Exception On invalid parameters, if fwrite() has thrown warnings, or has failed.
	 */
	public static function t_fwrite( $stream, $data ) {
		// PHP 5.x won't complain about data being null, so let's do it ourselves.
		if ( is_null( $data ) ) {
			throw new Exception( 'Data for fwrite() is null' );
		}

		$bytes_written = static::throw_on_warnings( function () use ( $stream, $data ) {
			return fwrite( $stream, $data );
		} );

		if ( false === $bytes_written ) {
			throw new Exception(
				"Unable to fwrite( resource for '" . static::stream_file_path( $stream ) . "', " .
				DPC_Bytes::b_strlen( $data ) . " bytes )"
			);
		}

		return $bytes_written;
	}

	/**
	 * Seek on a file pointer, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.fseek
	 * @see https://www.php.net/manual/en/function.fseek.php
	 *
	 * @param resource $stream File system pointer resource.
	 * @param int      $offset Offset.
	 *
	 * @return void
	 * @throws Exception On invalid parameters, if fseek() has thrown warnings, or has failed.
	 */
	public static function t_fseek( $stream, $offset ) {
		// PHP 5.x won't complain about offset being null, so let's do it ourselves.
		if ( is_null( $offset ) ) {
			throw new Exception( 'Offset for fseek() is null' );
		}

		$fseek_result = static::throw_on_warnings( function () use ( $stream, $offset ) {
			return fseek( $stream, $offset );
		} );

		if ( 0 !== $fseek_result ) {
			throw new Exception(
				"Unable to fseek( resource for '" . static::stream_file_path( $stream ) . "', $offset )"
			);
		}
	}

	/**
	 * Close an open file pointer, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.fclose
	 * @see https://www.php.net/manual/en/function.fclose.php
	 *
	 * @param resource $stream File system pointer resource.
	 *
	 * @return void
	 * @throws Exception If fclose() has thrown warnings, or has failed.
	 */
	public static function t_fclose( $stream ) {
		$fclose_result = static::throw_on_warnings( function () use ( $stream ) {
			return fclose( $stream );
		} );

		if ( true !== $fclose_result ) {
			throw new Exception(
				"Unable to fclose( resource for '" . static::stream_file_path( $stream ) . "' )"
			);
		}
	}

	/**
	 * Create an empty file if it doesn't exist, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.touch
	 * @see https://www.php.net/manual/en/function.touch.php
	 *
	 * @param string $filename Name of the file that is to be created.
	 *
	 * @return void
	 * @throws Exception On invalid parameters, if touch() has thrown warnings, or has failed.
	 */
	public static function t_touch( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for touch() is unset' );
		}

		$touch_result = static::throw_on_warnings( function () use ( $filename ) {
			return touch( $filename );
		} );

		if ( false === $touch_result ) {
			throw new Exception( "Unable to touch( '$filename' )" );
		}
	}

	/**
	 * Change file mode, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.chmod
	 * @see https://www.php.net/manual/en/function.chmod.php
	 *
	 * @param string $filename    Path to the file.
	 * @param int    $permissions Permissions.
	 *
	 * @return void
	 * @throws Exception On invalid parameters, if chmod() has thrown warnings, or has failed.
	 */
	public static function t_chmod( $filename, $permissions ) {
		// PHP 5.x won't complain about permissions being null, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for unpack() is unset' );
		}
		if ( is_null( $permissions ) ) {
			throw new Exception( 'Permissions for chmod() are null' );
		}

		$chmod_result = static::throw_on_warnings( function () use ( $filename, $permissions ) {
			return chmod( $filename, $permissions );
		} );

		if ( false === $chmod_result ) {
			throw new Exception( "Unable to chmod( '$filename', 0" . decoct( $permissions ) . ' )' );
		}
	}

	/**
	 * Tell whether the filename is a regular file (follow symlinks), throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.is-file
	 * @see https://www.php.net/manual/en/function.is-file.php
	 *
	 * @param string $filename Path to the file.
	 *
	 * @return bool True if the filename (or the symlink's target) exists and is a regular file, false otherwise.
	 *
	 * @throws Exception On invalid parameters, if is_file() has thrown warnings, or has failed.
	 */
	public static function t_is_file( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for is_file() is unset' );
		}

		return static::throw_on_warnings( function () use ( $filename ) {
			return is_file( $filename );
		} );
	}

	/**
	 * Tell whether the filename is a directory (follow symlinks), throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.is-dir
	 * @see https://www.php.net/manual/en/function.is-dir.php
	 *
	 * @param string $filename Path to the file.
	 *
	 * @return bool True if the filename (or the symlink's target) exists and is a directory, false otherwise.
	 * @throws Exception On invalid parameters, if is_dir() has thrown warnings, or has failed.
	 */
	public static function t_is_dir( $filename ) {
		// PHP 5.x won't complain about parameter being unset, so let's do it ourselves.
		if ( ! $filename ) {
			throw new Exception( 'Filename for is_dir() is unset' );
		}

		return static::throw_on_warnings( function () use ( $filename ) {
			return is_dir( $filename );
		} );
	}

	/**
	 * Make a directory, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.mkdir
	 * @see https://www.php.net/manual/en/function.mkdir.php
	 *
	 * @param string $directory   Directory path.
	 * @param int    $permissions Permissions of the newly created directory.
	 * @param bool   $recursive   If true, then any parent directories to the directory specified will also be created,
	 *                            with the same permissions.
	 *
	 * @return void
	 * @throws Exception On invalid parameters, if mkdir() has thrown warnings, or has failed.
	 */
	public static function t_mkdir( $directory, $permissions = 0777, $recursive = false ) {
		// PHP 5.x won't complain about permissions being null, so let's do it ourselves.
		if ( is_null( $permissions ) ) {
			throw new Exception( 'Permissions for mkdir() are unset' );
		}

		$mkdir_result = static::throw_on_warnings( function () use ( $directory, $permissions, $recursive ) {
			return mkdir( $directory, $permissions, $recursive );
		} );

		if ( false === $mkdir_result ) {
			throw new Exception( "Unable to mkdir( '$directory', 0" . decoct( $permissions ) . ", $recursive )" );
		}
	}

	/**
	 * Create a symbolic link, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.symlink
	 * @see https://www.php.net/manual/en/function.symlink.php
	 *
	 * @param string $target Target of the link ("target").
	 * @param string $link   Link name ("source").
	 *
	 * @return void
	 * @throws Exception If symlink() has thrown warnings, or has failed.
	 */
	public static function t_symlink( $target, $link ) {
		$symlink_result = static::throw_on_warnings( function () use ( $target, $link ) {
			return symlink( $target, $link );
		} );

		if ( false === $symlink_result ) {
			throw new Exception( "Unable to symlink( '$target', '$link' )" );
		}
	}

	/**
	 * Delete a file, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.unlink
	 * @see https://www.php.net/manual/en/function.unlink.php
	 *
	 * @param string $filename Path to the file.
	 *
	 * @return void
	 * @throws Exception If unlink() has thrown warnings, or has failed.
	 */
	public static function t_unlink( $filename ) {
		$unlink_result = static::throw_on_warnings( function () use ( $filename ) {
			return unlink( $filename );
		} );

		if ( false === $unlink_result ) {
			throw new Exception( "Unable to unlink( '$filename' )" );
		}
	}

	/**
	 * Return the target of a symbolic link, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.readlink
	 * @see https://www.php.net/manual/en/function.readlink.php
	 *
	 * @param string $path Symbolic link path.
	 *
	 * @return string Contents of the symbolic link path (symlink "target").
	 * @throws Exception If readlink() has thrown warnings, or has failed.
	 */
	public static function t_readlink( $path ) {
		$readlink_result = static::throw_on_warnings( function () use ( $path ) {
			return readlink( $path );
		} );

		if ( false === $readlink_result ) {
			throw new Exception( "Unable to readlink( '$path' )" );
		}

		return $readlink_result;
	}

	/**
	 * Decode a non-"null" JSON string, throw on warnings / errors.
	 *
	 * Will throw an error on recoding a string "null" (which is valid JSON).
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.json-decode
	 * @see https://www.php.net/manual/en/function.json-decode.php
	 *
	 * @param string $json        JSON string being decoded.
	 * @param bool   $associative When true, JSON objects will be returned as associative arrays; when false, JSON
	 *                            objects will be returned as objects.
	 *
	 * @return mixed Returns the value encoded in JSON in appropriate PHP type (never null though).
	 * @throws Exception On JSON decoding failures, or if json_decode() has thrown warnings.
	 */
	public static function t_json_decode( $json, $associative = false ) {
		// PHP 5.x won't complain about JSON being null, so let's do it ourselves.
		if ( is_null( $json ) ) {
			throw new Exception( 'JSON for json_decode() is unset' );
		}

		$decoded_json = static::throw_on_warnings( function () use ( $json, $associative ) {
			return json_decode( $json, $associative );
		} );

		// JSON_THROW_ON_ERROR was introduced in PHP 7.3, and we need to be compatible with 5.6, so we'll assume that
		// all "null" values are errors.
		if ( null === $decoded_json ) {

			// Invalid JSON might be very long, and/or it might contain binary data, so get just the first X bytes.
			$json_sample_max_length = 1024;
			$json_sample            = DPC_Bytes::b_substr( $json, 0, $json_sample_max_length );
			$json_sample_length     = DPC_Bytes::b_strlen( $json_sample );
			$json_sample_base64     = base64_encode( $json_sample );

			throw new Exception(
				"Unable to json_decode( " . DPC_Bytes::b_strlen( $json ) . " bytes; " .
				"first $json_sample_length bytes: base64_decode( '$json_sample_base64' ) )"
			);
		}

		return $decoded_json;
	}

	/**
	 * Unpack data from binary string, throw on warnings / errors.
	 *
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.unpack
	 * @see https://www.php.net/manual/en/function.unpack.php
	 *
	 * @see https://www.php.net/manual/en/function.pack.php#refsect1-function.pack-parameters
	 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.pack
	 *
	 * @param string $format Format code.
	 * @param string $string Packed data.
	 *
	 * @return array Associative array containing unpacked elements of binary string.
	 * @throws Exception On unpack() has failed, or if unpack() has thrown warnings.
	 */
	public static function t_unpack( $format, $string ) {
		// PHP 5.x won't complain about parameters being unset, so let's do it ourselves.
		if ( ! $format ) {
			throw new Exception( 'Format for unpack() is unset' );
		}
		if ( ! $string ) {
			throw new Exception( 'String for unpack() is unset' );
		}

		$unpack_result = static::throw_on_warnings( function () use ( $format, $string ) {
			return unpack( $format, $string );
		} );

		// PHP 7.x returns false on errors, 5.x doesn't (at least it doesn't promise to return false).
		if ( false === $unpack_result ) {
			throw new Exception(
				"Unable to unpack( '$format', base64_decode( '" . base64_encode( $string ) . "' ) )"
			);
		}

		return $unpack_result;
	}
}



/**
 * Run unpack(), expect and return only a single value that was unpacked.
 *
 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.unpack
 * @see https://www.php.net/manual/en/function.unpack.php
 *
 * @see https://www.php.net/manual/en/function.pack.php#refsect1-function.pack-parameters
 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.pack
 *
 * @param string $format Format code.
 * @param string $string Packed data.
 * @param string $label  Human-readable label for the data that is being unpacked, to be used in an exception.
 *
 * @return mixed Unpacked value (typically an integer).
 * @throws Exception If unpack() has failed, or if more than one value has been unpacked, or if unpack() has thrown
 *                   warnings.
 */
function dpc_unpack_single_value( $format, $string, $label ) {
	$unpacked = DPC_Throw_On_Errors::t_unpack( $format, $string );
	if ( 1 !== count( $unpacked ) || ( ! array_key_exists( 1, $unpacked ) ) ) {
		// String will contain binary data, so we don't want it to end up in the exception.
		$base64_encoded_string = base64_encode( $string );
		throw new Exception(
			"unpack( '$format', base64_decode( '$base64_encoded_string' ) " .
			"returned more than one value when decoding '$label'"
		);
	}

	return $unpacked[1];
}



/**
 * Test whether a filesystem object exists, and if so, is it a symlink, a file, or a directory.
 *
 * Needed because:
 *
 * * We need to know specifically whether a filesystem object is a file/directory/symlink, and not a, say, block
 *   device, or a socket;
 * * file_exists() / is_file() / is_dir() resolve symlinks and give out information about the symlink's target, which is
 *   error-prone, i.e. we want to find out whether a filesystem object itself is a symlink, and not the type of the
 *   filesystem object that the symlink points to;
 * * file_exists() follows symlinks and returns false for broken symlinks, i.e. the target of which doesn't exist.
 */
class DPC_Filesystem_Object {

	/**
	 * True if the filesystem object exists; it might be a file, directory, symlink, block object, socket, etc.
	 *
	 * @var bool
	 */
	public $exists = true;

	/**
	 * True if the filesystem object is a symlink (broken or not).
	 *
	 * @var bool
	 */
	public $is_link = false;

	/**
	 * True if the filesystem object is a directory.
	 *
	 * @var bool
	 */
	public $is_dir = false;

	/**
	 * True if the filesystem object is a file.
	 *
	 * @var bool
	 */
	public $is_file = false;

	/**
	 * Collect filesystem object state.
	 *
	 * @param string $path Path to a filesystem object.
	 *
	 * @throws Exception On I/O errors.
	 */
	function __construct( $path ) {

		// We could have made some filesystem changes recently, so make sure that we're not reading any cached data.
		clearstatcache();

		if ( DPC_Throw_On_Errors::t_is_link( $path ) ) {
			$this->is_link = true;
		} elseif ( DPC_Throw_On_Errors::t_file_exists( $path ) ) {
			if ( DPC_Throw_On_Errors::t_is_dir( $path ) ) {
				$this->is_dir = true;
			} elseif ( DPC_Throw_On_Errors::t_is_file( $path ) ) {
				$this->is_file = true;
			}
		} else {
			$this->exists = false;
		}
	}
}



/**
 * Exception that includes an HTTP status code to report to the Sender.
 */
class DPC_HTTP_Status_Code_Exception extends Exception {

	/**
	 * HTTP status code, typically 4xx or 5xx.
	 *
	 * @var int
	 */
	public $http_status_code;

	/**
	 * @param string $message          Exception message.
	 * @param int    $http_status_code HTTP status code.
	 */
	public function __construct( $message, $http_status_code ) {
		parent::__construct( $message );
		$this->http_status_code = $http_status_code;
	}

	/**
	 * Return a human-readable string for an exception.
	 *
	 * Useful for debugging.
	 *
	 * @return string Human-readable string for an exception.
	 */
	public function __toString() {
		return __CLASS__ . ': [' . $this->http_status_code . ']: ' . $this->message . PHP_EOL;
	}
}

/**
 * Exception that was caused my incorrect input data being passed to the Receiver (and thus the Sender is "to blame").
 */
class DPC_Invalid_Input_Exception extends DPC_HTTP_Status_Code_Exception {

	/**
	 * @param string $message Exception message.
	 */
	public function __construct( $message ) {
		parent::__construct( $message, 400 );
	}
}

/**
 * Exception that was caused by the Receiver not being able to process valid Chunk data (and thus the Receiver is
 * "to blame").
 */
class DPC_Internal_Error_Exception extends DPC_HTTP_Status_Code_Exception {

	/**
	 * @param string $message Exception message.
	 */
	public function __construct( $message ) {
		parent::__construct( $message, 500 );
	}
}



/**
 * Metadata that came together with the Chunk.
 */
class DPC_Input_Metadata {

	/**
	 * Output path, i.e. directory to write to; must be absolute and end with a "/".
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Constructor.
	 *
	 * @param string $path Output path, i.e. directory to write to; must be absolute and end with a "/".
	 *
	 * @throws DPC_Invalid_Input_Exception|Exception On invalid arguments.
	 */
	public function __construct( $path ) {
		if ( 0 === DPC_Bytes::b_strlen( $path ) ) {
			throw new DPC_Invalid_Input_Exception( 'Output path is unset.' );
		}
		if ( '/' !== DPC_Bytes::b_substr( $path, 0, 1 ) ) {
			throw new DPC_Invalid_Input_Exception( "Output path '$path' must be absolute." );
		}
		if ( '/' !== DPC_Bytes::b_substr( $path, - 1 ) ) {
			throw new DPC_Invalid_Input_Exception( "Output path '$path' must end with a '/'." );
		}

		// FIXME not sure if this is the most secure way to do it.
		if ( false !== DPC_Bytes::b_strpos( $path, '../' ) ) {
			throw new DPC_Invalid_Input_Exception( "Output path '$path' can't have relative parts" );
		}

		$this->path = $path;
	}

	/**
	 * Create Metadata object from decoded JSON array coming in from Chunk.
	 *
	 * @param array $array Decoded JSON array coming in from Chunk.
	 *
	 * @return DPC_Input_Metadata Metadata object.
	 * @throws DPC_Invalid_Input_Exception|Exception On missing required keys, unsupported extra keys in parameter
	 *                                               array, or invalid arguments.
	 */
	public static function from_array( $array ) {

		$path_key      = 'path';
		$required_keys = array( $path_key );
		$actual_keys   = array_keys( $array );

		// Complain if not all required keys have been found.
		$missing_required_keys = array_diff( $required_keys, $actual_keys );
		if ( ! empty( $missing_required_keys ) ) {
			throw new DPC_Invalid_Input_Exception(
				'Some required keys were not found in Metadata array: ' .
				implode( ', ', $missing_required_keys ) . '; ' .
				'Metadata array: ' . print_r( $array, true )
			);
		}

		// If some extra keys have been added, but they're not supported by this class, it's better to fail early and
		// let the caller know.
		$supported_keys         = $required_keys;
		$extra_unsupported_keys = array_diff( $actual_keys, $supported_keys );
		if ( ! empty( $extra_unsupported_keys ) ) {
			throw new DPC_Invalid_Input_Exception(
				'Extra unsupported keys were found in Metadata array: ' .
				implode( ', ', $extra_unsupported_keys ) . '; ' .
				'Metadata array: ' . print_r( $array, true )
			);
		}

		return new static( $array[ $path_key ] );
	}

}



/**
 * Object that gets passed around between the states, and holds data about the Entry that has been read so far.
 */
class DPC_Entry_Data_Read_So_Far {

	/**
	 * Entry Type, or null if Type hasn't been fully read yet.
	 *
	 * @see DPC_Supported_Entry_Types
	 * @see DPC_Entry_Type_Read_State
	 *
	 * @var int|null
	 */
	public $entry_type = null;

	/**
	 * Entry Permissions, or null if Permissions haven't been fully read yet.
	 *
	 * @see DPC_Entry_Permissions_Read_State
	 *
	 * @var int|null
	 */
	public $permissions = null;

	/**
	 * Entry Path size, or null if Path size hasn't been fully read yet.
	 *
	 * @see DPC_Entry_Path_Size_Read_State
	 *
	 * @var int|null
	 */
	public $path_size = null;

	/**
	 * Entry Path, or null if Path hasn't been fully read yet.
	 *
	 * @see DPC_Entry_Path_Read_State
	 *
	 * @var string|null
	 */
	public $path = null;

	/**
	 * Entry File total size, or null if File total size hasn't been fully read yet / not present in this particular
	 * Type of Entry.
	 *
	 * @see DPC_Entry_File_Total_Size_Read_State
	 *
	 * @var int|null
	 */
	public $file_total_size = null;

	/**
	 * Entry File total written so far size, or null if File total written so far size hasn't been fully read yet / not
	 * present in this particular Type of Entry.
	 *
	 * @see DPC_Entry_File_Total_Written_So_Far_Size_Read_State
	 *
	 * @var int|null
	 */
	public $file_total_written_so_far_size = null;

	/**
	 * Entry File part size, or null if File part size hasn't been fully read yet / not present in this particular Type
	 * of Entry.
	 *
	 * @see DPC_Entry_File_Part_Size_Read_State
	 *
	 * @var int|null
	 */
	public $file_part_size = null;

	/**
	 * Entry Symlink path size, or null if Symlink path size hasn't been fully read / not present in this particular
	 * Type of Entry.
	 *
	 * @see DPC_Entry_Symlink_Path_Size_Read_State
	 *
	 * @var string|null
	 */
	public $symlink_path_size = null;

	/**
	 * Serialize this object to an array that could be further serialized into JSON.
	 *
	 * @return array<string, int|string|null> Object's array representation.
	 *
	 * @see DPC_Entry_Data_Read_So_Far::from_array()
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

	/**
	 * Unserialize object from an array that was itself unserialized from JSON.
	 *
	 * @param array $array Object's array representation.
	 *
	 * @return DPC_Entry_Data_Read_So_Far Unserialized object.
	 *
	 * @see DPC_Entry_Data_Read_So_Far::to_array()
	 */
	public static function from_array( $array ) {
		$result = new DPC_Entry_Data_Read_So_Far();
		foreach ( $array as $key => $value ) {
			$result->$key = $value;
		}

		return $result;
	}
}



/**
 * Abstract read state that concrete read states should implement.
 */
abstract class DPC_Read_State {

	/**
	 * Data about the Entry that has been read so far, passed from the previous state and amended by the current state.
	 *
	 * @var DPC_Entry_Data_Read_So_Far
	 */
	protected $entry_data_read_so_far;

	/**
	 * Bytes of state's data that have been read so far.
	 *
	 * States that read more than one byte may use this as a buffer.
	 *
	 * @var string
	 */
	protected $bytes_read_so_far;

	/**
	 * Constructor.
	 *
	 * @param DPC_Entry_Data_Read_So_Far $entry_data_read_so_far Data about the Entry that has been read so far.
	 * @param string                     $bytes_read_so_far      Bytes of state's data that have been read so far.
	 *
	 * @throws DPC_Internal_Error_Exception On invalid parameters.
	 */
	public function __construct( $entry_data_read_so_far, $bytes_read_so_far = '' ) {

		if ( ! $entry_data_read_so_far ) {
			throw new DPC_Internal_Error_Exception( 'Object for entry data read so far must be set' );
		}

		$this->entry_data_read_so_far = $entry_data_read_so_far;
		$this->bytes_read_so_far      = $bytes_read_so_far;
	}

	/**
	 * Read the state's data, store/process it, and return next state, or null if the state ran out of data.
	 *
	 * Each concrete state:
	 *
	 * 1. Attempts to read its data, e.g. an Entry Type, from the Chunk handle;
	 * 2. If the data has been read fully:
	 *     1. Processes the data if needed, e.g. creates a directory/a symlink, writes data to a file, etc.;
	 *     2. Returns an instance of next state to run;
	 * 3. If the state has run out of data while reading it:
	 *     1. Stores the data that has been read so far;
	 *     2. Returns null to let the caller know that the state's object needs to be serialized and sent back to the
	 *     Sender.
	 *
	 * @param resource           $chunk_handle Resource, e.g. an opened file, to read the state's data from.
	 * @param DPC_Input_Metadata $metadata     Metadata object.
	 *
	 * @return DPC_Read_State|null An instance of the next state to run if the current state's data has been
	 *                             read/processed fully, *or* null if the current state ran out of data and needs to be
	 *                             serialized + resumed later.
	 * @throws DPC_Invalid_Input_Exception On errors caused by invalid Chunk data where the Sender is "to blame".
	 * @throws DPC_Internal_Error_Exception On errors caused by a processing error where the Receiver is "to blame".
	 * @throws Exception|\Exception On other errors.
	 */
	abstract public function process_and_return_next_state( $chunk_handle, $metadata );

	/**
	 * Return unique state name that this state should be serialized as.
	 *
	 * Will later be used to find the right class for the serialized state.
	 *
	 * @return string Unique state name to be later used to find the right clas for the serialized state.
	 * @throws Exception If concrete class doesn't implement this method.
	 */
	public static function state_name() {

		// PHP 5.6's strict mode doesn't like abstract static methods:
		//
		//     https://stackoverflow.com/questions/41611058/why-does-php-allow-abstract-static-functions
		//
		// so implement a stub.

		throw new Exception( 'Subclasses should implement ' . __FUNCTION__ );
	}

	/**
	 * Serialize this state to an array that could be further serialized into JSON.
	 *
	 * @return array State's array representation.
	 *
	 * @see DPC_Read_State::from_saved_state()
	 */
	public function to_saved_state() {
		return array(
			'entry_data_read_so_far' => $this->entry_data_read_so_far->to_array(),
			'bytes_read_so_far'      => base64_encode( $this->bytes_read_so_far ),
		);
	}

	/**
	 * Unserialize state from an array that was itself unserialized from JSON.
	 *
	 * @param array $saved_state State's array representation.
	 *
	 * @return DPC_Read_State Unserialized state.
	 * @throws DPC_Internal_Error_Exception|Exception On errors while unserializing the state.
	 *
	 * @see DPC_Read_State::to_saved_state()
	 */
	public static function from_saved_state( $saved_state ) {
		return new static(
			DPC_Entry_Data_Read_So_Far::from_array( $saved_state['entry_data_read_so_far'] ),
			DPC_Throw_On_Errors::t_base64_decode( $saved_state['bytes_read_so_far'] )
		);
	}
}



/**
 * State that reads the Entry Type.
 *
 * If the Receiver is at this state, we're at the beginning of a new Entry, i.e. the next 1 byte (if not EOF) would be
 * the Entry Type.
 */
class DPC_Entry_Type_Read_State extends DPC_Read_State {

	/**
	 * Map of valid Entry Type integer values.
	 *
	 * @see DPC_Supported_Entry_Types
	 *
	 * @var array<int, boolean>|null Map with integer Entry Types as keys, true as values; or null if the map hasn't
	 *      been initialized yet.
	 */
	private static $entry_type_int_values;

	/**
	 * Validate integer Entry Type.
	 *
	 * @param int $entry_type Integer Entry Type.
	 *
	 * @return bool True if the Entry Type is valid, false otherwise.
	 *
	 * @see DPC_Supported_Entry_Types
	 *
	 */
	private static function entry_type_is_valid( $entry_type ) {

		// Build an entry type existence map if it doesn't exist yet.
		if ( is_null( static::$entry_type_int_values ) ) {

			$class = new ReflectionClass( DPC_Supported_Entry_Types::class );

			static::$entry_type_int_values = array();
			foreach ( $class->getConstants() as $constant_value ) {
				static::$entry_type_int_values[ $constant_value ] = true;
			}
		}

		return array_key_exists( $entry_type, static::$entry_type_int_values );
	}

	/**
	 * Constructor.
	 *
	 * @throws DPC_Internal_Error_Exception On invalid parameters.
	 */
	public function __construct() {
		parent::__construct( new DPC_Entry_Data_Read_So_Far() );
	}

	public static function state_name() {
		return 'type';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$entry_type_encoded = DPC_Throw_On_Errors::t_fread( $chunk_handle, DPC_Entry_Type_Pack_Type::byte_size() );

		if ( DPC_Bytes::b_strlen( $entry_type_encoded ) < DPC_Entry_Type_Pack_Type::byte_size() ) {

			// Entry type not read in full because we ran out of data in a Chunk.
			return null;
		}

		$this->entry_data_read_so_far->entry_type = dpc_unpack_single_value(
			DPC_Entry_Type_Pack_Type::pack_argument(),
			$entry_type_encoded,
			'entry type'
		);

		if ( ! static::entry_type_is_valid( $this->entry_data_read_so_far->entry_type ) ) {
			throw new DPC_Invalid_Input_Exception(
				"Entry type {$this->entry_data_read_so_far->entry_type} is not valid."
			);
		}

		return new DPC_Entry_Permissions_Read_State( $this->entry_data_read_so_far );
	}
}



/**
 * State that reads the Entry Permissions.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the Permissions, and some bytes
 * that make up Permissions are still left to read.
 */
class DPC_Entry_Permissions_Read_State extends DPC_Read_State {

	/**
	 * Maximum permissions value.
	 *
	 * We don't support setuid bits and such, because PHP typically runs as an unprivileged user, so we wouldn't be
	 * able to set those.
	 */
	const MAX_PERMISSIONS_VALUE = 0777;

	public static function state_name() {
		return 'permissions';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$this->bytes_read_so_far .= DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			DPC_Entry_Permissions_Pack_Type::byte_size() - DPC_Bytes::b_strlen( $this->bytes_read_so_far )
		);

		if ( DPC_Bytes::b_strlen( $this->bytes_read_so_far ) < DPC_Entry_Permissions_Pack_Type::byte_size() ) {
			// Permissions not read in full because we ran out of data in a chunk.
			return null;
		}

		// Permissions read in full at this point.

		$this->entry_data_read_so_far->permissions = dpc_unpack_single_value(
			DPC_Entry_Permissions_Pack_Type::pack_argument(),
			$this->bytes_read_so_far,
			'permissions'
		);

		if ( $this->entry_data_read_so_far->permissions !== DPC_PERMISSIONS_UNSET ) {

			if ( $this->entry_data_read_so_far->permissions < 0 ||
			     $this->entry_data_read_so_far->permissions > static::MAX_PERMISSIONS_VALUE ) {
				throw new DPC_Invalid_Input_Exception(
					'Invalid permissions: 0' . decoct( $this->entry_data_read_so_far->permissions )
				);
			}

			// Symlinks can't have permissions, so if they do, it must be an error on the Sender's side.
			if ( DPC_Supported_Entry_Types::SYMLINK === $this->entry_data_read_so_far->entry_type ) {
				throw new DPC_Invalid_Input_Exception(
					'Entry "' . $this->entry_data_read_so_far->path . '" is a symlink, ' .
					'but has permissions set to 0' . decoct( $this->entry_data_read_so_far->permissions )
				);
			}
		}

		return new DPC_Entry_Path_Size_Read_State( $this->entry_data_read_so_far );
	}
}



/**
 * State that reads the Entry Path size.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the Path size, and some bytes
 * that make up the Path size are still left to read.
 */
class DPC_Entry_Path_Size_Read_State extends DPC_Read_State {

	/**
	 * Maximum size of a path.
	 *
	 * PHP_MAXPATHLEN is sometimes unreliable, e.g. it's been observed that on systems where this value is 1024, PHP is
	 * able to use paths that are 1013 bytes in length. So, set the limit to 1000 bytes here rather arbitrarily.
	 */
	const MAX_PATH_SIZE = 1000;

	public static function state_name() {
		return 'path-size';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$this->bytes_read_so_far .= DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			DPC_Entry_Path_Size_Pack_Type::byte_size() - DPC_Bytes::b_strlen( $this->bytes_read_so_far )
		);

		if ( DPC_Bytes::b_strlen( $this->bytes_read_so_far ) < DPC_Entry_Path_Size_Pack_Type::byte_size() ) {
			// Path size not read in full because we ran out of data in a chunk.
			return null;
		}

		// Path size read in full at this point.

		$this->entry_data_read_so_far->path_size = dpc_unpack_single_value(
			DPC_Entry_Path_Size_Pack_Type::pack_argument(),
			$this->bytes_read_so_far,
			'path size'
		);

		if ( $this->entry_data_read_so_far->path_size < 1 ||
		     $this->entry_data_read_so_far->path_size > static::MAX_PATH_SIZE ) {
			throw new DPC_Invalid_Input_Exception( "Invalid path size: {$this->entry_data_read_so_far->path_size}" );
		}

		return new DPC_Entry_Path_Read_State( $this->entry_data_read_so_far );
	}
}



/**
 * State that reads the Entry Path.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the Path, and some bytes that
 * make up the Path are still left to read.
 */
class DPC_Entry_Path_Read_State extends DPC_Read_State {

	/**
	 * Default permissions for directories that will get created when the permissions of the directory are not set.
	 *
	 * PHP's own default permissions of directories created with mkdir() are 0777 which seems to be a bit too liberal.
	 */
	const DIR_DEFAULT_PERMISSIONS = 0755;

	public static function state_name() {
		return 'path';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$this->bytes_read_so_far .= DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			$this->entry_data_read_so_far->path_size - DPC_Bytes::b_strlen( $this->bytes_read_so_far )
		);

		if ( DPC_Bytes::b_strlen( $this->bytes_read_so_far ) < $this->entry_data_read_so_far->path_size ) {
			// Path not read in full because we ran out of data in a chunk.
			return null;
		}

		// Path read in full at this point.

		$this->entry_data_read_so_far->path = $this->bytes_read_so_far;

		// FIXME not sure if this is the most secure way to do it.
		if ( false !== DPC_Bytes::b_strpos( $this->entry_data_read_so_far->path, '../' ) ) {
			throw new DPC_Invalid_Input_Exception(
				"Path '{$this->entry_data_read_so_far->path}' can't have relative parts"
			);
		}

		switch ( $this->entry_data_read_so_far->entry_type ) {

			case DPC_Supported_Entry_Types::DIRECTORY:

				// Nothing else left to read about the directory at this point, so just create it.

				$full_directory_path = $metadata->path . $this->entry_data_read_so_far->path;

				$fs_object = new DPC_Filesystem_Object( $full_directory_path );

				if ( $fs_object->exists ) {
					if ( $fs_object->is_dir ) {
						if ( $this->entry_data_read_so_far->permissions !== DPC_PERMISSIONS_UNSET ) {
							// The directory might have been created by the file part reader, or it might have existed
							// before we started writing anything anywhere, so we just need it to have the right
							// permissions.
							DPC_Throw_On_Errors::t_chmod(
								$full_directory_path,
								$this->entry_data_read_so_far->permissions
							);
						}
					} else {
						throw new DPC_Invalid_Input_Exception(
							"Path '$full_directory_path' exists already but is not a directory"
						);
					}
				} else {
					$permissions = $this->entry_data_read_so_far->permissions;
					if ( $permissions === DPC_PERMISSIONS_UNSET ) {
						$permissions = static::DIR_DEFAULT_PERMISSIONS;
					}

					DPC_Throw_On_Errors::t_mkdir( $full_directory_path, $permissions, true );
				}

				// Next bytes (if there's still data to read in the chunk) will make up a new entry.
				$next_state = new DPC_Entry_Type_Read_State();

				break;

			case DPC_Supported_Entry_Types::FILE:

				$next_state = new DPC_Entry_File_Total_Size_Read_State( $this->entry_data_read_so_far );

				break;

			case DPC_Supported_Entry_Types::SYMLINK:

				$next_state = new DPC_Entry_Symlink_Path_Size_Read_State( $this->entry_data_read_so_far );

				break;

			default:
				throw new DPC_Invalid_Input_Exception(
					"Unsupported entry type: {$this->entry_data_read_so_far->entry_type}"
				);
		}

		return $next_state;
	}
}



/**
 * State that reads the Entry File's total size.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the File's total size, and some
 * bytes that make up the File's total size are still left to read.
 */
class DPC_Entry_File_Total_Size_Read_State extends DPC_Read_State {

	/**
	 * Maximum total file size.
	 *
	 * We can technically support files up to 2^64 bytes in size, but we don't expect to encounter those, and we need a
	 * sanity check to make sure that we've read the right bytes for the total file size.
	 */
	const MAX_FILE_TOTAL_SIZE = 1024 * 1024 * 1024 * 1024;

	public static function state_name() {
		return 'file-total-size';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$this->bytes_read_so_far .= DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			( DPC_Entry_File_Total_Size_Pack_Type::byte_size() - DPC_Bytes::b_strlen( $this->bytes_read_so_far ) )
		);

		if ( DPC_Bytes::b_strlen( $this->bytes_read_so_far ) < DPC_Entry_File_Total_Size_Pack_Type::byte_size() ) {
			// File's total size not read in full because we ran out of data in a chunk.
			return null;
		}

		// File's total size read in full at this point.

		$this->entry_data_read_so_far->file_total_size = dpc_unpack_single_value(
			DPC_Entry_File_Total_Size_Pack_Type::pack_argument(),
			$this->bytes_read_so_far,
			'file total size'
		);

		if ( $this->entry_data_read_so_far->file_total_size < 0 /* File can be empty */ ||
		     $this->entry_data_read_so_far->file_total_size > static::MAX_FILE_TOTAL_SIZE ) {
			throw new DPC_Invalid_Input_Exception(
				"Invalid file total size: {$this->entry_data_read_so_far->file_total_size}"
			);
		}

		return new DPC_Entry_File_Total_Written_So_Far_Size_Read_State( $this->entry_data_read_so_far );
	}
}



/**
 * State that reads the Entry File's written so far size.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the File's total written so far
 * size, and some bytes that make up the File's total written so far size are still left to read.
 */
class DPC_Entry_File_Total_Written_So_Far_Size_Read_State extends DPC_Read_State {

	/**
	 * Maximum total written so far file size.
	 *
	 * We can technically support files up to 2^64 bytes in size, but we don't expect to encounter those, and we need a
	 * sanity check to make sure that we've read the right bytes for the file's total written so far size.
	 */
	const MAX_FILE_TOTAL_WRITTEN_SO_FAR_SIZE = 1024 * 1024 * 1024 * 1024;

	public static function state_name() {
		return 'file-total-written-so-far-size';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$this->bytes_read_so_far .= DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			(
				DPC_Entry_File_Total_Written_So_Far_Size_Pack_Type::byte_size() -
				DPC_Bytes::b_strlen( $this->bytes_read_so_far )
			)
		);

		if ( DPC_Bytes::b_strlen( $this->bytes_read_so_far ) <
		     DPC_Entry_File_Total_Written_So_Far_Size_Pack_Type::byte_size() ) {
			// File's total written so far size not read in full because we ran out of data in a chunk.
			return null;
		}

		// File's total written so far size read in full at this point.

		$this->entry_data_read_so_far->file_total_written_so_far_size = dpc_unpack_single_value(
			DPC_Entry_File_Total_Written_So_Far_Size_Pack_Type::pack_argument(),
			$this->bytes_read_so_far,
			'file total written so far size'
		);

		if (
			$this->entry_data_read_so_far->file_total_written_so_far_size < 0 /* File can be empty */ || (
				$this->entry_data_read_so_far->file_total_written_so_far_size >
				static::MAX_FILE_TOTAL_WRITTEN_SO_FAR_SIZE
			)
		) {
			throw new DPC_Invalid_Input_Exception(
				'Invalid file total written so far size: ' .
				$this->entry_data_read_so_far->file_total_written_so_far_size
			);
		}

		if ( $this->entry_data_read_so_far->file_total_written_so_far_size >
		     $this->entry_data_read_so_far->file_total_size ) {
			// Most likely bogus input.
			throw new DPC_Invalid_Input_Exception(
				'So far we have written more data ' .
				"({$this->entry_data_read_so_far->file_total_written_so_far_size} bytes) " .
				'than the total file size of the file ' .
				"({$this->entry_data_read_so_far->file_total_size} bytes)"
			);
		}

		return new DPC_Entry_File_Part_Size_Read_State( $this->entry_data_read_so_far );
	}
}



/**
 * State that reads the Entry File part size.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the File part size, and some
 * bytes that make up the File part's size are still left to read.
 */
class DPC_Entry_File_Part_Size_Read_State extends DPC_Read_State {

	/**
	 * Maximum file part size.
	 *
	 * We can technically support files up to 2^64 bytes in size, but we don't expect to encounter those, and we need a
	 * sanity check to make sure that we've read the right bytes for the file part's size.
	 */
	const MAX_FILE_PART_SIZE = 1024 * 1024 * 1024 * 1024;

	public static function state_name() {
		return 'file-part-size';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$this->bytes_read_so_far .= DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			( DPC_Entry_File_Part_Size_Pack_Type::byte_size() - DPC_Bytes::b_strlen( $this->bytes_read_so_far ) )
		);

		if ( DPC_Bytes::b_strlen( $this->bytes_read_so_far ) < DPC_Entry_File_Part_Size_Pack_Type::byte_size() ) {
			// File part's size not read in full because we ran out of data in a chunk.
			return null;
		}

		// File part's size read in full at this point.

		$this->entry_data_read_so_far->file_part_size = dpc_unpack_single_value(
			DPC_Entry_File_Part_Size_Pack_Type::pack_argument(),
			$this->bytes_read_so_far,
			'file part size'
		);

		if (
			$this->entry_data_read_so_far->file_part_size < 0 /* File can be empty */ ||
			$this->entry_data_read_so_far->file_part_size > static::MAX_FILE_PART_SIZE
		) {
			throw new DPC_Invalid_Input_Exception(
				'Invalid file part size: ' . $this->entry_data_read_so_far->file_part_size
			);
		}

		if ( $this->entry_data_read_so_far->file_part_size > $this->entry_data_read_so_far->file_total_size ) {
			// Most likely bogus input.
			throw new DPC_Invalid_Input_Exception(
				"File part ({$this->entry_data_read_so_far->file_part_size} bytes) is bigger than " .
				"the total file size of the file ({$this->entry_data_read_so_far->file_total_size} bytes)"
			);
		}

		return new DPC_Entry_File_Part_Data_Read_State( $this->entry_data_read_so_far );
	}
}



/**
 * State that reads the Entry File part's data.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the File part's data, and some
 * bytes that make up the File part's data are still left to read.
 */
class DPC_Entry_File_Part_Data_Read_State extends DPC_Read_State {

	/**
	 * How many bytes of file part data to read at once and buffer into RAM.
	 */
	const FILE_WRITE_BUFFER_SIZE = 100 * 1024;

	/**
	 * Default permissions for directories that get created to place files in.
	 *
	 * If the directory that is to hold the file doesn't exist yet, it will get created. However, at this point we
	 * don't know the permissions that the directory should get created with, so these are the default permissions that
	 * this file-holding directory will be created with.
	 */
	const FILE_DIR_DEFAULT_PERMISSIONS = 0755;

	/**
	 * How many bytes of this specific file part's data have been written so far.
	 *
	 * Chunk might get cut off in the middle of the file part's data, so we keep track of this number, and
	 * store/restore it as part of the State.
	 *
	 * @var int
	 */
	private $file_part_written_so_far_size;

	/**
	 * Constructor.
	 *
	 * @param DPC_Entry_Data_Read_So_Far $entry_data_read_so_far        Data about the Entry that has been read so far.
	 * @param string                     $bytes_read_so_far             Bytes of state's data that have been read so
	 *                                                                  far.
	 * @param int                        $file_part_written_so_far_size How many bytes of this specific file part's
	 *                                                                  data have been written so far.
	 *
	 * @throws DPC_Internal_Error_Exception On invalid parameters.
	 */
	public function __construct(
		$entry_data_read_so_far,
		$bytes_read_so_far = '',
		$file_part_written_so_far_size = 0
	) {
		parent::__construct( $entry_data_read_so_far, $bytes_read_so_far );
		$this->file_part_written_so_far_size = $file_part_written_so_far_size;
	}

	/**
	 * Serialize this state to an array, including "file_part_written_so_far_size".
	 *
	 * @return array State's array representation.
	 *
	 * @see DPC_Entry_File_Part_Data_Read_State::from_saved_state()
	 */
	public function to_saved_state() {
		$extra_data = array(
			'file_part_written_so_far_size' => $this->file_part_written_so_far_size,
		);

		return array_merge( parent::to_saved_state(), $extra_data );
	}

	/**
	 * Unserialize state from an array, including "file_part_written_so_far_size".
	 *
	 * @param array $saved_state State's array representation.
	 *
	 * @return DPC_Read_State Unserialized state.
	 *
	 * @throws DPC_Internal_Error_Exception|Exception On errors while unserializing the state.
	 *
	 * @see DPC_Entry_File_Part_Data_Read_State::to_saved_state()
	 */
	public static function from_saved_state( $saved_state ) {
		$immediate_object = parent::from_saved_state( $saved_state );

		return new static(
			$immediate_object->entry_data_read_so_far,
			$immediate_object->bytes_read_so_far,
			$saved_state['file_part_written_so_far_size']
		);
	}

	public static function state_name() {
		return 'file-part-data';
	}

	/**
	 * Create a directory leading to a file.
	 *
	 * @param string $full_file_path Full path to a file.
	 *
	 * @return void
	 * @throws DPC_Internal_Error_Exception|Exception On errors while creating a directory leading to a file.
	 */
	private static function create_directory_leading_to_file( $full_file_path ) {

		$file_directory        = dirname( $full_file_path );
		$file_directory_object = new DPC_Filesystem_Object( $file_directory );
		if ( $file_directory_object->exists ) {
			if ( ! $file_directory_object->is_dir ) {
				throw new DPC_Internal_Error_Exception(
					"Directory leading to a file '$file_directory' is not a directory"
				);
			}
		} else {
			DPC_Throw_On_Errors::t_mkdir( $file_directory, static::FILE_DIR_DEFAULT_PERMISSIONS, true );
		}
	}

	/**
	 * Create a file if it doesn't exist yet.
	 *
	 * Create the file with the right permissions first so that we don't end up potentially sensitive user data to a
	 * file with wrong permissions (even if it's for a few milliseconds).
	 *
	 * @param string $full_file_path Full path to a file.
	 *
	 * @return void
	 * @throws DPC_Internal_Error_Exception|Exception On errors while creating a file.
	 */
	private function create_file_if_it_does_not_exist( $full_file_path ) {

		$file_object = new DPC_Filesystem_Object( $full_file_path );
		if ( $file_object->exists ) {

			if ( $file_object->is_file ) {

				// The cache was just cleared by DPC_Filesystem_Object, so no need to call clearstatcache() again.

				$existing_file_size = DPC_Throw_On_Errors::t_filesize( $full_file_path );

				// The file might already exist at the target location, so complain about file size mismatch only if
				// this is not the first file part coming in; otherwise, just overwrite it.
				if ( $this->entry_data_read_so_far->file_total_written_so_far_size > 0 ) {

					if ( $existing_file_size !== $this->entry_data_read_so_far->file_total_written_so_far_size ) {
						throw new DPC_Internal_Error_Exception(
							"Existing file's '$full_file_path' size ($existing_file_size bytes) " .
							'does not match the bytes written so far ' .
							"({$this->entry_data_read_so_far->file_total_written_so_far_size} bytes)"
						);
					}

					if ( $existing_file_size === $this->entry_data_read_so_far->file_total_size ) {
						throw new DPC_Internal_Error_Exception(
							"File '$full_file_path' has been written in full ($existing_file_size bytes), " .
							'so a new file part coming in to append to it is unexpected'
						);
					}
				}

			} else {
				throw new DPC_Internal_Error_Exception( "Path '$full_file_path' already exists but is not a file" );
			}

		} else {

			if ( $this->entry_data_read_so_far->file_total_written_so_far_size > 0 ) {
				throw new DPC_Internal_Error_Exception(
					"File '$full_file_path' does not exist, but we expect to have " .
					"{$this->entry_data_read_so_far->file_total_written_so_far_size} bytes already written to it"
				);
			}

			DPC_Throw_On_Errors::t_touch( $full_file_path );
		}

		if ( $this->entry_data_read_so_far->permissions !== DPC_PERMISSIONS_UNSET ) {
			DPC_Throw_On_Errors::t_chmod( $full_file_path, $this->entry_data_read_so_far->permissions );
		}
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$full_file_path = $metadata->path . $this->entry_data_read_so_far->path;

		static::create_directory_leading_to_file( $full_file_path );

		$this->create_file_if_it_does_not_exist( $full_file_path );

		// Previous functions might have changed permissions and such, so make sure we're reading the most up-to-date
		// permissions here.
		clearstatcache();

		if ( ! is_writable( $full_file_path ) ) {

			// File's permissions from a Chunk might have disallowed to write to a file ourselves, so if that is the
			// case, temporarily set the permissions to permissive ones to be able to write File Part data to a file.
			// Later we'll set them back to what they're supposed to be.

			$current_permissions = DPC_Throw_On_Errors::t_fileperms( $full_file_path ) & 0777;

			// It's 0666, not 0600, because we're not sure if we own the file.
			DPC_Throw_On_Errors::t_chmod( $full_file_path, $current_permissions | 0666 );

			// Clear permissions cache if PHP managed to cache them up again.
			clearstatcache();
		}

		if ( 0 === $this->entry_data_read_so_far->file_total_written_so_far_size ) {
			// Overwrite existing file at the start of the first file part.
			$file_open_mode = 'wb';
		} else {
			// Append to whatever has been written so far.
			$file_open_mode = 'ab';
		}

		$file_handle = DPC_Throw_On_Errors::t_fopen( $full_file_path, $file_open_mode );

		while ( $this->file_part_written_so_far_size < $this->entry_data_read_so_far->file_part_size ) {

			$file_part_chunk_bytes_to_read = min(
				( $this->entry_data_read_so_far->file_part_size - $this->file_part_written_so_far_size ),
				static::FILE_WRITE_BUFFER_SIZE
			);

			$file_part_chunk            = DPC_Throw_On_Errors::t_fread( $chunk_handle, $file_part_chunk_bytes_to_read );
			$file_part_chunk_bytes_read = DPC_Bytes::b_strlen( $file_part_chunk );

			DPC_Throw_On_Errors::t_fwrite( $file_handle, $file_part_chunk );
			$this->file_part_written_so_far_size                          += $file_part_chunk_bytes_read;
			$this->entry_data_read_so_far->file_total_written_so_far_size += $file_part_chunk_bytes_read;

			if ( $file_part_chunk_bytes_read < $file_part_chunk_bytes_to_read ) {
				// File part data not read in full because we ran out of data in a chunk.
				break;
			}
		}

		DPC_Throw_On_Errors::t_fclose( $file_handle );

		if ( $this->entry_data_read_so_far->permissions !== DPC_PERMISSIONS_UNSET ) {
			// Either set permissions to the newly created file, or reset the temporary write permissions added
			// previously.
			DPC_Throw_On_Errors::t_chmod( $full_file_path, $this->entry_data_read_so_far->permissions );
		}

		if ( $this->file_part_written_so_far_size === $this->entry_data_read_so_far->file_part_size ) {
			// File part written in full -- next bytes (if there's still data to read in the chunk) will make up a new
			// entry.
			$next_state = new DPC_Entry_Type_Read_State();
		} else {
			// File part not written in full because we ran out of data in a chunk.
			$next_state = null;
		}

		return $next_state;
	}

}



/**
 * State that reads the Entry Symlink path size.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the Symlink path size, and some
 * bytes that make up the Symlink path's size are still left to read.
 */
class DPC_Entry_Symlink_Path_Size_Read_State extends DPC_Read_State {

	/**
	 * Maximum size of a symlink path.
	 *
	 * PHP_MAXPATHLEN is sometimes unreliable, e.g. it's been observed that on systems where this value is 1024, PHP is
	 * able to use paths that are 1013 bytes in length. So, set the limit to 1000 bytes here rather arbitrarily.
	 */
	const MAX_SYMLINK_PATH_SIZE = 1000;

	public static function state_name() {
		return 'symlink-path-size';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$this->bytes_read_so_far .= DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			DPC_Entry_Symlink_Path_Size_Pack_Type::byte_size() - DPC_Bytes::b_strlen( $this->bytes_read_so_far )
		);

		if ( DPC_Bytes::b_strlen( $this->bytes_read_so_far ) < DPC_Entry_Symlink_Path_Size_Pack_Type::byte_size() ) {
			// Symlink path size not read in full because we ran out of data in a chunk.
			return null;
		}

		// Symlink path size read in full at this point.

		$this->entry_data_read_so_far->symlink_path_size = dpc_unpack_single_value(
			DPC_Entry_Symlink_Path_Size_Pack_Type::pack_argument(),
			$this->bytes_read_so_far,
			'symlink path size'
		);

		if ( $this->entry_data_read_so_far->symlink_path_size < 1 ||
		     $this->entry_data_read_so_far->symlink_path_size > static::MAX_SYMLINK_PATH_SIZE ) {
			throw new DPC_Invalid_Input_Exception(
				"Invalid symlink path size: {$this->entry_data_read_so_far->symlink_path_size}"
			);
		}

		return new DPC_Entry_Symlink_Path_Read_State( $this->entry_data_read_so_far );
	}
}



/**
 * State that reads the Entry Symlink path.
 *
 * If the Receiver is at this state, we've read zero or more of the bytes that make up the Symlink path, and some bytes
 * that make up the Symlink path are still left to read.
 */
class DPC_Entry_Symlink_Path_Read_State extends DPC_Read_State {

	/**
	 * Default permissions for directories that get created to place symlinks in.
	 *
	 * If the directory that is to hold the symlink doesn't exist yet, it will get created. However, at this point we
	 * don't know the permissions that the directory should get created with, so these are the default permissions that
	 * this symlink-holding directory will be created with.
	 */
	const SYMLINK_DIR_DEFAULT_PERMISSIONS = 0755;

	public static function state_name() {
		return 'symlink-path';
	}

	public function process_and_return_next_state( $chunk_handle, $metadata ) {

		$this->bytes_read_so_far .= DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			$this->entry_data_read_so_far->symlink_path_size - DPC_Bytes::b_strlen( $this->bytes_read_so_far )
		);

		if ( DPC_Bytes::b_strlen( $this->bytes_read_so_far ) < $this->entry_data_read_so_far->symlink_path_size ) {
			// Symlink path not read in full because we ran out of data in a chunk.
			return null;
		}

		// Symlink path read in full at this point.

		$symlink_dst = $this->bytes_read_so_far;
		$symlink_src = $metadata->path . $this->entry_data_read_so_far->path;

		// Create a directory that leads to the symlink.
		$symlink_directory        = dirname( $symlink_src );
		$symlink_directory_object = new DPC_Filesystem_Object( $symlink_directory );
		if ( $symlink_directory_object->exists ) {
			if ( ! $symlink_directory_object->is_dir ) {
				throw new DPC_Internal_Error_Exception(
					"Directory leading to a symlink '$symlink_directory' is not a directory"
				);
			}
		} else {
			DPC_Throw_On_Errors::t_mkdir( $symlink_directory, static::SYMLINK_DIR_DEFAULT_PERMISSIONS, true );
		}

		// Create symlink itself.
		$create_symlink = true;
		$fs_object      = new DPC_Filesystem_Object( $symlink_src );
		if ( $fs_object->exists ) {

			if ( $fs_object->is_link ) {
				// Object exists, and is a symlink -- make sure it points to the right target.
				// (file_exists() would return false on existing but broken symlinks.)
				$current_symlink_target = DPC_Throw_On_Errors::t_readlink( $symlink_src );
				if ( $current_symlink_target === $symlink_dst ) {
					// No need to recreate the symlink.
					$create_symlink = false;
				} else {
					// Symlink already exists, but points to the wrong path.
					DPC_Throw_On_Errors::t_unlink( $symlink_src );
				}

			} else {
				throw new DPC_Internal_Error_Exception( "Path '$symlink_src' already exists but is not a symlink" );
			}
		}

		if ( $create_symlink ) {
			DPC_Throw_On_Errors::t_symlink( $symlink_dst, $symlink_src );
		}

		// Next bytes (if there's still data to read in the chunk) will make up a new entry.
		return new DPC_Entry_Type_Read_State();
	}
}



/**
 * Receiver, i.e. class that parses an incoming Chunk, and stores the Chunk's data in the filesystem.
 */
class DPC_Receiver {

	/**
	 * Recreate a read state from a state stored as an array (that itself was decoded from State JSON in Chunk).
	 *
	 * @param array $state_array Array representation of a state.
	 *
	 * @return DPC_Read_State|null State object, or null if the state couldn't be found/initialized.
	 * @throws DPC_Internal_Error_Exception On errors initializing a state object from a state array.
	 * @throws Exception On an incompletely implemented state, i.e. the one that has implementation stubs left.
	 *
	 * @see DPC_Receiver::state_array_from_state()
	 */
	private static function state_from_state_array( $state_array ) {
		$state_name = $state_array['name'];
		$state_args = $state_array['args'];

		$read_state_class_names = array();
		foreach ( get_declared_classes() as $class ) {
			if ( is_subclass_of( $class, DPC_Read_State::class ) ) {
				$read_state_class_names[] = $class;
			}
		}

		if ( empty( $read_state_class_names ) ) {
			throw new DPC_Internal_Error_Exception( 'Unable to find any ' . DPC_Read_State::class . ' subclasses' );
		}

		foreach ( $read_state_class_names as $read_state_class_name ) {

			try {
				$test_class = new ReflectionClass( $read_state_class_name );
			} catch ( ReflectionException $ex ) {
				throw new DPC_Internal_Error_Exception(
					"Unable to create reflection class for class '$read_state_class_name': " . $ex->getMessage()
				);
			}

			if ( ! $test_class->isAbstract() ) {
				if ( method_exists( $read_state_class_name, 'state_name' ) ) {
					if ( method_exists( $read_state_class_name, 'from_saved_state' ) ) {
						if ( $state_name === $read_state_class_name::state_name() ) {
							return $read_state_class_name::from_saved_state( $state_args );
						}
					}
				}
			}
		}

		return null;
	}

	/**
	 * Generate an array representation of state from a state object (which will later be encoded into JSON).
	 *
	 * @param DPC_Read_State $state State object.
	 *
	 * @return array Array representation of a state.
	 * @throws Exception On an incompletely implemented state, i.e. the one that has implementation stubs left.
	 *
	 * @see DPC_Receiver::state_from_state_array()
	 */
	private static function state_array_from_state( $state ) {
		return array(
			'name' => $state::state_name(),
			'args' => $state->to_saved_state(),
		);
	}

	/**
	 * Read and validate Signature bytes.
	 *
	 * @param resource $chunk_handle Resource, e.g. an opened file, to read the Signature from.
	 *
	 * @return void
	 *
	 * @throws DPC_Invalid_Input_Exception If Signature is invalid, e.g. when the input doesn't look like a Chunk.
	 * @throws Exception On read errors.
	 */
	private static function read_and_validate_signature( $chunk_handle ) {
		$data_format_signature = DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			DPC_Bytes::b_strlen( DPC_DATA_FORMAT_SIGNATURE )
		);
		if ( $data_format_signature !== DPC_DATA_FORMAT_SIGNATURE ) {
			throw new DPC_Invalid_Input_Exception( 'Invalid data format signature' );
		}
	}

	/**
	 * Read and validate Version bytes.
	 *
	 * @param resource $chunk_handle Resource, e.g. an opened file, to read the Version from.
	 *
	 * @return void
	 *
	 * @throws DPC_Invalid_Input_Exception If Version is invalid, e.g. when the Reader doesn't support the version of a
	 *                                     Chunk that is to be parsed.
	 * @throws Exception On read errors.
	 */
	private static function read_and_validate_version( $chunk_handle ) {
		$data_format_version_encoded = DPC_Throw_On_Errors::t_fread(
			$chunk_handle,
			DPC_Data_Format_Version_Pack_Type::byte_size()
		);
		if ( DPC_Bytes::b_strlen( $data_format_version_encoded ) < DPC_Data_Format_Version_Pack_Type::byte_size() ) {
			throw new DPC_Invalid_Input_Exception( 'Unable to read data format version' );
		}

		$data_format_version = dpc_unpack_single_value(
			DPC_Data_Format_Version_Pack_Type::pack_argument(),
			$data_format_version_encoded,
			'data format version'
		);

		if ( $data_format_version !== DPC_DATA_FORMAT_VERSION ) {
			throw new DPC_Invalid_Input_Exception(
				'Incompatible data format version ' .
				'(expected: ' . DPC_DATA_FORMAT_VERSION . "; got: $data_format_version)"
			);
		}
	}

	/**
	 * Read and parse Metadata.
	 *
	 * @param resource $chunk_handle Resource, e.g. an opened file, to read the Metadata from.
	 *
	 * @return DPC_Input_Metadata Metadata object.
	 *
	 * @throws DPC_Invalid_Input_Exception When Metadata JSON doesn't look valid, e.g. on invalid input.
	 * @throws Exception On read errors.
	 */
	private static function read_metadata( $chunk_handle ) {
		$metadata_json_size_encoded = DPC_Throw_On_Errors::t_fread(
			$chunk_handle, DPC_Metadata_JSON_Size_Pack_Type::byte_size()
		);
		if ( DPC_Bytes::b_strlen( $metadata_json_size_encoded ) < DPC_Metadata_JSON_Size_Pack_Type::byte_size() ) {
			throw new DPC_Invalid_Input_Exception( 'Unable to read metadata JSON size' );
		}

		$metadata_json_size = dpc_unpack_single_value(
			DPC_Metadata_JSON_Size_Pack_Type::pack_argument(),
			$metadata_json_size_encoded,
			'metadata JSON size'
		);

		if ( $metadata_json_size < 1 || $metadata_json_size > DPC_METADATA_JSON_MAX_SIZE ) {
			throw new DPC_Invalid_Input_Exception( "Invalid metadata JSON path size: $metadata_json_size" );
		}

		$metadata_json = DPC_Throw_On_Errors::t_fread( $chunk_handle, $metadata_json_size );
		if ( DPC_Bytes::b_strlen( $metadata_json ) < $metadata_json_size ) {
			throw new DPC_Invalid_Input_Exception( 'Unable to read metadata JSON' );
		}

		$metadata_array = DPC_Throw_On_Errors::t_json_decode( $metadata_json, JSON_OBJECT_AS_ARRAY );
		if ( ! is_array( $metadata_array ) ) {
			throw new DPC_Invalid_Input_Exception(
				'Metadata decoded from this JSON is not an array: ' . print_r( $metadata_array, true )
			);
		}

		return DPC_Input_Metadata::from_array( $metadata_array );
	}

	/**
	 * Read and parse State.
	 *
	 * @param resource $chunk_handle Resource, e.g. an opened file, to read the State from.
	 *
	 * @return DPC_Read_State State object.
	 *
	 * @throws DPC_Invalid_Input_Exception When State JSON doesn't look valid, e.g. on invalid input.
	 * @throws Exception On read errors.
	 */
	private static function read_state( $chunk_handle ) {
		$state_json_size_encoded = DPC_Throw_On_Errors::t_fread(
			$chunk_handle, DPC_State_JSON_Size_Pack_Type::byte_size()
		);
		if ( DPC_Bytes::b_strlen( $state_json_size_encoded ) < DPC_State_JSON_Size_Pack_Type::byte_size() ) {
			throw new DPC_Invalid_Input_Exception( 'Unable to read state JSON size' );
		}

		$state_json_size = dpc_unpack_single_value(
			DPC_State_JSON_Size_Pack_Type::pack_argument(),
			$state_json_size_encoded,
			'state JSON size'
		);

		if ( $state_json_size < 0 || $state_json_size > DPC_STATE_JSON_MAX_SIZE ) {
			throw new DPC_Invalid_Input_Exception( "Invalid state JSON path size: $state_json_size" );
		}

		if ( 0 === $state_json_size ) {
			// Empty (initial) state.
			$state = new DPC_Entry_Type_Read_State();
		} else {
			$state_json = DPC_Throw_On_Errors::t_fread( $chunk_handle, $state_json_size );
			if ( DPC_Bytes::b_strlen( $state_json ) < $state_json_size ) {
				throw new DPC_Invalid_Input_Exception( 'Unable to read state JSON' );
			}

			$state_array = DPC_Throw_On_Errors::t_json_decode( $state_json, JSON_OBJECT_AS_ARRAY );
			if ( ! is_array( $state_array ) ) {
				throw new DPC_Invalid_Input_Exception( "State decoded from this JSON is not an array: $state_json" );
			}

			$state = static::state_from_state_array( $state_array );
			if ( is_null( $state ) ) {
				throw new DPC_Invalid_Input_Exception(
					'Unable to decode state array: ' . print_r( $state_array, true )
				);
			}
		}

		return $state;
	}

	/**
	 * Process Chunk data.
	 *
	 * Read Chunk data, create the needed files/directories/symlinks.
	 *
	 * @param resource $chunk_handle Resource, e.g. an opened file, to read the Chunk data from.
	 *
	 * @return DPC_Read_State State that the state machine was left in after reading a Chunk.
	 *
	 * @throws DPC_Invalid_Input_Exception On invalid Chunk data.
	 * @throws DPC_Internal_Error_Exception On Chunk processing errors, e.g. write failures.
	 * @throws Exception On read/write failures and other errors.
	 */
	private static function do_process_chunk( $chunk_handle ) {

		self::read_and_validate_signature( $chunk_handle );

		self::read_and_validate_version( $chunk_handle );

		$metadata = self::read_metadata( $chunk_handle );

		$state = self::read_state( $chunk_handle );

		while ( null !== $state ) {
			$next_state = $state->process_and_return_next_state( $chunk_handle, $metadata );

			if ( null === $next_state ) {

				if ( DPC_Throw_On_Errors::t_feof( $chunk_handle ) ) {

					// Reached the end of the chunk.
					return $state;

				} else {
					throw new DPC_Invalid_Input_Exception(
						'We expect to be at the EOF of the Chunk; last State: ' . print_r( $state, true )
					);
				}
			}

			$state = $next_state;
		}

		throw new DPC_Internal_Error_Exception( 'Should not have reached this point' );
	}

	/**
	 * Prepare and Process Chunk data.
	 *
	 * Copy Chunk data to a temporary file, validate data size, catch all errors, serialize the resulting State.
	 *
	 * @param string $input_path          Path to read the Chunk data from, e.g. "/var/tmp/foo" or "php://input".
	 * @param ?int   $input_expected_size Expected size of the Chunk data, or null if size is not known.
	 *
	 * @return array State (in an array form) that the state machine was left in after reading a Chunk.
	 *
	 * @throws DPC_Invalid_Input_Exception On invalid Chunk data.
	 * @throws DPC_Internal_Error_Exception On Chunk processing errors, e.g. write failures.
	 * @throws Exception On read/write failures and other errors.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public static function process_chunk( $input_path, $input_expected_size = null ) {

		// We want *all* errors (including E_WARNING, E_NOTICE, and E_DEPRECATED) to be reported back to us.
		$old_error_reporting = error_reporting( - 1 );
		$old_display_errors  = ini_set( 'display_errors', 'stderr' );
		set_error_handler(
		/**
		 * Error handler.
		 *
		 * Catch PHP errors, warnings, exceptions, and throw everything as Exception class-derived exception.
		 *
		 * @see https://php-legacy-docs.zend.com/manual/php5/en/function.set-error-handler
		 * @see https://www.php.net/manual/en/function.set-error-handler.php
		 *
		 * @param int         $errno      Level of the error raised.
		 * @param string      $errstr     Error message.
		 * @param string|null $errfile    Filename that the error was raised in.
		 * @param int|null    $errline    Line number where the error was raised.
		 * @param array|null  $errcontext Deprecated, unused.
		 *
		 * @return mixed
		 * @throws DPC_Internal_Error_Exception
		 */
			function ( $errno, $errstr, $errfile = null, $errline = null, $errcontext = null ) {
				throw new DPC_Internal_Error_Exception( "$errstr (file: $errfile; line: $errline)" );
			}
		);

		// Copy chunk to a temporary file because for whatever reason we're unable to use "php://input" / "php://stdin"
		// directly (e.g. it reports wrong data sizes, returns EOF at arbitrary locations).
		$chunk_handle = DPC_Throw_On_Errors::t_tmpfile();

		// Don't use file_get_contents() because a chunk might get large.
		$input_data_bytes_written = 0;
		$input_path_handle        = DPC_Throw_On_Errors::t_fopen( $input_path, 'rb' );
		while ( ! DPC_Throw_On_Errors::t_feof( $input_path_handle ) ) {
			$input_data_chunk = DPC_Throw_On_Errors::t_fread( $input_path_handle, 100 * 1024 );
			DPC_Throw_On_Errors::t_fwrite( $chunk_handle, $input_data_chunk );
			$input_data_bytes_written += DPC_Bytes::b_strlen( $input_data_chunk );
		}

		if ( ! is_null( $input_expected_size ) ) {
			if ( $input_expected_size !== $input_data_bytes_written ) {
				throw new DPC_Invalid_Input_Exception(
					"Didn't receive all the data; expected: $input_expected_size bytes; got: $input_data_bytes_written"
				);
			}
		}

		DPC_Throw_On_Errors::t_fseek( $chunk_handle, 0 );

		$state = null;
		$error = null;

		if ( PHP_MAJOR_VERSION >= 7 ) {
			// On PHP 7.x, all Exception and Error are subclasses of Throwable.
			try {
				$state = static::do_process_chunk( $chunk_handle );
			} catch ( Throwable $throwable ) {
				$error = $throwable;
			}
		} else {
			// On PHP 5.x, there's only Exception.
			try {
				$state = static::do_process_chunk( $chunk_handle );
			} catch ( Exception $exception ) {
				$error = $exception;
			}
		}

		// Clean up temporary file (PHP will do it itself eventually, but we're just speeding up things here to free up
		// disk space as soon as we can).
		DPC_Throw_On_Errors::t_fclose( $chunk_handle );

		restore_error_handler();
		ini_set( 'display_errors', $old_display_errors );
		error_reporting( $old_error_reporting );

		if ( is_null( $state ) ) {
			if ( is_null( $error ) ) {
				throw new DPC_Internal_Error_Exception( 'State is null but no error has been reported' );
			}

			if ( is_a( $error, DPC_HTTP_Status_Code_Exception::class ) ) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $error;
			} else {
				throw new DPC_Internal_Error_Exception( "Error while processing Chunk: " . $error->getMessage() );
			}

		} else {
			if ( ! is_null( $error ) ) {
				throw new DPC_Internal_Error_Exception(
					'State has been returned but an error has been reported as well: ' . $error->getMessage()
				);
			}
		}

		return static::state_array_from_state( $state );
	}

}



/**
 * Receiver that parses an incoming Chunk from an HTTP POST payload.
 */
class DPC_HTTP_Receiver {

	/**
	 * Send JSON response back to the caller.
	 *
	 * @param array $response         Free-form response that is to be serialized to JSON.
	 * @param int   $http_status_code HTTP status code to send together with the response.
	 *
	 * @return void
	 */
	private static function send_http_response( $response, $http_status_code ) {

		$content_type_header = 'Content-Type: application/json';

		$json_response = dpc_json_encode_pretty( $response );

		if ( false === $json_response ) {
			http_response_code( 500 );
			header( $content_type_header );
			echo '{"error": "Unable to encode response to JSON"}' . PHP_EOL;

		} else {
			http_response_code( $http_status_code );
			header( $content_type_header );
			echo $json_response;
		}
	}

	/**
	 * Process Chunk data coming in as HTTP POST payload.
	 *
	 * @return void
	 */
	public static function process_chunk_from_http_post() {

		$state      = null;
		$request_id = null;

		try {

			$expected_http_method = 'POST';
			$actual_http_method   = $_SERVER['REQUEST_METHOD'];
			if ( $expected_http_method !== $actual_http_method ) {
				throw new DPC_Invalid_Input_Exception(
					"Expected $expected_http_method request, got $actual_http_method"
				);
			}

			$actual_content_type = $_SERVER['CONTENT_TYPE'];
			if ( DPC_CHUNK_MIME_TYPE !== $actual_content_type ) {
				throw new DPC_Invalid_Input_Exception(
					'Expected ' . DPC_CHUNK_MIME_TYPE . " 'Content-Type' value, got $actual_content_type"
				);
			}

			$input_expected_size = isset( $_SERVER['CONTENT_LENGTH'] ) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
			if ( $input_expected_size < 1 ) {
				throw new DPC_Invalid_Input_Exception( 'Unable to read Content-Length' );
			}

			$request_id = isset( $_GET['request_id'] ) ? $_GET['request_id'] : null;

			// Both null and empty are bad.
			if ( ! $request_id ) {
				throw new DPC_Invalid_Input_Exception( 'Request ID is unset' );
			}

			$state = DPC_Receiver::process_chunk( 'php://input', $input_expected_size );

		} catch ( Exception $exception ) {

			$http_status_code = 500;
			if ( is_a( $exception, DPC_HTTP_Status_Code_Exception::class ) ) {
				$http_status_code = $exception->http_status_code;
			}

			$error_message = $exception->getMessage();

			$traces = $exception->getTrace();

			foreach ( $traces as &$trace ) {
				// json_encode() doesn't support / gets weird when trying to encode resources and such, so filter those
				// out.
				unset( $trace['args'] );
				unset( $trace['type'] );
			}

			$response = array(
				'error'  => $error_message,
				'traces' => $traces,
			);

			static::send_http_response( $response, $http_status_code );
		}

		if ( ! is_null( $state ) ) {
			static::send_http_response(
				array(
					'request_id' => $request_id,
					'state'      => $state,
				),
				200
			);
		}
	}

}


/**
 * Receive and process an incoming DPC Chunk.
 *
 * *Not* called by Transport Server, so doesn't send back X-Vp-* headers (via success_header() / fatal_error()).
 *
 * @param array $args Argument array (unused).
 *
 * @return void
 * @throws Exception
 *
 * @noinspection PhpUnusedParameterInspection
 */
function action_dpc_receive( $args ) {
	if ( is_cli() ) {
		$state = DPC_Receiver::process_chunk( 'php://stdin' );

		echo dpc_json_encode_pretty( array( 'state' => $state ) );
	} else {
		DPC_HTTP_Receiver::process_chunk_from_http_post();
	}
}

if ( ! function_exists( 'str_contains' ) ) {
	/**
	 * Polyfill for `str_contains()` function added in PHP 8.0.
	 *
	 * Performs a case-sensitive check indicating if needle is
	 * contained in haystack.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the `$haystack`.
	 * @return bool True if `$needle` is in `$haystack`, otherwise false.
	 */
	function str_contains( $haystack, $needle ) {
		if ( '' === $needle ) {
			return true;
		}

		return false !== strpos( $haystack, $needle );
	}
}

/**
 * WordPress database access abstraction class. Forked from original WP code for use in helper-script.
 * Stripped all the html rendering parts and kept only the minimum version required.
 *
 * This class is used to interact with a database without needing to use raw SQL statements.
 */
class WP_DB_Wrapper {

	/**
	 * Whether the database queries are ready to start executing.
	 *
	 * @since 2.3.2
	 *
	 * @var bool
	 */
	public $ready = false;
	/**
	 * Database table columns charset.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	public $charset;

	/**
	 * Database table columns collate.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	public $collate;

	/**
	 * Database Username.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	protected $dbuser;

	/**
	 * Database Password.
	 *
	 * @since 3.1.0
	 *
	 * @var string
	 */
	protected $dbpassword;

	/**
	 * Database Name.
	 *
	 * @since 3.1.0
	 *
	 * @var string
	 */
	protected $dbname;

	/**
	 * Database Host.
	 *
	 * @since 3.1.0
	 *
	 * @var string
	 */
	protected $dbhost;

	/**
	 * Database handle.
	 *
	 * Possible values:
	 *
	 * - `mysqli` instance when the `mysqli` driver is in use
	 * - `resource` when the older `mysql` driver is in use
	 * - `null` if the connection is yet to be made or has been closed
	 * - `false` if the connection has failed
	 *
	 * @since 0.71
	 *
	 * @var mysqli|resource|false|null
	 */
	public $dbh;
	/**
	 * Whether MySQL is used as the database engine.
	 *
	 * Set in wpdb::db_connect() to true, by default. This is used when checking
	 * against the required MySQL version for WordPress. Normally, a replacement
	 * database drop-in (db.php) will skip these checks, but setting this to true
	 * will force the checks to occur.
	 *
	 * @since 3.3.0
	 *
	 * @var bool
	 */
	public $is_mysql = null;

	/**
	 * A list of incompatible SQL modes.
	 *
	 * @since 3.9.0
	 *
	 * @var string[]
	 */
	protected $incompatible_modes = array(
		'NO_ZERO_DATE',
		'ONLY_FULL_GROUP_BY',
		'STRICT_TRANS_TABLES',
		'STRICT_ALL_TABLES',
		'TRADITIONAL',
		'ANSI',
	);
	/**
	 * Whether to use mysqli over mysql. Default false.
	 *
	 * @since 3.9.0
	 *
	 * @var bool
	 */
	public $use_mysqli = false;

	/**
	 * Whether we've managed to successfully connect at some point.
	 *
	 * @since 3.9.0
	 *
	 * @var bool
	 */
	private $has_connected = false;
	/**
	 * Connects to the database server and selects a database.
	 *
	 * Does the actual setting up
	 * of the class properties and connection to the database.
	 *
	 * @since 2.0.8
	 *
	 * @link https://core.trac.wordpress.org/ticket/3354
	 *
	 * @param string $dbuser     Database user.
	 * @param string $dbpassword Database password.
	 * @param string $dbname     Database name.
	 * @param string $dbhost     Database host.
	 * @param string $dbcharset  Database charset, default utf-8.
	 * @param string $dbcollate  Database collate, default blank.
	 */
	public function __construct( $dbuser, $dbpassword, $dbname, $dbhost, $dbcharset = 'utf-8', $dbcollate = '') {
		// Use the `mysqli` extension if it exists unless `WP_USE_EXT_MYSQL` is defined as true.
		if ( function_exists( 'mysqli_connect' ) ) {
			$this->use_mysqli = true;

			if ( defined( 'WP_USE_EXT_MYSQL' ) ) {
				$this->use_mysqli = ! WP_USE_EXT_MYSQL;
			}
		}

		$this->dbuser     = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname     = $dbname;
		$this->dbhost     = $dbhost;
		$this->collate    = $dbcollate;
		$this->charset    = $dbcharset;

		$this->db_connect();
	}
	/**
	 * Connects to and selects database.
	 *
	 * @return bool True with a successful connection, false on failure.
	 */
	public function db_connect() {
		$this->is_mysql = true;

		/*
		 * Deprecated in 3.9+ when using MySQLi. No equivalent
		 * $new_link parameter exists for mysqli_* functions.
		 */
		$new_link     = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
		$client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;

		if ( $this->use_mysqli ) {
			/*
			 * Set the MySQLi error reporting off because WordPress handles its own.
			 * This is due to the default value change from `MYSQLI_REPORT_OFF`
			 * to `MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT` in PHP 8.1.
			 */
			mysqli_report( MYSQLI_REPORT_OFF );

			$this->dbh = mysqli_init();

			$host    = $this->dbhost;
			$port    = null;
			$socket  = null;
			$is_ipv6 = false;

			$host_data = $this->parse_db_host( $this->dbhost );
			if ( $host_data ) {
				list($host, $port, $socket, $is_ipv6) = $host_data;
			}

			/*
			 * If using the `mysqlnd` library, the IPv6 address needs to be enclosed
			 * in square brackets, whereas it doesn't while using the `libmysqlclient` library.
			 * @see https://bugs.php.net/bug.php?id=67563
			 */
			if ( $is_ipv6 && extension_loaded( 'mysqlnd' ) ) {
				$host = "[$host]";
			}

			mysqli_real_connect( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );

			if ( $this->dbh->connect_errno ) {
				$this->dbh = null;

				/*
				 * It's possible ext/mysqli is misconfigured. Fall back to ext/mysql if:
				 *  - We haven't previously connected, and
				 *  - WP_USE_EXT_MYSQL isn't set to false, and
				 *  - ext/mysql is loaded.
				 */
				$attempt_fallback = true;

				if ( $this->has_connected ) {
					$attempt_fallback = false;
				} elseif ( defined( 'WP_USE_EXT_MYSQL' ) && ! WP_USE_EXT_MYSQL ) {
					$attempt_fallback = false;
				} elseif ( ! function_exists( 'mysql_connect' ) ) {
					$attempt_fallback = false;
				}

				if ( $attempt_fallback ) {
					$this->use_mysqli = false;
					return $this->db_connect();
				}
			}
		} else {
			$this->dbh = mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
		}

		if ( ! $this->dbh ) {
			$message = 'Error establishing a database connection.';
			$this->bail( $message, 'db_connect_fail' );

			return false;
		} elseif ( $this->dbh ) {
			if ( ! $this->has_connected ) {
				$this->init_charset();
			}

			$this->has_connected = true;

			$this->set_charset( $this->dbh );

			$this->ready = true;
			$this->set_sql_mode();
			$this->select( $this->dbname, $this->dbh );

			return true;
		}

		return false;
	}
	/**
	 * Parses the DB_HOST setting to interpret it for mysqli_real_connect().
	 *
	 * mysqli_real_connect() doesn't support the host param including a port or socket
	 * like mysql_connect() does. This duplicates how mysql_connect() detects a port
	 * and/or socket file.
	 *
	 * @since 4.9.0
	 *
	 * @param string $host The DB_HOST setting to parse.
	 * @return array|false {
	 *     Array containing the host, the port, the socket and
	 *     whether it is an IPv6 address, in that order.
	 *     False if the host couldn't be parsed.
	 *
	 *     @type string      $0 Host name.
	 *     @type string|null $1 Port.
	 *     @type string|null $2 Socket.
	 *     @type bool        $3 Whether it is an IPv6 address.
	 * }
	 */
	public function parse_db_host( $host ) {
		$socket  = null;
		$is_ipv6 = false;

		// First peel off the socket parameter from the right, if it exists.
		$socket_pos = strpos( $host, ':/' );
		if ( false !== $socket_pos ) {
			$socket = substr( $host, $socket_pos + 1 );
			$host   = substr( $host, 0, $socket_pos );
		}

		// We need to check for an IPv6 address first.
		// An IPv6 address will always contain at least two colons.
		if ( substr_count( $host, ':' ) > 1 ) {
			$pattern = '#^(?:\[)?(?P<host>[0-9a-fA-F:]+)(?:\]:(?P<port>[\d]+))?#';
			$is_ipv6 = true;
		} else {
			// We seem to be dealing with an IPv4 address.
			$pattern = '#^(?P<host>[^:/]*)(?::(?P<port>[\d]+))?#';
		}

		$matches = array();
		$result  = preg_match( $pattern, $host, $matches );

		if ( 1 !== $result ) {
			// Couldn't parse the address, bail.
			return false;
		}

		$host = ! empty( $matches['host'] ) ? $matches['host'] : '';
		// MySQLi port cannot be a string; must be null or an integer.
		$port = ! empty( $matches['port'] ) ? abs( $matches['port'] ) : null;

		return array( $host, $port, $socket, $is_ipv6 );
	}
	/**
	 * Sets $this->charset and $this->collate.
	 *
	 * @since 3.1.0
	 */
	public function init_charset() {
		$charset = $this->charset;
		$collate = $this->collate;

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$charset = 'utf8';
			if ( empty( $collate ) ) {
				$collate = 'utf8_general_ci';
			}
		}

		$charset_collate = $this->determine_charset( $charset, $collate );

		$this->charset = $charset_collate['charset'];
		$this->collate = $charset_collate['collate'];
	}

	/**
	 * Determines the best charset and collation to use given a charset and collation.
	 *
	 * For example, when able, utf8mb4 should be used instead of utf8.
	 *
	 * @since 4.6.0
	 *
	 * @param string $charset The character set to check.
	 * @param string $collate The collation to check.
	 * @return array {
	 *     The most appropriate character set and collation to use.
	 *
	 *     @type string $charset Character set.
	 *     @type string $collate Collation.
	 * }
	 */
	public function determine_charset( $charset, $collate ) {
		if ( ( $this->use_mysqli && ! ( $this->dbh instanceof mysqli ) ) || empty( $this->dbh ) ) {
			return compact( 'charset', 'collate' );
		}

		if ( 'utf8' === $charset && $this->has_cap( 'utf8mb4' ) ) {
			$charset = 'utf8mb4';
		}

		if ( 'utf8mb4' === $charset && ! $this->has_cap( 'utf8mb4' ) ) {
			$charset = 'utf8';
			$collate = str_replace( 'utf8mb4_', 'utf8_', $collate );
		}

		if ( 'utf8mb4' === $charset ) {
			// _general_ is outdated, so we can upgrade it to _unicode_, instead.
			if ( ! $collate || 'utf8_general_ci' === $collate ) {
				$collate = 'utf8mb4_unicode_ci';
			} else {
				$collate = str_replace( 'utf8_', 'utf8mb4_', $collate );
			}
		}

		// _unicode_520_ is a better collation, we should use that when it's available.
		if ( $this->has_cap( 'utf8mb4_520' ) && 'utf8mb4_unicode_ci' === $collate ) {
			$collate = 'utf8mb4_unicode_520_ci';
		}

		return compact( 'charset', 'collate' );
	}
	/**
	 * Sets the connection's character set.
	 *
	 * @since 3.1.0
	 *
	 * @param mysqli|resource $dbh     The connection returned by `mysqli_connect()` or `mysql_connect()`.
	 * @param string          $charset Optional. The character set. Default null.
	 * @param string          $collate Optional. The collation. Default null.
	 */
	public function set_charset( $dbh, $charset = null, $collate = null ) {
		if ( ! isset( $charset ) ) {
			$charset = $this->charset;
		}
		if ( ! isset( $collate ) ) {
			$collate = $this->collate;
		}
		if ( $this->has_cap( 'collation' ) && ! empty( $charset ) ) {
			$set_charset_succeeded = true;

			if ( $this->use_mysqli ) {
				if ( function_exists( 'mysqli_set_charset' ) && $this->has_cap( 'set_charset' ) ) {
					$set_charset_succeeded = mysqli_set_charset( $dbh, $charset );
				}

				if ( $set_charset_succeeded ) {
					$query = sprintf( 'SET NAMES %s', $charset );
					if ( ! empty( $collate ) ) {
						$query .= sprintf( ' COLLATE %s', $collate );
					}
					mysqli_query( $dbh, $query );
				}
			} else {
				if ( function_exists( 'mysql_set_charset' ) && $this->has_cap( 'set_charset' ) ) {
					$set_charset_succeeded = mysql_set_charset( $charset, $dbh );
				}
				if ( $set_charset_succeeded ) {
					$query = sprintf( 'SET NAMES %s', $charset );
					if ( ! empty( $collate ) ) {
						$query .= sprintf( ' COLLATE %s', $collate );
					}
					mysql_query( $query, $dbh );
				}
			}
		}
	}
	/**
	 * Changes the current SQL mode, and ensures its WordPress compatibility.
	 *
	 * If no modes are passed, it will ensure the current MySQL server modes are compatible.
	 *
	 * @since 3.9.0
	 *
	 * @param array $modes Optional. A list of SQL modes to set. Default empty array.
	 */
	public function set_sql_mode( $modes = array() ) {
		if ( empty( $modes ) ) {
			if ( $this->use_mysqli ) {
				$res = mysqli_query( $this->dbh, 'SELECT @@SESSION.sql_mode' );
			} else {
				$res = mysql_query( 'SELECT @@SESSION.sql_mode', $this->dbh );
			}

			if ( empty( $res ) ) {
				return;
			}

			if ( $this->use_mysqli ) {
				$modes_array = mysqli_fetch_array( $res );
				if ( empty( $modes_array[0] ) ) {
					return;
				}
				$modes_str = $modes_array[0];
			} else {
				$modes_str = mysql_result( $res, 0 );
			}

			if ( empty( $modes_str ) ) {
				return;
			}

			$modes = explode( ',', $modes_str );
		}

		$modes              = array_change_key_case( $modes, CASE_UPPER );
		$incompatible_modes = $this->incompatible_modes;
		if ( function_exists( 'apply_filters' ) ) {
			/**
			 * Filters the list of incompatible SQL modes to exclude.
			 *
			 * @since 3.9.0
			 *
			 * @param array $incompatible_modes An array of incompatible modes.
			 */
			$incompatible_modes = (array) apply_filters( 'incompatible_sql_modes', $this->incompatible_modes );
		}
		foreach ( $modes as $i => $mode ) {
			if ( in_array( $mode, $incompatible_modes, true ) ) {
				unset( $modes[ $i ] );
			}
		}

		$modes_str = implode( ',', $modes );

		if ( $this->use_mysqli ) {
			mysqli_query( $this->dbh, "SET SESSION sql_mode='$modes_str'" );
		} else {
			mysql_query( "SET SESSION sql_mode='$modes_str'", $this->dbh );
		}
	}
	/**
	 * Selects a database using the current or provided database connection.
	 *
	 * The database name will be changed based on the current database connection.
	 * On failure, the execution will bail and display a DB error.
	 *
	 * @since 0.71
	 *
	 * @param string          $db  Database name.
	 * @param mysqli|resource $dbh Optional. Database connection.
	 *                             Defaults to the current database handle.
	 */
	public function select( $db, $dbh = null ) {
		if ( is_null( $dbh ) ) {
			$dbh = $this->dbh;
		}

		if ( $this->use_mysqli ) {
			$success = mysqli_select_db( $dbh, $db );
		} else {
			$success = mysql_select_db( $db, $dbh );
		}
		if ( ! $success ) {
			$this->ready = false;
			$message     = sprintf(
				"Cannot select database '%s'.",
				htmlspecialchars( $db, ENT_QUOTES )
			);
			$this->bail( $message, 'db_select_fail' );
		}
	}
	/**
	 * Wraps errors in a nice header and footer and dies.
	 *
	 * Will not die if wpdb::$show_errors is false.
	 *
	 * @since 1.5.0
	 *
	 * @param string $message    The error message.
	 * @param string $error_code Optional. A computer-readable string to identify the error.
	 *                           Default '500'.
	 * @throws Exception
	 */
	public function bail( $message, $error_code = '500' ) {
		$error = '';

		if ( $this->use_mysqli ) {
			if ( $this->dbh instanceof mysqli ) {
				$error = mysqli_error( $this->dbh );
			} elseif ( mysqli_connect_errno() ) {
				$error = mysqli_connect_error();
			}
		} else {
			if ( is_resource( $this->dbh ) ) {
				$error = mysql_error( $this->dbh );
			} else {
				$error = mysql_error();
			}
		}

		if ( $error ) {
			$message = $message . ' Reason: ' . $error;
		}

		throw new Exception( $message );
	}

	/**
	 * Closes the current database connection.
	 *
	 * @since 4.5.0
	 *
	 * @return bool True if the connection was successfully closed,
	 *              false if it wasn't, or if the connection doesn't exist.
	 */
	public function close() {
		if ( ! $this->dbh ) {
			return false;
		}

		if ( $this->use_mysqli ) {
			$closed = mysqli_close( $this->dbh );
		} else {
			$closed = mysql_close( $this->dbh );
		}

		if ( $closed ) {
			$this->dbh           = null;
			$this->ready         = false;
			$this->has_connected = false;
		}

		return $closed;
	}
	/**
	 * Determines whether the database or WPDB supports a particular feature.
	 *
	 * Capability sniffs for the database server and current version of WPDB.
	 *
	 * Database sniffs are based on the version of MySQL the site is using.
	 *
	 * WPDB sniffs are added as new features are introduced to allow theme and plugin
	 * developers to determine feature support. This is to account for drop-ins which may
	 * introduce feature support at a different time to WordPress.
	 *
	 * @since 2.7.0
	 * @since 4.1.0 Added support for the 'utf8mb4' feature.
	 * @since 4.6.0 Added support for the 'utf8mb4_520' feature.
	 * @since 6.2.0 Added support for the 'identifier_placeholders' feature.
	 *
	 * @see wpdb::db_version()
	 *
	 * @param string $db_cap The feature to check for. Accepts 'collation', 'group_concat',
	 *                       'subqueries', 'set_charset', 'utf8mb4', 'utf8mb4_520',
	 *                       or 'identifier_placeholders'.
	 * @return bool True when the database feature is supported, false otherwise.
	 */
	public function has_cap( $db_cap ) {
		$db_version     = $this->db_version();
		$db_server_info = $this->db_server_info();

		// Account for MariaDB version being prefixed with '5.5.5-' on older PHP versions.
		if (
			'5.5.5' === $db_version && str_contains( $db_server_info, 'MariaDB' )
			&& PHP_VERSION_ID < 80016 // PHP 8.0.15 or older.
		) {
			// Strip the '5.5.5-' prefix and set the version to the correct value.
			$db_server_info = preg_replace( '/^5\.5\.5-(.*)/', '$1', $db_server_info );
			$db_version     = preg_replace( '/[^0-9.].*/', '', $db_server_info );
		}

		switch ( strtolower( $db_cap ) ) {
			case 'collation':    // @since 2.5.0
			case 'group_concat': // @since 2.7.0
			case 'subqueries':   // @since 2.7.0
				return version_compare( $db_version, '4.1', '>=' );
			case 'set_charset':
				return version_compare( $db_version, '5.0.7', '>=' );
			case 'utf8mb4':      // @since 4.1.0
				if ( version_compare( $db_version, '5.5.3', '<' ) ) {
					return false;
				}
				if ( $this->use_mysqli ) {
					$client_version = mysqli_get_client_info();
				} else {
					$client_version = mysql_get_client_info();
				}

				/*
				 * libmysql has supported utf8mb4 since 5.5.3, same as the MySQL server.
				 * mysqlnd has supported utf8mb4 since 5.0.9.
				 */
				if ( false !== strpos( $client_version, 'mysqlnd' ) ) {
					$client_version = preg_replace( '/^\D+([\d.]+).*/', '$1', $client_version );
					return version_compare( $client_version, '5.0.9', '>=' );
				} else {
					return version_compare( $client_version, '5.5.3', '>=' );
				}
			case 'utf8mb4_520': // @since 4.6.0
				return version_compare( $db_version, '5.6', '>=' );
			case 'identifier_placeholders': // @since 6.2.0
				/*
				 * As of WordPress 6.2, wpdb::prepare() supports identifiers via '%i',
				 * e.g. table/field names.
				 */
				return true;
		}

		return false;
	}
	/**
	 * Retrieves the database server version.
	 *
	 * @since 2.7.0
	 *
	 * @return string|null Version number on success, null on failure.
	 */
	public function db_version() {
		return preg_replace( '/[^0-9.].*/', '', $this->db_server_info() );
	}

	/**
	 * Retrieves full database server information.
	 *
	 * @since 5.5.0
	 *
	 * @return string|false Server info on success, false on failure.
	 */
	public function db_server_info() {
		if ( $this->use_mysqli ) {
			$server_info = mysqli_get_server_info( $this->dbh );
		} else {
			$server_info = mysql_get_server_info( $this->dbh );
		}

		return $server_info;
	}
}

/**
 * Main entrypoint to the helper script.
 *
 * @return void
 */
function main() {

	ini_set( 'error_reporting', 0 );

	// Ensure no output buffering
	while ( ob_get_level() ) {
		ob_end_clean();
	}

	// Unpack arguments; support CLI or web.
	if ( is_cli() ) {
		if ( count( $_SERVER['argv'] ) !== 3 ) {
			fatal_error( COMMS_ERROR, 'Invalid args', 400 );
		}

		list( $script, $action, $base64_args ) = $_SERVER['argv'];
	} else {
		$action      = array_key_exists( 'action', $_REQUEST ) ? $_REQUEST['action'] : null;
		$base64_args = array_key_exists( 'args', $_REQUEST ) ? $_REQUEST['args'] : null;
		$salt        = array_key_exists( 'salt', $_REQUEST ) ? $_REQUEST['salt'] : null;
		$signature   = array_key_exists( 'signature', $_REQUEST ) ? (string) $_REQUEST['signature'] : '';
	}

	$json_args = base64_decode( $base64_args );

	if ( ! is_cli() ) {
		// Check expiry
		if ( time() > JP_EXPIRES ) {
			fatal_error( EXPIRY_ERROR, 'Expired', 419 );
		}

		// Check signature.
		if ( ! authenticate( $action, $json_args, $salt, $signature ) ) {
			fatal_error( COMMS_ERROR, 'Forbidden', 403 );
		}

		// Set an opaque Content-Type by default, to avoid tripping up broken web servers.
		header( 'Content-Type: application/octet-stream' );
	}

	// Execute action.
	$args = (array) json_decode( $json_args );
	jpr_action( $action, $args );
}

// Run main() unless this file is being included from the test suite.
if ( ! defined( 'JPB_TEST_SUITE' ) ) {
	main();
}
