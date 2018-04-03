<# if ( data.depth == 0 ) { #>
	<a href="#" class="media-menu-item active" data-title="<?php esc_attr_e( 'Mega Menu Content', 'sober' ) ?>" data-panel="mega"><?php esc_html_e( 'Mega Menu', 'sober' ) ?></a>
	<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Mega Menu Background', 'sober' ) ?>" data-panel="background"><?php esc_html_e( 'Background', 'sober' ) ?></a>
	<div class="separator"></div>
	<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu Icon', 'sober' ) ?>" data-panel="icon"><?php esc_html_e( 'Icon', 'sober' ) ?></a>
<# } else if ( data.depth == 1 ) { #>
	<a href="#" class="media-menu-item active" data-title="<?php esc_attr_e( 'Menu Setting', 'sober' ) ?>" data-panel="settings"><?php esc_html_e( 'Settings', 'sober' ) ?></a>
	<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu Design', 'sober' ) ?>" data-panel="design"><?php esc_html_e( 'Design', 'sober' ) ?></a>
	<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu Content', 'sober' ) ?>" data-panel="content"><?php esc_html_e( 'Content', 'sober' ) ?></a>
	<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu Icon', 'sober' ) ?>" data-panel="icon"><?php esc_html_e( 'Icon', 'sober' ) ?></a>
<# } else { #>
	<a href="#" class="media-menu-item active" data-title="<?php esc_attr_e( 'Menu Content', 'sober' ) ?>" data-panel="content"><?php esc_html_e( 'Content', 'sober' ) ?></a>
	<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu Icon', 'sober' ) ?>" data-panel="icon"><?php esc_html_e( 'Icon', 'sober' ) ?></a>
<# } #>
