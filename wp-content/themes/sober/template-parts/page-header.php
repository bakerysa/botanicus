<?php
/**
 * Template part for displaying page header
 */
?>
<div class="page-header">
		<div class="sober-container clearfix">
			<?php
			if ( ! is_singular() ) {
				the_archive_title( '<h1 class="page-title">', '</h1>' );
			} elseif ( is_page() ) {
				printf( '<div class="page-title">%s</div>', single_post_title( '', false ) );
			} else {
				printf( '<h1 class="page-title">%s</h1>', single_post_title( '', false ) );
			}
			sober_site_breadcrumb();
			?>
	</div>
</div>