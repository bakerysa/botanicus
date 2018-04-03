<?php
/**
 * Plugin Name: Sober Addons
 * Plugin URI: http://uix.store/sober
 * Description: A collection of extra elements for Visual Composer. It was made for Sober premium theme and requires Sober theme installed in order to work properly.
 * Author: UIX Themes
 * Author URI: http://uix.store
 * Version: 1.3.9
 * Text Domain: sober
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Sober_Addons
 */
class Sober_Addons {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init();
	}

	/**
	 * Defines constants
	 */
	public function define_constants() {
		define( 'SOBER_ADDONS_VER', '1.3.9' );
		define( 'SOBER_ADDONS_DIR', plugin_dir_path( __FILE__ ) );
		define( 'SOBER_ADDONS_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Load files
	 */
	public function includes() {
		include_once( SOBER_ADDONS_DIR . 'includes/update.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/import.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/user.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/portfolio.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/class-sober-vc.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/shortcodes/class-sober-shortcodes.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/shortcodes/class-sober-banner.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/shortcodes/class-sober-banner-grid.php' );
	}

	/**
	 * Initialize
	 */
	public function init() {
		add_action( 'admin_notices', array( $this, 'check_dependencies' ) );

		load_plugin_textdomain( 'sober', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

		add_action( 'vc_after_init', array( 'Sober_Addons_VC', 'init' ), 50 );
		add_action( 'init', array( 'Sober_Shortcodes', 'init' ), 50 );
		add_action( 'init', array( $this, 'update' ) );

		add_action( 'init', array( 'Sober_Addons_Portfolio', 'init' ) );
	}

	/**
	 * Check plugin dependencies
	 * Check if Visual Composer plugin is installed
	 */
	public function check_dependencies() {
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			$plugin_data = get_plugin_data( __FILE__ );

			printf(
				'<div class="updated"><p>%s</p></div>',
				sprintf(
					__( '<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'sober' ),
					$plugin_data['Name']
				)
			);
		}
	}

	/**
	 * Check for update
	 */
	public function update() {
		// set auto-update params
		$plugin_current_version = SOBER_ADDONS_VER;
		$plugin_remote_path     = 'http://update.uix.store';
        $plugin_slug            = plugin_basename( __FILE__ );
        $license_user           = '';
        $license_key            = '';

		new Sober_Addons_AutoUpdate( $plugin_current_version, $plugin_remote_path, $plugin_slug );
	}
}

new Sober_Addons();
