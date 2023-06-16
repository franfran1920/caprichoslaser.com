<?php

class Class_Pi_Edd_Debug_Log_manager{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'debug_log';

   

    private $setting_key = 'edd_enable_debug';
    private $setting_key2 = 'edd_view_debug_log';


    function __construct($plugin_name){
        $this->tab_name = __("Troubleshoot", 'pi-edd');
        $this->plugin_name = $plugin_name;
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        
        add_action('woocommerce_init', array($this,'shipping_zone_to_array'));

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }
        
        add_action($this->plugin_name.'_tab', array($this,'tab'),9);

        $this->settings = array(
            array('field'=>'pisol_edd_method_log'),
        );

        $this->settings2 = array(
            array('field'=>'pi_edd_enable_debug')
        );
        $this->register_settings();

        $this->clearLog();
        
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

        foreach($this->settings2 as $setting2){
            register_setting( $this->setting_key2, $setting2['field']);
        }
    }

    function tab(){
        ?>
        <a class=" pi-side-menu  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-welcome-write-blog"></span> <?php _e( $this->tab_name, 'pi-edd' ); ?> 
        </a>
        <?php
    }

    function tab_content(){
        $view_method_name = get_option('pi_edd_enable_debug', 0);
        ?>
            
            <?php settings_fields( $this->setting_key2 ); ?>
            <div class="my-2 p-3 bg-dark">
            <div class="row align-items-center">
                <div class="col-6"><label for="pi_edd_enable_debug" class="text-light">Enable shipping method log<br></label></div>
                <div class="col-6 text-right">
                    <form method="post" action="options.php"  class="pisol-setting-form d-inline-flex align-items-center exclude-quick-save">
                    <?php settings_fields( $this->setting_key2 ); ?>
                        <div class="custom-control custom-switch">
                    
                            <input type="checkbox" value="1" <?php  checked($view_method_name, 1); ?> class="custom-control-input" name="pi_edd_enable_debug" id="pi_edd_enable_debug">
                            <label class="custom-control-label" for="pi_edd_enable_debug"></label>
                        </div>
                        <input type="submit" class="btn btn-primary btn-lg ml-3" value="Save" />
                    </form>
                </div>
            </div>
            </div>
            
        <?php
        $this->table();
        
    }

    function table(){
        $this->zones = $this->shipping_zone_to_array();
        $logs = get_option('pisol_edd_method_log', array());
        $log_enabled = get_option('pi_edd_enable_debug', 0);
        if(!empty($log_enabled)){
            if(!empty($logs)){
                echo sprintf('<div class="alert alert-info my-3">%s</div>',__('Below are the list of shipping method for which you have not configured the Min, Max shipping days <i>(and this method are using the Default Min, Max shipping days to show estimate date)</i>. You can add missing shipping days for below method in <b>Shipping days</b> tab, If you can\'t find them in Shipping days tab then you can use <b>Dynamic method</b> tab.<br> <a href="https://www.youtube.com/watch?v=UVfrIsdO4q0&amp;feature=emb_title" target="_blank">check out the video on how to use Dynamic method tab to add shipping days</a>','pi-edd'));
            }else{
                echo sprintf('<div class="alert alert-info my-3">%s</div>',__('Go to front end of your site where you are not seeing the Estimate date / Getting the estimate date based on Default Min, Max shipping days and then go to the checkout page and it will keep checking all the shipping method that you come access to find the shipping method which do not have Min, Max shipping days set on them and it will be shown to you on this Log page','pi-edd'));
            }

            $this->tableHeader();
            $this->rows($logs);
            $this->tableFooter();
            $this->clearLogForm();
        }else{
            $this->describeImportanceOfLog();
        }
        
    }

    function rows($logs){
        if(empty($logs)){
            echo '<tr>';
            echo '<td colspan="2" class="text-center">'.__('No shipping method recorded yet','pi-edd').'</td>';
            echo '</tr>';
            return;
        }
        foreach($logs as $log){
            $this->row($log);
        }
    }

    function row($log){
        echo '<tr>';
        echo '<td>'.$log['method'].'</td>';
        echo '<td>'.(isset($this->zones[$log['zone_id']]) ? $this->zones[$log['zone_id']] : $log['zone_id']).'</td>';
        echo '<td>'.(isset($log['time']) ? $log['time'] : '-').'</td>';
        echo '</tr>';
    }

    function tableHeader(){
        ?>
        <table class="table">
            <tr>
                <th>Shipping method with missing Min, Max Shipping days</th>
                <th>Zone</th>
                <th>Log time</th>
            </tr>
        <?php
    }

    function clearLog(){
        $require_capability = pisol_edd_access_control::getCapability();

        if (!isset($_POST['pisol_edd_clear_log']) || !wp_verify_nonce($_POST['pisol_edd_clear_log'], 'pisol_edd_clear_log') || ! current_user_can(  $require_capability)) return;

        delete_option('pisol_edd_method_log');
    }

    function clearLogForm(){
        ?>
        <form method="post">
        <button type="submit" class="btn btn-danger btn-lg"><span class="dashicons dashicons-trash mr-2" style="font-size:30px;"></span> Clear log</button>
        <?php wp_nonce_field( 'pisol_edd_clear_log', 'pisol_edd_clear_log' ); ?>
        </form>
        <?php
    }

    function tableFooter(){
        ?>
        </table>
        <?php
    }

    function shipping_zone_to_array(){
        if(!is_admin()) return;

        $zones = WC_Shipping_Zones::get_zones();
        
        $this->shipping_zones = $this->zone_to_array($zones);

        $non_covered_zone =  WC_Shipping_Zones::get_zone_by("zone_id",0);
        if(is_object($non_covered_zone)){
           $non_covered_zone_name = $non_covered_zone->get_zone_name();
           $non_covered_zone_id = $non_covered_zone->get_id();
           if(!empty($non_covered_zone_name)){
               $this->shipping_zones[$non_covered_zone_id] =  $non_covered_zone_name;
           }
        }
        return $this->shipping_zones;
   }

   function zone_to_array($zones){
        $select = array();
        foreach($zones as $zone){
            $zone_obj = new WC_Shipping_Zone($zone['zone_id']);
            $methods = $zone_obj->get_shipping_methods(true);
            if(count($methods) > 0){
                $select[$zone['zone_id']] =  $zone['zone_name'];
            }
        }
        return $select;
    }

    function describeImportanceOfLog(){
        echo sprintf('<div class="alert alert-info my-3">%s</div>',__('Enable debug log to record all the shipping method in your site for which Min, Max shipping days are not set, and as a result of missing Min, Max shipping days estimate date is not shown or shown based on the Default Min, Max shipping days.','pi-edd'));
    }
   

}

