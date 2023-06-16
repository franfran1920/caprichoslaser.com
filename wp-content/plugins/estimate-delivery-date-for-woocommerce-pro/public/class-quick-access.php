<?php 

class pisol_edd_quick_access{
    
    protected static $instance = null;

    public static function get_instance( ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    protected function __construct(){
        add_action('wc_ajax_get_estimate_dates', [$this, 'get_data']);
        add_shortcode('pi_min_date', [$this, 'min_date']);
        add_shortcode('pi_max_date', [$this, 'max_date']);
        add_shortcode('pi_min_days', [$this, 'min_days']);
        add_shortcode('pi_max_days', [$this, 'max_days']);
        add_shortcode('pi_days', [$this, 'days']);
        add_shortcode('pi_date', [$this, 'date']);
    }

    function min_date($arg){
        $product_id = '';
        if(isset($arg['id'])){
            $product_id = $arg['id'];
        }else{
            global $product;
            if(is_object($product)) $product_id = $product->get_id();
        }

        if(empty($product_id)) return;

        return sprintf('<span class="pi-min-date pi-min-date-%s" data-id="%s"></span>', $product_id, esc_attr($product_id));
    }

    function max_date($arg){
        $product_id = '';
        if(isset($arg['id'])){
            $product_id = $arg['id'];
        }else{
            global $product;
            if(is_object($product)) $product_id = $product->get_id();
        }

        if(empty($product_id)) return;

        return sprintf('<span class="pi-max-date pi-max-date-%s" data-id="%s"></span>', $product_id, esc_attr($product_id));
    }

    function min_days($arg){
        $product_id = '';
        if(isset($arg['id'])){
            $product_id = $arg['id'];
        }else{
            global $product;
            if(is_object($product)) $product_id = $product->get_id();
        }

        if(empty($product_id)) return;

        return sprintf('<span class="pi-min-days pi-min-days-%s" data-id="%s"></span>', $product_id, esc_attr($product_id));
    }

    function max_days($arg){
        $product_id = '';
        if(isset($arg['id'])){
            $product_id = $arg['id'];
        }else{
            global $product;
            if(is_object($product)) $product_id = $product->get_id();
        }

        if(empty($product_id)) return;

        return sprintf('<span class="pi-max-days pi-max-days-%s" data-id="%s"></span>', $product_id, esc_attr($product_id));
    }

    function days($arg){
        $product_id = '';
        if(isset($arg['id'])){
            $product_id = $arg['id'];
        }else{
            global $product;
            if(is_object($product)) $product_id = $product->get_id();
        }

        if(empty($product_id)) return;

        return sprintf('<span class="pi-days pi-days-%s" data-id="%s"></span>', $product_id, esc_attr($product_id));
    }

    function date($arg){
        $product_id = '';
        if(isset($arg['id'])){
            $product_id = $arg['id'];
        }else{
            global $product;
            if(is_object($product)) $product_id = $product->get_id();
        }

        if(empty($product_id)) return;

        return sprintf('<span class="pi-date pi-date-%s" data-id="%s"></span>', $product_id, esc_attr($product_id));
    }

    function getShippingSetting(){
        $method_name = pisol_edd_shipping_methods::getMethodNameForEstimateCalculation();
        $shipping_method_settings = pisol_min_max_holidays::getMinMaxHolidaysValues($method_name);
        $this->shipping_method_settings = $shipping_method_settings;
        return $shipping_method_settings;
    }

    function getSetting(){
        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $settings = $pisol_edd_plugin_settings;
        }else{
            $settings = pisol_edd_plugin_settings::init();
        }
        return $settings;
    }


    function getEstimate($product){
        if(!is_object($product)) return false;

        $estimates = pisol_edd_product::estimates($product, $this->shipping_method_settings);

        return $estimates;
    }

    function get_data(){
        $all_estimates = [];

        $ids = isset($_POST['ids']) ? (array) self::wc_sanitize_recursive( $_POST['ids'] ) : [];

        if(empty($ids) || !is_array($ids)) wp_send_json( ['product_estimates' => $all_estimates ]);


        $this->shipping_method_settings = $this->getShippingSetting();
        $this->settings =  $this->getSetting();
        $ids = array_unique( $ids );
        foreach($ids as $id){
            $product = wc_get_product( $id );
            $estimates = $this->getEstimate($product);
            foreach($estimates as $product_id => $estimate){
                $formated_estimate = pisol_edd_common::estimateForDisplay($estimate, $this->settings['date_format']);

                $formated_estimate = $this->addingDateToArray($formated_estimate);

                if(!in_array($id, array_keys($estimates)) && !isset($all_estimates[$id])){
                    $all_estimates[$id] = $formated_estimate;
                }

                $all_estimates[$product_id] = $formated_estimate;
            }
        }

        wp_send_json( ['product_estimates' => $all_estimates ]);

    }

    function addingDateToArray($estimate){
        if($this->settings['show_best_worst_estimate'] == 'min'){
            $estimate['date'] = isset($estimate['min_date']) ? $estimate['min_date'] : '';
            $estimate['days'] = isset($estimate['min_days']) ? $estimate['min_days'] : '';
        }elseif($this->settings['show_best_worst_estimate'] == 'max'){
            $estimate['date'] = isset($estimate['max_date']) ? $estimate['max_date'] : '';
            $estimate['days'] = isset($estimate['max_days']) ? $estimate['max_days'] : '';
        }
        return $estimate;
    }

    static function wc_sanitize_recursive( $input ) {
        if ( is_array( $input ) ) {
            return array_map( [__CLASS__, 'wc_sanitize_recursive'], $input );
        } else {
            return wc_clean( $input );
        }
    }
}
pisol_edd_quick_access::get_instance();