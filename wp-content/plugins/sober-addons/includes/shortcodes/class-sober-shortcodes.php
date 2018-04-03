<?php

class Sober_Shortcodes {
	public static $current_banner = 1;

	/**
	 * Init shortcodes
	 */
	public static function init() {
		$shortcodes = array(
			'button',
			'product_grid',
			'product_carousel',
			'product_tabs',
			'post_grid',
			'countdown',
			'category_banner',
			'product',
			'banner2',
			'banner3',
			'banner4',
			'banner_grid_4',
			'banner_grid_5',
			'banner_grid_6',
			'chart',
			'message_box',
			'icon_box',
			'pricing_table',
			'map',
			'testimonial',
			'partners',
			'contact_box',
			'info_list',
			'faq',
			'team_member',
		);

		foreach ( $shortcodes as $shortcode ) {
			add_shortcode( 'sober_' . $shortcode, array( __CLASS__, $shortcode ) );
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_filter( 'post_class', array( __CLASS__, 'product_class' ), 10, 3 );

		add_action( 'wp_ajax_nopriv_sober_load_products', array( __CLASS__, 'ajax_load_products' ) );
		add_action( 'wp_ajax_sober_load_products', array( __CLASS__, 'ajax_load_products' ) );
	}

	/**
	 * Load scripts
	 */
	public static function enqueue_scripts() {
		wp_deregister_script( 'isotope' );
		wp_register_script( 'isotope', SOBER_ADDONS_URL . 'assets/js/isotope.pkgd.min.js', array(
			'jquery',
			'imagesloaded',
		), '3.0.1', true );
		wp_register_script( 'jquery-countdown', SOBER_ADDONS_URL . 'assets/js/jquery.countdown.js', array( 'jquery' ), '2.0.4', true );
		wp_register_script( 'jquery-circle-progress', SOBER_ADDONS_URL . 'assets/js/circle-progress.js', array( 'jquery' ), '1.1.3', true );

		wp_enqueue_script( 'sober-shortcodes', SOBER_ADDONS_URL . 'assets/js/shortcodes.js', array(
			'isotope',
			'wp-util',
			'jquery-countdown',
			'jquery-circle-progress',
		), '20160725', true );
	}

	/**
	 * Add classes to products which are inside loop of shortcodes
	 *
	 * @param array  $classes
	 * @param string $class
	 * @param int    $post_id
	 *
	 * @return array
	 */
	public static function product_class( $classes, $class, $post_id ) {
		if ( ! $post_id || get_post_type( $post_id ) !== 'product' || is_single( $post_id ) ) {
			return $classes;
		}

		global $woocommerce_loop;
		$accept_products = array(
			'sober_product_grid',
			'sober_ajax_products',
		);

		if ( ! isset( $woocommerce_loop['name'] ) || ! in_array( $woocommerce_loop['name'], $accept_products ) ) {
			return $classes;
		}

		// Add class for new products
		$newness = get_theme_mod( 'product_newness', false );
		if ( $newness && ( time() - ( 60 * 60 * 24 * $newness ) ) < strtotime( get_the_time( 'Y-m-d' ) ) ) {
			$classes[] = 'new';
		}

		return $classes;
	}

	/**
	 * Ajax load products
	 */
	public static function ajax_load_products() {
		check_ajax_referer( 'sober_get_products', 'nonce' );

		$atts = array(
			'load_more' => isset( $_POST['load_more'] ) ? $_POST['load_more'] : true,
			'type'      => isset( $_POST['type'] ) ? $_POST['type'] : '',
			'page'      => isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1,
			'per_page'  => isset( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : 10,
		);

		if ( isset( $_POST['columns'] ) ) {
			$atts['columns'] = intval( $_POST['columns'] );
		}

		if ( isset( $_POST['category'] ) ) {
			$atts['category'] = trim( $_POST['category'] );
		}

		$data = self::product_loop( $atts );

		wp_send_json_success( $data );
	}

	/**
	 * Product grid shortcode
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function product_grid( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 15,
			'type'          => 'recent',
			'category'      => '',
			'columns'       => 4,
			'load_more'     => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );


		$css_class = array(
			'sober-product-grid',
			'sober-products',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['load_more'] ) {
			$css_class[] = 'loadmore-enabled';
		}

		return sprintf(
			'<div class="sober-product-grid sober-products %s">%s</div>',
			esc_attr( trim( implode( ' ', $css_class ) ) ),
			self::product_loop( $atts )
		);
	}

	/**
	 * Product grid filterable
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function product_carousel( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 15,
			'columns'       => 4,
			'type'          => 'recent',
			'category'      => '',
			'autoplay'      => 5000,
			'loop'          => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );


		$css_class = array(
			'sober-product-carousel',
			'sober-products',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		return sprintf(
			'<div class="%s" data-columns="%s" data-autoplay="%s" data-loop="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $atts['columns'] ),
			esc_attr( $atts['autoplay'] ),
			esc_attr( $atts['loop'] ),
			self::product_loop( $atts )
		);
	}

	/**
	 * Product grid filterable
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function product_tabs( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 15,
			'columns'       => 4,
			'filter'        => 'category',
			'filter_type'   => 'isotope',
			'category'      => '',
			'load_more'     => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-product-grid',
			'sober-product-tabs',
			'sober-products',
			'sober-products-filterable',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['filter'] ) {
			$css_class[] = 'filterable';
			$css_class[] = 'filter-by-' . $atts['filter'];
			$css_class[] = 'filter-type-' . $atts['filter_type'];
		}

		if ( $atts['load_more'] ) {
			$css_class[] = 'loadmore-enabled';
		}

		$filter = array();

		if ( 'category' == $atts['filter'] ) {
			if ( empty( $atts['category'] ) ) {
				$categories = get_terms( 'product_cat' );
			} else {
				$categories = get_terms( array(
					'taxonomy' => 'product_cat',
					'slug'     => explode( ',', trim( $atts['category'] ) ),
				) );
			}

			if ( $categories && ! is_wp_error( $categories ) ) {
				if ( 'isotope' == $atts['filter_type'] ) {
					$filter[] = '<li data-filter="*" class="line-hover active">' . esc_html__( 'Show All', 'sober' ) . '</li>';
				} else {
					$atts['category'] = $categories[0]->slug; // Prepare for product_loop only
				}

				foreach ( $categories as $index => $category ) {
					$filter[] = sprintf(
						'<li data-filter=".product_cat-%s" class="line-hover %s">%s</li>',
						esc_attr( $category->slug ),
						'ajax' == $atts['filter_type'] && ! $index ? 'active' : '',
						esc_html( $category->name )
					);
				}
			}
		} elseif ( 'group' == $atts['filter'] ) {
			$atts['type'] = 'best_sellers'; // Prepare for product_loop only

			if ( 'isotope' == $atts['filter_type'] ) {
				$filter[] = '<li data-filter="*" class="line-hover active">' . esc_html__( 'Best Sellers', 'sober' ) . '</li>';
			} else {
				$filter[] = '<li data-filter=".best_sellers" class="line-hover active">' . esc_html__( 'Best Sellers', 'sober' ) . '</li>';
			}
			$filter[] = '<li data-filter=".new" class="line-hover">' . esc_html__( 'New Products', 'sober' ) . '</li>';
			$filter[] = '<li data-filter=".sale" class="line-hover">' . esc_html__( 'Sales Products', 'sober' ) . '</li>';
		}

		$loading = '
			<div class="products-loading-overlay">
				<span class="loading-icon">
					<span class="bubble"><span class="dot"></span></span>
					<span class="bubble"><span class="dot"></span></span>
					<span class="bubble"><span class="dot"></span></span>
				</span>
			</div>';

		return sprintf(
			'<div class="%s" data-columns="%s" data-per_page="%s" data-load_more="%s" data-nonce="%s">%s<div class="products-grid">%s%s</div></div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $atts['columns'] ),
			esc_attr( $atts['per_page'] ),
			esc_attr( $atts['load_more'] ),
			esc_attr( wp_create_nonce( 'sober_get_products' ) ),
			empty( $filter ) ? '' : '<div class="product-filter"><ul class="filter">' . implode( "\n\t", $filter ) . '</ul></div>',
			'ajax' == $atts['filter_type'] ? $loading : '',
			self::product_loop( $atts )
		);
	}

	/**
	 * Post grid
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function post_grid( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 3,
			'columns'       => 3,
			'category'      => '',
			'hide_meta'     => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-post-grid',
			'post-grid',
			'columns-' . $atts['columns'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$output = array();

		$args = array(
			'post_type'              => 'post',
			'posts_per_page'         => $atts['per_page'],
			'ignore_sticky_posts'    => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);

		if ( $atts['category'] ) {
			$args['category_name'] = trim( $atts['category'] );
		}

		$posts = new WP_Query( $args );

		if ( ! $posts->have_posts() ) {
			return '';
		}

		$column_class = 'col-sm-6 col-md-' . ( 12 / absint( $atts['columns'] ) );

		while ( $posts->have_posts() ) : $posts->the_post();
			$post_class = get_post_class( $column_class );
			$thumbnail  = $meta = '';

			if ( has_post_thumbnail() ) :
				$icon = '';

				if ( 'gallery' == get_post_format() ) {
					$icon = '<span class="format-icon"><svg viewBox="0 0 20 20"><use xlink:href="#gallery"></use></svg></span>';
				} elseif ( 'video' == get_post_format() ) {
					$icon = '<span class="format-icon"><svg viewBox="0 0 20 20"><use xlink:href="#play"></use></svg></span>';
				}

				$thumbnail = sprintf(
					'<a href="%s" class="post-thumbnail">%s%s</a>',
					esc_url( get_permalink() ),
					get_the_post_thumbnail( get_the_ID(), 'sober-blog-grid' ),
					$icon
				);
			endif;

			if ( ! $atts['hide_meta'] ) {
				$posted_on = sprintf(
					'<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date( 'd.m Y' ) )
				);

				$categories_list = get_the_category_list( ' ' );

				$meta = '<span class="posted-on">' . $posted_on . '</span><span class="cat-links"> ' . $categories_list . '</span>'; // WPCS: XSS OK.
			}

			$output[] = sprintf(
				'<div class="%s">
					%s
					<div class="post-summary">
						<div class="entry-meta">%s</div>
						<h3 class="entry-title"><a href="%s" rel="bookmark">%s</a></h3>
						<div class="entry-summary">%s</div>
						<a class="line-hover read-more active" href="%s">%s</a>
					</div>
				</div>',
				esc_attr( implode( ' ', $post_class ) ),
				$thumbnail,
				$atts['hide_meta'] ? '' : '<div class="entry-meta">' . $meta . '</div>',
				esc_url( get_permalink() ),
				get_the_title(),
				get_the_excerpt(),
				esc_url( get_permalink() ),
				esc_html__( 'Read More', 'sober' )
			);
		endwhile;

		wp_reset_postdata();

		return sprintf(
			'<div class="sober-post-grid post-grid %s">
				<div class="row">%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			implode( '', $output )
		);
	}

	/**
	 * Count down
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function countdown( $atts ) {
		$atts = shortcode_atts( array(
			'date'          => '',
			'text_align'    => 'left',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( empty( $atts['date'] ) ) {
			return '';
		}

		$css_class = array(
			'sober-countdown',
			'text-' . $atts['text_align'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$output   = array();
		$output[] = sprintf( '<div class="timers" data-date="%s">', esc_attr( $atts['date'] ) );
		$output[] = sprintf( '<div class="timer-day box"><span class="time day"></span><span class="title">%s</span></div>', esc_html__( 'Days', 'sober' ) );
		$output[] = sprintf( '<div class="timer-hour box"><span class="time hour"></span><span class="title">%s</span></div>', esc_html__( 'Hours', 'sober' ) );
		$output[] = sprintf( '<div class="timer-min box"><span class="time min"></span><span class="title">%s</span></div>', esc_html__( 'Mins', 'sober' ) );
		$output[] = sprintf( '<div class="timer-secs box"><span class="time secs"></span><span class="title">%s</span></div>', esc_html__( 'Sec', 'sober' ) );
		$output[] = '</div>';

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			implode( '', $output )
		);
	}

	/**
	 * Button
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function button( $atts ) {
		$atts = shortcode_atts( array(
			'label'         => '',
			'link'          => '',
			'style'         => 'normal',
			'size'          => 'normal',
			'align'         => 'inline',
			'color'         => 'dark',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$attributes = array();

		$css_class = array(
			'sober-button',
			'button-type-' . $atts['style'],
			'button-color-' . $atts['color'],
			'align-' . $atts['align'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( 'light' == $atts['style'] ) {
			$css_class[] = 'button-light line-hover';
		} else {
			$css_class[] = 'button';
			$css_class[] = $atts['size'];
			$css_class[] = 'button-' . $atts['size'];
		}

		if ( function_exists( 'vc_build_link' ) && ! empty( $atts['link'] ) ) {
			$link = vc_build_link( $atts['link'] );

			if ( ! empty( $link['url'] ) ) {
				$attributes['href'] = $link['url'];
			}

			if ( ! empty( $link['title'] ) ) {
				$attributes['title'] = $link['title'];
			}

			if ( ! empty( $link['target'] ) ) {
				$attributes['target'] = $link['target'];
			}

			if ( ! empty( $link['rel'] ) ) {
				$attributes['rel'] = $link['rel'];
			}
		}

		$attributes['class'] = implode( ' ', $css_class );
		$attr                = array();

		foreach ( $attributes as $name => $value ) {
			$attr[] = $name . '="' . esc_attr( $value ) . '"';
		}

		$button = sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			empty( $attributes['href'] ) ? 'span' : 'a',
			implode( ' ', $attr ),
			esc_html( $atts['label'] )
		);

		if ( 'center' == $atts['align'] ) {
			return '<div class="sober-button-wrapper text-center">' . $button . '</div>';
		}

		return $button;
	}

	/**
	 * Category Banner
	 *
	 * @param string $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function category_banner( $atts, $content ) {
		$atts = shortcode_atts( array(
			'image'          => '',
			'image_position' => 'left',
			'title'          => '',
			'text_position'  => 'top-left',
			'link'           => '',
			'button_text'    => '',
			'css_animation'  => '',
			'el_class'       => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-category-banner',
			'text-position-' . $atts['text_position'],
			'image-' . $atts['image_position'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$link = vc_build_link( $atts['link'] );

		$src   = '';
		$image = wp_get_attachment_image_src( $atts['image'], 'full' );

		if ( $image ) {
			$src   = $image[0];
			$image = sprintf( '<img alt="%s" src="%s">',
				esc_attr( $atts['image'] ),
				esc_url( $src )
			);
		}

		return sprintf(
			'<div class="%s">
				<div class="banner-inner">
					<a href="%s" target="%s" rel="%s" class="banner-image" style="%s">%s</a>
					<div class="banner-content">
						<h2 class="banner-title">%s</h2>
						<div class="banner-text">%s</div>
						<a href="%s" target="%s" rel="%s" class="sober-button button-light line-hover active">%s</a>
					</div>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			$src ? 'background-image: url(' . esc_url( $src ) . ');' : '',
			$image,
			esc_html( $atts['title'] ),
			$content,
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_html( $atts['button_text'] )
		);
	}

	/**
	 * Product
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function product( $atts, $content ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'title'         => '',
			'price'         => '',
			'link'          => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-product',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$image = wp_get_attachment_image_src( $atts['image'], 'full' );
		$src   = $image[0];
		$image = sprintf( '<img alt="%s" src="%s">', esc_attr( $atts['title'] ), esc_url( $image[0] ) );
		$link  = vc_build_link( $atts['link'] );

		$price = floatval( $atts['price'] );

		if ( shortcode_exists( 'woocs_show_custom_price' ) ) {
			$price = do_shortcode( '[woocs_show_custom_price value="' . $price . '"]' );
		} else {
			$price = wc_price( $price );
		}

		return sprintf(
			'<div class="%s">
				<div class="product-image" style="%s">
					%s
				</div>
				<div class="product-info">
					<h3 class="product-title">%s</h3>
					<div class="product-desc">%s</div>
					<div class="product-price">
						<span class="price">%s</span>
						<span class="button">%s</span>
					</div>
				</div>
				<a href="%s" target="%s" rel="%s" class="overlink">%s</a>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$src ? 'background-image: url(' . esc_url( $src ) . ');' : '',
			$image,
			esc_html( $atts['title'] ),
			$content,
			$price,
			esc_html__( 'Add to cart', 'sober' ),
			esc_url( $link['url'] ),
			esc_url( $link['target'] ),
			esc_url( $link['rel'] ),
			esc_html__( 'View Product', 'sober' )
		);
	}

	/**
	 * Banner 2 with buttons
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function banner2( $atts ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'image_size'    => '',
			'buttons'       => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-banner2',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);
		$image     = '';

		if ( $atts['image'] ) {
			$size = apply_filters( 'sober_banner_size', $atts['image_size'], $atts, 'sober_banner2' );

			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $size,
				) );

				$image = $image['thumbnail'];
			} else {
				$size_array = explode( 'x', $size );
				$size       = count( $size_array ) == 1 ? $size : $size_array;

				$image = wp_get_attachment_image_src( $atts['image'], $size );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s">',
						esc_attr( $atts['image'] ),
						esc_url( $image[0] )
					);
				}
			}
		}

		$buttons        = vc_param_group_parse_atts( $atts['buttons'] );
		$buttons_output = array();
		foreach ( (array) $buttons as $index => $button ) {
			$link = vc_build_link( $button['link'] );

			$buttons_output[] = sprintf(
				'<a href="%s" target="%s" title="%s" rel="%s" class="banner-button banner-button-%s">%s</a>',
				esc_url( $link['url'] ),
				esc_attr( $link['target'] ),
				esc_attr( $link['title'] ),
				esc_attr( $link['rel'] ),
				esc_attr( $index + 1 ),
				esc_html( $button['text'] )
			);
		}

		return sprintf(
			'<div class="%s">%s<div class="banner-buttons">%s</div></div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			implode( '', $buttons_output )
		);
	}

	/**
	 * Banner 3
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function banner3( $atts ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'image_size'    => '',
			'text'          => '',
			'text_align'    => 'left',
			'link'          => '',
			'button_text'   => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-banner3',
			'text-align-' . $atts['text_align'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);
		$link      = vc_build_link( $atts['link'] );
		$image     = '';

		if ( $atts['image'] ) {
			$size = apply_filters( 'sober_banner_size', $atts['image_size'], $atts, 'sober_banner3' );

			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $size,
				) );

				$image = $image['thumbnail'];
			} else {
				$size_array = explode( 'x', $size );
				$size       = count( $size_array ) == 1 ? $size : $size_array;

				$image = wp_get_attachment_image_src( $atts['image'], $size );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s">',
						esc_attr( $atts['text'] ),
						esc_url( $image[0] )
					);
				}
			}
		}

		return sprintf(
			'<div class="%s">
				<a href="%s" target="%s" rel="%s" title="%s">
					%s
					<span class="banner-content">
						<span class="banner-text">%s</span>
						<span class="sober-button button-light line-hover active">%s</span>
					</span>
				</a>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_attr( $link['title'] ),
			$image,
			esc_html( $atts['text'] ),
			esc_html( $atts['button_text'] )
		);
	}

	/**
	 * Banner 4
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner4( $atts, $content ) {
		$atts = shortcode_atts( array(
			'image'            => '',
			'image_size'       => 'full',
			'align_vertical'   => 'top',
			'align_horizontal' => 'left',
			'link'             => '',
			'button_text'      => '',
			'scheme'           => 'dark',
			'css_animation'    => '',
			'el_class'         => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-banner4',
			'horizontal-align-' . $atts['align_horizontal'],
			'vertical-align-' . $atts['align_vertical'],
			$atts['scheme'] . '-scheme',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);
		$link      = vc_build_link( $atts['link'] );
		$image     = '';

		if ( $atts['image'] ) {
			$size = apply_filters( 'sober_banner_size', $atts['image_size'], $atts, 'sober_banner4' );

			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $size,
				) );

				$image = $image['thumbnail'];
			} else {
				$size_array = explode( 'x', $size );
				$size       = count( $size_array ) == 1 ? $size : $size_array;

				$image = wp_get_attachment_image_src( $atts['image'], $size );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s">',
						esc_attr( $atts['text'] ),
						esc_url( $image[0] )
					);
				}
			}
		}

		$content = function_exists( 'wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content, true ) : $content;

		return sprintf(
			'<div class="%s">
				%s
				<div class="banner-content">
					<span class="banner-text">%s</span>
					<span class="sober-button button-light line-hover active">%s</span>
				</div>
				<a href="%s" target="%s" rel="%s" title="%s">%s</a>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			do_shortcode( $content ),
			esc_html( $atts['button_text'] ),
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_attr( $link['title'] ),
			esc_html__( 'View detail', 'sober' )
		);
	}

	/**
	 * Banner grid 4
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner_grid_4( $atts, $content ) {
		$atts = shortcode_atts( array(
			'reverse'  => 'no',
			'el_class' => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array( 'sober-banner-grid-4', $atts['el_class'] );

		if ( 'yes' == $atts['reverse'] ) {
			$css_class[] = 'reverse-order';
		}

		// Reset banner counter
		self::$current_banner = 1;

		add_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_4_banner_size' ) );
		$content = do_shortcode( $content );
		remove_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_4_banner_size' ) );

		return '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . $content . '</div>';
	}

	/**
	 * Banner grid 5
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner_grid_5( $atts, $content ) {
		$atts = shortcode_atts( array(
			'el_class' => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array( 'sober-banner-grid-5', $atts['el_class'] );

		// Reset banner counter
		self::$current_banner = 1;

		add_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_5_banner_size' ) );
		$content = do_shortcode( $content );
		remove_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_5_banner_size' ) );

		return '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . $content . '</div>';
	}

	/**
	 * Banner grid 6
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner_grid_6( $atts, $content ) {
		$atts = shortcode_atts( array(
			'reverse'  => 'no',
			'el_class' => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array( 'sober-banner-grid-6', $atts['el_class'] );

		if ( 'yes' == $atts['reverse'] ) {
			$css_class[] = 'reverse-order';
		}

		// Reset banner counter
		self::$current_banner = 1;

		add_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_6_banner_size' ) );
		$content = do_shortcode( $content );
		remove_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_6_banner_size' ) );

		return '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . $content . '</div>';
	}

	/**
	 * Chart
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function chart( $atts ) {
		$atts = shortcode_atts( array(
			'value'         => 100,
			'size'          => 200,
			'thickness'     => 8,
			'label_source'  => 'auto',
			'label'         => '',
			'color'         => '#6dcff6',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-chart',
			'sober-chart-' . $atts['value'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$label = 'custom' == $atts['label_source'] ? $atts['label'] : '<span class="unit">%</span>' . esc_html( $atts['value'] );

		return sprintf(
			'<div class="%s" data-value="%s" data-size="%s" data-thickness="%s" data-fill="%s">
				<div class="text" style="color: %s">%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( intval( $atts['value'] ) / 100 ),
			esc_attr( $atts['size'] ),
			esc_attr( $atts['thickness'] ),
			esc_attr( json_encode( array( 'color' => $atts['color'] ) ) ),
			esc_attr( $atts['color'] ),
			wp_kses_post( $label )
		);
	}

	/**
	 * Message Box
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function message_box( $atts, $content ) {
		$atts = shortcode_atts( array(
			'type'          => 'success',
			'closeable'     => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-message-box',
			$atts['type'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['closeable'] ) {
			$css_class[] = 'closeable';
		}

		$icon = str_replace( array( 'info', 'danger' ), array( 'information', 'error' ), $atts['type'] );

		return sprintf(
			'<div class="%s">
				<svg viewBox="0 0 20 20" class="message-icon"><use xlink:href="#%s"></use></svg>
				<div class="box-content">%s</div>
				%s
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $icon ),
			$content,
			$atts['closeable'] ? '<a class="close" href="#"><svg viewBox="0 0 14 14"><use xlink:href="#close-delete-small"></use></svg></a>' : ''
		);
	}

	/**
	 * Icon Box
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function icon_box( $atts, $content ) {
		$atts = shortcode_atts( array(
			'icon_type'        => 'fontawesome',
			'icon_fontawesome' => 'fa fa-adjust',
			'icon_openiconic'  => 'vc-oi vc-oi-dial',
			'icon_typicons'    => 'typcn typcn-adjust-brightness',
			'icon_entypo'      => 'entypo-icon entypo-icon-note',
			'icon_linecons'    => 'vc_li vc_li-heart',
			'icon_monosocial'  => 'vc-mono vc-mono-fivehundredpx',
			'icon_material'    => 'vc-material vc-material-cake',
			'image'            => '',
			'style'            => 'normal',
			'title'            => esc_html__( 'I am Icon Box', 'sober' ),
			'css_animation'    => '',
			'el_class'         => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-icon-box',
			'icon-type-' . $atts['icon_type'],
			'icon-style-' . $atts['style'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( 'image' == $atts['icon_type'] ) {
			$image = wp_get_attachment_image_src( $atts['image'], 'full' );
			$icon  = $image ? sprintf( '<img alt="%s" src="%s">', esc_attr( $atts['title'] ), esc_url( $image[0] ) ) : '';
		} else {
			vc_icon_element_fonts_enqueue( $atts['icon_type'] );
			$icon = '<i class="' . esc_attr( $atts[ 'icon_' . $atts['icon_type'] ] ) . '"></i>';
		}

		return sprintf(
			'<div class="%s">
				<div class="box-icon">%s</div>
				<h3 class="box-title">%s</h3>
				<div class="box-content">%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$icon,
			esc_html( $atts['title'] ),
			$content
		);
	}

	/**
	 * Pricing Table
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function pricing_table( $atts ) {
		$atts = shortcode_atts( array(
			'name'          => '',
			'price'         => '',
			'currency'      => '$',
			'recurrence'    => esc_html__( 'Per Month', 'sober' ),
			'features'      => '',
			'button_text'   => esc_html__( 'Get Started', 'sober' ),
			'button_link'   => '',
			'color'         => '#6dcff6',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-pricing-table',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$features = vc_param_group_parse_atts( $atts['features'] );
		$list     = array();
		foreach ( $features as $feature ) {
			$list[] = sprintf( '<li><span class="feature-name">%s</span><span class="feature-value">%s</span></li>', $feature['name'], $feature['value'] );
		}

		$features = $list ? '<ul>' . implode( '', $list ) . '</ul>' : '';
		$link     = vc_build_link( $atts['button_link'] );

		return sprintf(
			'<div class="%s" data-color="%s">
				<div class="table-header" style="background-color: %s">
					<h3 class="plan-name">%s</h3>
					<div class="pricing"><span class="currency">%s</span>%s</div>
					<div class="recurrence">%s</div>
				</div>
				<div class="table-content">%s</div>
				<div class="table-footer">
					<a href="%s" target="%s" rel="%s" title="%s" class="button" style="background-color: %s">%s</a>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $atts['color'] ),
			esc_attr( $atts['color'] ),
			esc_html( $atts['name'] ),
			esc_html( $atts['currency'] ),
			esc_html( $atts['price'] ),
			esc_html( $atts['recurrence'] ),
			$features,
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_attr( $link['title'] ),
			esc_attr( $atts['color'] ),
			esc_html( $atts['button_text'] )
		);
	}

	/**
	 * Google Map
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function map( $atts, $content ) {
		$atts = shortcode_atts( array(
			'api_key'       => '',
			'marker'        => '',
			'address'       => '',
			'lat'           => '',
			'lng'           => '',
			'width'         => '100%',
			'height'        => '625px',
			'zoom'          => 15,
			'color'         => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( empty( $atts['api_key'] ) ) {
			return esc_html__( 'Google map requires API Key in order to work.', 'sober' );
		}

		if ( empty( $atts['address'] ) && empty( $atts['lat'] ) && empty( $atts['lng'] ) ) {
			return esc_html__( 'No address', 'sober' );
		}

		if ( ! empty( $atts['address'] ) ) {
			$coordinates = self::get_coordinates( $atts['address'], $atts['api_key'] );
		} else {
			$coordinates = array(
				'lat' => $atts['lat'],
				'lng' => $atts['lng'],
			);
		}

		if ( ! empty( $coordinates['error'] ) ) {
			return $coordinates['error'];
		}

		$css_class = array(
			'sober-map',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$style = array();
		if ( $atts['width'] ) {
			$style[] = 'width: ' . $atts['width'];
		}

		if ( $atts['height'] ) {
			$style[] = 'height: ' . intval( $atts['height'] ) . 'px';
		}

		$marker = '';

		if ( $atts['marker'] ) {
			if ( filter_var( $atts['marker'], FILTER_VALIDATE_URL ) ) {
				$marker = $atts['marker'];
			} else {
				$attachment_image = wp_get_attachment_image_src( intval( $atts['marker'] ), 'full' );
				$marker           = $attachment_image ? $attachment_image[0] : '';
			}
		}

		wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $atts['api_key'] );

		return sprintf(
			'<div class="%s" style="%s" data-zoom="%s" data-lat="%s" data-lng="%s" data-color="%s" data-marker="%s">%s</div>',
			implode( ' ', $css_class ),
			implode( ';', $style ),
			absint( $atts['zoom'] ),
			esc_attr( $coordinates['lat'] ),
			esc_attr( $coordinates['lng'] ),
			esc_attr( $atts['color'] ),
			esc_attr( $marker ),
			$content
		);
	}

	/**
	 * Testimonial
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function testimonial( $atts, $content ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'name'          => '',
			'company'       => '',
			'align'         => 'center',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-testimonial',
			'testimonial-align-' . $atts['align'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$image = '';
		if ( $atts['image'] ) {
			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => '160x160',
				) );

				$image = $image['thumbnail'];
			} else {
				$image = wp_get_attachment_image_src( $atts['image'], 'large' );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s" width="160" height="160">',
						esc_attr( $atts['image'] ),
						esc_url( $image[0] )
					);
				}
			}
		}

		$authors = array(
			'<span class="name">' . esc_html( $atts['name'] ) . '</span>',
			'<span class="company">' . esc_html( $atts['company'] ) . '</span>',
		);

		return sprintf(
			'<div class="%s">
				<div class="author-photo">%s</div>
				<div class="testimonial-entry">
					<div class="testimonial-content">%s</div>
					<div class="testimonial-author">%s</div>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			$content,
			implode( ', ', $authors )
		);
	}

	/**
	 * Partners
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function partners( $atts ) {
		$atts = shortcode_atts( array(
			'source'              => 'media_library',
			'images'              => '',
			'custom_srcs'         => '',
			'image_size'          => 'full',
			'external_img_size'   => '',
			'custom_links'        => '',
			'custom_links_target' => '_self',
			'layout'              => 'bordered',
			'css_animation'       => '',
			'el_class'            => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class     = array(
			'sober-partners',
			$atts['layout'] . '-layout',
			$atts['el_class'],
		);
		$css_animation = self::get_css_animation( $atts['css_animation'] );
		$images        = $logos = array();
		$custom_links  = explode( ',', vc_value_from_safe( $atts['custom_links'] ) );
		$default_src   = vc_asset_url( 'vc/no_image.png' );

		switch ( $atts['source'] ) {
			case 'media_library':
				$images = explode( ',', $atts['images'] );
				break;

			case 'external_link':
				$images = vc_value_from_safe( $atts['custom_srcs'] );
				$images = explode( ',', $images );

				break;
		}

		foreach ( $images as $i => $image ) {
			$thumbnail = '';

			switch ( $atts['source'] ) {
				case 'media_library':
					if ( $image > 0 ) {
						$img       = wpb_getImageBySize( array(
							'attach_id'  => $image,
							'thumb_size' => $atts['image_size'],
						) );
						$thumbnail = $img['thumbnail'];
					} else {
						$thumbnail = '<img src="' . $default_src . '" />';
					}
					break;

				case 'external_link':
					$image      = esc_attr( $image );
					$dimensions = vcExtractDimensions( $atts['external_img_size'] );
					$hwstring   = $dimensions ? image_hwstring( $dimensions[0], $dimensions[1] ) : '';
					$thumbnail  = '<img ' . $hwstring . ' src="' . $image . '" />';
					break;
			}

			if ( empty( $custom_links[ $i ] ) ) {
				$logo = '<span class="partner-logo">' . $thumbnail . '</span>';
			} else {
				$logo = sprintf( '<a href="%s" target="%s" class="partner-logo">%s</a>', esc_url( $custom_links[ $i ] ), esc_attr( $atts['custom_links_target'] ), $thumbnail );
			}

			$logos[] = '<div class="partner' . esc_attr( $css_animation ) . '">' . $logo . '</div>';
		}

		return sprintf( '<div class="%s">%s</div>', esc_attr( implode( ' ', $css_class ) ), implode( ' ', $logos ) );
	}

	/**
	 * Contact Box
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function contact_box( $atts ) {
		$atts = shortcode_atts( array(
			'address'       => '',
			'phone'         => '',
			'fax'           => '',
			'email'         => '',
			'website'       => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-contact-box',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);
		$contact   = array();

		foreach ( array( 'address', 'phone', 'fax', 'email', 'website' ) as $info ) {
			if ( empty( $atts[ $info ] ) ) {
				continue;
			}

			$icon   = $name = '';
			$detail = esc_html( $atts[ $info ] );
			switch ( $info ) {
				case 'address':
					$name = esc_html__( 'Address', 'sober' );
					$icon = '<svg width="20" height="20" class="info-icon"><use xlink:href="#home"></use></svg>';
					break;

				case 'phone':
					$name   = esc_html__( 'Phone', 'sober' );
					$icon   = '<svg width="20" height="20" class="info-icon"><use xlink:href="#phone"></use></svg>';
					$detail = '<a href="tel:' . esc_attr( $atts[ $info ] ) . '">' . $detail . '</a>';
					break;

				case 'fax':
					$name = esc_html__( 'Fax', 'sober' );
					$icon = '<i class="info-icon fa fa-fax"></i>';
					break;

				case 'email':
					$name   = esc_html__( 'Email', 'sober' );
					$icon   = '<svg width="20" height="20" class="info-icon"><use xlink:href="#mail"></use></svg>';
					$detail = '<a href="mailto:' . esc_attr( $atts[ $info ] ) . '">' . $detail . '</a>';
					break;

				case 'website':
					$name   = esc_html__( 'Website', 'sober' );
					$icon   = '<i class="info-icon fa fa-globe"></i>';
					$detail = '<a href="' . esc_url( $atts[ $info ] ) . '" target="_blank" rel="nofollow">' . $detail . '</a>';
					break;
			}

			$contact[] = sprintf(
				'<div class="contact-info">
					%s
					<span class="info-name">%s</span>
					<span class="info-value">%s</span>
				</div>',
				$icon,
				$name,
				$detail
			);
		}

		return sprintf( '<div class="%s">%s</div>', esc_attr( implode( ' ', $css_class ) ), implode( ' ', $contact ) );
	}

	/**
	 * Info List
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function info_list( $atts ) {
		$atts = shortcode_atts( array(
			'info' => urlencode( json_encode( array(
				array(
					'icon' => 'fa fa-home',
					'label' => esc_html__( 'Address', 'sober' ),
					'value' => '9606 North MoPac Expressway',
				),
				array(
					'icon' => 'fa fa-phone',
					'label' => esc_html__( 'Phone', 'sober' ),
					'value' => '+1 248-785-8545',
				),
				array(
					'icon' => 'fa fa-fax',
					'label' => esc_html__( 'Fax', 'sober' ),
					'value' => '123123123',
				),
				array(
					'icon' => 'fa fa-envelope',
					'label' => esc_html__( 'Email', 'sober' ),
					'value' => 'sober@uix.store',
				),
				array(
					'icon' => 'fa fa-globe',
					'label' => esc_html__( 'Website', 'sober' ),
					'value' => 'http://uix.store',
				),
			) ) ),
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( function_exists( 'vc_param_group_parse_atts' ) ) {
			$info = (array) vc_param_group_parse_atts( $atts['info'] );
		} else {
			$info = json_decode( urldecode( $atts['info'] ), true );
		}

		$css_class = array(
			'sober-info-list',
			$atts['el_class'],
		);

		$animation = self::get_css_animation( $atts['css_animation'] );

		$list = array();
		foreach ( $info as $item ) {
			$list[] = sprintf(
				'<li class="%s">
					<i class="info-icon %s"></i>
					<span class="info-name">%s</span>
					<span class="info-value">%s</span>
				</li>',
				$animation,
				$item['icon'],
				$item['label'],
				$item['value']
			);
		}

		if ( ! $list ) {
			return '';
		}

		return sprintf( '<div class="%s"><ul>%s</ul></div>', esc_attr( implode( ' ', $css_class ) ), implode( '', $list ) );
	}

	/**
	 * FAQ
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function faq( $atts, $content ) {
		$atts = shortcode_atts( array(
			'title'         => esc_html__( 'Question content goes here', 'sober' ),
			'open'          => 'false',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-faq',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( 'true' == $atts['open'] ) {
			$css_class[] = 'open';
		}

		return sprintf(
			'<div class="%s">
				<div class="question">
					<span class="question-label">%s</span>
					<span class="question-icon"><span class="toggle-icon"></span></span>
					<span class="question-title">%s</span>
				</div>
				<div class="answer"><span class="answer-label">%s</span>%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_html__( 'Question', 'sober' ),
			esc_html( $atts['title'] ),
			esc_html__( 'Answer', 'sober' ),
			$content
		);
	}

	/**
	 * Team member
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function team_member( $atts ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'image_size'    => 'full',
			'name'          => '',
			'job'           => '',
			'facebook'      => '',
			'twitter'       => '',
			'google'        => '',
			'pinterest'     => '',
			'linkedin'      => '',
			'youtube'       => '',
			'instagram'     => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-team-member',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['image'] ) {
			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $atts['image_size'],
				) );

				$image = $image['thumbnail'];
			} else {
				$image = wp_get_attachment_image_src( $atts['image'], $atts['image_size'] );

				if ( $image ) {
					$image = sprintf( '<img src="%s" alt="%s" width="%s" height="%s">',
						esc_url( $image[0] ),
						esc_attr( $atts['name'] ),
						esc_attr( $image[1] ),
						esc_attr( $image[2] )
					);
				}
			}
		} else {
			$image = plugins_url( 'assets/images/man-placeholder.png', dirname( dirname( __FILE__ ) ) );
			$image = sprintf( '<img src="%s" alt="%s" width="360" height="430">',
				esc_url( $image ),
				esc_attr( $atts['name'] )
			);
		}

		$socials = array( 'facebook', 'twitter', 'google', 'pinterest', 'linkedin', 'youtube', 'instagram' );
		$links   = array();

		foreach ( $socials as $social ) {
			if ( empty( $atts[ $social ] ) ) {
				continue;
			}

			$icon = str_replace( array( 'google', 'pinterest', 'youtube' ), array(
				'google-plus',
				'pinterest-p',
				'youtube-play',
			), $social );

			$links[] = sprintf( '<a href="%s" target="_blank"><i class="fa fa-%s"></i></a>', esc_url( $atts[ $social ] ), esc_attr( $icon ) );
		}

		return sprintf(
			'<div class="%s">
				%s
				<div class="member-socials">%s</div>
				<div class="member-info">
					<h4 class="member-name">%s</h4>
					<span class="member-job">%s</span>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			implode( '', $links ),
			esc_html( $atts['name'] ),
			esc_html( $atts['job'] )
		);
	}

	/**
	 * Get coordinates
	 *
	 * @param string $address
	 * @param bool   $refresh
	 *
	 * @return array
	 */
	public static function get_coordinates( $address, $key = '', $refresh = false ) {
		$address_hash = md5( $address );
		$coordinates  = get_transient( $address_hash );
		$results      = array( 'lat' => '', 'lng' => '' );

		if ( $refresh || $coordinates === false ) {
			$args     = array( 'address' => urlencode( $address ), 'sensor' => 'false', 'key' => $key );
			$url      = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/geocode/json' );
			$response = wp_remote_get( $url );

			if ( is_wp_error( $response ) ) {
				$results['error'] = esc_html__( 'Can not connect to Google Maps APIs', 'sober' );

				return $results;
			}

			$data = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $data ) ) {
				$results['error'] = esc_html__( 'Can not connect to Google Maps APIs', 'sober' );

				return $results;
			}

			if ( $response['response']['code'] == 200 ) {
				$data = json_decode( $data );

				if ( $data->status === 'OK' ) {
					$coordinates = $data->results[0]->geometry->location;

					$results['lat']     = $coordinates->lat;
					$results['lng']     = $coordinates->lng;
					$results['address'] = (string) $data->results[0]->formatted_address;

					// cache coordinates for 3 months
					set_transient( $address_hash, $results, 3600 * 24 * 30 * 3 );
				} elseif ( $data->status === 'ZERO_RESULTS' ) {
					$results['error'] = esc_html__( 'No location found for the entered address.', 'sober' );
				} elseif ( $data->status === 'INVALID_REQUEST' ) {
					$results['error'] = esc_html__( 'Invalid request. Did you enter an address?', 'sober' );
				} else {
					$results['error'] = $data->error_message;
				}
			} else {
				$results['error'] = esc_html__( 'Unable to contact Google API service.', 'sober' );
			}
		} else {
			$results = $coordinates; // return cached results
		}

		return $results;
	}

