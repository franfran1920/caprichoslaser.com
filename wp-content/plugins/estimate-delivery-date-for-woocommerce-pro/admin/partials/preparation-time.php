<div class="card mt-3" style="max-width:100%">
<div class="row align-items-center">
    <div class="col-12 col-md-6">
    <label for="pisol_edd_set_time_from_class" class="mb-0"><?php _e('Set preparation time from shipping class','pi-edd'); ?></label>
    </div>
    <div class="col-12 col-md-6">
        <div class="custom-control custom-switch">
            <input type="checkbox" value="1" class="custom-control-input" name="<?php echo $this->setting_key; ?>[pisol_edd_set_time_from_class]" id="pisol_edd_set_time_from_class" <?php checked($set_time_from_class, 1); ?>>
            <label class="custom-control-label" for="pisol_edd_set_time_from_class"></label>
        </div>
    </div>
</div>
<div id="row_different_preparation_time">
<div id="row_product_preparation_time" class="row py-4 align-items-center ">
    <div class="col-12 col-md-5">
            <label class="h6 mb-0" for="product_preparation_time"><?php _e('Min product preparation days','pi-edd'); ?></label>                       
    </div>
    <div class="col-12 col-md-7">
        <input type="number" class="form-control " name="<?php echo $this->setting_key; ?>[product_preparation_time]" id="product_preparation_time" min="0" value="<?php echo $product_preparation_time; ?>">
    </div>
</div>

<div id="row_product_preparation_time_max" class="row py-4 align-items-center ">
    <div class="col-12 col-md-5">
            <label class="h6 mb-0" for="product_preparation_time_max"><?php _e('Max product preparation days','pi-edd'); ?></label>                       
    </div>
    <div class="col-12 col-md-7">
        <input type="number" class="form-control " name="<?php echo $this->setting_key; ?>[product_preparation_time_max]" id="product_preparation_time_max" min="0" value="<?php echo $product_preparation_time_max; ?>">
    </div>
</div>

<div id="row_out_of_stock_product_preparation_time" class="row py-4 align-items-center ">
    <div class="col-12 col-md-5">
        <label class="h6 mb-0" for="out_of_stock_product_preparation_time"><?php _e('Extra time added to preparation time (when product goes out of stock)','pi-edd'); ?></label> 
    </div>
    <div class="col-12 col-md-7">
        <input type="number" class="form-control " name="<?php echo $this->setting_key; ?>[out_of_stock_product_preparation_time]" id="out_of_stock_product_preparation_time" value="<?php echo $out_of_stock_product_preparation_time; ?>" min="0">
    </div>
</div>

<div id="row_exact_arrival_date" class="row py-4 align-items-center ">
    <div class="col-12 col-md-5">
        <label class="h6 mb-0" for="set_pisol_exact_availability_date"><?php _e('Exact Product availability date (Preparation time will be added to this date)','pi-edd'); ?></label> 
    </div>
    <div class="col-12 col-md-7">
        <input type="text" class="form-control" name="<?php echo $this->setting_key; ?>[pisol_exact_availability_date]" id="set_pisol_exact_availability_date" value="<?php echo $pisol_exact_availability_date; ?>"><a href="javascript:void(0)" id="pi-clear-exact">Clear</a>
    </div>
</div>

</div>
</div>
<script>
jQuery(function(){
    function pi_edd_class_hideShowField(parent, child, show_hide) {
        var $ = jQuery;
        $(parent).on('change', function () {
            if (show_hide == 'show') {
                if ($(parent).is(":checked")) {
                    $(child).fadeIn();
                } else {
                    $(child).fadeOut();
                }
            } else {
                if ($(parent).is(":checked")) {
                    $(child).fadeOut();
                } else {
                    $(child).fadeIn();
                }
            }
        });
        jQuery(parent).trigger("change");
    }

    pi_edd_class_hideShowField("#pisol_edd_set_time_from_class", "#row_different_preparation_time", 'show');

    jQuery("#set_pisol_exact_availability_date").datepicker({
                            dateFormat:"yy/mm/dd"
                        });

    jQuery("#pi-clear-exact").on('click', function(){
        jQuery("#set_pisol_exact_availability_date").val('');
    })
})
</script>