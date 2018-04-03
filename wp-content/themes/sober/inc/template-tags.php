<?php
/**
 * Custom template tags for this theme.
 *
 * @package Sober
 */


if ( ! function_exists( 'sober_fonts_url' ) ) :

	/**
	 * Register fonts
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	function sober_fonts_url() {
		$fonts_url     = '';
		$font_families = array();
		$font_subsets  = array( 'latin', 'latin-ext' );

		/* Translators: If there are characters in your language that are not
		* supported by Poppins, translate this to 'off'. Do not translate
		* into your own language.
		*/
		if ( 'off' !== _x( 'on', 'Poppins font: on or off', 'sober' ) ) {
			$font_families['Poppins'] = 'Poppins:300,400,500,600,700';
		}

		// Get custom fonts from typography settings
		$settings = array(
			'typo_body',
			'typo_h1',
			'typo_h2',
			'typo_h3',
			'typo_h4',
			'typo_h5',
			'typo_h6',
			'typo_menu',
			'typo_submenu',
			'typo_toggle_menu',
			'typo_toggle_submenu',
			'typo_page_header_title',
			'typo_page_header_minimal_title',
			'typo_breadcrumb',
			'type_widget_title',
			'type_product_title',
			'type_product_excerpt',
			'typo_woocommerce_headers',
			'type_footer_info',
		);

		foreach ( $settings as $setting ) {
			$typography = sober_get_option( $setting );

			if (
				isset( $typography['font-family'] )
				&& ! empty( $typography['font-family'] )
				&& ( 'Sofia Pro' !== $typography['font-family'] )
				&& ! array_key_exists( $typography['font-family'], $font_families )
			) {
				$font_families[ $typography['font-family'] ] = $typography['font-family'];

				if ( isset( $typography['subsets'] ) ) {
					if ( is_array( $typography['subsets'] ) ) {
						$font_subsets = array_merge( $font_subsets, $typography['subsets'] );
					} else {
						$font_subsets[] = $typography['subsets'];
					}
				}
			}
		}

		if ( ! empty( $font_families ) ) {
			$font_subsets = array_unique( $font_subsets );
			$query_args   = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( implode( ',', $font_subsets ) ),
			);

			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}

		return esc_url_raw( $fonts_url );
	}

endif;

