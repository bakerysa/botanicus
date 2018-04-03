<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Sober
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function sober_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of layout style
	$classes[] = sober_get_option( 'layout_style' );

	// Adds a class of layout
	$classes[] = 'sidebar-' . sober_get_layout();

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds classes of topbar
	if ( sober_get_option( 'topbar_enable' ) ) {
		$classes[] = 'topbar-enabled';
		$classes[] = 'topbar-' . sober_get_option( 'topbar_color' );
	} else {
		$classes[] = 'topbar-disabled';
	}

	// Adds a class of header layout
	$classes[] = 'header-' . sober_get_option( 'header_layout' );

	// Adds a class for header sticky
	$sticky = sober_get_option( 'header_sticky' );
	if ( $sticky && 'none' != $sticky ) {
		$classes[] = 'header-sticky';
		$classes[] = 'header-sticky-' . $sticky;
	}

	// Adds classes of header background
	// Only apply text color for header transparent
	$default_header_bg    = sober_get_option( 'header_bg' );
	$default_header_color = sober_get_option( 'header_text_color' );

	if ( sober_has_page_header() ) {
		if ( 'minimal' == sober_get_option( 'page_header_style' ) ) {
			$classes[] = 'header-white';
		} else {
			if ( function_exists( 'is_checkout' ) && is_checkout() ) {
				$classes[] = 'header-white';
			} elseif ( is_page() ) {
				$header_bg    = get_post_meta( get_the_ID(), 'site_header_bg', true );
				$header_color = get_post_meta( get_the_ID(), 'site_header_text_color', true );
				$header_bg    = $header_bg ? $header_bg : $default_header_bg;
				$header_color = $header_color ? $header_color : $default_header_color;

				$classes[] = 'header-' . $header_bg;

				if ( 'transparent' == $header_bg ) {
					$classes[] = 'header-text-' . $header_color;
				}
			} elseif ( is_home() ) {
				$blog_page_id = get_option( 'page_for_posts' );
				$header_bg    = get_post_meta( $blog_page_id, 'site_header_bg', true );
				$header_color = get_post_meta( $blog_page_id, 'site_header_text_color', true );
				$header_bg    = $header_bg ? $header_bg : $default_header_bg;
				$header_color = $header_color ? $header_color : $default_header_color;

				$classes[] = 'header-' . $header_bg;

				if ( 'transparent' == $header_bg ) {
					$classes[] = 'header-text-' . $header_color;
				}
			} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
				$shop_page_id = wc_get_page_id( 'shop' );
				$header_bg    = get_post_meta( $shop_page_id, 'site_header_bg', true );
				$header_color = get_post_meta( $shop_page_id, 'site_header_text_color', true );
				$header_bg    = $header_bg ? $header_bg : $default_header_bg;
				$header_color = $header_color ? $header_color : $default_header_color;

				$classes[] = 'header-' . $header_bg;

				if ( 'transparent' == $header_bg ) {
					$classes[] = 'header-text-' . $header_color;
				}
			} elseif ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) {
				$portfolio_page_id = get_option( 'sober_portfolio_page_id' );
				$header_bg         = get_post_meta( $portfolio_page_id, 'site_header_bg', true );
				$header_color      = get_post_meta( $portfolio_page_id, 'site_header_text_color', true );
				$header_bg         = $header_bg ? $header_bg : $default_header_bg;
				$header_color      = $header_color ? $header_color : $default_header_color;

				$classes[] = 'header-' . $header_bg;

				if ( 'transparent' == $header_bg ) {
					$classes[] = 'header-text-' . $header_color;
				}
			} else {
				$classes[] = 'header-' . $default_header_bg;

				if ( 'transparent' == $default_header_bg ) {
					$classes[] = 'header-text-' . sober_get_option( 'header_text_color' );
				}
			}
		}
	} else {
		if ( is_404() ) {
			$classes[] = 'header-white';
		} elseif ( ( is_front_page() && ! is_home() ) || is_page_template( 'templates/homepage.php' ) ) {
			$classes[] = 'header-' . $default_header_bg;

			if ( 'transparent' == $default_header_bg ) {
				$classes[] = 'header-text-' . sober_get_option( 'header_text_color' );
			}
		} elseif ( is_page() ) {
			$header_bg    = get_post_meta( get_the_ID(), 'site_header_bg', true );
			$header_color = get_post_meta( get_the_ID(), 'site_header_text_color', true );
			$header_bg    = $header_bg ? $header_bg : 'white';
			$header_color = $header_color ? $header_color : 'dark';

			$classes[] = 'header-' . $header_bg;

			if ( 'transparent' == $header_bg ) {
				$classes[] = 'header-text-' . $header_color;
			}
		} else {
			$classes[] = 'header-white';
		}
	}

	// Adds a class for header hover effect
	if ( sober_get_option( 'header_hover' ) ) {
		$classes[] = 'header-hoverable';
	}

	// Adds classes of page header
	$classes[] = sober_has_page_header() ? 'has-page-header' : 'no-page-header';
	$classes[] = 'page-header-style-' . sober_get_option( 'page_header_style' );
	if ( 'minimal' != sober_get_option( 'page_header_style' ) ) {
		$classes[] = sober_get_page_header_image() ? 'page-header-image' : 'page-header-color';
	}

	if ( sober_has_page_header() ) {
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			$color = 'dark';
		} elseif ( is_singular() ) {
			$color = get_post_meta( get_the_ID(), 'page_header_text_color', true );
			$color = $color ? $color : sober_get_option( 'page_header_text_color' );
		} elseif ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$color = sober_get_option( 'shop_page_header_text_color' );

			if ( is_product_taxonomy() ) {
				$term_id    = get_queried_object_id();
				$term_color = get_term_meta( $term_id, 'page_header_text_color', true );
				$color      = $term_color ? $term_color : $color;
			} elseif ( is_shop() ) {
				$shop_page_id = wc_get_page_id( 'shop' );
				$shop_color   = get_post_meta( $shop_page_id, 'page_header_text_color', true );
				$color        = $shop_color ? $shop_color : $color;
			}
		} elseif ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) {
			// This is masonry style because we checked has_page_header before
			$color = sober_get_option( 'portfolio_page_header_text_color' );
		} elseif ( is_home() && ! is_front_page() ) {
			$posts_page_id = get_option( 'page_for_posts' );

			if ( $posts_page_id ) {
				$color = get_post_meta( $posts_page_id, 'page_header_text_color', true );
				$color = $color ? $color : sober_get_option( 'page_header_text_color' );
			} else {
				$color = sober_get_option( 'page_header_text_color' );
			}
		} else {
			$color = sober_get_option( 'page_header_text_color' );
		}

		$classes[] = 'page-header-text-' . $color;
	}

	// Adds a class for hidden page title
	if ( is_page() && get_post_meta( get_the_ID(), 'hide_page_title', true ) ) {
		$classes[] = 'page-title-hidden';
	}

	// Adds a class of product hover image
	if ( ! in_array( sober_get_option( 'products_item_style' ), array( 'slider', 'zoom' ) ) && sober_get_option( 'product_hover_thumbnail' ) ) {
		$classes[] = 'shop-hover-thumbnail';
	}

	// Adds a class of product quick view
	if ( sober_get_option( 'product_quickview' ) ) {
		$classes[] = 'product-quickview-enable';
	}

	// Adds a class of order tracking page
	if ( sober_is_order_tracking_page() ) {
		$classes[] = 'woocommerce-page woocommerce-order-tracking';
	}

	// Add a class of blog layout
	if ( is_search() ) {
		$classes[] = 'blog-classic';
	} else {
		$classes[] = 'blog-' . sober_get_option( 'blog_layout' );
	}

	// Adds a class of single product layout
	if ( is_singular( array( 'product' ) ) ) {
		$classes[] = 'product-' . sober_get_option( 'single_product_style' );
	}

	// Adds a class of shop navigation type
	$classes[] = 'shop-navigation-' . sober_get_option( 'shop_nav_type' );

	if ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) {
		$classes[] = 'portfolio-' . sober_get_option( 'portfolio_style' );
	}

	// Adds a class for showing product buttons of shop page on mobile
	if ( sober_get_option( 'mobile_shop_add_to_cart' ) ) {
		$classes[] = 'mobile-shop-buttons';
	}

	return $classes;
}

