<?php
/**
 * Add more data for user
 */

/**
 * Add more contact method for user
 *
 * @param array $methods
 *
 * @return array
 */
function sober_addons_user_contact_methods( $methods ) {
	$methods['facebook']  = esc_html__( 'Facebook', 'sober' );
	$methods['twitter']   = esc_html__( 'Twitter', 'sober' );
	$methods['google']    = esc_html__( 'Google Plus', 'sober' );
	$methods['pinterest'] = esc_html__( 'Pinterest', 'sober' );
	$methods['instagram'] = esc_html__( 'Instagram', 'sober' );

	return $methods;
}

add_filter( 'user_contactmethods', 'sober_addons_user_contact_methods' );