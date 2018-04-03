<?php
/**
 * Plugin Name: Soo Wishlist
 * Plugin URI: http://sooplugins.com/plugin/soo-woocommerce-wishlist
 * Description: An WooCommerce's extension for creating wishlists
 * Version: 1.0.7
 * Author: SooPlugins
 * Author URI: http://sooplugins.com/
 * Text Domain: soow
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Soo_Wishlist_Plugin' ) ) :

	/**
	 * The main plugin class
	 */
	final class Soo_Wishlist_Plugin {
		/**
		 * The single instance of the class
		 *
		 * @var Soo_Wishlist_Plugin
		 */
		protected static $instance = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public $version = '1.0.7';

		/**
		 * Database version
		 *
		 * @var string
		 */
		public $db_version = '1.0.7';

		/**
		 * Main instance
		 *
		 * @return Soo_Wishlist_Plugin
		 */
		public static function instance() {
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Class constructor.
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();

			do_action( 'soo_wishlist_loaded' );
		}

		/**
		 * Defines plugin constants
		 */
		private function define_constants() {
			$this->define( 'SOOW_VERSION', $this->version );
			$this->define( 'SOOW_DB_VERSION', $this->db_version );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			require_once 'includes/class-wishlist-install.php';
			require_once 'includes/class-wishlist-list.php';
			require_once 'includes/class-wishlist.php';
			require_once 'includes/class-wishlist-shortcodes.php';
			require_once 'includes/class-wishlist-actions-handle.php';
			require_once 'includes/class-wishlist-frontend.php';
			require_once 'includes/function-wishlist.php';

			if ( is_admin() ) {
				require_once 'includes/class-wishlist-admin.php';
			}
		}

		/**
		 * Initialize hooks
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( 'Soo_Wishlist_Install', 'install' ) );
			add_action( 'init', array( $this, 'load_textdomain' ) );
			add_action( 'admin_init', array( 'Soo_Wishlist_Install', 'add_tables' ) );

			add_action( 'admin_notices', array( $this, 'notice' ) );

			add_action( 'init', array( $this, 'add_rewrite_rules' ), 0 );
			add_filter( 'query_vars', array( $this, 'add_public_query_var' ) );

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'init', array( 'Soo_Wishlist_Shortcodes', 'init' ) );
			add_action( 'init', array( 'Soo_Wishlist_Actions_Handle', 'init' ) );
			add_action( 'init', array( 'Soo_Wishlist_Frontend', 'init' ) );
		}

		/**
		 * Load plugin text domain
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'soow', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Add rewrite rules for wishlist view
		 */
		public function add_rewrite_rules() {
			$wishlist_page_id = soow_translated_object_id( get_option( 'soo_wishlist_page_id' ) );

			if ( empty( $wishlist_page_id ) ) {
				return;
			}

			$wishlist_page      = get_post( $wishlist_page_id );
			$wishlist_page_slug = $wishlist_page ? $wishlist_page->post_name : false;

			if ( empty( $wishlist_page_slug ) ) {
				return;
			}

			add_rewrite_rule( '(([^/]+/)*' . $wishlist_page_slug . ')(/(.*))?/?$', 'index.php?pagename=$matches[1]&wishlist_hash=$matches[4]', 'top' );
		}

		/**
		 * Add wishlist_hash query var
		 *
		 * @param array $public_var
		 *
		 * @return array
		 */
		public function add_public_query_var( $public_var ) {
			$public_var[] = 'wishlist_hash';

			return $public_var;
		}

		/**
		 * Init plugins when WordPress initialises
		 */
		public function init() {
			global $wpdb;

			$wpdb->soo_wishlists       = $wpdb->prefix . 'soo_wishlists';
			$wpdb->soo_wishlists_items = $wpdb->prefix . 'soo_wishlists_items';
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string      $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Display notice when WooCommerce is not activated
		 */
		public function notice() {
			if ( ! function_exists( 'WC' ) ) {
				?>
				<div class="error">
					<p><?php esc_html_e( 'Soo Wishlist is enabled but not effective. It requires WooCommerce in order to work.', 'soow' ); ?></p>
				</div>
				<?php
			}
		}
	}

endif;

/**
 * Init plugin
 */
function soo_wishlist_init() {
	Soo_Wishlist_Plugin::instance();
}

add_action( 'plugins_loaded', 'soo_wishlist_init' );