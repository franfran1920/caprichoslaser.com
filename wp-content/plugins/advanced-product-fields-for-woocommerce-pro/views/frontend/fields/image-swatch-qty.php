<?php
/** @var array $model */

use SW_WAPF_PRO\Includes\Classes\Enumerable;
use SW_WAPF_PRO\Includes\Classes\Html;

$cols = isset($model['field']->options['items_per_row']) ? intval($model['field']->options['items_per_row']) : 3;
$cols_tablet = isset($model['field']->options['items_per_row_tablet']) ? intval($model['field']->options['items_per_row_tablet']) : 3;
$cols_mobile = isset($model['field']->options['items_per_row_mobile']) ? intval($model['field']->options['items_per_row_mobile']) : 3;

if(!empty($model['field']->options['choices'])) {

    $label_pos = isset($model['field']->options['label_pos']) ? $model['field']->options['label_pos'] : 'default';
    echo '<div class="wapf-image-swatch-wrapper wapf-swatch-wrapper " style="--wapf-cols:'.$cols.';--wapf-cols-t:'.$cols_tablet.';--wapf-cols-m:'.$cols_mobile.'" data-is-required="'. $model['field']->required .'">';
    //echo '<input class="wapf-tf-h" name="wapf[field_' . $model['field']->id . ']" value="1" type="hidden" />';
    echo '<input type="hidden" class="wapf-tf-h" data-fid="'.$model['field']->id.'" value="1" name="wapf[field_'.$model['field']->id.']" />';
    for( $i = 0; $i < count( $model['field']->options['choices'] ); $i++ ) {
        $option = $model['field']->options['choices'][$i];
        $attributes = Html::option_attributes('radio', $model['product'], $model['field'], $option);
        $wrapper_classes = Html::quantity_swatch_wrapper_classes( $option, $model['field'], $model['product'] );
        $wrapper_attributes = Html::image_swatch_wrapper_attributes( $option, $model['field'] );
        $default = is_array( $model['default'] ) && isset( $model['default'][$i] ) ?  $model['default'][$i] : 0;
        //$default = $model['is_edit'] && is_array( $model['default'] ) ? ( isset( $model['default'][$i] ) ? $model['default'][$i] : 0 ) : ( empty( $option['default'] ) ? 0 : $option['default'] );
        $img_classes = '';
        if( isset( $wrapper_attributes['data-dir'])) $img_classes = 'wapf-tt-wrap';

       ?>
         <div class="wapf-swatch wapf-swatch--qty <?php echo join(' ', $wrapper_classes ) ?>">
            <div class="qty-swatch-img-wrapper">
                <div class="qty-swatch-img <?php echo $img_classes; ?>"  <?php echo Enumerable::from($wrapper_attributes)->join(function($value,$key) { return $key . '="' . esc_attr($value) .'"'; }, ' ') ?>>
                    <?php echo Html::get_swatch_image_html( $model['field'], $model['product'], $option ) ?>
                    <?php if($label_pos === 'tooltip') echo Html::swatch_label($model['field'], $option, $model['product']); ?>
                </div>
            </div>
            <div class="qty-swatch-inner">
                <div class="qty-swatch-qty">
                    <input data-no-zero="1" step="1" type="number" value="<?php esc_attr_e($default) ?>" <?php echo Enumerable::from($attributes)->join(function($value,$key) {if(strlen(''.$value)>0) return $key . '="' . esc_attr($value) .'"'; else return $key; },' '); ?> />
                </div>
                <?php if($label_pos === 'default') echo Html::swatch_label($model['field'], $option, $model['product']); ?>

            </div>
        </div>

    <?php } ?>

    </div>

<?php } ?>
