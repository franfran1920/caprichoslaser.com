<?php
namespace SW_WAPF_PRO\Includes\Classes\Integrations {

	class Woodmart
	{
		public function __construct() {
			add_action('wp_footer', [$this, 'add_javascript'] );
            add_filter('wapf/add_to_cart_redirect_when_editing', function( $x ) { return false; }, 10, 1);
		}

		public function add_javascript() {
			?>
			<script>
                jQuery(document).on('woodmart-quick-view-displayed',function(){
                    new WAPF.Frontend(jQuery('.product-quick-view'));
                });
			</script>
			<?php
		}

	}
}