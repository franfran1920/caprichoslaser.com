<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
class pisol_edd_access_control{

    private $setting = array();

    private $active_tab;

    private $this_tab = 'access_control';

    private $tab_name = "Access control";

    private $setting_key = 'pisol_edd_access_control';

    

    function __construct($plugin_name){

        $this->plugin_name = $plugin_name;

        $this->active_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'default';

        $this->settings = array(
                
                array('field'=>'color-setting', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__('Access control of plugin','pisol-dtt'), 'type'=>'setting_category'),

                array('field'=>'pi_edd_allow_shop_manager', 'label'=>__('All shop manager to access plugin setting','pisol-dtt'),'desc'=>__('Shop manager will be able to see the settings and modify the setting of the plugin','pisol-dtt'), 'type'=>'switch', 'default'=>0)

            );
        

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        add_action($this->plugin_name.'_tab', array($this,'tab'),7);
        
        $this->register_settings();

        $this->optionEditToShopManager();

    }

    function register_settings(){   

        foreach($this->settings as $setting){
                register_setting( $this->setting_key, $setting['field']);
        }
    
    }

    function delete_settings(){
        foreach($this->settings as $setting){
            delete_option( $setting['field'] );
        }
    }

    function tab(){
        ?>
        <a class="pi-side-menu  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-cart"></span> <?php _e( $this->tab_name, 'pisol-dtt' ); ?> 
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
        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-md my-3" value="<?php echo __('Save Changes','pisol-dtt'); ?>">
        </form>
       <?php
    }

    function optionEditToShopManager(){
		
		$settings = array(
            'pisol_edd_access_control',
            'shipping_setting',
            'edd_enable_debug', 
            'edd_view_debug_log', 
            'edd_dynamic_methods',
            'edd_view_dynamic_methods_names',
            'design_settting',
            'holidays_setting',
            'message_settting',
            'pi_edd_basic_setting',
            'shop_holidays_setting',
            'pi_sn_translate_setting'
        );

        $shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );

        foreach ($shipping_classes as $shipping_class){
            $settings[] =  'pisol_edd_shipping_class_'.$shipping_class->term_id;
        }


            foreach($settings as $setting){
                add_filter("option_page_capability_{$setting}", array(__CLASS__, 'getCapability'));
            }
	}

    static function  getCapability(){
        $access_control = get_option('pi_edd_allow_shop_manager', '0');
        if(empty($access_control)){
            $capability = 'manage_options';
        }else{
            $capability = 'manage_woocommerce';
        }

        return (string)apply_filters('pisol_edd_settings_cap', $capability);
    }
    
}


