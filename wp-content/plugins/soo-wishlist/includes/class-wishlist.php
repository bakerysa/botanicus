<?php
/**
 * Wishlist list
 *
 * @package Soo Wishlist
 */

/**
 * Class Soo_Wishlist
 */
class Soo_Wishlist {
	/**
	 * Wishlist list instance
	 *
	 * @var Soo_Wishlist_List
	 */
	public $list;

	/**
	 * The single instance of the class
	 *
	 * @var Soo_Wishlist_Plugin
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return Soo_Wishlist
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init plugin
	 */
	public function init() {
		$this->list = new Soo_Wishlist_List();
		//if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) ) {
		//	$this->list = new Soo_Wishlist_List();
		//}
	}

	/**
	 * Get items from list
	 *
	 * @return array
	 */
	public function get_items() {
		return $this->list->items;
	}

	/**
	 * Get remove URL for an item in wishlist
	 *
	 * @param int|bool $product_id
	 *
	 * @return string
	 */
	public function get_remove_url( $product_id = false ) {
		$product_id        = $product_id ? $product_id : get_the_ID();
		$wishlish_page_url = soow_get_wishlist_url();
		$key               = $this->list->generate_item_key( $product_id );

		return apply_filters( 'soo_wishlist_remove_item_url', $wishlish_page_url ? wp_nonce_url( add_query_arg( 'remove_from_wishlist', $key, $wishlish_page_url ), 'soo_wishlist_' . $key ) : '' );
	}

	/**
	 * Add an item to current list
	 *
	 * @param int|object $product
	 *
	 * @return string|WP_Error
	 */
	public function add( $product ) {
//		if ( ! $this->can_edit() ) {
//			return new WP_Error( 'wishlist_error', esc_html__( 'You do not have permission to add items', 'soow' ) );
//		}

		$product_id = is_numeric( $product ) ? $product : $product->get_id();

		if ( $this->list->in_wishlist( $product_id ) ) {
			return new WP_Error( 'wishlist_error', esc_html__( 'This product is exists your wishlist', 'soow' ) );
		}

		$result = $this->_push_item( $product_id );

		if ( $result && ! is_wp_error( $result ) ) {
			$key                     = $this->list->generate_item_key( $product_id );
			$this->list->items[$key] = array( 'id' => $product_id );

			return $key;
		} else {
			return $result;
		}
	}

	/**
	 * Remove a product from list
	 *
	 * @param int $product_id
	 *
	 * @return bool|WP_Error
	 */
	public function remove( $product_id ) {
//		if ( ! $this->can_edit() ) {
//			return new WP_Error( 'wishlist_error', esc_html__( 'You do not have permission to remove items', 'soow' ) );
//		}

		if ( ! $this->list->in_wishlist( $product_id ) ) {
			return new WP_Error( 'wishlist_error', esc_html__( 'This product is not exists your wishlist', 'soow' ) );
		}

		$key = $this->list->generate_item_key( $product_id );

		$removed = $this->_remove_item( $product_id );

		if ( $removed ) {
			unset( $this->list->items[$key] );

			return true;
		}

		return new WP_Error( 'wishlist_error', esc_html__( 'There is an error occur. Please try again.', 'soow' ) );
	}

	/**
	 * Create new list and insert it into database
	 *
	 * @param array $args
	 *
	 * @return int The list ID
	 */
	public function add_list( $args = array() ) {
		global $wpdb;

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();

		$args = wp_parse_args( $args, array(
			'title'        => '',
			'slug'         => '',
			'status'       => 'private',
			'user_id'      => $user_id,
			'hash'         => '',
			'created_date' => date( 'Y-m-d H:i:s' ),
			'updated_date' => date( 'Y-m-d H:i:s' ),
			'is_default'   => 0,
		) );

		if ( ! in_array( $args['status'], array( 'public', 'private' ) ) ) {
			$args['status'] = 'private';
		}

		$args['hash'] = $this->_generate_hash();

		$result = $wpdb->insert(
			$wpdb->soo_wishlists,
			array(
				'title'        => $args['title'],
				'slug'         => $args['slug'],
				'status'       => $args['status'],
				'user_id'      => $args['user_id'],
				'hash'         => $args['hash'],
				'created_date' => $args['created_date'],
				'updated_date' => $args['updated_date'],
				'is_default'   => $args['is_default'],
			),
			array(
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
			)
		);

		if ( $result ) {
			$new_list_id = $wpdb->insert_id;

			// Update list slug
			$wpdb->update(
				$wpdb->soo_wishlists,
				array(
					'slug' => wp_unique_post_slug( sanitize_title( $args['title'], $new_list_id ), $new_list_id, $args['status'], 'wishlist', 0 ),
				),
				array(
					'ID' => $new_list_id,
				)
			);

			// If this is a new default list, we have to update the previous default list
			if ( $args['is_default'] ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->soo_wishlists} SET is_default = 0 WHERE ID != %d AND user_id = %d AND is_default = %d", $new_list_id, $user_id, 1 ) );
			}

			return $new_list_id;
		}

