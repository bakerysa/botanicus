<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Sober
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found container">
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( '404', 'sober' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<?php esc_html_e( 'The page you are looking for not available!', 'sober' ); ?>
				</div><!-- .page-content -->

				<div class="page-search clearfix">
					<h4><?php esc_html_e( 'Search', 'sober' ); ?></h4>
					<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label>
							<input type="search" class="search-field" placeholder="<?php esc_html_e( 'Enter keywords', 'sober' ) ?>" value="" name="s">
						</label>
						<input type="submit" class="search-submit" value="<?php esc_attr_e( 'Search', 'sober' ) ?>">
						<svg width="20" height="20"><use xlink:href="#search"></use></svg>
					</form>
				</div>
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
