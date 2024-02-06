<?php

class Pi_Edd_Menu{

    public $plugin_name;
    public $menu;
    
    function __construct($plugin_name , $version){
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action( 'admin_menu', array($this,'plugin_menu') );
        add_action('admin_notices', array($this, 'criticalNotice'));

        add_action('admin_notices',array($this,'validateDefMinMax'));
    }

    function criticalNotice(){
        $default_zone_id = get_option('pi_defaul_shipping_zone',0);
        if($default_zone_id == "" || $default_zone_id == 0 ){
            echo "<div class='notice notice-error is-dismissible'>
                        <h3>Estimate delivery date for WooCommerce Pro</h3>
                        <p>You must select a <strong>Default shipping Zone</strong>, without this you wont see any estimated shipping date on the website </p>
                        <p>Go to <a href='".admin_url("admin.php?page=pi-edd&tab=basic_setting")."'>Plugin Settings</a> to correct this</p>
                        </div>";
        }

        if(!pisol_checking::checkZones()){
            echo "<div class='notice notice-error is-dismissible'>
                        <h3>Estimate delivery date for Woocommerce Pro</h3>
                        <p>You must have shipping zones to use this setting, so create shipping zone in WooCommerce <a href='".admin_url("admin.php?page=wc-settings&tab=shipping")."'>Click here to set shipping zone</p>
                </div>";
        }
    }

    function plugin_menu(){

        $require_capability = pisol_edd_access_control::getCapability();
        
        $this->menu = add_submenu_page(
            'woocommerce',
            __( 'Estimate Date', 'pi-edd' ),
            'Estimate delivery date',
            $require_capability,
            'pi-edd',
            array($this, 'menu_option_page'),
            6
        );

        add_action("load-".$this->menu, array($this,"bootstrap_style"));
 
    }

    public function bootstrap_style() {
        add_thickbox();
        wp_enqueue_script( 'selectWoo');
		wp_enqueue_style( $this->plugin_name."_bootstrap", plugin_dir_url( __FILE__ ) . 'css/bootstrap.css', array(), $this->version, 'all' );
        
        wp_enqueue_style( $this->plugin_name."_flatpickr", plugin_dir_url( __FILE__ ) . 'css/flatpickr.min.css', array(), $this->version, 'all' );
       
        wp_enqueue_script( $this->plugin_name."_flatpick", plugin_dir_url( __FILE__ ) . 'js/flatpickr.min.js', array('jquery'), $this->version );
        wp_enqueue_script( $this->plugin_name."_holidays", plugin_dir_url( __FILE__ ) . 'js/pisol-holidays.js', array('jquery'), $this->version );
        wp_enqueue_script( $this->plugin_name."_jsrender", plugin_dir_url( __FILE__ ) . 'js/jsrender.min.js', array('jquery'), $this->version );
        wp_enqueue_script( $this->plugin_name."_translate", plugin_dir_url( __FILE__ ) . 'js/pisol-translate.js', array('jquery',$this->plugin_name."_jsrender", 'selectWoo'), $this->version );

        /* 4/7/19 */
        wp_enqueue_script( $this->plugin_name."_shipping", plugin_dir_url( __FILE__ ) . 'js/pisol-shipping.js', array('jquery',$this->plugin_name."_jsrender"), $this->version );
        /* 4/7/19 */
        
        wp_enqueue_style($this->plugin_name."_timepicker", plugin_dir_url( __FILE__ ) . 'css/jquery.timepicker.min.css');
        wp_enqueue_script( $this->plugin_name."_timepicker", plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.min.js', array('jquery'), $this->version );

        wp_enqueue_script( $this->plugin_name."_quick_save", plugin_dir_url( __FILE__ ) . 'js/pisol-quick-save.js', array('jquery'), $this->version, 'all' );
        
        $time_picker = '
            jQuery(document).ready(function($){

                function clearValue(id) {
                    $("<a class=\'pi-clear-value btn btn-danger text-light\'>Clear Value</a>").insertAfter("#" + id);
                }

                clearValue("pi_shipping_breakup_time");

                $(".pi-clear-value").on("click", function () {
                    $("input", $(this).parent()).val("");
                  });

                $("#pi_shipping_breakup_time").attr("readonly","readonly");
                $("#pi_shipping_breakup_time").timepicker({ "timeFormat": "H:mm",interval: 15 });
            });
        ';
        wp_add_inline_script($this->plugin_name."_timepicker", $time_picker, 'after');

        wp_enqueue_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
        
	}

    function validateDefMinMax(){
        $min_days = get_option('pi_edd_default_min_shipping_days', null);
        $max_days = get_option('pi_edd_default_max_shipping_days', null);
        $page = admin_url('admin.php?page=pi-edd&tab=default#pi-edd-default-shipping-days-container');
        if(empty($min_days) || empty($max_days)){

            if(empty($min_days) && empty($max_days)){
                self::adminError(sprintf(__('You have not set the <i>Default Min & Max shipping days</i> <a href="%s">Click here to correct this issue</a>', 'pi-edd'), $page));
                return;
            }

            if(empty($min_days)){
                self::adminError(sprintf(__('You have not set the <i>Default Min shipping days</i> <a href="%s">Click here to correct this issue</a>', 'pi-edd'), $page));
            }

            if(empty($max_days)){
                self::adminError(sprintf(__('You have not set the <i>Default Max shipping days</i> <a href="%s">Click here to correct this issue</a>', 'pi-edd'), $page));
            }
        }

        if(!empty($min_days) && !empty($max_days) && $min_days  > $max_days){
            self::adminError(sprintf(__('<i>Default Max shipping days</i> should be grater then or equal to <i>Default Min shipping days</i> <a href="%s">Click here to correct this issue</a>', 'pi-edd'), $page));
        }

        if(!empty($min_days) && filter_var($min_days, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1))) === false){
            self::adminError(sprintf(__('<i>Default Min shipping days</i> should be an integer grater then zero <a href="%s">Click here to correct this issue</a>', 'pi-edd'), $page));
        }

