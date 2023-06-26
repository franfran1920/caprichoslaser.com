<?php
/** @var array $model */

use SW_WAPF_PRO\Includes\Classes\Enumerable;
use SW_WAPF_PRO\Includes\Classes\Html;

$cols = isset($model['field']->options['items_per_row']) ? intval($model['field']->options['items_per_row']) : 3;
$cols_tablet = isset($model['field']->options['items_per_row_tablet']) ? intval($model['field']->options['items_per_row_tablet']) : 3;
$cols_mobile = isset($model['field']->options['items_per_row_mobile']) ? intval($model['field']->options['items_per_row_mobile']) : 3;
$first = true;
if(!empty($model['field']->options['choices'])) {

	echo '<div class="wapf-image-swatch-wrapper wapf-swatch-wrapper wapf-col--'.$cols.'" style="--wapf-cols:'.$cols.';--wapf-cols-t:'.$cols_tablet.';--wapf-cols-m:'.$cols_mobile.'">';

	foreach ($model['field']->options['choices'] as $option) {

		$attributes = Html::option_attributes('radio', $model['product'], $model['field'], $option,false);
        $wrapper_attributes = Html::image_swatch_wrapper_attributes( $option, $model['field'] );
		$wrapper_classes = Html::option_wrapper_classes($option, $model['field'], $model['product'], $model['default'] );
		if( in_array( 'wapf-checked', $wrapper_classes ) ) {
			$attributes['checked'] = '';
		}

		echo sprintf(
			'<div class="wapf-swatch wapf-swatch--image %s" %s>%s<input %s />%s%s</div>',
			join( ' ', $wrapper_classes ),
            Enumerable::from($wrapper_attributes)->join(function($value,$key) { return $key . '="' . esc_attr($value) .'"'; }, ' '),
			$first ? '<input type="hidden" class="wapf-tf-h" data-fid="'.$model['field']->id.'" value="0" name="wapf[field_'.$model['field']->id.']" />' : '',
			Enumerable::from($attributes)->join(function($value,$key) {
				if($value)
					return $key . '="' . esc_attr($value) .'"';
				else return $key;
			},' '),
			Html::get_swatch_image_html( $model['field'], $model['product'], $option ),
			Html::swatch_label($model['field'], $option, $model['product'])
		);

		$first = false;

	}

	echo '</div>';

}