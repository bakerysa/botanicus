<?php
/**
 * Custom functions that act in the footer.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Sober
 */

/**
 * Display Footer Newsletter
 *
 * @since 1.0.0
 */
function sober_footer_content() {
	if ( ! sober_get_option( 'footer_content_enable' ) || is_404() ) {
		return;
	}

	$content = sober_get_option( 'footer_content' );

	if ( empty( $content ) ) {
		return;
	}
	?>

	<div class="footer-content">
		<div class="sober-container">
			<?php echo do_shortcode( wp_kses_post( $content ) ); ?>
		</div>
	</div>

	<?php
}

add_action( 'sober_footer', 'sober_footer_content', 5 );

/**
 * Display widgets on footer
 */
function sober_footer_widgets() {
	if ( ! sober_get_option( 'footer_widgets' ) ) {
		return;
	}

	$layout = sober_get_option( 'footer_widgets_layout' );
	$colums = array(
		'2-columns'       => 2,
		'3-columns'       => 3,
		'4-columns'       => 4,
		'4-columns-equal' => 4,
	);
	?>

	<div class="footer-widgets widgets-area widgets-<?php echo esc_attr( $layout ) ?>">
		<div class="container">
			<div class="row">

				<?php
				for ( $i = 1; $i <= $colums[$layout]; $i++ ) {
					$column_class = 12 / $colums[$layout];

					if ( '4-columns' == $layout ) {
						$column_class = in_array( $i, array(1, 4) ) ? 4 : 2;
					}

					echo '<div class="footer-widgets-area-' . esc_attr( $i ) . ' footer-widgets-area col-xs-12 col-sm-6 col-md-' . esc_attr( $column_class ) . '">';

					dynamic_sidebar( 'footer-' . $i );

					echo '</div>';
				}
				?>

			</div>
		</div>
	</div>

	<?php
}

add_action( 'sober_footer', 'sober_footer_widgets', 6 );

/**
 * Display Instagram feed on footer
 */
function sober_footer_instagram() {
	if ( ! sober_get_option( 'footer_instagram' ) ) {
		return;
	}

	$username = sober_get_option( 'footer_instagram_user' );

	if ( empty( $username ) ) {
		return;
	}

	$media = sober_get_instagram_images( $username, 8 );

	if ( is_wp_error( $media ) ) {
		echo $media->get_error_message();
	} else {
		$current = 1;
		$limit = 8;
		?>

		<div class="footer-instagram">
			<div class="sober-container">
				<ul class="instagram-feed">

					<?php
					foreach ( $media as $item ) {
						if ( apply_filters( 'sober_footer_instagram_video', false ) && 'video' == $item['type'] ) {
							continue;
						}

						if ( $current > $limit ) {
							break;
						}

						$srcset = array(
							$item['thumbnail'] . ' 160w',
							$item['small'] . ' 320w',
							$item['large'] . ' 640w',
							$item['large'] . ' 2x',
						);
						$sizes = array(
							'(max-width: 1280px) 160px',
							'320px',
						);
						$image = sprintf(
							'<img src="%s" alt="%s" srcset="%s" sizes="%s">',
							esc_url( $item['small'] ),
							esc_attr( $item['description'] ),
							esc_attr( implode( ', ', $srcset ) ),
							esc_attr( implode( ', ', $sizes ) )
						);

						printf(
							'<li><a href="%s" target="_blank">%s</a></li>',
							esc_url( $item['link'] ),
							$image
						);

						$current++;
					}
					?>

				</ul>
			</div>
		</div>

		<?php
	}
}

add_action( 'sober_footer', 'sober_footer_instagram', 7 );

/**
 *  Display site footer
 */
