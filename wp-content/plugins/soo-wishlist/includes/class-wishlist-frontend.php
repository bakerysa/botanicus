<?php

/**
 * Frontend hooks
 */
class Soo_Wishlist_Frontend {
	/**
	 * Initialize hooks
	 */
	public static function init() {
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		add_action( 'soo_wishlist_before_list', 'wc_print_notices' );
		add_action( 'soo_wishlist_after_list', array( __CLASS__, 'sharing_template' ) );

		add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'single_product_button' ) );
	}

	/**
	 * Add CSS classes to body
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public static function body_class( $classes ) {
		if ( soow_is_wishlist() ) {
			$classes[] = 'woocommerce-page';
			$classes[] = 'woocommerce-wishlist';
		}

		return $classes;
	}

	/**
	 * Load scripts
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( 'soo-wishlist', Soo_Wishlist_Plugin::instance()->plugin_url() . '/assets/css/wishlist.css', array(), SOOW_VERSION );

		wp_enqueue_script( 'soo-wishlist', Soo_Wishlist_Plugin::instance()->plugin_url() . '/assets/js/wishlist.js', array( 'jquery' ), SOOW_VERSION, true );

		wp_localize_script( 'soo-wishlist', 'soowData', array(
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'wishlisturl' => get_permalink( get_option( 'soo_wishlist_page_id' ) ),
			'fragments'   => apply_filters( 'add_to_cart_fragments', array() ),
		) );
	}

	/**
	 * Print sharing template after wishlist
	 */
	public static function sharing_template( $list ) {
		if ( ! apply_filters( 'soo_wishlist_sharing', true ) ) {
			return;
		}

		$list_hash = $list->__get( 'hash' );
		$list_hash = $list_hash ? $list_hash : get_query_var( 'wishlist_hash' );

		if ( ! $list_hash ) {
			return;
		}

		$socials = apply_filters( 'soo_wishlist_sharing_socials', array(
			'facebook' => get_option( 'soo_wishlist_share_facebook', 'yes' ),
			'google'   => get_option( 'soo_wishlist_share_google', 'yes' ),
			'twitter'  => get_option( 'soo_wishlist_share_twitter', 'yes' ),
			'email'    => get_option( 'soo_wishlist_share_email', 'yes' ),
		) );

		if ( ! in_array( 'yes', $socials ) ) {
			return;
		}

		$args = array(
			'wishlist_url' => trailingslashit( soow_get_wishlist_url() ) . $list_hash,
			'socials'      => $socials,
		);

		soow_get_template( 'wishlist/share.php', $args );
	}

	/**
	 * Display add wishlist button in the single product
	 */
	public static function single_product_button() {
		echo do_shortcode( '[add_to_wishlist]' );
	}
}