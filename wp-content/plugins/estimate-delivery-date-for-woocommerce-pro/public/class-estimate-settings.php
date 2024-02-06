<?php

class pisol_edd_plugin_settings{
    private $settings;
    function __construct(){
        global $pisol_edd_plugin_settings;
        $pisol_edd_plugin_settings  = array(
            'enable_estimate_globally' => Pisol_edd_get_option('pi_edd_enable_estimate',1),
            'default_shipping_zone' => Pisol_edd_get_option('pi_defaul_shipping_zone',0),
            'default_shipping_method' => Pisol_edd_get_option('pi_edd_default_shipping_method',''),
            'show_range' => Pisol_edd_get_option('pi_general_range',0),
            'show_best_worst_estimate' => Pisol_edd_get_option('pi_edd_min_max','max'),
            'date_format' => Pisol_edd_get_option('pi_general_date_format','Y/m/d'),
            'shipping_cutoff_time' => Pisol_edd_get_option('pi_shipping_breakup_time',""),
            'shipping_closed_on_days' => Pisol_edd_get_option('pi_days_of_week',""),
            'shop_closed_on_days' => Pisol_edd_get_option('pi_shop_closed_days',""),

            'shop_estimate_on_product_page' => Pisol_edd_get_option('pi_show_product_page',1),
            'product_page_simple_estimate_msg' => self::getLanguageMessage('pi_product_page_text','Estimated delivery date {date}'),
            'product_page_range_estimate_msg' => self::getLanguageMessage('pi_product_page_text_range','Estimated delivery between {min_date} - {max_date}'),
            'product_page_simple_estimate_msg_back_order' => self::getLanguageMessage('pi_product_page_text_back_order','Estimated delivery date {date}'),
            'product_page_range_estimate_msg_back_order' => self::getLanguageMessage('pi_product_page_text_range_back_order','Estimated delivery between {min_date} - {max_date}'),
            'product_page_msg_position' => Pisol_edd_get_option('pi_product_page_position','woocommerce_before_add_to_cart_button'),

            'shop_estimate_on_product_archive_page' => Pisol_edd_get_option('pi_show_product_loop_page',1),
            'shop_estimate_of_variable_product_on_archive_page' => Pisol_edd_get_option('pi_show_variable_product_estimate_on_loop',0),
            'archive_page_simple_estimate_msg' => self::getLanguageMessage('pi_loop_page_text','Estimated delivery date {date}'),
            'archive_page_range_estimate_msg' => self::getLanguageMessage('pi_loop_page_text_range','Estimated delivery between {min_date} - {max_date}'),
            'archive_page_simple_estimate_msg_back_order' => self::getLanguageMessage('pi_loop_page_text_back_order','Estimated delivery date {date}'),
            'archive_page_range_estimate_msg_back_order' => self::getLanguageMessage('pi_loop_page_text_range_back_order','Estimated delivery between {min_date} - {max_date}'),
            'archive_page_msg_position' => Pisol_edd_get_option('pi_loop_page_position','woocommerce_after_shop_loop_item_title'),

            'show_estimate_for_each_product_in_cart' => Pisol_edd_get_option('pi_show_cart_page',1),
            'show_estimate_for_each_product_in_checkout' => Pisol_edd_get_option('pi_show_checkout_page',1),

            'show_combined_estimate_on_checkout_page' => Pisol_edd_get_option('pi_edd_cart_page_show_overall_estimate',1),
            'show_combined_estimate_on_cart_page' => Pisol_edd_get_option('pi_edd_show_overall_estimate_on_cart_page',1),
            'cart_checkout_simple_estimate_msg' => self::getLanguageMessage('pi_cart_page_text','Estimated delivery date {date}'),
            'cart_checkout_range_estimate_msg' => self::getLanguageMessage('pi_cart_page_text_range','Estimated delivery between {min_date} - {max_date}'),

            'cart_checkout_simple_estimate_msg_back_order' => self::getLanguageMessage('pi_cart_page_text_back_order','Estimated delivery date {date}'),
            'cart_checkout_range_estimate_msg_back_order' => self::getLanguageMessage('pi_cart_page_text_range_back_order','Estimated delivery between {min_date} - {max_date}'),
            'no_estimate_in_order_with_status' => Pisol_edd_get_option('pi_edd_disable_on_status',array('cancelled','failed', 'refunded')),
            'add_each_product_estimate_in_email' => Pisol_edd_get_option('pi_edd_cart_page_show_single_estimate',0),
            'add_each_product_estimate_in_stored_order' => Pisol_edd_get_option('pi_edd_cart_page_show_single_estimate',0),
            'add_combined_estimate_in_email' => Pisol_edd_get_option('pi_edd_show_overall_estimate_in_email',1),
            'show_combined_estimate_on_order_success_page' => Pisol_edd_get_option('pi_edd_show_overall_estimate_in_order_success_page',1),
            'pi_order_estimate_calculation_method' => Pisol_edd_get_option('pi_order_estimate_calculation_method','smallest-larges'),

            'combined_estimate_simple_msg' => self::getLanguageMessage('pi_overall_estimate_text','Order estimated delivery date {date}'),
            'combined_estimate_range_msg' => self::getLanguageMessage('pi_overall_estimate_range_text','Order estimated delivery between {min_date} - {max_date}'),

            'show_estimate_for_each_shipping_method' => Pisol_edd_get_option('pi_edd_show_estimate_on_each_method',0),
            'shipping_method_estimate_range_msg' => self::getLanguageMessage('pi_edd_estimate_message_below_shipping_method_range','Delivery by {min_date} - {max_date}'),
            'shipping_method_estimate_simple_msg' => self::getLanguageMessage('pi_edd_estimate_message_below_shipping_method_single_date','Delivery by {date}'),

            'enable_different_msg_for_same_day_estimate' => Pisol_edd_get_option('pi_edd_enable_special_wording_same_day_delivery',0),
            'enable_different_msg_for_next_day_estimate' => Pisol_edd_get_option('pi_edd_enable_special_wording_tomorrow_delivery',0),

            'msg_for_same_day_delivery' => self::getLanguageMessage('pi_edd_estimate_message_same_day_delivery','Delivery by Today'),
            'msg_for_next_day_delivery' => self::getLanguageMessage('pi_edd_estimate_message_tomorrow_delivery','Delivery by Tomorrow'),

            'holidays' => $this->setHolidays(Pisol_edd_get_option('pi_edd_holidays',"")),
            'shop_holidays' => $this->setHolidays(Pisol_edd_get_option('pi_edd_shop_holidays',"")),

            'product_page_bg_color' => Pisol_edd_get_option('pi_product_bg_color',"#f0947e"),
            'product_page_text_color' => Pisol_edd_get_option('pi_product_text_color',"#fff"),

            'archive_page_bg_color' => Pisol_edd_get_option('pi_loop_bg_color',"#f0947e"),
            'archive_page_text_color' => Pisol_edd_get_option('pi_loop_text_color',"#fff"),

            'cart_page_bg_color' => Pisol_edd_get_option('pi_cart_bg_color',"#f0947e"),
            'cart_page_text_color' => Pisol_edd_get_option('pi_cart_text_color',"#fff"),

            'icon' => Pisol_edd_get_option('pi_edd_icon',""),
            'show_first_variation_estimate' => Pisol_edd_get_option('pi_edd_show_default_estimate_for_variable_product','select-variation-msg'),
            'no_variation_selected_message' => self::getLanguageMessage('pi_no_variation_selected_msg','Select a product variation to get estimate'),
            'out_off_stock_message' => self::getLanguageMessage('pi_product_out_off_stock_estimate_msg','Product is out of stock'),
            'load_single_page_estimate_by_ajax' => Pisol_edd_get_option('pi_show_single_estimate_by_ajax',0),
            'load_load_page_estimate_by_ajax' => Pisol_edd_get_option('pi_show_loop_estimate_by_ajax',0),
            'estimate_for_back_order_product' => Pisol_edd_get_option('pi_edd_estimate_for_back_order_product',1),
            'global_estimate_status' =>  Pisol_edd_get_option('pi_edd_default_global_estimate_status','enable'),
            'enable_block_support' => Pisol_edd_get_option('pi_edd_estimate_block_support', 0)
        );

        $this->settings = $pisol_edd_plugin_settings ;
    }

    static function init(){
        $obj = new self();
        return $obj->settings;
    }

    function setHolidays($holidays){
        if(!empty($holidays)){
            $explode = explode( ":", $holidays );
            return $explode ;
        }
        return array();
    }

    public static function getLanguageMessage($variable, $default=""){
        $lang = apply_filters('pi_edd_language_code_filter', get_locale());
        $messages = get_option('pi_edd_translate_message',array());
        if(is_array($messages)){
            foreach($messages as $message){
                if($message['language'] == $lang && isset($message[$variable]) && $message[$variable] != "" ){
                    return $message[$variable]; 
                }
            }
        }
        return Pisol_edd_get_option($variable, $default);
    }
}

add_action('wp_loaded',function(){
    new pisol_edd_plugin_settings();
});