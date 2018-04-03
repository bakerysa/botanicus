<?php
/**
 * Template part for display single post content
 *
 * @package Sober
 */
?>

<div id="project-<?php the_ID() ?>" <?php post_class() ?>>
	<header class="project-header entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="project-image"><?php the_post_thumbnail( 'sober-portfolio-single' ) ?></div>
		<?php endif; ?>

		<?php the_terms( get_the_ID(), 'portfolio_type', '<div class="project-meta entry-meta">', ', ', '</div>' ); ?>
		<?php the_title( '<h1 class="project-title entry-title">', '</h1>' ) ?>
		<?php if ( sober_get_option( 'project_share' ) ) { sober_social_share(); } ?>
	</header>

	<div class="project-content entry-content col-lg-8 col-md-12 col-lg-offset-2">
		<?php the_content(); ?>
		<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sober' ),
			'after'  => '</div>',
		) );
		?>
	</div>
</div>