if ( ! function_exists( 'sober_entry_meta' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and categories.
	 */
	function sober_entry_meta() {
		$time_string = sprintf(
			'<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date( get_option( 'date_format', 'd.m Y' ) ) )
		);

		$posted_on = is_singular() ? $time_string : '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';

		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( ' ' );

		echo '<span class="posted-on">' . $posted_on . '</span><span class="cat-links"> ' . $categories_list . '</span>'; // WPCS: XSS OK.
	}
endif;

if ( ! function_exists( 'sober_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for post tags.
	 */
	function sober_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			$tags_list = get_the_tag_list( '', ' ' );

			if ( $tags_list ) {
				printf( '<span class="tags-links">%s</span>', $tags_list ); // WPCS: XSS OK.
			}
		}
	}
endif;

if ( ! function_exists( 'sober_currency_switcher' ) ) :
	/**
	 * Print HTML of currency switcher
	 * It requires plugin WooCommerce Currency Switcher installed
	 */
	function sober_currency_switcher() {
		if ( ! class_exists( 'WOOCS' ) ) {
			return;
		}

		global $WOOCS;

		$currencies    = $WOOCS->get_currencies();
		$currency_list = array();
		foreach ( $currencies as $key => $currency ) {
			if ( $WOOCS->current_currency == $key ) {
				array_unshift( $currency_list, sprintf(
					'<li><a href="#" class="woocs_flag_view_item woocs_flag_view_item_current" data-currency="%s">%s</a></li>',
					esc_attr( $currency['name'] ),
					esc_html( $currency['name'] )
				) );
			} else {
				$currency_list[] = sprintf(
					'<li><a href="#" class="woocs_flag_view_item" data-currency="%s">%s</a></li>',
					esc_attr( $currency['name'] ),
					esc_html( $currency['name'] )
				);
			}
		}
		?>
		<div class="currency list-dropdown">
			<span class="current"><?php echo esc_html( $currencies[ $WOOCS->current_currency ]['name'] ); ?>
				<span class="caret"></span></span>
			<ul>
				<?php echo implode( "\n\t", $currency_list ); ?>
			</ul>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'sober_language_switcher' ) ) :
	/**
	 * Print HTML of language switcher
	 * It requires plugin WPML installed
	 */
	function sober_language_switcher() {
		$languages = function_exists( 'icl_get_languages' ) ? icl_get_languages() : apply_filters( 'sober_languages', array() );

		if ( empty( $languages ) ) {
			return;
		}

		$lang_list = array();
		$current   = '';

		foreach ( (array) $languages as $code => $language ) {
			if ( ! $language['active'] ) {
				$lang_list[] = sprintf(
					'<li class="%s"><a href="%s">%s</a></li>',
					esc_attr( $code ),
					esc_url( $language['url'] ),
					esc_html( $language['translated_name'] )
				);
			} else {
				$current = $language;
				array_unshift( $lang_list, sprintf(
					'<li class="%s"><a href="%s">%s</a></li>',
					esc_attr( $code ),
					esc_url( $language['url'] ),
					esc_html( $language['translated_name'] )
				) );
			}
		}
		?>

		<div class="language list-dropdown">
			<span class="current"><?php echo esc_html( $current['language_code'] ) ?><span class="caret"></span></span>
			<ul>
				<?php echo implode( "\n\t", $lang_list ); ?>
			</ul>
		</div>

		<?php
	}
endif;

if ( ! function_exists( 'sober_has_page_header' ) ) :
	/**
	 * Check if current page has page header
	 *
	 * @return bool
	 */
	function sober_has_page_header() {
		$has = sober_get_option( 'page_header_enable' );

		if ( is_front_page() && ! is_home() ) {
			$has = false;
		} elseif ( is_page_template( 'templates/homepage.php' ) ) {
			$has = false;
		} elseif ( is_page() && get_post_meta( get_the_ID(), 'hide_page_header', true ) ) {
			$has = false;
		} elseif ( is_singular( array( 'post', 'product', 'portfolio' ) ) ) {
			$has = false;
		} elseif ( is_404() ) {
			$has = false;
		} elseif ( is_home() ) {
			$posts_page_id = get_option( 'page_for_posts' );

			if ( $posts_page_id && get_post_meta( $posts_page_id, 'hide_page_header', true ) ) {
				$has = false;
			}
		} elseif ( is_post_type_archive( 'portfolio' ) ) {
			if ( 'masonry' != sober_get_option( 'portfolio_style' ) ) {
				$has = false;
			} else {
				$has = false;
			}
		} elseif ( function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) {
			$has = false;
		} elseif ( function_exists( 'soow_is_wishlist' ) && soow_is_wishlist() ) {
			$has = false;
		} elseif ( sober_is_order_tracking_page() ) {
			$has = false;
		} elseif ( function_exists( 'WC' ) ) {
			if ( is_account_page() || is_cart() ) {
				$has = false;
			} elseif ( is_shop() && get_post_meta( wc_get_page_id( 'shop' ), 'hide_page_header', true ) ) {
				$has = false;
			}
		}

		/**
		 * Filter for checking has page header
		 *
		 * @since  2.0.8
		 */
		return apply_filters( 'sober_has_page_header', $has );
	}
endif;

if ( ! function_exists( 'sober_get_page_header_image' ) ) :

	/**
	 * Get page header image URL
	 *
	 * @return string
	 */
	function sober_get_page_header_image() {
		if ( ! sober_has_page_header() ) {
			return '';
		}

		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return '';
		}

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$image = sober_get_option( 'shop_page_header_bg' );

			if ( is_product_taxonomy() ) {
				$term_id      = get_queried_object_id();
				$thumbnail_id = absint( get_term_meta( $term_id, 'page_header_image_id', true ) );

				if ( $thumbnail_id ) {
					$thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'full' );
					$image     = $thumbnail[0];
				}
			} elseif ( is_shop() ) {
				$shop_image = get_post_meta( wc_get_page_id( 'shop' ), 'page_header_bg', true );
				$image      = $shop_image ? current( wp_get_attachment_image_src( $shop_image, 'full' ) ) : $image;
			}
		} elseif ( is_home() && ! is_front_page() ) {
			$posts_page_id = get_option( 'page_for_posts' );

			if ( $posts_page_id ) {
				$image = get_post_meta( $posts_page_id, 'page_header_bg', true );
				$image = $image ? current( wp_get_attachment_image_src( $image, 'full' ) ) : sober_get_option( 'page_header_bg' );
			} else {
				$image = sober_get_option( 'page_header_bg' );
			}
		} elseif ( is_page() ) {
			$image = get_post_meta( get_the_ID(), 'page_header_bg', true );
			$image = $image ? wp_get_attachment_image_src( $image, 'full' ) : get_the_post_thumbnail_url( get_the_ID(), 'full' );
			$image = $image ? $image[0] : sober_get_option( 'page_header_bg' );
		} elseif ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) {
			$image = sober_get_option( 'portfolio_page_header_bg' );
		} else {
			$image = sober_get_option( 'page_header_bg' );
		}

		return $image;
	}
endif;

