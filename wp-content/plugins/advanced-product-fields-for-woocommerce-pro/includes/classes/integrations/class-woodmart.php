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
                jQuery(document).on('woodmart-quick-view-displayed',function(){ new WAPF.Frontend(jQuery('.product-quick-view')); });
                document.addEventListener( 'wapf/image_changed', function(e) {
                    if( ! e.detail || !e.detail.image || ! woodmartThemeModule ) return;
                    if ( woodmartThemeModule.initZoom ) { var zoomImg = document.querySelector('.zoomImg'); if (zoomImg) { zoomImg.src = e.detail.image.full_src; if (zoomImg.complete) woodmartThemeModule.initZoom(); else zoomImg.addEventListener('load', woodmartThemeModule.initZoom); } }
                    var thumb = document.querySelector('.product-image-thumbnail img'); if( thumb ) { thumb.src = e.detail.image.full_src; thumb.srcset = ''; }
                });
            </script>
			<?php
		}

	}
}