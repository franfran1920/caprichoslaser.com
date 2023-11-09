<?php
/* @var $field [] */
/* @var $type string */
use \SW_WAPF_PRO\Includes\Classes\Config;
$types = Config::get_field_definitions( 'admin' );
?>

<div rv-each-field="renderedFields" rv-cloak class="wapf-field" rv-data-type="field.type"
     rv-data-level="field.level"
     rv-data-field-id="field.id" rv-data-grouptype="field.group"
     rv-class-wapf--active="activeField | equalIds field">
    <div class="wapf-field__header">
        <div class="wapf-field-sort sort--left" title="<?php _e('Drag & drop','sw-wapf');?>">☰</div>
        <div class="wapf-field-icon">
            <?php
                foreach ( $types as $type ) {
                    ?>
                    <div rv-if="field.type | eq '<?php echo $type['id']; ?>'">
                        <?php if(isset($type['icon'])) echo $type['icon']; ?>
                    </div>
                    <?php
                }
            ?>
        </div>
        <div rv-if="field.group|in 'field,content'" class="wapf-field-label" rv-on-click="setActiveField">
            <span rv-text="field.label|ifEmpty '<?php _e('(No label)','sw-wapf'); ?>'"></span>
            <span class="wapf-field-below-title">
                <span class="wapf-field-id" rv-on-click="copyFieldId">
                    <span>ID: {field.id}</span>
                    <span class="wapf-copy-id"><a href="#" data-copy="<?php _e('copy', 'sw-wapf') ?>" data-copied="<?php _e('copied!', 'sw-wapf') ?>"><?php _e('copy', 'sw-wapf'); ?></a></span>
                </span>
            </span>
        </div>
        <div rv-if="field.group|eq 'layout'" class="wapf-field-label" rv-on-click="setActiveField">
            <span style="font-weight: bold" rv-text="fieldDefinitions | getFieldDefTitle field.type"></span>
        </div>

        <div class="wapf-field-type">
            <span rv-text="fieldDefinitions | getFieldDefTitle field.type"></span>
        </div>
        <div class="wapf-field-actions">
            <a href="#" title="<?php _e('Duplicate field','sw-wapf');?>" rv-on-click="duplicateField">Duplicate</a>
            <a href="#" style="color: #a00 !important" title="<?php _e('Delete field','sw-wapf');?>" rv-on-click="deleteField">Delete</a>
        </div>
    </div>

    <div class="wapf-field-body" style="display: none;">
        <?php

        do_action('wapf/admin/before_field_settings');

        \SW_WAPF_PRO\Includes\Classes\Html::setting([
            'type'              => 'field-type',
            'id'                => 'type',
            'label'             => __('Type','sw-wapf'),
            'description'       => __('Specify the field type.','sw-wapf'),
            'options'           => $types,
        ]);
        ?>
        <div rv-if="field.group|neq 'layout'">
        <?php
        \SW_WAPF_PRO\Includes\Classes\Html::setting([
            'type'              => 'text',
            'id'                => 'label',
            'label'             => __('Label','sw-wapf'),
            'description'       => __('Label shown near the field.','sw-wapf'),
            'is_field_setting'  => true
        ]);
        ?>
        </div>
        <div rv-if="field.group | notin 'content,layout'" rv-show="field.type | neq 'calc'">
        <?php
        \SW_WAPF_PRO\Includes\Classes\Html::setting([
            'type'              => 'textarea',
            'id'                => 'description',
            'label'             => __('Instructions','sw-wapf'),
            'description'       => __('Display extra info near the field.','sw-wapf'),
            'is_field_setting'  => true
        ]);

        \SW_WAPF_PRO\Includes\Classes\Html::setting([
            'type'              => 'true-false',
            'id'                => 'required',
            'label'             => __('Required','sw-wapf'),
            'description'       => __('Is input required?','sw-wapf'),
            'is_field_setting'  => true
        ]);
        ?>
        </div>
        <?php

        do_action('wapf/admin/before_additional_field_settings');

        foreach( Config::get_field_options() as $field_type => $options) { ?>
            <div rv-if="field.type | eq '<?php echo $field_type; ?>'" class="wapf_field__options">
                <?php
                    foreach($options as $option) {
                        if(!empty($option) && isset($option['type']))
                            \SW_WAPF_PRO\Includes\Classes\Html::setting( array_merge($option,['field_type' => $field_type]) );
                    }
                ?>
            </div>
        <?php
        }

        do_action('wapf/admin/after_additional_field_settings');
        ?>

        <div rv-if="field.type | notin 'p,img,sectionend,calc'">
        <?php
        \SW_WAPF_PRO\Includes\Classes\Html::setting([
	        'type'              => 'repeater',
	        'id'                => 'clone',
	        'label'             => __('Enable repeater','sw-wapf'),
	        'description'       => __('Make the element repeatable.','sw-wapf'),
	        'is_field_setting'  => true
        ]);
        ?>
        </div>
        <div rv-if="field.type | neq 'sectionend'">
        <?php
        \SW_WAPF_PRO\Includes\Classes\Html::setting([
	        'type'              => 'conditionals',
	        'id'                => 'conditionals',
	        'label'             => __('Conditionals','sw-wapf'),
	        'description'       => __('Show field only if conditions are met.','sw-wapf'),
	        'is_field_setting'  => true
        ]);
        ?>
        </div>

        <div rv-if="field.group | notin 'content,layout'">
		    <?php
		    \SW_WAPF_PRO\Includes\Classes\Html::setting([
			    'type'              => 'true-falses',
			    'options'           => [
                    'hide_cart'     => __('Hide this field value on the cart page','sw-wapf'),
                    'hide_checkout' => __('Hide this field value on the checkout page','sw-wapf'),
                    'hide_order'    => __('Hide this field value on the "order received" page and emails.','sw-wapf'),
                ],
			    'label'             => __('Hide on cart, checkout, order','sw-wapf'),
			    'description'       => __("Hide field values from cart, checkout, or order.",'sw-wapf'),
			    'is_field_setting'  => true
		    ]);
		    ?>
        </div>

        <div rv-if="field.type | neq 'sectionend'">
        <?php
        \SW_WAPF_PRO\Includes\Classes\Html::setting([
            'type'              => 'attributes',
            'id'                => 'attributes',
            'label'             => __('Wrapper attributes','sw-wapf'),
            'is_field_setting'  => true
        ]);
        ?>
        </div>
        <?php do_action('wapf/admin/after_field_settings'); ?>
    </div>

</div>