        if(!empty($max_days) && filter_var($max_days, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1))) === false){
            self::adminError(sprintf(__('<i>Default Max shipping days</i> should be an integer grater then zero <a href="%s">Click here to correct this issue</a>', 'pi-edd'), $page));
        }
    }

    static function adminError($msg){
        echo sprintf('<div class="notice notice-error is-dismissible"><p><strong>%s</strong></p></div>', $msg);
    }

    function menu_option_page(){
        if(function_exists('settings_errors')){
            settings_errors();
        }
        ?>
        <div class="bootstrap-wrapper">
        <div class="container mt-2">
            <div class="row">
                    <div class="col-12">
                        <div class='bg-dark'>
                        <div class="row">
                            <div class="col-12 col-sm-2 py-2">
                                    <a href="https://www.piwebsolution.com/" target="_blank"><img class="img-fluid ml-2" src="<?php echo plugin_dir_url( __FILE__ ); ?>img/pi-web-solution.svg"></a>
                            </div>
                            <div class="col-12 col-sm-10 d-flex">
                                
                            </div>
                        </div>
                        </div>
                    </div>
            </div>
            <div class="row">
                
                <div class="col-12">
                <div class="bg-light border pl-3 pr-3 pb-3 pt-0">
                    <div class="row">
                    <div class="col-12 col-md-2 px-0 border-right">
                                <?php do_action($this->plugin_name.'_tab'); ?>
                                <a class=" pi-side-menu  bg-secondary" href="https://www.piwebsolution.com/woocommerce-estimated-delivery-date-per-product/" target="_blank">
                                <span class="dashicons dashicons-info"></span> User Guide
                                </a>
                    </div>
                        <div class="col">
                        <?php do_action($this->plugin_name.'_tab_content'); ?>
                        </div>
                        <?php do_action($this->plugin_name.'_promotion'); ?>
                    </div>
                </div>
                </div>
            </div>
        </div>
        </div>
        <?php
        $this->currentTime();
    }

    function currentTime(){
        echo '<div class="pi-edd-current-time-box">';
        echo '<strong>Timing as per your website timezone</strong><br>';
        echo current_time('M d, Y H:i A');
        echo '<br><a href="'.admin_url('options-general.php#timezone_string').'" target="_blank">(click to change)</a>';
        echo '</div>';
    }

}