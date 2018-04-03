<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sober
 */

if ( 'no-sidebar' == sober_get_layout() ) {
	return;
}

$sidebar         = 'blog-sidebar';
$sidebar_classes = array( 'col-md-4' );

if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
	$sidebar = 'shop-sidebar';
} elseif ( is_page() ) {
	$sidebar         = 'page-sidebar';
	$sidebar_classes = array( 'col-md-3' );
}

$sidebar_classes[] = $sidebar;
?>

<aside id="secondary" class="widget-area primary-sidebar <?php echo esc_attr( join( ' ', $sidebar_classes ) ) ?> " role="complementary">
	<?php dynamic_sidebar( $sidebar ); ?>
</aside><!-- #secondary -->
