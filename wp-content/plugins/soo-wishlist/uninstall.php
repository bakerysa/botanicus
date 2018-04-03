<?php
/**
 * Uninstall plugin
 */

// If uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function soo_wishlist_uninstall() {
	global $wpdb;

	// define local private attribute
	$wpdb->soo_wishlists       = $wpdb->prefix . 'soo_wishlists';
	$wpdb->soo_wishlists_items = $wpdb->prefix . 'soo_wishlists_items';

	// Delete option from options table
	delete_option( 'soo_wishlist_version' );
	delete_option( 'soo_wishlist_db_version' );

	//remove any additional options and custom table
	$sql = "DROP TABLE IF EXISTS `" . $wpdb->soo_wishlists . "`";
	$wpdb->query( $sql );
	$sql = "DROP TABLE IF EXISTS `" . $wpdb->soo_wishlists_items . "`";
	$wpdb->query( $sql );
}


if ( ! is_multisite() ) {
	soo_wishlist_uninstall();
} else {
	global $wpdb;
	$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
	$original_blog_id = get_current_blog_id();

	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		yith_wcwl_uninstall();
	}

	switch_to_blog( $original_blog_id );
}