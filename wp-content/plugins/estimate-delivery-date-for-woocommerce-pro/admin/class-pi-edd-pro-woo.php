<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       piwebsolution.com
 * @since      1.0.0
 *
 * @package    Pi_Edd
 * @subpackage Pi_Edd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pi_Edd
 * @subpackage Pi_Edd/admin
 * @author     PI Websolution <rajeshsingh520@gmail.com>
 */
class Pi_Edd_Pro_Woo {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( ) {
		add_action( 'woocommerce_product_data_tabs', array($this,'productTab') );
		/** Adding order preparation days */
		add_action( 'woocommerce_product_data_panels', array($this,'order_preparation_days') );
		add_action( 'woocommerce_process_product_meta', array($this,'order_preparation_days_save') );

		add_action( 'woocommerce_product_quick_edit_end', array($this, 'quickEditForm'));
		add_action('woocommerce_product_quick_edit_save',array($this, 'quickEditSave'));

		add_filter('manage_product_posts_columns', array($this,'quickEditColumns'));
		add_action('manage_posts_custom_column',  array($this,'quickEditColumnsValue'), 10, 2);

		add_filter( 'default_hidden_columns', array($this, 'hideColumns'),10,2);

	}

	function productTab($tabs){
        $tabs['pisol_edd'] = array(
            'label'    => 'Preparation Time',
            'target'   => 'pisol_edd',
            'priority' => 21,
        );
        return $tabs;
	}
	
