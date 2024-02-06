<?php

class pisol_edd_css{
    function __construct(){
        $this->plugin_name = 'pisol_edd';
        add_action( 'wp_enqueue_scripts', array($this,'register_plugin_styles') );
        
    }

    function register_plugin_styles(){
        wp_enqueue_script('pi-edd-common', plugin_dir_url( __FILE__ ) . 'js/pi-edd-common.js', array( 'jquery'), "4.7.20.11", false );

        if(function_exists('is_cart') && is_cart()){
            wp_enqueue_script('pi-edd-cart', plugin_dir_url( __FILE__ ) . 'js/pi-edd-cart.js', array( 'jquery'), "4.7.20.11", false );
        }
        wp_register_style( "{$this->plugin_name}_dummy-handle", false );
        wp_enqueue_style( "{$this->plugin_name}_dummy-handle" );

        $padding_x = 5;
        $padding_y = 5;

        $css = '
            .pi-edd{
                display:block;
                width:100%;
                text-align:center;
                margin-top:5px;
                margin-bottom:5px;
                font-size:12px;
                border-radius:6px;
            }

            .pi-edd-show{
                display:block;
            }

            .pi-edd-short-code-show{
                display:inline-block;
            }

            .pi-edd-hide{
                display:none;
            }

            .pi-edd span{
                font-weight:bold;
            }

            .pi-edd-product, .pi-edd-ajax{
                background:'.get_option('pi_product_bg_color','#f0947e').';
                color:'.get_option('pi_product_text_color','#ffffff').';
                padding: '.$padding_y.'px '.$padding_x.'px;
                margin-top:1rem;
                margin-bottom:1rem;
                clear:both;
                text-align:'.get_option('pi_product_text_align', 'center').';
            }

            .pi-edd-loop{
                background:'.get_option('pi_loop_bg_color','#f0947e').';
                color:'.get_option('pi_loop_text_color','#ffffff').';
                padding: '.$padding_y.'px '.$padding_x.'px;
                text-align:'.get_option('pi_loop_text_align', 'center').';
            }

            .pi-edd-loop-ajax{
                width:100%;
            }

            .pi-edd.pi-edd-cart{
                background:'.get_option('pi_cart_bg_color','#f0947e').';
                color:'.get_option('pi_cart_text_color','#ffffff').';
                padding: '.$padding_y.'px '.$padding_x.'px;
                text-align:'.get_option('pi_cart_text_align','left').';
                display:block;
                padding:0px 10px;
                width:auto;
            }

            .pi-edd-icon{
                display:inline-block !important;
                margin:0 7px;
                vertical-align:middle;
            }
        ';
        

        wp_add_inline_style( "{$this->plugin_name}_dummy-handle", $css );
    }

}