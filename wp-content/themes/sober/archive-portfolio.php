<?php
/**
 * The template for displaying portfolio archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sober
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			if ( have_posts() ) :

				if ( sober_get_option( 'portfolio_filter' ) ) {
					sober_portfolio_filter();
				}

				echo '<div class="portfolio-items portfolio-' . esc_attr( sober_get_option( 'portfolio_style' ) ) . ' row">';

				/* Start the Loop */
				while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content-portfolio', sober_get_option( 'portfolio_style' ) );

				endwhile;

				echo '</div><!-- .portfolio-items -->';

				printf(
					'<nav class="navigation portfolio-navigation ajax-navigation" role="navigation">%s</nav>',
					get_next_posts_link( '<span class="button-text">' . esc_html__( 'Load more', 'sober' ) . '</span><span class="loading-icon"><span class="bubble"><span class="dot"></span></span><span class="bubble"><span class="dot"></span></span><span class="bubble"><span class="dot"></span></span></span>' )
				);

			else :

				get_template_part( 'template-parts/content', 'none' );

			endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
