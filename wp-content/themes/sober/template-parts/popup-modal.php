<?php
/**
 * The template part for displaying the popup in modal layout
 *
 * @package Sober
 */
?>

<div id="popup" class="sober-modal sober-popup popup-layout-modal">
	<div class="sober-modal-backdrop popup-backdrop"></div>
	<div class="popup-modal">
		<a href="#" class="close-modal">
			<svg viewBox="0 0 20 20">
				<use xlink:href="#close-delete"></use>
			</svg>
		</a>

		<div class="popup-container">
			<div class="popup-content popup-image">
				<?php
				if ( $popup_banner = sober_get_option( 'popup_image' ) ) {
					printf( '<img src="%s">', esc_url( $popup_banner ) );
				}
				?>
			</div>

			<div class="popup-content">
				<div class="popup-content-wrap">
					<?php echo do_shortcode( wp_kses_post( sober_get_option( 'popup_content' ) ) ); ?>
				</div>
			</div>
		</div>
	</div>
</div>