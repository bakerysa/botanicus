<?php
/**
 * Template part for displaying header with ... .
 *
 * @package Sober
 */
?>

<div id="site-navigation" class="site-nav">
	<span class="toggle-nav hidden-md hidden-lg" data-target="mobile-menu"><span class="icon-nav"></span></span>
	<span class="toggle-nav hidden-xs hidden-sm" data-target="primary-menu"><span class="icon-nav"></span></span>
</div><!-- #site-navigation -->

<div class="site-branding">
	<?php get_template_part( 'template-parts/logo' ); ?>
</div><!-- .site-branding -->

<div class="header-icon">

	<ul class="hidden-xs hidden-sm hidden-md">
		<?php sober_header_icons( 'v6' ) ?>
	</ul>

	<?php sober_mobile_header_icon() ?>
</div><!-- .header-icon -->
