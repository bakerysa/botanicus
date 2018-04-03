<?php
/**
 * Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Soo_Wishlist_Shortcodes
 */
class Soo_Wishlist_Shortcodes {
	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'add_to_wishlist' => 'add_to_wishlist',
			'soo_wishlist'    => 'wishlist',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( $shortcode, array( __CLASS__, $function ) );
		}
	}

	/**
	 * Add to wishlist button shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function add_to_wishlist( $atts, $content = null ) {
		global $product;

		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts, 'add_to_wishlist' );

		$content = $content ? $content : esc_html__( 'Add to wishlist', 'soow' );
		$content = apply_filters( 'soo_wishlist_button_text', $content, $atts );
		$item    = $atts['id'] ? wc_get_product( intval( $atts['id'] ) ) : $product;
		$item_id = $item->get_id();

		$exists = Soo_Wishlist()->list->in_wishlist( $item );

		$args = array(
			'class'        => array(
				'button',
				'add-to-wishlist-button',
				'add-to-wishlist-' . $item_id,
				$exists ? 'added' : '',
			),
			'product_type' => method_exists( $item, 'get_type' ) ? $item->get_type() : $item->product_type,
			'product_id'   => $item_id,
			'text'         => $content,
			'exists'       => $exists,
			'url'          => $exists ? soow_get_wishlist_url() : add_query_arg( array( 'add_to_wishlist' => $item_id ) ),
		);

		ob_start();

		soow_get_template( 'global/add-to-wishlist.php', $args );

		return ob_get_clean();
	}

	/**
	 * Wishlist shortcode
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function wishlist( $atts ) {
		$atts = shortcode_atts( array(
			'hash' => '',
		), $atts, 'soo_wishlist' );

		$hash = $atts['hash'] ? $atts['hash'] : get_query_var( 'wishlist_hash' );

		if ( $hash ) {
			$list = new Soo_Wishlist_List( array( 'hash' => $hash ) );
		} else {
			$list = Soo_Wishlist()->list;
		}

		$template = $list->count() ? 'wishlist/wishlist.php' : 'wishlist/empty.php';

		$args = array(
			'list'    => $list,
			'options' => array(
				'show_price'        => get_option( 'soo_wishlist_show_price', 'yes' ),
				'show_stock_status' => get_option( 'soo_wishlist_show_stock_status', 'yes' ),
				'show_button'       => get_option( 'soo_wishlist_show_button', 'yes' ),
			),
		);

		ob_start();

		soow_get_template( $template, $args );

		return '<div class="woocommerce soo-wishlist">' . ob_get_clean() . '</div>';
	}
}