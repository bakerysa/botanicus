<?php
/**
 * Template part for display single post content
 *
 * @package Sober
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( 'no-sidebar' == sober_get_layout() ) : ?>
	<div class="row">
		<div class="col-md-8 col-md-offset-2 post-summary">
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
				<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sober' ),
					'after'  => '</div>',
				) );
				?>
			</div>
			<!-- .entry-content -->

			<?php if ( has_tag() ) : ?>

			<footer class="entry-footer">
				<?php sober_entry_footer() ?>
			</footer>
			<!-- .entry-footer -->

			<?php endif; ?>

			<?php if ( 'no-sidebar' == sober_get_layout() ) : ?>
		</div>
	</div>
<?php endif; ?>

</article><!-- #post-## -->

