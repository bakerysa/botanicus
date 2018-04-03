<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ) ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'sober' ) ?></span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search the site', 'sober' ) ?>" value="<?php get_search_query() ?>" name="s" />
	</label>
	<input type="submit" class="search-submit" value="<?php esc_attr_e( 'Search', 'sober' ) ?>" />
</form>