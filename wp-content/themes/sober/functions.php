<?php
/**
 * Sober functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Sober
 */

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function sober_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sober_content_width', 640 );
}

add_action( 'after_setup_theme', 'sober_content_width', 0 );

if ( ! function_exists( 'sober_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function sober_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'sober', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Supports WooCommerce plugin.
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-zoom' );
		//add_theme_support( 'wc-product-gallery-slider' );

		add_theme_support( 'post-formats', array( 'gallery', 'video', 'audio' ) );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary'   => esc_html__( 'Primary Menu', 'sober' ),
			'secondary' => esc_html__( 'Secondary Menu', 'sober' ),
			'topbar'    => esc_html__( 'Topbar Menu', 'sober' ),
			'footer'    => esc_html__( 'Footer Menu', 'sober' ),
			'socials'   => esc_html__( 'Footer Socials', 'sober' ),
			'mobile'    => esc_html__( 'Mobile Menu', 'sober' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		set_post_thumbnail_size( 100, 100, true );
		add_image_size( 'sober-blog-thumbnail', 750, 480, true );
		add_image_size( 'sober-blog-grid', 360, 240, true );
		add_image_size( 'sober-widget-thumbnail', 100, 100, true );
	}

endif;

add_action( 'after_setup_theme', 'sober_setup' );

/**
 * Initialize instances
 *
 * Priority 20 to make sure it run after plugin's callback, such as register custom post types...
 */
function sober_init() {
	Sober_WooCommerce::instance();

	if ( is_admin() ) {
		Sober_WC_Settings::init();
		Sober_WC_Term_Settings::init();
		Sober_WC_Product_Settings::init();

		new Sober_Mega_Menu_Edit();
	}
}

add_action( 'init', 'sober_init', 50 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function sober_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'sober' ),
		'id'            => 'blog-sidebar',
		'description'   => esc_html__( 'Add widgets here in order to display on blog', 'sober' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Page Sidebar', 'sober' ),
		'id'            => 'page-sidebar',
		'description'   => esc_html__( 'Add widgets here in order to display on pages', 'sober' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Shop Sidebar', 'sober' ),
		'id'            => 'shop-sidebar',
		'description'   => esc_html__( 'Add widgets here in order to display on shop pages', 'sober' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Shop Filter', 'sober' ),
		'id'            => 'shop-filter',
		'description'   => esc_html__( 'Add filter widgets here', 'sober' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	for ( $i = 1; $i < 5; $i++ ) {
		register_sidebar( array(
			'name'          => sprintf( esc_html__( 'Footer Sidebar %s', 'sober' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => esc_html__( 'Add widgets here in order to display on footer', 'sober' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}
}

add_action( 'widgets_init', 'sober_widgets_init' );


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Widgets
 */
require get_template_directory() . '/inc/widgets/widgets.php';

/**
 * Custom functions for WooCommerce
 */
require get_template_directory() . '/inc/class-sober-wc.php';

/**
 * Custom functions that act in the backend.
 */
require get_template_directory() . '/inc/admin/plugins.php';
require get_template_directory() . '/inc/admin/meta-boxes.php';
require get_template_directory() . '/inc/admin/woocommerce.php';
require get_template_directory() . '/inc/admin/nav-menus.php';
require get_template_directory() . '/inc/admin/ajax.php';

/**
 * Custom functions that act in the frontend.
 */
require get_template_directory() . '/inc/frontend/frontend.php';
require get_template_directory() . '/inc/frontend/header.php';
require get_template_directory() . '/inc/frontend/menus.php';
require get_template_directory() . '/inc/frontend/footer.php';
require get_template_directory() . '/inc/frontend/entry.php';
require get_template_directory() . '/inc/frontend/comments.php';
require get_template_directory() . '/inc/frontend/breadcrumbs.php';