function sober_footer_info() {
	$social_extra  = sober_get_option( 'footer_social_extra' );
	$wrapper       = sober_get_option( 'footer_wrapper' );
	$wrapper_class = 'wrapped' == $wrapper ? 'container' : 'sober-container';
	?>
	<div class="footer-info footer-<?php echo esc_attr( $wrapper ) ?>">
		<div class="<?php echo $wrapper_class; ?>">
			<div class="row">

				<div class="site-info <?php echo has_nav_menu( 'socials' ) || $social_extra ? 'col-md-6' : 'col-md-12' ?>">
					<div class="copyright">
						<?php echo do_shortcode( wp_kses_post( sober_get_option( 'footer_copyright' ) ) ); ?>
					</div><!-- .site-info -->
					<?php
					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu( array(
							'container'       => 'nav',
							'container_class' => 'footer-menu',
							'theme_location'  => 'footer',
							'menu_id'         => 'footer-menu',
							'depth'           => 1,
						) );
					}
					?>
				</div>

				<?php if ( has_nav_menu( 'socials' ) || $social_extra ) : ?>
					<div class="footer-social col-md-6">
						<?php
						if ( has_nav_menu( 'socials' ) ) {
							wp_nav_menu( array(
								'theme_location'  => 'socials',
								'container_class' => 'socials-menu ',
								'menu_id'         => 'footer-socials',
								'depth'           => 1,
								'link_before'     => '<span>',
								'link_after'      => '</span>',
							) );
						}

						if ( $social_extra ) {
							printf( '<div class="socials-extra">%s</div>', do_shortcode( wp_kses_post( $social_extra ) ) );
						}
						?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
	<?php
}

add_action( 'sober_footer', 'sober_footer_info' );

/**
 * Footer bottom content
 *
 * @since 1.1.5
 */
function sober_footer_bottom() {
	if ( ! ( $footer_bottom_content = sober_get_option( 'footer_bottom_content' ) ) ) {
		return;
	}

	printf(
		'<div class="footer-bottom text-%s"><div class="sober-container">%s</div></div>',
		esc_attr( sober_get_option( 'footer_bottom_content_align' ) ),
		do_shortcode( wp_kses_post( $footer_bottom_content ) )
	);
}

add_action( 'sober_footer', 'sober_footer_bottom', 30 );

/**
 * Add this back-to-top button to footer
 */
function sober_gotop_button() {
	if ( sober_get_option( 'footer_gotop' ) ) {
		echo '<a href="#page" id="gotop"><svg viewBox="0 0 20 20"><use xlink:href="#backtotop-arrow"></use></svg></a>';
	}
}

add_action( 'wp_footer', 'sober_gotop_button' );


