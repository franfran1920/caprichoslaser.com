<?php

namespace SW_WAPF_PRO\Includes\Classes {

	class Util {

        public static function pricing_hint_format() {

            static $hint = null;

            if( $hint === null ) {
                $hint = get_option('wapf_hint_format', '');
                if( empty( $hint ) ) $hint = '(+{x})';
            }

            return $hint;

        }

		public static function show_pricing_hints() {

            static $show = null;

            if( $show === null ) {
                $show = get_option('wapf_show_pricing_hints','yes') === 'yes';
            }

			return $show;
		}

        public static function can_edit_in_cart() {

            static $can_edit = null;

            if( $can_edit === null ) {
                $can_edit = get_option( 'wapf_edit_cart', 'no' ) === 'yes';
            }

            return $can_edit;

        }

	}
}