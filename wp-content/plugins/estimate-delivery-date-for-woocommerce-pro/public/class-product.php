<?php

class pisol_edd_product{
    public $product_id = 0;
    function __construct($product, $shipping_method_settings, $given_variation_ids = array()){
        $this->product = $product;
        $this->product_id = $product->get_id();
        $this->given_variation_ids = $given_variation_ids;
        $this->shipping_method_settings = $shipping_method_settings;
        $this->use_variable_preparation_time  = $this->useVariablePreparationTime();
    } 

    function useVariablePreparationTime(){
        $pisol_edd_use_variation_preparation_time = get_post_meta($this->product_id,'pisol_edd_use_variation_preparation_time',true);
        if($pisol_edd_use_variation_preparation_time == 'yes'){
            return true;
        }
        return false;
    }

    static function estimates($product, $shipping_method_settings, $given_variation_ids = array()){
        if(!is_object($product)) return false;

        $obj = new self($product, $shipping_method_settings, $given_variation_ids);
        return $obj->estimateObj();
    }

    static function singleEstimate($product, $shipping_method_settings){
        if(!is_object($product)) return false;

        $obj = new self($product, $shipping_method_settings);
        $estimates = $obj->estimateObj();
        if($product->is_type('variable')){
            $first_variation = $obj->getFirstVariationId($product);
            return  isset($estimates[$first_variation]) ? $estimates[$first_variation] : false;
        }else{
            return isset($estimates[$obj->product_id]) ? $estimates[$obj->product_id] : false;
        }
    }

    function estimateObj(){
        $estimate = array();
        
            if($this->product->is_type('variable')){

                $estimate = $this->getVariationsEstimate();

            }else{
                if($this->showEstimate($this->product_id)){
                    $estimate[$this->product_id] = $this->simpleEstimate();
                }
            }

       
        return apply_filters('pisol_edd_product_estimate', $estimate, $this->product, $this->shipping_method_settings);
    }

    function getVariationsEstimate(){
        if(!empty($this->given_variation_ids) && is_array($this->given_variation_ids)){
            $variations = $this->given_variation_ids;
        }else{
            $variations = $this->product->get_available_variations();
        }
        $variation_estimates = array();
        foreach((array)$variations as $variation){
            if($this->showEstimate($variation['variation_id'])){
                if(self::variableEstimateEnabled($variation['variation_id'])){
                    $variation_estimates[$variation['variation_id']] = $this->simpleEstimate($variation['variation_id']);
                }
            }
        }
        return $variation_estimates;
    }

    function showEstimate($product_id){
        $product = wc_get_product($product_id);

        if(!is_object($product)) return false;
        
        $disable_estimate = self::disableEstimateForThisProduct($this->product_id);
       
        
		if($disable_estimate == 'yes' || $product->is_virtual() || $product->is_type('external') || !$this->showBackOrderEstimate($product)){
            return false;
        }
        return true;
    }

    static function disableEstimateForThisProduct($product_id){
        $product_estimate_status = Pisol_edd_get_post_meta($product_id,'pisol_edd_disable_estimate');

        if(!empty($product_estimate_status)) return $product_estimate_status;

        $global_estimate_status = Pisol_edd_get_option('pi_edd_default_global_estimate_status','enable');

        if($global_estimate_status == 'enable') return 'no';
        
        if($global_estimate_status == 'disable') return 'yes';

        return 'no';
    }

    function showBackOrderEstimate($product){
        if(!$product->is_in_stock()) return false;

        $estimate_for_backorder = Pisol_edd_get_option('pi_edd_estimate_for_back_order_product',1);

        if($product->is_on_backorder() && empty($estimate_for_backorder)) return false;

        return true;
    }

