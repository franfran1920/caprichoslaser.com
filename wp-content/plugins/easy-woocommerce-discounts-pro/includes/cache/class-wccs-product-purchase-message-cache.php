<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCCS_Product_Purchase_Message_Cache extends WCCS_Abstract_Cache {

    public function __construct() {
        parent::__construct( 'wccs_product_purchase_message_', 'wccs_product_purchase_message' );
    }

    public function get_purchase_message( array $args ) {
        if ( empty( $args ) || empty( $args['product_id'] ) ) {
            return false;
        }

        $transient_name = $this->get_transient_name( array( 'product_id' => $args['product_id'] ) );
        $transient_key  = md5( wp_json_encode( $args ) );
        $transient      = get_transient( $transient_name );
        $transient      = false === $transient ? array() : $transient;
        
        return isset( $transient[ $transient_key ] ) ? $transient[ $transient_key ] : false;
    }

    public function set_purchase_message( array $args, $message ) {
        if ( empty( $args ) || empty( $args['product_id'] ) ) {
            return false;
        }

        $transient_name = $this->get_transient_name( array( 'product_id' => $args['product_id'] ) );
        $transient_key  = md5( wp_json_encode( $args ) );
        $transient      = get_transient( $transient_name );
        $transient      = false === $transient ? array() : $transient;

        $transient[ $transient_key ] = $message;

        return set_transient( $transient_name, $transient );
    }

}