if ( ! function_exists( 'sober_get_layout' ) ) :
	/**
	 * Get layout base on current page
	 *
	 * @return string
	 */
	function sober_get_layout() {
		$layout = sober_get_option( 'layout_default' );

		if ( is_404() || is_singular( array(
				'product',
				'portfolio',
			) ) || is_post_type_archive( array( 'portfolio' ) ) || is_tax( 'portfolio_type' ) || sober_is_order_tracking_page() || is_page_template( 'templates/homepage.php' )
		) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'soow_is_wishlist' ) && soow_is_wishlist() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) {
			$layout = 'no-sidebar';
		} elseif ( is_singular() && get_post_meta( get_the_ID(), 'custom_layout', true ) ) {
			$layout = get_post_meta( get_the_ID(), 'layout', true );
		} elseif ( is_singular( 'post' ) ) {
			$layout = sober_get_option( 'layout_post' );
		} elseif ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_post_type_archive( 'product' ) ) ) {
			$layout = sober_get_option( 'layout_shop' );
		} elseif ( is_page() ) {
			$layout = sober_get_option( 'layout_page' );
		}

		return $layout;
	}

endif;

if ( ! function_exists( 'sober_get_content_columns' ) ) :
	/**
	 * Get CSS classes for content columns
	 *
	 * @param string $layout
	 *
	 * @return array
	 */
	function sober_get_content_columns( $layout = null ) {
		$layout = $layout ? $layout : sober_get_layout();

		if ( 'no-sidebar' == $layout ) {
			return array();
		}

		if ( is_page() ) {
			return array( 'col-md-9', 'col-sm-12', 'col-xs-12' );
		}

		return array( 'col-md-8', 'col-sm-12', 'col-xs-12' );
	}

endif;

if ( ! function_exists( 'sober_content_columns' ) ) :

	/**
	 * Display CSS classes for content columns
	 *
	 * @param string $layout
	 */
	function sober_content_columns( $layout = null ) {
		echo implode( ' ', sober_get_content_columns( $layout ) );
	}

endif;

if ( ! function_exists( 'the_comments_pagination' ) ) :
	/**
	 * Back compact function for comments pagination
	 *
	 * @param array $args
	 */
	function the_comments_pagination( $args = array() ) {
		if ( get_comment_pages_count() < 1 || get_option( 'page_comments' ) ) {
			return;
		}
		?>
		<nav class="navigation comments-pagination" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comments navigation', 'sober' ) ?></h2>
			<div class="nav-links">
				<?php paginate_comments_links( $args ) ?>
			</div>
		</nav>
		<?php
	}

endif;

if ( ! function_exists( 'sober_entry_thumbnail' ) ) :
	/**
	 * Show entry thumbnail base on its format
	 *
	 * @since  1.0
	 */
	function sober_entry_thumbnail( $size = 'thumbnail' ) {
		$html = '';

		switch ( get_post_format() ) {
			case 'gallery':
				$images = get_post_meta( get_the_ID(), 'images' );

				if ( empty( $images ) ) {
					break;
				}

				$gallery = array();
				foreach ( $images as $image ) {
					$gallery[] = wp_get_attachment_image( $image, $size );
				}
				$html .= '<div class="entry-gallery entry-image">' . implode( '', $gallery ) . '</div>';
				break;

			case 'audio':

				$thumb = get_the_post_thumbnail( get_the_ID(), $size );
				if ( ! empty( $thumb ) ) {
					$html .= '<a class="entry-image" href="' . get_permalink() . '">' . $thumb . '</a>';
				}

				$audio = get_post_meta( get_the_ID(), 'audio', true );
				if ( ! $audio ) {
					break;
				}

				// If URL: show oEmbed HTML or jPlayer
				if ( filter_var( $audio, FILTER_VALIDATE_URL ) ) {
					if ( $oembed = @wp_oembed_get( $audio, array( 'width' => 1140 ) ) ) {
						$html .= $oembed;
					} else {
						$html .= '<div class="audio-player">' . wp_audio_shortcode( array( 'src' => $audio ) ) . '</div>';
					}
				} else {
					$html .= $audio;
				}
				break;

			case 'video':
				$video = get_post_meta( get_the_ID(), 'video', true );
				if ( ! $video ) {
					break;
				}

				// If URL: show oEmbed HTML
				if ( filter_var( $video, FILTER_VALIDATE_URL ) ) {
					if ( $oembed = @wp_oembed_get( $video, array( 'width' => 1140 ) ) ) {
						$html .= $oembed;
					} else {
						$atts = array(
							'src'   => $video,
							'width' => 1140,
						);

						if ( has_post_thumbnail() ) {
							$atts['poster'] = get_the_post_thumbnail_url( get_the_ID(), 'full' );
						}
						$html .= wp_video_shortcode( $atts );
					}
				} // If embed code: just display
				else {
					$html .= $video;
				}
				break;

			default:
				$html = get_the_post_thumbnail( get_the_ID(), $size );

				break;
		}

		echo apply_filters( __FUNCTION__, $html, get_post_format() );
	}

