<div id="smm-panel-design" class="smm-panel-design smm-panel">
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e( 'Border', 'sober' ) ?></th>
			<td>
				<fieldset>
					<label>
						<input type="checkbox" name="{{ smm.getFieldName( 'border.left', data.data['menu-item-db-id'] ) }}" value="1" {{ parseInt( data.megaData.border.left ) ? 'checked="checked"' : '' }}>
						<?php esc_html_e( 'Border Left', 'sober' ) ?>
					</label>
				</fieldset>
			</td>
		</tr>
	</table>
</div>
