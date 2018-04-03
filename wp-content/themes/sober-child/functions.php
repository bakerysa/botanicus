<?php
/**
 * Sober functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Sober Child
 */

add_action( 'wp_enqueue_scripts', 'sober_child_enqueue_scripts', 20 );

function sober_child_enqueue_scripts() {
	wp_enqueue_style( 'sober-child', get_stylesheet_uri() );
}

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}

// Remove related products
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Trim zeros in price decimals
add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

add_filter( 'woocommerce_variation_option_name', 'display_price_in_variation_option_name' );

function display_price_in_variation_option_name( $term ) {
    global $wpdb, $product;

    if ( empty( $term ) ) return $term;
    if ( empty( $product->id ) ) return $term;

    $result = $wpdb->get_col( "SELECT slug FROM {$wpdb->prefix}terms WHERE name = '$term'" );

    $term_slug = ( !empty( $result ) ) ? $result[0] : $term;

    $query = "SELECT postmeta.post_id AS product_id
                FROM {$wpdb->prefix}postmeta AS postmeta
                    LEFT JOIN {$wpdb->prefix}posts AS products ON ( products.ID = postmeta.post_id )
                WHERE postmeta.meta_key LIKE 'attribute_%'
                    AND postmeta.meta_value = '$term_slug'
                    AND products.post_parent = $product->id";

    $variation_id = $wpdb->get_col( $query );

    $parent = wp_get_post_parent_id( $variation_id[0] );

    if ( $parent > 0 ) {
         $_product = new WC_Product_Variation( $variation_id[0] );
         return $term . ' (' . wp_kses( woocommerce_price( $_product->get_price() ), array() ) . ')';
    }
    return $term;

}
