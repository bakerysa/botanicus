<?php
/**
 * Wishlist functions
 *
 * @package Soo Wishlist
 */

/**
 * Count all products in a wishlist
 *
 * @return int
 */
function soow_count_products() {
	return Soo_Wishlist()->list->count();
}

/**
 * Get wishlist page URL
 *
 * @return false|string
 */
function soow_get_wishlist_url() {
	$id = soow_translated_object_id( get_option( 'soo_wishlist_page_id' ) );

	return get_permalink( $id );
}

/**
 * Check if current page is wishlist
 *
 * @return bool
 */
function soow_is_wishlist() {
	$page_id = soow_translated_object_id( get_option( 'soo_wishlist_page_id' ) );

	if ( ! $page_id ) {
		return false;
	}

	return is_page( $page_id );
}

/**
 * Retrieve translated page id, if WPML is installed
 *
 * @param $id int Original page id
 *
 * @return int Translation id
 */
function soow_translated_object_id( $id ) {
	if ( function_exists( 'wpml_object_id_filter' ) ) {
		return wpml_object_id_filter( $id, 'page', true );
	} elseif ( function_exists( 'icl_object_id' ) ) {
		return icl_object_id( $id, 'page', true );
	}

	return $id;
}

/**
 * Get template
 *
 * @param string $template
 * @param array  $args
 */
function soow_get_template( $template, $args = array() ) {
	if ( ! function_exists( 'wc_get_template' ) ) {
		return;
	}

	wc_get_template( $template, $args, '', Soo_Wishlist_Plugin::instance()->plugin_path() . '/templates/' );
}

/**
 * Set cookie
 *
 * @param  string $name  Cookie name
 * @param  mixed  $value Cookie value
 * @param int     $time  Time in seconds
 */
function soow_setcookie( $name, $value, $time = null ) {
	$time = $time != null ? $time : time() + apply_filters( 'soo_wishlist_cookie_expiration', 60 * 60 * 24 * 30 );

	if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
		$value = json_encode( stripslashes_deep( $value ) );
	}

	wc_setcookie( $name, $value, $time );
}

/**
 * Get cookie value
 *
 * @param      $name
 * @param bool $encode
 *
 * @return array|bool|mixed|null|object
 */
function soow_getcookie( $name, $encode = false ) {
	if ( isset( $_COOKIE[$name] ) ) {
		if ( $encode ) {
			return json_decode( stripslashes( $_COOKIE[$name] ), true );
		} else {
			return $_COOKIE[$name];
		}
	}

	return false;
}

/**
 * Main instance of plugin
 *
 * @return Soo_Wishlist
 */
function Soo_Wishlist() {
	return Soo_Wishlist::instance();
}