if ( ! function_exists( 'sober_search_modal' ) ) :
	/**
	 * Add search modal to footer
	 */
	function sober_search_modal() {
		$type = sober_get_option( 'header_search_content' );
		$cats = sober_get_option( 'header_search_' . $type . '_cats' );
		?>
		<div id="search-modal" class="search-modal sober-modal" tabindex="-1" role="dialog">
			<div class="modal-header">
				<a href="#" class="close-modal">
					<svg viewBox="0 0 20 20">
						<use xlink:href="#close-delete"></use>
					</svg>
				</a>

				<h2><?php esc_html_e( 'Search', 'sober' ); ?></h2>
			</div>

			<div class="modal-content">
				<div class="container">
					<form method="get" class="instance-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php
						if ( '0' !== $cats ) {
							$taxonomy = 'product' == $type ? 'product_cat' : 'category';
							$cats_args = array(
								'taxonomy' => $taxonomy,
								'parent'   => sober_get_option( 'header_search_' . $type . '_cats_top' ) ? 0 : '',
								'orderby'  => 'count',
								'order'    => 'DESC',
							);

							if ( is_numeric( $cats ) ) {
								$cats_args['number'] = $cats;
							} elseif ( ! empty( $cats ) ) {
								$cats_args['name'] = explode( ',', $cats );
								$cats_args['orderby'] = 'include';
								unset( $cats_args['parent'] );
							}

							$categories = get_terms( $cats_args );

							if ( $categories && ! is_wp_error( $categories ) ) :
							?>

								<div class="product-cats">
									<label>
										<input type="radio" name="<?php echo $taxonomy ?>" value="" checked="checked">
										<span class="line-hover"><?php esc_html_e( 'All Categories', 'sober' ) ?></span>
									</label>

									<?php foreach ( $categories as $category ) : ?>
										<label>
											<input type="radio" name="<?php echo $taxonomy ?>" value="<?php echo esc_attr( $category->slug ); ?>">
											<span class="line-hover"><?php echo esc_html( $category->name ); ?></span>
										</label>
									<?php endforeach; ?>
								</div>

							<?php
							endif;
						}
						?>

						<div class="search-fields">
							<button type="submit" class="search-submit">
								<svg viewBox="0 0 20 20">
									<use xlink:href="#search"></use>
								</svg>
							</button>

							<input type="text" name="s" placeholder="<?php esc_attr_e( 'Search', 'sober' ); ?>" class="search-field" autocomplete="off">
							<input type="hidden" name="post_type" value="<?php echo esc_attr( $type ) ?>">

							<button type="reset" class="search-reset">
								<svg viewBox="0 0 14 14">
									<use xlink:href="#close-delete-small"></use>
								</svg>
							</button>
						</div>
					</form>

					<div class="search-results">
						<div class="text-center loading">
							<i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw margin-bottom"></i></div>
						<div class="results-container"></div>
						<div class="view-more-results text-center">
							<a href="" class="button search-results-button"><?php esc_html_e( 'View more', 'sober' ) ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

endif;

add_action( 'wp_footer', 'sober_search_modal' );

if ( ! function_exists( 'sober_login_modal' ) ) :
	/**
	 * Add login modal to footer
	 */
	function sober_login_modal() {
		if ( ! shortcode_exists( 'woocommerce_my_account' ) ) {
			return;
		}

		if ( is_user_logged_in() ) {
			return;
		}
		?>

		<div id="login-modal" class="login-modal sober-modal woocommerce-account" tabindex="-1" role="dialog">
			<div class="modal-header">
				<a href="#" class="close-modal">
					<svg viewBox="0 0 20 20">
						<use xlink:href="#close-delete"></use>
					</svg>
				</a>

				<h2><?php esc_html_e( 'My Account', 'sober' ); ?></h2>
			</div>

			<div class="modal-content">
				<div class="container">
					<?php echo do_shortcode( '[woocommerce_my_account]' ) ?>
				</div>
			</div>
		</div>

		<?php
	}
endif;

add_action( 'wp_footer', 'sober_login_modal' );

if ( ! function_exists( 'sober_cart_modal' ) ) :
	/**
	 * Add cart modal to footer
	 */
	function sober_cart_modal() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}
		?>

		<div id="cart-modal" class="cart-modal sober-modal woocommerce" tabindex="-1" role="dialog">
			<div class="modal-header">
				<a href="#" class="close-modal">
					<svg viewBox="0 0 20 20">
						<use xlink:href="#close-delete"></use>
					</svg>
				</a>

				<h2><?php esc_html_e( 'Cart', 'sober' ); ?></h2>
			</div>

			<div class="modal-content">
				<div class="container">

					<div class="cart-tabs-nav tabs-nav text-center">
						<span class="line-hover tab-nav active" data-tab="cart"><?php esc_html_e( 'Shopping Cart', 'sober' ) ?>
							<span class="count cart-counter"><?php echo WC()->cart->get_cart_contents_count() ?></span>
						</span>

						<?php if ( function_exists( 'Soo_Wishlist' ) ) : ?>
							<span class="line-hover tab-nav" data-tab="wishlist">
								<?php esc_html_e( 'Wishlist', 'sober' ) ?>
								<span class="count wishlist-counter"><?php echo soow_count_products() ?></span>
							</span>
						<?php elseif ( defined( 'YITH_WCWL' ) && YITH_WCWL ) : ?>
							<span class="line-hover tab-nav" data-tab="wishlist">
								<?php esc_html_e( 'Wishlist', 'sober' ) ?>
								<span class="count wishlist-counter"><?php echo yith_wcwl_count_products() ?></span>
							</span>
						<?php endif; ?>
					</div>
					<div class="tab-panels">
						<div class="tab-panel woocommerce active" data-tab="cart">
							<div class="widget_shopping_cart_content">
								<?php woocommerce_mini_cart(); ?>
							</div>
						</div>
						<div class="tab-panel woocommerce-wishlist" data-tab="wishlist">
							<?php
							if ( shortcode_exists( 'soo_wishlist' ) ) {
								echo do_shortcode( '[soo_wishlist]' );
							} elseif ( shortcode_exists( 'yith_wcwl_wishlist' ) ) {
								echo do_shortcode( '[yith_wcwl_wishlist]' );
							}
							?>
						</div>
					</div>

				</div>
			</div>
		</div>

		<?php
	}
