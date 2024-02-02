<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCCS_Customer {

	/**
	 * @var WP_User
	 */
	public $customer;

	/**
	 * @var WC_Customer
	 */
	public $wc_customer;

	public function __construct( WP_User $customer ) {
		$this->customer    = $customer;
		$this->wc_customer = new WC_Customer( $this->customer->ID );
	}

	public function __get( $key ) {
		if ( property_exists( $this, $key ) ) {
			return $this->$key;
		} else {
			return $this->customer->$key;
		}
	}

	public function __call( $name, $arguments ) {
		if ( method_exists( $this, $name ) ) {
			return call_user_func_array( array( $this, $name ), $arguments );
		} elseif ( is_callable( array( $this->customer, $name ) ) ) {
			return call_user_func_array( array( $this->customer, $name ), $arguments );
		}
	}

	public function has_role( array $roles ) {
		if ( empty( $this->customer->roles ) || empty( $roles ) ) {
			return false;
		}

		foreach ( $this->customer->roles as $role ) {
			if ( in_array( $role, $roles ) ) {
				return true;
			}
		}

		return false;
	}

	public function get_bought_products() {
		global $wpdb;

		$billing_email = '';
		if ( ! $this->customer || 0 >= (int) $this->customer->ID ) {
			$billing_email = WCCS()->WCCS_Checkout_Helpers->get_billing_email();
		}

		if ( empty( $this->customer->user_email ) && empty( $this->customer->ID ) && empty( $billing_email ) ) {
			return array();
		}

		$transient_name    = 'wccs_wc_cbps_' . md5( $this->customer->user_email . $this->customer->ID . $billing_email );
		$transient_version = WC_Cache_Helper::get_transient_version( 'orders' );
		$transient_value   = get_transient( $transient_name );

		if ( isset( $transient_value['value'], $transient_value['version'] ) && $transient_value['version'] === $transient_version ) {
			$result = $transient_value['value'];
		} else {
			$customer_data = array();
			if ( 0 < $this->customer->ID ) {
				$customer_data[] = $this->customer->ID;
			}

			if ( isset( $this->customer->user_email ) ) {
				$customer_data[] = $this->customer->user_email;
			}

			if ( ! empty( $billing_email ) ) {
				$customer_data[] = $billing_email;
			}

			$customer_data = array_map( 'esc_sql', array_filter( array_unique( $customer_data ) ) );
			$statuses      = array_map( 'esc_sql', WCCS()->order_helpers->wc_get_is_paid_statuses() );

			if ( 0 == count( $customer_data ) ) {
				return false;
			}

			$result = $wpdb->get_col( "
				SELECT im.meta_value FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
				WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
				AND pm.meta_key IN ( '_billing_email', '_customer_user' )
				AND im.meta_key IN ( '_product_id', '_variation_id' )
				AND im.meta_value != 0
				AND pm.meta_value IN ( '" . implode( "','", $customer_data ) . "' )
			" );
			$result = array_unique( array_map( 'absint', $result ) );

			$transient_value = array(
				'version' => $transient_version,
				'value'   => $result,
			);

			set_transient( $transient_name, $transient_value, DAY_IN_SECONDS * 30 );
		}

		return $result;
	}

	public function has_bought_products( array $products, $type = 'at_least_one_of' ) {
		if ( empty( $products ) ) {
			return true;
		}

		$bought_products = $this->get_bought_products();
		if ( empty( $bought_products ) ) {
			return 'none_of' === $type;
		}

		$products = array_map( 'WCCS_Helpers::maybe_get_exact_item_id', $products );

		if ( 'at_least_one_of' === $type || 'none_of' === $type ) {
			if ( count( array_intersect( $products, $bought_products ) ) ) {
				return 'at_least_one_of' === $type;
			}

			return 'none_of' === $type;
		} elseif ( 'all_of' === $type ) {
			return ! count( array_diff( $products, $bought_products ) );
		} elseif ( 'only' === $type ) {
			return ! count( array_diff( $products, $bought_products ) ) && ! count( array_diff( $bought_products, $products ) );
		}

		return false;
	}

	public function get_bought_categories() {
		$bought_products = $this->get_bought_products();
		if ( empty( $bought_products ) ) {
			return array();
		}

		$bought_categories = array();

		foreach ( $bought_products as $product_id ) {
			$bought_categories = array_merge( $bought_categories, wc_get_product_cat_ids( $product_id ) );
		}

		return array_unique( $bought_categories );
	}

	public function has_bought_categories( array $categories, $type = 'at_least_one_of' ) {
		if ( empty( $categories ) ) {
			return true;
		}

		$bought_categories = $this->get_bought_categories();
		if ( empty( $bought_categories ) ) {
			return 'none_of' === $type;
		}

		$categories = array_map( 'WCCS_Helpers::maybe_get_exact_category_id', $categories );

		if ( 'at_least_one_of' === $type || 'none_of' === $type ) {
			if ( count( array_intersect( $categories, $bought_categories ) ) ) {
				return 'at_least_one_of' === $type;
			}

			return 'none_of' === $type;
		} elseif ( 'all_of' === $type ) {
			return ! count( array_diff( $categories, $bought_categories ) );
		} elseif ( 'only' === $type ) {
			return ! count( array_diff( $categories, $bought_categories ) ) && ! count( array_diff( $bought_categories, $categories ) );
		}

		return false;
	}

	public function get_bought_product_tags() {
		$bought_products = $this->get_bought_products();
		if ( empty( $bought_products ) ) {
			return array();
		}

		$product_helpers = WCCS()->product_helpers;

		$tags = array();

		foreach ( $bought_products as $product_id ) {
			$tags = array_merge( $tags, $product_helpers->wc_get_product_term_ids( $product_id, 'product_tag' ) );
		}

		return array_unique( $tags );
	}

	public function has_bought_product_tags( array $tags, $type = 'at_least_one_of' ) {
		if ( empty( $tags ) ) {
			return true;
		}

		$bought_tags = $this->get_bought_product_tags();
		if ( empty( $bought_tags ) ) {
			return 'none_of' === $type;
		}

		$tags = array_map( 'WCCS_Helpers::maybe_get_exact_tag_id', $tags );

		if ( 'at_least_one_of' === $type || 'none_of' === $type ) {
			if ( count( array_intersect( $tags, $bought_tags ) ) ) {
				return 'at_least_one_of' === $type;
			}

			return 'none_of' === $type;
		} elseif ( 'all_of' === $type ) {
			return ! count( array_diff( $tags, $bought_tags ) );
		} elseif ( 'only' === $type ) {
			return ! count( array_diff( $tags, $bought_tags ) ) && ! count( array_diff( $bought_tags, $tags ) );
		}

		return false;
	}

	public function get_bought_product_taxonomies( $taxonomy ) {
		$bought_products = $this->get_bought_products();
		if ( empty( $bought_products ) ) {
			return array();
		}

		$product_helpers = WCCS()->product_helpers;

		$taxonomies = array();
		foreach ( $bought_products as $product_id ) {
			$taxonomies = array_merge( $taxonomies, $product_helpers->wc_get_product_term_ids( $product_id, $taxonomy ) );
		}

		return array_unique( $taxonomies );
	}

	public function has_bought_product_taxonomies( array $taxonomies, $taxonomy, $type = 'at_least_one_of' ) {
		if ( empty( $taxonomies ) ) {
			return true;
		}

		$bought_taxonomies = $this->get_bought_product_taxonomies( $taxonomy );
		if ( empty( $bought_taxonomies ) ) {
			return 'none_of' === $type;
		}

		for ( $i = 0; $i < count( $taxonomies ); $i++ ) {
			$taxonomies[ $i ] = WCCS_Helpers::maybe_get_exact_item_id( $taxonomies[ $i ], $taxonomy );
		}

		if ( 'at_least_one_of' === $type || 'none_of' === $type ) {
			if ( count( array_intersect( $taxonomies, $bought_taxonomies ) ) ) {
				return 'at_least_one_of' === $type;
			}

			return 'none_of' === $type;
		} elseif ( 'all_of' === $type ) {
			return ! count( array_diff( $taxonomies, $bought_taxonomies ) );
		} elseif ( 'only' === $type ) {
			return ! count( array_diff( $taxonomies, $bought_taxonomies ) ) && ! count( array_diff( $bought_taxonomies, $taxonomies ) );
		}

		return false;
	}

	/**
	 * Get customer total spent.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args
	 *
	 * @return float
	 */
	public function get_total_spent( array $args = array() ) {
		$customer_orders = $this->get_orders( $args );
		if ( empty( $customer_orders ) ) {
			return 0;
		}

		$spent = 0;
		foreach ( $customer_orders as $order ) {
			$spent += (float) $order->get_total();
		}

		return (float) wc_format_decimal( $spent, 2 );
	}

	/**
	 * Get number of customer orders.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args
	 *
	 * @return int
	 */
	public function get_number_of_orders( array $args = array() ) {
		$args = wp_parse_args( $args, array(
			'return' => 'ids',
		) );

		return count( $this->get_orders( $args ) );
	}

	/**
	 * Get customer orders.
	 *
	 * @since  2.0.0
	 *
	 * @param  array $args
	 *
	 * @return array
	 */
	public function get_orders( array $args = array() ) {
		$args = wp_parse_args( $args, array(
			'limit'      => -1,
			'status'     => WCCS()->order_helpers->wc_get_is_paid_statuses(),
			'date_query' => array(),
		) );
		$args['paginate'] = false;

		if ( empty( $args['date_query'] ) ) {
			if ( ! empty( $args['date_after'] ) ) {
				$args['date_query']['after'] = $args['date_after'];
			}
			if ( ! empty( $args['date_before'] ) ) {
				$args['date_query']['before'] = $args['date_before'];
			}
		} elseif ( ! empty( $args['date_query'] ) ) {
			if ( ! empty( $args['date_query']['after'] ) ) {
				$args['date_after'] = $args['date_query']['after'];
			}
			if ( ! empty( $args['date_query']['before'] ) ) {
				$args['date_before'] = $args['date_query']['before'];
			}
		}

		$orders = array();
		if ( $this->customer->ID ) {
			$orders = WCCS()->order_helpers->wc_get_orders( array_merge( $args, array( 'customer_id' => $this->customer->ID ) ) );
		} else {
			$billing_email = WCCS()->WCCS_Checkout_Helpers->get_billing_email();
			if ( ! empty( $billing_email ) ) {
				$orders = WCCS()->order_helpers->wc_get_orders( array_merge( $args, array( 'billing_email' => $billing_email ) ) );
			}
		}

		return array_unique( $orders );
	}

	/**
	 * Getting customer number of products reviews.
	 *
	 * @since  2.0.0
	 *
	 * @param  array $args
	 *
	 * @return integer
	 */
	public function get_number_of_products_reviews( array $args = array() ) {
		if ( $this->customer->ID ) {
			$email = $this->customer->user_email;
		} else {
			$email = WCCS()->WCCS_Checkout_Helpers->get_billing_email();
		}

		if ( 0 >= $this->customer->ID && empty( $email ) ) {
			return 0;
		}

		global $wpdb;

		$date_query = '';
		if ( ! empty( $args['date_before'] ) ) {
			$date_query .= " AND comments.comment_date < '" . esc_sql( $args['date_before'] ) . "'";
		}
		if ( ! empty( $args['date_after'] ) ) {
			$date_query .= " AND comments.comment_date > '" . esc_sql( $args['date_after'] ) . "'";
		}

		$query_args     = array();
		$user_condition = '';
		if ( 0 < $this->customer->ID ) {
			$user_condition .= 'comments.user_id = %1$d';
			$query_args[]    = $this->customer->ID;
		}
		if ( ! empty( $email ) ) {
			$user_condition .= ! empty( $user_condition ) ? ' OR comments.comment_author_email = "%2$s"' : 'comments.comment_author_email = "%1$s"';
			$query_args[]    = $email;
		}

		// @todo Adding cache.
		return (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(comments.comment_ID) FROM {$wpdb->comments} comments
			INNER JOIN {$wpdb->posts} posts ON (comments.comment_post_ID = posts.ID)
			WHERE comments.comment_approved = '1'
			AND posts.post_type = 'product'
			AND comments.comment_parent = 0
			AND ($user_condition)
			$date_query
			", $query_args )
		);
	}

}
