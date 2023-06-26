<?php

class Class_Pi_Edd_Show_Holidays{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'shop_holidays';

    private $tab_name = "Shop holidays";

    private $setting_key = 'shop_holidays_setting';


    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        add_action($this->plugin_name.'_tab', array($this,'tab'),3);

        $this->settings = array(
            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Select shop holidays",'pi-edd'), 'type'=>"setting_category"),
            array('field'=>'pi_edd_shop_holidays'),
            
            
            
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
        <a class=" pi-side-menu  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-no"></span> <?php _e( $this->tab_name, 'http2-push-content' ); ?> 
        </a>
        <?php
    }

    function tab_content(){
       ?>
       
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); 
        $dates = get_option("pi_edd_shop_holidays");
        ?>
        <input type="hidden" id="pi_edd_shop_holidays" name="pi_edd_shop_holidays" value="<?php echo $dates; ?>">
        
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_edd($setting, $this->setting_key);
            }
        ?>
        <div class="row">
            <div class="col-12 col-md-7">
                <div id="pi-shop-holiday-calender" class="mt-2"></div>
                <input type="submit" class="mt-3 btn btn-primary btn-md" value="Save holidays dates" />
                <input type="button" id="reset-shop-holidays" class="mt-3 btn btn-primary btn-md bg-primary" value="Delete all dates" />
            </div>
            <div class="col-12 col-md-5 mt-3">
                <h3><?php _e('Selected holiday dates','pi-edd'); ?></h3>
                <div id="pi-selected-shop-holidays">
                <?php
                    if(!empty($dates)){
                        $dates_array = explode(':', $dates);
                        foreach($dates_array as $date){
                            if(!empty($date)){
                                echo sprintf('<span class="bg-secondary py-2 px-3 text-light d-block font-weight-bold m-2">%s</span>', pi_edd_admin_common::formatedDate($date));
                            }
                        }
                    }
                ?>
                </div>
            </div>
        </div>
        </form>

       <?php
    }
}

