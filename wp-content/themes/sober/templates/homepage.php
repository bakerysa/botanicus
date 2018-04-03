<?php
/**
 * Template Name: Home Page
 *
 * The template file for displaying home page.
 */

get_header(); ?>

<?php
if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;
endif;
?>

<?php get_footer(); ?>
