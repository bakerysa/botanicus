<?php
/*
Plugin Name: YITH WooCommerce Subscription
Description: YITH WooCommerce Subscription
Version: 1.2.1
Author: YITHEMES
Author URI: http://yithemes.com/
Text Domain: yith-woocommerce-subscription
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 3.0.0
WC tested up to: 3.3.0
*/

/*
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITHEMES
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}


if ( ! defined( 'YITH_YWSBS_DIR' ) ) {
    define( 'YITH_YWSBS_DIR', plugin_dir_path( __FILE__ ) );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWSBS_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_YWSBS_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_YWSBS_DIR  );


// This version can't be activate if premium version is active  ________________________________________
if ( defined( 'YITH_YWSBS_PREMIUM' ) ) {
    function yith_ywsbs_install_free_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Subscription while you are using the premium one.', 'yith-woocommerce-subscription' ); ?></p>
        </div>
    <?php
    }

    add_action( 'admin_notices', 'yith_ywsbs_install_free_admin_notice' );

    deactivate_plugins( plugin_basename( __FILE__ ) );
    return;

}

// Registration hook  ________________________________________
if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( !function_exists( 'yith_ywsbs_install_woocommerce_admin_notice' ) ) {
    function yith_ywsbs_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Subscription is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-subscription' ); ?></p>
        </div>
    <?php
    }
}

// Define constants ________________________________________
if ( defined( 'YITH_YWSBS_VERSION' ) ) {
    return;
}else{
    define( 'YITH_YWSBS_VERSION', '1.2.1' );
}

if ( ! defined( 'YITH_YWSBS_FREE_INIT' ) ) {
    define( 'YITH_YWSBS_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWSBS_INIT' ) ) {
    define( 'YITH_YWSBS_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWSBS_FILE' ) ) {
    define( 'YITH_YWSBS_FILE', __FILE__ );
}


if ( ! defined( 'YITH_YWSBS_URL' ) ) {
    define( 'YITH_YWSBS_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWSBS_ASSETS_URL' ) ) {
    define( 'YITH_YWSBS_ASSETS_URL', YITH_YWSBS_URL . 'assets' );
}

if ( ! defined( 'YITH_YWSBS_TEMPLATE_PATH' ) ) {
    define( 'YITH_YWSBS_TEMPLATE_PATH', YITH_YWSBS_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWSBS_INC' ) ) {
    define( 'YITH_YWSBS_INC', YITH_YWSBS_DIR . '/includes/' );
}

if ( ! defined( 'YITH_YWSBS_SUFFIX' ) ) {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    define( 'YITH_YWSBS_SUFFIX', $suffix );
}

if ( ! defined( 'YITH_YWSBS_TEST_ON' ) ) {
    define( 'YITH_YWSBS_TEST_ON', false );
}

if ( ! function_exists( 'yith_ywsbs_install' ) ) {
    function yith_ywsbs_install() {

        if ( !function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'yith_ywsbs_install_woocommerce_admin_notice' );
        } else {
            do_action( 'yith_ywsbs_init' );
        }
    }

    add_action( 'plugins_loaded', 'yith_ywsbs_install', 11 );
}


function yith_ywsbs_constructor() {

    // Woocommerce installation check _________________________
    if ( !function_exists( 'WC' ) ) {
        function yith_ywsbs_install_woocommerce_admin_notice() {
            ?>
            <div class="error">
                <p><?php _e( 'YITH WooCommerce Subscription is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-subscription' ); ?></p>
            </div>
        <?php
        }

        add_action( 'admin_notices', 'yith_ywsbs_install_woocommerce_admin_notice' );
        return;
    }

    // Load YWSBS text domain ___________________________________
    load_plugin_textdomain( 'yith-woocommerce-subscription', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    if( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }

    require_once( YITH_YWSBS_INC . 'functions.yith-wc-subscription.php' );
	require_once( YITH_YWSBS_INC . 'class.yith-wc-subscription.php' );
	require_once( YITH_YWSBS_INC . 'class.ywsbs-susbscription-helper.php' );
	require_once( YITH_YWSBS_INC . 'class.ywsbs-susbscription.php' );
	require_once( YITH_YWSBS_INC . 'class.yith-wc-subscription-order.php' );
	require_once( YITH_YWSBS_INC . 'class.yith-wc-subscription-cart.php' );
	require_once( YITH_YWSBS_INC . 'class.yith-wc-subscription-admin.php' );
	require_once( YITH_YWSBS_INC . 'class.yith-wc-subscription-cron.php' );
	require_once( YITH_YWSBS_INC . 'gateways/paypal/class.yith-wc-subscription-paypal.php' );
	require_once( YITH_YWSBS_INC . 'admin/class.ywsbs-subscriptions-list-table.php' );

	if ( is_admin() ) {
        YITH_WC_Subscription_Admin();
	}

    YITH_WC_Subscription();


    add_action( 'ywsbs_renew_orders', array( YWSBS_Subscription_Cron(), 'renew_orders') );
    add_action( 'ywsbs_cancel_orders', array( YWSBS_Subscription_Cron(), 'cancel_orders') );

}
add_action( 'yith_ywsbs_init', 'yith_ywsbs_constructor' );