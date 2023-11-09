<?php /* @var $model array */ ?>

<div style="width: 100%;" rv-show="field.choices | isNotEmpty">
    <div class="wapf-options__header">
        <div class="wapf-option__sort"></div>
        <div class="wapf-option__flex"><?php _e('Text','sw-wapf'); ?></div>
        <?php if(isset($model['show_pricing_options']) && $model['show_pricing_options']) { ?>
            <div class="wapf-option__flex"><?php _e('Price type','sw-wapf'); ?></div>
            <div class="wapf-option__flex"><?php _e('Pricing','sw-wapf'); ?></div>
        <?php } ?>
	    <?php if(!empty($model['inputs'])) { foreach ($model['inputs'] as $input) { ?>
            <div class="wapf-option__flex"><?php echo isset($input['title']) ? esc_html($input['title']) : ''; ?></div>
	    <?php }} ?>
        <div class="wapf-option__selected"><?php _e('Selected', 'sw-wapf'); ?></div>
        <div  class="wapf-option__delete"></div>
    </div>
    <div rv-sortable-options="field.choices" class="wapf-options__body">
        <div class="wapf-option" rv-each-choice="field.choices" rv-data-option-slug="choice.slug">
            <div class="wapf-option__sort"><span rv-sortable-option class="wapf-option-sort">☰</span></div>
            <div class="wapf-option__flex"><input rv-on-keyup="onChange" rv-on-change="onChange" type="text" class="choice-label" rv-value="choice.label"/></div>
            <?php if(isset($model['show_pricing_options']) && $model['show_pricing_options']) { ?>
                <div class="wapf-option__flex">
                    <select class="wapf-pricing-list" rv-on-change="onChange" rv-value="choice.pricing_type">
                        <option value="none"><?php _e('No price change','sw-wapf'); ?></option>
                        <?php
                        foreach(\SW_WAPF_PRO\Includes\Classes\Config::get_pricing_options() as $k => $v) {
                            echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="wapf-option__flex">
                    <div rv-if="choice.pricing_type | eq 'fx'" class="wapf-input-prepend-append">
                        <div class="wapf-input-prepend small" rv-on-click="openFormulaBuilder" style="cursor: pointer;opacity: .75"><i style="display:flex;" class="dashicons-before dashicons-editor-help"></i></div>
                        <input placeholder="<?php _e('Enter formula','sw-wapf');?>" rv-on-change="onChange" type="text" rv-value="choice.pricing_amount" />
                    </div>
                    <input rv-if="choice.pricing_type | neq 'fx'" placeholder="<?php _e('Amount','sw-wapf');?>" rv-on-change="onChange" type="number" step="any" rv-value="choice.pricing_amount" />
                </div>
            <?php } ?>
            <?php if(!empty($model['inputs'])) { foreach ($model['inputs'] as $input) { ?>
                <div class="wapf-option__flex">
                    <?php echo \SW_WAPF_PRO\Includes\Classes\Html::admin_choice_option_extra_input($input); ?>
                </div>
            <?php } } ?>
            <div class="wapf-option__selected"><input data-multi-option="<?php echo isset($model['multi_option']) ? $model['multi_option'] : '0' ;?>" rv-on-change="field.checkSelected" rv-checked="choice.selected" type="checkbox" /></div>
            <div class="wapf-option__delete"><a href="#" rv-on-click="field.deleteChoice" class="button wapf-button--tiny-rounded">&times;</a></div>
        </div>
    </div>
</div>

<div style="display: flex;align-items: center;justify-content: space-between">
    <div>
        <a href="#" rv-on-click="field.addChoiceEvent" class="button"><?php _e('Add option','sw-wapf'); ?></a>
    </div>
    <div>
        <ul>
            <li style="display: inline-block"><a rv-on-click="openOptionsImport" data-type="select" href="#"><?php _e('Import','sw-wapf'); ?></a></li>
            <li style="display: inline-block;">&bull;</li>
            <li style="display: inline-block;"><a href="https://www.studiowombat.com/knowledge-base/all-pricing-options-explained/?ref=wapf_admin" target="_blank"><?php _e('Pricing help','sw-wapf'); ?></a></li>
        </ul>
    </div>
</div>
