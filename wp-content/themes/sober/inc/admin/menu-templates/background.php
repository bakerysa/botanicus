<# var itemId = data.data['menu-item-db-id']; #>
<div id="smm-panel-background" class="smm-panel-background smm-panel">
	<p class="background-image">
		<label><?php esc_html_e( 'Background Image', 'sober' ) ?></label><br>
		<span class="background-image-preview">
			<# if ( data.megaData.background.image ) { #>
				<img src="{{ data.megaData.background.image }}">
			<# } #>
		</span>

		<button type="button" class="button remove-button <# if ( ! data.megaData.background.image ) { print( 'hidden' ) } #>"><?php esc_html_e( 'Remove', 'sober' ) ?></button>
		<button type="button" class="button upload-button" id="background_image-button"><?php esc_html_e( 'Select Image', 'sober' ) ?></button>

		<input type="hidden" name="{{ smm.getFieldName( 'background.image', itemId ) }}" value="{{ data.megaData.background.image }}">
	</p>

	<p class="background-color">
		<label><?php esc_html_e( 'Background Color', 'sober' ) ?></label><br>
		<input type="text" class="background-color-picker" name="{{ smm.getFieldName( 'background.color', itemId ) }}" value="{{ data.megaData.background.color }}">
	</p>

	<p class="background-repeat">
		<label><?php esc_html_e( 'Background Repeat', 'sober' ) ?></label><br>
		<select name="{{ smm.getFieldName( 'background.repeat', itemId ) }}">
			<option value="no-repeat" {{ 'no-repeat' == data.megaData.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'No Repeat', 'sober' ) ?></option>
			<option value="repeat" {{ 'repeat' == data.megaData.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile', 'sober' ) ?></option>
			<option value="repeat-x" {{ 'repeat-x' == data.megaData.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile Horizontally', 'sober' ) ?></option>
			<option value="repeat-y" {{ 'repeat-y' == data.megaData.background.repeat ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tile Vertically', 'sober' ) ?></option>
		</select>
	</p>

	<p class="background-position background-position-x">
		<label><?php esc_html_e( 'Background Position', 'sober' ) ?></label><br>

		<select name="{{ smm.getFieldName( 'background.position.x', itemId ) }}">
			<option value="left" {{ 'left' == data.megaData.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Left', 'sober' ) ?></option>
			<option value="center" {{ 'center' == data.megaData.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Center', 'sober' ) ?></option>
			<option value="right" {{ 'right' == data.megaData.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Right', 'sober' ) ?></option>
			<option value="custom" {{ 'custom' == data.megaData.background.position.x ? 'selected="selected"' : '' }}><?php esc_html_e( 'Custom', 'sober' ) ?></option>
		</select>

		<input
			type="text"
			name="{{ smm.getFieldName( 'background.position.custom.x', itemId ) }}"
			value="{{ data.megaData.background.position.custom.x }}"
			class="{{ 'custom' != data.megaData.background.position.x ? 'hidden' : '' }}">
	</p>

	<p class="background-position background-position-y">
		<select name="{{ smm.getFieldName( 'background.position.y', itemId ) }}">
			<option value="top" {{ 'top' == data.megaData.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Top', 'sober' ) ?></option>
			<option value="center" {{ 'center' == data.megaData.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Middle', 'sober' ) ?></option>
			<option value="bottom" {{ 'bottom' == data.megaData.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Bottom', 'sober' ) ?></option>
			<option value="custom" {{ 'custom' == data.megaData.background.position.y ? 'selected="selected"' : '' }}><?php esc_html_e( 'Custom', 'sober' ) ?></option>
		</select>
		<input
			type="text"
			name="{{ smm.getFieldName( 'background.position.custom.y', itemId ) }}"
			value="{{ data.megaData.background.position.custom.y }}"
			class="{{ 'custom' != data.megaData.background.position.y ? 'hidden' : '' }}">
	</p>

	<p class="background-attachment">
		<label><?php esc_html_e( 'Background Attachment', 'sober' ) ?></label><br>
		<select name="{{ smm.getFieldName( 'background.attachment', itemId ) }}">
			<option value="scroll" {{ 'scroll' == data.megaData.background.attachment ? 'selected="selected"'  : '' }}><?php esc_html_e( 'Scroll', 'sober' ) ?></option>
			<option value="fixed" {{ 'fixed' == data.megaData.background.attachment ? 'selected="selected"'  : '' }}><?php esc_html_e( 'Fixed', 'sober' ) ?></option>
		</select>
	</p>

	<p class="background-size">
		<label><?php esc_html_e( 'Background Size', 'sober' ) ?></label><br>
		<select name="{{ smm.getFieldName( 'background.size', itemId ) }}">
			<option value=""><?php esc_html_e( 'Default', 'sober' ) ?></option>
			<option value="cover" {{ 'cover' == data.megaData.background.size ? 'selected="selected"' : '' }}><?php esc_html_e( 'Cover', 'sober' ) ?></option>
			<option value="contain" {{ 'contain' == data.megaData.background.size ? 'selected="selected"' : '' }}><?php esc_html_e( 'Contain', 'sober' ) ?></option>
		</select>
	</p>
</div>