    function simpleEstimate($variation_id = 0){
        $preparation_time = $this->getProductPreparationTimeMin($variation_id);
        $preparation_time_max = $this->getProductPreparationTimeMax($variation_id);
        $first_date = $this->getExactDateSetForProduct($variation_id);
        
        $estimate = $this->estimateCalculation($preparation_time, $preparation_time_max, $variation_id, $first_date);
        return $this->isInStock($estimate, $variation_id) ;
    }

    function getExactDateSetForProduct( $variation_id = 0 ){
       
        $lot_arrival_date = $this->getLotArrivalDate($variation_id);
        if($lot_arrival_date !== null){
            return $lot_arrival_date;
        }
       
        $exact_available_date = null;

        $current_date = current_time('Y/m/d');
        if($this->use_variable_preparation_time && !empty($variation_id)){
            $use_exact_date = Pisol_edd_get_post_meta($variation_id , 'pisol_enable_exact_date');
            if($use_exact_date == 'yes'){
                $exact_date = Pisol_edd_get_post_meta($variation_id , 'pisol_exact_availability_date');

                if(strtotime($exact_date) && (strtotime($exact_date) >= strtotime($current_date))){
                    $exact_available_date = !empty($exact_date) ? $exact_date : null;
                }else{
                    $exact_available_date = $current_date;
                }
            }
        }else{
            $use_exact_date = Pisol_edd_get_post_meta($this->product_id , 'pisol_enable_exact_date');
            if($use_exact_date == 'yes'){
                $exact_date = Pisol_edd_get_post_meta($this->product_id , 'pisol_exact_availability_date');
                if(strtotime($exact_date) && (strtotime($exact_date) >= strtotime($current_date))){
                    $exact_available_date = !empty($exact_date) ? $exact_date : null;
                }else{
                    $exact_available_date = $current_date;
                }
            }
        }
        return apply_filters('pisol_dtt_shipping_class_based_pisol_exact_availability_date',$exact_available_date, $this->product_id, $variation_id);
    }

    function getLotArrivalDate($variation_id = 0){
        $product_quantity_in_cart = self::getProductQuantityInCart($this->product_id, $variation_id);
        $lot_arrival_date = null;

        
            $current_date = current_time('Y/m/d');
            if(!empty($variation_id)){
                if($this->use_variable_preparation_time){
                    if($this->isOnBackOrder($variation_id, $product_quantity_in_cart)){
                        $lot_arrival_date = Pisol_edd_get_post_meta($variation_id , 'pisol_exact_lot_arrival_date');
                    }
                }else{
                    if($this->isOnBackOrder($variation_id, $product_quantity_in_cart)){
                        $lot_arrival_date = Pisol_edd_get_post_meta($this->product_id , 'pisol_exact_lot_arrival_date');
                    }
                }
            }else{
                if($this->isOnBackOrder($this->product_id, $product_quantity_in_cart)){
                    $lot_arrival_date = Pisol_edd_get_post_meta($this->product_id , 'pisol_exact_lot_arrival_date');
                }
            }

            if(empty($lot_arrival_date)) return null;

            if(strtotime($current_date) > strtotime($lot_arrival_date)) return null;

        return $lot_arrival_date;
    }

