<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sober
 */

get_header(); ?>

	<div id="primary" class="content-area <?php sober_content_columns() ?>">
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) :

			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

			if ( 'classic' == sober_get_option( 'blog_layout' ) ) {
				the_posts_pagination( array(
					'prev_text' => '<svg viewBox="0 0 20 20"><use xlink:href="#left-arrow"></use></svg>',
					'next_text' => '<svg viewBox="0 0 20 20"><use xlink:href="#right-arrow"></use></svg>',
				) );
			} else {
				printf(
					'<nav class="navigation posts-navigation ajax-navigation" role="navigation">%s</nav>',
					get_next_posts_link( '<span class="button-text">' . esc_html__( 'Load more', 'sober' ) . '</span><span class="loading-icon"><span class="bubble"><span class="dot"></span></span><span class="bubble"><span class="dot"></span></span><span class="bubble"><span class="dot"></span></span></span>' )
				);
			}

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