	function order_preparation_days() {

		global $post;
		if(is_object($post)){
			$this->error($post->ID);
		}

		$global = get_option('pi_edd_default_global_estimate_status', 'enable') == 'disable' ? __('(Don\'t show estimate)','pi-edd' ) : __('(Show estimate)','pi-edd' );

		echo '<div id="pisol_edd" class="panel woocommerce_options_panel hidden">';
		woocommerce_wp_select( array(
            'label' => __("Disable estimate for this product", 'pi-edd'), 
            'id' => 'pisol_edd_disable_estimate', 
            'name' => 'pisol_edd_disable_estimate', 
			'description' => __("Check this if you don't want to show estimate date for this particular product", 'pi-edd'),
			'desc_tip'=>true,
			'options' => array(
				'' => sprintf(__('Use Global Option %s','pi-edd'), $global),
				'yes'=>__('Disable estimate', 'pi-edd'),
				'no'=>__('Enable estimate', 'pi-edd')
			  )
          ) );
		echo '<div id="pisol-product-preparation-days">';
		
		echo '<div class="show_if_variable">';
		woocommerce_wp_checkbox( array(
			'label' => __("Use Product variation preparation time", 'pi-edd'), 
			'id' => 'pisol_edd_use_variation_preparation_time', 
			'name' => 'pisol_edd_use_variation_preparation_time', 
			'description' => __("You can set different preparation time for each of the variation (use only for variable products, that too if you want to set different preparation time for each variation)", 'pi-edd')
		  ) );
		echo '</div>';

		echo '<div class="pi-edd-product-level-setting">';
		
		echo '<div class="product_preparation_time_main">';
		echo '<strong style="background:#000; color:#fff; padding:5px; display:block;">'.__('Product preparation time','pi-edd').'</strong>';
			$args = array(
			'id' => 'product_preparation_time',
			'label' => __( 'Min Product preparation days', 'pi-edd' ),
			'type' => 'number',
			'custom_attributes' => array(
				'step' 	=> '1',
				'min'	=> '0'
			) ,
			'placeholder'=>'If left blank 0 will be considered',
			'class' => 'form-control',
			'desc_tip' => true,
			'description' => __( 'Enter the Min number of days it take to prepare this product' , 'pi-edd'),
			);
			woocommerce_wp_text_input( $args );

			$args7 = array(
				'id' => 'product_preparation_time_max',
				'label' => __( 'Max Product preparation days', 'pi-edd' ),
				'type' => 'number',
				'custom_attributes' => array(
					'step' 	=> '1',
					'min'	=> '0'
				) ,
				'placeholder'=>'If left blank 0 will be considered',
				'class' => 'form-control',
				'desc_tip' => true,
				'description' => __( 'Enter the Max number of days it take to prepare this product leave empty if you only want min preparation time ' , 'pi-edd'),
				);
				woocommerce_wp_text_input( $args7 );
			echo '<strong style="background:#000; color:#fff; padding:5px; display:block;">'.__('Exact arrival date of product','pi-edd').'</strong>';
			woocommerce_wp_checkbox( array(
				'label' => __("Insert exact product arrival date", 'pi-edd'), 
				'id' => 'pisol_enable_exact_date', 
				'name' => 'pisol_enable_exact_date', 
				'description' => __("If product will be available to you in future at some fixed date then use this option to insert that exact date, preparation time will be added to this ", 'pi-edd'),
				'desc_tip'=>true
			) );	
			echo '<div class="product_availability_date_main">';	
				$args3 = array(
					'id' => 'pisol_exact_availability_date',
					'label' => __( 'Exact Product availability date (Preparation time will be added to this date)', 'pi-edd' ),
					'type' => 'text',
					
					'class' => 'form-control pisol_edd_date_picker',
					'desc_tip' => true,
					'description' => __( 'Select a date when this product will be available with you for dispatch, based on that it will show the estimate date, once you add date in this it plugin will use it for estimate calculation and ignore the above "preparation time" and "out of stock time"' , 'pi-edd'),
					);
				woocommerce_wp_text_input( $args3 );
			echo '</div>';
			echo '<strong style="background:#000; color:#fff; padding:5px; display:block;">'.__('Setting for out of stock product (on Back-order)','pi-edd').'</strong>';
			woocommerce_wp_select( array(
				'label' => __("Extra time as", 'pi-edd'), 
				'id' => 'pisol_edd_extra_time_as', 
				'description' => __("Extra time as single time or as a range of times", 'pi-edd'),
				'desc_tip'=>true,
				'class' => 'pi-extra-time-type',
				'custom_attributes' => array('data-range' => '#pi-range-extra-container', 'data-single' => '#pi-single-extra-container'),
				'options' => array(
					'single'=>__('Single time', 'pi-edd'),
					'range'=>__('Range of time', 'pi-edd')
				  )
			  ) );
			  echo '<div id="pi-range-extra-container">';
			  $min = array(
				'id' => 'out_of_stock_product_preparation_time_min',
				'label' => __( 'Min Extra days', 'pi-edd' ),
				'type' => 'number',
				'custom_attributes' => array(
					'step' 	=> '1',
					'min'	=> '0'
				) ,
				'placeholder'=>'If left blank 0 will be considered',
				'class' => 'form-control',
				'desc_tip' => true,
				'description' => __( 'This will be added in the normal product preparation time when product is out of stock and you are allowing back-order ' , 'pi-edd'),
				);
				woocommerce_wp_text_input( $min );
				$max = array(
					'id' => 'out_of_stock_product_preparation_time_max',
					'label' => __( 'Max Extra days', 'pi-edd' ),
					'type' => 'number',
					'custom_attributes' => array(
						'step' 	=> '1',
						'min'	=> '0'
					) ,
					'placeholder'=>'If left blank 0 will be considered',
					'class' => 'form-control',
					'desc_tip' => true,
					'description' => __( 'This will be added in the normal product preparation time when product is out of stock and you are allowing back-order ' , 'pi-edd'),
					);
					woocommerce_wp_text_input( $max );
			  echo '</div>';
			echo '<div id="pi-single-extra-container">';
				$args2 = array(
					'id' => 'out_of_stock_product_preparation_time',
					'label' => __( 'Extra time', 'pi-edd' ),
					'type' => 'number',
					'custom_attributes' => array(
						'step' 	=> '1',
						'min'	=> '0'
					) ,
					'placeholder'=>'If left blank 0 will be considered',
					'class' => 'form-control',
					'desc_tip' => true,
					'description' => __( 'This will be added in the normal product preparation time when product is out of stock and you are allowing back-order ' , 'pi-edd'),
					);
				woocommerce_wp_text_input( $args2 );
			echo '</div>';
			$exact_product_refill_date = array(
					'id' => 'pisol_exact_lot_arrival_date',
					'label' => __( 'When the next lot of product will arrive (used when product is on back-order) (Preparation time will be added to this date)', 'pi-edd' ),
					'type' => 'text',
					
					'class' => 'form-control pisol_edd_date_picker',
					'desc_tip' => true,
					'description' => __( '<a href="https://www.piwebsolution.com/woocommerce-estimated-delivery-date-per-product/#Out_of_stock_product_Available_on_back_order" target="_blank">click to Read more</a>', 'pi-edd' ),
					);
				woocommerce_wp_text_input( $exact_product_refill_date );
		    echo '</div>';
			
			echo '</div>';
			
			
		echo '</div>';
		echo '</div>';
		wp_nonce_field( 'pi-edd-save-product-nonce', 'pi-edd-save-product-nonce' );
	}

