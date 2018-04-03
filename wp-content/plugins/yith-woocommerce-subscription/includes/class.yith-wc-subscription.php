<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWSBS_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH WooCommerce Subscription
 *
 * @class   YITH_WC_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YITH_WC_Subscription' ) ) {

    class YITH_WC_Subscription {

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Subscription
         */
        protected static $instance;

	    /**
	     * Post name of subscription
	     *
	     * @var string
	     */
	    public $post_name = 'ywsbs_subscription';

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Subscription
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
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            if( get_option('ywsbs_enabled') != 'yes' ){
                return;
            }

            /* general actions */
            add_filter( 'woocommerce_locate_core_template', array( $this, 'filter_woocommerce_template' ), 10, 3 );
            add_filter( 'woocommerce_locate_template', array( $this, 'filter_woocommerce_template' ), 10, 3 );

            /*Register custom post type YWSBS_Subscription */

	        YWSBS_Subscription_Helper();
            YWSBS_Subscription_Cron();
            YWSBS_Subscription_Order();
            YWSBS_Subscription_Cart();
            YWSBS_Subscription_Paypal();


            // Change product prices
            add_filter('woocommerce_get_price_html', array($this, 'change_price_html'), 10, 2);

            add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'order_formatted_line_subtotal'), 10, 3 );

            // Ensure a subscription is never in the cart with products
            add_filter( 'woocommerce_add_to_cart_validation', array($this, 'cart_item_validate'), 10, 3 );


        }

        /**
         * Load YIT Plugin Framework
         *
         * @since  1.0.0
         * @return void
         * @author Emanuela Castorina
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if( ! empty( $plugin_fw_data ) ){
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

        /**
         * Locate default templates of woocommerce in plugin, if exists
         *
         * @param $core_file     string
         * @param $template      string
         * @param $template_base string
         *
         * @return string
         * @since  1.0.0
         */
        public function filter_woocommerce_template( $core_file, $template, $template_base ) {

            $located = yith_ywsbs_locate_template( $template );

            if ( $located ) {
                return $located;
            }
            else {
                return $core_file;
            }
        }

	    /**
	     * @param $price
	     * @param $product
	     *
	     * @return string
	     */
	    public function change_price_html( $price, $product ) {

	        if ( ! $this->is_subscription( $product->get_id() ) ) {
		        return $price;
	        }

	        $price_is_per      = yit_get_prop( $product, '_ywsbs_price_is_per' );
	        $price_time_option = yit_get_prop( $product, '_ywsbs_price_time_option' );
	        $price_time_option_string = ywsbs_get_price_per_string( $price_is_per, $price_time_option );

	        $price .= ' / ' . $price_time_option_string;

            return $price;

        }

	    /**
	     * @param $product
	     *
	     * @return bool
	     * @internal param $product_id
	     *
	     */
	    public function is_subscription( $product ) {
		    if ( is_numeric( $product ) ) {
			    $product = wc_get_product( $product );
		    }

		    $is_subscription = yit_get_prop( $product, '_ywsbs_subscription' );
		    $price_is_per    = yit_get_prop( $product, '_ywsbs_price_is_per' );

		    return apply_filters( 'ywsbs_is_subscription', ( $is_subscription == 'yes' && $price_is_per != '' ) ? true : false, $product->get_id() );
        }

        /**
         * Check if in the cart there are subscription that needs shipping
         *
         * @return bool
         * @since  1.0.0
         */
	    public function cart_has_subscription_with_shipping() {

		    $cart_has_subscription_with_shipping = false;

		    $cart_contents = WC()->cart->get_cart();

		    if ( ! isset( $cart_contents ) || empty( $cart_contents ) ) {
			    return $cart_has_subscription_with_shipping;
		    }

		    foreach ( $cart_contents as $cart_item ) {
			    $product = $cart_item['data'];
			    if ( $this->is_subscription( $product->id ) && $product->needs_shipping() ) {
				    $cart_has_subscription_with_shipping = true;
			    }
		    }

		    return apply_filters( 'ywsbs_cart_has_subscription_with_shipping', $cart_has_subscription_with_shipping );

	    }

	    /**
	     * @param $valid
	     * @param $product_id
	     * @param $quantity
	     *
	     * @return mixed
	     */
	    public function cart_item_validate( $valid, $product_id, $quantity ) {
		    if ( $this->is_subscription( $product_id ) && $item_key = $this->cart_has_subscriptions() ) {
			    $this->clean_cart_from_subscriptions( $item_key );
			    $message = __( 'A subscription has been removed from your cart. You cannot purchases different subscriptions at the same time.', 'yith-woocommerce-subscription' );
			    wc_add_notice( $message, 'notice' );
		    }

		    return $valid;
	    }

        /**
         * Removes all subscription products from the shopping cart.
         *
         * @since 1.0
         */
        public function clean_cart_from_subscriptions( $item_key ) {
            WC()->cart->set_quantity( $item_key, 0 );
        }

        /**
         * Check if in the cart there are subscription
         *
         * @return bool/int
         * @since  1.0.0
         */
	    public function cart_has_subscriptions() {
		    $contents = WC()->cart->cart_contents;
		    if ( ! empty( $contents ) ) {
			    foreach ( $contents as $item_key => $item ) {
				    if ( $this->is_subscription( $item['product_id'] ) ) {
					    return $item_key;
				    }
			    }
		    }

		    return false;
	    }

	    /**
	     * @param $subtotal
	     * @param $item
	     * @param $order
	     *
	     * @return string
	     */
	    public function order_formatted_line_subtotal( $subtotal, $item, $order ) {

		    $product_id = $item['product_id'];
		    $product    = wc_get_product( $product_id );

		    if ( ! $this->is_subscription( $product ) ) {
			    return $subtotal;
		    }

		    $price_is_per      = yit_get_prop( $product, '_ywsbs_price_is_per' );
		    $price_time_option = yit_get_prop( $product, '_ywsbs_price_time_option' );

		    $subtotal .= ' / ' . $price_is_per . ' ' . $price_time_option;

		    return apply_filters( 'ywsbs_order_formatted_line_subtotal', $subtotal, $item, $this, $product );

	    }

        /**
         * Check if in the order there are subscription
         *
         * @param  $order_id int
         *
         * @return bool
         * @since  1.0.0
         */
	    public function order_has_subscription( $order_id ) {

		    $order       = wc_get_order( $order_id );
		    $order_items = $order->get_items();

		    if ( empty( $order_items ) ) {
			    return false;
		    }

		    foreach ( $order_items as $key => $order_item ) {
			    $id = ( $order_item['variation_id'] ) ? $order_item['variation_id'] : $order_item['product_id'];

			    if ( YITH_WC_Subscription()->is_subscription( $id ) ) {
				    return true;
			    }
		    }

		    return false;
	    }
    }


}

/**
 * Unique access to instance of YITH_WC_Subscription class
 *
 * @return \YITH_WC_Subscription
 */
function YITH_WC_Subscription() {
    return YITH_WC_Subscription::get_instance();
}
