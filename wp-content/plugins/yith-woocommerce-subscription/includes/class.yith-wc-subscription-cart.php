<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWSBS_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription Cart Class
 *
 * @class   YWSBS_Subscription_Cart
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YWSBS_Subscription_Cart' ) ) {

	/**
	 * Class YWSBS_Subscription_Cart
	 */
	class YWSBS_Subscription_Cart {

        /**
         * Single instance of the class
         *
         * @var \YWSBS_Subscription_Cart
         */
        protected static $instance;

		/**
		 * @var string
		 */
		public $post_type_name = 'ywsbs_subscription';

        /**
         * Returns single instance of the class
         *
         * @return \YWSBS_Subscription_Cart
         * @since 1.0.0
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

            add_filter('woocommerce_cart_item_price', array($this, 'change_price_in_cart_html'), 10, 3);
            add_filter('woocommerce_cart_item_subtotal', array($this, 'change_price_in_cart_html'), 10, 3);

        }


		/**
		 * @param $price
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return string
		 */
		public function change_price_in_cart_html(  $price, $cart_item, $cart_item_key ) {

			$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

            if ( !YITH_WC_Subscription()->is_subscription( $product_id ) ) {
                return $price;
            }

            $product = $cart_item['data'];

            $price_is_per = yit_get_prop( $product, '_ywsbs_price_is_per' );
            $price_time_option = yit_get_prop( $product, '_ywsbs_price_time_option' );
			$price_is_per = ywsbs_get_price_per_string( $price_is_per,$price_time_option);

            $price .=  ' / '. $price_is_per; //' / '. $price_is_per. ' '. $price_time_option;

            return $price;

        }
    }
}

/**
 * Unique access to instance of YWSBS_Subscription_Cart class
 *
 * @return \YWSBS_Subscription_Cart
 */
function YWSBS_Subscription_Cart() {
    return YWSBS_Subscription_Cart::get_instance();
}
