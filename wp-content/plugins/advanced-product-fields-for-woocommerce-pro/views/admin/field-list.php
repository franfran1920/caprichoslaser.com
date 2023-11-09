<?php
/* @var $model array */
use SW_WAPF_PRO\Includes\Classes\Helper;

$field_defs = \SW_WAPF_PRO\Includes\Classes\Config::get_field_definitions( 'admin' );

foreach ( $field_defs as &$def ) {
    unset( $def['icon'] );
    unset( $def['multi_choice'] );
    unset( $def['multi_select'] );
    unset( $def['has_products'] );
    unset( $def['group_label'] );
}

?>
<div rv-controller="FieldListCtrl"
     data-field-definitions="<?php echo Helper::thing_to_html_attribute_string( $field_defs ); ?>"
     data-raw-fields="<?php echo Helper::thing_to_html_attribute_string($model['fields']); ?>"
     data-field-conditions="<?php echo Helper::thing_to_html_attribute_string($model['condition_options']); ?>"
>

    <input type="hidden" name="wapf-fields" rv-value="fieldsJson" />

    <div class="wapf-performance wapf-list--empty" rv-if="hiddenForPerformance">
        <a href="#" class="button button-primary button-large" rv-on-click="renderFields"><?php _e('View all fields','sw-wapf');?></a>
        <div style="padding-top: 15px">
            <?php _e('To ensure optimal page load performance, the field list is not displayed yet.<br/>If you want to edit or add fields, click the button above to view the list. Rendering may take a moment to complete.','sw-wapf');?>
        </div>
    </div>

    <div rv-if="hiddenForPerformance | eq false" class="wapf-field-list">

        <div class="wapf-field-list__body">
            <span rv-show="renderedFields | isEmpty" class="wapf-list--empty" style="display: <?php echo empty($model['fields']) ? 'block' : 'none';?>;">
                <a href="#" class="button button-primary button-large" rv-on-click="addField"><?php _e('Add your first field','sw-wapf');?></a>
            </span>

            <?php \SW_WAPF_PRO\Includes\Classes\Html::admin_field([], $model['type']); ?>

        </div>

        <div rv-cloak>
            <div rv-show="renderedFields | isNotEmpty" class="wapf-field-list__footer">
                <a href="#" class="button button-primary button-large" rv-on-click="addField"><?php _e('Add a Field','sw-wapf');?></a>
            </div>
        </div>

    </div>

</div>