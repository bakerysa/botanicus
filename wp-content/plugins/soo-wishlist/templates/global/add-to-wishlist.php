<?php
/**
 * Template for displaying add to wishlist button.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/add-to-wishlist.php.
 *
 * @author        SooPlugins
 * @package       Soo Wishlist/Templates
 * @version       1.0.0
 */

echo apply_filters(
	'soo_wishlist_button',
	sprintf(
		'<a href="%s" data-product_id="%s" data-product_type="%s" class="%s" rel="nofollow">%s</a>',
		esc_url( $url ),
		esc_attr( $product_id ),
		esc_attr( $product_type ),
		esc_attr( implode( ' ', $class ) ),
		esc_html( $text )
	)
);