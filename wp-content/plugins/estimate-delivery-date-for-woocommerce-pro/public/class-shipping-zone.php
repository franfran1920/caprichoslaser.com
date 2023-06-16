<?php

/**
 * step 1: check user selected zone
 * step 2: get gro location based zone
 * step 3: get the default zone
 * 
 * Function with "Edd" in name are as per the plugin logic remaining are general functions
 */

class Pi_edd_shipping_zone
{
    function __construct(){
        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }
    }

    public static function getShippingZone(){
            $obj = new self();
            $shipping_zone = null;
            if(!$shipping_zone = pisol_edd_wp_cache_get('shipping_zone','pisol_edd_np_cache')){
                if($shipping_zone == null){
                    $shipping_zone = $obj->getUserSelectedZone();
                }

                if($shipping_zone == null){
                    $shipping_zone = $obj->getGeoLocatedZone();
                }

                if($shipping_zone == null){
                    $shipping_zone = $obj->getShippingZoneById($obj->settings['default_shipping_zone']);
                }
                pisol_edd_wp_cache_set('shipping_zone', $shipping_zone, 'pisol_edd_np_cache');
            }
        return $shipping_zone;
    }

    public static function getOriginalShippingZone(){
        $obj = new self();
        $shipping_zone = null;
        
        if($shipping_zone == null){
            $shipping_zone = $obj->getUserSelectedZone();
        }

        if($shipping_zone == null){
            $shipping_zone = $obj->getGeoLocatedZone();
        }
            
    return $shipping_zone;
}

    private function getUserSelectedZone(){
        global $woocommerce;
        if(isset(WC()->cart)){
            $shipping_packages =  WC()->cart->get_shipping_packages();
        
            $shipping_zone = wc_get_shipping_zone( reset( $shipping_packages ) );

            if(is_object($shipping_zone)){
                $methods = $shipping_zone->get_shipping_methods(true);
                if(!empty($methods)){
                    return $shipping_zone;
                }
                return null;
            }
        }
        return null;
    }

    private function getGeoLocatedZone(){
        $destination = $this->ipBasedDestination();
        
        if(empty($destination['country'])) return null;

        $shipping_zone = WC_Shipping_Zones::get_zone_matching_package( $destination );
        if(is_object($shipping_zone)){
            $methods = $shipping_zone->get_shipping_methods(true);
            if(!empty($methods)){
                return $shipping_zone;
            }
            return null;
        }
        return null;
    }

    private function ipBasedDestination(){
        $geo_instance  = new WC_Geolocation();
        $user_ip  = $geo_instance->get_ip_address();
        $user_geodata = $geo_instance->geolocate_ip($user_ip);
        
        $destination['destination']['country'] =  $user_geodata['country'];
        $destination['destination']['state'] =  $user_geodata['state'];
        $destination['destination']['postcode'] = "";
        return $destination;
    }

    private function getShippingZoneById($zone_id = 0){
        $shipping_zone = WC_Shipping_Zones::get_zone( $zone_id );
        if(is_object($shipping_zone)){
            $methods = $shipping_zone->get_shipping_methods(true);
            if(!empty($methods)){
                return $shipping_zone;
            }
            return null;
        }
        return null;
    }

}

