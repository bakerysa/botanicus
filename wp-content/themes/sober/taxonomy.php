<?php
/**
 * The template for displaying taxonomy archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sober
 */

if ( is_object_in_taxonomy( 'portfolio', get_query_var( 'taxonomy' ) ) ) {
	get_template_part( 'archive', 'portfolio' );
} else {
	get_template_part( 'archive' );
}