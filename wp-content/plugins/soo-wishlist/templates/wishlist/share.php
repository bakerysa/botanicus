<?php
/**
 * Template for displaying add to wishlist button.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/share.php.
 *
 * @author        SooPlugins
 * @package       Soo Wishlist/Templates
 * @version       1.0.0
 */
?>

<div class="soo-wishlist-share wishlist-share">
	<h4><?php esc_html_e( 'Share on', 'soow' ) ?></h4>
	<ul>
		<?php
		foreach ( $socials as $social => $enabled ) :
			if ( ! $enabled || $enabled !== 'yes' ) {
				continue;
			}

			$icon = $social;
			$url = '';

			switch ( strtolower( $social ) ) {
				case 'facebook':
					$url = add_query_arg( array(
						'url' => urlencode( $wishlist_url )
					), 'https://www.facebook.com/sharer.php' );
					break;

				case 'twitter':
					$url = add_query_arg( array(
						'url' => urlencode( $wishlist_url )
					), 'https://twitter.com/share' );
					break;

				case 'google':
					$icon = 'google-plus';
					$url = add_query_arg( array(
						'url' => urlencode( $wishlist_url )
					), 'https://plus.google.com/share' );
					break;

				case 'email':
					$icon = 'envelope';
					$url = sprintf( 'mailto:?subject=%s&body=%s', esc_html__( 'I have some thing to share you', 'sober' ), $wishlist_url );
					break;
			}

			if ( ! empty( $url ) ) {
				printf(
					'<li class="%s"><a href="%s" target="_blank" class="%s" title="%s"><i class="fa fa-%s"></i></a></li>',
					esc_attr( strtolower( $social ) ) . '-share',
					esc_url( $url ),
					esc_attr( strtolower( $social ) ),
					esc_attr( esc_html__( 'Share on ', 'soow' ) ) . ucwords( $social ),
					esc_attr( strtolower( $icon ) )
				);
			}
		endforeach;
		?>
	</ul>
</div>
