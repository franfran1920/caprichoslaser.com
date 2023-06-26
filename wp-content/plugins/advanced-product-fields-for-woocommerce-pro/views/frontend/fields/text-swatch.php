<?php
/** @var array $model */

use SW_WAPF_PRO\Includes\Classes\Enumerable;
use SW_WAPF_PRO\Includes\Classes\Html;

if(!empty($model['field']->options['choices'])) {

	echo '<div class="wapf-swatch-wrapper" data-is-required="'. $model['field']->required .'">';
	echo '<input type="hidden" class="wapf-tf-h" data-fid="'.$model['field']->id.'" value="0" name="wapf[field_'.$model['field']->id.']" />';
	foreach ($model['field']->options['choices'] as $option) {

		$attributes = Html::option_attributes('radio',$model['product'],$model['field'],$option, false);
		$wrapper_classes = Html::option_wrapper_classes($option, $model['field'], $model['product'], $model['default'] );
		if( in_array( 'wapf-checked', $wrapper_classes ) ) {
			$attributes['checked'] = '';
		}

		echo sprintf(
			'<div class="wapf-swatch wapf-swatch--text %s">%s<input autocomplete="off" %s /></div>',
			join( ' ', $wrapper_classes),
			wp_kses($option['label'],\SW_WAPF_PRO\Includes\Classes\Field_Groups::$allowed_html_minimal) . ' ' . Html::frontend_option_pricing_hint( $option, $model['field'], $model['product'] ),
			Enumerable::from($attributes)->join(function($value,$key) {
				if($value)
					return $key . '="' . esc_attr($value) .'"';
				else return $key;
			},' ')
		);

	}

	echo '</div>';

}