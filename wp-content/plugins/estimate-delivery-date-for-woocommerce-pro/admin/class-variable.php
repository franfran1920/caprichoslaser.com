<?php

class pisol_variable_product{
    function __construct(){
        // 1. Add custom field input @ Product Data > Variations > Single Variation
        add_action( 'woocommerce_variation_options_pricing', array($this,'add_custom_field_to_variations'), 10, 3 );

        // 2. Save custom field on product variation save
        add_action( 'woocommerce_save_product_variation', array($this,'save_custom_field_variations'), 10, 2 );

        // 3. Store custom field value into variation data
        add_filter( 'woocommerce_available_variation', array($this,'add_custom_field_variation_data') );
    }

    function add_custom_field_to_variations( $loop, $variation_data, $variation ) {
        
        echo '<div class="pi-edd-setting-variable" style="padding:10px; border:1px solid #ccc; float:left; width:100%; margin-bottom:20px;">';
        $this->error( $variation->ID );
        woocommerce_wp_checkbox( array(
            'label' => __(" Disable estimate for this variation", 'pi-edd'), 
            'id' => 'pisol_edd_disable_variation_estimate['.$loop.']', 
            'name' => 'pisol_edd_disable_variation_estimate['.$loop.']', 
            'class'=> 'checkbox pisol_edd_disable_estimate_for_variation',
            'value' => get_post_meta( $variation->ID, 'pisol_edd_disable_variation_estimate', true ),
          ) );
        echo '<div class="pisol-edd-variation-setting-container">';
        echo '<div style="background:#000; color:#fff; padding:5px;">'.__('Product preparation time', 'pi-edd').'</div>';
        echo '<div class="product_preparation_time_'.$loop.'">';
        woocommerce_wp_text_input( array(
            'id' => 'pisol_preparation_days[' . $loop . ']',
            'class' => 'short',
            'type' => 'number',
            'placeholder'=>'If left blank 0 will be considered',
            'label' => __( 'Min product preparation days',  'pi-edd' ),
            'value' => get_post_meta( $variation->ID, 'pisol_preparation_days', true )
            )
        );

        woocommerce_wp_text_input( array(
            'id' => 'pisol_preparation_days_max[' . $loop . ']',
            'class' => 'short',
            'type' => 'number',
            'placeholder'=>'If left blank 0 will be considered',
            'label' => __( 'Max product preparation days',  'pi-edd' ),
            'value' => get_post_meta( $variation->ID, 'pisol_preparation_days_max', true )
            )
        );

        echo '<strong style="background:#000; color:#fff; padding:5px; display:block;">'.__('Exact arrival date of product','pisol-edd').'</strong>';
        woocommerce_wp_checkbox( array(
			'label' => __("Insert exact product arrival date", 'pi-edd'), 
			'id' => 'pisol_enable_exact_date[' . $loop . ']', 
            'name' => 'pisol_enable_exact_date[' . $loop . ']', 
            'class'=> 'checkbox pisol_variation_extra_date_enabler pisol_enable_exact_date_'.$loop,
            'value' => get_post_meta( $variation->ID, 'pisol_enable_exact_date', true ),
            'desc_tip' => true,
			'description' => __("If product will be available to you in future at some fixed date then use this option to insert that exact date, preparation time will be added to this ", 'pi-edd')
        ) );
        
        echo '<div class="product_availability_date_'.$loop.'">';	
				$args3 = array(
					'id' => 'pisol_exact_availability_date[' . $loop . ']',
					'label' => __( 'Exact Product availability date (Preparation time will be added to this date)', 'pi-edd' ),
					'type' => 'text',
					'value' => get_post_meta( $variation->ID, 'pisol_exact_availability_date', true ),
					'class' => 'form-control pisol_edd_date_picker',
					'desc_tip' => true,
					'description' => __( 'Select a date when this product will be available with you for dispatch, based on that it will show the estimate date, once you add date in this it plugin will use it for estimate calculation and ignore the above "preparation time" and "out of stock time"' , 'pi-edd'),
					);
				woocommerce_wp_text_input( $args3 );
            echo '</div>';
        echo '<strong style="background:#000; color:#fff; padding:5px; display:block;">'.__('Setting for out of stock product (on Back-order)','pisol-edd').'</strong>';

        woocommerce_wp_select( array(
            'label' => __("Extra time as", 'pi-edd'), 
            'id' => 'pisol_edd_extra_time_as[' . $loop . ']', 
            'description' => __("Extra time as single time or as a range of times", 'pi-edd'),
            'desc_tip'=>true,
            'class' => 'pi-extra-time-type',
            'value' => get_post_meta( $variation->ID, 'pisol_edd_extra_time_as', true ),
            'custom_attributes' => array('data-range' => '#pi-range-extra-container_'.$loop, 'data-single' => '#pi-single-extra-container_'.$loop),
            'options' => array(
                'single'=>__('Single time', 'pi-edd'),
                'range'=>__('Range of time', 'pi-edd')
              )
          ) );
          echo '<div id="pi-range-extra-container_'.$loop.'">';
          $min = array(
            'id' => 'out_of_stock_product_preparation_time_min[' . $loop . ']',
            'label' => __( 'Min Extra time', 'pi-edd' ),
            'type' => 'number',
            'custom_attributes' => array(
                'step' 	=> '1',
                'min'	=> '0'
            ) ,
            'placeholder'=>'If left blank 0 will be considered',
            'value' => get_post_meta( $variation->ID, 'out_of_stock_product_preparation_time_min', true ),
            'class' => 'form-control',
            'desc_tip' => true,
            'description' => __( 'This will be added in the normal product preparation time when product is out of stock and you are allowing back-order ' , 'pi-edd'),
            );
            woocommerce_wp_text_input( $min );
            $max = array(
                'id' => 'out_of_stock_product_preparation_time_max[' . $loop . ']',
                'label' => __( 'Max Extra time', 'pi-edd' ),
                'type' => 'number',
                'custom_attributes' => array(
                    'step' 	=> '1',
                    'min'	=> '0'
                ) ,
                'placeholder'=>'If left blank 0 will be considered',
                'value' => get_post_meta( $variation->ID, 'out_of_stock_product_preparation_time_max', true ),
                'class' => 'form-control',
                'desc_tip' => true,
                'description' => __( 'This will be added in the normal product preparation time when product is out of stock and you are allowing back-order ' , 'pi-edd'),
                );
                woocommerce_wp_text_input( $max );
          echo '</div>';
        echo '<div id="pi-single-extra-container_'.$loop.'">';
        woocommerce_wp_text_input( array(
            'id' => 'out_of_stock_product_preparation_time[' . $loop . ']',
            'class' => 'short',
            'type' => 'number',
            'label' => __( 'Extra time', 'pi-edd' ),
            'placeholder'=>'If left blank 0 will be considered',
            'value' => get_post_meta( $variation->ID, 'out_of_stock_product_preparation_time', true ),
            'desc_tip' => true,
				'description' => __( 'This will be added in the normal product preparation time when product is out of stock and you are allowing back-order ', 'pi-edd' ),
            )
            );
        echo '</div>';

            $exact_product_refill_date = array(
                'id' => 'pisol_exact_lot_arrival_date[' . $loop . ']',
                'label' => __( 'When the next lot of product will arrive (used when product is on back-order) (Preparation time will be added to this date)', 'pi-edd' ),
                'type' => 'text',
                'value' => get_post_meta( $variation->ID, 'pisol_exact_lot_arrival_date', true ),
                'class' => 'form-control pisol_edd_date_picker',
                'desc_tip' => false,
                'description' => __( '<a href="https://www.piwebsolution.com/woocommerce-estimated-delivery-date-per-product/#Out_of_stock_product_Available_on_back_order" target="_blank">Read more</a>', 'pi-edd' ),
                );
            woocommerce_wp_text_input( $exact_product_refill_date );
        echo '</div>';
        
            echo '</div>';
            echo '</div>';
    }

