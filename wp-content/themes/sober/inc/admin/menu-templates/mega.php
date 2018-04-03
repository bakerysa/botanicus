<div id="smm-panel-mega" class="smm-panel-mega smm-panel">
	<p class="mega-settings">
		<span class="setting-field">
			<label>
				<?php esc_html_e( 'Enable mega menu', 'sober' ) ?><br>
				<select name="{{ smm.getFieldName( 'mega', data.data['menu-item-db-id'] ) }}">
					<option value="0"><?php esc_html_e( 'No', 'sober' ) ?></option>
					<option value="1" {{ parseInt( data.megaData.mega ) ? 'selected="selected"' : '' }}><?php esc_html_e( 'Yes', 'sober' ) ?></option>
				</select>
			</label>
		</span>

		<span class="setting-field">
			<label>
				<?php esc_html_e( 'Mega panel width', 'sober' ) ?><br>
				<input type="text" name="{{ smm.getFieldName( 'width', data.data['menu-item-db-id'] ) }}" placeholder="auto" value="{{ data.megaData.width }}">
			</label>
		</span>
	</p>

	<div id="smm-mega-content" class="smm-mega-content">
		<#
		var items = _.filter( data.children, function( item ) {
			return item.subDepth == 0;
		} );
		#>
		<# _.each( items, function( item, index ) { #>

			<div class="smm-submenu-column" data-width="{{ item.megaData.width }}">
				<ul>
					<li class="menu-item menu-item-depth-{{ item.subDepth }}">
						<# if ( item.megaData.icon ) { #>
						<i class="{{ item.megaData.icon }}"></i>
						<# } #>
						{{{ item.data['menu-item-title'] }}}
						<# if ( item.subDepth == 0 ) { #>
						<span class="smm-column-handle smm-resizable-e"><i class="dashicons dashicons-arrow-left-alt2"></i></span>
						<span class="smm-column-width-label"></span>
						<span class="smm-column-handle smm-resizable-w"><i class="dashicons dashicons-arrow-right-alt2"></i></span>
						<input type="hidden" name="{{ smm.getFieldName( 'width', item.data['menu-item-db-id'] ) }}" value="{{ item.megaData.width }}" class="menu-item-width">
						<# } #>
					</li>
				</ul>
			</div>

		<# } ) #>
	</div>
</div>