    function getProductPreparationTimeMax($variation_id = 0){
        $out_of_stock_preparation_time = 0;
        $product_quantity_in_cart = self::getProductQuantityInCart($this->product_id, $variation_id);
        $is_on_back_order = false;
        if(!empty($variation_id)){

            if($this->use_variable_preparation_time){
                $preparation_time = (int)Pisol_edd_get_post_meta($variation_id, 'pisol_preparation_days_max');

                if($this->isOnBackOrder($variation_id, $product_quantity_in_cart)){
                    $out_of_stock_preparation_time = (int)$this->getOutOfStockExtraPreparationTime( $variation_id, 'max' );
                    $is_on_back_order = true;
                }
            }else{

                $preparation_time = (int)Pisol_edd_get_post_meta($this->product_id, 'product_preparation_time_max');
                if($this->isOnBackOrder($variation_id, $product_quantity_in_cart)){
                    $out_of_stock_preparation_time = (int)$this->getOutOfStockExtraPreparationTime( $variation_id, 'max' );
                    $is_on_back_order = true;
                }

            }

        }else{

            $preparation_time = (int)Pisol_edd_get_post_meta($this->product_id, 'product_preparation_time_max');

            if($this->isOnBackOrder($this->product_id, $product_quantity_in_cart)){
                $out_of_stock_preparation_time = (int)$this->getOutOfStockExtraPreparationTime( $variation_id, 'max' );
                $is_on_back_order = true;
            }
        }

        $preparation_time = (int)apply_filters('pisol_dtt_shipping_class_based_preparation_time_max', $preparation_time, $this->product_id, $variation_id);

        $out_of_stock_preparation_time = (int)apply_filters('pisol_dtt_shipping_class_based_out_of_stock_preparation_time', $out_of_stock_preparation_time, $this->product_id, $variation_id, $is_on_back_order);

        return $preparation_time + $out_of_stock_preparation_time;

    }

    function getProductPreparationTimeMin($variation_id = 0){
        $out_of_stock_preparation_time = 0;
        $product_quantity_in_cart = self::getProductQuantityInCart($this->product_id, $variation_id);
        $is_on_back_order = false;
        if(!empty($variation_id)){

            if($this->use_variable_preparation_time){
                $preparation_time = (int)Pisol_edd_get_post_meta($variation_id, 'pisol_preparation_days');

                if($this->isOnBackOrder($variation_id, $product_quantity_in_cart)){
                    $out_of_stock_preparation_time = (int)$this->getOutOfStockExtraPreparationTime( $variation_id, 'min' );
                    $is_on_back_order = true;
                }
            }else{

                $preparation_time = (int)Pisol_edd_get_post_meta($this->product_id, 'product_preparation_time');
                if($this->isOnBackOrder($variation_id, $product_quantity_in_cart)){
                    $out_of_stock_preparation_time = (int)$this->getOutOfStockExtraPreparationTime( $variation_id, 'min' );
                    $is_on_back_order = true;
                }

            }

        }else{

            $preparation_time = (int)Pisol_edd_get_post_meta($this->product_id, 'product_preparation_time');

            if($this->isOnBackOrder($this->product_id, $product_quantity_in_cart)){
                $out_of_stock_preparation_time = (int)$this->getOutOfStockExtraPreparationTime( $variation_id, 'min' );
                $is_on_back_order = true;
            }
        }

        $preparation_time = (int)apply_filters('pisol_dtt_shipping_class_based_preparation_time', $preparation_time, $this->product_id, $variation_id);

        $out_of_stock_preparation_time = (int)apply_filters('pisol_dtt_shipping_class_based_out_of_stock_preparation_time', $out_of_stock_preparation_time, $this->product_id, $variation_id, $is_on_back_order);

        return $preparation_time + $out_of_stock_preparation_time;
    }

    function getOutOfStockExtraPreparationTime($variation_id = 0, $min_max = ''){
        $out_of_stock_preparation_time = 0;
        $lot_arrival_date = $this->getLotArrivalDate($variation_id);
        if($lot_arrival_date !== null) return 0;

        if($this->use_variable_preparation_time && !empty($variation_id)){
            $type = Pisol_edd_get_post_meta( $variation_id, 'pisol_edd_extra_time_as');
        }else{
            $type = Pisol_edd_get_post_meta( $this->product_id, 'pisol_edd_extra_time_as');
        }

        if($type == 'range'){

            if($this->use_variable_preparation_time && !empty($variation_id)){
                $min = (int)Pisol_edd_get_post_meta($variation_id, 'out_of_stock_product_preparation_time_min');
                $max = (int)Pisol_edd_get_post_meta($variation_id, 'out_of_stock_product_preparation_time_max');
            }else{
                $min = (int)Pisol_edd_get_post_meta($this->product_id, 'out_of_stock_product_preparation_time_min');
                $max = (int)Pisol_edd_get_post_meta($this->product_id, 'out_of_stock_product_preparation_time_max');
            }

            if($min_max == 'min'){
                $out_of_stock_preparation_time = $min;
            }else{
                $out_of_stock_preparation_time = $max;
            }

            if($min > $max){
                $out_of_stock_preparation_time = $min;
            }

        }else{

            if($this->use_variable_preparation_time && !empty($variation_id)){
                $out_of_stock_preparation_time = (int)Pisol_edd_get_post_meta($variation_id, 'out_of_stock_product_preparation_time');
            }else{
                $out_of_stock_preparation_time = (int)Pisol_edd_get_post_meta($this->product_id, 'out_of_stock_product_preparation_time');
            }
        }

        return (int)$out_of_stock_preparation_time;
    }

