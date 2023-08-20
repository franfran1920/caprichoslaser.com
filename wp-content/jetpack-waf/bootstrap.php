<?php
define( 'DISABLE_JETPACK_WAF', false );
if ( defined( 'DISABLE_JETPACK_WAF' ) && DISABLE_JETPACK_WAF ) return;
define( 'JETPACK_WAF_MODE', 'normal' );
define( 'JETPACK_WAF_SHARE_DATA', '1' );
define( 'JETPACK_WAF_DIR', '/var/www/html/caprichoslaser.com/wp-content/jetpack-waf' );
define( 'JETPACK_WAF_WPCONFIG', '/var/www/html/caprichoslaser.com/wp-content/../wp-config.php' );
require_once '/var/www/html/caprichoslaser.com/wp-content/plugins/jetpack-disabled/vendor/autoload.php';
Automattic\Jetpack\Waf\Waf_Runner::initialize();