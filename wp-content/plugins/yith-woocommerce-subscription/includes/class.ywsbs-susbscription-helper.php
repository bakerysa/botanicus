<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Helper Class
 *
 * @class   YWSBS_Subscription_Helper
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( ! class_exists( 'YWSBS_Subscription_Helper' ) ) {

	/**
	 * Class YWSBS_Subscription_Helper
	 */
	class YWSBS_Subscription_Helper {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Subscription_Helper
		 */

		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @access public
		 *
		 * @return \YWSBS_Subscription_Helper
		 * @since  1.0.0
		 */

		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */

		public function __construct() {

			add_action( 'init', array( $this, 'register_subscription_post_type' ) );


		}


		/**
		 * Register ywsbs_subscription post type
		 *
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */

		public function register_subscription_post_type() {

			$supports = false;

			if ( apply_filters( 'ywsbs_test_on', YITH_YWSBS_TEST_ON ) ){
				$supports = array( 'custom-fields' );
			}

			$labels = array(
				'name'               => _x( 'Subscriptions', 'Post Type General Name', 'yith-woocommerce-subscription' ),
				'singular_name'      => _x( 'Subscription', 'Post Type Singular Name', 'yith-woocommerce-subscription' ),
				'menu_name'          => __( 'Subscription', 'yith-woocommerce-subscription' ),
				'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-subscription' ),
				'all_items'          => __( 'All Subscriptions', 'yith-woocommerce-subscription' ),
				'view_item'          => __( 'View Subscriptions', 'yith-woocommerce-subscription' ),
				'add_new_item'       => __( 'Add New Subscription', 'yith-woocommerce-subscription' ),
				'add_new'            => __( 'Add New Subscription', 'yith-woocommerce-subscription' ),
				'edit_item'          => __( 'Subscription', 'yith-woocommerce-subscription' ),
				'update_item'        => __( 'Update Subscription', 'yith-woocommerce-subscription' ),
				'search_items'       => __( 'Search Subscription', 'yith-woocommerce-subscription' ),
				'not_found'          => __( 'Not found', 'yith-woocommerce-subscription' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-subscription' ),
			);

			$args = array(
				'label'               => __( 'ywsbs_subscription', 'yith-woocommerce-subscription' ),
				'labels'              => $labels,
				'supports'            => $supports,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
				'capabilities'        => array(
					'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
					'edit_post'    => 'edit_subscription',
					'delete_post'  => 'delete_subscription',

				),
				'map_meta_cap'        => false
			);


			register_post_type( 'ywsbs_subscription', $args );
			flush_rewrite_rules();
		}




		/**
		 * Get all subscriptions of a user
		 *
		 * @access public
		 *
		 * @param int $user_id
		 *
		 * @return array
		 * @since  1.0.0
		 */

		public function get_subscriptions_by_user( $user_id ) {
			$subscriptions = get_posts(
				array(
					'post_type'      => YITH_WC_Subscription()->post_name,
					'posts_per_page' => - 1,
					'meta_key'       => 'user_id',
					'meta_value'     => $user_id,
				)
			);

			return $subscriptions;
		}



	}

}


/**
 * Unique access to instance of YWSBS_Subscription class
 *
 * @return \YWSBS_Subscription_Helper
 */
function YWSBS_Subscription_Helper() {
	return YWSBS_Subscription_Helper::get_instance();
}
