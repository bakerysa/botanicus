<?php
/**
 * The template part for displaying related posts
 *
 * @package Sober
 */

// Only support posts
if ( 'post' != get_post_type() ) {
	return;
}

$related_posts = new WP_Query(
	array(
		'posts_per_page'         => 3,
		'ignore_sticky_posts'    => 1,
		'category__in'           => wp_get_post_categories( get_the_ID() ),
		'post__not_in'           => array( get_the_ID() ),
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	)
);

if ( ! $related_posts->have_posts() ) {
	return;
}
?>

	<div class="related-posts">
		<h2 class="related-title"><?php esc_html_e( 'You Might Also Like', 'sober' ) ?></h2>
		<div class="related-content row">
			<?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>

				<div <?php post_class( 'col-sm-12 col-sm-6 col-md-4' ) ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink() ?>" class="post-thumbnail" rel="bookmark"><?php the_post_thumbnail( 'sober-blog-grid' ) ?></a>
					<?php endif; ?>

					<h3 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title() ?></a></h3>
				</div>

			<?php endwhile; ?>
		</div>
	</div>

<?php
wp_reset_postdata();