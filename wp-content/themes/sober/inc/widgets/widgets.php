<?php
/**
 * Load and register widgets
 *
 * @package
 */

require_once get_theme_file_path( 'inc/widgets/social-media-links.php' );
require_once get_theme_file_path( 'inc/widgets/popular-posts.php' );

/**
 * Register widgets
 *
 * @since  1.0
 *
 * @return void
 */
function sober_register_widgets() {
	register_widget( 'Sober_Social_Links_Widget' );
	register_widget( 'Sober_Popular_Posts_Widget' );
}
add_action( 'widgets_init', 'sober_register_widgets' );

/**
 * Change markup of archive and category widget to include .count for post count
 *
 * @param string $output
 *
 * @return string
 */
function sober_widget_archive_count( $output ) {
	$output = preg_replace( '|\((\d+)\)|', '<span class="count">\\1</span>', $output );

	return $output;
}

add_filter( 'wp_list_categories', 'sober_widget_archive_count' );
add_filter( 'get_archives_link', 'sober_widget_archive_count' );