<?php
/**
 * Template part for displaying header with ... .
 *
 * @package Sober
 */
?>

<div class="row">
	<div class="header-icon header-icon-left col-xs-3 col-sm-3 col-md-3 col-lg-2">
		<span class="toggle-nav hidden-lg" data-target="mobile-menu"><span class="icon-nav"></span></span>

		<ul class="hidden-xs hidden-sm hidden-md">
			<?php sober_header_icons( 'v4', 'left' ) ?>
		</ul>
	</div><!-- .header-icon -->

	<div class="header-content col-xs-6 col-sm-6 col-md-6 col-lg-8">
		<nav class="main-navigation primary-nav site-navigation hidden-xs hidden-sm hidden-md">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-menu' ) ); ?>
		</nav><!-- #site-navigation -->

		<div class="site-branding">
			<?php get_template_part( 'template-parts/logo' ); ?>
		</div><!-- .site-branding -->

		<nav class="main-navigation secondary-nav site-navigation hidden-xs hidden-sm hidden-md">
			<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'container' => false, 'menu_class' => 'nav-menu' ) ); ?>
		</nav><!-- #site-navigation -->
	</div>

	<div class="header-icon header-icon-right col-xs-3 col-sm-3 col-md-3 col-lg-2">
		<ul class="hidden-xs hidden-sm hidden-md">
			<?php sober_header_icons( 'v4', 'right' ) ?>
		</ul>

		<?php sober_mobile_header_icon() ?>
	</div><!-- .header-icon -->
</div>