<?php
/**
 * Template part for displaying posts.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sober
 */

global $wp_query;

if ( 'grid' == sober_get_option( 'blog_layout' ) ) {
	$blog_image_size = 'sober-blog-grid';

	if ( 'no-sidebar' == sober_get_layout() ) {
		$blog_post_class = 'grid-post col-md-4';
	} else {
		$blog_post_class = 'grid-post col-md-6';
	}
} else {
	if ( ( $wp_query->current_post + 1 ) % 4 == 1 ) {
		$blog_image_size = 'sober-blog-thumbnail';
		$blog_post_class = 'main-post';
	} else {
		$blog_image_size = 'sober-blog-grid';
		$blog_post_class = 'clearfix sub-post';
	}
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $blog_post_class ); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink() ?>" class="post-thumbnail">
			<?php
			the_post_thumbnail( $blog_image_size );
			if ( 'gallery' == get_post_format() ) {
				?>
				<span class="format-icon">
					<svg viewBox="0 0 20 20">
						<use xlink:href="#gallery"></use>
					</svg>
				</span>
				<?php
			} elseif ( 'video' == get_post_format() ) {
				?>
				<span class="format-icon">
					<svg viewBox="0 0 20 20">
						<use xlink:href="#play"></use>
					</svg>
				</span>
				<?php
			}
			?>
		</a>
	<?php endif; ?>

	<div class="post-summary">

		<div class="entry-meta"><?php sober_entry_meta(); ?></div>
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-content -->

		<a class="line-hover read-more" href="<?php the_permalink() ?>"><?php esc_html_e( 'Read more', 'sober' ) ?></a>
	</div>
</article><!-- #post-## -->
