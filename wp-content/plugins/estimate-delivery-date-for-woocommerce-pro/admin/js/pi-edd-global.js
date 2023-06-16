jQuery(function ($) {

    /**
     * This is needed so we can update quickedit field on quickedit save
     */
    jQuery('#the-list').on('click', '.editinline', function () {
        var post_id = jQuery(this).closest('tr').attr('id');
        jQuery('input[name="product_preparation_time"]', '.inline-edit-row').val($("#product_preparation_time_" + post_id).val());
    });

    pi_edd_disable_estimate_on_product();

    function pi_edd_disable_estimate_on_product() {
        var $ = jQuery;
        var global = pi_edd_setting.global_estimate_status;
        $("#pisol_edd_disable_estimate").on('change', function () {
            var selected_val = $("#pisol_edd_disable_estimate").val();
            if (selected_val == 'yes' || (selected_val == "" && global == 'disable')) {
                $("#pisol-product-preparation-days").fadeOut();
            } else if (selected_val == 'no' || (selected_val == "" && global == 'enable')) {
                $("#pisol-product-preparation-days").fadeIn();
            }
        });
        jQuery("#pisol_edd_disable_estimate").trigger("change");

        $("#pisol_enable_exact_date").on('change', function () {
            if ($("#pisol_enable_exact_date").is(":checked")) {

                $(".product_availability_date_main").fadeIn();
            } else {

                $(".product_availability_date_main").fadeOut();
            }
        });
        jQuery("#pisol_enable_exact_date").trigger("change");


        $("#pisol_edd_use_variation_preparation_time").on('change', function () {
            hideVariationExtras();
        });

        function hideVariationExtras() {
            jQuery(".pisol_edd_disable_estimate_for_variation").each(function () {
                onVariationDisableEstimateHandler(this);
            });
        }

        function onVariationDisableEstimateChange() {
            jQuery(document).on('change', '.pisol_edd_disable_estimate_for_variation', function () {
                onVariationDisableEstimateHandler(this);
            });
        }

        onVariationDisableEstimateChange();

        function onVariationDisableEstimateHandler(element) {
            if (jQuery("#pisol_edd_use_variation_preparation_time").is(":checked") && !jQuery(element).is(":checked")) {
                jQuery(element).parent('p').next('.pisol-edd-variation-setting-container').fadeIn();
            } else if (jQuery("#pisol_edd_use_variation_preparation_time").is(":checked") && jQuery(element).is(":checked")) {
                jQuery(element).parent('p').next('.pisol-edd-variation-setting-container').fadeOut();
            } else if (!jQuery("#pisol_edd_use_variation_preparation_time").is(":checked")) {
                jQuery(element).parent('p').next('.pisol-edd-variation-setting-container').fadeOut();
            }
        }

        jQuery("#pisol_edd_use_variation_preparation_time").trigger("change");

        jQuery(document).on('woocommerce_variations_loaded', function (event) {
            jQuery("#pisol_edd_use_variation_preparation_time").trigger("change");
        });

        jQuery(".pisol_edd_date_picker").datepicker({
            dateFormat: "yy/mm/dd",
            locale: "en",
            showButtonPanel: true,
            beforeShow: function (input) {
                setTimeout(function () {
                    var buttonPane = $(input)
                        .datepicker("widget")
                        .find(".ui-datepicker-buttonpane");

                    $("<button>", {
                        text: "Clear",
                        click: function () {
                            //Code to clear your date field (text box, read only field etc.) I had to remove the line below and add custom code here
                            $.datepicker._clearDate(input);
                        }
                    }).appendTo(buttonPane).addClass("ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all");
                }, 1);
            },
            onChangeMonthYear: function (year, month, instance) {
                setTimeout(function () {
                    var buttonPane = $(instance)
                        .datepicker("widget")
                        .find(".ui-datepicker-buttonpane");

                    $("<button>", {
                        text: "Clear",
                        click: function () {
                            //Code to clear your date field (text box, read only field etc.) I had to remove the line below and add custom code here
                            $.datepicker._clearDate(instance.input);
                        }
                    }).appendTo(buttonPane).addClass("ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all");
                }, 1);
            }
        }).attr('readonly', 'true');
    }

    pi_edd_hide_global_preparation_time();
    function pi_edd_hide_global_preparation_time() {
        var $ = jQuery;
        $("#pisol_edd_use_variation_preparation_time").on('change', function () {
            if ($("#pisol_edd_use_variation_preparation_time").is(":checked")) {
                $(".pi-edd-product-level-setting").fadeOut();
            } else {
                $(".pi-edd-product-level-setting").fadeIn();
            }
        });
        jQuery("#pisol_edd_use_variation_preparation_time").trigger("change");
    }

    pi_edd_hideShowField("#pi_edd_enable_special_wording_same_day_delivery", "#row_pi_edd_estimate_message_same_day_delivery", 'show');
    pi_edd_hideShowField("#pi_edd_enable_special_wording_tomorrow_delivery", "#row_pi_edd_estimate_message_tomorrow_delivery", 'show');

    pi_edd_hideShowField("#pi_general_range", "#row_pi_edd_min_max", 'hide');
    function pi_edd_hideShowField(parent, child, show_hide) {
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

    function manageVariationDate() {
        this.init = function () {
            var parent = this;
            jQuery(document).on('woocommerce_variations_loaded woocommerce_variations_added woocommerce_variations_removed', function () {
                parent.total_variation = parent.countThrough();
                $(".pisol_edd_date_picker").datepicker({
                    dateFormat: "yy/mm/dd",
                    locale: "en",
                    showButtonPanel: true,
                    beforeShow: function (input) {
                        setTimeout(function () {
                            var buttonPane = $(input)
                                .datepicker("widget")
                                .find(".ui-datepicker-buttonpane");

                            $("<button>", {
                                text: "Clear",
                                click: function () {
                                    //Code to clear your date field (text box, read only field etc.) I had to remove the line below and add custom code here
                                    $.datepicker._clearDate(input);
                                }
                            }).appendTo(buttonPane).addClass("ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all");
                        }, 1);
                    },
                    onChangeMonthYear: function (year, month, instance) {
                        setTimeout(function () {
                            var buttonPane = $(instance)
                                .datepicker("widget")
                                .find(".ui-datepicker-buttonpane");

                            $("<button>", {
                                text: "Clear",
                                click: function () {
                                    //Code to clear your date field (text box, read only field etc.) I had to remove the line below and add custom code here
                                    $.datepicker._clearDate(instance.input);
                                }
                            }).appendTo(buttonPane).addClass("ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all");
                        }, 1);
                    }
                }).attr('readonly', 'true');
            });
        }

        this.countThrough = function () {
            var count = 0;
            var parent = this;
            jQuery(".pisol_variation_extra_date_enabler").each(function () {
                parent.pi_edd_hide_global_preparation_time(count);
                count++;
            });
            return (count);
        }

        this.pi_edd_hide_global_preparation_time = function (count) {
            var $ = jQuery;
            $(".pisol_enable_exact_date_" + count).on('change', function () {
                if ($(this).is(":checked")) {
                    $(".product_availability_date_" + count).fadeIn();

                } else {

                    $(".product_availability_date_" + count).fadeOut();
                }
            });
            jQuery(".pisol_enable_exact_date_" + count).trigger("change");
        }
    }

    var manageVariationDate_obj = new manageVariationDate();
    manageVariationDate_obj.init();


    function eddDynamicMethods() {
        this.init = function () {
            if (typeof pisol_edd_dynamic_methods_min_max == 'undefined') return;
            this.count = pisol_edd_dynamic_methods_min_max;
            this.addEvent();
            this.deleteMethod();
            this.selectWoo();
            this.sorting();
            this.setTimePicker();
            this.clearTime();
            this.changeDetection();
            this.multipleDateSelector();
        }

        this.clearTime = function () {
            jQuery(document).on('click', '.pisol-edd-clear-time', function () {
                var parent = jQuery(this).parent().parent();
                jQuery('.pisol-edd-dynamic-time-picker', parent).val('');
            });
        }

        this.sorting = function () {
            jQuery("#dynamic-methods-container").sortable();
        }

        this.addEvent = function () {
            var parent = this;
            jQuery("#add-dynamic-method").on('click', function () {
                var html = jQuery("#method-template").html();
                html = html.replace(/{{count}}/g, parent.count);
                jQuery("#dynamic-methods-container").append(html);
                jQuery('.pi_edd_dynamic_method_overwrite_shipping_off_days').trigger('change');
                parent.selectWoo();
                parent.multipleDateSelector();
                parent.count++;
            })
        }

        this.deleteMethod = function () {
            jQuery(document).on('click', '.delete-dynamic-method', function (e) {
                e.preventDefault();
                jQuery(this).parent().parent().parent().remove();
            });
        }

        this.selectWoo = function () {
            jQuery(".zone-selectwoo").selectWoo();
            jQuery(".pi_days_of_week").selectWoo({
                placeholder: 'Select non working days'
            });
        }

        this.setTimePicker = function () {
            jQuery('body').on('focus', '.pisol-edd-dynamic-time-picker', function () {
                var obj = jQuery(this).timepicker({
                    interval: 15,
                    timeFormat: "H:mm",
                    scrollbar: true,
                    dynamic: false
                });

            });
        }

        this.changeDetection = function () {
            jQuery(document).on('change', '.pi_edd_dynamic_method_overwrite_shipping_off_days', function () {
                var parent = jQuery(this).parent().parent();
                if (jQuery(this).is(":checked")) {
                    jQuery(".pi_days_of_week_container", parent).fadeIn();
                } else {
                    jQuery(".pi_days_of_week_container", parent).fadeOut();
                }
            });


            jQuery('.pi_edd_dynamic_method_overwrite_shipping_off_days').trigger('change');
        }

        this.multipleDateSelector = function () {
            jQuery(".pi-multiple-date-selector").flatpickr({
                dateFormat: 'Y/m/d',
                mode: "multiple",
                conjunction: ":"
            });
        }
    }
    var eddDynamicMethodsObj = new eddDynamicMethods();
    eddDynamicMethodsObj.init();

    function extraTimeForOutOfStock() {
        jQuery(document).on('change', ".pi-extra-time-type", function () {
            var val = jQuery(this).val();
            var single = jQuery(this).data('single');
            var range = jQuery(this).data('range');
            if (val == 'single') {
                jQuery(single).fadeIn();
                jQuery(range).fadeOut();
            } else if (val == 'range') {
                jQuery(single).fadeOut();
                jQuery(range).fadeIn();
            }
        });
        jQuery(".pi-extra-time-type").trigger('change');
    }
    extraTimeForOutOfStock();

    jQuery(document).on('woocommerce_variations_loaded woocommerce_variations_added woocommerce_variations_removed', function () {
        jQuery(".pi-extra-time-type").trigger('change');
    });

    jQuery("#pi_edd_disable_on_status").selectWoo();
});