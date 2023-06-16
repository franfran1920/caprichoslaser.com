<div class="bg-secondary p-3 mt-3" id="pi-edd-default-shipping-days-container">
<strong class="h6 text-light">You must set default shipping days <?php pisol_help::inline('pi-edd-reason-for-default-min-max', 'You must set default shipping days'); ?></strong>
</div>
<div class="mb-3 border p-3">
        <form method="post" action="options.php"  class="pisol-setting-form exclude-quick-save">
        <?php settings_fields( $this->setting_key ); ?>
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_edd($setting, $this->setting_key);
            }
        ?>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
</div>
<div id="pi-edd-reason-for-default-min-max" style="display:none;">
    <div class="bootstrap-wrapper">
    <p>
    If the minimum and maximum shipping days are not set for a shipping method, no estimate delivery date will be displayed. To prevent this, set the default minimum and maximum shipping days.
    </p>
    <p>
    If the default minimum and maximum shipping days are set, they will be used to calculate the estimate delivery date for any shipping methods without specific shipping days.
    </p>
    <p>
    Use the troubleshoot option in our plugin to identify any shipping methods without set minimum and maximum shipping days, ensuring that the plugin will not have to rely on the default shipping days value. 
    </p>
    </div>
</div>