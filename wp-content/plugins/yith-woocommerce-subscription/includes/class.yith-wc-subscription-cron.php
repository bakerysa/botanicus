<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWSBS_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Cron Class
 *
 * @class   YWSBS_Subscription_Cron
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YWSBS_Subscription_Cron' ) ) {

    class YWSBS_Subscription_Cron {

        /**
         * Single instance of the class
         *
         * @var \YWSBS_Subscription_Cron
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \YWSBS_Subscription_Cron
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

            add_action( 'wp_loaded', array( $this, 'set_cron'), 30 );

        }


        public function set_cron(){
            if ( !wp_next_scheduled( 'ywsbs_renew_orders' ) ) {
                wp_schedule_event( time(), 'daily', 'ywsbs_renew_orders' );
            }

            if ( !wp_next_scheduled( 'ywsbs_cancel_orders' ) ) {
                wp_schedule_event( time(), 'daily', 'ywsbs_cancel_orders' );
            }
        }


        public function renew_orders() {

            global $wpdb;


            $subscriptions = $wpdb->get_results( $wpdb->prepare( "SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm3 ON ( ywsbs_p.ID = ywsbs_pm3.post_id )
                 WHERE ( ywsbs_pm.meta_key='_status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ( ywsbs_pm3.meta_key='_renew_order' AND  ywsbs_pm3.meta_value = 0 )
                 AND ywsbs_p.post_type = %s
                 AND ( ywsbs_pm2.meta_key='_payment_due_date' AND  ( ywsbs_pm2.meta_value  < DATE_ADD( NOW(), INTERVAL 1 DAY)
                 AND  ywsbs_pm2.meta_value  >= NOW() ) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ", 'ywsbs_subscription' )) ;


            if( !empty( $subscriptions ) ){
                foreach ( $subscriptions as $subscription ) {
                    $order_pending = get_post_meta( $subscription->ID, '_renew_order', true );
                    if( $order_pending == 0){
                        $order_id = YWSBS_Subscription_Order()->renew_order( $subscription->ID );
                        update_post_meta( $subscription->ID, '_renew_order', $order_id );
                    }
                }
            }
        }



        public function cancel_orders() {

            global $wpdb;

            //get all subscriptions that have status active and _expired_data<NOW to cancel the subscription

            $subscriptions = $wpdb->get_results( $wpdb->prepare( "SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='_status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ( ywsbs_pm2.meta_key='_expired_date' AND  ywsbs_pm2.meta_value  < NOW() )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ", 'ywsbs_subscription' )) ;


            if( !empty( $subscriptions ) ){
                foreach ( $subscriptions as $subscription ) {
                    YWSBS_Subscription()->cancel_subscription( $subscription->ID );
                }
            }
        }



    }
}

/**
 * Unique access to instance of YWSBS_Subscription_Cron class
 *
 * @return \YWSBS_Subscription_Cron
 */
function YWSBS_Subscription_Cron() {
    return YWSBS_Subscription_Cron::get_instance();
}

