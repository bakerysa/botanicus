<?php
/**
 * Handles actions
 *
 * @author  SooPlugins
 * @package Soo Wishlist
 */


/**
 * Class Soo_Wishlist_Actions_Handle
 */
class Soo_Wishlist_Actions_Handle {
	/**
	 * Initialize actions handle
	 */
	public static function init() {
		add_action( 'wp_loaded', array( __CLASS__, 'add_to_wishlist_action' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'remove_from_wishlist_action' ) );
		add_action( 'wp_logout', array( __CLASS__, 'remove_cookie' ) );

		add_action( 'wp_ajax_soow_add_to_wishlist', array( __CLASS__, 'ajax_add_to_wishlist' ) );
		add_action( 'wp_ajax_nopriv_soow_add_to_wishlist', array( __CLASS__, 'ajax_add_to_wishlist' ) );

		add_action( 'wp_ajax_soow_update_fragments', array( __CLASS__, 'ajax_update_fragments' ) );
		add_action( 'wp_ajax_nopriv_soow_update_fragments', array( __CLASS__, 'ajax_update_fragments' ) );
	}

	/**
	 * Handles adding to wish-list by URL
	 */
	public static function add_to_wishlist_action() {
		if ( empty( $_REQUEST['add_to_wishlist'] ) || ! is_numeric( $_REQUEST['add_to_wishlist'] ) ) {
			return;
		}

//		if ( ! Soo_Wishlist()->can_edit() ) {
//			wc_add_notice( esc_html__( 'You can not add items to this list', 'soow' ), 'error' );
//
//			wp_safe_redirect( soow_get_wishlist_url() );
//			exit;
//		}

		$product_id = absint( $_REQUEST['add_to_wishlist'] );
		$product    = wc_get_product( $product_id );

		if ( ! $product ) {
			return;
		}

		$added = Soo_Wishlist()->add( $product_id );

		if ( ! is_wp_error( $added ) ) {
			wc_add_notice( sprintf( esc_html__( '%s has been added to your wishlist', 'soow' ), $product->get_title() ), 'success' );
		} else {
			wc_add_notice( $added->get_error_message(), 'error' );
		}

		wp_safe_redirect( soow_get_wishlist_url() );
		exit;
	}

	/**
	 * Handles removing a product from wish-list by URL
	 */
	public static function remove_from_wishlist_action() {
		if ( empty( $_REQUEST['remove_from_wishlist'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		$key = $_REQUEST['remove_from_wishlist'];

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'soo_wishlist_' . $key ) ) {
			return;
		}

//		if ( ! Soo_Wishlist()->can_edit() ) {
//			wc_add_notice( esc_html__( 'You can not remove items from this list', 'soow' ), 'error' );
//
//			wp_safe_redirect( soow_get_wishlist_url() );
//			exit;
//		}

		$product           = Soo_Wishlist()->list->find( $key );
		$wishlist_page_url = soow_get_wishlist_url();

		if ( ! $product ) {
			wc_add_notice( esc_html__( 'This product is not exists in your wishlist', 'soow' ), 'error' );

			wp_safe_redirect( $wishlist_page_url );
			exit;
		}

		$result = Soo_Wishlist()->remove( $product['id'] );

		if ( ! is_wp_error( $result ) ) {
			wc_add_notice( sprintf( esc_html__( '%s has been removed from your wishlist', 'soow' ), get_the_title( $product['id'] ) ), 'success' );
		} else {
			wc_add_notice( $result->get_error_message(), 'error' );
		}

		wp_safe_redirect( $wishlist_page_url );
		exit;
	}

	/**
	 * Handle actions when user logout
	 * Destroy wish-list hash cookie
	 */
	public static function remove_cookie() {
		wc_setcookie( 'soo_wishlist_hash', null, time() - 3600 );
	}

	/**
	 * Ajax function adds a product to wish-list
	 */
	public static function ajax_add_to_wishlist() {
		if ( empty( $_REQUEST['product_id'] ) ) {
			wp_send_json_error( esc_html__( 'Invalid product ID', 'soow' ) );
			exit;
		}

		$product_id = absint( $_REQUEST['product_id'] );
		$product    = wc_get_product( $product_id );

		if ( ! $product ) {
			wp_send_json_error( esc_html__( 'Product is not exists', 'soow' ) );
			exit;
		}

		$added = Soo_Wishlist()->add( $product_id );

		if ( is_wp_error( $added ) ) {
			wp_send_json_error( $added->get_error_message() );
			exit;
		}

		$fragments = apply_filters( 'add_to_wishlist_fragments', array() );

		wp_send_json_success( array( 'fragments' => $fragments ) );
		exit;
	}

	/**
	 * Ajax function update wish-list fragments
	 */
	public static function ajax_update_fragments() {
		$fragments = apply_filters( 'add_to_wishlist_fragments', array() );

		wp_send_json_success( $fragments );
		exit;
	}
}