<?php

class pisol_edd_bulk_edit_options{
    function __construct(){
        add_action( 'woocommerce_product_bulk_edit_end', array($this, 'form') );
        add_action( 'woocommerce_product_bulk_edit_save', array($this, 'save') );
    }

    function form(){
        include_once 'partials/bulk-edit.php';
    }

    function save( $product ) {
        $post_id = $product->get_id();    
        if ( isset( $_REQUEST['product_preparation_time'] ) &&  $_REQUEST['product_preparation_time'] !== '') {
            $min_time = $_REQUEST['product_preparation_time'];
            update_post_meta( $post_id, 'product_preparation_time', wc_clean( $min_time ) );
        }

        if ( isset( $_REQUEST['product_preparation_time_max'] ) && $_REQUEST['product_preparation_time_max'] !== '') {
            $max_time = $_REQUEST['product_preparation_time_max'];
            update_post_meta( $post_id, 'product_preparation_time_max', wc_clean( $max_time ) );
        }

        if( isset($_REQUEST['set_back_order_days_bulk']) ){

            $time_as = sanitize_text_field( $_REQUEST['pisol_edd_extra_time_as'] );

            if($time_as == 'single'){
                update_post_meta( $post_id, 'pisol_edd_extra_time_as', 'single' );

                $out_of_stock_product_preparation_time = isset( $_REQUEST['out_of_stock_product_preparation_time'] ) ? ($_REQUEST['out_of_stock_product_preparation_time']) : '';

                update_post_meta( $post_id, 'out_of_stock_product_preparation_time', sanitize_text_field($out_of_stock_product_preparation_time) );


            }elseif($time_as == 'range'){
                update_post_meta( $post_id, 'pisol_edd_extra_time_as', 'range' );

                $out_of_stock_extra_min = isset($_REQUEST['out_of_stock_product_preparation_time_min']) ? $_REQUEST['out_of_stock_product_preparation_time_min'] : '';

                update_post_meta( $post_id, 'out_of_stock_product_preparation_time_min', sanitize_text_field($out_of_stock_extra_min) );
			

			    $out_of_stock_extra_max = isset($_REQUEST['out_of_stock_product_preparation_time_max']) ? $_REQUEST['out_of_stock_product_preparation_time_max'] : '';

                update_post_meta( $post_id, 'out_of_stock_product_preparation_time_max', sanitize_text_field($out_of_stock_extra_max) );
            }
        }
    }
}
new pisol_edd_bulk_edit_options();