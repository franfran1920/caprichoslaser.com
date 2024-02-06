<?php

class pisol_edd_shipping_class_based_estimate{
    function __construct(){
        add_filter('pisol_dtt_shipping_class_based_preparation_time', array($this,'preparationDays'), 10, 3);
        add_filter('pisol_dtt_shipping_class_based_preparation_time_max', array($this,'maxPreparationDays'), 10, 3);
        
        add_filter('pisol_dtt_shipping_class_based_out_of_stock_preparation_time', array($this,'outOfStockDays'), 10, 4);

        add_filter('pisol_dtt_shipping_class_based_pisol_exact_availability_date', array($this,'exactProductArrivalDate'), 10, 3);
    }

    function preparationDays($preparation_time, $product_id, $variation_id){
        if(empty($variation_id)){
            $product = wc_get_product($product_id);
        }else{
            $product = wc_get_product($variation_id);
        }

        $class_id = $product->get_shipping_class_id();

        if(empty($class_id)) return $preparation_time;

        $class_key_name = 'pisol_edd_shipping_class_'.$class_id;
        $values = Pisol_edd_get_option($class_key_name, array());

        if(empty($values)){
            return $preparation_time;
        }else{
            $set_time_from_class = self::zoneValue('pisol_edd_set_time_from_class', 0, $values);
            if(empty($set_time_from_class)){
                return $preparation_time;
            }else{
                $preparation_time = self::zoneValue('product_preparation_time', 0, $values);
                return $preparation_time;
            }
        }

        return $preparation_time;
    }

    function maxPreparationDays($preparation_time, $product_id, $variation_id){
        if(empty($variation_id)){
            $product = wc_get_product($product_id);
        }else{
            $product = wc_get_product($variation_id);
        }

        $class_id = $product->get_shipping_class_id();

        if(empty($class_id)) return $preparation_time;

        $class_key_name = 'pisol_edd_shipping_class_'.$class_id;
        $values = Pisol_edd_get_option($class_key_name, array());

        if(empty($values)){
            return $preparation_time;
        }else{
            $set_time_from_class = self::zoneValue('pisol_edd_set_time_from_class', 0, $values);
            if(empty($set_time_from_class)){
                return $preparation_time;
            }else{
                $preparation_time = self::zoneValue('product_preparation_time_max', 0, $values);
                return $preparation_time;
            }
        }

        return $preparation_time;
    }

    function outOfStockDays($preparation_time, $product_id, $variation_id, $is_on_back_order){
        if(!$is_on_back_order) return $preparation_time;
        
        if(empty($variation_id)){
            $product = wc_get_product($product_id);
        }else{
            $product = wc_get_product($variation_id);
        }

        $class_id = $product->get_shipping_class_id();

        if(empty($class_id)) return $preparation_time;

        $class_key_name = 'pisol_edd_shipping_class_'.$class_id;
        $values = Pisol_edd_get_option($class_key_name, array());

        if(empty($values)){
            return $preparation_time;
        }else{
            $set_time_from_class = self::zoneValue('pisol_edd_set_time_from_class', 0, $values);
            if(empty($set_time_from_class)){
                return $preparation_time;
            }else{
                $preparation_time = self::zoneValue('out_of_stock_product_preparation_time', 0, $values);
                return $preparation_time;
            }
        }

        return $preparation_time;
    }

    static function zoneValue($variable, $default, $zone_values){
        if(isset($zone_values[$variable])) return $zone_values[$variable];
    
        return $default;
    }

    function exactProductArrivalDate($exact_date, $product_id, $variation_id){
        if(empty($variation_id)){
            $product = wc_get_product($product_id);
        }else{
            $product = wc_get_product($variation_id);
        }

        $class_id = $product->get_shipping_class_id();

        if(empty($class_id)) return $exact_date;

        $class_key_name = 'pisol_edd_shipping_class_'.$class_id;
        $values = Pisol_edd_get_option($class_key_name, array());

        if(empty($values)){
            return $exact_date;
        }else{
            $set_time_from_class = self::zoneValue('pisol_edd_set_time_from_class', 0, $values);
            if(empty($set_time_from_class)){
                return $exact_date;
            }else{
                
                $pisol_exact_availability_date = self::zoneValue('pisol_exact_availability_date', '', $values);
                return !empty($pisol_exact_availability_date) ? $pisol_exact_availability_date : null;
            }
        }

        return $exact_date;
    }
}

new pisol_edd_shipping_class_based_estimate();