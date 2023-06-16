<?php
class pisol_edd_show_shipping_method_name{
    function __construct(){
        add_action('woocommerce_after_shipping_rate', array($this,'getMethodName'),9999,2);
    }

    function getMethodName($method, $index){
        $view_name = get_option('pi_edd_view_shipping_method_system_name', 0);
        if(current_user_can( 'manage_options' ) && !empty($view_name)){
            echo '<small>Method Name: <strong>'.$method->get_id().'</strong></small>';
        }
    }
}

new pisol_edd_show_shipping_method_name();