<?php

/**
 * Enqueue scripts and styles.
 */
function sober_scripts() {
	$version = wp_get_theme()->get( 'Version' );

	// Register then enqueue styles
	wp_register_style( 'sober-fonts', sober_fonts_url() );
	wp_register_style( 'bootstrap', get_theme_file_uri( 'css/bootstrap.css' ), array(), '3.3.6' );
	wp_register_style( 'font-awesome', get_theme_file_uri( 'css/font-awesome.min.css' ), array(), '4.7.0' );
	wp_register_style( 'photoswipe', get_theme_file_uri( 'css/photoswipe.css' ), array(), '4.1.1' );
	wp_register_style( 'sober', get_template_directory_uri() . '/style.css', array(
		'sober-fonts',
		'font-awesome',
		'bootstrap',
	), $version );

	wp_enqueue_style( 'sober' );

	// Register then enqueue scripts
	wp_register_script( 'isotope', get_theme_file_uri( 'js/isotope.pkgd.min.js' ), array( 'imagesloaded' ), '3.0.1', true );
	wp_register_script( 'owl-carousel', get_theme_file_uri( 'js/owl.carousel.min.js' ), array(), '2.2.1', true );
	wp_register_script( 'owl2row', get_theme_file_uri( 'js/owl2row.js' ), array( 'owl-carousel' ), '1.0', true );
	wp_register_script( 'jquery-fitvids', get_theme_file_uri( 'js/jquery.fitvids.js' ), array(), '1.1', true );
	wp_register_script( 'sticky-kit', get_theme_file_uri( 'js/sticky-kit.min.js' ), array( 'jquery' ), '1.1.3', true );
	wp_register_script( 'photoswipe', get_theme_file_uri( 'js/photoswipe.min.js' ), array(), '4.1.1', true );
	wp_register_script( 'photoswipe-ui-default', get_theme_file_uri( 'js/photoswipe-ui.min.js' ), array( 'photoswipe' ), '4.1.1', true );
	wp_register_script( 'simple-scrollbar', get_theme_file_uri( 'js/simple-scrollbar.min.js' ), array(), '0.2.1', true );
	wp_register_script( 'headroom', get_theme_file_uri( 'js/headroom.min.js' ), array(), '0.9.3', true );
	wp_register_script( 'rellax', get_theme_file_uri( 'js/rellax.min.js' ), array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'zoom', get_theme_file_uri( 'js/jquery.zoom.min.js' ), array( 'jquery' ), '1.7.18', true );
	wp_register_script( 'notify', get_theme_file_uri( 'js/notify.min.js' ), array( 'jquery' ), '0.4.2', true );

	if ( wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
		wp_enqueue_script( 'wc-add-to-cart-variation' );
	}

	if ( is_singular( 'product' ) && ( get_option( 'woocommerce_enable_lightbox' ) || current_theme_supports( 'wc-product-gallery-lightbox' ) ) ) {
		wp_enqueue_script( 'photoswipe-ui-default' );
	}

	if ( 'style-1' == sober_get_option( 'single_product_style' ) ) {
		wp_enqueue_script( 'sticky-kit' );
	} elseif ( 'style-3' == sober_get_option( 'single_product_style' ) ) {
		wp_enqueue_script( 'owl2row' );
	}

	if ( 'smart' == sober_get_option( 'header_sticky' ) ) {
		wp_enqueue_script( 'headroom' );
	}

	if ( 'minimal' != sober_get_option( 'page_header_style' ) && 'none' != sober_get_option( 'page_header_parallax' ) ) {
		wp_enqueue_script( 'rellax' );
	}

	if ( 'zoom' == sober_get_option( 'products_item_style' ) ) {
		wp_enqueue_script( 'zoom' );
	}

	if ( sober_get_option( 'products_sorting' ) && wp_script_is( 'select2', 'registered' ) && function_exists( 'WC' ) && ( is_shop() || is_product_taxonomy() ) ) {
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'select2' );
	}

	if ( sober_get_option( 'added_to_cart_notice' ) ) {
		wp_enqueue_script( 'notify' );
	}

	wp_register_script( 'sober', get_template_directory_uri() . '/js/script.js', array(
		'jquery',
		'imagesloaded',
		'isotope',
		'owl-carousel',
		'jquery-fitvids',
		'simple-scrollbar',
	), $version, true );

	wp_enqueue_script( 'sober' );
	wp_localize_script( 'sober', 'soberData', array(
		'sticky_header'             => sober_get_option( 'header_sticky' ),
		'quickview'                 => sober_get_option( 'product_quickview_behavior' ),
		'quickview_details'         => sober_get_option( 'product_quickview_detail_link' ),
		'shop_nav_type'             => sober_get_option( 'shop_nav_type' ),
		'page_header_parallax'      => sober_get_option( 'page_header_parallax' ),
		'menu_animation'            => sober_get_option( 'menu_animation' ),
		'open_cart_modal_after_add' => sober_get_option( 'open_cart_modal_after_add' ),
		'popup_frequency'           => sober_get_option( 'popup_frequency' ),
		'popup_visible'             => sober_get_option( 'popup_visible' ),
		'popup_visible_delay'       => sober_get_option( 'popup_visible_delay' ),
		'added_to_cart_notice'      => sober_get_option( 'added_to_cart_notice' ),
		'lightbox'                  => sober_get_option( 'product_lightbox' ),
		'zoom'                      => sober_get_option( 'product_zoom' ),
		'tab_behaviour'             => sober_get_option( 'products_toggle_behaviour' ),
		'single_ajax_add_to_cart'   => get_option( 'sober_enable_single_ajax_add_to_cart' ),
		'isRTL'                     => is_rtl(),
		'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
		'l10n' => array(
			'added_to_cart_notice' => esc_html__( 'Product is added to cart successfully', 'sober' ),
			'quick_view_details'   => esc_html__( 'View full product details', 'sober' ),
		),
	) );

	// Enqueue comment reply script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'sober_scripts' );

