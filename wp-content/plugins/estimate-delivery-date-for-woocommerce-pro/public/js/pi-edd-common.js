(function ($) {
    "use strict";

    function frontEndVariables() {
        this.init = function () {
            this.detect();
        }

        this.detect = function () {
            var ids = [];
            jQuery('.pi-min-date').each(function () {
                var element_id = jQuery(this).data('id');
                if (element_id && !isNaN(element_id)) {
                    ids.push(element_id);
                }
            });

            jQuery('.pi-max-date').each(function () {
                var element_id = jQuery(this).data('id');
                if (element_id && !isNaN(element_id)) {
                    ids.push(element_id);
                }
            });

            jQuery('.pi-min-days').each(function () {
                var element_id = jQuery(this).data('id');
                if (element_id && !isNaN(element_id)) {
                    ids.push(element_id);
                }
            });

            jQuery('.pi-max-days').each(function () {
                var element_id = jQuery(this).data('id');
                if (element_id && !isNaN(element_id)) {
                    ids.push(element_id);
                }
            });

            jQuery('.pi-date').each(function () {
                var element_id = jQuery(this).data('id');
                if (element_id && !isNaN(element_id)) {
                    ids.push(element_id);
                }
            });

            jQuery('.pi-days').each(function () {
                var element_id = jQuery(this).data('id');
                if (element_id && !isNaN(element_id)) {
                    ids.push(element_id);
                }
            });


            if (ids.length > 0) {
                this.ajaxCall(ids);
            }
        }

        this.ajaxCall = function (ids) {
            var parent = this;
            var action = 'get_estimate_dates';
            jQuery.ajax({
                url: pi_edd_variable.wc_ajax_url.toString().replace('%%endpoint%%', action),
                method: 'post',
                data: {
                    ids: ids,
                },
                success: function (response) {
                    if (response?.product_estimates) {
                        parent.replaceElements(response.product_estimates)
                    }
                }
            });
        }

        this.replaceElements = function (estimates) {
            var parent = this;
            jQuery.each(estimates, function (id, estimate) {
                parent.minDate(id, estimate.min_date);
                parent.minDays(id, estimate.min_days);
                parent.maxDate(id, estimate.max_date);
                parent.maxDays(id, estimate.max_days);
                parent.date(id, estimate.date);
                parent.days(id, estimate.days);
            })
        }

        this.minDate = function (id, min_date) {
            if (jQuery('.pi-min-date-' + id).length > 0) {
                jQuery('.pi-min-date-' + id).html(min_date);
            }
        }

        this.minDays = function (id, min_days) {
            if (jQuery('.pi-min-days-' + id).length > 0) {
                jQuery('.pi-min-days-' + id).html(min_days);
            }
        }

        this.maxDate = function (id, max_date) {
            if (jQuery('.pi-max-date-' + id).length > 0) {
                jQuery('.pi-max-date-' + id).html(max_date);
            }
        }

        this.maxDays = function (id, max_days) {
            if (jQuery('.pi-max-days-' + id).length > 0) {
                jQuery('.pi-max-days-' + id).html(max_days);
            }
        }

        this.date = function (id, date) {
            if (jQuery('.pi-date-' + id).length > 0) {
                jQuery('.pi-date-' + id).html(date);
            }
        }

        this.days = function (id, days) {
            if (jQuery('.pi-days-' + id).length > 0) {
                jQuery('.pi-days-' + id).html(days);
            }
        }
    }

    jQuery(function ($) {
        var frontEndVariablesObj = new frontEndVariables();
        frontEndVariablesObj.init();
    });


})(jQuery);