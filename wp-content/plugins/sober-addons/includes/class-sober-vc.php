<?php

/**
 * Class Sober_Addons_VC
 */
class Sober_Addons_VC {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $_instance = null;

	/**
	 * Temporary cached terms variable
	 *
	 * @var array
	 */
	protected $terms = array();

	/**
	 * Main Instance.
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @return Sober_Addons_VC - Main instance.
	 */
	public static function init() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->modify_elements();
		$this->map_shortcodes();

		vc_set_as_theme();
		remove_action( 'admin_bar_menu', array( vc_frontend_editor(), 'adminBarEditLink' ), 1000 );

		add_filter( 'vc_google_fonts_get_fonts_filter', array( $this, 'add_google_fonts' ) );
	}

	/**
	 * Modify VC element params
	 */
	public function modify_elements() {
		// Add new option to Custom Header element
		vc_add_param( 'vc_custom_heading', array(
			'heading'     => esc_html__( 'Separate URL', 'sober' ),
			'description' => esc_html__( 'Do not wrap heading text with link tag. Display URL separately', 'sober' ),
			'type'        => 'checkbox',
			'param_name'  => 'separate_link',
			'value'       => array( esc_html__( 'Yes', 'sober' ) => 'yes' ),
			'weight'      => 0,
		) );
		vc_add_param( 'vc_custom_heading', array(
			'heading'     => esc_html__( 'Link Arrow', 'sober' ),
			'description' => esc_html__( 'Add an arrow to the separated link when hover', 'sober' ),
			'type'        => 'checkbox',
			'param_name'  => 'link_arrow',
			'value'       => array( esc_html__( 'Yes', 'sober' ) => 'yes' ),
			'weight'      => 0,
			'dependency'  => array(
				'element' => 'separate_link',
				'value'   => 'yes',
			),
		) );
	}

	/**
	 * Register custom shortcodes within Visual Composer interface
	 *
	 * @see http://kb.wpbakery.com/index.php?title=Vc_map
	 */
	public function map_shortcodes() {
		// Product Grid
		vc_map( array(
			'name'        => esc_html__( 'Product Grid', 'sober' ),
			'description' => esc_html__( 'Display products in grid', 'sober' ),
			'base'        => 'sober_product_grid',
			'icon'        => $this->get_icon( 'product-grid.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Number Of Products', 'sober' ),
					'description' => esc_html__( 'Total number of products you want to show', 'sober' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 15,
				),
				array(
					'heading'     => esc_html__( 'Columns', 'sober' ),
					'description' => esc_html__( 'Display products in how many columns', 'sober' ),
					'param_name'  => 'columns',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( '4 Columns', 'sober' ) => 4,
						esc_html__( '5 Columns', 'sober' ) => 5,
						esc_html__( '6 Columns', 'sober' ) => 6,
					),
				),
				array(
					'heading'     => esc_html__( 'Category', 'sober' ),
					'description' => esc_html__( 'Select what categories you want to use. Leave it empty to use all categories.', 'sober' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'value'       => '',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => $this->get_terms(),
					),
				),
				array(
					'heading'     => esc_html__( 'Product Type', 'sober' ),
					'description' => esc_html__( 'Select product type you want to show', 'sober' ),
					'param_name'  => 'type',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Recent Products', 'sober' )       => 'recent',
						esc_html__( 'Featured Products', 'sober' )     => 'featured',
						esc_html__( 'Sale Products', 'sober' )         => 'sale',
						esc_html__( 'Best Selling Products', 'sober' ) => 'best_sellers',
						esc_html__( 'Top Rated Products', 'sober' )    => 'top_rated',
					),
				),
				array(
					'heading'     => esc_html__( 'Load More Button', 'sober' ),
					'description' => esc_html__( 'Show load more button with ajax loading', 'sober' ),
					'param_name'  => 'load_more',
					'type'        => 'checkbox',
					'value'       => array(
						esc_html__( 'Yes', 'sober' ) => 'yes',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Product Tabs
		vc_map( array(
			'name'        => esc_html__( 'Product Tabs', 'sober' ),
			'description' => esc_html__( 'Product grid grouped by tabs', 'sober' ),
			'base'        => 'sober_product_tabs',
			'icon'        => $this->get_icon( 'product-tabs.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Number Of Products', 'sober' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 15,
					'description' => esc_html__( 'Total number of products will be display in single tab', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Columns', 'sober' ),
					'param_name'  => 'columns',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( '4 Columns', 'sober' ) => 4,
						esc_html__( '5 Columns', 'sober' ) => 5,
						esc_html__( '6 Columns', 'sober' ) => 6,
					),
					'description' => esc_html__( 'Display products in how many columns', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Tabs', 'sober' ),
					'description' => esc_html__( 'Select how to group products in tabs', 'sober' ),
					'param_name'  => 'filter',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Group by category', 'sober' ) => 'category',
						esc_html__( 'Group by feature', 'sober' )  => 'group',
					),
				),
				array(
					'heading'     => esc_html__( 'Categories', 'sober' ),
					'description' => esc_html__( 'Select what categories you want to use. Leave it empty to use all categories.', 'sober' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'value'       => '',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => $this->get_terms(),
					),
					'dependency'  => array(
						'element' => 'filter',
						'value'   => 'category',
					),
				),
				array(
					'heading'     => esc_html__( 'Tabs Effect', 'sober' ),
					'description' => esc_html__( 'Select the way tabs load products', 'sober' ),
					'param_name'  => 'filter_type',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Isotope Toggle', 'sober' ) => 'isotope',
						esc_html__( 'Ajax Load', 'sober' )      => 'ajax',
					),
				),
				array(
					'heading'     => esc_html__( 'Load More Button', 'sober' ),
					'param_name'  => 'load_more',
					'type'        => 'checkbox',
					'value'       => array(
						esc_html__( 'Yes', 'sober' ) => 'yes',
					),
					'description' => esc_html__( 'Show load more button with ajax loading', 'sober' ),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
				),
			),
		) );

		// Product Carousel
		vc_map( array(
			'name'        => esc_html__( 'Product Carousel', 'sober' ),
			'description' => esc_html__( 'Product carousel slider', 'sober' ),
			'base'        => 'sober_product_carousel',
			'icon'        => $this->get_icon( 'product-carousel.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Number Of Products', 'sober' ),
					'description' => esc_html__( 'Total number of products you want to show', 'sober' ),
					'param_name'  => 'number',
					'type'        => 'textfield',
					'value'       => 15,
				),
				array(
					'heading'     => esc_html__( 'Columns', 'sober' ),
					'description' => esc_html__( 'Display products in how many columns', 'sober' ),
					'param_name'  => 'columns',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( '3 Columns', 'sober' ) => 3,
						esc_html__( '4 Columns', 'sober' ) => 4,
						esc_html__( '5 Columns', 'sober' ) => 5,
						esc_html__( '6 Columns', 'sober' ) => 6,
					),
				),
				array(
					'heading'     => esc_html__( 'Product Type', 'sober' ),
					'description' => esc_html__( 'Select product type you want to show', 'sober' ),
					'param_name'  => 'type',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Recent Products', 'sober' )       => 'recent',
						esc_html__( 'Featured Products', 'sober' )     => 'featured',
						esc_html__( 'Sale Products', 'sober' )         => 'sale',
						esc_html__( 'Best Selling Products', 'sober' ) => 'best_sellers',
						esc_html__( 'Top Rated Products', 'sober' )    => 'top_rated',
					),
				),
				array(
					'heading'     => esc_html__( 'Categories', 'sober' ),
					'description' => esc_html__( 'Select what categories you want to use. Leave it empty to use all categories.', 'sober' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'value'       => '',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => $this->get_terms(),
					),
				),
				array(
					'heading'     => esc_html__( 'Auto Play', 'sober' ),
					'description' => esc_html__( 'Auto play speed in miliseconds. Enter "0" to disable auto play.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'autoplay',
					'value'       => 5000,
				),
				array(
					'heading'    => esc_html__( 'Loop', 'sober' ),
					'type'       => 'checkbox',
					'param_name' => 'loop',
					'value'      => array( esc_html__( 'Yes', 'sober' ) => 'yes' ),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Post Grid
		vc_map( array(
			'name'        => esc_html__( 'Sober Post Grid', 'sober' ),
			'description' => esc_html__( 'Display posts in grid', 'sober' ),
			'base'        => 'sober_post_grid',
			'icon'        => $this->get_icon( 'post-grid.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'description' => esc_html__( 'Number of posts you want to show', 'sober' ),
					'heading'     => esc_html__( 'Number of posts', 'sober' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 3,
				),
				array(
					'heading'     => esc_html__( 'Columns', 'sober' ),
					'description' => esc_html__( 'Display posts in how many columns', 'sober' ),
					'param_name'  => 'columns',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( '3 Columns', 'sober' ) => 3,
						esc_html__( '4 Columns', 'sober' ) => 4,
					),
				),
				array(
					'heading'     => esc_html__( 'Category', 'sober' ),
					'description' => esc_html__( 'Enter categories name', 'sober' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => $this->get_terms( 'category' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Hide Post Meta', 'sober' ),
					'description' => esc_html__( 'Hide information about date, category', 'sober' ),
					'type'        => 'checkbox',
					'param_name'  => 'hide_meta',
					'value'       => array( esc_html__( 'Yes', 'sober' ) => 'yes' ),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Countdown
		vc_map( array(
			'name'        => esc_html__( 'Countdown', 'sober' ),
			'description' => esc_html__( 'Countdown digital clock', 'sober' ),
			'base'        => 'sober_countdown',
			'icon'        => $this->get_icon( 'countdown.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Date', 'sober' ),
					'description' => esc_html__( 'Enter the date in format: YYYY/MM/DD', 'sober' ),
					'admin_label' => true,
					'type'        => 'textfield',
					'param_name'  => 'date',
				),
				array(
					'heading'     => esc_html__( 'Text Align', 'sober' ),
					'description' => esc_html__( 'Select text alignment', 'sober' ),
					'param_name'  => 'text_align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Left', 'sober' )   => 'left',
						esc_html__( 'Center', 'sober' ) => 'center',
						esc_html__( 'Right', 'sober' )  => 'right',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Button
		vc_map( array(
			'name'        => esc_html__( 'Sober Button', 'sober' ),
			'description' => esc_html__( 'Button in style', 'sober' ),
			'base'        => 'sober_button',
			'icon'        => $this->get_icon( 'button.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Text', 'sober' ),
					'description' => esc_html__( 'Enter button text', 'sober' ),
					'admin_label' => true,
					'type'        => 'textfield',
					'param_name'  => 'label',
				),
				array(
					'heading'    => esc_html__( 'URL (Link)', 'sober' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Style', 'sober' ),
					'description' => esc_html__( 'Select button style', 'sober' ),
					'param_name'  => 'style',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Normal', 'sober' )  => 'normal',
						esc_html__( 'Outline', 'sober' ) => 'outline',
						esc_html__( 'Light', 'sober' )   => 'light',
					),
				),
				array(
					'heading'     => esc_html__( 'Size', 'sober' ),
					'description' => esc_html__( 'Select button size', 'sober' ),
					'param_name'  => 'size',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Normal', 'sober' ) => 'normal',
						esc_html__( 'Large', 'sober' )  => 'large',
						esc_html__( 'Small', 'sober' )  => 'small',
					),
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( 'normal', 'outline' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Color', 'sober' ),
					'description' => esc_html__( 'Select button color', 'sober' ),
					'param_name'  => 'color',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Dark', 'sober' )  => 'dark',
						esc_html__( 'White', 'sober' ) => 'white',
					),
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( 'outline' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Alignment', 'sober' ),
					'description' => esc_html__( 'Select button alignment', 'sober' ),
					'param_name'  => 'align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Inline', 'sober' ) => 'inline',
						esc_html__( 'Left', 'sober' )   => 'left',
						esc_html__( 'Center', 'sober' ) => 'center',
						esc_html__( 'Right', 'sober' )  => 'right',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Banner
		vc_map( array(
			'name'        => esc_html__( 'Banner Image', 'sober' ),
			'description' => esc_html__( 'Banner image for promotion', 'sober' ),
			'base'        => 'sober_banner',
			'icon'        => $this->get_icon( 'banner.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image', 'sober' ),
					'description' => esc_html__( 'Banner Image', 'sober' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image size', 'sober' ),
					'description' => esc_html__( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => '',
				),
				array(
					'heading'     => esc_html__( 'Banner description', 'sober' ),
					'description' => esc_html__( 'A short text display before the banner text', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'desc',
				),
				array(
					'heading'     => esc_html__( 'Banner Text', 'sober' ),
					'description' => esc_html__( 'Enter the banner text', 'sober' ),
					'type'        => 'textarea',
					'param_name'  => 'content',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Banner Text Position', 'sober' ),
					'description' => esc_html__( 'Select text position', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'text_position',
					'value'       => array(
						esc_html__( 'Left', 'sober' )   => 'left',
						esc_html__( 'Center', 'sober' ) => 'center',
						esc_html__( 'Right', 'sober' )  => 'right',
					),
				),
				array(
					'type'       => 'font_container',
					'param_name' => 'font_container',
					'value'      => '',
					'settings'   => array(
						'fields' => array(
							'font_size',
							'line_height',
							'color',
							'font_size_description'   => esc_html__( 'Enter text font size.', 'sober' ),
							'line_height_description' => esc_html__( 'Enter text line height.', 'sober' ),
							'color_description'       => esc_html__( 'Select text color.', 'sober' ),
						),
					),
				),
				array(
					'heading'     => esc_html__( 'Use theme default font family?', 'sober' ),
					'description' => esc_html__( 'Use font family from the theme.', 'sober' ),
					'type'        => 'checkbox',
					'param_name'  => 'use_theme_fonts',
					'value'       => array( esc_html__( 'Yes', 'sober' ) => 'yes' ),
				),
				array(
					'type'       => 'google_fonts',
					'param_name' => 'google_fonts',
					'value'      => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
					'settings'   => array(
						'fields' => array(
							'font_family_description' => esc_html__( 'Select font family.', 'sober' ),
							'font_style_description'  => esc_html__( 'Select font styling.', 'sober' ),
						),
					),
					'dependency' => array(
						'element'            => 'use_theme_fonts',
						'value_not_equal_to' => 'yes',
					),
				),
				array(
					'heading'    => esc_html__( 'Link (URL)', 'sober' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Button Type', 'sober' ),
					'description' => esc_html__( 'Select button type', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'button_type',
					'value'       => array(
						esc_html__( 'Light Button', 'sober' )  => 'light',
						esc_html__( 'Normal Button', 'sober' ) => 'normal',
						esc_html__( 'Arrow Icon', 'sober' )    => 'arrow_icon',
					),
				),
				array(
					'heading'     => esc_html__( 'Button Text', 'sober' ),
					'description' => esc_html__( 'Enter the text for banner button', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'button_text',
					'dependency'  => array(
						'element' => 'button_type',
						'value'   => array( 'light', 'normal' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Button Visibility', 'sober' ),
					'description' => esc_html__( 'Select button visibility', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'button_visibility',
					'value'       => array(
						esc_html__( 'Always visible', 'sober' ) => 'always',
						esc_html__( 'When hover', 'sober' )     => 'hover',
						esc_html__( 'Hidden', 'sober' )         => 'hidden',
					),
				),
				array(
					'heading'     => esc_html__( 'Banner Color Scheme', 'sober' ),
					'description' => esc_html__( 'Select color scheme for description, button color', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'scheme',
					'value'       => array(
						esc_html__( 'Dark', 'sober' )  => 'dark',
						esc_html__( 'Light', 'sober' ) => 'light',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
				array(
					'heading'    => esc_html__( 'CSS box', 'sober' ),
					'type'       => 'css_editor',
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'sober' ),
				),
			),
		) );

		// Banner 2
		vc_map( array(
			'name'        => esc_html__( 'Banner Image 2', 'sober' ),
			'description' => esc_html__( 'Simple banner that supports multiple buttons', 'sober' ),
			'base'        => 'sober_banner2',
			'icon'        => $this->get_icon( 'banner2.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image', 'sober' ),
					'description' => esc_html__( 'Banner Image', 'sober' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Image size', 'sober' ),
					'description' => esc_html__( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => '',
				),
				array(
					'heading'     => esc_html__( 'Buttons', 'sober' ),
					'description' => esc_html__( 'Enter link and label for buttons.', 'sober' ),
					'type'        => 'param_group',
					'param_name'  => 'buttons',
					'params'      => array(
						array(
							'heading'    => esc_html__( 'Button Text', 'sober' ),
							'type'       => 'textfield',
							'param_name' => 'text',
						),
						array(
							'heading'    => esc_html__( 'Button Link', 'sober' ),
							'type'       => 'vc_link',
							'param_name' => 'link',
						),
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Banner 3
		vc_map( array(
			'name'        => esc_html__( 'Banner Image 3', 'sober' ),
			'description' => esc_html__( 'Simple banner with text at bottom', 'sober' ),
			'base'        => 'sober_banner3',
			'icon'        => $this->get_icon( 'banner3.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image', 'sober' ),
					'description' => esc_html__( 'Banner Image', 'sober' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image size', 'sober' ),
					'description' => esc_html__( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => '',
				),
				array(
					'heading'     => esc_html__( 'Banner Text', 'sober' ),
					'description' => esc_html__( 'Enter banner text', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'text',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Banner Text Position', 'sober' ),
					'description' => esc_html__( 'Select text position', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'text_align',
					'value'       => array(
						esc_html__( 'Left', 'sober' )   => 'left',
						esc_html__( 'Center', 'sober' ) => 'center',
						esc_html__( 'Right', 'sober' )  => 'right',
					),
				),
				array(
					'heading'    => esc_html__( 'Link (URL)', 'sober' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Button Text', 'sober' ),
					'description' => esc_html__( 'Enter the text for banner button', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'button_text',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Banner 4
		vc_map( array(
			'name'        => esc_html__( 'Banner Image 4', 'sober' ),
			'description' => esc_html__( 'Simple banner image with text', 'sober' ),
			'base'        => 'sober_banner4',
			'icon'        => $this->get_icon( 'banner4.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image', 'sober' ),
					'description' => esc_html__( 'Banner Image', 'sober' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image size', 'sober' ),
					'description' => esc_html__( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => 'full',
				),
				array(
					'heading'    => esc_html__( 'Link (URL)', 'sober' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
				array(
					'heading'    => esc_html__( 'Banner Content', 'sober' ),
					'type'       => 'textarea_html',
					'param_name' => 'content',
					'group'      => esc_html__( 'Text', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Button Text', 'sober' ),
					'description' => esc_html__( 'Enter the text for banner button', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'button_text',
					'group'       => esc_html__( 'Text', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Text Color Scheme', 'sober' ),
					'description' => esc_html__( 'Select color scheme for banner content', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'scheme',
					'group'       => esc_html__( 'Text', 'sober' ),
					'value'       => array(
						esc_html__( 'Dark', 'sober' )  => 'dark',
						esc_html__( 'Light', 'sober' ) => 'light',
					),
				),
				array(
					'heading'     => esc_html__( 'Content Horizontal Alignment', 'sober' ),
					'description' => esc_html__( 'Horizontal alignment of banner text', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'align_horizontal',
					'group'       => esc_html__( 'Text', 'sober' ),
					'value'       => array(
						esc_html__( 'Left', 'sober' )   => 'left',
						esc_html__( 'Center', 'sober' ) => 'center',
						esc_html__( 'Right', 'sober' )  => 'right',
					),
				),
				array(
					'heading'     => esc_html__( 'Content Vertical Alignment', 'sober' ),
					'description' => esc_html__( 'Vertical alignment of banner text', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'align_vertical',
					'group'       => esc_html__( 'Text', 'sober' ),
					'value'       => array(
						esc_html__( 'Top', 'sober' )    => 'top',
						esc_html__( 'Middle', 'sober' ) => 'middle',
						esc_html__( 'Bottom', 'sober' ) => 'bottom',
					),
				),
			),
		) );

		// Category Banner
		vc_map( array(
			'name'        => esc_html__( 'Category Banner', 'sober' ),
			'description' => esc_html__( 'Banner image with special style', 'sober' ),
			'base'        => 'sober_category_banner',
			'icon'        => $this->get_icon( 'category-banner.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image', 'sober' ),
					'description' => esc_html__( 'Banner Image', 'sober' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image Position', 'sober' ),
					'description' => esc_html__( 'Select image position', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'image_position',
					'value'       => array(
						esc_html__( 'Left', 'sober' )         => 'left',
						esc_html__( 'Right', 'sober' )        => 'right',
						esc_html__( 'Top', 'sober' )          => 'top',
						esc_html__( 'Bottom', 'sober' )       => 'bottom',
						esc_html__( 'Top Left', 'sober' )     => 'top-left',
						esc_html__( 'Top Right', 'sober' )    => 'top-right',
						esc_html__( 'Bottom Left', 'sober' )  => 'bottom-left',
						esc_html__( 'Bottom Right', 'sober' ) => 'bottom-right',
					),
				),
				array(
					'heading'     => esc_html__( 'Title', 'sober' ),
					'description' => esc_html__( 'The banner title', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Description', 'sober' ),
					'description' => esc_html__( 'The banner description', 'sober' ),
					'type'        => 'textarea',
					'param_name'  => 'content',
				),
				array(
					'heading'     => esc_html__( 'Text Position', 'sober' ),
					'description' => esc_html__( 'Select the position for title and description', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'text_position',
					'value'       => array(
						esc_html__( 'Top Left', 'sober' )     => 'top-left',
						esc_html__( 'Top Right', 'sober' )    => 'top-right',
						esc_html__( 'Middle Left', 'sober' )  => 'middle-left',
						esc_html__( 'Middle Right', 'sober' ) => 'middle-right',
						esc_html__( 'Bottom Left', 'sober' )  => 'bottom-left',
						esc_html__( 'Bottom Right', 'sober' ) => 'bottom-right',
					),
				),
				array(
					'heading'    => esc_html__( 'Link (URL)', 'sober' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Button Text', 'sober' ),
					'description' => esc_html__( 'Enter the text for banner button', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'button_text',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
				array(
					'heading'    => __( 'CSS box', 'sober' ),
					'type'       => 'css_editor',
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'sober' ),
				),
			),
		) );

		// Product
		vc_map( array(
			'name'        => esc_html__( 'Sober Product', 'sober' ),
			'description' => esc_html__( 'Display single product banner', 'sober' ),
			'base'        => 'sober_product',
			'icon'        => $this->get_icon( 'product.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Images', 'sober' ),
					'description' => esc_html__( 'Upload a product image', 'sober' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
					'value'       => '',
				),
				array(
					'heading'     => esc_html__( 'Product name', 'sober' ),
					'description' => esc_html__( 'Enter product name', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Product description', 'sober' ),
					'description' => esc_html__( 'Enter product description', 'sober' ),
					'type'        => 'textarea',
					'param_name'  => 'content',
				),
				array(
					'heading'     => esc_html__( 'Product price', 'sober' ),
					'description' => esc_html__( 'Enter product price. Only allow number.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'price',
				),
				array(
					'heading'    => esc_html__( 'Product URL', 'sober' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Banner Grid 4
		vc_map( array(
			'name'                    => esc_html__( 'Banner Grid 4', 'sober' ),
			'description'             => esc_html__( 'Arrange 4 banners per row with unusual structure.', 'sober' ),
			'base'                    => 'sober_banner_grid_4',
			'icon'                    => $this->get_icon( 'banner-grid-4.png' ),
			'category'                => esc_html__( 'Sober', 'sober' ),
			'js_view'                 => 'VcColumnView',
			'content_element'         => true,
			'show_settings_on_create' => false,
			'as_parent'               => array( 'only' => 'sober_banner,sober_banner2,sober_banner3' ),
			'params'                  => array(
				array(
					'heading'     => esc_html__( 'Reverse Order', 'sober' ),
					'description' => esc_html__( 'Reverse the order of banners inside this grid', 'sober' ),
					'param_name'  => 'reverse',
					'type'        => 'checkbox',
					'value'       => array( esc_html__( 'Yes', 'sober' ) => 'yes' ),
				),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Banner Grid 5
		vc_map( array(
			'name'                    => esc_html__( 'Banner Grid 5', 'sober' ),
			'description'             => esc_html__( 'Arrange 5 banners in 3 columns.', 'sober' ),
			'base'                    => 'sober_banner_grid_5',
			'icon'                    => $this->get_icon( 'banner-grid-5.png' ),
			'category'                => esc_html__( 'Sober', 'sober' ),
			'js_view'                 => 'VcColumnView',
			'content_element'         => true,
			'show_settings_on_create' => false,
			'as_parent'               => array( 'only' => 'sober_banner,sober_banner2,sober_banner3' ),
			'params'                  => array(
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Banner Grid 6
		vc_map( array(
			'name'                    => esc_html__( 'Banner Grid 6', 'sober' ),
			'description'             => esc_html__( 'Arrange 6 banners in 4 columns.', 'sober' ),
			'base'                    => 'sober_banner_grid_6',
			'icon'                    => $this->get_icon( 'banner-grid-6.png' ),
			'category'                => esc_html__( 'Sober', 'sober' ),
			'js_view'                 => 'VcColumnView',
			'content_element'         => true,
			'show_settings_on_create' => false,
			'as_parent'               => array( 'only' => 'sober_banner,sober_banner2,sober_banner3' ),
			'params'                  => array(
				array(
					'heading'     => esc_html__( 'Reverse Order', 'sober' ),
					'description' => esc_html__( 'Reverse the order of banners inside this grid', 'sober' ),
					'param_name'  => 'reverse',
					'type'        => 'checkbox',
					'value'       => array( esc_html__( 'Yes', 'sober' ) => 'yes' ),
				),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Circle Chart
		vc_map( array(
			'name'        => esc_html__( 'Circle Chart', 'sober' ),
			'description' => esc_html__( 'Circle chart with animation', 'sober' ),
			'base'        => 'sober_chart',
			'icon'        => $this->get_icon( 'chart.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Value', 'sober' ),
					'description' => esc_html__( 'Enter the chart value in percentage. Minimum 0 and maximum 100.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'value',
					'value'       => 100,
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Circle Size', 'sober' ),
					'description' => esc_html__( 'Width of the circle', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'size',
					'value'       => 200,
				),
				array(
					'heading'     => esc_html__( 'Circle thickness', 'sober' ),
					'description' => esc_html__( 'Width of the arc', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'thickness',
					'value'       => 8,
				),
				array(
					'heading'     => esc_html__( 'Color', 'sober' ),
					'description' => esc_html__( 'Pick color for the circle', 'sober' ),
					'type'        => 'colorpicker',
					'param_name'  => 'color',
					'value'       => '#6dcff6',
				),
				array(
					'heading'     => esc_html__( 'Label Source', 'sober' ),
					'description' => esc_html__( 'Chart label source', 'sober' ),
					'param_name'  => 'label_source',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Auto', 'sober' )   => 'auto',
						esc_html__( 'Custom', 'sober' ) => 'custom',
					),
				),
				array(
					'heading'     => esc_html__( 'Custom label', 'sober' ),
					'description' => esc_html__( 'Text label for the chart', 'sober' ),
					'param_name'  => 'label',
					'type'        => 'textfield',
					'dependency'  => array(
						'element' => 'label_source',
						'value'   => 'custom',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Message Box
		vc_map( array(
			'name'        => esc_html__( 'Sober Message Box', 'sober' ),
			'description' => esc_html__( 'Notification box with close button', 'sober' ),
			'base'        => 'sober_message_box',
			'icon'        => $this->get_icon( 'message-box.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'          => esc_html__( 'Type', 'sober' ),
					'description'      => esc_html__( 'Select message box type', 'sober' ),
					'edit_field_class' => 'vc_col-xs-12 vc_message-type',
					'type'             => 'dropdown',
					'param_name'       => 'type',
					'default'          => 'success',
					'admin_label'      => true,
					'value'            => array(
						esc_html__( 'Success', 'sober' )       => 'success',
						esc_html__( 'Informational', 'sober' ) => 'info',
						esc_html__( 'Error', 'sober' )         => 'danger',
						esc_html__( 'Warning', 'sober' )       => 'warning',
					),
				),
				array(
					'heading'    => esc_html__( 'Message Text', 'sober' ),
					'type'       => 'textarea_html',
					'param_name' => 'content',
					'holder'     => 'div',
				),
				array(
					'heading'     => esc_html__( 'Closeable', 'sober' ),
					'description' => esc_html__( 'Display close button for this box', 'sober' ),
					'type'        => 'checkbox',
					'param_name'  => 'closeable',
					'value'       => array(
						esc_html__( 'Yes', 'sober' ) => true,
					),
				),
				vc_map_add_css_animation(),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'param_name'  => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
				),
			),
		) );

		// Icon Box
		vc_map( array(
			'name'        => esc_html__( 'Icon Box', 'sober' ),
			'description' => esc_html__( 'Information box with icon', 'sober' ),
			'base'        => 'sober_icon_box',
			'icon'        => $this->get_icon( 'icon-box.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Icon library', 'sober' ),
					'description' => esc_html__( 'Select icon library.', 'sober' ),
					'param_name'  => 'icon_type',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Font Awesome', 'sober' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'sober' )  => 'openiconic',
						esc_html__( 'Typicons', 'sober' )     => 'typicons',
						esc_html__( 'Entypo', 'sober' )       => 'entypo',
						esc_html__( 'Linecons', 'sober' )     => 'linecons',
						esc_html__( 'Mono Social', 'sober' )  => 'monosocial',
						esc_html__( 'Material', 'sober' )     => 'material',
						esc_html__( 'Custom Image', 'sober' ) => 'image',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'sober' ),
					'description' => esc_html__( 'Select icon from library.', 'sober' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_fontawesome',
					'value'       => 'fa fa-adjust',
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'sober' ),
					'description' => esc_html__( 'Select icon from library.', 'sober' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_openiconic',
					'value'       => 'vc-oi vc-oi-dial',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'openiconic',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'openiconic',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'sober' ),
					'description' => esc_html__( 'Select icon from library.', 'sober' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_typicons',
					'value'       => 'typcn typcn-adjust-brightness',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'typicons',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'typicons',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'sober' ),
					'description' => esc_html__( 'Select icon from library.', 'sober' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_entypo',
					'value'       => 'entypo-icon entypo-icon-note',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'entypo',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'entypo',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'sober' ),
					'description' => esc_html__( 'Select icon from library.', 'sober' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_linecons',
					'value'       => 'vc_li vc_li-heart',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'linecons',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'linecons',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'sober' ),
					'description' => esc_html__( 'Select icon from library.', 'sober' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_monosocial',
					'value'       => 'vc-mono vc-mono-fivehundredpx',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'monosocial',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'monosocial',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'sober' ),
					'description' => esc_html__( 'Select icon from library.', 'sober' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_material',
					'value'       => 'vc-material vc-material-cake',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'material',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'material',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon Image', 'sober' ),
					'description' => esc_html__( 'Upload icon image', 'sober' ),
					'type'        => 'attach_image',
					'param_name'  => 'image',
					'value'       => '',
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'image',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon Style', 'sober' ),
					'description' => esc_html__( 'Select icon style', 'sober' ),
					'param_name'  => 'style',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Normal', 'sober' ) => 'normal',
						esc_html__( 'Circle', 'sober' ) => 'circle',
						esc_html__( 'Round', 'sober' )  => 'round',
					),
				),
				array(
					'heading'     => esc_html__( 'Title', 'sober' ),
					'description' => esc_html__( 'The box title', 'sober' ),
					'admin_label' => true,
					'param_name'  => 'title',
					'type'        => 'textfield',
					'value'       => esc_html__( 'I am Icon Box', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Content', 'sober' ),
					'description' => esc_html__( 'The box title', 'sober' ),
					'holder'      => 'div',
					'param_name'  => 'content',
					'type'        => 'textarea_html',
					'value'       => esc_html__( 'I am icon box. Click edit button to change this text.', 'sober' ),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Pricing Table
		vc_map( array(
			'name'        => esc_html__( 'Pricing Table', 'sober' ),
			'description' => esc_html__( 'Eye catching pricing table', 'sober' ),
			'base'        => 'sober_pricing_table',
			'icon'        => $this->get_icon( 'pricing-table.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Plan Name', 'sober' ),
					'admin_label' => true,
					'param_name'  => 'name',
					'type'        => 'textfield',
				),
				array(
					'heading'     => esc_html__( 'Price', 'sober' ),
					'description' => esc_html__( 'Plan pricing', 'sober' ),
					'param_name'  => 'price',
					'type'        => 'textfield',
				),
				array(
					'heading'     => esc_html__( 'Currency', 'sober' ),
					'description' => esc_html__( 'Price currency', 'sober' ),
					'param_name'  => 'currency',
					'type'        => 'textfield',
					'value'       => '$',
				),
				array(
					'heading'     => esc_html__( 'Recurrence', 'sober' ),
					'description' => esc_html__( 'Recurring payment unit', 'sober' ),
					'param_name'  => 'recurrence',
					'type'        => 'textfield',
					'value'       => esc_html__( 'Per Month', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Features', 'sober' ),
					'description' => esc_html__( 'Feature list of this plan. Click to arrow button to edit.', 'sober' ),
					'param_name'  => 'features',
					'type'        => 'param_group',
					'params'      => array(
						array(
							'heading'    => esc_html__( 'Feature name', 'sober' ),
							'param_name' => 'name',
							'type'       => 'textfield',
						),
						array(
							'heading'    => esc_html__( 'Feature value', 'sober' ),
							'param_name' => 'value',
							'type'       => 'textfield',
						),
					),
				),
				array(
					'heading'    => esc_html__( 'Button Text', 'sober' ),
					'param_name' => 'button_text',
					'type'       => 'textfield',
					'value'      => esc_html__( 'Get Started', 'sober' ),
				),
				array(
					'heading'    => esc_html__( 'Button Link', 'sober' ),
					'param_name' => 'button_link',
					'type'       => 'vc_link',
					'value'      => esc_html__( 'Get Started', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Table color', 'sober' ),
					'description' => esc_html__( 'Pick color scheme for this table. It will be applied to table header and button.', 'sober' ),
					'param_name'  => 'color',
					'type'        => 'colorpicker',
					'value'       => '#6dcff6',
				),
				vc_map_add_css_animation(),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'param_name'  => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
				),
			),
		) );

		// Google Map
		vc_map( array(
			'name'        => esc_html__( 'Sober Maps', 'sober' ),
			'description' => esc_html__( 'Google maps in style', 'sober' ),
			'base'        => 'sober_map',
			'icon'        => $this->get_icon( 'map.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'API Key', 'sober' ),
					'description' => esc_html__( 'Google requires an API key to work.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'api_key',
				),
				array(
					'heading'     => esc_html__( 'Address', 'sober' ),
					'description' => esc_html__( 'Enter address for map marker. If this option does not work correctly, use the Latitude and Longitude options bellow.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'address',
					'admin_label' => true,
				),
				array(
					'heading'          => esc_html__( 'Latitude', 'sober' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-xs-6',
					'param_name'       => 'lat',
					'admin_label'      => true,
				),
				array(
					'heading'          => esc_html__( 'Longitude', 'sober' ),
					'type'             => 'textfield',
					'param_name'       => 'lng',
					'edit_field_class' => 'vc_col-xs-6',
					'admin_label'      => true,
				),
				array(
					'heading'     => esc_html__( 'Marker', 'sober' ),
					'description' => esc_html__( 'Upload custom marker icon or leave this to use default marker.', 'sober' ),
					'param_name'  => 'marker',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Width', 'sober' ),
					'description' => esc_html__( 'Map width in pixel or percentage.', 'sober' ),
					'param_name'  => 'width',
					'type'        => 'textfield',
					'value'       => '100%',
				),
				array(
					'heading'     => esc_html__( 'Height', 'sober' ),
					'description' => esc_html__( 'Map height in pixel.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'height',
					'value'       => '625px',
				),
				array(
					'heading'     => esc_html__( 'Zoom', 'sober' ),
					'description' => esc_html__( 'Enter zoom level. The value is between 1 and 20.', 'sober' ),
					'param_name'  => 'zoom',
					'type'        => 'textfield',
					'value'       => '15',
				),
				array(
					'heading'          => esc_html__( 'Color', 'sober' ),
					'description'      => esc_html__( 'Select map color style', 'sober' ),
					'edit_field_class' => 'vc_col-xs-12 vc_btn3-colored-dropdown vc_colored-dropdown',
					'param_name'       => 'color',
					'type'             => 'dropdown',
					'value'            => array(
						esc_html__( 'Default', 'sober' )       => '',
						esc_html__( 'Grey', 'sober' )          => 'grey',
						esc_html__( 'Classic Black', 'sober' ) => 'inverse',
						esc_html__( 'Vista Blue', 'sober' )    => 'vista-blue',
					),
				),
				array(
					'heading'     => esc_html__( 'Content', 'sober' ),
					'description' => esc_html__( 'Enter content of info window.', 'sober' ),
					'type'        => 'textarea_html',
					'param_name'  => 'content',
					'holder'      => 'div',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Testimonial
		vc_map( array(
			'name'        => esc_html__( 'Testimonial', 'sober' ),
			'description' => esc_html__( 'Written review from a satisfied customer', 'sober' ),
			'base'        => 'sober_testimonial',
			'icon'        => $this->get_icon( 'testimonial.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Photo', 'sober' ),
					'description' => esc_html__( 'Author photo or avatar. Recommend 160x160 in dimension.', 'sober' ),
					'type'        => 'attach_image',
					'param_name'  => 'image',
				),
				array(
					'heading'     => esc_html__( 'Name', 'sober' ),
					'description' => esc_html__( 'Enter full name of the author', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'name',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Company', 'sober' ),
					'description' => esc_html__( 'Enter company name of author', 'sober' ),
					'param_name'  => 'company',
					'type'        => 'textfield',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Alignment', 'sober' ),
					'description' => esc_html__( 'Select testimonial alignment', 'sober' ),
					'param_name'  => 'align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Center', 'sober' ) => 'center',
						esc_html__( 'Left', 'sober' )   => 'left',
						esc_html__( 'Right', 'sober' )  => 'right',
					),
				),
				array(
					'heading'     => esc_html__( 'Content', 'sober' ),
					'description' => esc_html__( 'Testimonial content', 'sober' ),
					'type'        => 'textarea_html',
					'param_name'  => 'content',
					'holder'      => 'div',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Partners
		vc_map( array(
			'name'        => esc_html__( 'Partner Logos', 'sober' ),
			'description' => esc_html__( 'Show list of partner logo', 'sober' ),
			'base'        => 'sober_partners',
			'icon'        => $this->get_icon( 'partners.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image source', 'sober' ),
					'description' => esc_html__( 'Select images source', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'source',
					'value'       => array(
						esc_html__( 'Media library', 'sober' )  => 'media_library',
						esc_html__( 'External Links', 'sober' ) => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Images', 'sober' ),
					'description' => esc_html__( 'Select images from media library', 'sober' ),
					'type'        => 'attach_images',
					'param_name'  => 'images',
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading'     => esc_html__( 'Image size', 'sober' ),
					'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'img_size',
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading'     => esc_html__( 'External links', 'sober' ),
					'description' => esc_html__( 'Enter external links for partner logos (Note: divide links with linebreaks (Enter)).', 'sober' ),
					'type'        => 'exploded_textarea_safe',
					'param_name'  => 'custom_srcs',
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Image size', 'sober' ),
					'description' => esc_html__( 'Enter image size in pixels. Example: 200x100 (Width x Height).', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'external_img_size',
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Custom links', 'sober' ),
					'description' => esc_html__( 'Enter links for each image here. Divide links with linebreaks (Enter).', 'sober' ),
					'type'        => 'exploded_textarea_safe',
					'param_name'  => 'custom_links',
				),
				array(
					'heading'     => esc_html__( 'Custom link target', 'sober' ),
					'description' => esc_html__( 'Select where to open custom links.', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'custom_links_target',
					'value'       => array(
						esc_html__( 'Same window', 'sober' ) => '_self',
						esc_html__( 'New window', 'sober' )  => '_blank',
					),
				),
				array(
					'heading'     => esc_html__( 'Layout', 'sober' ),
					'description' => esc_html__( 'Select the layout images source', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'layout',
					'value'       => array(
						esc_html__( 'Bordered', 'sober' ) => 'bordered',
						esc_html__( 'Plain', 'sober' )    => 'plain',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Contact Box
		vc_map( array(
			'name'        => esc_html__( 'Contact Box', 'sober' ),
			'description' => esc_html__( 'Contact information', 'sober' ),
			'base'        => 'sober_contact_box',
			'icon'        => $this->get_icon( 'contact.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Address', 'sober' ),
					'description' => esc_html__( 'The office address', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'address',
					'holder'      => 'p',
				),
				array(
					'heading'     => esc_html__( 'Phone', 'sober' ),
					'description' => esc_html__( 'The phone number', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'phone',
					'holder'      => 'p',
				),
				array(
					'heading'     => esc_html__( 'Fax', 'sober' ),
					'description' => esc_html__( 'The fax number', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'fax',
					'holder'      => 'p',
				),
				array(
					'heading'     => esc_html__( 'Email', 'sober' ),
					'description' => esc_html__( 'The email adress', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'email',
					'holder'      => 'p',
				),
				array(
					'heading'     => esc_html__( 'Website', 'sober' ),
					'description' => esc_html__( 'The phone number', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'website',
					'holder'      => 'p',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Info List
		vc_map( array(
			'name'        => esc_html__( 'Info List', 'sober' ),
			'description' => esc_html__( 'List of information', 'sober' ),
			'base'        => 'sober_info_list',
			'icon'        => $this->get_icon( 'info-list.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Information', 'sober' ),
					'description' => esc_html__( 'Enter information', 'sober' ),
					'type'        => 'param_group',
					'param_name'  => 'info',
					'value'       => urlencode( json_encode( array(
						array(
							'icon'  => 'fa fa-home',
							'label' => esc_html__( 'Address', 'sober' ),
							'value' => '9606 North MoPac Expressway',
						),
						array(
							'icon'  => 'fa fa-phone',
							'label' => esc_html__( 'Phone', 'sober' ),
							'value' => '+1 248-785-8545',
						),
						array(
							'icon'  => 'fa fa-fax',
							'label' => esc_html__( 'Fax', 'sober' ),
							'value' => '123123123',
						),
						array(
							'icon'  => 'fa fa-envelope',
							'label' => esc_html__( 'Email', 'sober' ),
							'value' => 'sober@uix.store',
						),
						array(
							'icon'  => 'fa fa-globe',
							'label' => esc_html__( 'Website', 'sober' ),
							'value' => 'http://uix.store',
						),
					) ) ),
					'params'      => array(
						array(
							'type'       => 'iconpicker',
							'heading'    => esc_html__( 'Icon', 'sober' ),
							'param_name' => 'icon',
							'settings'   => array(
								'emptyIcon'    => false,
								'iconsPerPage' => 4000,
							),
						),
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Label', 'sober' ),
							'param_name'  => 'label',
							'admin_label' => true,
						),
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Value', 'sober' ),
							'param_name'  => 'value',
							'admin_label' => true,
						),
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// FAQ
		vc_map( array(
			'name'        => esc_html__( 'FAQ', 'sober' ),
			'description' => esc_html__( 'Question and answer toggle', 'sober' ),
			'base'        => 'sober_faq',
			'icon'        => $this->get_icon( 'faq.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'js_view'     => 'VcToggleView',
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Question', 'sober' ),
					'description' => esc_html__( 'Enter title of toggle block.', 'sober' ),
					'type'        => 'textfield',
					'holder'      => 'h4',
					'class'       => 'vc_toggle_title wpb_element_title',
					'param_name'  => 'title',
					'value'       => esc_html__( 'Question content goes here', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Answer', 'sober' ),
					'description' => esc_html__( 'Toggle block content.', 'sober' ),
					'type'        => 'textarea_html',
					'holder'      => 'div',
					'class'       => 'vc_toggle_content',
					'param_name'  => 'content',
					'value'       => esc_html__( 'Answer content goes here, click edit button to change this text.', 'sober' ),
				),
				array(
					'heading'     => esc_html__( 'Default state', 'sober' ),
					'description' => esc_html__( 'Select "Open" if you want toggle to be open by default.', 'sober' ),
					'type'        => 'dropdown',
					'param_name'  => 'open',
					'value'       => array(
						esc_html__( 'Closed', 'sober' ) => 'false',
						esc_html__( 'Open', 'sober' )   => 'true',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Team Member
		vc_map( array(
			'name'        => esc_html__( 'Team Member', 'sober' ),
			'description' => esc_html__( 'Single team member information', 'sober' ),
			'base'        => 'sober_team_member',
			'icon'        => $this->get_icon( 'member.png' ),
			'category'    => esc_html__( 'Sober', 'sober' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image', 'sober' ),
					'description' => esc_html__( 'Member photo', 'sober' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image Size', 'sober' ),
					'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => 'full',
				),
				array(
					'heading'     => esc_html__( 'Full Name', 'sober' ),
					'description' => esc_html__( 'Member name', 'sober' ),
					'type'        => 'textfield',
					'param_name'  => 'name',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Job', 'sober' ),
					'description' => esc_html__( 'The job/position name of member in your team', 'sober' ),
					'param_name'  => 'job',
					'type'        => 'textfield',
					'admin_label' => true,
				),
				array(
					'heading'    => esc_html__( 'Facebook', 'sober' ),
					'type'       => 'textfield',
					'param_name' => 'facebook',
				),
				array(
					'heading'    => esc_html__( 'Twitter', 'sober' ),
					'type'       => 'textfield',
					'param_name' => 'twitter',
				),
				array(
					'heading'    => esc_html__( 'Google Plus', 'sober' ),
					'type'       => 'textfield',
					'param_name' => 'google',
				),
				array(
					'heading'    => esc_html__( 'Pinterest', 'sober' ),
					'type'       => 'textfield',
					'param_name' => 'pinterest',
				),
				array(
					'heading'    => esc_html__( 'Linkedin', 'sober' ),
					'type'       => 'textfield',
					'param_name' => 'linkedin',
				),
				array(
					'heading'    => esc_html__( 'Youtube', 'sober' ),
					'type'       => 'textfield',
					'param_name' => 'youtube',
				),
				array(
					'heading'    => esc_html__( 'Instagram', 'sober' ),
					'type'       => 'textfield',
					'param_name' => 'instagram',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'sober' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'sober' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );
	}

	/**
	 * Get Icon URL
	 *
	 * @param string $file_name The icon file name with extension
	 *
	 * @return string Full URL of icon image
	 */
	protected function get_icon( $file_name ) {

		if ( file_exists( SOBER_ADDONS_DIR . 'assets/icons/' . $file_name ) ) {
			$url = SOBER_ADDONS_URL . 'assets/icons/' . $file_name;
		} else {
			$url = SOBER_ADDONS_URL . 'assets/icons/default.png';
		}

		return $url;
	}

	/**
	 * Get category for auto complete field
	 *
	 * @param string $taxonomy Taxnomy to get terms
	 *
	 * @return array
	 */
	protected function get_terms( $taxonomy = 'product_cat' ) {
		// We don't want to query all terms again
		if ( isset( $this->terms[ $taxonomy ] ) ) {
			return $this->terms[ $taxonomy ];
		}

		$cats = get_terms( $taxonomy );
		if ( ! $cats || is_wp_error( $cats ) ) {
			return array();
		}

		$categories = array();
		foreach ( $cats as $cat ) {
			$categories[] = array(
				'label' => $cat->name,
				'value' => $cat->slug,
				'group' => 'category',
			);
		}

		// Store this in order to avoid double query this
		$this->terms[ $taxonomy ] = $categories;

		return $categories;
	}

	/**
	 * Add new fonts into Google font list
	 *
	 * @param array $fonts Array of objects
	 *
	 * @return array
	 */
	public function add_google_fonts( $fonts ) {
		$fonts[] = (object) array(
			'font_family' => 'Amatic SC',
			'font_styles' => '400,700',
			'font_types'  => '400 regular:400:normal,700 regular:700:normal',
		);

		$fonts[] = (object) array(
			'font_family' => 'Montez',
			'font_styles' => '400',
			'font_types'  => '400 regular:400:normal',
		);

		usort( $fonts, array( $this, 'sort_fonts' ) );

		return $fonts;
	}

	/**
	 * Sort fonts base on name
	 *
	 * @param object $a
	 * @param object $b
	 *
	 * @return int
	 */
	private function sort_fonts( $a, $b ) {
		return strcmp( $a->font_family, $b->font_family );
	}
}

