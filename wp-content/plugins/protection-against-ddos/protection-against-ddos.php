<?php
/*
Plugin Name: Protection against DDoS
Description: Protection against DDoS.
Author: WPChef
Author URI: https://wpchef.org
Text Domain: protection-against-ddos
Version: 1.5.2
*/

	// Make sure we don't expose any info if called directly
	if ( !function_exists( 'add_action' ) ) {
		echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
		exit;
	}

	class ProtectionAgainstDDoSException extends Exception {}

	class ProtectionAgainstDDoS{
		public $plugin_title = 'Protection against DDoS';
		private static $instance;

		protected $cookie_name_prefix = 'paddos';
		protected $cookie_postfix_length = 5;
		protected $cookie_value = 1;
		//protected $cookie_time = 31536000;//60sec*60min*24h*365d = 1 year;
		protected $option_name = 'paddos';

		protected $redirect_url = 'http://127.0.0.1/';

		protected $cookie_set = false;

		public static function instance()
		{
			if( !isset( self::$instance ) ){
				self::$instance = new self();
			}
			return self::$instance;
		}

		private function __construct()
		{
			add_action( 'admin_init', array( $this , 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			add_action( 'wp_footer', array( $this , 'footer_js_cookie' ) );
			add_action( 'admin_footer', array( $this , 'footer_js_cookie' ) );
			add_action( 'login_footer', array( $this , 'footer_js_cookie' ) );

			//var_dump( $_SERVER ); exit;
		}

		public function get_settings( $option = null )
		{
			$defaults = array(
				'deny_xmlrpc' => false,
				'deny_feeds' => false,
				'deny_autodiscover' => false,
				'deny_wpad' => false,
				'deny_countries' => true,
				'countries' => '',
				'redirect_url' => $this->redirect_url,
				'cookie_name' => '',
				'patched' => false,
			);

			$settings = get_option( $this->option_name );
			if ( is_array( $settings ) )
				$settings += $defaults;

			elseif ( $settings )
			{
				$cookie_name = $settings;
				$settings = $defaults;
				$settings['cookie_name'] = $cookie_name;
			}

			else
				$settings = $defaults;

			if ( !$option )
				return $settings;

			elseif ( !isset($settings[ $option ]) )
				return null;

			return $settings[ $option ];
		}

		function set_settings( $values, $option = null )
		{
			$settings = $this->get_settings();
			if ( $option )
				$settings[ $option ] = $values;

			elseif ( !is_array( $values ) )
				return false;

			else
				$settings = array_merge( $settings, $values );

			update_site_option( $this->option_name, $settings );
		}

		function admin_menu()
		{
			add_options_page( 'Protection against DDoS Settings', 'DDoS Protection', 'install_plugins', $this->option_name, array( $this, 'settings_page' ) );
		}

		function admin_init()
		{
			if ( is_multisite() && !is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
			{
				add_action( 'all_admin_notices', array( $this, 'error_notice_mu' ) );
				return;
			}

			if ( !$this->get_settings( 'cookie_name' ) && !$this->activate() )
				add_action( 'all_admin_notices', array( $this, 'error_notice' ) );
		}

		public function activate()
		{
			try {
				$this->patch_htaccess();
			}
			catch ( Exception $e )
			{
				$this->set_settings( $e->getMessage(), 'error' );
				return false;
			}

			return true;
		}

		public function settings_page()
		{
			if ( !empty($_POST[$this->option_name]) && check_admin_referer( $this->option_name ) )
			{
				$post = stripslashes_deep( $_POST[$this->option_name] );
				$settings = array(
					'deny_xmlrpc' => !empty( $post['deny_xmlrpc'] ),
					'deny_feeds' => !empty( $post['deny_feeds'] ),
					'deny_autodiscover' => !empty( $post['deny_autodiscover'] ),
					'deny_wpad' => !empty( $post['deny_wpad'] ),
					'deny_countries' => !empty( $post['deny_countries'] ),
					'countries' => $post['countries'],
					'redirect_url' => $post['redirect_url'],
					'patched' => false,
				);
				$this->set_settings( $settings );
				?>
				<div class="notice notice-success"><p>Settings saved.</p></div>
				<?php
			}

			$settings = $this->get_settings();
			$error = false;
			if ( !$settings['patched'] )
			{
				try {
					$this->patch_htaccess();
					$this->set_settings( true, 'patched' );
				}
				catch ( Exception $e )
				{
					$error = $e->getMessage();
				}

			}
			?>
			<div class="wrap">
			<?php if ( $error ): ?>
				<div class="notice notice-error"><p>
					New settings was not applied because the .htaccess file is not writable. Please make it writable and try again.
				</p></div>
				<?php endif ?>
			<h1>Protection against DDoS Settings</h1>
			<form method="post">
				<?php wp_nonce_field( $this->option_name ) ?>
				<table class="form-table">
					<tr>
						<th>Restriction features:
						<td>
							<p><label>
								<input type="checkbox" name="<?php echo $this->option_name?>[deny_xmlrpc]" value="1" <?php echo $settings['deny_xmlrpc'] ? 'checked ':'' ?>/>
								Deny access <b>from outside</b> to xmlrpc.php
							</label></p>
							<p><label>
								<input type="checkbox" name="<?php echo $this->option_name?>[deny_feeds]" value="1" <?php echo $settings['deny_feeds'] ? 'checked ':'' ?>/>
								Deny access <b>from outside</b> to all feeds
							</label></p>
							<p><label>
								<input type="checkbox" name="<?php echo $this->option_name?>[deny_autodiscover]" value="1" <?php echo $settings['deny_autodiscover'] ? 'checked ':'' ?>/>
                                    Deny access <b>from outside</b> to <a href="https://docs.microsoft.com/Exchange/architecture/client-access/autodiscover" target="_blank">autodiscover/autodiscover.xml</a>
							</label></p>
							<p><label>
								<input type="checkbox" name="<?php echo $this->option_name?>[deny_wpad]" value="1" <?php echo $settings['deny_wpad'] ? 'checked ':'' ?>/>
                                    Deny access <b>from outside</b> to <a href="https://wikipedia.org/wiki/Web_Proxy_Auto-Discovery_Protocol" target="_blank">wpad.dat</a>
							</label></p>
					<tr>
						<th>
							<select name="<?php echo $this->option_name ?>[deny_countries]">
								<option value="1" <?php echo $settings['deny_countries'] ? 'selected ':''?>>Deny</option>
								<option value="" <?php echo $settings['deny_countries'] ? '':'selected '?>>Allow</option>
							</select> countries:
						<td>
							<input type="text" name="<?php echo $this->option_name ?>[countries]" value="<?php echo esc_attr($settings['countries']) ?>" />
							<p class="description">
								For CloudFlare users only: Specify the <a href="https://support.cloudflare.com/hc/articles/217074967#1QrbtSK5NSL7A0FOWD2bbZ" target="_blank">two-letter country codes</a> here you want to allow or deny, separated by comma.
							</p>
					<tr>
						<th>
							Redirect to:
						<td>
							<input type="text" name="<?php echo $this->option_name ?>[redirect_url]" value="<?php echo esc_attr($settings['redirect_url']) ?>" />
							<p class="description">Denied requests will be redirected to this URL.</p>
					<tr>
						<th>
						<td>
							<?php submit_button(); ?>
				</tabe>
			</form>
		</div>
			<?php
		}

		public function error_notice()
		{
			$this->activation_error_notice('Can\'t activate the Protection Against DDoS plugin because the .htaccess file is not writable. Please make it writable and try again.');
		}

		public function error_notice_mu()
		{
			$this->activation_error_notice('Please activate the Protection Against DDoS plugin for the entire network. It can\'t be activated for a particular site within a network.');
		}

		public function activation_error_notice( $msg )
		{
			?>
			<div class="notice notice-error paddos-error">
				<p><?php echo $msg ?></p>
			</div>
			<script>jQuery(function($){ $('.paddos-error').siblings('.updated.notice').hide(); })</script>
			<?php

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
				deactivate_plugins( plugin_basename( __FILE__ ), false, true );

			if ( is_plugin_active( plugin_basename( __FILE__ ) ) )
				deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		static function deactivate()
		{
			$self = self::instance();
			$cookie_name = $self->get_settings('cookie_name');

			if ( !$cookie_name )
				return;

			try {
				$self->unpatch_htaccess();
			}
			catch ( Exception $e )
			{
				wp_die( 'The .htaccess file is not writable. The Protection Against DDoS plugin will be deactivated, but to complete the process please clean up the .htaccess file manually. Otherwise your site will not work correctly.' );
				//$self->error_message = $e->getMessage();
				//add_action( 'admin_notices', array( $this, 'error_notice' ) );
			}
		}

		protected function get_new_cookie_name()
		{
			$cookie_name_postfix = wp_generate_password($this->cookie_postfix_length, false);
			$cookie_name = $this->cookie_name_prefix.'_'.$cookie_name_postfix;
			return $cookie_name;
		}

		public function patch_htaccess()
		{
			$cookie_name = $this->get_settings( 'cookie_name' );
			if ( !$cookie_name )
				$cookie_name = $this->get_new_cookie_name();

			if ( class_exists( 'SecureLockDown' ) )
				SecureLockDown::instance()->temp_unlock_htaccess();

			$this->unpatch_htaccess();

			$inserts = $this->get_htaccess_inserts($cookie_name);
			$htaccess_filename = $this->get_htaccess_filename();
			$section_name = $this->plugin_title;
			$htaccess_content_without_section = $this->get_htaccess_content_without_section( $htaccess_filename, $section_name );
			$this->write_htaccess_section( $htaccess_filename, $section_name, $inserts );

			if( !is_writable($htaccess_filename) || file_put_contents($htaccess_filename, $htaccess_content_without_section, FILE_APPEND) === false)
				throw new Exception('WARNING!!! File \'htaccess\' may be damaged!!! Can\'t write initial content of ".htaccess"-file after having written plugin\'s section.');

			$this->set_settings( $cookie_name, 'cookie_name' );
		}

		public function unpatch_htaccess()
		{
			if ( class_exists( 'SecureLockDown' ) )
				SecureLockDown::instance()->temp_unlock_htaccess();

			$htaccess_filename = $this->get_htaccess_filename();
			$htaccess_content_without_section = $this->get_htaccess_content_without_section( $htaccess_filename, $this->plugin_title );
			if( !is_writable($htaccess_filename) || file_put_contents($htaccess_filename, $htaccess_content_without_section)===false)
				throw new Exception('Can\'t write file \'.htaccess\'.');

			$this->set_settings( '', 'cookie_name' );
		}

		public function footer_js_cookie()
		{
			if ( $this->cookie_set || $_SERVER['REQUEST_METHOD'] != 'GET' )
				return;

			$cookie_name = $this->get_settings('cookie_name');
			$cookie_value = $this->cookie_value;
			if(isset($_COOKIE[$cookie_name]) && ($_COOKIE[$cookie_name]==$cookie_value)) return;
			//$cookie_time = time()+$this->cookie_time;
			?>
<script>eval(function(p,a,c,k,e,d){e=function(c){return c.toString(36)};if(!''.replace(/^/,String)){while(c--){d[c.toString(a)]=k[c]||c.toString(a)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('7 3=2 0(2 0().6()+5*4*1*1*f);8.e="c=b; 9=/; a="+3.d();',16,16,'Date|60|new|date|24|365|getTime|var|document|path|expires|<?=$cookie_value?>|<?=$cookie_name?>|toUTCString|cookie|1000'.split('|'),0,{}))</script>
<?php
			//setcookie($cookie_name, $cookie_value, $cookie_time, COOKIEPATH, COOKIE_DOMAIN, false);
		}


		protected function get_htaccess_filename()
		{
			$htaccess_filename = get_home_path().'.htaccess';
			return $htaccess_filename;
		}

		protected function get_htaccess_inserts($cookie_name)
		{
			$htaccess_template = $this->htaccess_template();
			$login_url = parse_url(wp_login_url(),PHP_URL_PATH);

			$vars = array(
				'cookie_name' => $cookie_name,
				'cookie_value' => $this->cookie_value,
				'$redirect_url' => $this->get_settings('redirect_url'),
				'login_url' => $login_url,
			);
			$search = $replace = array();
			foreach ( $vars as $key => $val )
			{
				$search[] = '{'.$key.'}';
				$replace[] = str_replace( '"', '\%22', preg_quote( $val ) );
				//$replace[] = preg_quote(urlencode($val));

			}

			$htaccess_template = str_replace( $search, $replace, $htaccess_template );

			return $htaccess_template;
		}

		protected function get_htaccess_content_without_section( $htaccess_filename, $section_name )
		{
			if( !file_exists( $htaccess_filename ) ) return '';
			$content_without_section = '';
			$file_content = file( $htaccess_filename );
			if($file_content===false) throw new Exception("File '.htaccess' is not readable.");
			$lines = explode( "\n", implode( '', $file_content ));

			$start_marker = $this->get_htaccess_section_start_marker( $section_name );
			$stop_marker = $this->get_htaccess_section_stop_marker( $section_name );

			$result = array();
			$in_section = false;
			foreach ( $lines as $line )
			{
				if( strpos( $line, $start_marker ) !== false)
				{
					$in_section = true;
					continue;
				}
				elseif( strpos( $line, $stop_marker ) !== false)
				{
					$in_section = false;
					continue;
				}
				elseif( !$in_section ) $result[] = $line;
			}

			$content_without_section = trim(implode( "\n", $result ));
			return $content_without_section;
		}

		protected function write_htaccess_section( $htaccess_filename, $section_name, $inserts )
		{
			$start_marker = $this->get_htaccess_section_start_marker( $section_name );
			$stop_marker = $this->get_htaccess_section_stop_marker( $section_name );

			$content = $start_marker."\n".$inserts."\n".$stop_marker."\n\n";
			if( !is_writable($htaccess_filename) || file_put_contents( $htaccess_filename, $content ) === false) throw new Exception('Can\'t write plugin section in .htaccess file.');
		}

		protected function get_htaccess_section_start_marker( $section_name )
		{
			$start_marker = '# BEGIN '.$section_name;
			return $start_marker;
		}

		protected function get_htaccess_section_stop_marker( $section_name )
		{
			$stop_marker = '# END '.$section_name;
			return $stop_marker;
		}

		function htaccess_template()
		{
			$settings = $this->get_settings();

			$base = parse_url( site_url(), PHP_URL_PATH );
			$base = trailingslashit( $base );

			$template = '<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase '.$base;

			$allow_localhost_cond = 'RewriteCond "%{REMOTE_HOST}/%{SERVER_ADDR}" !^(.+)/\1$';

			if ( $settings['deny_xmlrpc'] )
				$template .= '
# Deny xmlrpc
'.$allow_localhost_cond.'
RewriteRule ^xmlrpc.php(/.*)?$ "{$redirect_url}" [R,L,NE]';

			if ( $settings['deny_feeds'] )
				$template .= '
# Deny feeds
'.$allow_localhost_cond.'
RewriteCond %{REQUEST_URI} ^(.*/)?feed(/.*)?$ [OR]
RewriteCond %{QUERY_STRING} ^(.*&)?feed=.+
RewriteRule .* "{$redirect_url}" [R,L,NE]';

			if ( $settings['deny_autodiscover'] )
				$template .= '
# Deny autodiscover
'.$allow_localhost_cond.'
RewriteRule ^autodiscover/autodiscover.xml(/.*)?$ "{$redirect_url}" [R,L,NE]';

			if ( $settings['deny_wpad'] )
				$template .= '
# Deny wpad.dat
'.$allow_localhost_cond.'
RewriteRule ^wpad.dat(/.*)?$ "{$redirect_url}" [R,L,NE]';

			if ( $settings['countries'] )
			{
				$countries = array();
				foreach ( explode( ',', $settings['countries'] ) as $item )
				{
					$item = strtoupper( $item );
					$item = preg_replace('/[^A-Z]+/', '', $item);
					if ( $item )
						$countries[] = $item;
				}
				if ( $countries )
					$countries = implode( '|', $countries );
				else
					$countries = 'nobody';

				$deny = $settings['deny_countries'];

				$template .= '
# '.($deny ? 'Deny' : 'Allow only').' countries
SetEnvIf CF-IPCountry ^('.$countries.') Targeted=1
RewriteCond %{ENV:Targeted} '.($deny  ? '' : '!').'=1
'.$allow_localhost_cond.'
RewriteRule .* "{$redirect_url}" [R,L,NE]';
			}

			$template .= '
# Protect login
'.$allow_localhost_cond.'
RewriteCond %{REQUEST_URI} "^{login_url}"
RewriteCond %{REQUEST_METHOD} POST
RewriteCond %{HTTP_COOKIE} !{cookie_name}={cookie_value} [NC]
RewriteRule ^(.*)$ "{$redirect_url}" [R,L,NE]
</IfModule>';

			return $template;
		}
	}

	ProtectionAgainstDDoS::instance();

	//register_activation_hook( WP_PLUGIN_DIR.'/protection-against-ddos/protection-against-ddos.php', array( $this, 'activate' ) );
	register_deactivation_hook( __FILE__, array('ProtectionAgainstDDoS', 'deactivate' ) );

?>