endif;

add_action( 'wp_footer', 'sober_cart_modal' );

if ( ! function_exists( 'sober_quick_view_modal' ) ) :
	/**
	 * Adds quick view modal to footer
	 */
	function sober_quick_view_modal() {
		if ( ! sober_get_option( 'product_quickview' ) ) {
			return;
		}
		?>

		<div id="quick-view-modal" class="quick-view-modal sober-modal woocommerce" tabindex="-1" role="dialog">
			<div class="modal-header">
				<a href="#" class="close-modal">
					<svg viewBox="0 0 20 20">
						<use xlink:href="#close-delete"></use>
					</svg>
				</a>

				<h2><?php esc_html_e( 'Product Quick View', 'sober' ); ?></h2>
			</div>

			<div class="modal-content">
				<div class="container">

					<div class="sober-modal-backdrop"></div>
					<div class="product"></div>

				</div>
			</div>

			<div class="sober-modal-backdrop"></div>
			<div class="loader"></div>
		</div>

		<?php
	}
endif;

add_action( 'wp_footer', 'sober_quick_view_modal' );

/**
 * Adds photoSwipe dialog element
 */
function sober_product_images_lightbox() {
	if ( ! get_option( 'woocommerce_enable_lightbox' ) || ! is_singular( array( 'product' ) ) || version_compare( WC()->version, '3.0.0', '>=' ) ) {
		return;
	}

	?>
	<div id="pswp" class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

		<div class="pswp__bg"></div>

		<div class="pswp__scroll-wrap">

			<div class="pswp__container">
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
			</div>

			<div class="pswp__ui pswp__ui--hidden">

				<div class="pswp__top-bar">


					<div class="pswp__counter"></div>

					<button class="pswp__button pswp__button--close" title="<?php esc_attr_e( 'Close (Esc)', 'sober' ) ?>"></button>

					<button class="pswp__button pswp__button--share" title="<?php esc_attr_e( 'Share', 'sober' ) ?>"></button>

					<button class="pswp__button pswp__button--fs" title="<?php esc_attr_e( 'Toggle fullscreen', 'sober' ) ?>"></button>

					<button class="pswp__button pswp__button--zoom" title="<?php esc_attr_e( 'Zoom in/out', 'sober' ) ?>"></button>

					<div class="pswp__preloader">
						<div class="pswp__preloader__icn">
							<div class="pswp__preloader__cut">
								<div class="pswp__preloader__donut"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
					<div class="pswp__share-tooltip"></div>
				</div>

				<button class="pswp__button pswp__button--arrow--left" title="<?php esc_attr_e( 'Previous (arrow left)', 'sober' ) ?>"></button>

				<button class="pswp__button pswp__button--arrow--right" title="<?php esc_attr_e( 'Next (arrow right)', 'sober' ) ?>"></button>

				<div class="pswp__caption">
					<div class="pswp__caption__center"></div>
				</div>

			</div>

		</div>

	</div>
	<?php
}

add_action( 'wp_footer', 'sober_product_images_lightbox' );

/**
 * Prepare mobile nav at the footer
 */