add_filter( 'body_class', 'sober_body_classes' );

/**
 * Enqueues front-end CSS for theme customization
 */
function sober_customize_css() {
	$css = '';

	// Page header image
	if ( $image = sober_get_page_header_image() ) {
		$css .= '.page-header { background-image: url(' . esc_url( $image ) . '); }';
	}

	// 404 Background image
	if ( is_404() ) {
		$image = sober_get_option( '404_bg' );

		if ( $image ) {
			$css .= 'body.error404 { background-image: url( ' . esc_url( $image ) . '); }';
		}
	}

	// Logo width
	$logo_width  = sober_get_option( 'logo_width' );
	$logo_height = sober_get_option( 'logo_height' );

	if ( $logo_width || $logo_height ) {
		$logo_css = '';

		if ( $logo_width ) {
			$logo_css .= 'width: ' . esc_attr( $logo_width ) . 'px;';
		}

		if ( $logo_height ) {
			$logo_css .= 'height: ' . esc_attr( $logo_height ) . 'px;';
		}

		$css .= '.site-branding .logo img {' . $logo_css . '}';
	}

	// Logo margin
	$logo_margin = sober_get_option( 'logo_position' );
	$logo_margin = array_filter( $logo_margin );

	if ( $logo_margin ) {
		$logo_css = '';

		foreach( $logo_margin as $pos => $value ) {
			$logo_css .= 'margin-' . $pos . ': ' . esc_attr( $value ) . ';';
		}

		$css .= '.site-branding .logo {' . $logo_css . '}';
	}

	// Preloader
	if ( sober_get_option( 'preloader' ) ) {
		$color = sober_get_option( 'preloader_background_color' );

		$css .= $color ? '.preloader { background-color: ' . $color . '; }' : '';
	}

	// Popup
	$css .= '.sober-popup.popup-layout-fullscreen, .sober-popup-backdrop {background-color: ' . esc_attr( sober_get_option( 'popup_overlay_color' ) ) . '; }';

	$css .= sober_typography_css();

	// Page content spacings
	if ( is_page() && ! is_page_template( 'templates/homepage.php' ) ) {
		$top_spacing    = get_post_meta( get_the_ID(), 'top_spacing', true );
		$bottom_spacing = get_post_meta( get_the_ID(), 'bottom_spacing', true );

		if ( 'none' == $top_spacing ) {
			$css .= '.site-content { padding-top: 0 !important; }';
		} elseif ( 'custom' == $top_spacing && ( $top = get_post_meta( get_the_ID(), 'top_padding', true ) ) ) {
			$css .= '.site-content { padding-top: ' . $top . ' !important; }';
		}

		if ( 'none' == $bottom_spacing ) {
			$css .= '.site-content { padding-bottom: 0 !important; }';
		} elseif ( 'custom' == $bottom_spacing && ( $bottom = get_post_meta( get_the_ID(), 'bottom_padding', true ) ) ) {
			$css .= '.site-content { padding-bottom: ' . $bottom . ' !important; }';
		}
	}

	wp_add_inline_style( 'sober', $css );
}

