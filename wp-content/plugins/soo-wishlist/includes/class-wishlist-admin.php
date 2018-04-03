<?php

/**
 * Class Soo_Wishlist_Options
 */
class Soo_Wishlist_Admin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_setting_tab' ), 30 );
		add_action( 'woocommerce_settings_tabs_soow', array( $this, 'setting_tab' ) );
		add_action( 'woocommerce_update_options_soow', array( $this, 'update_settings' ) );
	}

	/**
	 * Add setting tab
	 *
	 * @param array $settings_tabs
	 *
	 * @return array
	 */
	public function add_setting_tab( $settings_tabs ) {
		$settings_tabs['soow'] = esc_html__( 'Wishlist', 'soow' );

		return $settings_tabs;
	}

	/**
	 * Display settings
	 */
	public function setting_tab() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Save settings
	 */
	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Get plugin settings configuration
	 */
	private function get_settings() {
		$settings = array(
			array(
				'type' => 'sectionstart',
				'id'   => 'soo_wishlist_page_section_start',
			),
			array(
				'name' => esc_html__( 'Wishlist Pages', 'soow' ),
				'desc' => esc_html__( 'These pages need to be set so that WooCommerce knows where to send users to access their wishlist', 'sober' ),
				'type' => 'title',
				'id'   => 'soo_wishlist_page_section_title',
			),
			array(
				'name'     => esc_html__( 'Wishlist Page', 'soow' ),
				'desc'     => esc_html__( 'Page content: [soo_wishlist]', 'soow' ),
				'type'     => 'single_select_page',
				'id'       => 'soo_wishlist_page_id',
				'desc_tip' => true,
			),
			array(
				'name'    => esc_html__( 'Show Unit Price', 'soow' ),
				'type'    => 'checkbox',
				'id'      => 'soo_wishlist_show_price',
				'default' => 'yes',
			),
			array(
				'name'    => esc_html__( 'Show Stock Status', 'soow' ),
				'type'    => 'checkbox',
				'id'      => 'soo_wishlist_show_stock_status',
				'default' => 'yes',
			),
			array(
				'name'    => esc_html__( 'Show "Add To Cart" Button', 'soow' ),
				'type'    => 'checkbox',
				'id'      => 'soo_wishlist_show_button',
				'default' => 'yes',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'soo_wishlist_page_section_end',
			),


			array(
				'type' => 'sectionstart',
				'id'   => 'soo_wishlist_share_section_start',
			),
			array(
				'name'    => esc_html__( 'Social Share', 'soow' ),
				'desc'    => esc_html__( 'Show sharing buttons at the bottom of wishlist', 'sober' ),
				'type'    => 'title',
				'id'      => 'soo_wishlist_page_share_section_title',
				'default' => 'yes',
			),
			array(
				'name'    => esc_html__( 'Share on Facebook', 'soow' ),
				'type'    => 'checkbox',
				'id'      => 'soo_wishlist_share_facebook',
				'default' => 'yes',
			),
			array(
				'name'    => esc_html__( 'Share on Google+', 'soow' ),
				'type'    => 'checkbox',
				'id'      => 'soo_wishlist_share_google',
				'default' => 'yes',
			),
			array(
				'name'    => esc_html__( 'Share on Twitter', 'soow' ),
				'type'    => 'checkbox',
				'id'      => 'soo_wishlist_share_twitter',
				'default' => 'yes',
			),
			array(
				'name'    => esc_html__( 'Share by Email', 'soow' ),
				'type'    => 'checkbox',
				'id'      => 'soo_wishlist_share_email',
				'default' => 'yes',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'soo_wishlist_share_section_end',
			),
		);

		return apply_filters( 'soo_wishlist_settings', $settings );
	}
}

return new Soo_Wishlist_Admin();