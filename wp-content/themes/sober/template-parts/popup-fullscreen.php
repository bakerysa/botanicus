<?php
/**
 * The template part for displaying the popup in fullscreen layout
 *
 * @package Sober
 */
?>

<div id="popup" class="sober-modal sober-popup popup-layout-fullscreen">
	<div class="popup-modal">

		<div class="popup-content">
			<div class="popup-content-wrap">
				<?php echo do_shortcode( wp_kses_post( sober_get_option( 'popup_content' ) ) ); ?>
			</div>
		</div>

		<a href="#" class="close-modal line-hover line-white active"><?php esc_html_e( 'No, thanks', 'sober' ) ?></a>

	</div>
</div>
