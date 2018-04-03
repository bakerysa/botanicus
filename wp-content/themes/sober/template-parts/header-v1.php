<?php
/**
 * Template part for displaying header with left menu ( default ).
 *
 * @package Sober
 */
?>

<div class="row">
	<nav id="site-navigation" class="main-navigation site-navigation col-xs-3 col-sm-3 col-md-3 col-lg-5">
		<span class="toggle-nav hidden-lg" data-target="mobile-menu"><span class="icon-nav"></span></span>
		<?php wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'nav-menu',
		) ); ?>
	</nav><!-- #site-navigation -->

	<div class="site-branding col-xs-6 col-sm-6 col-md-6 col-lg-2">
		<?php get_template_part( 'template-parts/logo' ); ?>
	</div><!-- .site-branding -->

	<div class="header-icon col-xs-3 col-sm-3 col-md-3 col-lg-5">
		<ul class="hidden-xs hidden-sm hidden-md">
			<?php sober_header_icons( 'v1' ) ?>
		</ul>

		<?php sober_mobile_header_icon() ?>
	</div><!-- .header-icon -->
</div>