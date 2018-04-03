<div id="smm-panel-settings" class="smm-panel-settings smm-panel">
	<# if ( data.depth == 1 ) { #>

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Hide label', 'sober' ) ?></th>
				<td>
					<label>
						<input type="checkbox" name="{{ smm.getFieldName( 'hide_text', data.data['menu-item-db-id'] ) }}" value="1" {{ parseInt( data.megaData.hide_text ) ? 'checked="checked"' : '' }}>
						<?php esc_html_e( 'Hide menu item text', 'sober' ) ?>
					</label>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Disable link', 'sober' ) ?></th>
				<td>
					<label>
						<input type="checkbox" name="{{ smm.getFieldName( 'disable_link', data.data['menu-item-db-id'] ) }}" value="1" {{ parseInt( data.megaData.disable_link ) ? 'checked="checked"' : '' }}>
						<?php esc_html_e( 'Disable menu item link', 'sober' ) ?>
					</label>
				</td>
			</tr>
		</table>

	<# } #>
</div>
