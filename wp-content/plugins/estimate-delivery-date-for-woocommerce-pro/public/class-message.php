<?php

class pisol_edd_message{
    function __construct($estimate, $msg, $product_id = 0, $template = 'default', $class = ""){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }

        $this->estimate = $estimate;

        $this->estimate_display = $this->estimateForDisplay($estimate);

        $this->msg = $msg;
        $this->product_id = $product_id;
        $this->template = $template;
        $this->class = $class;
    }

    static function msg($estimate, $msg, $product_id = 0, $template = 'default', $class = ""){
        if(empty($estimate) || !is_array($estimate) || empty($msg)) return apply_filters('pisol_edd_product_estimate_msg', null, $estimate, $msg, $product_id, $template, $class);

        $obj = new self($estimate, $msg, $product_id, $template, $class);
        return apply_filters('pisol_edd_product_estimate_msg', $obj->msgObj(), $estimate, $msg, $product_id, $template, $class);
    }

    function estimateForDisplay($estimate){
        $estimate_for_display = pisol_edd_common::estimateForDisplay($estimate,$this->settings['date_format']);

        if($this->settings['show_best_worst_estimate'] == 'max'){
            $estimate_for_display['date'] = $estimate_for_display['max_date'];
            $estimate_for_display['days'] = $estimate_for_display['max_days'];
        }else{
            $estimate_for_display['date'] = $estimate_for_display['min_date'];
            $estimate_for_display['days'] = $estimate_for_display['min_days'];
        }

        $estimate_for_display['weeks'] = self::daysToWeeks($estimate_for_display['days']);

        $estimate_for_display['min_weeks'] = self::daysToWeeks($estimate_for_display['min_days']);

        $estimate_for_display['max_weeks'] = self::daysToWeeks($estimate_for_display['max_days']);

        $estimate_for_display['icon'] = $this->getIconImg();

        return apply_filters('pisol_edd_msg_short_codes', $estimate_for_display, $estimate);
    }

    

    function msgObj(){

        $msg = $this->msg;

        foreach($this->estimate_display as $key => $val){
            $msg = str_replace("{{$key}}",$val, $msg);
        }

        $html = $this->template($msg);
        return $html;
    }

    function template($msg){
        switch($this->template){
            case 'default':
                $html = $this->defaultTemplate($msg);
            break;

            case 'plain':
                $html = $this->plainTemplate($msg);
            break;

            case 'shortcode':
                $html = $this->shortcodeTemplate($msg);
            break;

            case 'cart':
                $html = $this->cartTemplate($msg);
            break;

            case 'checkout':
                $html = $this->checkoutTemplate($msg);
            break;

            case 'method':
                $html = $this->methodTemplate($msg);
            break;

        }
        return $html;
    }

    function defaultTemplate($msg){
        if(empty($msg)) return; 
        return sprintf('<div class="pi-edd %s" id="pi-estimate-for-%s">%s</div>',esc_attr($this->class), esc_attr($this->product_id), $msg);
    }

    function shortcodeTemplate($msg){
        if(empty($msg)) return; 
        return sprintf('<span class="pi-shortcode %s" id="pi-estimate-for-%s">%s</span>',esc_attr($this->class), esc_attr($this->product_id), $msg);
    }

    function plainTemplate($msg){
        if(empty($msg)) return;
        return $msg;
    }

    static function variationEstimate($estimate, $initial, $no_var_msg, $product_id, $shortcode=false){
        if( $initial == $no_var_msg && $no_var_msg == ""){
            $style = ' style="display:none;" ';
        }else{
            $style = '';
        }

        if($shortcode){

            return sprintf('<span class="pi-shortcode pi-variable-estimate pi-edd-estimate-%4$s" data-estimates="%1$s" data-notselected="%2$s" %5$s >%3$s</span>',htmlspecialchars(json_encode($estimate,JSON_FORCE_OBJECT), ENT_QUOTES, 'UTF-8'),esc_attr( $no_var_msg), $initial, $product_id, $style);
        }else{

            return sprintf('<div class="pi-edd pi-edd-product pi-variable-estimate pi-edd-estimate-%4$s" data-estimates="%1$s" data-notselected="%2$s"  %5$s>%3$s</div>',htmlspecialchars(json_encode($estimate,JSON_FORCE_OBJECT), ENT_QUOTES, 'UTF-8'),esc_attr( $no_var_msg), $initial, $product_id, $style);
        }
    }

    function cartTemplate($msg){
        if(empty($msg)) return; 
        return sprintf('<tr id="pi-overall-estimate-cart"><td colspan="6" class="actions">%s</td></tr>',$msg);
    }

    function checkoutTemplate($msg){
        if(empty($msg)) return; 
        return sprintf('<tr id="pi-overall-estimate-cart"><td colspan="2" class="actions">%s</td></tr>',$msg);
    }

    function methodTemplate($msg){
        if(empty($msg)) return; 
        return sprintf('<div class="pi-edd-method-estimate"><p>%s</p></div>', $msg);
    }

   
    function getIconImg(){
        $icon =  wp_get_attachment_url(get_option("pi_edd_icon",""));
        if($icon){
            return apply_filters( 'pisol_edd_icon_image_tag', sprintf('<img class="pi-edd-icon" src="%s" alt="%s">',esc_url($icon), __('Estimate date','pi-edd')), $icon);
        }
       return  apply_filters( 'pisol_edd_icon_image_tag', sprintf('<img class="pi-edd-icon" src="%s" alt="%s">',esc_url(plugin_dir_url( __FILE__ ).'/img/shipping.svg'), __('Estimate date','pi-edd')), esc_url(plugin_dir_url( __FILE__ ).'/img/shipping.svg'));
    }

    static function daysToWeeks($days){
        if(empty($days)) return;
        
		$weeks = intval($days / 7);
		$days = $days % 7;
		$msg = '';
		if($weeks)
		{
			$msg = sprintf(_n("%d week","%d weeks", $weeks,'pi-edd'), $weeks);
		}
		if($days)
		{
			if($weeks)
			{
				$msg .= __(" and ", 'pi-edd');
			}
			$msg .= sprintf(_n("%d day","%d days", $days, 'pi-edd'), $days);
		}
		return $msg;
	}
}