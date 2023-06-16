<?php

class pisol_edd_public_interface{
    function __construct(){
        add_action('wp_loaded',array($this, 'wpLoaded'),20); // we have delayed its execution 20 so it can work properly with Yith dynamic pricing plugin
    }

    function wpLoaded(){
            $enabled = Pisol_edd_get_option('pi_edd_enable_estimate',1);
            if(empty($enabled)) return;
            
            if(!is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX )){
                new pisol_edd_product_archive_page();
                new pisol_edd_cart_page();
                new pisol_edd_checkout_page();
                new pisol_edd_method_estimate();
                new pisol_edd_single_product_page();
                new pisol_edd_css();
                new pisol_edd_single_ajax_product_page();
            }

            new pisol_edd_order(); // as this has some part that run on admin side
    }
}

new pisol_edd_public_interface();