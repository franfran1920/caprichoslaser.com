== 4.7.9 ==
* max preparation time added
* preparation time will be added in exact product arrival date and lot arrival date as well 
* new design for product page
* text align on product and loop page
* pisol_edd_filter_shipping_method_settings filter added 
* option to view shipping method name on checkout page

== 4.7.10.1 ==
* fix compatibility with Advance shipping by Jeroen Sormani
* filter added for out of stock type

== 4.7.10.3
* bulk insertion of min, max preparation time option given

== 4.7.10.4 ==
* Shop close dates added
* bug fix: no estimate shown on the loop if first variation was out of stock

== 4.7.10.7 ==
* shipping method based cutoff time filter added in
* new filter function added in pisol_edd_is_on_backorder
* filter added to change estimate message text

== 4.7.13 ==
* shipping method based cutoff time option in plugin

== 4.7.17.10 ==
* wpml-config.xml added to force copying of value in translated product
* Rest API support for https://wordpress.org/plugins/custom-shipping-methods-for-woocommerce/
* Added support for https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/
* New calculation method for order estimate 
* msg filter extended

== 4.7.17.13 ==
* pi_edd_disable_estimate_in_pdf filter to disable estimate in pdf invoice and packing slip

== 4.7.17.14 ==
* set shipping method holiday dates
* code improvement on setting class

== 4.7.17.17 ==
* changes in 4.7.17.13 caused issue with translation that was fixed in this version

== 4.7.19 ==
* setting exact arrival date from the shipping class

== 4.7.20 ==
* wc-ajax implemented for front end ajax request

== 4.7.20.1 ==
* Made compatible with Hpos data base structure

== 4.7.20.2 ==
* date_i18n adjusted so it get utc time stamp only

== 4.7.20.3 ==
* WooCommerce cart and checkout block support added in

== 4.7.20.4 ==
* Quick save floating button added in

== 4.7.20.6 ==
* Shipping days going blank if shipping method where saved bug fixed

== 4.7.20.7 ==
* Added message option in the product estimate short code
* Given short code for [pi_min_date id=""] [pi_max_date] [pi_min_days] [pi_max_days] [pi_date] [pi_days]

== 4.7.20.10 ==
* WC 7.5 fetch support added

== 4.7.20.11 ==
* WC 7.5 fetch support for address change on cart page

== 4.7.20.12 ==
* 2 new filter function added in class-order.php and class-cart.php

== 4.7.20.13 ==
* Alt tag in the icon 
* subscription renewal error removed 

== 4.7.20.14 ==
* 3 new filters added to change the order and shipping estimate msg based on the shipping method selected 
pisol_edd_shipping_method_msg
pisol_edd_order_estimate_msg
pisol_edd_order_estimate_msg_email