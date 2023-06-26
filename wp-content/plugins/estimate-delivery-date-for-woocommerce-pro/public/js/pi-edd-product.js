(function ($) {
  "use strict";
  /* 4.4.9.6 */
  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  jQuery(document).ready(function ($) {

    function variableProduct() {
      this.init = function () {
        this.variationChange();
        this.variationReset();
        this.customTrigger();
      }

      this.customTrigger = function () {
        var parent = this;
        jQuery(document).on('pi_edd_custom_get_estimate_trigger', function (e, product_id, variable_id) {

          parent.selectEstimate(product_id, variable_id);
        });
      }

      this.variationChange = function () {
        var parent = this;
        $(document).on('show_variation', "form.variations_form", function (event, data) {
          var form = $(event.target).closest('form.variations_form');
          var product_id = form.data('product_id');
          if (data != undefined) {
            var variation_id = data.variation_id;
            if (data.is_in_stock) {
              if (variation_id != "" && variation_id != 0 && !isNaN(variation_id)) {
                parent.selectEstimate(product_id, variation_id);
              } else {
                parent.noVariationSelected(product_id);
              }
            } else {
              parent.outOfStockMessage(product_id);
            }
          }
        });
      }

      this.variationReset = function () {
        var parent = this;
        $(document).on('reset_data', "form.variations_form", function (event, data) {
          var form = $(event.target).closest('form.variations_form');
          var product_id = form.data('product_id');
          parent.noVariationSelected(product_id);
        });
      }

      this.noVariationSelected = function (product_id) {
        var parent = this;
        if (pi_edd_variable.showFirstVariationEstimate == 'first-variation') {
          var form = $("form.variations_form");
          var variation_data = form.data("product_variations");
          if (variation_data != undefined && variation_data[0] != undefined && variation_data[0].variation_id != undefined) {
            var variation_id = variation_data[0].variation_id;
            parent.selectEstimate(product_id, variation_id);
          }
        } else {
          jQuery(".pi-edd-estimate-" + product_id).each(function () {
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
        jQuery(".pi-edd-estimate-" + product_id).each(function () {
          var estimates = pi_edd_variable.out_of_stock_message;

          if (estimates == "") {
            parent.hideContainer(product_id);
          } else {
            parent.showContainer(product_id);
            jQuery(this).html(estimates);
          }
        })
      }

      this.selectEstimate = function (product_id, variation_id) {
        var parent = this;
        jQuery(".pi-edd-estimate-" + product_id).each(function () {
          var estimates = (jQuery(this).data('estimates'));
          if (estimates[variation_id] != null) {
            parent.showContainer(product_id);
            jQuery(this).html(estimates[variation_id]);
          } else {
            parent.hideContainer(product_id);
          }
        })
      }

      this.hideContainer = function (product_id) {
        jQuery(".pi-edd-estimate-" + product_id).fadeOut(1);
      }

      this.showContainer = function (product_id) {
        jQuery(".pi-edd-estimate-" + product_id).fadeIn(1);
      }


    }
    var variableProductObj = new variableProduct();
    variableProductObj.init();

  });



})(jQuery);
