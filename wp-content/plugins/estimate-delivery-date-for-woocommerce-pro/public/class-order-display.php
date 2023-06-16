<?php
/**
 * move this outside the wp_loaded event as some payment gateway causes the issue
 */
class pisol_edd_order_meta_email{
    function __construct(){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }

        $this->fields_to_hide = array('pi_item_min_date', 'pi_item_max_date', 'pi_item_min_days', 'pi_item_max_days');

         /** modify how meta key are shown on the order detail page email and stored order */
         if(!empty($this->settings['enable_estimate_globally'])){
            add_filter( 'woocommerce_order_item_get_formatted_meta_data', array($this,'changeItemMetaKeys'), 20, 2 );

            /** support for pdf invoice 
             * https://wordpress.org/plugins/print-invoices-packing-slip-labels-for-woocommerce/
             */
            add_filter('wf_pklist_modify_meta_data', array($this,'webtoffee_modify_meta_data'), 10, 2);
            
            add_filter('wf_alter_line_item_variation_data',array($this,'wf_pklist_add_vertical_variation'),10,4);

            /**
             * https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/
             */
            add_action( 'wpo_wcpdf_after_order_data', array($this,'showOrderEstimate'), 10, 2 );
        }

    }

    function changeItemMetaKeys($meta_data, $item){
        $hide_keys = $this->fields_to_hide;
        $order_id = $item->get_order_id();

        foreach($meta_data as $key => $data){

            if(in_array($data->key, $hide_keys)){
                unset($meta_data[$key]);
            }
            
            if($data->key === 'pi_item_estimate_msg'){
                $meta_data[$key]->display_key = __("&#128652;",'pi-edd');
            }
            
            if((pisol_edd_common::disableEstimateInOrderEmail($order_id) || empty($this->settings['add_each_product_estimate_in_email'])) && $data->key === 'pi_item_estimate_msg'){
                unset( $meta_data[$key] );
            }
            
            
        }

        

        return $meta_data;
    }

    function webtoffee_modify_meta_data($meta, $item){
        $keys = $this->fields_to_hide;

        foreach ($meta as $id => $value) {
            if (in_array($id, $keys) && $id !== 0) {
                if (isset($meta[$id])) {
                    unset($meta[$id]);
                }
            } else {
                $meta[$id] = $value . '<br />';
            }
        }

        return $meta;
    }

    function wf_pklist_add_vertical_variation($current_item, $meta_data, $id, $value){    
        if($id == 'pi_item_estimate_msg'){
            return __("&#128652;",'pi-edd').': '.$value.'<br>';
        }
        return $current_item.'<br />';
    }

    function showOrderEstimate($template_type, $order){
        if(empty($order)) return;

        if(apply_filters('pi_edd_disable_estimate_in_pdf', false)) return;

        if ($template_type == 'packing-slip' || $template_type == 'invoice') {
            
            if(is_object($order)){
                $order_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->id : $order->get_id();
            }else{
                $order_id = $order;
            }
            
            
            $overall_estimate_msg = $order->get_meta( 'pi_overall_estimate', true );
            /** Old way of storing date with string */
            if(!empty($overall_estimate_msg)){
            echo '<tr class="pi-overall-estimate"><td colspan="2"><strong>'.$overall_estimate_msg.'</strong></td></tr>';
            }else{

                /** the above will be used for the old orders that stored the estimate as string
                 * in "pi_overall_estimate"
                 * This loop will be used for the latest implement where we only store estimate date in Y/m/d format and estimate number of days in "pi_overall_estimate_date" and "pi_overall_estimate_days"
                 */
                $estimate = $this->orderEstimate($order_id, $order);
                $msg = pisol_edd_cart_page::getOrderMessage($estimate, $this->settings);
                $msg = str_replace('{icon}',"", $msg);
                $message = pisol_edd_message::msg($estimate, $msg, 0, 'cart','pi-edd-cart'); 

                $allowed_tags = apply_filters('pi_edd_allowed_tags', '<span>');
            
                echo '<tr><td colspan="2"><strong>'.strip_tags($message, $allowed_tags).'</strong></td></tr>';
            
            }
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

new pisol_edd_order_meta_email();