	/**
	 * Loop over found products.
	 *
	 * @param  array  $atts
	 * @param  string $loop_name
	 *
	 * @return string
	 * @internal param array $columns
	 */
	protected static function product_loop( $atts, $loop_name = 'sober_product_grid' ) {
		global $woocommerce_loop;

		$query_args = self::get_query( $atts );

		if ( isset( $atts['type'] ) && 'top_rated' == $atts['type'] ) {
			add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
		} elseif ( isset( $atts['type'] ) && 'best_sellers' == $atts['type'] ) {
			add_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
		}

		$products = new WP_Query( $query_args );

		if ( isset( $atts['type'] ) && 'top_rated' == $atts['type'] ) {
			remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
		} elseif ( isset( $atts['type'] ) && 'best_sellers' == $atts['type'] ) {
			remove_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
		}

		$woocommerce_loop['name'] = $loop_name;
		$columns                  = isset( $atts['columns'] ) ? absint( $atts['columns'] ) : null;

		if ( $columns ) {
			$woocommerce_loop['columns'] = $columns;
		}

		ob_start();

		if ( $products->have_posts() ) {
			woocommerce_product_loop_start();

			while ( $products->have_posts() ) : $products->the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile; // end of the loop.

			woocommerce_product_loop_end();
		}

		$return = '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';

		if ( isset( $atts['load_more'] ) && $atts['load_more'] && $products->max_num_pages > 1 ) {
			$paged = max( 1, $products->get( 'paged' ) );
			$type  = isset( $atts['type'] ) ? $atts['type'] : 'recent';

			if ( $paged < $products->max_num_pages ) {
				$button = sprintf(
					'<div class="load-more text-center">
						<a href="#" class="button ajax-load-products" data-page="%s" data-columns="%s" data-per_page="%s" data-type="%s" data-category="%s" data-nonce="%s" rel="nofollow">
							<span class="button-text">%s</span>
							<span class="loading-icon">
								<span class="bubble"><span class="dot"></span></span>
								<span class="bubble"><span class="dot"></span></span>
								<span class="bubble"><span class="dot"></span></span>
							</span>
						</a>
					</div>',
					esc_attr( $paged + 1 ),
					esc_attr( $columns ),
					esc_attr( $query_args['posts_per_page'] ),
					esc_attr( $type ),
					isset( $atts['category'] ) ? esc_attr( $atts['category'] ) : '',
					esc_attr( wp_create_nonce( 'sober_get_products' ) ),
					esc_html__( 'Load More', 'sober' )
				);

				$return .= $button;
			}
		}

		woocommerce_reset_loop();
		wp_reset_postdata();

		return $return;
	}

