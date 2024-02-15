<?php
namespace SW_WAPF_PRO\Includes\Classes\Integrations {

    use \WC_Aelia_CurrencySwitcher;

    class Aelia
    {

        public function __construct() {

            add_action('wp_footer',                         [$this, 'add_footer_script'], 100);
            add_filter('wapf/html/pricing_hint/amount',     [$this, 'convert_pricing_hint'], 10, 4);
            add_filter('wapf/pricing/cart_item_options',    [$this, 'convert_options_total'], 10, 4 );

        }

        public function convert_options_total( $options_total, $product, $quantity, $cart_item ) {

            $info = $this->get_currency_info();

            if( ! $info['is_default'] ) {
                return (float) $options_total * $info['exchange_rate'];
            }

            return $options_total;

        }

        public function convert_pricing_hint($amount, $product, $type, $for_page) {

            if( $type === 'fx' && $for_page !== 'cart' ) return $amount;

            $info = $this->get_currency_info();
            if( ! $info['is_default'] ) {
                return (float) $amount * $info['exchange_rate'];
            }

            return $amount;
        }

        public function add_footer_script() {

            $info = $this->get_currency_info();

            if( $info['is_default'] ) return;

            ?>
            <script>
                var wapf_aelia_rate = <?php echo $info['exchange_rate']; ?>;

                jQuery(document).on('wapf/pricing',function(e,productTotal,optionsTotal){
                    jQuery('.wapf-product-total').html(WAPF.Util.formatMoney(productTotal,window.wapf_config.display_options));
                    jQuery('.wapf-options-total').html(WAPF.Util.formatMoney(optionsTotal*wapf_aelia_rate,window.wapf_config.display_options));
                    jQuery('.wapf-grand-total').html(WAPF.Util.formatMoney(productTotal+(optionsTotal*wapf_aelia_rate),window.wapf_config.display_options));
                });

                WAPF.Filter.add('wapf/fx/hint', function(price) {
                    return price*wapf_aelia_rate;
                });
            </script>
            <?php

        }

        private function get_currency_info() {

            static $info = null;

            if( $info === null ) {

                $settings = WC_Aelia_CurrencySwitcher::settings();
                $instance = WC_Aelia_CurrencySwitcher::instance();

                $curr = $instance->get_selected_currency();
                $default = $settings->base_currency();
                $exchange = $settings->get_exchange_rate($curr);

                $info = [
                    'current' => $curr,
                    'default' => $default,
                    'is_default' => $curr === $default,
                    'exchange_rate' => $exchange
                ];

            }

            return $info;

        }


    }
}