    function save_custom_field_variations( $variation_id, $i ) {
        $pisol_preparation_days = $_POST['pisol_preparation_days'][$i];
        if ( isset( $pisol_preparation_days ) ){
            update_post_meta( $variation_id, 'pisol_preparation_days', sanitize_text_field( $pisol_preparation_days ) );
        } else{
            update_post_meta( $variation_id, 'pisol_preparation_days', 0 );
        }
        
        $pisol_preparation_days_max = $_POST['pisol_preparation_days_max'][$i];
        if ( isset( $pisol_preparation_days_max ) ){
            update_post_meta( $variation_id, 'pisol_preparation_days_max', sanitize_text_field( $pisol_preparation_days_max ) );
        } else{
            update_post_meta( $variation_id, 'pisol_preparation_days_max', 0 );
        }

        $out_of_stock_product_preparation_time = $_POST['out_of_stock_product_preparation_time'][$i];
        if ( isset( $out_of_stock_product_preparation_time ) ){
            update_post_meta( $variation_id, 'out_of_stock_product_preparation_time', sanitize_text_field( $out_of_stock_product_preparation_time ) );
        }else{
            update_post_meta( $variation_id, 'out_of_stock_product_preparation_time',0 );
        }

        $enable_exact_date = isset($_POST['pisol_enable_exact_date'][$i]) ? 'yes' : 'no';
        update_post_meta($variation_id, 'pisol_enable_exact_date', $enable_exact_date );
        
        $pisol_edd_disable_variation_estimate = isset($_POST['pisol_edd_disable_variation_estimate'][$i]) ? 'yes' : 'no';
        update_post_meta($variation_id, 'pisol_edd_disable_variation_estimate', $pisol_edd_disable_variation_estimate );

		$exact_date = isset($_POST['pisol_exact_availability_date'][$i]) && strtotime($_POST['pisol_exact_availability_date'][$i]) ? $_POST['pisol_exact_availability_date'][$i] : "";
        update_post_meta($variation_id, 'pisol_exact_availability_date', $exact_date );
        
        $lot_arrival_date  = isset($_POST['pisol_exact_lot_arrival_date'][$i]) && strtotime($_POST['pisol_exact_lot_arrival_date'][$i]) ? $_POST['pisol_exact_lot_arrival_date'][$i] : "";
        update_post_meta($variation_id, 'pisol_exact_lot_arrival_date', $lot_arrival_date );

        $extra_time_type = isset($_POST['pisol_edd_extra_time_as'][$i]) ? $_POST['pisol_edd_extra_time_as'][$i] : "single";
	    update_post_meta($variation_id,  'pisol_edd_extra_time_as', $extra_time_type );

        if($extra_time_type == 'range'){
            $this->saveExtraMinMaxRange($variation_id, $i);
        }
       
    }

