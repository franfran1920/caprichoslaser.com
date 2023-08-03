jQuery(function ($) {
    jQuery("#set_back_order_days_bulk").on('change', function () {
        if (jQuery(this).is(':checked')) {
            jQuery(".backorder_days_settin_from_bulk").fadeIn();
        } else {
            jQuery(".backorder_days_settin_from_bulk").fadeOut();
        }
    });

    jQuery("#pi-extra-time-type-bulk").on('change', function () {
        if (jQuery(this).val() == 'single') {
            jQuery("#single-time-bulk").fadeIn();
            jQuery(".range-time-bulk").fadeOut();
        } else {
            jQuery("#single-time-bulk").fadeOut();
            jQuery(".range-time-bulk").fadeIn();
        }
    });

    jQuery("#pi-extra-time-type-bulk").trigger('change');
});