endif;

if ( ! function_exists( 'sober_social_share' ) ) :
	/**
	 * Print HTML for post sharing
	 *
	 * @param int $post_id
	 */
	function sober_social_share( $post_id = null ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		?>
		<ul class="socials-share">
			<li>
				<a target="_blank" class="share-facebook social"
				   href="http://www.facebook.com/sharer.php?u=<?php echo urlencode( get_permalink( $post_id ) ); ?>&t=<?php echo urlencode( get_the_title( $post_id ) ); ?>">
					<i class="fa fa-facebook"></i>
				</a>
			</li>
			<li>
				<a class="share-twitter social"
				   href="http://twitter.com/share?text=<?php echo esc_attr( get_the_title( $post_id ) ); ?>&url=<?php echo urlencode( get_permalink( $post_id ) ); ?>"
				   target="_blank">
					<i class="fa fa-twitter"></i>
				</a>
			</li>
			<li>
				<a target="_blank" class="share-google-plus social"
				   href="https://plus.google.com/share?url=<?php echo urlencode( get_permalink( $post_id ) ); ?>&text=<?php echo urlencode( get_the_title( $post_id ) ); ?>"><i
						class="fa fa-google-plus"></i>
				</a>
			</li>
			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
				<li>
					<a target="_blank" class="share-pinterest social"
					   href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>&description=<?php echo urlencode( get_the_title( $post_id ) ); ?>"><i
							class="fa fa-pinterest-p"></i>
					</a>
				</li>
			<?php endif; ?>
		</ul>
		<?php
	}
endif;

if ( ! function_exists( 'sober_is_order_tracking_page' ) ) :
	/**
	 * Check if current page is order tracking page
	 *
	 * @return bool
	 */
	function sober_is_order_tracking_page() {
		$page_id = get_option( 'sober_order_tracking_page_id' );
		$page_id = sober_get_translated_object_id( $page_id );

		if ( ! $page_id ) {
			return false;
		}

		return is_page( $page_id );
	}
endif;

if ( ! function_exists( 'sober_get_translated_object_id' ) ) :
	/**
	 * Get translated object ID if the WPML plugin is installed
	 * Return the original ID if this plugin is not installed
	 *
	 * @param int    $id            The object ID
	 * @param string $type          The object type 'post', 'page', 'post_tag', 'category' or 'attachment'. Default is 'page'
	 * @param bool   $original      Set as 'true' if you want WPML to return the ID of the original language element if the translation is missing.
	 * @param bool   $language_code If set, forces the language of the returned object and can be different than the displayed language.
	 *
	 * @return mixed
	 */
	function sober_get_translated_object_id( $id, $type = 'page', $original = true, $language_code = false ) {
		if ( function_exists( 'wpml_object_id_filter' ) ) {
			return wpml_object_id_filter( $id, $type, $original, $language_code );
		} elseif ( function_exists( 'icl_object_id' ) ) {
			return icl_object_id( $id, $type, $original, $language_code );
		}

		return $id;
	}
endif;

if ( ! function_exists( 'sober_get_mega_menu_setting_default' ) ) :
	/**
	 * Get the default mega menu settings of a menu item
	 *
	 * @return array
	 */
	function sober_get_mega_menu_setting_default() {
		return apply_filters(
			'sober_mega_menu_setting_default',
			array(
				'mega'         => false,
				'icon'         => '',
				'hide_text'    => false,
				'disable_link' => false,
				'content'      => '',
				'width'        => '',
				'border'       => array(
					'left' => 0,
				),
				'background'   => array(
					'image'      => '',
					'color'      => '',
					'attachment' => 'scroll',
					'size'       => '',
					'repeat'     => 'no-repeat',
					'position'   => array(
						'x'      => 'left',
						'y'      => 'top',
						'custom' => array(
							'x' => '',
							'y' => '',
						),
					),
				),
			)
		);
	}
endif;

