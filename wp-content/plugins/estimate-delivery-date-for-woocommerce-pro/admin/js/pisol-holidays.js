(function ($) {
	'use strict';
	jQuery(document).ready(function ($) {
		const flatpickr1 = $("#pi-holiday-calender").flatpickr({
			dateFormat: 'Y/m/d',
			mode: "multiple",
			conjunction: ":",
			defaultDate: jQuery("#pi_edd_holidays").val(),
			inline: true,
			onChange: function (selectedDates, dateStr, instance) {
				$("#pi_edd_holidays").val(dateStr);
			}
		});

		$("#reset-holidays").click(function () {
			$("#pi_edd_holidays").val("");
			flatpickr1.clear();
			jQuery(this).closest('form').trigger('submit');
		});

		const flatpickr2 = $('#pi-shop-holiday-calender').flatpickr({
			dateFormat: 'Y/m/d',
			mode: "multiple",
			conjunction: ":",
			defaultDate: jQuery("#pi_edd_shop_holidays").val(),
			inline: true,
			onChange: function (selectedDates, dateStr, instance) {
				$("#pi_edd_shop_holidays").val(dateStr);
			}
		});

		$("#reset-shop-holidays").click(function () {
			$("#pi_edd_shop_holidays").val("");
			flatpickr2.clear();
			jQuery(this).closest('form').trigger('submit');
		});
	});

})(jQuery);