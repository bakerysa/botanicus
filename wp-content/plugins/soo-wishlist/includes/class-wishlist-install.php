<?php
/**
 * Installation actions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Soo_Wishlist_Install
 */
class Soo_Wishlist_Install {
	public static function install() {
		self::add_tables();
		self::add_pages();

		update_option( 'soo_wishlist_version', SOOW_VERSION );
		update_option( 'soo_wishlist_db_version', SOOW_DB_VERSION );
		add_action( 'init', 'flush_rewrite_rules' );
	}

	/**
	 * Add tables for a fresh installation
	 */
	public static function add_tables() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}


		$sql = "
CREATE TABLE {$wpdb->prefix}soo_wishlists (
  ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  title TEXT NOT NULL,
  slug varchar(200) NOT NULL,
  status varchar(20) NOT NULL DEFAULT 'private',
  user_id BIGINT UNSIGNED NULL,
  hash varchar(64) NOT NULL,
  created_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  is_default int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (ID),
  KEY slug (slug)
) $collate;
CREATE TABLE {$wpdb->prefix}soo_wishlists_items (
  ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id BIGINT UNSIGNED NOT NULL,
  quantity int(11) NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  wishlist_id BIGINT UNSIGNED NOT NULL,
  added_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (ID),
  KEY product_id (product_id)
) $collate;
		";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * Add "Wishlist" page
	 */
	private static function add_pages() {
		$page_id = get_option( 'soo_wishlist_page_id' );

		if ( $page_id && get_post( $page_id ) ) {
			return;
		}

		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => esc_sql( _x( 'wishlist', 'page_slug', 'soow' ) ),
			'post_title'     => esc_html__( 'Wishlist', 'soow' ),
			'post_content'   => '[soo_wishlist]',
			'post_parent'    => 0,
			'comment_status' => 'closed',
		);
		$page_id   = wp_insert_post( $page_data );

		update_option( 'soo_wishlist_page_id', $page_id );
	}
}