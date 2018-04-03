<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $messages ) {
	return;
}

?>
<div class="woocommerce-error sober-message-box danger" role="alert">
	<svg viewBox="0 0 20 20" class="message-icon"><use xlink:href="#error"></use></svg>
	<div class="box-content">
		<ul>
			<?php foreach ( $messages as $message ) : ?>
				<li><?php echo wp_kses_post( $message ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<a class="close" href="#"><svg viewBox="0 0 14 14"><use xlink:href="#close-delete-small"></use></svg></a>
</div>
