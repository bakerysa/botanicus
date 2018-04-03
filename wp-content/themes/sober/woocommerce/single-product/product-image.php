<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.2
 */

defined( 'ABSPATH' ) || exit;

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = method_exists( $product, 'get_image_id' ) ? $product->get_image_id() : get_post_thumbnail_id( $product->get_id() );
$thumbnail_size    = apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' );
$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	//	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . ( has_post_thumbnail() ? 'with-images' : 'without-images' ),
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
) );

if ( ! sober_get_option( 'product_lightbox' ) ) {
	$wrapper_classes[] = 'woocommerce-product-gallery--no_lightbox';
}
?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		$attributes = array(
			'title'                   => get_post_field( 'post_title', $post_thumbnail_id ),
			'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
			'data-src'                => $full_size_image[0],
			'data-large_image'        => $full_size_image[0],
			'data-large_image_width'  => $full_size_image[1],
			'data-large_image_height' => $full_size_image[2],
		);

		if ( has_post_thumbnail() ) {
			$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $product->get_id(), 'shop_thumbnail' ) . '" class="woocommerce-product-gallery__image woocommerce-main-image"><a href="' . esc_url( $full_size_image[0] ) . '">';
			$html .= get_the_post_thumbnail( $product->get_id(), 'shop_single', $attributes );
			$html .= '</a></div>';
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html .= '</div>';
		}

		$images         = array();
		$images[]       = apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id );
		$attachment_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();

		if ( $attachment_ids && has_post_thumbnail() ) {
			foreach ( $attachment_ids as $attachment_id ) {
				$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
				$attributes      = array(
					'title'                   => get_post_field( 'post_title', $attachment_id ),
					'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
					'data-src'                => $full_size_image[0],
					'data-large_image'        => $full_size_image[0],
					'data-large_image_width'  => $full_size_image[1],
					'data-large_image_height' => $full_size_image[2],
				);

				$html = '<div data-thumb="' . get_the_post_thumbnail_url( $attachment_id, 'shop_thumbnail' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '">';
				$html .= wp_get_attachment_image( $attachment_id, 'shop_single', false, $attributes );
				$html .= '</a></div>';

				$images[] = $html;
			}
		}

		echo '<div class="woocommerce-product-gallery__slider">' . implode( "\n\t", $images ) . '</div>';

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</figure>
</div>
