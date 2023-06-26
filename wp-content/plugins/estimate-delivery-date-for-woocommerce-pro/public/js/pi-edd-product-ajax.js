(function ($) {
    "use strict";

    function simpleAjaxEstimate() {
        this.init = function () {
            this.detectSimple();
            this.shippingMethodUpdatedEvent();
            this.singleProductEstimateEvent();
        }

        this.shippingMethodUpdatedEvent = function () {
            var parent = this;
            jQuery(document).on('pisol_shipping_address_updated', function () {
                parent.detectSimple();
            });
        }

        this.singleProductEstimateEvent = function () {
            var parent = this;
            jQuery(document).on('pisol_load_single_product_estimate', function () {
                parent.detectSimple();
            });
        }

        this.detectSimple = function () {
            var parent = this;
            jQuery(".pi-edd-ajax-simple").each(function () {
                var product_id = jQuery(this).data('product_id');
                parent.getEstimate(product_id, this);
            });
        }

        this.getEstimate = function (product_id, container) {
            this.blockUI(product_id);
            var parent = this;
            var action = 'pisol_product_estimate';
            var message = jQuery(container).data('message');
            jQuery.ajax({
                url: pi_edd_variable.wc_ajax_url.toString().replace('%%endpoint%%', action),
                method: 'post',
                data: {
                    action: action,
                    product_id: product_id,
                    message: message
                },
                success: function (response) {
                    if (response != "") {
                        parent.showContainer(container);
                        jQuery(container).html(response);

                    } else {
                        parent.hideContainer(container);
                    }
                }
            }).always(function () {
                parent.unblockUI(product_id);
            });
        }

        this.hideContainer = function (container) {
            jQuery(container).fadeOut();
        }

        this.showContainer = function (container) {
            jQuery(container).fadeIn();
        }

        this.blockUI = function (product_id) {
            if (typeof jQuery.fn.block == 'undefined') return;
            jQuery('.pi-edd-ajax-estimate-' + product_id).block({
                message: null,
                overlayCSS: {
                    background: "#fff",
                    opacity: .6
                }
            });
        }

        this.unblockUI = function (product_id) {
            if (typeof jQuery.fn.block == 'undefined') return;
            jQuery('.pi-edd-ajax-estimate-' + product_id).unblock();
        }

    }

    function variableEstimate() {
        this.init = function () {
            this.detectVariationChange();
            this.detectReset();
            this.customTrigger();
            this.loadFirstVariation();
        }

        this.loadFirstVariation = function () {
            var parent = this;
            if (pi_edd_variable.showFirstVariationEstimate == 'first-variation') {
                jQuery(".pi-shortcode.pi-edd-ajax-variable").each(function () {
                    var product_id = jQuery(this).data('product_id');
                    var message = jQuery(this).data('message');
                    parent.getEstimate(product_id, 'first', message);
                });
            }
        }

        this.customTrigger = function () {
            var parent = this;
            jQuery(document).on('pi_edd_custom_get_estimate_trigger', function (e, product_id, variable_id) {

                parent.getEstimate(product_id, variable_id);
            });
        }

        this.detectVariationChange = function () {
            var parent = this;
            jQuery(document).on('show_variation.pi_edd_variation_change', "form.variations_form", function (event, data) {
                var form = $(event.target).closest('form.variations_form');
                var product_id = form.data('product_id');
                if (data != undefined) {
                    var variation_id = data.variation_id;
                    if (data.is_in_stock) {
                        if (variation_id != "" && variation_id != 0 && !isNaN(variation_id)) {
                            parent.getEstimate(product_id, variation_id);
                        } else {
                            parent.noVariationSelected(product_id);
                        }
                    } else {
                        parent.outOfStockMessage(product_id);
                    }
                }
            });
        }

        this.detectReset = function () {
            var parent = this;
            jQuery(document).on('reset_data.pi_edd_variation_reset', "form.variations_form", function (event, data) {
                var form = $(event.target).closest('form.variations_form');
                var product_id = form.data('product_id');
                parent.noVariationSelected(product_id);
            });
        }

        this.getEstimate = function (product_id, variable_id, message = '') {
            this.blockUI(product_id);
            var parent = this;
            var action = 'pisol_product_estimate';
            jQuery.ajax({
                url: pi_edd_variable.wc_ajax_url.toString().replace('%%endpoint%%', action),
                method: 'post',
                data: {
                    action: action,
                    product_id: product_id,
                    variable_id: variable_id,
                    message: message
                },
                success: function (response) {
                    if (response != "") {
                        parent.showContainer(product_id);
                        parent.setMessage(product_id, variable_id, response);

                    } else {
                        parent.hideContainer(product_id);
                    }
                    jQuery(document).trigger('pi_edd_variation_estimate_loaded', [response, product_id]);
                }
            }).always(function () {
                parent.unblockUI(product_id);
            });
        }

        this.hideContainer = function (product_id) {
            jQuery(".pi-edd-ajax-estimate-" + product_id).fadeOut();
        }

        this.showContainer = function (product_id) {
            jQuery(".pi-edd-ajax-estimate-" + product_id).fadeIn();
        }

        this.setMessage = function (product_id, variable_id, message) {
            jQuery(".pi-edd-ajax-estimate-" + product_id).html(message);
        }

        this.noVariationSelected = function (product_id) {
            var parent = this;
            if (pi_edd_variable.showFirstVariationEstimate == 'first-variation') {
                this.getEstimate(product_id, 'first');
            } else {
                jQuery(".pi-edd-ajax-estimate-" + product_id).each(function () {
                    var estimates = jQuery(this).data('notselected');
                    if (estimates == "") {
                        parent.hideContainer(product_id);
                    } else {
                        parent.showContainer(product_id);
                        jQuery(this).html(estimates);
                    }
                });
            }
        }

        this.outOfStockMessage = function (product_id) {
            var parent = this;
            jQuery(".pi-edd-ajax-estimate-" + product_id).each(function () {
                var estimates = pi_edd_variable.out_of_stock_message;
                if (estimates == "") {
                    parent.hideContainer(product_id);
                } else {
                    parent.showContainer(product_id);
                    jQuery(this).html(estimates);
                }
            });
        }

        this.blockUI = function (product_id) {
            if (typeof jQuery.fn.block == 'undefined') return;
            jQuery('.pi-edd-ajax-estimate-' + product_id).block({
                message: null,
                overlayCSS: {
                    background: "#fff",
                    opacity: .6
                }
            });
        }

        this.unblockUI = function (product_id) {
            if (typeof jQuery.fn.block == 'undefined') return;
            jQuery('.pi-edd-ajax-estimate-' + product_id).unblock();
        }

    }

    jQuery(function ($) {
        var simpleAjaxEstimateObj = new simpleAjaxEstimate();
        simpleAjaxEstimateObj.init();

        var variableEstimateObj = new variableEstimate();
        variableEstimateObj.init();
    })

})(jQuery);