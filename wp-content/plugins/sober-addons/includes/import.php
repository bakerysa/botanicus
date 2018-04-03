<?php
/**
 * Register one click import demo data
 */

add_filter( 'soo_demo_packages', 'sober_addons_import_register' );

function sober_addons_import_register() {
	return array(
		array(
			'name'       => 'Minimal',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/minimal/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/minimal/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/minimal/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home Page 1',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Modern',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/modern/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/modern/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/modern/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home Page 2',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Classic',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/classic/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/classic/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/classic/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home Page 3',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Clean',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/clean/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/clean/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/clean/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home Page 4',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'secondary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Categories V1',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/categories/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/categories/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/categories/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home Page 5',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Categories V2',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/categories_2/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/categories_2/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/categories_2/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home Page 6',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'secondary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Best Sellings',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/bestselling/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/bestselling/customizer.dat',
			'pages'      => array(
				'front_page' => 'Home Page 7',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Parallax',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/parallax/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/parallax/customizer.dat',
			'pages'      => array(
				'front_page' => 'Home Page 8',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'secondary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Full Screen',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/fullscreen/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/fullscreen/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/fullscreen/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home Page 9',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Full Slider',
			'content'    => 'http://uix.store/data/sober/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/fullslider/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/fullslider/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/fullslider/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home Page 10',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 897,
					'height' => 908,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Furniture',
			'content'    => 'http://uix.store/data/sober/furniture/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/furniture/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/furniture/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/furniture/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/furniture/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 937,
					'height' => 875,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
		array(
			'name'       => 'Furniture 2',
			'content'    => 'http://uix.store/data/sober/furniture2/demo-content.xml',
			'widgets'    => 'http://uix.store/data/sober/furniture2/widgets.wie',
			'preview'    => 'http://uix.store/data/sober/furniture2/preview.jpg',
			'customizer' => 'http://uix.store/data/sober/furniture2/customizer.dat',
			'sliders'    => 'http://uix.store/data/sober/furniture2/sliders.zip',
			'pages'      => array(
				'front_page' => 'Home V12',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => array(
				'shop_catalog_image_size'   => array(
					'width'  => 433,
					'height' => 516,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 937,
					'height' => 875,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 80,
					'height' => 100,
					'crop'   => 1,
				),
			),
		),
	);
}

add_action( 'soodi_after_setup_pages', 'sober_addons_import_order_tracking' );

/**
 * Update more page options
 *
 * @param $pages
 */
function sober_addons_import_order_tracking( $pages ) {
	if ( isset( $pages['order_tracking'] ) ) {
		$order = get_page_by_title( $pages['order_tracking'] );

		if ( $order ) {
			update_option( 'sober_order_tracking_page_id', $order->ID );
		}
	}

	if ( isset( $pages['portfolio'] ) ) {
		$portfolio = get_page_by_title( $pages['portfolio'] );

		if ( $portfolio ) {
			update_option( 'sober_portfolio_page_id', $portfolio->ID );
		}
	}
}

add_action( 'soodi_before_import_content', 'sober_addons_import_product_attributes' );

/**
 * Prepare product attributes before import demo content
 *
 * @param $file
 */
function sober_addons_import_product_attributes( $file ) {
	global $wpdb;

	if ( ! class_exists( 'WXR_Parser' ) ) {
		require_once WP_PLUGIN_DIR . '/soo-demo-importer/includes/parsers.php';
	}

	$parser      = new WXR_Parser();
	$import_data = $parser->parse( $file );

	if ( isset( $import_data['posts'] ) ) {
		$posts = $import_data['posts'];

		if ( $posts && sizeof( $posts ) > 0 ) {
			foreach ( $posts as $post ) {
				if ( 'product' === $post['post_type'] ) {
					if ( ! empty( $post['terms'] ) ) {
						foreach ( $post['terms'] as $term ) {
							if ( strstr( $term['domain'], 'pa_' ) ) {
								if ( ! taxonomy_exists( $term['domain'] ) ) {
									$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $term['domain'] ) );

									// Create the taxonomy
									if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
										$attribute = array(
											'attribute_label'   => $attribute_name,
											'attribute_name'    => $attribute_name,
											'attribute_type'    => 'select',
											'attribute_orderby' => 'menu_order',
											'attribute_public'  => 0
										);
										$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
										delete_transient( 'wc_attribute_taxonomies' );
									}

									// Register the taxonomy now so that the import works!
									register_taxonomy(
										$term['domain'],
										apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ),
										apply_filters( 'woocommerce_taxonomy_args_' . $term['domain'], array(
											'hierarchical' => true,
											'show_ui'      => false,
											'query_var'    => true,
											'rewrite'      => false,
										) )
									);
								}
							}
						}
					}
				}
			}
		}
	}
}