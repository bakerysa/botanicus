<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWSBS_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription Class
 *
 * @class   YWSBS_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YWSBS_Subscription' ) ) {

    class YWSBS_Subscription {



        protected $subscription_meta_data = array(
            'status'                 => 'pending',
            'start_date'             => '',
            'payment_due_date'       => '',
            'expired_date'           => '',
            'cancelled_date'           => '',
            'payed_order_list'       => array(),
            'product_id'             => '',
            'variation_id'           => '',
            'product_name'           => '',
            'quantity'               => '',
            'line_subtotal'          => '',
            'line_total'             => '',
            'line_subtotal_tax'      => '',
            'line_tax'               => '',
            'line_tax_data'          => '',

            'cart_discount'          => '',
            'cart_discount_tax'      => '',

            'order_total'            => '',
            'order_currency'         => '',
            'renew_order'         => 0,

            'prices_include_tax'     => '',

            'payment_method'         => '',
            'payment_method_title'   => '',

            'subscriptions_shippings'          => '',

            'price_is_per'           => '',
            'price_time_option'      => '',
            'max_length'             => '',

            'order_ids'              => array(),
            'order_id'               => '',
            'user_id'                => 0,
            'customer_ip_address'    => '',
            'customer_user_agent'    => '',

            'billing_first_name'     => '',
            'billing_last_name'      => '',
            'billing_company'        => '',
            'billing_address_1'      => '',
            'billing_address_2'      => '',
            'billing_city'           => '',
            'billing_state'          => '',
            'billing_postcode'       => '',
            'billing_country'        => '',
            'billing_email'          => '',
            'billing_phone'          => '',

            'shipping_first_name'    => '',
            'shipping_last_name'     => '',
            'shipping_company'       => '',
            'shipping_address_1'     => '',
            'shipping_address_2'     => '',
            'shipping_city'          => '',
            'shipping_state'         => '',
            'shipping_postcode'      => '',
            'shipping_country'       => '',
        );

	    /**
	     * The subscription (post) ID.
	     *
	     * @var int
	     */
	    public $id = 0;


	    /**
	     * @var string
	     */
	    public $price_time_option;

	    /**
	     * @var int
	     */
	    public $variation_id;

	    /**
	     * $post Stores post data
	     *
	     * @var $post WP_Post
	     */
	    public $post = null;

	    /**
	     * $post Stores post data
	     *
	     * @var string
	     */
	    public $status;


        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function __construct( $subscription_id = 0, $args = array() ) {
            add_action( 'init', array( $this, 'register_post_type' ) );

	        //populate the subscription if $subscription_id is defined
	        if ( $subscription_id ) {
		        $this->id = $subscription_id;
		        $this->populate();
	        }

	        //add a new subscription if $args is passed
	        if ( $subscription_id == '' && ! empty( $args ) ) {
		        $this->add_subscription( $args );
	        }

        }

	    /**
	     * __get function.
	     *
	     * @param string $key
	     *
	     * @return mixed
	     */
	    public function __get( $key ) {
		    $value = get_post_meta( $this->id, $key, true );

		    if ( ! empty( $value ) ) {
			    $this->$key = $value;
		    }

		    return $value;
	    }


	    /**
	     * set function.
	     *
	     * @param string $property
	     * @param mixed  $value
	     *
	     * @return bool|int
	     */
	    public function set( $property, $value ) {

		    $this->$property = $value;

		    return update_post_meta( $this->id, $property, $value );
	    }


	    public function __isset( $key ) {
		    if ( ! $this->id ) {
			    return false;
		    }

		    return metadata_exists( 'post', $this->id, $key );
	    }

	    /**
	     * Populate the subscription
	     *
	     * @return void
	     * @since  1.0.0
	     */
	    public function populate() {

		    $this->post = get_post( $this->id );

		    foreach ( $this->get_subscription_meta() as $key => $value ) {
			    $this->$key = $value;
		    }

		    do_action( 'ywsbs_subscription_loaded', $this );
	    }

	    /**
	     * @param $args
	     *
	     * @return int|WP_Error
	     */
	    public function add_subscription( $args ) {

            $subscription_id = wp_insert_post( array(
                'post_status' => 'publish',
	            'post_type'   => 'ywsbs_subscription',
            ) );

            if( $subscription_id ){
	            $this->id = $subscription_id;
	            $meta     = apply_filters( 'ywsbs_add_subcription_args', wp_parse_args( $args, $this->get_default_meta_data() ), $this );
	            $this->update_subscription_meta( $meta );
	            $this->populate();
            }

            return $subscription_id;
        }

        /**
         * Update post meta in subscription
         *
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         * @return void
         */
        function update_subscription_meta( $meta ){
            foreach( $meta as $key => $value ){
                update_post_meta( $this->id, '_'.$key, $value);
            }
        }

	    /**
	     * @param $order_id
	     *
	     * @internal param $subscription_id
	     */
	    public function start_subscription( $order_id) {

//	        $payed = $this->payed_order_list;
//
//	        //do not nothing if this subscription has payed with this order
//	        if ( ! empty( $payed ) && is_array( $payed ) && in_array( $order_id, $payed ) ) {
//		        return;
//	        }
//
//		    $payed = empty( $payed ) ? array() : $payed;
//
//		    if ( $this->start_date == '' ) {
//			    $this->set( 'start_date', current_time('timestamp') );
//		    }
//
//            if ( $this->payment_due_date == '' ) {
//	            $timestamp              = ywsbs_get_timestamp_from_option( current_time('timestamp'), $this->price_is_per, $this->price_time_option );
//                $this->set( 'payment_due_date', date( "Y-m-d H:i:s", $timestamp ) );
//            }
//
//            $expired_date = $this->expired_date;
//            if ( $expired_date == ''  && $this->max_length != '' ) {
//                $timestamp              = ywsbs_get_timestamp_from_option( current_time('timestamp'), $this->max_length, $this->price_time_option ) ;
//	            $this->set( 'expired_date', date( "Y-m-d H:i:s", $timestamp ) );
//            }
//
//            $this->set( 'status', 'active' );
//
//            $payed[] = $order_id;
//
//            $this->set( 'payed_order_list', $payed );

		    $payed = $this->payed_order_list;

		    //do not nothing if this subscription has payed with this order
		    if ( ! empty( $payed ) && is_array( $payed ) && in_array( $order_id, $payed ) ) {
			    return;
		    }

		    $payed = empty( $payed ) ? array() : $payed;

		    $order       = wc_get_order( $order_id );
		    $new_status  = 'active';
		    $rates_payed = 1;
		    if ( $this->start_date == '' ) {
			    $this->set( 'start_date', current_time('timestamp') );
		    }

		    if ( $this->payment_due_date == '' ) {
			    //Change the next payment_due_date
			    $this->set( 'payment_due_date', $this->get_next_payment_due_date( 0, $this->start_date ) );
		    }

		    if ( $this->expired_date == '' && $this->max_length != '' ) {
			    $timestamp = ywsbs_get_timestamp_from_option( current_time('timestamp'), $this->max_length, $this->price_time_option );
			    $this->set( 'expired_date', $timestamp );
		    }

		    $this->set( 'status', $new_status );

		    do_action( 'ywsbs_customer_subscription_payment_done_mail', $this );

		    $payed[] = $order_id;

		    $this->set( 'rates_payed', $rates_payed );
		    $this->set( 'payed_order_list', $payed );

        }


        /**
         * Update the subscription if a payment is done manually from user
         *
         * order_id is the id of the last order created
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         * @return void
         */
        public function update_subscription( $order_id ) {


            $payed = $this->payed_order_list;
            //do not nothing if this subscription has payed with this order
            if ( !empty( $payed ) && is_array( $payed ) && in_array( $order_id, $payed ) ) {
                return;
            }

            //Change the status to active
	        $this->set( 'status', 'active' );

            //Change the next payment_due_date
            $price_is_per      = $this->price_is_per;
            $price_time_option = $this->price_time_option;
            $timestamp         = ywsbs_get_timestamp_from_option( current_time('timestamp'), $price_is_per, $price_time_option );

	        $this->set( 'payment_due_date', date( "Y-m-d H:i:s", $timestamp ) );
            //update _payed_order_list
            $payed[] = $order_id;
	        $this->set( 'payed_order_list', $payed );
	        $this->set( 'renew_order', 0);

        }


	    /**
	     * @return array
	     * @internal param $subscription_id
	     *
	     */
	    function get_subscription_meta(  ) {
            $subscription_meta = array();
            foreach ( $this->get_default_meta_data() as $key => $value ) {
            	$meta_value = get_post_meta( $this->id, $key, true );
                $subscription_meta[$key] = empty($meta_value) ? get_post_meta( $this->id, '_'.$key, true ) : $meta_value;
            }

            return $subscription_meta;
        }

	    /**
	     * Return an array of all custom fields subscription
	     *
	     * @return array
	     * @since  1.0.0
	     */
	    private function get_default_meta_data(){
		    return $this->subscription_meta_data;
	    }


	    /**
	     * @internal param $subscription_id
	     */
	    function cancel_subscription(){


            //Change the status to active

            $this->set( 'status', 'cancelled' );
            $this->set( 'cancelled_date', date( "Y-m-d H:i:s" ) );

            do_action('ywsbs_subscription_cancelled', $this->id);

            //if there's a pending order for this subscription change the status of the order to cancelled
            $order_in_pending = $this->renew_order;
            if( $order_in_pending ){
                $order = wc_get_order( $order_in_pending );
                if( $order ){
                    $order->update_status('failed');
                }
            }

        }

	    /**
	     * Return the next payment due date if there are rates not payed
	     *
	     * @param int $trial_period
	     *
	     * @since  1.0.0
	     * @author Emanuela Castorina
	     * @return array
	     */
	    public function get_next_payment_due_date( $trial_period = 0, $start_date = 0) {

		    $start_date = ( $start_date ) ? $start_date : current_time('timestamp');
		    if ( $this->num_of_rates == '' || ( $this->num_of_rates - $this->rates_payed ) > 0 ) {
			    $payment_due_date = ( $this->payment_due_date == '' ) ?  $start_date : $this->payment_due_date;
			    if( $trial_period != 0){
				    $timestamp = $start_date + $trial_period;
			    }else{
				    $timestamp = ywsbs_get_timestamp_from_option( $payment_due_date, $this->price_is_per, $this->price_time_option );
			    }

			    return $timestamp;
		    }

		    return false;

	    }

    }




}

