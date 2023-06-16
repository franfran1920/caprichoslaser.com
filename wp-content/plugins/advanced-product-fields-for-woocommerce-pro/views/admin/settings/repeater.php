<?php
/* @var $model array */
?>

<div class="wapf-toggle" rv-unique-checkbox>
    <input rv-on-change="repeaterChanged" rv-checked="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.enabled" type="checkbox" >
    <label class="wapf-toggle__label" for="wapf-toggle-">
        <span class="wapf-toggle__inner" data-true="<?php _e('Yes','sw-wapf'); ?>" data-false="<?php _e('No','sw-wapf'); ?>"></span>
        <span class="wapf-toggle__switch"></span>
    </label>
</div>

<div style="display: flex;flex-flow:column;padding-top:15px;" rv-show="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.enabled">
    <div style="display: flex;justify-content: space-between">
        <div style="width: 49%">
            <div style="font-weight: bold;padding-bottom:10px;"><?php _e('Repeater type','sw-wapf'); ?></div>
            <select rv-on-change="onChange" rv-value="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.type">
                <option value="qty"><?php _e('Repeat based on WooCommerce quantity input','sw-wapf') ?></option>
                <option value="button"><?php _e('Repeat by clicking a button','sw-wapf') ?></option>
            </select>
        </div>
        <div style="width: 49%">
                <div style="font-weight: bold;padding-bottom:10px;"><?php _e('Label for duplicates','sw-wapf'); ?></div>
                <input rv-on-change="onChange" type="text" rv-value="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.label" />
            <div style="clear: both" class="wapf-option-note">
                <?php _e("use {{n}} to denote the number of the duplicate. Leave blank if you don't need a label.",'sw-wapf'); ?>
            </div>
        </div>
    </div>
    <div style="padding-top:12px; display: flex;justify-content: space-between" rv-if="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.type | eq 'button'">
        <div style="width: 49%">
            <div style="font-weight: bold;padding-bottom:10px;"><?php _e('Button text','sw-wapf'); ?></div>
            <input rv-on-change="onChange" type="text" rv-value="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.add" />
        </div>
        <div style="width: 49%">
            <div style="font-weight: bold;padding-bottom:10px;"><?php _e('"Delete" button text','sw-wapf'); ?></div>
            <input rv-on-change="onChange" type="text" rv-value="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.del" />
        </div>
    </div>
    <div style="padding-top:12px; display: flex;justify-content: space-between" rv-if="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.type | eq 'button'">
        <div style="width: 49%">
            <div style="font-weight: bold;padding-bottom:10px;"><?php _e('Maximum repetitions','sw-wapf'); ?></div>
            <input rv-on-change="onChange" type="number" min="1" rv-value="<?php echo $model['is_field_setting'] ? 'field' : 'settings'; ?>.clone.max" />
            <div style="clear: both" class="wapf-option-note">
                <?php echo __("Set the max. allowed repetitions. Leave blank if you don't need a maximum.",'sw-wapf'); ?>
            </div>
        </div>
    </div>
</div>