<?php

class pisol_edd_order_estimate{
    function __construct($products, $shipping_method_settings){
        
        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }

        $this->products = $products;
        $this->shipping_method_settings = $shipping_method_settings;
    }

    static function orderEstimate($products, $shipping_method_settings){
        $obj =  new self($products, $shipping_method_settings);
        return $obj->orderEstimateObj();
    }

    function orderEstimateObj(){
        $product_estimates = $this->getEstimatesForAllProducts();
        $dates =  $this->getEstimateDates($product_estimates);
        return $this->estimate($dates, $product_estimates);
    }

    function estimate($dates, $product_estimates){
        $estimate = array();
        if($this->settings['pi_order_estimate_calculation_method'] == 'first-second-largest'){
            $estimate['max_date'] = $this->getLargestDate($dates);
            $estimate['max_days'] = $this->daysAwayFromToday($estimate['max_date']);
            $estimate['min_date'] = $this->getSecondLargestDate($dates);
            $estimate['min_days'] = $this->daysAwayFromToday($estimate['min_date']);
            
        }elseif($this->settings['pi_order_estimate_calculation_method'] == 'largest-of-product'){

            $max_estimate = $this->getProductWithMaxEstimateDate($product_estimates);
            if($max_estimate !== false){
                $estimate['max_date'] = $max_estimate['max_date'];
                $estimate['max_days'] = $max_estimate['max_days'];
                $estimate['min_date'] = $max_estimate['min_date'];
                $estimate['min_days'] = $max_estimate['min_days'];
            }

        }else{
            $estimate['min_date'] = $this->getSmallestDate($dates);
            $estimate['min_days'] = $this->daysAwayFromToday($estimate['min_date']);
            $estimate['max_date'] = $this->getLargestDate($dates);
            $estimate['max_days'] = $this->daysAwayFromToday($estimate['max_date']);
        }
        return apply_filters('pisol_edd_overall_estimate_dates', $estimate, $dates, $this->products, $this->settings);
    }

    function getEstimateDates($estimates){
        if(empty($this->settings['show_range'])){
            if($this->settings['show_best_worst_estimate'] == 'min'){
                return $this->getEstimates($estimates, 'min');
            }else{
                return $this->getEstimates($estimates, 'max');
            }
        }else{
            return $this->getEstimates($estimates, 'all');
        }
    }

    function getEstimates($estimates, $type= 'all'){
        $dates = array();
        foreach((array)$estimates as $estimate){
            if($type == 'all'){
                if(isset($estimate['min_date']) && !empty($estimate['min_date'])){
                    $dates[] = $estimate['min_date'];
                }
                if(isset($estimate['max_date']) && !empty($estimate['max_date'])){
                    $dates[] = $estimate['max_date'];
                }
            }elseif($type == 'min'){
                if(isset($estimate['min_date']) && !empty($estimate['min_date'])){
                    $dates[] = $estimate['min_date'];
                }
            }elseif($type == 'max'){
                if(isset($estimate['max_date']) && !empty($estimate['max_date'])){
                    $dates[] = $estimate['max_date'];
                }
            }
        }
        return $dates;
    }

    function getEstimatesForAllProducts(){
        $estimates = array();
        foreach($this->products as $product){
            $product_id = $product['product_id'];
            $variation_id = $product['variation_id'];
            $product = wc_get_product($product_id);
            if(!is_object($product)) continue;

            $estimates[] = $this->getProductEstimate($product, $product_id, $variation_id);
        }
        return $estimates;
    }

    function getProductEstimate($product , $product_id, $variation_id){
        if(!is_object($product)) return false;

        $estimates = pisol_edd_product::estimates($product, $this->shipping_method_settings, array(array('variation_id' => $variation_id)));

        if(empty($variation_id)){
            return isset($estimates[$product_id]) ? $estimates[$product_id] : false;
        }else{
            return isset($estimates[$variation_id]) ? $estimates[$variation_id] : false;
        }
    }

    function getLargestDate($date_array){
        $estimates = $date_array;
        $longest = 0;
        $longestDate = "";
        if(is_array($estimates) && !empty($estimates)){
        foreach($estimates as $key => $date){
            if(!$date) continue;

            $curDate = strtotime($date);
            if ($curDate > $longest) {
                $longest = $curDate;
                $longestDate = $date;
            }
        }
        }
        return $longestDate;
    }

    function getSecondLargestDate($date_array){
        $first_largest = $this->getLargestDate($date_array);
        $estimates = $date_array;
        $longest = 0;
        $longestDate = "";
        if(is_array($estimates) && !empty($estimates)){
        foreach($estimates as $key => $date){
            if(!$date) continue;
            if($date == $first_largest) continue;

            $curDate = strtotime($date);
            if ($curDate > $longest) {
                $longest = $curDate;
                $longestDate = $date;
            }
        }
        }
        return empty($longestDate) ? $first_largest : $longestDate;

    }

    function getSmallestDate($date_array){
        $estimates = $date_array;
        
        $smallestDate = "";
        if(is_array($estimates) && !empty($estimates)){
        $smallest = strtotime($estimates[0]);
        $smallestDate = $estimates[0];
        foreach($estimates as $key => $date){
            if(!$date) continue;

            $curDate = strtotime($date);
            if ($curDate < $smallest) {
                $smallest = $curDate;
                $smallestDate = $date;
            }
        }
        }
        return $smallestDate;
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

    function getProductWithMaxEstimateDate($product_estimates){
        if(empty($product_estimates)) return false;
        
        $max_key = 0;
        foreach ($product_estimates as $key => $estimate) {
            if(isset($product_estimates[$max_key]['max_days']) && isset($estimate['max_days']) && $product_estimates[$max_key]['max_days'] < $estimate['max_days']) {
                $max_key = $key;
            }
        }
        return isset($product_estimates[$max_key]) ? $product_estimates[$max_key] : false;
    }

}