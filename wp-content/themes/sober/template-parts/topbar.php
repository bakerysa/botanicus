<?php
/**
 * Template part for displaying topbar
 */
?>
<div id="topbar" class="topbar">
	<div class="sober-container">

		<?php if ( '2-columns' == sober_get_option( 'topbar_layout' ) ) : ?>

			<div class="row">
				<div class="topbar-left topbar-content text-left col-md-6">
					<?php
					switch ( sober_get_option( 'topbar_left' ) ) {
						case 'switchers':
							sober_currency_switcher();
							sober_language_switcher();
							break;

						default:
							echo do_shortcode( sober_get_option( 'topbar_content' ) );
							break;
					}
					?>
				</div>

				<div class="topbar-menu text-right col-md-6">
					<?php
					if ( has_nav_menu( 'topbar' ) ) {
						wp_nav_menu( array( 'theme_location' => 'topbar', 'menu_id' => 'topbar-menu', 'menu_class' => 'topbar-menu nav-menu' ) );
					}
					?>
				</div>
			</div>

		<?php else : ?>
			<?php if ( sober_get_option( 'topbar_closeable' ) ) : ?>
				<button type="button" class="close" aria-label="Close">
					<svg viewBox="0 0 13 13">
						<use xlink:href="#close-delete-small"></use>
					</svg>		
				</button>
			<?php endif; ?>

			<div class="topbar-content text-center">
				<?php echo do_shortcode( sober_get_option( 'topbar_content' ) ); ?>
			</div>

		<?php endif; ?>

	</div>
</div>