/**
 * Display site header
 */
function sober_header() {
	get_template_part( 'template-parts/header', sober_get_option( 'header_layout' ) );
}

add_action( 'sober_header', 'sober_header' );

/**
 * Display topbar
 */
function sober_topbar() {
	if ( ! sober_get_option( 'topbar_enable' ) ) {
		return;
	}

	get_template_part( 'template-parts/topbar' );
}

add_action( 'sober_before_header', 'sober_topbar' );

/**
 * Display page header
 */
function sober_page_header() {
	if ( ! sober_has_page_header() ) {
		return;
	}

	get_template_part( 'template-parts/page-header' );
}

add_action( 'sober_after_header', 'sober_page_header' );

/**
 * Display a special page header for WooCommerce pages
 */
function sober_woocommerce_pages_header() {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}

	$allow = is_cart() || is_account_page() || sober_is_order_tracking_page();

	if ( function_exists( 'soow_is_wishlist' ) ) {
		$allow = $allow || soow_is_wishlist();
	} elseif ( function_exists( 'yith_wcwl_is_wishlist_page' ) ) {
		$allow = $allow || yith_wcwl_is_wishlist_page();
	}

	if ( ! $allow ) {
		return;
	}

	$pages = array();

	// Prepare for cart links
	$pages['cart'] = sprintf(
		'<li class="shopping-cart-link line-hover %s"><a href="%s">%s<span class="count cart-counter">%d</span></a></li>',
		is_cart() ? 'active' : '',
		esc_url( wc_get_cart_url() ),
		esc_html__( 'Shopping Cart', 'sober' ),
		WC()->cart->get_cart_contents_count()
	);

	// Prepare for wishlist link
	if ( function_exists( 'soow_count_products' ) ) {
		$pages['wishlist'] = sprintf(
			'<li class="wishlist-link line-hover %s"><a href="%s">%s<span class="count wishlist-counter">%d</span></a></li>',
			soow_is_wishlist() ? 'active' : '',
			esc_url( soow_get_wishlist_url() ),
			esc_html__( 'Wishlist', 'sober' ),
			soow_count_products()
		);
	} elseif ( function_exists( 'yith_wcwl_count_products' ) ) {
		$wishlist_page_id = yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) );

		$pages['wishlist'] = sprintf(
			'<li class="wishlist-link line-hover %s"><a href="%s">%s<span class="count wishlist-counter">%d</span></a></li>',
			yith_wcwl_is_wishlist_page() ? 'active' : '',
			esc_url( get_permalink( $wishlist_page_id ) ),
			esc_html__( 'Wishlist', 'sober' ),
			yith_wcwl_count_products()
		);
	}

	// Prepare for order tracking link
	if ( $tracking_page_id = get_option( 'sober_order_tracking_page_id' ) ) {
		$pages['order_tracking'] = sprintf(
			'<li class="order-tracking-link line-hover %s"><a href="%s">%s</a></li>',
			sober_is_order_tracking_page() ? 'active' : '',
			esc_url( get_permalink( sober_get_translated_object_id( $tracking_page_id ) ) ),
			esc_html__( 'Order Tracking', 'sober' )
		);
	}

	// Prepare for account link
	if ( is_user_logged_in() ) {
		$pages['account'] = sprintf(
			'<li class="account-link line-hover %s"><a href="%s">%s</a></li>',
			is_account_page() ? 'active' : '',
			esc_url( wc_get_page_permalink( 'myaccount' ) ),
			esc_html__( 'My Account', 'sober' )
		);
	}

	// Prepare for login/logout link
	if ( is_user_logged_in() ) {
		$pages['logout'] = sprintf(
			'<li class="logout-link line-hover"><a href="%s">%s</a></li>',
			esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ),
			esc_html__( 'Logout', 'sober' )
		);
	} else {
		$pages['login'] = sprintf(
			'<li class="login-link line-hover %s"><a href="%s">%s</a></li>',
			is_account_page() ? 'active' : '',
			esc_url( wc_get_page_permalink( 'myaccount' ) ),
			esc_html__( 'Login', 'sober' )
		);
	}

	$pages = apply_filters( 'sober_woocomemrce_page_header_links', $pages );

	if ( ! empty( $pages ) ) {
		printf( '<div class="woocommerce-page-header"><div class="container"><ul>%s</ul></div></div>', implode( "\n", $pages ) );
	}
}

