<?php

class Class_Pi_Edd_Message{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'message';

    

    private $setting_key = 'message_settting';

    

    


    function __construct($plugin_name){

        $this->product_location = array(
            'woocommerce_before_add_to_cart_button'=>__('Before add to cart button', 'pi-edd'), 'woocommerce_after_add_to_cart_button'=>__('After add to cart button', 'pi-edd'),
            'woocommerce_before_add_to_cart_form'=>__('Before add to cart form', 'pi-edd'),
            'woocommerce_after_add_to_cart_form'=>__('After add to cart form', 'pi-edd'), 
        );
        $this->category_location = array(
            'woocommerce_after_shop_loop_item_title'=>__('After title', 'pi-edd'), 'woocommerce_shop_loop_item_title'=>__('After Image', 'pi-edd'), 
            'woocommerce_after_shop_loop_item'=>__('After price', 'pi-edd')
        );


        $this->tab_name = __("Advance setting", 'pi-edd');

        $this->plugin_name = $plugin_name;
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        add_action('woocommerce_init', array($this,'shipping_zone_to_array'));

        add_action($this->plugin_name.'_tab', array($this,'tab'),3);

        
        
        
    }

    function shipping_zone_to_array(){
      
        
        
        $this->settings = array(
            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Single product page setting",'pi-edd'), 'type'=>"setting_category"),

            array('field'=>'pi_show_product_page', 'label'=>__('Show estimate on single product page','pi-edd'),'type'=>'switch', 'default'=>1,   'desc'=>__('Show estimate date on product page','pi-edd')),

            array('field'=>'pi_show_single_estimate_by_ajax', 'label'=>__('Load estimate by Ajax','pi-edd'),'type'=>'switch', 'default'=>0,   'desc'=>__('Ajax will help you avoid caching','pi-edd')),
            
            array('field'=>'pi_product_page_text', 'label'=>__('Estimated date, Wording on product page','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery date {date}',  'desc'=>__('This will be shown besides the estimated date on single product page, PRO version allows you to show more custom message using short code {date}, {days} to show estimate as number of days count, {icon}','pi-edd')),

            array('field'=>'pi_product_page_text_range', 'label'=>__('Estimated date, Wording on product page, for date range','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery between {min_date} - {max_date}',  'desc'=>__('This will be shown besides the estimated date range on single product page when showing date range, PRO version allows you to show more custom message using short code {min_date}, {max_date}, {min_days} or {max_days} as estimate in terms of days count, {icon}','pi-edd')),

            array('field'=>'pi_product_page_text_back_order', 'label'=>__('Back order estimated date wording on product page','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery date {date}',  'desc'=>__('This will be shown besides the estimated date on single product page, PRO version allows you to show more custom message using short code {date}, {days} to show estimate as number of days count, {icon}','pi-edd')),

            array('field'=>'pi_product_page_text_range_back_order', 'label'=>__('Back order estimated date wording on product page, for date range','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery between {min_date} - {max_date}',  'desc'=>__('This will be shown besides the estimated date range on single product page when showing date range, PRO version allows you to show more custom message using short code {min_date}, {max_date}, {min_days} or {max_days} as estimate in terms of days count, {icon}','pi-edd')),
            
            array('field'=>'pi_product_page_position', 'label'=>__('Position on single product page','pi-edd'),'type'=>'select', 'default'=>'woocommerce_before_add_to_cart_button',   'desc'=>__('Estimate position on single product page','pi-edd'), 'value'=>$this->product_location),
            
            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Shop / Category page setting",'pi-edd'), 'type'=>"setting_category"),

            array('field'=>'pi_show_product_loop_page', 'label'=>__('Show estimate on product loop page','pi-edd'),'type'=>'switch', 'default'=>1,  'desc'=>__('Show estimate date on shop page or product category page','pi-edd')),

            array('field'=>'pi_show_loop_estimate_by_ajax', 'label'=>__('Load estimate by Ajax','pi-edd'),'type'=>'switch', 'default'=>0,   'desc'=>__('Ajax will help you avoid caching','pi-edd')),

            array('field'=>'pi_show_variable_product_estimate_on_loop', 'label'=>__('Show Variable product estimate on archive page','pi-edd'),'type'=>'switch', 'default'=>0,  'desc'=>__('If you enable this then it will show the estimate of the first variation on the archive page, if disabled it will not show estimate for variable product on archive page','pi-edd')),
            
            array('field'=>'pi_loop_page_text', 'label'=>__('Estimated date, Wording on category / shop page','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery date {date} ',  'desc'=>__('This will be shown besides the estimated date on category or shop page, PRO version allows you to show more custom message using short code {date}, {days} to show estimate as number of days count, {icon}','pi-edd')),

            array('field'=>'pi_loop_page_text_range', 'label'=>__('Estimated date, Wording on category / shop page, for date range','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery between {min_date} - {max_date}',  'desc'=>__('This will be shown besides the estimated date range on category or shop page when showing date range, PRO version allows you to show more custom message using short code {min_date}, {max_date},  {min_days} or {max_days} as estimate in terms of days count, {icon}','pi-edd')),

            array('field'=>'pi_loop_page_text_back_order', 'label'=>__('Back order estimated date, Wording on category / shop page','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery date {date} ',  'desc'=>__('This will be shown besides the estimated date on category or shop page, PRO version allows you to show more custom message using short code {date}, {days} to show estimate as number of days count, {icon}','pi-edd')),

            array('field'=>'pi_loop_page_text_range_back_order', 'label'=>__('Back order estimated date wording on category / shop page, for date range','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery between {min_date} - {max_date}',  'desc'=>__('This will be shown besides the estimated date range on category or shop page when showing date range, PRO version allows you to show more custom message using short code {min_date}, {max_date},  {min_days} or {max_days} as estimate in terms of days count, {icon}','pi-edd')),

            array('field'=>'pi_loop_page_position', 'label'=>__('Position on category / shop page','pi-edd'),'type'=>'select', 'default'=>'woocommerce_after_shop_loop_item_title',   'desc'=>__('Estimate position on single product page','pi-edd'), 'value'=>$this->category_location),

            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Cart / Checkout page setting",'pi-edd'), 'type'=>"setting_category"),

            array('field'=>'pi_show_cart_page', 'label'=>__('Show estimate on cart page for each product','pi-edd'),'type'=>'switch', 'default'=>1,   'desc'=>__('Show estimate date on cart page for each product','pi-edd')),

            array('field'=>'pi_show_checkout_page', 'label'=>__('Show estimate on checkout page for each product','pi-edd'),'type'=>'switch', 'default'=>1,   'desc'=>__('Show estimate date on checkout page for each product','pi-edd')),
           
            
            
            
           

            
            array('field'=>'pi_edd_cart_page_show_overall_estimate', 'label'=>__('Show estimate for complete order on checkout page','pi-edd'),'type'=>'switch', 'default'=>1,   'desc'=>__('When you enable this it will show the estimate time for the complete cart, (it takes estimate of all the product in cart and show the largest date as the estimate,<strong>if estimate date of Product A = March 5, Product B = March 7 the the estimate of complete cart will be March 7</strong>)','pi-edd')),

            array('field'=>'pi_edd_show_overall_estimate_on_cart_page', 'label'=>__('Show estimate for complete order on cart page','pi-edd'),'type'=>'switch', 'default'=>1,   'desc'=>__('When you enable this it will show the estimate time for the complete cart, (it takes estimate of all the product in cart and show the largest date as the estimate,<strong>if estimate date of Product A = March 5, Product B = March 7 the the estimate of complete cart will be March 7</strong>)','pi-edd')),
            
            array('field'=>'pi_cart_page_text', 'label'=>__('Estimated date, Wording on cart / checkout page','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery date {date}',  'desc'=>__('This will be shown besides the estimated date on cart or checkout page, PRO version allows you to show more custom message using short code {date}, {days} to show estimate as number of days count','pi-edd')),

            array('field'=>'pi_cart_page_text_range', 'label'=>__('Estimated date, Wording on cart / checkout page, for date range','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery between {min_date} - {max_date}',  'desc'=>__('This will be shown besides the estimated date range on cart or checkout page when showing date range, PRO version allows you to show more custom message using short code {min_date}, {max_date}, {min_days} or {max_days} as estimate in terms of days count','pi-edd')),

            array('field'=>'pi_cart_page_text_back_order', 'label'=>__('Back order estimated date wording on cart / checkout page','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery date {date}',  'desc'=>__('This will be shown besides the estimated date on cart or checkout page, PRO version allows you to show more custom message using short code {date}, {days} to show estimate as number of days count','pi-edd')),

            array('field'=>'pi_cart_page_text_range_back_order', 'label'=>__('Back order estimated date wording on cart / checkout page, for date range','pi-edd'),'type'=>'text', 'default'=>'Estimated delivery between {min_date} - {max_date}',  'desc'=>__('This will be shown besides the estimated date range on cart or checkout page when showing date range, PRO version allows you to show more custom message using short code {min_date}, {max_date}, {min_days} or {max_days} as estimate in terms of days count','pi-edd')),

            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Order detail and Order email",'pi-edd'), 'type'=>"setting_category"),

            array('field'=>'pi_edd_disable_on_status', 'label'=>__('Don\'t show estimate date in the order with this status','pi-edd'),'type'=>'multiselect', 'default'=> array('cancelled','failed', 'refunded'), 'value'=> $this->orderStatus(),  'desc'=>__('estimate dates will not be shown in the email send for this order status','pi-edd')),

            array('field'=>'pi_edd_cart_page_show_single_estimate', 'label'=>__('Add estimate date for each product in stored order and email','pi-edd'),'type'=>'switch', 'default'=>0,   'desc'=>__('It will add estimate for each product in : order stored in backend, order email send to admin and customer','pi-edd')),
           
            array('field'=>'pi_edd_show_overall_estimate_in_email', 'label'=>__('Show estimate for complete order in order email','pi-edd'),'type'=>'switch', 'default'=>1,   'desc'=>__('When you enable this it will show the estimate time for the complete order in order email, (it takes estimate of all the product in cart and show the largest date as the estimate,<strong>if estimate date of Product A = March 5, Product B = March 7 the the estimate of complete cart will be March 7</strong>)','pi-edd')),

            array('field'=>'pi_edd_show_overall_estimate_in_order_success_page', 'label'=>__('Show estimate for complete order in order success page','pi-edd'),'type'=>'switch', 'default'=>1,   'desc'=>__('When you enable this it will show the estimate time for the complete order on order success page, (it takes estimate of all the product in cart and show the largest date as the estimate,<strong>if estimate date of Product A = March 5, Product B = March 7 the the estimate of complete cart will be March 7</strong>)','pi-edd')),

            array('field'=>'pi_order_estimate_calculation_method', 'label'=>__('Calculation method used for order estimate','pi-edd'),'type'=>'select', 'default'=>'woocommerce_after_shop_loop_item_title',   'desc'=>__('Calculation method used for showing overall order estimate  <a href="https://www.piwebsolution.com/woocommerce-estimated-delivery-date-per-product/#Overall_order_estimate_date_calculation_option" target="_blank">Read more..</a>','pi-edd'), 'value'=>array('smallest-larges'=>"Show smallest and largest date", 'first-second-largest'=>'Show first and second largest date',
            'largest-of-product' => 'Show the Product with longest estimate date')),

            array('field'=>'pi_overall_estimate_text', 'label'=>__('Overall estimate wording','pi-edd'),'type'=>'text', 'default'=>'Order estimated delivery date {date}',  'desc'=>__('This will show the overall estimate of the order on checkout page and order email, and stored in the order using short code {date}, {days} to show estimate as number of days count','pi-edd')),

            array('field'=>'pi_overall_estimate_range_text', 'label'=>__('Overall estimate wording for range','pi-edd'),'type'=>'text', 'default'=>'Order estimated delivery between {min_date} - {max_date}',  'desc'=>__('This will show the overall estimate of the order on checkout page and order email, and stored in the order, using short code {min_date}, {min_days}, {max_date}, {max_days} to show estimate as number of days count','pi-edd')),

            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Show estimated time below each shipping methods on cart/checkout",'pi-edd'), 'type'=>"setting_category"),

            array('field'=>'pi_edd_show_estimate_on_each_method', 'label'=>__('Add estimate date for each of the shipping method below their name on cart/checkout page','pi-edd'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will show the estimated time for each shipping method in a zone just below their name, so the user can select the based needed method for faster delivery','pi-edd')),

            array('field'=>'pi_edd_estimate_message_below_shipping_method_range', 'label'=>__('Message template to show range of estimated time below each shipping method','pi-edd'),'type'=>'text', 'default'=>'Delivery by {min_date} - {max_date}',  'desc'=>__('This will be shown below each shipping method name on cart/checkout page, using short code {min_date}, {min_days}, {max_date}, {max_days} to show estimate as number of days count','pi-edd')),

            array('field'=>'pi_edd_estimate_message_below_shipping_method_single_date', 'label'=>__('This template is used when Min and Max estimate date are same','pi-edd'),'type'=>'text', 'default'=>'Delivery by {date}',  'desc'=>__('This message will be used when the min and max estimate is same date, and using the range template will be useless <strong>(E.g this will be meaning less Delivery by 3rd Mach - 3rd March) so we use this template</strong>, use short code {date} or {days}','pi-edd')),

            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Special wording when estimate comes out to be Today/Tomorrow",'pi-edd'), 'type'=>"setting_category"),
            
            array('field'=>'pi_edd_enable_special_wording_same_day_delivery', 'label'=>__('Enable special message to show for same day delivery','pi-edd'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will allow you to show special message when the delivery estimate is same day','pi-edd')),

            array('field'=>'pi_edd_estimate_message_same_day_delivery', 'label'=>__('Same day delivery message','pi-edd'),'type'=>'text', 'default'=>'Delivery by Today',  'desc'=>__('This message will be shown as estimate, when the min and max estimate date is same and that date is today itself (that is for same day delivery) you can use icon here {icon}','pi-edd')),

            array('field'=>'pi_edd_enable_special_wording_tomorrow_delivery', 'label'=>__('Enable special message to show for Tomorrow estimate','pi-edd'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will allow you to show special message when the delivery estimate for tomorrow','pi-edd')),

            array('field'=>'pi_edd_estimate_message_tomorrow_delivery', 'label'=>__('Tomorrow delivery message','pi-edd'),'type'=>'text', 'default'=>'Delivery by Tomorrow',  'desc'=>__('This message will be shown as estimate, when the min and max estimate date is same and that date is today itself (that is for same day delivery) you can use icon here {icon}','pi-edd')),

            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Estimate msg for out of stock product / Variable product estimate when no estimate selected",'pi-edd'), 'type'=>"setting_category"),
            
            array('field'=>'pi_product_out_off_stock_estimate_msg', 'label'=>__('Estimate message shown when product is out of stock','pi-edd'), 'type'=>'text', 'default'=>'Out of stock product',  'desc'=>__('This message will be shown when product is out of stock','pi-edd')),

            array('field'=>'pi_no_variation_selected_msg', 'label'=>__('Message shown when user has not selected any variation','pi-edd'), 'type'=>'text', 'default'=>'Select a product variation to get estimate',  'desc'=>__('This message will be shown on variable product page when user has not selected any variation','pi-edd')),
            
            array('field'=>'pi_edd_show_default_estimate_for_variable_product', 'label'=>__('Show default estimate for the variable product ','pi-edd'), 'type'=>'select','value'=>array('select-variation-msg'=>'Show message to select variation','first-variation'=>'Select first variation estimate'), 'default'=>'select-variation-msg',  'desc'=>__('When user first lands on the variable product page and no variation is selected then show this estimate','pi-edd')),

            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Disable estimate for shipping method",'pi-edd'), 'type'=>"setting_category"),

            array('field'=>'pi_edd_dont_show_estimate_for_method', 'label'=>__('Disable estimate for shipping method','pi-edd'),'type'=>'text', 'default'=>'',  'desc'=>__('You can disable the estimate for shipping method by inserting there system name <a href="https://youtu.be/FKeSkDLfSKI" target="_blank">Check out how you can find shipping method system name</a> <br>E.g: flat_rate:23, free_shipping:33','pi-edd')),
        );
        $this->register_settings();
        
        if(PISOL_EDD_DELETE_SETTING){
            $this->delete_settings();
        }
    }

    
    function delete_settings(){
        foreach($this->settings as $setting){
            delete_option( $setting['field'] );
        }
    }


    function register_settings(){   

        foreach($this->settings as $setting){
            register_setting( $this->setting_key, $setting['field']);
        }
    
    }

    function tab(){
        ?>
        <a class=" pi-side-menu <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-dashboard"></span> <?php _e( $this->tab_name, 'http2-push-content' ); ?> 
        </a>
        <?php
    }

    function tab_content(){
       ?>
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_edd($setting, $this->setting_key);
            }
        ?>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }

    function orderStatus(){
       
        $order_states = wc_get_order_statuses();
        $formated_states = array();
        foreach($order_states as $key => $val){
             $new_key = str_replace('wc-', '', $key);
             $formated_states[$new_key] = $val;
        }
        return $formated_states;
     }
}


