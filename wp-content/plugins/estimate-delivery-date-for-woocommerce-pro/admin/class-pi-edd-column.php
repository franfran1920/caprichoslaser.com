<?php

class pisol_edd_order_table{
    function __construct(){
        add_filter( 'manage_edit-shop_order_columns', array($this,'deliverPickupDateColumn') );
        add_filter( 'manage_woocommerce_page_wc-orders_columns', array($this,'deliverPickupDateColumn') ); //hpos

        add_action( 'manage_shop_order_posts_custom_column', array($this,'deliverPickupDate') );
        add_action( 'manage_woocommerce_page_wc-orders_custom_column', array($this,'deliverPickupDateHOPS'),10, 2 ); //hpos
    }

    function deliverPickupDateColumn( $columns ) {
        $columns['pisol_estimate_date'] = 'Estimate Date';
        return $columns;
    }

    function deliverPickupDate( $column ) {
        global $post;
        if ( 'pisol_estimate_date' === $column ) {
            $overall_est_min = get_post_meta( $post->ID, 'pi_overall_estimate_min_date',true );
            $overall_est_max = get_post_meta( $post->ID, 'pi_overall_estimate_max_date',true  );
            $estimate = pi_edd_admin_common::formatedDate( $overall_est_min ).' - '.pi_edd_admin_common::formatedDate( $overall_est_max );
            echo $estimate;
        }

        
    }

    function deliverPickupDateHOPS( $column, $order ) {

        if(empty($order)) return;

        if ( 'pisol_estimate_date' === $column ) {
            $overall_est_min = $order->get_meta( 'pi_overall_estimate_min_date',true );
            $overall_est_max = $order->get_meta( 'pi_overall_estimate_max_date',true  );
            $estimate = pi_edd_admin_common::formatedDate( $overall_est_min ).' - '.pi_edd_admin_common::formatedDate( $overall_est_max );
            echo $estimate;
        }

        
    }

}

new pisol_edd_order_table();