jQuery(function ($) {

    var pisol_state = {
        zone: '',
        method: ''
    }

    var shipping_obj = new shippingNavigation();
    shipping_obj.init();

    function shippingNavigation() {
        this.init = function () {
            this.selectZone();
            this.selectMethod();
            this.submit();
            this.setTimePicker();
            this.clearCutoffTime();
            this.nonWorkingDaysChange();
            this.selectWoo();
        }

        this.clearCutoffTime = function () {
            $(document).on('click', '#pisol-form-clear-time', function () {
                $("#pisol-form-shipping_cutoff_time").val('');
            });
        }

        this.setTimePicker = function () {
            $("#pisol-form-shipping_cutoff_time").timepicker({
                "timeFormat": "H:mm", interval: 15, scrollbar: true,
                dynamic: false
            });
        }

        this.selectZone = function () {
            var parent = this;
            $(".pisol-shipping-zone").on('click', function () {
                parent.removeActive(".pisol-shipping-zone");
                $(this).addClass('pisol_active');
                var zone = $(this).data('zone');
                var method_id = ".pi_zone_method_" + zone;
                parent.hideAllMethod();
                parent.resetForm();
                $(method_id).fadeIn();
            })
        }

        this.removeActive = function (class_name) {
            $(class_name).removeClass('pisol_active');
        }

        this.hideAllMethod = function () {
            $('.pisol-shipping-method').fadeOut();
        }

        this.selectMethod = function () {
            var parent = this;
            $(".pisol-shipping-method").on('click', function () {
                parent.removeActive(".pisol-shipping-method");
                var min = $(this).data('minimum');
                var max = $(this).data('maximum');
                var zone = $(this).data('zone');
                var method = $(this).data('method');
                var method_name = $(this).data('method_name');
                var method_title = $(this).data('method_title');
                var shipping_cutoff_time = $(this).data('shipping_cutoff_time');
                var overwrite_global_shipping_off_days = $(this).data('overwrite_global_shipping_off_days');

                var pi_days_of_week = $(this).data('pi_days_of_week');

                var overwrite_global_shipping_off_dates = $(this).data('overwrite_global_shipping_off_dates');

                var holiday_dates = $(this).data('holiday_dates');

                $(this).addClass('pisol_active');
                parent.resetForm();
                parent.fillForm(min, max, zone, method, method_name, method_title, shipping_cutoff_time, overwrite_global_shipping_off_days, pi_days_of_week, overwrite_global_shipping_off_dates, holiday_dates);
            });
        }

        this.fillForm = function (min, max, zone, method, method_name, method_title, shipping_cutoff_time, overwrite_global_shipping_off_days, pi_days_of_week, overwrite_global_shipping_off_dates, holiday_dates) {
            $("#pisol-min-max-form").fadeIn();
            $('#pisol-form-minimum').val(min)
            $('#pisol-form-maximum').val(max)
            $('#pisol-form-zone').val(zone)
            $('#pisol-form-method').val(method)
            $('#pisol-form-method-name').val(method_name);
            $("#pisol-form-method-title").html(method_title);

            $('#pisol-form-shipping_cutoff_time').val(shipping_cutoff_time);

            $("#pi_days_of_week").val(pi_days_of_week);


            if (overwrite_global_shipping_off_days == 1) {
                $("#pisol-form-overwrite_global_shipping_off_days").prop('checked', true);
                this.showHideNonWorkingDaysSelector(true);
            } else {
                $("#pisol-form-overwrite_global_shipping_off_days").prop('checked', false);
                this.showHideNonWorkingDaysSelector(false);
            }

            $("#holiday_dates").val(holiday_dates);

            if (overwrite_global_shipping_off_dates == 1) {
                $("#pisol-form-overwrite_global_shipping_off_dates").prop('checked', true);
            } else {
                $("#pisol-form-overwrite_global_shipping_off_dates").prop('checked', false);
            }

            this.selectWoo();
            this.multipleDateSelector();
        }


        this.resetForm = function () {
            $("#pisol-min-max-form").fadeOut();
            $('#pisol-form-minimum').val("");
            $('#pisol-form-maximum').val("");
            $('#pisol-form-zone').val("");
            $('#pisol-form-method').val("");
            $('#pisol-form-method-name').val("");
            $('#pisol-form-shipping_cutoff_time').val("");
            $("#pisol-form-overwrite_global_shipping_off_days").prop('checked', false);
            $("#pi_days_of_week").val('');
            $("#pisol-form-overwrite_global_shipping_off_dates").prop('checked', false);
            $("#holiday_dates").val('');
        }

        this.submit = function () {
            var parent = this;
            $("#pisol-min-max-form").submit(function (e) {
                e.preventDefault();
                var min = parseInt($('#pisol-form-minimum').val());
                var max = parseInt($('#pisol-form-maximum').val());
                var zone = parseInt($('#pisol-form-zone').val());
                var method = parseInt($('#pisol-form-method').val());
                var method_name = ($('#pisol-form-method-name').val());
                var method_title = $('#pisol-form-method-title').html();
                var shipping_cutoff_time = $('#pisol-form-shipping_cutoff_time').val();
                var overwrite_global_shipping_off_days = $("#pisol-form-overwrite_global_shipping_off_days").is(":checked") ? 1 : '';

                var pi_days_of_week = $("#pi_days_of_week").val();

                var overwrite_global_shipping_off_dates = $("#pisol-form-overwrite_global_shipping_off_dates").is(":checked") ? 1 : '';

                var holiday_dates = $("#holiday_dates").val();

                if (validateForm() === true && parent.validate(min, max, zone, method, method_name) === true) {
                    $("#pisol-min-max-form").parent().addClass('pi-block-condition-row');
                    $.ajax({
                        url: ajaxurl,
                        method: 'post',
                        data: {
                            action: 'pisol_update_method',
                            min_days: min,
                            max_days: max,
                            zone: zone,
                            method: method,
                            method_name: method_name,
                            method_title: method_title,
                            shipping_cutoff_time: shipping_cutoff_time,
                            overwrite_global_shipping_off_days: overwrite_global_shipping_off_days,
                            pi_days_of_week: pi_days_of_week,
                            overwrite_global_shipping_off_dates: overwrite_global_shipping_off_dates,
                            holiday_dates: holiday_dates
                        },
                        success: function (result) {
                            parent.error(result);
                            if (result.includes('successfully')) {
                                $("#pisol-method-" + method).data('minimum', min);
                                $("#pisol-method-" + method).data('maximum', max);


                                $("#pisol-method-" + method).data('shipping_cutoff_time', shipping_cutoff_time);

                                $("#pisol-method-" + method).data('overwrite_global_shipping_off_days', overwrite_global_shipping_off_days);

                                $("#pisol-method-" + method).data('pi_days_of_week', pi_days_of_week);

                                $("#pisol-method-" + method).data('overwrite_global_shipping_off_dates', overwrite_global_shipping_off_dates);

                                $("#pisol-method-" + method).data('holiday_dates', holiday_dates);
                            }
                        }
                    }).always(function () {
                        $("#pisol-min-max-form").parent().removeClass('pi-block-condition-row');
                    })
                }
            })
        }

        this.multipleDateSelector = function () {
            jQuery(".pi-multiple-date-selector").flatpickr({
                dateFormat: 'Y/m/d',
                mode: "multiple",
                conjunction: ":"
            });
        }

        this.validate = function (min, max, zone, method, method_name) {
            if (isNaN(min) || isNaN(max) || isNaN(zone) || isNaN(method)) {
                this.error('Minimum and Maximum days should be integer number');
                return false;
            }

            if (method_name == "") {
                this.error('There is some error please refresh the page and try again');
                return false;
            }

            if (parseInt(min) > parseInt(max)) {
                this.error('Maximum days should be grater then or equal to the Minimum days');
                return false;
            }

            return true;
        }

        this.error = function (message) {
            var html = '<div class="alert alert-warning">' + message + '</div>';
            $(".pisol-error").html(html);
        }

        this.showHideNonWorkingDaysSelector = function (show = true) {
            if (show) {
                $("#non-working-days").fadeIn();
            } else {
                $("#non-working-days").fadeOut();
            }
        }

        this.nonWorkingDaysChange = function () {
            var parent = this;
            $(document).on('change', "#pisol-form-overwrite_global_shipping_off_days", function () {
                if ($(this).is(":checked")) {
                    parent.showHideNonWorkingDaysSelector(true);
                } else {
                    parent.showHideNonWorkingDaysSelector(false);
                }
            });
        }

        this.selectWoo = function () {
            $("#pi_days_of_week").selectWoo({ placeholder: 'Select non working days' });
        }

    }

});