if ( ! function_exists( 'sober_parse_args' ) ) :
	/**
	 * Recursive merge user defined arguments into defaults array.
	 *
	 * @param array $args
	 * @param array $default
	 *
	 * @return array
	 */
	function sober_parse_args( $args, $default = array() ) {
		$args   = (array) $args;
		$result = $default;

		foreach ( $args as $key => $value ) {
			if ( is_array( $value ) && isset( $result[ $key ] ) ) {
				$result[ $key ] = sober_parse_args( $value, $result[ $key ] );
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

endif;

if ( ! function_exists( 'get_theme_file_path' ) ) :
	/**
	 * Retrieves the path of a file in the theme.
	 *
	 * Searches in the stylesheet directory before the template directory so themes
	 * which inherit from a parent theme can just override one file.
	 *
	 * @param string $file Optional. File to search for in the stylesheet directory.
	 *
	 * @return string The path of the file.
	 */
	function get_theme_file_path( $file = '' ) {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$path = get_stylesheet_directory();
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
			$path = get_stylesheet_directory() . '/' . $file;
		} else {
			$path = get_template_directory() . '/' . $file;
		}

		/**
		 * Filters the path to a file in the theme.
		 *
		 * @param string $path The file path.
		 * @param string $file The requested file to search for.
		 */
		return apply_filters( 'theme_file_path', $path, $file );
	}
endif;

if ( ! function_exists( 'get_theme_file_uri' ) ) :
	/**
	 * Retrieves the URL of a file in the theme.
	 *
	 * Searches in the stylesheet directory before the template directory so themes
	 * which inherit from a parent theme can just override one file.
	 *
	 * @param string $file Optional. File to search for in the stylesheet directory.
	 *
	 * @return string The URL of the file.
	 */
	function get_theme_file_uri( $file = '' ) {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$url = get_stylesheet_directory_uri();
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
			$url = get_stylesheet_directory_uri() . '/' . $file;
		} else {
			$url = get_template_directory_uri() . '/' . $file;
		}

		/**
		 * Filters the URL to a file in the theme.
		 *
		 * @param string $url  The file URL.
		 * @param string $file The requested file to search for.
		 */
		return apply_filters( 'theme_file_uri', $url, $file );
	}
endif;

if ( ! function_exists( 'sober_portfolio_filter' ) ) :
	/**
	 * Get portfolio types and display it as a filter for Isotope script
	 */
	function sober_portfolio_filter() {
		$types = get_terms( array(
			'taxonomy'   => 'portfolio_type',
			'hide_empty' => true,
		) );

		if ( ! $types || is_wp_error( $types ) || 1 === count( $types ) ) {
			return;
		}

		$filter   = array();
		$filter[] = '<li data-filter="*" class="line-hover active">' . esc_html__( 'All', 'sober' ) . '</li>';

		foreach ( $types as $type ) {
			$filter[] = sprintf( '<li data-filter=".portfolio_type-%s" class="line-hover">%s</li>', esc_attr( $type->slug ), esc_html( $type->name ) );
		}

		printf(
			'<div class="portfolio-filter"><ul class="filter">%s</ul></div>',
			implode( "\n\t", $filter )
		);
	}
endif;

if ( ! function_exists( 'sober_shopping_cart_icon' ) ) {
	/**
	 * Get shopping cart icon HTML
	 */
	function sober_shopping_cart_icon( $echo = true ) {
		$source = sober_get_option( 'shop_cart_icon_source' );
		$icon   = '<svg viewBox="0 0 20 20"><use xlink:href="#basket-addtocart"></use></svg>';

		if ( 'image' == $source ) {
			$width  = floatval( sober_get_option( 'shop_cart_icon_width' ) );
			$height = floatval( sober_get_option( 'shop_cart_icon_height' ) );

			$width  = $width ? ' width="' . $width . 'px"' : '';
			$height = $height ? ' height="' . $height . 'px"' : '';

			$dark  = sober_get_option( 'shop_cart_icon_image' );
			$light = sober_get_option( 'shop_cart_icon_image_light' );
			$light = $light ? $light : $dark;

			if ( $dark ) {
				$icon = sprintf(
					'<span class="shopping-cart-icon"><img src="%1$s" alt="%2$s" %3$s class="icon-dark"><img src="%4$s" alt="%2$s" %3$s class="icon-light"></span>',
					esc_url( $dark ),
					esc_attr__( 'Shopping Cart', 'sober' ),
					$width . $height,
					esc_url( $light )
				);
			}
		} else {
			$svg = sober_get_option( 'shop_cart_icon' );

			if ( $svg ) {
				$icon = sprintf( '<svg viewBox="0 0 20 20"><use xlink:href="#%s"></use></svg>', esc_attr( $svg ) );
			}
		}

		if ( ! $echo ) {
			return $icon;
		}

		echo $icon;
	}
}

if ( ! function_exists( 'sober_header_icons' ) ) :
	/**
	 * Print header icons base on settings in Customizer
	 *
	 * @param string $header_version
	 * @param string $position
	 */
	function sober_header_icons( $header_version = 'v1', $position = 'right' ) {
		switch ( $header_version ) {
			case 'v4':
				$icons = sober_get_option( 'header_icons_' . $position . '_v4' );
				break;

			case 'v5':
				$icons = sober_get_option( 'header_icons_' . $position );
				break;

			default:
				$icons = sober_get_option( 'header_icons' );
				break;
		}

		if ( empty( $icons ) ) {
			return;
		}

		foreach ( (array) $icons as $icon ) {
			switch ( $icon ) {
				case 'cart':
					if ( ! function_exists( 'WC' ) ) {
						break;
					}
					printf(
						'<li class="menu-item menu-item-cart">
							<a href="%s" class="cart-contents" data-toggle="%s" data-target="cart-modal" data-tab="cart">
								%s
								<span class="count cart-counter">%s</span>
							</a>
						</li>',
						esc_url( wc_get_cart_url() ),
						esc_attr( sober_get_option( 'shop_cart_icon_behaviour' ) ),
						sober_shopping_cart_icon( false ),
						intval( WC()->cart->get_cart_contents_count() )
					);
					break;

				case 'wishlist':
					if ( defined( 'YITH_WCWL' ) ) {
						printf(
							'<li class="menu-item menu-item-wishlist">
								<a href="%s" class="wishlist-contents" data-toggle="%s" data-target="cart-modal" data-tab="wishlist">
									<svg viewBox="0 0 20 20"><use xlink:href="#heart-wishlist-like"></use></svg>
									<span class="count wishlist-counter">%s</span>
								</a>
							</li>',
							esc_url( get_permalink( yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) ) ) ),
							esc_attr( sober_get_option( 'wishlist_icon_behaviour' ) ),
							yith_wcwl_count_products()
						);
					} elseif ( function_exists( 'Soo_Wishlist' ) ) {
						printf(
							'<li class="menu-item menu-item-wishlist"><a href="%s" class="wishlist-contents" data-toggle="%s" data-target="cart-modal" data-tab="wishlist"><svg viewBox="0 0 20 20"><use xlink:href="#heart-wishlist-like"></use></svg><span class="count wishlist-counter">%s</span></a></li>',
							esc_url( soow_get_wishlist_url() ),
							esc_attr( sober_get_option( 'wishlist_icon_behaviour' ) ),
							soow_count_products()
						);
					}
					break;

				case 'login':
					if ( ! function_exists( 'WC' ) ) {
						break;
					}
					printf(
						'<li class="menu-item menu-item-account"><a href="%s" data-toggle="%s" data-target="login-modal"><svg viewBox="0 0 20 20"><use xlink:href="#user-account-people"></use></svg></a></li>',
						esc_url( wc_get_account_endpoint_url( 'dashboard' ) ),
						is_user_logged_in() ? 'link' : 'modal'
					);
					break;

				case 'search':
					echo '<li class="menu-item menu-item-search"><a href="#" data-toggle="modal" data-target="search-modal"><svg viewBox="0 0 20 20"><use xlink:href="#search"></use></svg></a></li>';
					break;
			}
		}
	}
