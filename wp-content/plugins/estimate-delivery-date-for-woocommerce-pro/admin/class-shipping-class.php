<?php 

class pisol_edd_shipping_class_settings{
    function __construct($class_id){
        $this->class_id = $class_id;
        $this->setting_key = 'pisol_edd_shipping_class_'.$class_id;
        $this->class_value = Pisol_edd_get_option($this->setting_key, array());
    }

    static function classSettings($class_id){
        $obj = new self($class_id);
        $obj->settings();
    }

    function settings(){
        ?>
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <?php
           $this->classTimings();
        ?>
        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-md my-3" value="<?php echo __('Save Changes','pisol-dtt'); ?>">
        </form>
       <?php
        
    }

    function classTimings(){
        $set_time_from_class = self::zoneValue('pisol_edd_set_time_from_class', 0, $this->class_value);
        $product_preparation_time = self::zoneValue('product_preparation_time', 0, $this->class_value);
        $product_preparation_time_max = self::zoneValue('product_preparation_time_max', 0, $this->class_value);
        $out_of_stock_product_preparation_time = self::zoneValue('out_of_stock_product_preparation_time', 0, $this->class_value);
        $pisol_exact_availability_date = self::zoneValue('pisol_exact_availability_date', '', $this->class_value);
        

        include 'partials/preparation-time.php';
    }

    static function zoneValue($variable, $default, $zone_values){
        if(isset($zone_values[$variable])) return $zone_values[$variable];
    
        return $default;
    }

}