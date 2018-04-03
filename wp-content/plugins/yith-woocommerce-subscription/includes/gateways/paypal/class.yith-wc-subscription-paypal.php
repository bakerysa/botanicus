<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWSBS_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Paypal Class
 *
 * @class   YWSBS_Subscription_Paypal
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YWSBS_Subscription_Paypal' ) ) {

    class YWSBS_Subscription_Paypal {

        /**
         * Single instance of the class
         *
         * @var \YWSBS_Subscription_Cron
         */
        protected static $instance;

        protected $wclog = '';

        protected $debug;

        /**
         * Returns single instance of the class
         *
         * @return \YWSBS_Subscription_Paypal
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

            $settings = get_option( 'woocommerce_paypal_settings' );

	        if( $settings['enabled'] != 'yes'){
		        return;
	        }

            $this->debug = ( isset( $settings['debug'] ) &&  $settings['debug'] == 'yes' ) ? true : false;

            if ( $this->debug ) {
                $this->wclog = new WC_Logger();
            }

            // When necessary, set the PayPal args to be for a subscription instead of shopping cart
            add_filter( 'woocommerce_paypal_args', array( $this, 'subscrption_args') );

            // Check if there's a subcription in a valid PayPal IPN request
            add_action( 'valid-paypal-standard-ipn-request', array( $this, 'paypal_ipn_request'), 0);
        }

        public function subscrption_args( $args ) {

            $order_info = $this->get_order_info($args);

            if( empty($order_info) || !isset( $order_info['order_id'])){
               return $args;
            }

            $order = wc_get_order( $order_info['order_id'] );

            //check if order has subscriptions
            $order_items = $order->get_items();

	        if ( empty( $order_items ) ) {
		        return $args;
	        }

            $item_names = array();
            $has_subscription = false;

            foreach ( $order_items as $key => $order_item  ) {
                $product_id = $order_item['product_id'];
	            $product = wc_get_product( $product_id );
                if( YITH_WC_Subscription()->is_subscription( $product ) ) {
                    // It's a subscription
                    $has_subscription = true;
                    $args['cmd'] = '_xclick-subscriptions';
	                $args['sra'] = apply_filters('ywsbs_reattempt_failed_recurring_payments', 1 );

	                $subscription_info = wc_get_order_item_meta($key,'_subscription_info', true);

	                $price_is_per      = yit_get_prop( $product, '_ywsbs_price_is_per' );
	                $price_time_option = yit_get_prop( $product, '_ywsbs_price_time_option' );
	                $price_time_option = ywsbs_get_price_time_option_paypal( $price_time_option );
	                $max_length        = yit_get_prop( $product, '_ywsbs_max_length' );

	                $subscription_num = ( $max_length ) ? $max_length / $price_is_per : '';

	                $args['a3'] = wc_format_decimal( $subscription_info['order_total'], 2 );
	                $args['p3'] = $price_is_per;
	                $args['t3'] = $price_time_option;

	                if ( $subscription_num == '' || $subscription_num > 1 ) {
		                $args['src'] = 1;
		                if ( $subscription_num != '' ) {
			                $args['srt'] = $subscription_num;
		                }
	                } else {
		                $args['src'] = 0;
	                }

                }

	            if ( $order_item['qty'] > 1 ) {
		            $item_names[] = $order_item['qty'] . ' x ' . $this->format_item_name( $order_item['name'] );
	            } else {
		            $item_names[] = $this->format_item_name( $order_item['name'] );
	            }

            }

            if( ! $has_subscription ){
                return $args;
            }

            if( $order->get_total() != $args['a3']){

                $args['a1'] = number_format( (float) $order->get_total(), wc_get_price_decimals(), '.', '' );
                $args['p1'] = 1;
                $args['t1'] = $args['t3'];
            }

            //recurring time
	        $subscription_num  = ( $max_length ) ? $max_length / $price_is_per : '';
	        if( $subscription_num == '' || $subscription_num > 1 ){
		        $args['src'] = 1;
		        if( $subscription_num != ''){
			        $args['srt'] = $subscription_num;
		        }
	        }else{
		        $args['src'] = 0;
	        }

	        if( ! $has_subscription ){
		        return $args;
	        }


	        if( count($item_names) > 1){
                $args['item_name'] = $this->format_item_name( sprintf( __( 'Order %s', 'yith-woocommerce-subscription' ), $order->get_order_number() . " - " . implode( ', ', $item_names ) ) );
            }else{
                $args['item_name'] = implode( ', ', $item_names );
            }

            $args['rm'] = 2;

            return $args;
        }

        function get_order_info($args){
            if( isset($args['custom'])){
                $order_info = json_decode( $args['custom'], true);
            }

            return $order_info;
        }


        public function paypal_ipn_request( $ipn_args  ) {

            if ( $this->debug ) {
                $this->wclog->add( 'paypal', 'YSBS - Subscription transaction details: ' . print_r( $ipn_args, true ) );
            }

            $txn_type = $ipn_args['txn_type'];
            $sbs_types = array( 'subscr_signup', 'subscr_payment', 'subscr_modify', 'subscr_failed', 'subscr_eot', 'subscr_cancel', 'recurring_payment_suspended_due_to_max_failed_payment');

            if( ! in_array( $txn_type, $sbs_types) ){
                return;
            }

            //there's a subscription in the IPN Request

            //check if the order has the same order_key
            $order_info = $this->get_order_info( $ipn_args );
            $order = wc_get_order( $order_info['order_id']);
            $order_id = $order_info['order_id'];

            if( $order->order_key != $order_info['order_key'] ) {
                return;
            }


            $ipn_track_ids = yit_get_prop( $order, '_paypal_ipn_track_ids' );
            $ipn_trans_ids = yit_get_prop( $order, '_paypal_transaction_ids' );

            //check if the ipn request as been processed
            if ( isset( $ipn_args['ipn_track_id'] ) ) {
                $track_id = $ipn_args['txn_type'] . '-' . $ipn_args['ipn_track_id'];
                if ( empty( $ipn_track_ids ) || !in_array( $track_id, $ipn_track_ids ) ) {
                    $ipn_track_ids[] = $track_id;
                }
                else {
                    if ( $this->debug ) {
                        $this->wclog->add( 'paypal', 'YSBS - Subscription IPN Error: IPN ' . $track_id . ' message has already been correctly handled.' );
                    }
                    return;
                }
            }


            //check if the ipn request as been processed
            if ( isset( $ipn_args['txn_id'] ) ) {
                $transaction_id = $ipn_args['txn_id'] . '-' . $ipn_args['txn_type'];

                if ( isset( $ipn_args['payment_status'] ) ) {
                    $transaction_id .= '-' . $ipn_args['payment_status'];
                }
                if ( empty( $ipn_trans_ids ) || !in_array( $transaction_id, $ipn_trans_ids ) ) {
                    $ipn_trans_ids[] = $transaction_id;
                }
                else {
                    if ( $this->debug ) {
                        $this->wclog->add( 'paypal', 'YSBS - Subscription IPN Error: IPN ' . $transaction_id . ' message has already been correctly handled.' );
                    }
                    return;
                }
            }


            switch ( $ipn_args['txn_type'] ) {
                case 'subscr_signup':
	                $args = array(
		                'Subscriber ID'         => $ipn_args['subscr_id'],
		                'Subscriber first name' => $ipn_args['first_name'],
		                'Subscriber last name'  => $ipn_args['last_name'],
		                'Subscriber address'    => $ipn_args['payer_email'],
	                );

	                yit_save_prop( $order, $args, false, true );
	                $order->add_order_note( __( 'IPN subscription started', 'yith-woocommerce-subscription' ) );
	                break;

                case 'subscr_payment':
                    if ( 'completed' == strtolower( $ipn_args['payment_status'] ) ) {

	                    $subscriptions = yit_get_prop( $order, 'subscriptions' );

                        if ( empty( $subscriptions ) ) {
                            if ( $this->debug ) {
                                $this->wclog->add( 'paypal', 'YSBS - IPN subscription payment error - ' . $order_id . ' haven\'t subscriptions' );
                            }
                        }

                        foreach ( $subscriptions as $subscription ) {

	                        $pending_order       = get_post_meta( $subscription, '_renew_order', true );
	                        $subscription_status = get_post_meta( $subscription, '_status', true );
                            if( $subscription_status == 'cancelled'){
                                if ( $this->debug ) {
                                    $this->wclog->add( 'paypal', 'YSBS - IPN subscription payment error - subscription ' . $subscription . ' is cancelled' );
                                }

                                break;
                            }

                            if( $pending_order ){
                                $last_order = wc_get_order( $pending_order );
	                            $args = array(
		                            'Subscriber ID'           => $ipn_args['subscr_id'],
		                            'Subscriber first name'   => $ipn_args['first_name'],
		                            'Subscriber last name'    => $ipn_args['last_name'],
		                            'Subscriber address'      => $ipn_args['payer_email'],
		                            'Subscriber payment type' => $ipn_args['payment_type'],
	                            );

	                            yit_save_prop( $last_order, $args, false, true );

                                $last_order->add_order_note( __( 'IPN subscription payment completed.', 'yith-woocommerce-subscription' ) );
                                $last_order->payment_complete( $ipn_args['txn_id'] );
                            }else{

	                            $args = array(
		                            'Subscriber ID'           => $ipn_args['subscr_id'],
		                            'Subscriber first name'   => $ipn_args['first_name'],
		                            'Subscriber last name'    => $ipn_args['last_name'],
		                            'Subscriber address'      => $ipn_args['payer_email'],
		                            'Subscriber payment type' => $ipn_args['payment_type'],
	                            );

	                            yit_save_prop( $order, $args, false, true );

                                $order->add_order_note( __( 'IPN subscription payment completed.', 'yith-woocommerce-subscription' ) );

                                $order->payment_complete( $ipn_args['txn_id'] );
                            }

                        }

                    }

                    break;

                case 'subscr_modify':

                    break;

                case 'subscr_failed':

                    break;

                case 'subscr_eot':
                    /*subscription expired*/
                    break;

                case 'recurring_payment_suspended_due_to_max_failed_payment':
                case 'subscr_cancel':
                    /*subscription cancelled*/
                    $paypal_sub_id = $ipn_args['subscr_id'];
                    $order_sub_id = yit_get_prop( $order, 'Subscriber ID' );

                    if( $paypal_sub_id != $order_sub_id ){
                        if ( $this->debug ) {
                            $this->wclog->add( 'paypal', 'YSBS - IPN subscription cancellation request ignored - new PayPal Profile ID linked to this subscription, for order ' . $order_id );
                        }
                    }else{
                        $subscriptions = yit_get_prop( $order, 'subscriptions' );
                        if ( empty( $subscriptions ) ) {
                            if ( $this->debug ) {
                                $this->wclog->add( 'paypal', 'YSBS - IPN subscription cancellation request ignored - order ' . $order_id . ' doesn\'t not subscriptions' );
                            }
                        }

                        foreach ( $subscriptions as $subscription_id ) {
                        	$subscription = new YWSBS_Subscription( $subscription_id );
	                        $subscription->cancel_subscription();
                            // Subscription Cancellation Completed
                            $order->add_order_note( __( 'YSBS - IPN subscription cancelled for the order.', 'yith-woocommerce-subscription' ) );

                            if ( $this->debug ) {
                                $this->wclog->add( 'paypal', 'YSBS -IPN subscription cancelled for order ' . $order_id );
                            }
                        }
                    }

                    break;
                default:
            }

        }

        protected static function format_item_name( $item_name ) {

            if ( strlen( $item_name ) > 127 ) {
                $item_name = substr( $item_name, 0, 124 ) . '...';
            }
            return html_entity_decode( $item_name, ENT_NOQUOTES, 'UTF-8' );

        }


    }
}

/**
 * Unique access to instance of YWSBS_Subscription_Paypal class
 *
 * @return \YWSBS_Subscription_Paypal
 */
function YWSBS_Subscription_Paypal() {
    return YWSBS_Subscription_Paypal::get_instance();
}

