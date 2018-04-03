<?php
/**
 * Wishlist list
 *
 * @package Soo Wishlist
 */

/**
 * Class Soo_Wishlist_List
 */
class Soo_Wishlist_List {
	/**
	 * List data
	 *
	 * @var array
	 */
	private $data;

	/**
	 * List items
	 *
	 * @var array
	 */
	public $items = array();

	/**
	 * Class constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		$this->data  = $this->_get_list_data( $args );
		$this->items = $this->_get_items();
	}

	/**
	 * Get list data by data name
	 *
	 * This name must be matched with the database column name
	 *
	 * @param string $name
	 *
	 * @return bool|int|string
	 */
	public function __get( $name ) {
		if ( empty( $this->data ) ) {
			return false;
		}

		if ( isset( $this->data[$name] ) ) {
			return $this->data[$name];
		}

		return false;
	}

	/**
	 * Get number of items in this list
	 *
	 * @return int
	 */
	public function count() {
		if ( $this->items ) {
			return count( $this->items );
		}

		return 0;
	}

	/**
	 * Check if a product is exists in the list or not
	 *
	 * @param int|object $product
	 *
	 * @return int
	 */
	public function in_wishlist( $product ) {
		$product_id = is_numeric( $product ) ? $product : $product->get_id();

		if ( empty( $this->items ) ) {
			return false;
		}

		$key = $this->generate_item_key( $product_id );

		if ( array_key_exists( $key, $this->items ) ) {
			return $key;
		}

		return false;
	}

	/**
	 * Search the product by key
	 *
	 * @param string $key
	 *
	 * @return array|bool
	 */
	public function find( $key ) {
		if ( ! isset( $this->items[$key] ) ) {
			return false;
		}

		return $this->items[$key];
	}

	/**
	 * Generate unique item key for a product
	 *
	 * @param int $product_id
	 *
	 * @return string
	 */
	public function generate_item_key( $product_id ) {
		return md5( $product_id );
	}

	/**
	 * Get the list data
	 *
	 * If ID or hash is set, it will get it from DB
	 * else if no args passed but current user is logged in, it will get the default list data from DB
	 * else it will return false
	 *
	 * @param array $args
	 *
	 * @return bool|array
	 */
	private function _get_list_data( $args = array() ) {
		global $wpdb;

		$args = wp_parse_args( $args, array(
			'ID'   => 0,
			'hash' => '',
		) );

		$query = "SELECT * FROM {$wpdb->soo_wishlists}";
		$where = array();

		if ( $args['ID'] ) {
			$ID      = absint( $args['ID'] );
			$where[] = "ID = $ID";
		}

		if ( ! empty( $args['hash'] ) ) {
			$hash    = esc_sql( trim( $args['hash'] ) );
			$where[] = "hash = '$hash'";
		}

		// If we have a target list
		if ( ! empty( $where ) ) {
			$where = implode( ' AND ', $where );
			$query .= ' WHERE ' . $where;

			return $wpdb->get_row( $query, ARRAY_A );

		} elseif ( is_user_logged_in() ) {
			// Get the default list of current user
			$user_id = get_current_user_id();
			$query .= " WHERE user_id = $user_id AND is_default = 1 LIMIT 1";

			return $wpdb->get_row( $query, ARRAY_A );
		}

		// Return false when no target list and user is not logged in
		return false;
	}

	/**
	 * Get wishlist items base on list data
	 *
	 * @return array
	 */
	private function _get_items() {
		// Check the data to know which list is selected
		if ( empty( $this->data ) ) {
			if ( is_user_logged_in() ) {
				return array();
			} else {
				return soow_getcookie( 'soo_wishlist', true );
			}
		}

		global $wpdb;

		$sql = "SELECT *
                FROM {$wpdb->soo_wishlists_items} AS i
                LEFT JOIN {$wpdb->soo_wishlists} AS l ON l.ID = i.wishlist_id
                INNER JOIN {$wpdb->posts} AS p ON p.ID = i.product_id
                INNER JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID
                WHERE 1 AND p.post_type = %s AND p.post_status = %s AND i.user_id = %d  AND i.wishlist_id = %d";

		$sql_args = array(
			'product',
			'publish',
			$this->__get( 'user_id' ),
			$this->__get( 'ID' ),
		);

		$wishlist_items = $wpdb->get_results( $wpdb->prepare( $sql, $sql_args ) );

		// Reset list
		$items = array();
		foreach ( $wishlist_items as $wishlist_item ) {
			$key = $this->generate_item_key( $wishlist_item->product_id );

			$items[$key] = array(
				'id' => $wishlist_item->product_id,
			);
		}

		return $items;
	}
}