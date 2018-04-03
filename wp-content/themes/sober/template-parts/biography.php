<?php
/**
 * The template part for displaying an Author biography
 *
 * @package Sober
 */
?>

<div class="author-info clearfix">
	<div class="author-vcard pull-left">
		<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>"><?php echo get_avatar( get_the_author_meta( 'user_email' ), 60 ); ?></a>
		<h2 class="author-title">
			<span class="author-heading"><?php esc_html_e( 'Author', 'sober' ); ?></span>
			<span class="author-name"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>"><?php echo get_the_author(); ?></a></span>
		</h2>
	</div><!-- .author-avatar -->

	<div class="author-socials text-right pull-right">
		<?php
		$socials = array( 'facebook', 'twitter', 'google', 'instagram', 'pinterest' );
		foreach ( $socials as $social ) {
			$link = get_the_author_meta( $social );

			if ( empty( $link ) ) {
				continue;
			}

			printf(
				'<a href="%s" target="_blank" rel="nofollow"><i class="fa fa-%s" aria-hidden="true"></i></a>',
				esc_url( $link ),
				str_replace( array( 'google', 'pinterest' ), array( 'google-plus', 'pinterest-p' ), $social )
			);
		}
		?>
	</div><!-- .author-description -->
</div><!-- .author-info -->