		return false;
	}

	/**
	 * Remove a list of current user
	 *
	 * @param int $list_id
	 *
	 * @return bool|false|int
	 */
	public function remove_list( $list_id ) {
		global $wpdb;

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$result = $wpdb->delete(
			$wpdb->soo_wishlists,
			array(
				'ID'      => $list_id,
				'user_id' => get_current_user_id(),
			),
			array(
				'%d',
				'%d',
			)
		);

		return $result;
	}

	/**
	 * Check if current user can edit a list
	 *
	 * @param object|bool $list The Soo_Wishlist_list object
	 *
	 * @return bool
	 */
	public function can_edit( $list = false ) {
		$list = $list ? $list : $this->list;

		if ( is_user_logged_in() ) {
			if ( get_current_user_id() == $list->__get( 'user_id' ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			$list_owner_id = $list->__get( 'user_id' );

			// If has owner
			if ( $list_owner_id ) {
				return false;
			}

			return true;
		}
	}

	/**
	 * Insert new row into wishlist table
	 *
	 * @param int|object $product
	 *
	 * @return bool|WP_Error|int
	 */
	private function _push_item( $product ) {
		global $wpdb;

		$product_id = is_numeric( $product ) ? $product : $product->get_id();

		if ( is_user_logged_in() ) {

			// Create new list if there is no list for current user
			if ( ! $this->list->__get( 'ID' ) ) {
				$id = $this->add_list( array( 'is_default' => 1 ) );

				if ( $id ) {
					$this->list = new Soo_Wishlist_List( array( 'ID' => $id ) );
				} else {
					return new WP_Error( 'wishlist_error', esc_html__( 'Could not create new list', 'soow' ) );
				}
			}

			$result = $wpdb->insert(
				$wpdb->soo_wishlists_items,
				array(
					'product_id'  => $product_id,
					'quantity'    => 1,
					'user_id'     => get_current_user_id(),
					'wishlist_id' => $this->list->__get( 'ID' ),
					'added_date'  => date( 'Y-m-d H:i:s' ),
				),
				array(
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
				)
			);

			if ( $result ) {
				return $wpdb->insert_id;
			} else {
				return new WP_Error( 'wishlist_error', esc_html__( 'Could not add this product to your wishlist', 'soow' ) );
			}
		} else {
			$items       = $this->get_items();
			$key         = $this->list->generate_item_key( $product_id );
			$items[$key] = array( 'id' => $product_id );

			soow_setcookie( 'soo_wishlist', $items );
		}

		return true;
	}

	/**
	 * Remove an item from list
	 *
	 * @param int|object $product
	 *
	 * @return int|bool
	 */
	private function _remove_item( $product ) {
		global $wpdb;

		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

		if ( is_user_logged_in() ) {
			$result = $wpdb->delete(
				$wpdb->soo_wishlists_items,
				array(
					'product_id'  => $product->get_id(),
					'user_id'     => get_current_user_id(),
					'wishlist_id' => $this->list->__get( 'ID' ),
				),
				array(
					'%d',
					'%d',
					'%d',
				)
			);

			return $result;
		} else {
			$key = $this->list->generate_item_key( $product->get_id() );
			$items = $this->get_items();
			unset( $items[$key] );

			soow_setcookie( 'soo_wishlist', $items );
		}

		return true;
	}

	/**
	 * Get the random hash string
	 *
	 * @return string
	 */
	private function _generate_hash() {
		if ( function_exists( 'wc_rand_hash' ) ) {
			return wc_rand_hash();
		} else {
			return sha1( wp_rand() );
		}
	}
}
