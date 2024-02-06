<?php

class Class_Pi_Edd_Translate{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'translate';

    private $tab_name = "Translate";

    private $setting_key = 'pi_sn_translate_setting';

    
    private $date_format = array();
    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;
        
        
        $this->settings = array(
            array('field'=>'pi_edd_translate_message')
        );
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
            
        }

        add_action($this->plugin_name.'_tab', array($this,'tab'),3);
        

       
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
        <a class=" pi-side-menu  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-translation"></span> <?php _e( $this->tab_name); ?> 
        </a>
        <?php
    }

    function tab_content(){
       $saved_translations = get_option('pi_edd_translate_message',array());
       ?>
        <script>

            var pi_edd_saved_translations = <?php echo json_encode(array_values((is_array($saved_translations) ? $saved_translations : array())  )); ?>
        </script>
        <script id="pi_translate" type="text/x-jsrender">
            <div class="row pt-4 border-bottom align-items-center ">    
			<div class="col-12 col-md-8">
            <?php
                $languages = $this->getLanguages();
                echo '<select name="pi_edd_translate_message[{{:count}}][language]" class="form-control">';
                    foreach($languages as $language){
                        $val = !empty($language['value']) ? $language['value'] : 'en_US'; 
                        echo '<option value="'.$val.'" lang="'.$language['lang'].'" {{if language == "'.$language['value'].'"}}selected="selected"{{/if}}>'.$language['name'].' - '.$language['value'].'</option>';
                    }
                echo '</select>';
            ?>
            </div>
            <div class="col-12 col-md-4 text-right">
            <button class="btn btn-warning btn-remove">Remove Translation</button>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Estimated date wording on product page </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_product_page_text]" class="form-control" value="{{:pi_product_page_text}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Estimated date wording on product page, for date range</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_product_page_text_range]" class="form-control" value="{{:pi_product_page_text_range}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Back order estimated date wording on product page </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_product_page_text_back_order]" class="form-control" value="{{:pi_product_page_text_back_order}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Back order estimated date wording on product page for date range</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_product_page_text_range_back_order]" class="form-control" value="{{:pi_product_page_text_range_back_order}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Estimated date wording on category/shop page </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_loop_page_text]" class="form-control" value="{{:pi_loop_page_text}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Estimated date wording on category/shop page for date range</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_loop_page_text_range]" class="form-control" value="{{:pi_loop_page_text_range}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Back order estimated date wording on category/shop page </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_loop_page_text_back_order]" class="form-control" value="{{:pi_loop_page_text_back_order}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Back order estimated date wording on category/shop page, for date range</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_loop_page_text_range_back_order]" class="form-control" value="{{:pi_loop_page_text_range_back_order}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Estimated date wording on cart/checkout page </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_cart_page_text]" class="form-control" value="{{:pi_cart_page_text}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Estimated date wording on cart/checkout page, for date range</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_cart_page_text_range]" class="form-control" value="{{:pi_cart_page_text_range}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Back order estimated date wording on cart/checkout page </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_cart_page_text_back_order]" class="form-control" value="{{:pi_cart_page_text_back_order}}">
                    </div>
                </div>
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Back order estimated date wording on cart/checkout page, for date range</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_cart_page_text_range_back_order]" class="form-control" value="{{:pi_cart_page_text_range_back_order}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Estimated date wording on Overall order estimate </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_overall_estimate_text]" class="form-control" value="{{:pi_overall_estimate_text}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Estimated date wording on overall order estimate as range </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_overall_estimate_range_text]" class="form-control" value="{{:pi_overall_estimate_range_text}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Shipping method single estimate message template </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_edd_estimate_message_below_shipping_method_single_date]" class="form-control" value="{{:pi_edd_estimate_message_below_shipping_method_single_date}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Shipping method range estimate message template </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_edd_estimate_message_below_shipping_method_range]" class="form-control" value="{{:pi_edd_estimate_message_below_shipping_method_range}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Same day delivery message</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_edd_estimate_message_same_day_delivery]" class="form-control" value="{{:pi_edd_estimate_message_same_day_delivery}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Tomorrow delivery message</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_edd_translate_message[{{:count}}][pi_edd_estimate_message_tomorrow_delivery]" class="form-control" value="{{:pi_edd_estimate_message_tomorrow_delivery}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>No Variation selected message</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input name="pi_edd_translate_message[{{:count}}][pi_no_variation_selected_msg]" class="form-control" value="{{:pi_no_variation_selected_msg}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>Message for out of stock product</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input name="pi_edd_translate_message[{{:count}}][pi_product_out_off_stock_estimate_msg]" class="form-control" value="{{:pi_product_out_off_stock_estimate_msg}}">
                    </div>
                </div>
            </div>
            </div>
        </script>
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <div class="row py-4 border-bottom align-items-center bg-primary text-light">
            <div class="col-12">
            <h2 class="mt-0 mb-0 text-light font-weight-light h4">Add translation for the estimate message</h2>
            </div>
        </div>
        <div id="pi_edd_translation_container">

        </div>
        <button type="button" class="btn btn-primary my-2" id="btn-edd-add-translation">Add Translation</button><br>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }

    function getLanguages(){
        $languages = array();
        $args = array('echo' => 0); 
        $html = wp_dropdown_languages( $args );
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        libxml_clear_errors();
        $options = $dom->getElementsByTagName('option');
        foreach ($options as $option){
            $value = $option->getAttribute('value');
            $lang = $option->getAttribute('lang');
            $name = $option->nodeValue;
            $languages[] = array( 'value'=>$value, 'name'=> $name, 'lang'=>$lang);
        }
        return $languages;
    }
    
}