endif;

if ( ! function_exists( 'sober_typography_css' ) ) :
	/**
	 * Get typography CSS base on settings
	 *
	 * @since 1.1.6
	 */
	function sober_typography_css() {
		$css        = '';
		$properties = array(
			'font-family'    => 'font-family',
			'font-size'      => 'font-size',
			'variant'        => 'font-weight',
			'line-height'    => 'line-height',
			'letter-spacing' => 'letter-spacing',
			'color'          => 'color',
			'text-transform' => 'text-transform',
			'text-align'     => 'text-align',
		);

		$settings = array(
			'typo_body'                      => 'body,button,input,select,textarea',
			'typo_link'                      => 'a',
			'typo_link_hover'                => 'a:hover, a:visited',
			'typo_h1'                        => 'h1, .h1',
			'typo_h2'                        => 'h2, .h2',
			'typo_h3'                        => 'h3, .h3',
			'typo_h4'                        => 'h4, .h4',
			'typo_h5'                        => 'h5, .h5',
			'typo_h6'                        => 'h6, .h6',
			'typo_menu'                      => '.nav-menu > li > a',
			'typo_submenu'                   => '.nav-menu .sub-menu a',
			'typo_toggle_menu'               => '.primary-menu.side-menu .menu > li > a',
			'typo_toggle_submenu'            => '.primary-menu.side-menu .sub-menu li a',
			'typo_page_header_title'         => '.page-header .page-title',
			'typo_page_header_minimal_title' => '.page-header-style-minimal .page-header .page-title',
			'typo_breadcrumb'                => '.woocommerce .woocommerce-breadcrumb, .breadcrumb',
			'type_widget_title'              => '.widget-title',
			'type_product_title'             => '.woocommerce div.product .product_title',
			'type_product_excerpt'           => '.woocommerce div.product .woocommerce-product-details__short-description, .woocommerce div.product div[itemprop="description"]',
			'typo_woocommerce_headers'       => '.woocommerce .upsells h2, .woocommerce .related h2',
			'type_footer_info'               => '.footer-info',
		);

		foreach ( $settings as $setting => $selector ) {
			$typography = sober_get_option( $setting );
			$default    = (array) sober_get_option_default( $setting );
			$style      = '';

			foreach ( $properties as $key => $property ) {
				if ( isset( $typography[ $key ] ) && ! empty( $typography[ $key ] ) ) {
					if ( isset( $default[ $key ] ) && strtoupper( $default[ $key ] ) == strtoupper( $typography[ $key ] ) ) {
						continue;
					}

					$value = 'font-family' == $key ? rtrim( trim( $typography[ $key ] ), ',' ) : $typography[ $key ];
					$value = 'variant' == $key ? str_replace( 'regular', '400', $value ) : $value;

					if ( $value ) {
						$style .= $property . ': ' . $value . ';';
					}
				}
			}

			if ( ! empty( $style ) ) {
				$css .= $selector . '{' . $style . '}';
			}
		}

		return $css;
	}