add_action( 'wp_enqueue_scripts', 'sober_customize_css', 20 );

/**
 * Open content container
 */
function sober_open_content_container() {
	$class = 'container';

	if (
		( function_exists( 'WC' ) && ( is_shop() || is_product_taxonomy() || is_product() ) ) ||
		is_page_template( 'templates/homepage.php' ) ||
		( ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) && 'fullwidth' == sober_get_option( 'portfolio_style' ) )
	) {
		$class = 'sober-container';
	}

	$class = apply_filters( 'sober_site_content_container_class', $class );

	echo '<div class="' . $class . '">';

	if ( 'no-sidebar' != sober_get_layout() ) {
		echo '<div class="row">';
	}
}

add_action( 'sober_before_content', 'sober_open_content_container', 5 );

/**
 * Close content container
 */
function sober_close_content_container() {
	echo '</div>';

	if ( 'no-sidebar' != sober_get_layout() ) {
		echo '</div>';
	}
}

add_action( 'sober_after_content', 'sober_close_content_container', 50 );

/**
 * Add icon list as svg at the footer
 * It is hidden
 */
function sober_include_shadow_icons() {
	echo '<div id="svg-defs" class="svg-defs hidden">';
	include get_template_directory() . '/images/sprite.svg';
	echo '</div>';
}

add_action( 'sober_before_site', 'sober_include_shadow_icons' );