	function order_preparation_days_save( $post_id ) {

		$nonce_value = wc_get_var( $_REQUEST['pi-edd-save-product-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) );
		
		if (!wp_verify_nonce($nonce_value, 'pi-edd-save-product-nonce')){
			return;
		}
		

		$product = wc_get_product( $post_id );
		
		$value = ((isset( $_POST['product_preparation_time'] ) && $_POST['product_preparation_time'] >= 0 && $_POST['product_preparation_time'] != '') ? (int)$_POST['product_preparation_time'] : '');
		$return = $product->update_meta_data( 'product_preparation_time', sanitize_text_field( $value ) );

		$max_value = ((isset( $_POST['product_preparation_time_max'] ) && $_POST['product_preparation_time_max'] >= 0 && $_POST['product_preparation_time_max'] != '') ? (int)$_POST['product_preparation_time_max'] : '');
		$return = $product->update_meta_data( 'product_preparation_time_max', sanitize_text_field( $max_value ) );

		$out_of_stock_product_preparation_time = ((isset( $_POST['out_of_stock_product_preparation_time'] ) && $_POST['out_of_stock_product_preparation_time'] >= 0 && $_POST['out_of_stock_product_preparation_time'] != '') ? (int)$_POST['out_of_stock_product_preparation_time'] : '');

		$product->update_meta_data( 'out_of_stock_product_preparation_time', sanitize_text_field( $out_of_stock_product_preparation_time ) );
		

		$disable_estimate = isset($_POST['pisol_edd_disable_estimate']) ? $_POST['pisol_edd_disable_estimate'] : '';
		$product->update_meta_data( 'pisol_edd_disable_estimate', $disable_estimate );

		$use_variation_days = isset($_POST['pisol_edd_use_variation_preparation_time']) ? 'yes' : 'no';
		$product->update_meta_data( 'pisol_edd_use_variation_preparation_time', $use_variation_days );

		$enable_exact_date = isset($_POST['pisol_enable_exact_date']) ? 'yes' : 'no';
		$product->update_meta_data( 'pisol_enable_exact_date', $enable_exact_date );

		$exact_date = isset($_POST['pisol_exact_availability_date']) && strtotime($_POST['pisol_exact_availability_date']) ? $_POST['pisol_exact_availability_date'] : "";
		$product->update_meta_data( 'pisol_exact_availability_date', $exact_date );
		
		$lot_arrival_date  = isset($_POST['pisol_exact_lot_arrival_date']) && strtotime($_POST['pisol_exact_lot_arrival_date']) ? $_POST['pisol_exact_lot_arrival_date'] : "";
		$product->update_meta_data( 'pisol_exact_lot_arrival_date', $lot_arrival_date );

		$extra_time_type = isset($_POST['pisol_edd_extra_time_as']) ? $_POST['pisol_edd_extra_time_as'] : "single";
		$product->update_meta_data( 'pisol_edd_extra_time_as', $extra_time_type );

		if($extra_time_type == 'range'){
			$this->saveExtraMinMaxRange($product, $post_id);
		}
		
		$product->save();
	}

	function saveExtraMinMaxRange($product, $post_id){

		$out_of_stock_extra_min = isset($_POST['out_of_stock_product_preparation_time_min']) ? $_POST['out_of_stock_product_preparation_time_min'] : '';
		

		$out_of_stock_extra_max = isset($_POST['out_of_stock_product_preparation_time_max']) ? $_POST['out_of_stock_product_preparation_time_max'] : '';
		

		if($out_of_stock_extra_min != $out_of_stock_extra_max && $out_of_stock_extra_min > $out_of_stock_extra_max){
			$error = new WP_Error('error', __('Max extra days should be grater then or equal to the Min extra days','pi-edd'));
			$user_id = get_current_user_id();
			set_transient("pi_edd_time_error_{$post_id}_{$user_id}", $error, 45);
			return false;
		} 

		$product->update_meta_data( 'out_of_stock_product_preparation_time_min',  $out_of_stock_extra_min);

		$product->update_meta_data( 'out_of_stock_product_preparation_time_max',  $out_of_stock_extra_max);
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
	   
	function quickEditForm(){
		$global = get_option('pi_edd_default_global_estimate_status', 'enable') == 'disable' ? __('(Don\'t show estimate)','pi-edd' ) : __('(Show estimate)','pi-edd' );
		$args = array(
			'id' => 'product_preparation_time',
			'label' => __( 'Product preparation time', 'pi-edd' ),
			'type' => 'number',
			'custom_attributes' => array(
				'step' 	=> '1',
				'min'	=> '0'
			) ,
			'placeholder'=>0,
			'class' => 'form-control',
			'desc_tip' => false,
			'description' => __( 'Enter the number of days it take to prepare this product', 'pi-edd' ),
			);
			
		?>
		<div class="inline-edit-col">
			<label class="alignleft">
				<?php 
				woocommerce_wp_text_input( $args ); ?>
			</label>
		</div>
		<?php
	}

	function quickEditSave($product){

			$post_id = $product->get_id();
		
			if ( isset( $_REQUEST['product_preparation_time'] ) ) {
		
				$customFieldDemo = trim(esc_attr( $_REQUEST['product_preparation_time'] ));
		
				// Do sanitation and Validation here
		
				update_post_meta( $post_id, 'product_preparation_time', wc_clean( $customFieldDemo ) );
			}
	}

	function quickEditColumns( $column_array ) {
 
		$column_array['preparation_time'] = 'Preparation Days';
		$column_array['estimate_date'] = 'Estimate date';
	 
		return $column_array;
	}

	function quickEditColumnsValue( $column_name, $id ) {
		switch( $column_name ) :
			case 'preparation_time': 
				$ppt = get_post_meta( $id, 'product_preparation_time', true );
				echo $ppt == "" ? 0 : $ppt;
				echo '<input type="hidden" id="product_preparation_time_post-'.$id.'" value="'.esc_attr($ppt).'"/>';
				$enable = get_post_meta( $id, 'pisol_edd_disable_estimate', true );
				echo '<input type="hidden" id="pisol_edd_disable_estimate_post-'.$id.'" value="'.esc_attr($enable).'"/>';
			break;

			case 'estimate_date': 
				
				$enable = get_post_meta( $id, 'pisol_edd_disable_estimate', true );
				$global = get_option('pi_edd_default_global_estimate_status', 'enable') == 'disable' ? __('Disabled (G)','pi-edd' ) : __('Enabled (G)','pi-edd' );
				if($enable == 'yes'){
					echo __('Disabled','pi-edd');
				}elseif($enable == 'no'){
					echo __('Enabled','pi-edd');
				}elseif(empty($enable)){
					echo '<span title="Based on global setting">'.$global.'</span>';
				}
			break;
			
		endswitch;
	}

	function hideColumns($hidden, $screen){
		if( isset( $screen->id ) && 'edit-product' === $screen->id ){      
			$hidden[] = 'preparation_time';     
			$hidden[] = 'estimate_date';     
		}   
		return $hidden;
	}

}

new Pi_Edd_Pro_Woo();