function sober_mobile_nav() {
	$menu        = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
	$menu_top    = sober_get_option( 'mobile_menu_top' );
	$menu_bottom = sober_get_option( 'mobile_menu_bottom' );
	$css_class   = array( 'side-menu', 'mobile-menu' );

	if ( ! empty( $menu_top ) ) {
		$css_class[] = 'has-top-content';
	}

	if ( ! empty( $menu_bottom ) ) {
		$css_class[] = 'has-bottom-content';
	}
	?>

	<div class="side-menu-backdrop"></div>

	<div id="mobile-menu" class="<?php echo esc_attr( implode( ' ', $css_class ) ) ?>">
		<div class="mobile-menu-inner">
			<?php if ( sober_get_option( 'mobile_menu_close' ) ) : ?>
				<span class="toggle-nav active" data-target="mobile-menu"><span class="icon-nav"></span></span>
			<?php endif; ?>

			<?php if ( ! empty( $menu_top ) ) : ?>

				<div class="mobile-menu-top clearfix">
					<?php
					foreach ( (array) $menu_top as $item ) {
						switch ( $item ) {
							case 'currency':
								sober_currency_switcher();
								break;

							case 'language':
								sober_language_switcher();
								break;
						}
					}
					?>
				</div>

			<?php endif; ?>

			<?php if ( sober_get_option( 'mobile_menu_search' ) ) : ?>
				<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ) ?>">
					<label>
						<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'sober' ) ?></span>
						<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search', 'sober' ) ?>" value="<?php get_search_query() ?>" name="s" />
					</label>
					<?php if ( 'all' !== sober_get_option( 'mobile_menu_search_content' ) ) : ?>
						<input type="hidden" name="post_type" value="<?php echo esc_attr( sober_get_option( 'mobile_menu_search_content' ) ) ?>">
					<?php endif; ?>
					<button type="submit" class="search-submit">
						<span class="screen-reader-text"><?php esc_html_e( 'Search', 'sober' ) ?></span>
						<svg width="20" height="20">
							<use xlink:href="#search"></use>
						</svg>
					</button>
				</form>
			<?php endif; ?>

			<nav id="mobile-nav" class="mobile-nav">
				<?php wp_nav_menu( array( 'theme_location' => $menu, 'container' => false, 'after' => '<button class="toggle"><span class="caret"></span></button>' ) ); ?>
			</nav>

			<?php if ( ! empty( $menu_bottom ) && function_exists( 'WC' ) ) : ?>

				<div class="mobile-menu-bottom">
					<ul>
						<?php
						foreach ( (array) $menu_bottom as $item ) {
							switch ( $item ) {
								case 'cart':
									printf(
										'<li class="item-cart"><a href="%s">
											%s
											<span>%s</span>
											<span class="count cart-counter">%s</span>
										</a></li>',
										esc_url( wc_get_cart_url() ),
										sober_shopping_cart_icon( false ),
										esc_html__( 'Shopping Cart', 'sober' ),
										intval( WC()->cart->get_cart_contents_count() )
									);
									break;

								case 'login':
									printf(
										'<li class="item-login"><a href="%s" data-toggle="%s" data-target="login-modal">
											<svg viewBox="0 0 20 20"><use xlink:href="#user-account-people"></use></svg>
											<span>%s</span>
										</a></li>',
										esc_url( wc_get_account_endpoint_url( 'dashboard' ) ),
										is_user_logged_in() ? 'link' : 'modal',
										is_user_logged_in() ? esc_html__( 'My Account', 'sober' ) : esc_html__( 'Login', 'sober' )
									);
									break;
							}
						}
						?>
					</ul>
				</div>

			<?php endif; ?>
		</div>
	</div>

	<?php
}

add_action( 'wp_footer', 'sober_mobile_nav' );

/**
 * Place primary menu on footer for header v6
 */
function sober_side_nav() {
	if ( 'v6' !== sober_get_option( 'header_layout' ) ) {
		return;
	}

	?>
	<nav id="primary-menu" class="side-menu primary-menu">
		<span class="toggle-nav active" data-target="primary-menu"><span class="icon-nav"></span></span>

		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'after' => '<button class="toggle"><span class="caret"></span></button>' ) ); ?>
	</nav>
	<?php
}

add_action( 'wp_footer', 'sober_side_nav' );

/**
 * Add the popup HTML to footer
 *
 * @since 2.0
 */
function sober_popup() {
	if ( ! sober_get_option( 'popup' ) ) {
		return;
	}

	$popup_frequency = intval( sober_get_option( 'popup_frequency' ) );

	if ( $popup_frequency > 0 && isset( $_COOKIE['sober_popup'] ) ) {
		return;
	}

	$popup_layout = sober_get_option( 'popup_layout' );

	get_template_part( 'template-parts/popup', $popup_layout );
}

add_action( 'wp_footer', 'sober_popup' );

/**
 * Adds preloader container at the bottom of the site
 */
function sober_preloader() {
	if ( ! sober_get_option( 'preloader' ) ) {
		return;
	}

	get_template_part( 'template-parts/preloader' );
}

add_action( 'sober_after_footer', 'sober_preloader' );