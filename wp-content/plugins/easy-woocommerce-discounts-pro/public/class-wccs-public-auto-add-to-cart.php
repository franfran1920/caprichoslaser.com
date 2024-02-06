<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCCS_Public_Auto_Add_To_Cart {

	const CART_ITEM_ID         = '_wccs_auto_added_product';
	const CART_ITEM_RULE       = '_wccs_auto_added_product_rule';
	const CART_ITEM_MAIN_PRICE = '_wccs_auto_added_product_main_price';

	public $add_or_remove_cart_item = false;

	protected $loader;

	protected $auto_added_cart_items = array();

	protected $do_remove_products = false;

    public function __construct( WCCS_Loader $loader ) {
        $this->loader = $loader;

        // Automatically add free products to the cart for purchase x receive y pricing rules.
		if ( (int) WCCS()->settings->get_setting( 'auto_add_free_to_cart', 1 ) ) {
			$this->loader->add_action( 'woocommerce_after_calculate_totals', $this, 'add_products', 9999 );
			$this->loader->add_action( 'woocommerce_after_calculate_totals', $this, 'remove_products', 9999 );

			$this->loader->add_filter( 'woocommerce_add_cart_item', $this, 'add_cart_item', 9999 );
			$this->loader->add_filter( 'woocommerce_get_cart_item_from_session', $this, 'get_cart_item_from_session', 9999 );
			$this->loader->add_filter( 'woocommerce_cart_item_price', $this, 'cart_item_price', 9999, 3 );
			$this->loader->add_filter( 'woocommerce_cart_item_quantity', $this, 'cart_item_quantity', 9999, 3 );
			$this->loader->add_filter( 'woocommerce_checkout_cart_item_quantity', $this, 'checkout_cart_item_quantity', 9999, 3 );
			$this->loader->add_filter( 'woocommerce_cart_item_remove_link', $this, 'cart_item_remove_link', 9999, 2 );
			$this->loader->add_filter( 'wccs_apply_pricing_on_cart_item', $this, 'apply_pricing_on_cart_item', 9999, 2 );

			// Cart item class.
			$this->loader->add_filter( 'woocommerce_mini_cart_item_class', $this, 'cart_item_class', 10, 2 );
			$this->loader->add_filter( 'woocommerce_cart_item_class', $this, 'cart_item_class', 10, 2 );
			$this->loader->add_filter( 'woocommerce_order_item_class', $this, 'cart_item_class', 10, 2 );
		}
    }

	public function get_auto_added_cart_items() {
		return $this->auto_added_cart_items;
	}

	public function add_products( $cart = null ) {
		if ( ! $this->should_add_products() || $this->add_or_remove_cart_item ) {
			return;
		}

		$cart          = $cart && is_a( $cart, 'WC_Cart' ) ? $cart : WC()->cart;
		$cart_contents = $cart->get_cart();
		if ( empty( $cart_contents ) ) {
			return;
		}

		$this->auto_added_cart_items = array();
		$this->do_remove_products    = true;

		$rules = WCCS()->pricing->get_purchase_pricings( 'auto' );
		if ( empty( $rules ) ) {
			return;
		}

		foreach ( $rules as $pricing ) {
			if ( empty( $pricing['auto_add_product'] ) ) {
				continue;
			}

			$purchase_quantities_group = WCCS()->cart->get_items_quantities(
				$pricing['purchased_items'],
				( ! empty( $pricing['quantity_based_on'] ) ? $pricing['quantity_based_on'] :  'all_products' ),
				true,
				'price',
				'desc',
				! empty( $pricing['exclude_items'] ) ? $pricing['exclude_items'] : array(),
				true
			);
			if ( empty( $purchase_quantities_group ) ) {
				continue;
			}

			foreach ( $purchase_quantities_group as $group ) {
				if ( ! isset( $group['count'] ) || (int) $group['count'] < (int) $pricing['purchase']['purchase'] ) {
					continue;
				}

				$this->add_to_cart( $cart, $pricing, (int) $group['count'] );
			}
		}
	}

	public function remove_products( $cart = null ) {
		if ( ! $this->should_remove_products() || $this->add_or_remove_cart_item ) {
			return;
		}

		$cart          = $cart && is_a( $cart, 'WC_Cart' ) ? $cart : WC()->cart;
		$cart_contents = $cart->get_cart();
		if ( empty( $cart_contents ) ) {
			return;
		}

		foreach ( $cart_contents as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item[ self::CART_ITEM_ID ] ) && ! in_array( $cart_item_key, $this->auto_added_cart_items ) ) {
				$this->add_or_remove_cart_item = true;
				$cart->remove_cart_item( $cart_item_key );
				$this->add_or_remove_cart_item = false;
			}
		}
	}

	protected function add_to_cart( $cart, $pricing, $purchase_quantity ) {
		if ( empty( $pricing ) || empty( $pricing['auto_add_product'] ) || ! $purchase_quantity ) {
			return false;
		}

		// Checking product existance.
		$product = wc_get_product( WCCS_Helpers::maybe_get_exact_item_id( (int) $pricing['auto_add_product'] ) );
		if ( ! $product ) {
			return false;
		}

		$type = $product->get_type();
		if ( ! WCCS_Helpers::is_allowed_auto_add_product_type( $type ) ) {
			return false;
		}

		if ( ! $product->is_purchasable() ) {
			if ( is_cart() ) {
				$message = apply_filters(
					'wccs_auto_add_product_cannot_be_purchased_message',
					sprintf( __( 'Sorry, &quot;%1$s&quot; could not be added to the cart because it is not purchasable.', 'easy-woocommerce-discounts' ), $product->get_name() ),
					$product
				);
				if ( ! empty( $message ) ) {
					wc_add_notice( $message, 'error' );
				}
			}
			return false;
		} elseif ( ! $product->is_in_stock() ) {
			if ( is_cart() ) {
				$message = apply_filters(
					'wccs_auto_add_product_out_of_stock_message',
					sprintf( __( 'Sorry, &quot;%1$s&quot; could not be added to the cart because it is out of stock.', 'easy-woocommerce-discounts' ), $product->get_name() ),
					$product
				);
				if ( ! empty( $message ) ) {
					wc_add_notice( $message, 'error' );
				}
			}
			return false;
		}

		// Checking variation product attributes.
		$attributes = array();
		if ( 'variation' === $type ) {
			// Variation attributes should not be empty.
			$attributes = $product->get_variation_attributes();
			if ( ! empty( $attributes ) && in_array( '', $attributes ) ) {
				return false;
			}
		}

		// Quantity of the product that should be add to the cart.
        $receive = (int) $pricing['purchase']['receive'];
        /**
         * Do not do repeat when $do_same is false because it causes issue in the cart
         * when cart item quantity change by user inside the cart.
         */
		if ( 'true' === $pricing['repeat'] ) {
            $receive = $receive * floor( $purchase_quantity / (int) $pricing['purchase']['purchase'] );
		}

		if ( ! $product->has_enough_stock( (int) $receive ) ) {
			if ( is_cart() ) {
				$message = apply_filters(
					'wccs_auto_add_product_not_enough_stock_message',
					sprintf( __( 'Sorry, &quot;%1$s&quot; could not be added to the cart because there is not enough stock.', 'easy-woocommerce-discounts' ), $product->get_name() ),
					$product
				);
				if ( ! empty( $message ) ) {
					wc_add_notice( $message, 'error' );
				}
			}
			return false;
		}

		$product_id   = 'variation' !== $type ? $product->get_id() : WCCS()->product_helpers->get_parent_id( $product );
		$variation_id = 'variation' === $type ? $product->get_id() : 0;

		$cart_item_data = apply_filters( 'woocommerce_add_cart_item_data', array(
			self::CART_ITEM_ID   => 1,
			self::CART_ITEM_RULE => (int) $pricing['id'],
		), $product_id, $variation_id, $receive );

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
		$cart_id   = $cart->generate_cart_id( $product_id, $variation_id, $attributes, $cart_item_data );
		$cart_item = $cart->get_cart_item( $cart_id );
		// Does item already exists in the cart.
		if ( ! empty( $cart_item ) ) {
			// Update quantities.
			if ( $cart_item['quantity'] != $receive ) {
				$this->add_or_remove_cart_item = true;
				$cart->set_quantity( $cart_id, $receive );
				$this->add_or_remove_cart_item = false;
			}
			$this->add_auto_added_product_cart_item_key( $cart_id );
			return $cart_id;
		}

		try {
			$this->add_or_remove_cart_item = true;
			$cart_item_key = $cart->add_to_cart(
				$product_id,
				$receive,
				$variation_id,
				$attributes,
				$cart_item_data
			);
			$this->add_or_remove_cart_item = false;

			if ( ! empty( $cart_item_key ) ) {
				$this->add_auto_added_product_cart_item_key( $cart_item_key );
				return $cart_item_key;
			}
		} catch ( \Exception $e ) {}

		return false;
	}

	protected function should_add_products() {
		return apply_filters( 'wccs_auto_add_products_' . __FUNCTION__, true );
	}

	protected function should_remove_products() {
		return apply_filters( 'wccs_auto_add_products_' . __FUNCTION__, $this->do_remove_products );
	}

	protected function add_auto_added_product_cart_item_key( $cart_item_key ) {
		if ( empty( $this->auto_added_cart_items ) || ! in_array( $cart_item_key, $this->auto_added_cart_items ) ) {
			$this->auto_added_cart_items[] = $cart_item_key;
		}
	}

	public function add_cart_item( $cart_item_data ) {
		if ( ! isset( $cart_item_data[ self::CART_ITEM_ID ] ) ) {
			return $cart_item_data;
		}

		$price = (float) $cart_item_data['data']->get_price( 'edit' );

		$cart_item_data[ self::CART_ITEM_MAIN_PRICE ] = $price;

		$cart_item_data['data']->set_price( 0 );

		return $cart_item_data;
	}

	public function get_cart_item_from_session( $cart_item ) {
		if ( ! isset( $cart_item[ self::CART_ITEM_ID ] ) ) {
			return $cart_item;
		}

		return $this->add_cart_item( $cart_item );
	}

	public function cart_item_price( $price, $cart_item, $cart_item_key ) {
		if ( ! isset( $cart_item[ self::CART_ITEM_ID ] ) ) {
			return $price;
		}

		$main_price = WCCS_Helpers::maybe_exchange_price( (float) $cart_item[ self::CART_ITEM_MAIN_PRICE ] );

		if ( 0 < $main_price ) {
			return apply_filters(
				'wccs_auto_add_products_' . __FUNCTION__,
				'<del>' . WCCS()->cart->get_product_price( $cart_item['data'], array( 'price' => $main_price, 'qty' => 1 ) ) . '</del> <ins>' . $price . '</ins>',
				$price,
				$cart_item,
				$cart_item_key
			);
		}

		return $price;
	}

	public function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		if ( ! isset( $cart_item[ self::CART_ITEM_ID ] ) ) {
			return $product_quantity;
		}

		return apply_filters(
			'wccs_auto_add_products_' . __FUNCTION__,
			'<div class="quantity"><span class="qty">' . esc_html( $cart_item['quantity'] ) . '</span></div>'
		);
	}

	public function checkout_cart_item_quantity( $quantity, $cart_item, $cart_item_key ) {
		if ( ! isset( $cart_item[ self::CART_ITEM_ID ] ) ) {
			return $quantity;
		}

		return apply_filters(
			'wccs_auto_add_products_' . __FUNCTION__,
			' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', esc_html( $cart_item['quantity'] ) ) . '</strong>',
			$cart_item,
			$cart_item_key
		);
	}

	public function cart_item_remove_link( $link, $cart_item_key ) {
		if ( ! empty( $this->auto_added_cart_items ) && in_array( $cart_item_key, $this->auto_added_cart_items ) ) {
			$link = apply_filters(
				'wccs_auto_add_products_' . __FUNCTION__,
				'', $link, $cart_item_key, $this->auto_added_cart_items
			);
		}

		return $link;
	}

	public function apply_pricing_on_cart_item( $value, $cart_item ) {
		if ( isset( $cart_item[ self::CART_ITEM_ID ] ) ) {
			$value = false;
		}

		return $value;
	}

	public function cart_item_class( $class, $cart_item ) {
		if ( isset( $cart_item[ self::CART_ITEM_ID ] ) ) {
			$class .= ' ewd-auto-added-product ewd-auto-added-free-product';
		}

		return $class;
	}

}