    static function getProductQuantityInCart( $product_id, $variation_id = 0 ) {
        $key = md5($product_id.'-'.$variation_id.'-quantity-in-cart');
        $running_qty =  pisol_edd_wp_cache_get($key, 'pisol_edd_np_cache');
        if($running_qty === false){
            $running_qty = 0; 
            if(isset(WC()->cart)){
                foreach(WC()->cart->get_cart() as $other_cart_item_keys => $values ) {
                    if ( $product_id == $values['product_id'] && $variation_id == $values['variation_id']) {				
                        $running_qty += (int) $values['quantity'];
                    }
                }
            }
            pisol_edd_wp_cache_set($key, $running_qty, 'pisol_edd_np_cache');
        }
        return $running_qty;
    }

    function isOnBackOrder($product_id, $quantity_in_cart){
        $product = wc_get_product($product_id);
        if(!is_object($product)) return false;
        $stock_qt = $product->get_stock_quantity();
        if($product->is_on_backorder($quantity_in_cart) ){
            return apply_filters('pisol_edd_is_on_backorder', true, $product_id, $quantity_in_cart, $this);
        }
        return apply_filters('pisol_edd_is_on_backorder', false, $product_id, $quantity_in_cart, $this);
    }

    function estimateCalculation($preparation_time, $preparation_time_max, $variation_id,  $first_date = null){
        $this->shipping_method_settings = apply_filters('pisol_edd_filter_shipping_method_settings',$this->shipping_method_settings, $this->product_id, $variation_id);
        $obj = new pisol_calculate_estimates($this->shipping_method_settings, $preparation_time, $preparation_time_max, $first_date);
        return $obj->estimate();
    }

    
    function isInStock($estimate, $variation_id){
        if(empty($variation_id)){
            $product_id = $this->product_id;
            $product = $this->product;
        }else{
            $product_id = $variation_id;
            $product = wc_get_product($product_id);
        }
        
        if(!is_object($product) || !is_array($estimate) || empty($estimate)) return $estimate;

        if($product->is_in_stock()){
            $estimate['in_stock'] = true;
        }else{
            $estimate['in_stock'] = false;
        }

        $product_quantity_in_cart = self::getProductQuantityInCart($this->product_id, $variation_id);

        if($this->isOnBackOrder($product_id, $product_quantity_in_cart)){
            $estimate['is_on_backorder'] = true;
        }else{
            $estimate['is_on_backorder'] = false;
        }

        return $estimate;

    }

    function getFirstVariationId($product){
        $all_variations = $product->get_available_variations();

        $variation_id_in_stock = pisol_edd_common::selectInStockVariation($all_variations);

        return $variation_id_in_stock;
	}

    static function estimateDisabled($product_id){
        $disable_estimate = self::disableEstimateForThisProduct($product_id);
        if($disable_estimate == 'yes') return true;

        return false;
    }

    static function variableEstimateEnabled($product_id){
        $disable_estimate = Pisol_edd_get_post_meta($product_id,'pisol_edd_disable_variation_estimate');
        if($disable_estimate == 'yes') return false;

        return true;
    }
}