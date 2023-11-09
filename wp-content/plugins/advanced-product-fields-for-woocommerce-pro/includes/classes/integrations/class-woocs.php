<?php
namespace SW_WAPF_PRO\Includes\Classes\Integrations {

	use SW_WAPF_PRO\Includes\Classes\Cart;

	class Woocs
    {
        public function __construct($mode = 'public') {
	        add_action('woocommerce_before_calculate_totals',   [$this, 'recalculate_pricing'],9);

	        add_filter('wapf/html/pricing_hint/amount',         [$this, 'convert_pricing_hint'], 10, 4);

	        add_filter('wapf/pricing/cart_item_base',           [$this, 'convert_back'],10,3);

	        add_action('wp_footer',                             [$this, 'add_footer_script'], 100);

	        add_filter('wapf/pricing/product',                  [$this, 'set_product_base_price'], 10, 2);

	        add_filter('woocommerce_available_variation',       [$this, 'set_variant_price'], 10, 3);

	        add_action('wp_footer',                             [$this, 'change_currency_info_on_frontend'], 5555);


        }

        public function change_currency_info_on_frontend() {

            echo '<script>';

	        $info = $this->get_current_currency_info();



            if(isset( $info['position'] ) ) {

                $format = '%1$s%2$s';

                switch ($info['position']) {
                    case 'left':
                        $format = '%1$s%2$s';
                        break;
                    case 'right':
                        $format = '%2$s%1$s';
                        break;
                    case 'left_space':
                        $format = '%1$s&nbsp;%2$s';
                        break;
                    case 'right_space':
                        $format = '%2$s&nbsp;%1$s';
                        break;
                }

                echo "wapf_config.display_options.format='" . $format . "';";

            }

            if( isset( $info['symbol'] ) ) {
                echo "wapf_config.display_options.symbol='" . $info['symbol'] . "';";
            }

            if( isset( $info['decimals'] ) ) {
                echo "wapf_config.display_options.decimals=" . $info['decimals'] . ";";
            }

            if( isset( $info['hide_cents'] ) && $info['hide_cents'] ) {
                echo "wapf_config.display_options.decimals=0;";
            }

            if( isset( $info['separators'] ) ) {

                $thousand_sep = ',';
                $decimal_sep = '.';

                switch ( $info['separators'] ) {
                    case '1':  $thousand_sep = '.'; $decimal_sep = ','; break;
                    case '2':  $thousand_sep = ' '; $decimal_sep = '.'; break;
                    case '3':  $thousand_sep = ' '; $decimal_sep = ','; break;
                    case '4':  $thousand_sep = ''; $decimal_sep = '.'; break;
                    case '5':  $thousand_sep = ''; $decimal_sep = ','; break;
                }

                echo "wapf_config.display_options.thousand='" . $thousand_sep . "';";
                echo "wapf_config.display_options.decimal='" . $decimal_sep . "';";
            }

            echo '</script>';
        }

        public function set_variant_price($variant_data,$product,$variations) {

	        if(in_array($product->get_type(),['variable','variable-subscription']) && !$this->is_default_currency() && !$this->price_already_converted() && !$this->product_has_fixed_price($product)) {
		        $rate = $this->get_current_currency_rate();
		        $variant_data['display_price'] = floatval($variant_data['display_price']) * $rate;
	        }

	        return $variant_data;

        }

        public function set_product_base_price($price, $product) {

	        if(in_array($product->get_type(),['simple','subscription']) && !$this->is_default_currency() && !$this->price_already_converted() && !$this->product_has_fixed_price($product)) {
		        $rate = $this->get_current_currency_rate();
		        return $price*$rate;
	        }

	        return $price;
        }

        public function convert_back($price) {
	        if(!$this->is_default_currency() && $this->price_already_converted()) {
	            global $WOOCS;
	            $rate = $this->get_current_currency_rate();

		        return $WOOCS->back_convert($price,$rate,8);
	        }
	        return $price;
        }

        public function add_footer_script() {

        	if($this->is_default_currency())
        		return;

        	?>
	        <script>
		        var wapf_woocs_rate = <?php echo $this->get_current_currency_rate(); ?>;

		        jQuery(document).on('wapf/pricing',function(e,productTotal,optionsTotal,total,$parent){
		            jQuery('.wapf-product-total').html(WAPF.Util.formatMoney(productTotal,window.wapf_config.display_options));
		            jQuery('.wapf-options-total').html(WAPF.Util.formatMoney(optionsTotal*wapf_woocs_rate,window.wapf_config.display_options));
                    jQuery('.wapf-grand-total').html(WAPF.Util.formatMoney(productTotal+(optionsTotal*wapf_woocs_rate),window.wapf_config.display_options));
		        });

		        WAPF.Filter.add('wapf/fx/hint', function(price) {
		            return price*wapf_woocs_rate;
                });
	        </script>
			<?php
        }

        public function convert_pricing_hint($amount, $product, $type, $for_page) {

            if( $type === 'fx' && $for_page !== 'cart' ) return $amount;

	        if(!$this->is_default_currency() && !$this->product_has_fixed_price($product)) {
		        $rate = $this->get_current_currency_rate();
		        return (float) $amount*$rate;
	        }

	        return $amount;
        }

        public function convert_product_price($price, $product){

        	if(!$this->is_default_currency() && !$this->price_already_converted()) {
		        return $this->convert_to_current_currency($price);
	        }

	        return $price;
        }

        public function recalculate_pricing($cart_obj) {

	        foreach( $cart_obj->get_cart() as $key=>$item ) {

		        $cart_item = WC()->cart->cart_contents[$key];

		        if( empty( $cart_item['wapf'] ) ) {
                    continue;
                }

		        $pricing = Cart::calculate_cart_item_prices($cart_item);

		        if($pricing !== false) {
                    WC()->cart->cart_contents[$key]['wapf_item_price'] = $pricing;
                }

	        }
        }

		private function price_already_converted() {
			if(get_option('woocs_is_multiple_allowed') != 1)
				return false;
			return true;
		}

		private function is_default_currency($curr = false) {
			global $WOOCS;
			if(!$curr)
				$curr = $WOOCS->current_currency;

			return strtolower($curr) === strtolower($WOOCS->default_currency);
		}

		private function product_has_fixed_price($product) {

			if(get_option('woocs_is_fixed_enabled') != 1)
				return false;

			global $WOOCS;

			$curr = $WOOCS->current_currency;

			$regular_price = get_post_meta( $product->get_id(), '_woocs_regular_price_' . $curr, true);
			if(!empty($regular_price) && floatval($regular_price) > 0)
				return true;

			$sale_price = get_post_meta( $product->get_id(), '_woocs_sale_price_' . $curr, true);
			if(!empty($sale_price) && floatval($sale_price) > 0 )
				return true;

			return false;
		}

		private function convert_to_current_currency($value) {
			return apply_filters('woocs_exchange_value', $value);
		}

		private function get_current_currency_rate() {
			$info = $this->get_current_currency_info();

			return floatval($info['rate']);
		}

		private function get_current_currency_info() {
			global $WOOCS;
			$currencies = $WOOCS->get_currencies();
			return $currencies[$WOOCS->current_currency];
		}

		private function can_use_geo_pricing() {
			$geo_rules = get_option('woocs_geo_rules', []);
			return !empty($geo_rules) || get_option('woocs_is_geoip_manipulation', 0);
        }


    }
}