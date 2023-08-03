<?php

class Class_Pi_edd_shipping_class_option{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'shipping_class';

    private $tab_name = "Shipping Class";

    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        $this->class_id =  filter_input(INPUT_GET,'pi_edd_class_id');

        

        $this->settings = $this->settings();
        $this->register_settings();
       
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),2);

    

        
    }

    function settings(){
        $shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );
        $settings = array();
        foreach ($shipping_classes as $shipping_class){
            $settings[] = array(
                'field'=> 'pisol_edd_shipping_class_'.$shipping_class->term_id
            );
        }
        
        return $settings;
    }

    function register_settings(){   

        foreach($this->settings as $setting){
                register_setting( $setting['field'], $setting['field']);
        }
    
    }

    function shippingClasses(){
        $class_id = $this->class_id;
        
        $shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );

        echo '<div class="btn-group-vertical mt-3">';
        if(!empty($shipping_classes)){
            foreach ($shipping_classes as $shipping_class){

                $class = $shipping_class->term_id == $class_id ? 'btn-primary' : 'btn-secondary';
                echo '<a href="'.admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab.'&pi_edd_class_id='.$shipping_class->term_id ).'" class="btn '.$class.' border-bottom text-left">'.$shipping_class->name.'</a>';
            }
        }else{
            echo '<span class="alert alert-warning">There are no shipping class in your WooCommerce setting</span>';
        }
        echo '</div>';
    }
   
    function tab(){
        ?>
        <a class=" pi-side-menu  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
        <span class="dashicons dashicons-airplane"></span> <?php _e( $this->tab_name); ?> 
        </a>
        <?php
    }

    function tab_content(){
        ?>
        <div class="row">
            <div class="col-12 col-md-3">
                <?php $this->shippingClasses(); ?>
            </div>
            <div class="col-12 col-md-9">
                <?php
                    
                    if($this->class_id !== null){
                        pisol_edd_shipping_class_settings::classSettings($this->class_id);
                    }else{
                        printf('<div class="alert alert-warning mt-3">%s</div>',__('Select a shipping class from the left','pisol-edd'));
                    }
                ?>
            </div>
        </div>
        <?php
    }

    
}
