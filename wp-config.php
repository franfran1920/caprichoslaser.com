<?php
//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'caprichoslaser' );

/** Database username */
define( 'DB_USER', 'franfran1920' );

/** Database password */
define( 'DB_PASSWORD', 'diesinueve19' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define("FS_METHOD","direct");
/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '?tPO$mm^DCrZuUwVg,h4Q0LsPQ:gO[%8o..OVFNS?@()SG9Al L!NJ b<iq?<gR!' );
define( 'SECURE_AUTH_KEY',  'yy^^Qx|&js(Mm1gKOfz9htTc^24+RoWv:S}~i2yG2d7xzIHJ:8NE8j4re$&>3cvk' );
define( 'LOGGED_IN_KEY',    'XDdCTI1u /4g$,IxG E x()+|+Mrmy$y~V,EUm*1R+flIn6k!&dTsrEL0 ;2~3CP' );
define( 'NONCE_KEY',        '#*:@V?za-|mEK&Na^^HRzqym7bJhn];QI?%hc$h2im[3UQZ>[B/|9Hi&_qG2ux z' );
define( 'AUTH_SALT',        'e1e*/V8uy|s2oVi4l/z{Tq*Q7z1J1Q`:%bSwx:S_p!2GSrUR=<~p2%RKIo8X8~n,' );
define( 'SECURE_AUTH_SALT', '6~/DFAIjSH4Z7`>:U5AH8 ZKHQMfEv;=|-YRS_B*/E2bN2p*QVAx;>G!+?Ps#*#r' );
define( 'LOGGED_IN_SALT',   ' 5(+{0iLqpKHLY$A|Om9(] -RR40bu]<`ZGAdHvt_(,md;-*vTusfMo=v(ZMHd<%' );
define( 'NONCE_SALT',       'U,uGXjCl7|snGd8klqC#d[)hvJx2Mbk&@BRrV_V1oA4~+q^vkip1[ -+,dfeA6`3' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */
define('WP_MEMORY_LIMIT', '256M');


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