endif;

if ( ! function_exists( 'sober_get_instagram_images' ) ) :
	/**
	 * Get Instagram images
	 *
	 * @param string $username
	 * @param int    $limit
	 *
	 * @return array|WP_Error
	 */
	function sober_get_instagram_images( $username, $limit = 12 ) {
		$username = strtolower( $username );
		$username = str_replace( '@', '', $username );
		$keys     = get_option( 'sober_instagram_keys' );

		if ( isset( $keys[ $username ] ) && $keys[ $username ]['limit'] >= $limit ) {
			$transient_key = $keys[ $username ]['key'];
		} else {
			$transient_key = 'sober_instagram-' . sanitize_title_with_dashes( $username . '__' . $limit );
		}

		if ( false === ( $images = get_transient( $transient_key ) ) ) {
			// Get user ID
			$username = strtolower( $username );
			$username = ltrim( $username, '@' );
			$url      = 'https://instagram.com/' . trim( $username ) . '/?__a=1';

			$profile = wp_remote_get( $url );
			if ( is_wp_error( $profile ) ) {
				return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'sober' ) );
			}

			if ( 200 != wp_remote_retrieve_response_code( $profile ) ) {
				return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'sober' ) );
			}

			if ( ! $profile ) {
				return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'sober' ) );
			}

			$profile = json_decode( $profile['body'], true );

			if ( ! isset( $profile['graphql']['user']['id'] ) ) {
				return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'sober' ) );
			}

			$user_id = $profile['graphql']['user']['id'];
			// End getting user ID

			// Get images
			$data = sober_scrape_instagram( $user_id, $limit );

			if ( is_wp_error( $data ) ) {
				return $data;
			}

			$images = $data['images'];
			$count  = $data['image_count'];

			while ( $count < $limit && $data['has_next_page'] ) {
				$data   = sober_scrape_instagram( $user_id, $limit, $data['end_cursor'] );
				$images = array_merge( $images, $data['images'] );
				$count += count( $data['image_count'] );
			}

			$images = serialize( $images );
			set_transient( $transient_key, $images, 2 * 3600 );

			// Store transition key
			$keys[ $username ] = array(
				'key'   => $transient_key,
				'limit' => $count,
			);
			update_option( 'sober_instagram_keys', $keys );
		}

		if ( ! empty( $images ) ) {
			return unserialize( $images );
		} else {
			return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'sober' ) );
		}
	}
endif;

