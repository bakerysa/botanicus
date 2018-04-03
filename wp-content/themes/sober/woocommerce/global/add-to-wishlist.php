<?php
/**
 * Template for displaying add to wishlist button.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/add-to-wishlist.php.
 *
 * @author        SooPlugins
 * @package       Soo Wishlist/Templates
 * @version       1.0.0
 */

echo apply_filters(
	'soo_wishlist_button',
	sprintf(
		'<a href="%s" data-product_id="%s" data-product_type="%s" class="%s" rel="nofollow">
			<svg viewBox="0 0 20 20" class="like"><use xlink:href="#heart-wishlist-like"></use></svg>
			<svg viewBox="0 0 20 20" class="liked"><use xlink:href="#heart-wishlist-liked"></use></svg>
			<span class="indent-text">%s</span>
		</a>',
		esc_url( $url ),
		esc_attr( $product_id ),
		esc_attr( $product_type ),
		esc_attr( implode( ' ', $class ) ),
		esc_html( $text )
	)
);