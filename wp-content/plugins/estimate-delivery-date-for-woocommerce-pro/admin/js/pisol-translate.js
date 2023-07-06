(function ($) {
	'use strict';

	jQuery(function ($) {

		var data = window.pi_edd_saved_translations;

		if (data != undefined) {
			for (var i = 0; i < data.length; i++) {
				data[i].count = i;
				addTranslation(data[i]);
			}


			var count = data.length == 0 ? 0 : (data.length);
		}
		$(document).on("click", ".btn-remove", function () {
			$(this).parent().parent().remove();
		});

		$(document).on("click", "#btn-edd-add-translation", function () {
			addTranslation({
				count: count,
				language: "",
				pi_product_page_text: "Estimated delivery date {date}",
				pi_product_page_text_range: "Estimated delivery between {min_date} - {max_date}",
				pi_product_page_text_back_order: "Estimated delivery date {date}",
				pi_product_page_text_range_back_order: "Estimated delivery between {min_date} - {max_date}",
				pi_loop_page_text: "Estimated delivery date {date}",
				pi_loop_page_text_range: "Estimated delivery between {min_date} - {max_date}",
				pi_loop_page_text_back_order: "Estimated delivery date {date}",
				pi_loop_page_text_range_back_order: "Estimated delivery between {min_date} - {max_date}",
				pi_cart_page_text: "Estimated delivery date {date}",
				pi_cart_page_text_range: "Estimated delivery between {min_date} - {max_date}",
				pi_cart_page_text_back_order: "Estimated delivery date {date}",
				pi_cart_page_text_range_back_order: "Estimated delivery between {min_date} - {max_date}",
				pi_overall_estimate_text: "Overall estimated delivery date {date}",
				pi_overall_estimate_range_text: "Order estimated delivery between {min_date} - {max_date}",
				pi_edd_estimate_message_below_shipping_method_range: "Delivery by {min_date} - {max_date}",
				pi_edd_estimate_message_below_shipping_method_single_date: "Delivery by {date}",
				pi_edd_estimate_message_tomorrow_delivery: 'Delivery by Tomorrow',
				pi_edd_estimate_message_same_day_delivery: 'Delivery by Today',
				pi_product_out_off_stock_estimate_msg: 'Out of stock product',
				pi_no_variation_selected_msg: 'Select a product variation to get estimate'
			});
			count++;
		});

		$("#pi_days_of_week").selectWoo();
		$("#pi_shop_closed_days").selectWoo();
	});

	function addTranslation(data) {
		var tmpl = $.templates("#pi_translate");
		var html = tmpl.render(data);
		$("#pi_edd_translation_container").append(html);
	}



})(jQuery);