	/**
	 * Build query args from shortcode attributes
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	private static function get_query( $atts ) {
		$args = array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'orderby'                => get_option( 'woocommerce_default_catalog_orderby' ),
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => 1,
			'posts_per_page'         => $atts['per_page'],
			'meta_query'             => WC()->query->get_meta_query(),
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);

		if( version_compare( WC()->version, '3.0.0', '>=' ) ) {
			$args['tax_query'] = WC()->query->get_tax_query();
		}

		// Ordering
		if ( 'menu_order' == $args['orderby'] || 'price' == $args['orderby'] ) {
			$args['order'] = 'ASC';
		}

		if ( 'price-desc' == $args['orderby'] ) {
			$args['orderby'] = 'price';
		}

		if ( method_exists( WC()->query, 'get_catalog_ordering_args' ) ) {
			$ordering_args   = WC()->query->get_catalog_ordering_args( $args['orderby'], $args['order'] );
			$args['orderby'] = $ordering_args['orderby'];
			$args['order']   = $ordering_args['order'];

			if ( $ordering_args['meta_key'] ) {
				$args['meta_key'] = $ordering_args['meta_key'];
			}
		}

		// Improve performance
		if ( ! isset( $atts['load_more'] ) || ! $atts['load_more'] ) {
			$args['no_found_rows'] = true;
		}

		if ( ! empty( $atts['category'] ) ) {
			$args['product_cat'] = $atts['category'];
			unset( $args['update_post_term_cache'] );
		}

		if ( ! empty( $atts['page'] ) ) {
			$args['paged'] = absint( $atts['page'] );
		}

		if ( isset( $atts['type'] ) ) {
			switch ( $atts['type'] ) {
				case 'featured':
					if( version_compare( WC()->version, '3.0.0', '<' ) ) {
						$args['meta_query'][] = array(
							'key'   => '_featured',
							'value' => 'yes',
						);
					} else {
						$args['tax_query'][] = array(
							'taxonomy' => 'product_visibility',
							'field'    => 'name',
							'terms'    => 'featured',
							'operator' => 'IN',
						);
					}

					unset( $args['update_post_meta_cache'] );
					break;

				case 'sale':
					$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
					break;

				case 'best_sellers':
					$args['meta_key'] = 'total_sales';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';
					unset( $args['update_post_meta_cache'] );

					add_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
					break;

				case 'new':
					$newness = intval( sober_get_option( 'product_newness' ) );

					if ( $newness > 0 ) {
						$args['date_query'] = array(
							'after' => date( 'Y-m-d', strtotime( '-' . $newness . ' days' ) )
						);
					} else {
						$meta_query[] = array(
							'key'   => '_is_new',
							'value' => 'yes',
						);
					}
					break;

				case 'top_rated':
					unset( $args['product_cat'] );
					$args          = self::_maybe_add_category_args( $args, $atts['category'] );
					$args['order'] = 'DESC';
					break;
			}
		}

		return $args;
	}

	/**
	 * Adds a tax_query index to the query to filter by category.
	 *
	 * @param array $args
	 * @param string $category
	 *
	 * @return array;
	 */
	protected static function _maybe_add_category_args( $args, $category ) {
		if ( ! empty( $category ) ) {
			if ( empty( $args['tax_query'] ) ) {
				$args['tax_query'] = array();
			}
			$args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_cat',
					'terms'    => array_map( 'sanitize_title', explode( ',', $category ) ),
					'field'    => 'slug',
					'operator' => 'IN',
				),
			);
		}

		return $args;
	}

	/**
	 * WP Core doens't let us change the sort direction for invidual orderby params - https://core.trac.wordpress.org/ticket/17065.
	 *
	 * This lets us sort by meta value desc, and have a second orderby param.
	 *
	 * @access public
	 * @param array $args
	 * @return array
	 */
	public static function order_by_popularity_post_clauses( $args ) {
		global $wpdb;
		$args['orderby'] = "$wpdb->postmeta.meta_value+0 DESC, $wpdb->posts.post_date DESC";
		return $args;
	}

	/**
	 * Change banner size while it is inside a banner grid 4
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public static function banner_grid_4_banner_size( $size ) {
		switch ( self::$current_banner % 8 ) {
			case 1:
			case 7:
				$size = '920x820';
				break;

			case 2:
			case 3:
			case 5:
			case 6:
				$size = '460x410';
				break;

			case 0:
			case 4:
				$size = '920x410';
				break;
		}

		self::$current_banner ++;

		return $size;
	}

	/**
	 * Change banner size while it is inside a banner grid 5
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public static function banner_grid_5_banner_size( $size ) {
		switch ( self::$current_banner % 5 ) {
			case 1:
			case 0:
				$size = '520x400';
				break;

			case 3:
				$size = '750x920';
				break;

			case 2:
			case 4:
				$size = '520x500';
				break;
		}

		self::$current_banner ++;

		return $size;
	}

	/**
	 * Change banner size while it is inside a banner grid 6
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public static function banner_grid_6_banner_size( $size ) {
		switch ( self::$current_banner % 6 ) {
			case 1:
				$size = '640x800';
				break;

			case 2:
			case 3:
				$size = '640x395';
				break;

			case 4:
			case 5:
			case 0:
				$size = '426x398';
				break;
		}

		self::$current_banner ++;

		return $size;
	}

	/**
	 * Get CSS classes for animation
	 *
	 * @param string $css_animation
	 *
	 * @return string
	 */
	public static function get_css_animation( $css_animation ) {
		$output = '';

		if ( '' !== $css_animation && 'none' !== $css_animation ) {
			wp_enqueue_script( 'waypoints' );
			wp_enqueue_style( 'animate-css' );
			$output = ' wpb_animate_when_almost_visible wpb_' . $css_animation . ' ' . $css_animation;
		}

		return $output;
	}
}
