<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sober
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'sober_before_site' ) ?>

<div id="page" class="site">
	<?php do_action( 'sober_before_header' ); ?>

	<header id="masthead" class="site-header" role="banner">
		<div class="<?php echo 'full-width' == sober_get_option( 'header_wrapper' ) ? 'sober-container clearfix' : 'container' ?>">

			<?php do_action( 'sober_header' ); ?>

		</div>
	</header><!-- #masthead -->

	<?php do_action( 'sober_after_header' ); ?>

	<div id="content" class="site-content">

		<?php do_action( 'sober_before_content' ); ?>
