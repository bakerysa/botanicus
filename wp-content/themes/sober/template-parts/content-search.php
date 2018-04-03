<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sober
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'sub-post clearfix' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink() ?>" class="post-thumbnail">
			<?php
			the_post_thumbnail( 'sober-blog-grid' );
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
		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta"><?php sober_entry_meta(); ?></div>
		<?php endif; ?>

		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

		<?php if ( 'product' === get_post_type() ) : ?>
			<?php woocommerce_template_loop_price(); ?>
		<?php endif; ?>

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-content -->

		<a class="line-hover read-more" href="<?php the_permalink() ?>"><?php esc_html_e( 'Read more', 'sober' ) ?></a>
	</div>
</article><!-- #post-## -->
