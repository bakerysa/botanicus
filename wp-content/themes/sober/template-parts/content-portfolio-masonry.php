<?php
/**
 * Template part for displaying projects in masonry portfolio page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sober
 */

global $wp_query;
$current_project = $wp_query->current_post + 1;
$current_project_mod = $current_project % 6;
$project_thumbnail_size = 'sober-portfolio-large';
$project_class = 'col-sm-6 col-md-6';

if ( in_array( $current_project_mod, array( 2, 4 ) ) ) {
	$project_thumbnail_size = 'sober-portfolio-wide';
}
?>
<div id="project-<?php the_ID() ?>" <?php post_class( $project_class ) ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink() ?>" class="project-thumbnail">
			<?php the_post_thumbnail( $project_thumbnail_size ) ?>
		</a>
	<?php endif; ?>
	<div class="project-summary">
		<h3 class="project-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
		<?php
		if ( has_term( null, 'portfolio_type' ) ) {
			the_terms( get_the_ID(), 'portfolio_type', '<div class="project-type">', ', ', '</div>' );
		}
		?>
	</div>
</div>