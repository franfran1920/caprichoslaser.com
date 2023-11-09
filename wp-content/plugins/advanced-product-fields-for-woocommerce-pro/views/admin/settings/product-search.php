<?php /* @var $model array */ ?>

<div style="width: 100%;" rv-show="field.products | isNotEmpty">
    <div class="wapf-options__header">
        <div class="wapf-option__sort"></div>
        <div class="wapf-option__flex"><?php _e('Product','sw-wapf'); ?></div>
        <div class="wapf-option__selected"><?php _e('Selected', 'sw-wapf'); ?></div>
        <div  class="wapf-option__delete"></div>
    </div>
    <div rv-sortable-options="field.products" class="wapf-options__body">
        <div class="wapf-option" rv-each-product="field.products" rv-data-option-slug="product.slug">
            <div class="wapf-option__sort"><span rv-sortable-option class="wapf-option-sort">☰</span></div>
            <div class="wapf-option__flex">
               <select
                    data-select2-keys="product_id,label"
                    rv-on-change="onChange"
                    rv-select2="product"
                    multiple="multiple"
                    class="wapf-select2"
                    data-select2-placeholder="<?php __("Search a product...",'sw-wapf') ?>"
                    data-select2-action="wapf_search_products"
                    data-select2-single="true"
                >
                </select>
            </div>

            <div class="wapf-option__selected"><input data-multi-option="1" rv-on-change="field.checkSelected" rv-checked="choice.selected" type="checkbox" /></div>
            <div class="wapf-option__delete"><a href="#" rv-on-click="field.deleteProductEvent" class="button wapf-button--tiny-rounded">&times;</a></div>
        </div>
    </div>
</div>

<div>
    <a href="#" rv-on-click="field.addProductEvent" class="button"><?php _e('Add product','sw-wapf'); ?></a>
</div>
