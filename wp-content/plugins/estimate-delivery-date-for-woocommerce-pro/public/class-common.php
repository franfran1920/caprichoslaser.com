<?php

class pisol_edd_common{

    public static function formatedDate($date, $date_format = "Y/m/d"){
        if(empty($date)) return null;
        
        $original_timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
        $date = str_replace( '/', '-', $date );
        $translated_date = date_i18n($date_format, strtotime($date));
        date_default_timezone_set( $original_timezone );
        
        return $translated_date;
    }

    public static function estimateForDisplay($estimate, $date_format = 'Y/m/d'){
        if(empty($estimate) || !is_array($estimate)) return array();

        $estimate['min_date'] = !empty($estimate['min_date']) && isset($estimate['min_date']) ? self::formatedDate($estimate['min_date'],$date_format) : null;
        $estimate['max_date'] = !empty($estimate['max_date']) && isset($estimate['max_date']) ? self::formatedDate($estimate['max_date'],$date_format) : null;
        
        return $estimate;
    }

    static function isSameDateEstimate($min_date, $max_date, $settings){
        $today = current_time('Y/m/d');

        if(empty($settings['enable_different_msg_for_same_day_estimate'])) return false;

        if(!empty($settings['show_range'])){
            if($min_date == $today && $max_date == $today){
                return true;
            }else{
                return false;
            }
        }else{
            if($settings['show_best_worst_estimate'] == 'max'){
                if($max_date == $today){
                    return true;
                }else{
                    return false;
                }
            }else{
                if($min_date == $today){
                    return true;
                }else{
                    return false;
                }
            }
        }

        return false;
    }

    static function isNextDateEstimate($min_date, $max_date, $settings){
        $today = current_time('Y/m/d');
        $tomorrow = date('Y/m/d', strtotime($today.' +1 day'));

        if(empty($settings['enable_different_msg_for_next_day_estimate'])) return false;

        if(!empty($settings['show_range'])){
            if($min_date == $tomorrow && $max_date == $tomorrow){
                return true;
            }else{
                return false;
            }
        }else{
            if($settings['show_best_worst_estimate'] == 'max'){
                if($max_date == $tomorrow){
                    return true;
                }else{
                    return false;
                }
            }else{
                if($min_date == $tomorrow){
                    return true;
                }else{
                    return false;
                }
            }
        }

        return false;
    }

    static function selectInStockVariation($variations){
        if(empty($variations) || !is_array($variations)) return false;

        foreach($variations as $variation){
            $variation_id = $variation['variation_id'];
            $variation = wc_get_product($variation_id);
            if(!is_object($variation)) continue;

            if($variation->is_in_stock() || $variation->is_on_backorder()){
                return $variation_id;
            }
        }

        return false;

    }

    static function disableEstimateInOrderEmail($order){


        $no_estimate_in_order_with_status = get_option('pi_edd_disable_on_status', array('cancelled','failed', 'refunded'));
        
        if(empty($no_estimate_in_order_with_status )){
            return apply_filters('pisol_edd_hide_estimate_in_order', false, $order);
        } 

        $order_obj = wc_get_order($order);

        if(!is_object($order_obj)){
            return apply_filters('pisol_edd_hide_estimate_in_order', false, $order);
        }

        $order_status = $order_obj->get_status();
    
        if(in_array($order_status, $no_estimate_in_order_with_status)){
            return apply_filters('pisol_edd_hide_estimate_in_order', true, $order);
        }

        return apply_filters('pisol_edd_hide_estimate_in_order', false, $order);
    }
}