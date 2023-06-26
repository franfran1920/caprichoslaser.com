<?php

class pisol_edd_bulk_edit_product{
    function __construct(){
        add_filter( 'bulk_actions-edit-product', array($this, 'quickAction') );
        add_action( 'handle_bulk_actions-edit-product',  array($this, 'handleBulkAction') , 10, 3);  
    }

    function quickAction($bulk_actions){
        $bulk_actions['pisol_enable_estimate'] = __( 'Enable estimate', 'pi-edd');
        $bulk_actions['pisol_disable_estimate'] = __( 'Disable estimate', 'pi-edd');
        $bulk_actions['pisol_global_estimate'] = __( 'Follow Global Estimate', 'pi-edd');
       
        return $bulk_actions;
    }

    function handleBulkAction( $redirect_to, $do_action, $product_ids ){
        if($do_action == 'pisol_enable_estimate'){
            foreach($product_ids as $product_id){
                update_post_meta($product_id, 'pisol_edd_disable_estimate', 'no');
            }
        }

        if($do_action == 'pisol_disable_estimate'){
            foreach($product_ids as $product_id){
                update_post_meta($product_id, 'pisol_edd_disable_estimate', 'yes');
            }
        }

        if($do_action == 'pisol_global_estimate'){
            foreach($product_ids as $product_id){
                update_post_meta($product_id, 'pisol_edd_disable_estimate', '');
            }
        }

        return $redirect_to;
    }
}
new pisol_edd_bulk_edit_product();