    function saveExtraMinMaxRange($variation_id, $i){

        $out_of_stock_extra_min = isset($_POST['out_of_stock_product_preparation_time_min'][$i]) ? $_POST['out_of_stock_product_preparation_time_min'][$i] : '';

        $out_of_stock_extra_max = isset($_POST['out_of_stock_product_preparation_time_max'][$i]) ? $_POST['out_of_stock_product_preparation_time_max'][$i] : '';

        if($out_of_stock_extra_min != $out_of_stock_extra_max && $out_of_stock_extra_min > $out_of_stock_extra_max){
            $error = new WP_Error('error', __('Max extra days should be grater then or equal to the Min extra days','pi-edd'));
            $user_id = get_current_user_id();
            set_transient("pi_edd_time_error_{$variation_id}_{$user_id}", $error, 45);
            return false;
        } 
        
        update_post_meta( $variation_id, 'out_of_stock_product_preparation_time_min', esc_attr( $out_of_stock_extra_min ) );
        
        update_post_meta( $variation_id, 'out_of_stock_product_preparation_time_max', esc_attr( $out_of_stock_extra_max ) );
        
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

    function add_custom_field_variation_data( $variations ) {
        $variations['pisol_preparation_days'] = '<div class="woocommerce_pisol_preparation_days">'.__('Preparation days:', 'pi-edd'). '<span>' . get_post_meta( $variations[ 'variation_id' ], 'pisol_preparation_days', true ) . '</span></div>';
        return $variations;
    }

    static function  getVariations($product_id){
        $product = wc_get_product($product_id);
        $variations = array();
        if($product->is_type( 'variable' )){
            $variations = $product->get_available_variations();
        }
        return $variations;
    }

    static function getVariationPreparationTime($variation_id){
        $preparation_time = get_post_meta( $variation_id, 'pisol_preparation_days', true );
        if($preparation_time == ""){
            return 0;
        }
        return (int)$preparation_time;
    }
}

new pisol_variable_product();