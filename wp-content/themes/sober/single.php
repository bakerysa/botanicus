<?php
/**
 * The template for displaying all single posts.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Sober
 */

get_header(); ?>

	<div id="primary" class="content-area <?php sober_content_columns() ?>">
		<main id="main" class="site-main" role="main">

			<?php

			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'single' );

				if ( 'no-sidebar' == sober_get_layout() ) :
					echo '<div class="row"><div class="col-md-8 col-md-offset-2">';
				endif;

				if ( sober_get_option( 'post_author_box' ) ) {
					get_template_part( 'template-parts/biography' );
				}

				if ( sober_get_option( 'post_navigation' ) ) {
					the_post_navigation( array(
						'prev_text' => '<svg viewBox="0 0 20 20"><use xlink:href="#left-arrow"></use></svg><span>' . esc_html__( 'Previous Post', 'sober' ) . '</span>',
						'next_text' => '<span>' . esc_html__( 'Next Post', 'sober' ) . '</span><svg viewBox="0 0 20 20"><use xlink:href="#right-arrow"></use></svg>',
					) );
				}

				if ( sober_get_option( 'post_related_posts' ) ) {
					get_template_part( 'template-parts/related-posts' );
				}

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

				if ( 'no-sidebar' == sober_get_layout() ) :
					echo '</div></div>';
				endif;

			endwhile; // End of the loop.

			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
