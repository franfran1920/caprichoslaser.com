<?php

class pisol_edd_order_success_email_edit{
    function __construct(){
        
        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }

        add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this,'showOrderEstimate'), 10, 1 );

        add_action('wp_loaded', array($this, 'afterLoading'));


        if(!empty($this->settings['add_combined_estimate_in_email'])){
            add_action( "woocommerce_email_after_order_table", array($this,"showOrderEstimate"), 10, 1);
        }

    }

    function afterLoading(){
        if(!empty($this->settings['show_combined_estimate_on_order_success_page'])){
            //include the custom order meta to woocommerce mail
            $thankyou_page_position = apply_filters('pi_edd_thankyou_page_position', 'woocommerce_order_details_after_order_table_items');
            add_action( $thankyou_page_position, array($this,"showOrderEstimate"), 10, 1);
        }
    }

    function showOrderEstimate($order){
        if(empty($order)) return;
        
        if(is_object($order)){
            $order_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->id : $order->get_id();
        }else{
            $order_id = $order;
        }


        if(pisol_edd_common::disableEstimateInOrderEmail($order_id)) return;
        
        
        $overall_estimate_msg = $order->get_meta( 'pi_overall_estimate', true );
        /** Old way of storing date with string */
        if(!empty($overall_estimate_msg)){
        echo '<p class="pi-overall-estimate"><strong>'.$overall_estimate_msg.'</strong></p>';
        }else{

            /** the above will be used for the old orders that stored the estimate as string
             * in "pi_overall_estimate"
             * This loop will be used for the latest implement where we only store estimate date in Y/m/d format and estimate number of days in "pi_overall_estimate_date" and "pi_overall_estimate_days"
             */
            $estimate = $this->orderEstimate($order_id, $order);
            $msg = pisol_edd_cart_page::getOrderMessage($estimate, $this->settings);
            $msg = apply_filters('pisol_edd_order_estimate_msg_email', $msg, $order);
            $msg = str_replace('{icon}',"", $msg);
            $message = pisol_edd_message::msg($estimate, $msg, 0, 'cart','pi-edd-cart'); 

            $allowed_tags = apply_filters('pi_edd_allowed_tags', '<span>');
           
            echo '<p class="pi-overall-estimate"><strong>'.strip_tags($message, $allowed_tags).'</strong></p>';
           
        }
        
    }

    function orderEstimate($order_id, $order){
        $estimate = [];
        $estimate['min_date'] = $order->get_meta( 'pi_overall_estimate_min_date', true);
        $estimate['min_days'] = $order->get_meta( 'pi_overall_estimate_min_days', true);
        $estimate['max_date'] = $order->get_meta( 'pi_overall_estimate_max_date', true);
        $estimate['max_days'] = $order->get_meta( 'pi_overall_estimate_max_days', true);
        return $estimate;
    }
}

new pisol_edd_order_success_email_edit();