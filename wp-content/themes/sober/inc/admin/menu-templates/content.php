<?php
global $wp_widget_factory;
?>
<div id="smm-panel-content" class="smm-panel-content smm-panel">
	<p>
		<textarea name="{{ smm.getFieldName( 'content', data.data['menu-item-db-id'] ) }}" class="widefat" rows="20" contenteditable="true">{{{ data.megaData.content }}}</textarea>
	</p>
	<p class="description"><?php esc_html_e( 'Allow HTML and Shortcodes', 'sober' ) ?></p>
	<p class="description"><?php esc_html_e( 'Tip: Build your content inside a page with visual page builder then copy generated content here.', 'sober' ) ?></p>
</div>