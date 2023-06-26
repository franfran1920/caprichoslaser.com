<?php
/** @var array $model */

use SW_WAPF_PRO\Includes\Classes\Enumerable;
use SW_WAPF_PRO\Includes\Classes\Html;

if(!empty($model['field']->options['choices'])) {

    echo '<div class="wapf-checkboxes" '.Html::multi_choice_attributes($model['field'],$model['product']).'>';

    foreach ($model['field']->options['choices'] as $option) {

	    $attributes = Html::option_attributes('checkbox',$model['product'],$model['field'],$option, true);
	    $wrapper_classes = Html::option_wrapper_classes($option, $model['field'], $model['product'], $model['default'] );
	    if( in_array( 'wapf-checked', $wrapper_classes ) ) {
			$attributes['checked'] = '';
		}

        echo sprintf(
            '<div class="%s"><label for="%s" class="wapf-input-label"><input type="hidden" class="wapf-tf-h" data-fid="'.$model['field']->id.'" value="0" name="wapf[field_'.$model['field']->id.'][]" />
<input %s /><span class="wapf-custom"></span><span class="wapf-label-text">%s</span></label></div>',
            join( ' ', $wrapper_classes ),
	        $attributes['id'],
            Enumerable::from($attributes)->join(function($value,$key) {
                if($value)
                    return $key . '="' . esc_attr($value) .'"';
                else return $key;
            },' '),
            esc_html($option['label']) . ' ' . Html::frontend_option_pricing_hint( $option, $model['field'], $model['product'])
        );

    }

    echo '</div>';

}