<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/
 */


/**
 * Registering meta boxes
 *
 * Using Meta Box plugin: http://www.deluxeblogtips.com/meta-box/
 *
 * @see http://www.deluxeblogtips.com/meta-box/docs/define-meta-boxes
 *
 * @param array $meta_boxes Default meta boxes. By default, there are no meta boxes.
 *
 * @return array All registered meta boxes
 */
function sober_register_meta_boxes( $meta_boxes ) {
	// Post format's meta box
	$meta_boxes[] = array(
		'id'       => 'post-format-settings',
		'title'    => esc_html__( 'Format Details', 'sober' ),
		'pages'    => array( 'post' ),
		'context'  => 'normal',
		'priority' => 'high',
		'autosave' => true,
		'fields'   => array(
			array(
				'name'             => esc_html__( 'Image', 'sober' ),
				'id'               => 'image',
				'type'             => 'image_advanced',
				'class'            => 'image',
				'max_file_uploads' => 1,
			),
			array(
				'name'  => esc_html__( 'Gallery', 'sober' ),
				'id'    => 'images',
				'type'  => 'image_advanced',
				'class' => 'gallery',
			),
			array(
				'name'  => esc_html__( 'Audio', 'sober' ),
				'id'    => 'audio',
				'type'  => 'textarea',
				'cols'  => 20,
				'rows'  => 2,
				'class' => 'audio',
			),
			array(
				'name'  => esc_html__( 'Video', 'sober' ),
				'id'    => 'video',
				'type'  => 'textarea',
				'cols'  => 20,
				'rows'  => 2,
				'class' => 'video',
			),
		),
	);

	// Display Settings
	$meta_boxes[] = array(
		'id'       => 'display-settings',
		'title'    => esc_html__( 'Display Settings', 'sober' ),
		'pages'    => array( 'page' ),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			array(
				'name' => esc_html__( 'Site Header', 'sober' ),
				'id'   => 'heading_site_header',
				'type' => 'heading',
			),
			array(
				'name'    => esc_html__( 'Header Background', 'sober' ),
				'id'      => 'site_header_bg',
				'type'    => 'select',
				'options' => array(
					''            => esc_html__( 'Default', 'sober' ),
					'white'       => esc_html__( 'White', 'sober' ),
					'transparent' => esc_html__( 'Transparent', 'sober' ),
				),
			),
			array(
				'name'    => esc_html__( 'Header Text Color', 'sober' ),
				'desc'    => esc_html__( 'This option only works if the header background is transparent', 'sober' ),
				'id'      => 'site_header_text_color',
				'class'   => 'site_header_text_color',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
					'dark'  => esc_html__( 'Dark', 'sober' ),
				),
			),
			array(
				'name' => esc_html__( 'Page Header', 'sober' ),
				'id'   => 'heading_page_header',
				'type' => 'heading',
			),
			array(
				'name' => esc_html__( 'Hide Page Header', 'sober' ),
				'id'   => 'hide_page_header',
				'type' => 'checkbox',
				'std'  => false,
			),
			array(
				'name'             => esc_html__( 'Page Header Image', 'sober' ),
				'id'               => 'page_header_bg',
				'class'            => 'page-header-field',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
			array(
				'name'    => esc_html__( 'Page Header Text Color', 'sober' ),
				'id'      => 'page_header_text_color',
				'class'   => 'page-header-field',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
					'dark'  => esc_html__( 'Dark', 'sober' ),
				),
			),
			array(
				'name'  => esc_html__( 'Hide Breadcrumb', 'sober' ),
				'id'    => 'hide_breadcrumb',
				'class' => 'page-header-field',
				'type'  => 'checkbox',
				'std'   => false,
			),
			array(
				'name'  => esc_html__( 'Hide Page Title', 'sober' ),
				'id'    => 'hide_page_title',
				'class' => 'hide-page-title',
				'type'  => 'checkbox',
				'std'   => false,
			),
			array(
				'name' => esc_html__( 'Layout', 'sober' ),
				'id'   => 'heading_layout',
				'type' => 'heading',
			),
			array(
				'name' => esc_html__( 'Custom Layout', 'sober' ),
				'id'   => 'custom_layout',
				'type' => 'checkbox',
				'std'  => false,
			),
			array(
				'name'    => esc_html__( 'Layout', 'sober' ),
				'id'      => 'layout',
				'type'    => 'image_select',
				'class'   => 'custom-layout',
				'options' => array(
					'no-sidebar'   => get_template_directory_uri() . '/images/options/sidebars/empty.png',
					'single-left'  => get_template_directory_uri() . '/images/options/sidebars/single-left.png',
					'single-right' => get_template_directory_uri() . '/images/options/sidebars/single-right.png',
				),
			),
			array(
				'name'    => esc_html__( 'Content Top Spacing', 'sober' ),
				'id'      => 'top_spacing',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'Default', 'sober' ),
					'none'   => esc_html__( 'No spacing', 'sober' ),
					'custom' => esc_html__( 'Custom', 'sober' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'top_padding',
				'class' => 'custom-spacing hidden',
				'type'  => 'text',
				'std'   => '50px',
			),
			array(
				'name'    => esc_html__( 'Content Bottom Spacing', 'sober' ),
				'id'      => 'bottom_spacing',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'Default', 'sober' ),
					'none'   => esc_html__( 'No spacing', 'sober' ),
					'custom' => esc_html__( 'Custom', 'sober' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'bottom_padding',
				'class' => 'custom-spacing hidden',
				'type'  => 'text',
				'std'   => '100px',
			),
		),
	);

	return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'sober_register_meta_boxes' );

/**
 * Enqueue scripts for admin
 *
 * @since  1.0
 */
function sober_meta_boxes_scripts( $hook ) {

	if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		wp_enqueue_script( 'sober-meta-boxes', get_template_directory_uri() . '/js/admin/meta-boxes.js', array( 'jquery' ), '20160523', true );
	}
}

add_action( 'admin_enqueue_scripts', 'sober_meta_boxes_scripts' );