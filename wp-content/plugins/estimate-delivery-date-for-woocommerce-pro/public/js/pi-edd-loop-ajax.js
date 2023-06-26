(function ($) {
    "use strict";

    function pi_edd_loop_ajax() {
        this.products = [];

        this.init = function () {

            this.makeAjaxCall();
        }

        this.getProducts = function () {
            var products = [];
            jQuery('.pi-edd-loop-ajax').each(function () {
                var id = jQuery(this).data('product');
                if (id != undefined && id != null) {
                    products.push(id);
                }
            })
            return products;
        }

        this.makeAjaxCall = function () {
            this.products = this.getProducts();
            var parent = this;
            var action = 'pi_edd_loop_estimate';
            if (this.products.length > 0) {
                var promise = jQuery.ajax({
                    url: pi_edd_variable.wc_ajax_url.toString().replace('%%endpoint%%', action),
                    method: 'post',
                    data: {
                        action: action,
                        products: parent.products
                    },
                    success: function (response) {
                        parent.assignEstimates(response);
                    }
                });
            }
        }

        this.assignEstimates = function (response) {
            var estimates = JSON.parse(response);

            jQuery.each(estimates, function (product_id, estimate) {
                var id = '#pi-edd-loop-ajax-id-' + product_id;
                jQuery(id).html(estimate);
            });
        }
    }

    jQuery(document).ready(function ($) {
        var pi_edd_loop_ajax_obj = new pi_edd_loop_ajax();
        pi_edd_loop_ajax_obj.init();

        jQuery(document).on('pi_edd_load_loop_ajax', function () {
            var pi_edd_loop_ajax_obj = new pi_edd_loop_ajax();
            pi_edd_loop_ajax_obj.init();
        })
    });

})(jQuery);