add_action( 'sober_after_header', 'sober_woocommerce_pages_header', 20 );


/**
 * Display the main breadcrumb
 */
function sober_site_breadcrumb() {
	if ( is_singular() && get_post_meta( get_the_ID(), 'hide_breadcrumb', true ) ) {
		return;
	}

	if ( is_home() && ! is_front_page() ) {
		$posts_page_id = get_option( 'page_for_posts' );

		if ( $posts_page_id && get_post_meta( $posts_page_id, 'hide_breadcrumb', true ) ) {
			return;
		}
	}

	if ( sober_get_option( 'show_breadcrumb' ) == false ) {
		return;
	}

	$yoast = get_option( 'wpseo_internallinks' );

	if ( function_exists( 'yoast_breadcrumb' ) && $yoast && $yoast['breadcrumbs-enable'] ) {
		yoast_breadcrumb( '<div class="breadcrumb">', '</div>' );
	} elseif ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		woocommerce_breadcrumb();
	} else {
		sober_breadcrumbs();
	}
}

/**
 * Change the taxonomy argument of breadcrumb for portfolio pages
 *
 * @param array $args
 *
 * @return array
 */
function sober_breadcrumbs_taxonomy( $args ) {
	if ( is_singular( 'portfolio' ) || is_tax() ) {
		$args['taxonomy'] = get_query_var( 'taxonomy' );
	}

	return $args;
}

add_filter( 'sober_breadcrumbs_args', 'sober_breadcrumbs_taxonomy' );

/**
 * Filter to archive title and add page title for singular pages
 *
 * @param string $title
 *
 * @return string
 */
function sober_the_archive_title( $title ) {
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
	} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$title = get_the_title( get_option( 'woocommerce_checkout_page_id' ) );
	} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
		$title = get_the_title( get_option( 'woocommerce_cart_page_id' ) );
	} elseif ( function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) {
		$title = get_the_title( get_option( 'yith_wcwl_wishlist_page_id' ) );
	} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$title = get_the_title( get_option( 'woocommerce_myaccount_page_id' ) );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_home() ) {
		$title = 'page' == get_option( 'show_on_front' ) ? get_the_title( get_option( 'page_for_posts' ) ) : esc_html__( 'Blog', 'sober' );
	} elseif ( is_search() ) {
		$title = esc_html__( 'Search', 'sober' );
	}

	return $title;
}

add_filter( 'get_the_archive_title', 'sober_the_archive_title' );

/**
 * Display top five categories on blog page
 */
function sober_blog_category_list() {
	if ( ! is_home() ) {
		return;
	}

	if ( ! sober_get_option( 'blog_categories' ) ) {
		return;
	}

	$cats = get_terms( array(
		'taxonomy' => 'category',
		'number'   => 5,
		'orderby'  => 'count',
		'order'    => 'DESC',
	) );

	if ( 'posts' == get_option( 'show_on_front' ) ) {
		$blog_url = home_url();
	} else {
		$blog_url = get_page_link( get_option( 'page_for_posts' ) );
	}

	$link_class = 'line-hover';

	if ( 'classic' == sober_get_option( 'blog_layout' ) && 'minimal' != sober_get_option( 'page_header_style' ) ) {
		$link_class .= ' line-white';
	}
	?>

	<div class="blog-cat-list">
		<ul class="cat-list">
			<li>
				<a href="<?php echo esc_url( $blog_url ) ?>" class="<?php echo esc_attr( $link_class ) ?> active"><?php esc_html_e( 'All Blog Posts', 'sober' ) ?></a>
			</li>
			<?php
			foreach ( $cats as $cat ) {
				printf(
					'<li><a href="%s" class="%s">%s</a></li>',
					esc_url( get_term_link( $cat, 'category' ) ),
					$link_class,
					esc_html( $cat->name )
				);
			}
			?>
		</ul>
	</div>

	<?php
}

add_action( 'sober_after_header', 'sober_blog_category_list', 20 );
