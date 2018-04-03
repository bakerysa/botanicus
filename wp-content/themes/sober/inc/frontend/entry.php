<?php
/**
 * Hooks and functions for blog and other content types
 *
 * @package Sober
 */


/**
 * Change more string at the end of the excerpt
 *
 * @since  1.0
 *
 * @param string $more
 *
 * @return string
 */
function sober_excerpt_more( $more ) {
	$more = '&hellip;';

	return $more;
}

add_filter( 'excerpt_more', 'sober_excerpt_more' );

/**
 * Change length of the excerpt
 *
 * @since  1.0
 *
 * @param string $length
 *
 * @return string
 */
function sober_excerpt_length( $length ) {
	$excerpt_length = absint( sober_get_option( 'excerpt_length' ) );

	if ( $excerpt_length > 0 ) {
		return $excerpt_length;
	}

	return $length;
}

add_filter( 'excerpt_length', 'sober_excerpt_length' );

/**
 * Print HTML for single post header
 */
function sober_single_post_header() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$fields = (array) sober_get_option( 'post_header' );
	?>

	<div class="entry-header">
		<?php
		foreach ( $fields as $field ) {
			switch ( $field ) {
				case 'meta':
					echo '<div class="entry-meta">';
					sober_entry_meta();
					echo '</div>';
					break;

				case 'title':
					the_title( '<h1 class="entry-title">', '</h1>' );
					break;

				case 'share':
					sober_social_share();
					break;

				case 'image':
					echo '<div class="entry-thumbnail">';
					sober_entry_thumbnail( 'full' );
					echo '</div><!-- .entry-meta -->';
			}
		}
		?>

	</div>

	<?php
}

add_action( 'sober_before_content', 'sober_single_post_header' );