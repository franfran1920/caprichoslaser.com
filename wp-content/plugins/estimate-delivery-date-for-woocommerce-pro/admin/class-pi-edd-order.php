<?php

class pisol_edd_backend_order_detail{
   
        protected $post_type = 'shop_order';

        public $screen_ids = ['shop_order', 'woocommerce_page_wc-orders'];
    
        function __construct(){
            add_action( 'add_meta_boxes', array($this,'metaBox') );
            add_action( 'admin_enqueue_scripts',array($this, 'loadScript'));

            /**
             * Save_post even will not work with hpos so we shifted to 
             * woocommerce_update_order event
             */
            //add_action( 'save_post', array($this,'savePostTimeSlot'), 10, 2 );
            add_action( 'woocommerce_update_order', array($this,'savePostTimeSlot'), 10, 2 );
            
            add_filter('pisol_dtt_delivery_type_label_value', array($this, 'nonDeliverableTypeLabelValue'),20,2);
            add_filter('pisol_dtt_order_table_delivery_method', array($this, 'deliveryMethod'),10, 2 );
        }
    
        function metaBox(){
            add_meta_box(
              'pisol_delivery_detail',
              __( 'Order estimate date', 'pi-edd'),
              array($this,'detail'),
              $this->screen_ids
            );
        }
    
        function detail($post){
            $order_id = $post->ID;
            $order = wc_get_order( $order_id );
            if(empty($order)) return;

            $min_estimate_date = $order->get_meta('pi_overall_estimate_min_date', true );
            $max_estimate_date = $order->get_meta( 'pi_overall_estimate_max_date', true );
            $this->error($order_id);
            $min_estimate_date_display = pi_edd_admin_common::formatedDate($min_estimate_date);
            $max_estimate_date_display = pi_edd_admin_common::formatedDate($max_estimate_date);
            wp_nonce_field( 'pisol_edd_edit_estimate_backend', 'pisol_edd_edit_estimate_backend' );
            include 'partials/order.php';

        }

        function error($post_id){
            $user_id = get_current_user_id();
            if ( $error = get_transient( "pi_edd_time_error_{$post_id}_{$user_id}" ) ) { ?>
                <div class="error">
                    <p><?php echo $error->get_error_message(); ?></p>
                </div><?php
            
                delete_transient("pi_edd_time_error_{$post_id}_{$user_id}");
            }
        }
    
        function loadScript(){
            $current_screen = get_current_screen();
            $current_screen_id = $current_screen->id;
            if (in_array( $current_screen_id, $this->screen_ids) || $current_screen_id == 'edit-shop_order') {
                wp_enqueue_script( 'jquery-ui-datepicker' );

                $js = '
                    jQuery(function($){
                        jQuery(".pisol-order-date-picker").datepicker({
                            dateFormat:"yy/mm/dd"
                        });

                    });
                ';
                wp_add_inline_script('jquery-ui-datepicker', $js, 'after');
               
            }
        }
        
        /**
         * $order default has to be set to null as some plugin calls this hook and pass only one variable $post_id
         */
        function savePostTimeSlot( $post_id, $order = null ){
           
          if (!isset($_POST['pisol_edd_edit_estimate_backend']) || !wp_verify_nonce($_POST['pisol_edd_edit_estimate_backend'], 'pisol_edd_edit_estimate_backend')) return;

          if( empty( $order ) ) return;
    
          $min_estimate_date = sanitize_text_field( $_POST['pi_overall_estimate_min_date'] );
          $max_estimate_date = sanitize_text_field( $_POST['pi_overall_estimate_max_date'] );

          if (!empty( $min_estimate_date ) &&  !empty( $max_estimate_date ) && self::validateDate( $min_estimate_date ) && self::validateDate( $max_estimate_date )) {

              if(strtotime($min_estimate_date) > strtotime($max_estimate_date)){
                $error = new WP_Error('error', 'Min estimate date cant be after the Max estimate date');
                $user_id = get_current_user_id();
                set_transient("pi_edd_time_error_{$post_id}_{$user_id}", $error, 45);
                return false;
              } 
              
              $order->update_meta_data( 'pi_overall_estimate_min_date', $min_estimate_date );
              $order->update_meta_data( 'pi_overall_estimate_max_date', $max_estimate_date ); 
              
              $min_estimate_days = $this->daysAwayFromToday( $min_estimate_date );
              $max_estimate_days = $this->daysAwayFromToday( $max_estimate_date );

              $order->update_meta_data( 'pi_overall_estimate_min_days', $min_estimate_days );
              $order->update_meta_data( 'pi_overall_estimate_max_days', $max_estimate_days ); 
          }
          
          return true;
        }

        static function validateDate($date, $format = 'Y/m/d')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }

        function daysAwayFromToday($estimate){
            if(empty($estimate)) return null;
    
            $today = current_time("Y/m/d");
            $datetime1 = date_create($today); 
            $datetime2 = date_create($estimate); 
      
            // Calculates the difference between DateTime objects 
            $interval = date_diff($datetime1, $datetime2);
            return $interval->days;
        }
}
    
new pisol_edd_backend_order_detail();