if ( ! function_exists( 'sober_scrape_instagram' ) ) :
	/**
	 * Get images from Instagram Graphql
	 *
	 * @since 2.0
	 *
	 * @param int $user_id Instagram user ID
	 * @param int $limit
	 * @param string $end_cursor
	 *
	 * @return array | WP_Error
	 */
	function sober_scrape_instagram( $user_id, $limit = 12, $end_cursor = '' ) {
		if ( 0 > $limit ) {
			return new WP_Error( 'invalid_limit', esc_html__( 'No images to get.', 'sober' ) );
		}

		$params = array(
			'query_id' => '17888483320059182',
			'id'       => $user_id,
			'first'    => $limit,
		);

		if ( ! empty( $end_cursor ) ) {
			$params['after'] = $end_cursor;
		}

		$url    = add_query_arg( $params, 'https://instagram.com/graphql/query/' );
		$remote = wp_remote_get( $url );

		if ( is_wp_error( $remote ) ) {
			return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram Graphql.', 'sober' ) );
		}

		if ( 200 != wp_remote_retrieve_response_code( $remote ) ) {
			return new WP_Error( 'invalid_response', esc_html__( 'Instagram Graphql did not return a 200.', 'sober' ) );
		}

		$data = json_decode( $remote['body'], true );

		if ( ! $data ) {
			return new WP_Error( 'bad_json', esc_html__( 'Instagram Graphql has returned invalid data.', 'sober' ) );
		}

		if ( ! isset( $data['data']['user']['edge_owner_to_timeline_media'] ) ) {
			return new WP_Error( 'bad_json_2', esc_html__( 'Instagram Graphql has returned invalid data.', 'sober' ) );
		}

		$data = $data['data']['user']['edge_owner_to_timeline_media'];

		if ( ! is_array( $data ) ) {
			return new WP_Error( 'bad_array', esc_html__( 'Instagram Graphql has returned invalid data.', 'sober' ) );
		}

		$images = array();
		$image_count = 0;

		foreach ( $data['edges'] as $node ) {
			$node = $node['node'];

			// No need to store video
			if ( ! $node['is_video'] ) {
				$image_count++;
			}

			if ( isset( $node['thumbnail_resources'] ) ) {
				$sizes['150'] = $node['thumbnail_resources'][0]['src'];
				$sizes['240'] = $node['thumbnail_resources'][1]['src'];
				$sizes['320'] = $node['thumbnail_resources'][2]['src'];
				$sizes['480'] = $node['thumbnail_resources'][3]['src'];
				$sizes['640'] = $node['thumbnail_resources'][4]['src'];
			} else {
				$sizes['640'] = $node['thumbnail_src'];
			}

			if ( isset( $node['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
				$caption = $node['edge_media_to_caption']['edges'][0]['node']['text'];
			} else {
				$caption = '';
			}

			$images[] = array(
				'description' => $caption,
				'link'        => trailingslashit( '//instagram.com/p/' . $node['shortcode'] ),
				'time'        => $node['taken_at_timestamp'],
				'comments'    => $node['edge_media_to_comment']['count'],
				'likes'       => $node['edge_media_preview_like']['count'],
				'thumbnail'   => $node['thumbnail_resources'][0]['src'],
				'small'       => $node['thumbnail_resources'][2]['src'],
				'large'       => $node['thumbnail_src'],
				'original'    => $node['display_url'],
				'type'        => $node['is_video'] ? 'video' : 'image',
			);
		}

		return array(
			'images'        => $images,
			'image_count'   => $image_count,
			'has_next_page' => $data['page_info']['has_next_page'],
			'end_cursor'    => $data['page_info']['end_cursor'],
		);
	}
endif;

if ( ! function_exists( 'sober_mobile_header_icon' ) ) :
	/**
	 * Display the header icon base on settings in Customizer
	 */
	function sober_mobile_header_icon() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$icon = sober_get_option( 'mobile_header_icon' );

		switch ( $icon ) {
			case 'cart':
				printf(
					'<a href="%s" class="cart-contents  menu-item-mobile-cart hidden-lg" data-toggle="%s" data-target="cart-modal" data-tab="cart">%s%s</a>',
					esc_url( wc_get_cart_url() ),
					esc_attr( sober_get_option( 'shop_cart_icon_behaviour' ) ),
					sober_shopping_cart_icon( false ),
					sober_get_option( 'mobile_cart_badge' ) ? '<span class="count cart-counter">' . intval( WC()->cart->get_cart_contents_count() ) . '</span>' : ''
				);

				break;

			case 'wishlist':
				if ( defined( 'YITH_WCWL' ) ) {
					printf(
						'<a href="%s" class="wishlist-contents menu-item-mobile-wishlist hidden-lg" data-toggle="%s" data-target="cart-modal" data-tab="wishlist">
							<svg viewBox="0 0 20 20"><use xlink:href="#heart-wishlist-like"></use></svg>
							%s
						</a>',
						esc_url( get_permalink( yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) ) ) ),
						esc_attr( sober_get_option( 'wishlist_icon_behaviour' ) ),
						sober_get_option( 'mobile_wishlist_badge' ) ? '<span class="count wishlist-counter">' . yith_wcwl_count_products() . '</span>' : ''
					);
				} elseif ( function_exists( 'Soo_Wishlist' ) ) {
					printf(
						'<a href="%s" class="wishlist-contents menu-item-mobile-wishlist hidden-lg" data-toggle="%s" data-target="cart-modal" data-tab="wishlist">
							<svg viewBox="0 0 20 20"><use xlink:href="#heart-wishlist-like"></use></svg>
							%s
						</a>',
						esc_url( soow_get_wishlist_url() ),
						esc_attr( sober_get_option( 'wishlist_icon_behaviour' ) ),
						sober_get_option( 'mobile_wishlist_badge' ) ? '<span class="count wishlist-counter">' . soow_count_products() . '</span>' : ''
					);
				}
				break;
		}
	}
endif;
