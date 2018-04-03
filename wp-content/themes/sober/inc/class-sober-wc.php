<?php
/**
 * Customize WooCommerce templates
 *
 * @package Sober
 */

/**
 * Class for all WooCommerce template modification
 *
 * @version 1.0
 */
class Sober_WooCommerce {
	/**
	 * The single instance of the class
	 *
	 * @var Sober_WooCommerce
	 */
	protected static $instance = null;

	/**
	 * Number of days to keep set a product as a new one
	 * @var int
	 */
	protected $new_duration;

	/**
	 * Main instance
	 *
	 * @return Sober_WooCommerce
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construction function
	 */
	public function __construct() {
		// Check if Woocomerce plugin is actived
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}

		$this->new_duration = sober_get_option( 'product_newness' );

		$this->parse_query();
		$this->hooks();
	}

	/**
	 * Parse request to change the shop columns and products per page
	 */
	public function parse_query() {
		if ( isset( $_GET['shop_columns'] ) && in_array( intval( $_GET['shop_columns'] ), array( 4, 5, 6 ) ) ) {
			wc_setcookie( 'product_columns', intval( $_GET['shop_columns'] ), 6 * 60 * 24 * 30 );
			WC()->session->set( 'product_columns', intval( $_GET['shop_columns'] ) );
		}
	}

	/**
	 * Hooks to WooCommerce actions, filters
	 *
	 * @since  1.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'pre_get_posts', array( $this, 'get_products_by_group' ) );

		// Need an early hook to ajaxify update mini shop cart
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragments' ) );

		// Update wishlist fragment
		add_filter( 'add_to_wishlist_fragments', array( $this, 'add_to_wishlist_fragments' ) );

		// Disable redirect to product page while having only one search result
		add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

		// WooCommerce Styles
		add_filter( 'woocommerce_enqueue_styles', array( $this, 'wc_styles' ) );

		// Change message content
		add_filter( 'wc_add_to_cart_message_html', array( $this, 'add_to_cart_message' ) );

		// Add Bootstrap classes
		add_filter( 'post_class', array( $this, 'product_class' ), 50, 3 );
		add_filter( 'product_cat_class', array( $this, 'product_cat_class' ), 50 );

		// Change shop columns
		add_filter( 'loop_shop_columns', array( $this, 'shop_columns' ), 20 );
		add_filter( 'loop_shop_per_page', array( $this, 'products_per_page' ), 20 );

		// Add badges
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_ribbons' ), 15 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'product_ribbons' ), 7 );

		// Wrap product thumbnail
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'open_loop_thumbnail_wrapper' ), 5 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'close_loop_thumbnail_wrapper' ), 30 );

		// Change product link position
		if ( 'slider' == sober_get_option( 'products_item_style' ) ) {
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'product_loop_thumbnail_images' ), 7 );
		} else {
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'product_loop_link_open' ), 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 20 );
		}

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'show_product_loop_buttons' ), 25 );

		// Adds hovered thumbnail to loop product
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'show_product_loop_hover_thumbnail' ) );

		// Add link to product title in shop loop
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'show_product_loop_title' ) );

		// Remove stars in shop loop
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

		// Change next and prev icon
		add_filter( 'woocommerce_pagination_args', array( $this, 'pagination_args' ) );

		// Add toolbars for shop page
		add_filter( 'woocommerce_show_page_title', '__return_false' );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'shop_toolbar' ) );

		// Remove breadcrumb, use theme's instead
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'breadcrumb_args' ) );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		if ( sober_get_option( 'product_hide_outstock_price' ) ) {
			add_filter( 'woocommerce_get_price_html', array( $this, 'price_html' ), 10, 2 );
		}

		/**
		 * Single products hooks
		 */
		if ( is_singular( 'product' ) ) {
			add_action( 'woocommerce_before_main_content', array( $this, 'product_breadcrumb' ), 20 );
		}

		// Adds breadcrumb and product navigation on top of product
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'product_toolbar' ), 5 );

		// Wrap images and summary into a div
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'open_product_summary' ), 5 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'close_product_summary' ), 5 );

		// Remove thumbnails of product style 1 & 4
		if ( in_array( sober_get_option( 'single_product_style' ), array( 'style-1', 'style-4' ) ) ) {
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
		}

		// Change thumbnail size
		add_filter( 'single_product_small_thumbnail_size', array( $this, 'small_thumbnail_size' ) );

		// Wrap product summary for sticky description on product style 1
		if ( 'style-1' == sober_get_option( 'single_product_style' ) ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'open_sticky_description' ), 0 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'close_sticky_description' ), 1000 );
		}

		// Reorder stars
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 15 );

		// Reorder description
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 10 );

		// Reorder price
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );

		// Add to wishlist button
		remove_action( 'woocommerce_after_add_to_cart_button', array(
			'Soo_Wishlist_Frontend',
			'single_product_button',
		) );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'single_product_wishlist_button' ) );
		add_filter( 'woocommerce_reset_variations_link', array( $this, 'reset_variations_link' ) );

		// Product share
		add_action( 'woocommerce_share', array( $this, 'share' ) );

		// Product extra content
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_extra_content' ), 200 );

		// Change product tabs title
		add_filter( 'woocommerce_product_tabs', array( $this, 'product_tabs' ), 50 );

		// Remove tab heading
		add_filter( 'woocommerce_product_additional_information_heading', '__return_false' );
		add_filter( 'woocommerce_product_description_heading', '__return_false' );

		// Related and upsells columns
		// add_filter( 'woocommerce_related_products_columns', array( $this, 'related_products_columns' ), 20 );
		// add_filter( 'woocommerce_up_sells_columns', array( $this, 'related_products_columns' ), 20 );

		add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
		add_filter( 'woocommerce_upsell_display_args', array( $this, 'upsell_products_args' ) );

		if ( 'style-3' == sober_get_option( 'single_product_style' ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display' );
		}

		add_filter( 'woocommerce_dropdown_variation_attribute_options_args', array( $this, 'dropdown_variation_options_args' ) );

		/**
		 * Cart page
		 */
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
		add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sell_columns' ) );

		// Add billing title
		add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'billing_title' ) );

		/**
		 * Cart widget
		 */
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );

		add_action( 'woocommerce_widget_shopping_cart_buttons', array( $this, 'widget_shopping_cart_button_checkout' ), 10 );
		add_action( 'woocommerce_widget_shopping_cart_buttons', array( $this, 'widget_shopping_cart_button_view_cart' ), 20 );
	}

	/**
	 * Remove default woocommerce styles
	 *
	 * @since  1.0
	 *
	 * @param  array $styles
	 *
	 * @return array
	 */
	public function wc_styles( $styles ) {
		// unset( $styles['woocommerce-general'] );
		unset( $styles['woocommerce-layout'] );
		unset( $styles['woocommerce-smallscreen'] );

		return $styles;
	}

	/**
	 * Change the main query to get products by group
	 *
	 * @param object $query
	 */
	public function get_products_by_group( $query ) {
		if ( is_admin() || empty( $_GET['product_group'] ) || ! is_woocommerce() || ! $query->is_main_query() ) {
			return;
		}

		switch ( $_GET['product_group'] ) {
			case 'featured':
				if( version_compare( WC()->version, '3.0.0', '<' ) ) {
					$meta_query = WC()->query->get_meta_query();
					$meta_query[] = array(
						'key'   => '_featured',
						'value' => 'yes',
					);
					$query->set( 'meta_query', $meta_query );
				} else {
					$tax_query = WC()->query->get_tax_query();
					$tax_query[] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
						'operator' => 'IN',
					);
					$query->set( 'tax_query', $tax_query );
				}
				break;

			case 'sale':
				$query->set( 'post__in', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) );
				break;

			case 'new':
				$newness = intval( sober_get_option( 'product_newness' ) );

				if ( $newness > 0 ) {
					$query->set( 'date_query', array(
						'after' => date( 'Y-m-d', strtotime( '-' . $newness . ' days' ) )
					) );
				} else {
					$meta_query = WC()->query->get_meta_query();
					$meta_query[] = array(
						'key'   => '_is_new',
						'value' => 'yes',
					);
					$query->set( 'meta_query', $meta_query );
				}
				break;
		}
	}

	/**
	 * Move the button to the end of message
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	public function add_to_cart_message( $message ) {
		if ( preg_match( '/(<a\b[^>]*>(.*?)<\/a>)/i', $message, $matches ) ) {
			$message = preg_replace( '/<a\b[^>]*>(.*?)<\/a>/i', '', $message );
			$message .= $matches[0];
		}

		return $message;
	}

	/**
	 * Ajaxify update cart viewer
	 *
	 * @since 1.0
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	public function add_to_cart_fragments( $fragments ) {
		$fragments['span.cart-counter'] = '<span class="count cart-counter">' . WC()->cart->get_cart_contents_count() . '</span>';

		return $fragments;
	}

	/**
	 * Ajaxify update wishlist viewer
	 *
	 * @param $fragments
	 *
	 * @return mixed
	 */
	public function add_to_wishlist_fragments( $fragments ) {
		if ( ! function_exists( 'Soo_Wishlist' ) ) {
			return $fragments;
		}

		$fragments['span.wishlist-counter'] = '<span class="count wishlist-counter">' . soow_count_products() . '</span>';

		$fragments['.sober-modal div.soo-wishlist'] = do_shortcode( '[soo_wishlist]' );

		return $fragments;
	}

	/**
	 * Change the shop columns
	 *
	 * @since  1.0.0
	 *
	 * @param  int $columns The default columns
	 *
	 * @return int
	 */
	public function shop_columns( $columns ) {
		if ( is_woocommerce() ) {
			$columns = ! is_null( WC()->session->get( 'product_columns' ) ) ? intval( WC()->session->get( 'product_columns' ) ) : intval( sober_get_option( 'product_columns' ) );
		}

		return $columns;
	}

	/**
	 * Change number of products per page
	 *
	 * @param int $limit
	 *
	 * @return int
	 */
	public function products_per_page( $limit ) {
		if ( is_woocommerce() ) {
			if ( ! is_null( WC()->session->get( 'product_columns' ) ) ) {
				$limit = 3 * intval( WC()->session->get( 'product_columns' ) );
			} else {
				$limit = intval( sober_get_option( 'products_per_page' ) );
			}
		}

		return $limit;
	}

	/**
	 * Change next and previous icon of pagination nav
	 *
	 * @since  1.0
	 */
	public function pagination_args( $args ) {
		$args['prev_text'] = '<svg viewBox="0 0 20 20"><use xlink:href="#left-arrow"></use></svg>';
		$args['next_text'] = '<svg viewBox="0 0 20 20"><use xlink:href="#right-arrow"></use></svg>';

		if ( sober_get_option( 'shop_nav_type' ) != 'links' ) {
			$loading           = '<span class="loading-icon"><span class="bubble"><span class="dot"></span></span><span class="bubble"><span class="dot"></span></span><span class="bubble"><span class="dot"></span></span></span>';
			$args['prev_text'] = '';
			$args['next_text'] = '<span class="button-text">' . esc_html__( 'Load More', 'sober' ) . '</span>' . $loading;
		}

		return $args;
	}

	/**
	 * Display a tool bar on top of product archive
	 *
	 * @since 1.0
	 */
	public function shop_toolbar() {
		if ( ! sober_get_option( 'shop_toolbar' ) ) {
			return;
		}

		$columns = ! is_null( WC()->session->get( 'product_columns' ) ) ? intval( WC()->session->get( 'product_columns' ) ) : intval( sober_get_option( 'product_columns' ) );
		$toggle  = sober_get_option( 'products_toggle' );
		$sort    = sober_get_option( 'products_sorting' );
		$display = ( sober_get_option( 'products_toggle_taxonomy_keep' ) && is_product_taxonomy() ) || is_shop();

		if ( $toggle ) {
			$type = sober_get_option( 'products_toggle_type' );
			$tabs = array();

			$tabs[] = sprintf(
				'<li data-filter="*" class="line-hover active"><a href="%s">%s</a></li>',
				esc_url( remove_query_arg( array( 'product_group' ) ) ),
				esc_html__( 'All Products', 'sober' )
			);

			if ( 'tag' == $type && $display ) {
				$tags = trim( sober_get_option( 'products_toggle_tags' ) );
				$tags = get_terms( array(
					'taxonomy' => 'product_tag',
					'orderby'  => 'include',
					'name'     => explode( ',', $tags ),
				) );

				if ( $tags ) {
					foreach ( $tags as $tag ) {
						$tabs[] = sprintf(
							'<li data-filter=".product_tag-%s" class="line-hover"><a href="%s">%s</a></li>',
							esc_attr( $tag->slug ),
							esc_url( get_term_link( $tag ) ),
							esc_html( $tag->name )
						);
					}
				}
			} elseif ( 'category' == $type && $display ) {
				$amount = sober_get_option( 'products_toggle_category_amount' );
				$args = array(
					'taxonomy' => 'product_cat',
					'orderby'  => 'count',
					'order'    => 'DESC',
					'parent'   => 0,
				);

				if ( is_numeric( $amount ) ) {
					$args['number'] = $amount;
					$args['parent'] = '';
				} elseif ( ! empty( $amount ) ) {
					$args['name'] = explode( ',', $amount );
					$args['orderby'] = 'include';
					$args['parent'] = '';
				}

				$categories = get_terms( $args );

				if ( $categories && ! is_wp_error( $categories ) ) {
					foreach ( $categories as $category ) {
						$tabs[] = sprintf(
							'<li data-filter=".product_cat-%s" class="line-hover"><a href="%s">%s</a></li>',
							esc_attr( $category->slug ),
							esc_url( get_term_link( $category ) ),
							esc_html( $category->name )
						);
					}
				}
			} elseif ( 'group' == $type || ! $display ) {
				$groups   = (array) sober_get_option( 'products_toggle_groups' );
				$type     = 'group';
				$base_url = '';

				if ( is_shop() ) {
					$base_url = wc_get_page_permalink( 'shop' );
				} elseif ( is_product_taxonomy() ) {
					$term = get_queried_object();
					$base_url = get_term_link( $term );
				}

				if ( ! empty( $_GET['product_group'] ) ) {
					$tabs = array();
					$tabs[] = sprintf(
						'<li data-filter="*" class="line-hover"><a href="%s">%s</a></li>',
						$base_url ? esc_url( $base_url ) : esc_url( remove_query_arg( 'product_group' ) ),
						esc_html__( 'All Products', 'sober' )
					);
				}

				if ( in_array( 'featured', $groups ) ) {
					$tabs[] = sprintf(
						'<li data-filter=".featured" class="line-hover %s"><a href="%s">%s</a></li>',
						isset( $_GET['product_group'] ) && 'featured' == $_GET['product_group'] ? 'active' : '',
						esc_url( add_query_arg( array( 'product_group' => 'featured' ), $base_url ) ),
						esc_html__( 'Hot Products', 'sober' )
					);
				}

				if ( in_array( 'new', $groups ) ) {
					$tabs[] = sprintf(
						'<li data-filter=".new" class="line-hover %s"><a href="%s">%s</a></li>',
						isset( $_GET['product_group'] ) && 'new' == $_GET['product_group'] ? 'active' : '',
						esc_url( add_query_arg( array( 'product_group' => 'new' ), $base_url ) ),
						esc_html__( 'New Products', 'sober' )
					);
				}

				if ( in_array( 'sale', $groups ) ) {
					$tabs[] = sprintf(
						'<li data-filter=".sale" class="line-hover %s"><a href="%s">%s</a></li>',
						isset( $_GET['product_group'] ) && 'sale' == $_GET['product_group'] ? 'active' : '',
						esc_url( add_query_arg( array( 'product_group' => 'sale' ), $base_url ) ),
						esc_html__( 'Sales Products', 'sober' )
					);
				}
			}
		}
		?>

		<div class="shop-toolbar">
			<div class="row">
				<div class="col-sm-9 col-md-7 hidden-xs nav-filter">
					<?php if ( $toggle ) : ?>
						<ul class="products-filter filter-by-<?php echo esc_attr( $type ) ?> clearfix">
							<?php echo implode( "\n", $tabs ) ?>
						</ul>
					<?php else : ?>
						<?php
						if ( $sort ) {
							woocommerce_catalog_ordering();
						}
						woocommerce_result_count();
						?>
					<?php endif; ?>
				</div>

				<div class="col-xs-12 col-sm-3 col-md-5 controls">
					<ul class="toolbar-control">
						<?php if ( $toggle ) : ?>
							<li class="data totals">
								<?php
								if ( $sort ) {
									woocommerce_catalog_ordering();
								} else {
									woocommerce_result_count();
								}
								?>
							</li>
						<?php endif; ?>
						<li class="data product-size">
							<a href="<?php echo esc_url( add_query_arg( array( 'shop_columns' => 6 ) ) ) ?>" class="small-size <?php echo 6 == $columns ? 'active' : '' ?>">
								<svg viewBox="0 0 15 15">
									<use xlink:href="#small-view-size"></use>
								</svg>
							</a>
							<a href="<?php echo esc_url( add_query_arg( array( 'shop_columns' => 5 ) ) ) ?>" class="medium-size <?php echo 5 == $columns ? 'active' : '' ?>">
								<svg viewBox="0 0 15 15">
									<use xlink:href="#medium-view-size"></use>
								</svg>
							</a>
							<a href="<?php echo esc_url( add_query_arg( array( 'shop_columns' => 4 ) ) ) ?>" class="large-size <?php echo 4 == $columns ? 'active' : '' ?>">
								<svg viewBox="0 0 15 15">
									<use xlink:href="#large-view-size"></use>
								</svg>
							</a>
						</li>
						<?php if ( is_active_sidebar( 'shop-filter' ) && sober_get_option( 'products_filter' ) ) : ?>
							<li class="data filter">
								<a href="#" class="toggle-filter">
									<svg viewBox="0 0 20 20">
										<use xlink:href="#filter"></use>
									</svg>
									<?php esc_html_e( 'Filter', 'sober' ) ?>
								</a>

								<div class="filter-widgets woocommerce">
									<button class="close">
										<svg viewBox="0 0 20 20">
											<use xlink:href="#close-delete"></use>
										</svg>
									</button>
									<?php dynamic_sidebar( 'shop-filter' ) ?>
								</div>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Filter function for breadcrumb args
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function breadcrumb_args( $args ) {
		$args['delimiter']   = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>';

		return $args;
	}

	/**
	 * Display product breadcrumb
	 */
	public function product_breadcrumb() {
		?>

		<div class="product-breadcrumb">
			<nav class="product-navigation post-navigation">
				<?php previous_post_link( '%link', '<svg viewBox="0 0 20 20"><use xlink:href="#left-arrow"></use></svg><span class="screen-reader-text">%title</span>' ) ?>
				<?php next_post_link( '%link', '<svg viewBox="0 0 20 20"><use xlink:href="#right-arrow"></use></svg><span class="screen-reader-text">%title</span>' ) ?>
			</nav>
			<?php woocommerce_breadcrumb() ?>
		</div>

		<?php
	}

	/**
	 * Add Bootstrap's column classes for product
	 *
	 * @since 1.0
	 *
	 * @param array  $classes
	 * @param string $class
	 * @param string $post_id
	 *
	 * @return array
	 */
	public function product_class( $classes, $class = '', $post_id = '' ) {
		global $woocommerce_loop, $product;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $classes;
		}

		// Add classes for product
		if (
			get_post_meta( $post_id, '_is_new', true ) ||
			( $this->new_duration && ( time() - ( 60 * 60 * 24 * $this->new_duration ) ) < strtotime( get_the_time( 'Y-m-d' ) ) )
		) {
			$classes[] = 'new';
		}

		// Add classes for products in archive page only
		if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ) ) ) {
			return $classes;
		}

		$classes[] = 'layout-' . sober_get_option( 'single_product_style' );

		if ( ! is_single( $post_id ) ) {
			$classes[] = 'col-md-4 col-sm-4 col-xs-6';

			if ( $woocommerce_loop['columns'] == 5 ) {
				$classes[] = 'col-lg-1-5';
			} elseif ( isset( $woocommerce_loop['columns'] ) ) {
				$classes[] = 'col-lg-' . ( 12 / $woocommerce_loop['columns'] );
			}

			$gallery_image_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();
			if ( ! empty( $gallery_image_ids ) ) {
				$classes[] = 'product-has-gallery';
			}

			// Adds a class of product style in grid
			$classes[] = 'product-style-' . sober_get_option( 'products_item_style' );
		}

		return $classes;
	}

	/**
	 * Add class for product category item
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function product_cat_class( $classes ) {
		global $woocommerce_loop, $product;

		$classes[] = 'col-md-4 col-sm-4 col-xs-6';

		if ( ! isset( $woocommerce_loop['columns'] ) ) {
			$classes[] = 'col-lg-1-5';

			return $classes;
		}

		if ( $woocommerce_loop['columns'] == 5 ) {
			$classes[] = 'col-lg-1-5';
		} else {
			$classes[] = 'col-lg-' . ( 12 / $woocommerce_loop['columns'] );
		}

		return $classes;
	}

	/**
	 * Display badge for new product or featured product
	 *
	 * @since 1.0
	 */
	public function product_ribbons() {
		global $product;

		$output = array();

		if ( $product->is_on_sale() ) {
			$percentage = 0;

			if ( $product->get_type() == 'variable' ) {
				$available_variations = $product->get_available_variations();
				$max_percentage       = 0;

				for ( $i = 0; $i < count( $available_variations ); $i ++ ) {
					$variation_id        = $available_variations[ $i ]['variation_id'];
					$variable_product    = new WC_Product_Variation( $variation_id );
					$regular_price       = $variable_product->get_regular_price();
					$sales_price         = $variable_product->get_sale_price();
					$variable_percentage = $regular_price && $sales_price ? round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) ) : 0;

					if ( $variable_percentage > $max_percentage ) {
						$max_percentage = $variable_percentage;
					}
				}

				$percentage = $max_percentage ? $max_percentage : $percentage;
			} elseif ( $product->get_regular_price() != 0 ) {
				$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
			}

			if ( $percentage ) {
				$off      = sober_get_option( 'product_onsale_off' ) ? ' ' . esc_html__( 'OFF', 'sober' ) : '';
				$output[] = '<span class="onsale ribbon">' . $percentage . '%' . $off . '</span>';
			}
		}

		if ( $product->is_featured() ) {
			$output[] = '<span class="featured ribbon">' . esc_html__( 'Hot', 'sober' ) . '</span>';
		}

		if (
			get_post_meta( $product->get_id(), '_is_new', true ) ||
			( ( time() - ( 60 * 60 * 24 * $this->new_duration ) ) < strtotime( get_the_time( 'Y-m-d' ) ) )
		) {
			$output[] = '<span class="newness ribbon">' . esc_html__( 'New', 'sober' ) . '</span>';
		}

		if ( sober_get_option( 'product_sold_out_ribbon' ) && ! $product->is_in_stock() ) {
			$output[] = '<span class="sold-out ribbon">' . esc_html__( 'Sold Out', 'sober' ) . '</span>';
		}

		if ( $output ) {
			printf( '<span class="ribbons">%s</span>', implode( '', $output ) );
		}
	}

	/**
	 * Open product thumbnail wrapper
	 */
	public function open_loop_thumbnail_wrapper() {
		echo '<div class="product-header">';
	}

	/**
	 * Close product thumbnail wrapper
	 */
	public function close_loop_thumbnail_wrapper() {
		echo '</div>';
	}

	public function product_loop_thumbnail_images() {
		global $product;

		$image_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();

		if ( $image_ids ) {
			echo '<div class="product-images__slider owl-carousel owl-theme">';
		}

		woocommerce_template_loop_product_link_open();
		woocommerce_template_loop_product_thumbnail();
		woocommerce_template_loop_product_link_close();

		foreach ( $image_ids as $image_id ) {
			$src = wp_get_attachment_image_src( $image_id, 'shop_catalog' );

			if ( ! $src ) {
				continue;
			}

			woocommerce_template_loop_product_link_open();

			printf(
				'<img data-src="%s" width="%s" height="%s" class="owl-lazy" alt="%s">',
				esc_url( $src[0] ),
				esc_attr( $src[1] ),
				esc_attr( $src[2] ),
				esc_attr( $product->get_title() )
			);

			woocommerce_template_loop_product_link_close();
		}

		if ( $image_ids ) {
			echo '</div>';
		}
	}

	/**
	 * Open product link on the shop page
	 * Adds more attributes zooming
	 */
	public function product_loop_link_open() {
		if ( 'zoom' == sober_get_option( 'products_item_style' ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

			if ( $image ) {
				echo '<a href="' . get_the_permalink() . '" class="woocommerce-LoopProduct-link product-thumbnail-zoom" data-zoom_image="' . $image[0] . '">';
			} else {
				woocommerce_template_loop_product_link_open();
			}
		} else {
			woocommerce_template_loop_product_link_open();
		}
	}

	/**
	 * Add billing title
	 */
	public function billing_title() {
		if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

			<h3><?php esc_html_e( 'Billing &amp; Shipping', 'sober' ); ?></h3>

		<?php else : ?>

			<h3><?php esc_html_e( 'Billing Details', 'sober' ); ?></h3>

		<?php endif;
	}

	/**
	 * Add hover image for a product on catalog page
	 */
	public function show_product_loop_hover_thumbnail() {
		global $product;

		if ( ! sober_get_option( 'product_hover_thumbnail' ) ) {
			return;
		}

		if ( in_array( sober_get_option( 'products_item_style' ), array( 'slider', 'zoom' ) ) ) {
			return;
		}

		$image_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();

		if ( empty( $image_ids ) ) {
			return;
		}

		echo wp_get_attachment_image( $image_ids[0], 'shop_catalog', false, array( 'class' => 'attachment-shop_catalog size-shop_catalog product-hover-image' ) );
	}

	/**
	 * Show product buttons inside the .product-header div
	 * This contains add_to_cart and wishlist buttons
	 */
	public function show_product_loop_buttons() {
		$style              = sober_get_option( 'products_item_style' );
		$quickview          = sober_get_option( 'product_quickview' );
		$quickview_behavior = sober_get_option( 'product_quickview_behavior' );
		?>

		<?php if ( 'default' != $style ) : ?>

			<div class="buttons-icon">
				<?php
				// Wishlist icon
				if ( shortcode_exists( 'add_to_wishlist' ) ) {
					echo do_shortcode( '[add_to_wishlist]' );
				} elseif ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
					echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
				}

				// Quick-view icon
				if ( 'quickview' != $style && $quickview && $quickview_behavior == 'view_button' ) {
					printf( '<a href="%s" class="quick_view_button button"><svg width="20" height="29"><use xlink:href="#quickview-eye"></use></svg></a>', esc_url( get_permalink() ) );
				}
				?>
			</div>

		<?php endif; ?>

		<?php if ( in_array( $style, array( 'default', 'quickview', 'addtocart' ) ) ) : ?>

			<div class="buttons">
				<?php
				woocommerce_template_loop_add_to_cart();

				if ( 'default' == $style ) {
					if ( shortcode_exists( 'add_to_wishlist' ) ) {
						echo do_shortcode( '[add_to_wishlist]' );
					} elseif ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
						echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
					}
				}

				if (
					( 'default' == $style && $quickview && $quickview_behavior == 'view_button' )
					|| ( 'quickview' == $style && $quickview )
				) {
					printf( '<a href="%s" class="quick_view_button button"><svg width="20" height="29"><use xlink:href="#quickview-eye"></use></svg></a>', esc_url( get_permalink() ) );
				}
				?>
			</div>

		<?php endif; ?>

		<?php
	}

	/**
	 * Print new product title shop page with link inside
	 */
	public function show_product_loop_title() {
		?>

		<h3 class="woocommerce-loop-product__title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h3>

		<?php
	}

	/**
	 * Hide the price for out-of-stock products
	 *
	 * @param string $price
	 * @param object $product
	 *
	 * @return string
	 */
	public function price_html( $price, $product ) {
		if ( ! $product->is_in_stock() ) {
			$price = esc_html__( 'Sold Out', 'sober' );
		}

		return $price;
	}

	/**
	 * Display product toolbar on single product page
	 */
	public function product_toolbar() {
		if ( 'style-4' == sober_get_option( 'single_product_style' ) ) {
			return;
		}

		?>

		<div class="product-toolbar">
			<?php
			the_post_navigation( array(
				'screen_reader_text' => esc_html__( 'Product navigation', 'sober' ),
				'prev_text'          => _x( '<svg viewBox="0 0 20 20"><use xlink:href="#left-arrow"></use></svg><span class="screen-reader-text">%title</span>', 'Previous post link', 'sober' ),
				'next_text'          => _x( '<span class="screen-reader-text">%title</span><svg viewBox="0 0 20 20"><use xlink:href="#right-arrow"></use></svg>', 'Next post link', 'sober' ),
			) );

			$yoast = get_option( 'wpseo_internallinks' );

			if ( function_exists( 'yoast_breadcrumb' ) && $yoast && $yoast['breadcrumbs-enable'] ) {
				yoast_breadcrumb( '<div class="breadcrumb">', '</div>' );
			} else {
				woocommerce_breadcrumb();
			}
			?>
		</div>

		<?php
	}

	/**
	 * Open product summary div
	 */
	public function open_product_summary() {
		echo '<div class="product-summary clearfix">';
	}

	/**
	 * Close product summary div
	 */
	public function close_product_summary() {
		echo '</div>';
	}

	/**
	 * Change thumbnail size in single product page
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public function small_thumbnail_size( $size ) {
		if ( 'style-1' == sober_get_option( 'single_product_style' ) ) {
			$size = 'shop_single';
		}

		return $size;
	}

	/**
	 * Open sticky product summary container
	 */
	public function open_sticky_description() {
		echo '<div class="sticky-summary">';
	}

	/**
	 * Close sticky product summary container
	 */
	public function close_sticky_description() {
		echo '</div>';
	}

	/**
	 * Add wishlist button again
	 */
	public function single_product_wishlist_button() {
		global $product;

		// Button was added to variable products manually
		if ( $product->is_type( 'variable' ) || $product->is_type( 'external' ) ) {
			return;
		}

		if ( shortcode_exists( 'add_to_wishlist' ) ) {
			echo do_shortcode( '[add_to_wishlist]' );
		}
	}

	/**
	 * Wrap reset variations link with a div container
	 *
	 * @param $link
	 *
	 * @return string
	 */
	public function reset_variations_link( $link ) {
		return '<div class="variations-reset">' . $link . '</div>';
	}

	/**
	 * Product share
	 * Share on Facebook, Twitter, Pinterest
	 */
	public function share() {
		$socials = sober_get_option( 'product_share' );

		if ( empty( $socials ) ) {
			return;
		}
		?>

		<div class="product-share">
			<span class="screen-reader-text"><?php esc_html_e( 'Share','sober' ) ?></span>

			<?php if ( in_array( 'facebook', $socials ) ) : ?>

				<a href="<?php echo esc_url( add_query_arg( array( 'u' => rawurlencode( get_permalink() ) ), 'https://www.facebook.com/sharer/sharer.php' ) ) ?>" target="_blank" class="facebook-share-link">
					<i class="fa fa-facebook"></i><?php esc_html_e( 'Facebook', 'sober' ) ?>
				</a>
			<?php endif; ?>

			<?php if ( in_array( 'twitter', $socials ) ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array(
					'url'  => rawurlencode( get_permalink() ),
					'text' => rawurlencode( get_the_title() ),
				), 'https://twitter.com/intent/tweet' ) ) ?>" target="_blank" class="twitter-share-link">
					<i class="fa fa-twitter"></i><?php esc_html_e( 'Twitter', 'sober' ) ?>
				</a>
			<?php endif; ?>

			<?php if ( in_array( 'pinterest', $socials ) ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array(
					'url'         => rawurlencode( get_permalink() ),
					'media'       => get_the_post_thumbnail_url( null, 'full' ),
					'description' => rawurlencode( get_the_title() ),
				), 'http://pinterest.com/pin/create/button' ) ) ?>" target="_blank" class="pinterest-share-link">
					<i class="fa fa-pinterest-p"></i><?php esc_html_e( 'Pinterest', 'sober' ) ?>
				</a>
			<?php endif; ?>

			<?php if ( in_array( 'email', $socials ) ) : ?>
				<a href="mailto:?subject=<?php esc_attr_e( 'I wanted you to see this', 'sober' ) ?>&amp;body=<?php echo esc_attr_e( 'Check out this site', 'sober' ) . ' ' . rawurlencode( get_permalink() ) ?>" target="_blank" class="email-share-link">
					<i class="fa fa-envelope-o"></i><?php esc_html_e( 'Email', 'sober' ) ?>
				</a>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Add extra content at bottom of product's short description
	 */
	public function product_extra_content() {
		$content = sober_get_option( 'product_extra_content' );

		if ( empty( $content ) ) {
			return;
		}

		printf( '<div class="product-extra-content">%s</div>', do_shortcode( wp_kses_post( $content ) ) );
	}

	/**
	 * Change product tab titles
	 * Add <span> to the counter beside "Review" tab
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_tabs( $tabs ) {
		foreach ( $tabs as &$tab ) {
			$tab['title'] = str_replace( array( '(', ')' ), array(
				'<span class="counter">',
				'</span>',
			), $tab['title'] );
		}

		return $tabs;
	}

	/**
	 * Change related & up-sell products columns
	 *
	 * @param int $columns
	 *
	 * @return int
	 */
	public function related_products_columns( $columns ) {
		global $woocommerce_loop;

		if ( 'style-3' == sober_get_option( 'single_product_style' ) ) {
			if ( isset( $woocommerce_loop['name'] ) && $woocommerce_loop['name'] == 'up-sells' ) {
				$columns = 2;
			} else {
				$columns = 4;
			}
		} else {
			$columns = 5;
		}

		return $columns;
	}

	/**
	 * Change related products args
	 * It contains 'posts_per_page' and 'columns'
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function related_products_args( $args ) {
		global $woocommerce_loop;

		$product_style = sober_get_option( 'single_product_style' );

		if ( 'style-3' == $product_style ) {
			$args['posts_per_page'] = 4;

			if ( isset( $woocommerce_loop['name'] ) && $woocommerce_loop['name'] == 'up-sells' ) {
				$args['columns'] = 2;
			} else {
				$args['columns'] = 4;
			}
		} elseif ( 'style-4' == $product_style ) {
			$args['posts_per_page'] = 4;
			$args['columns']        = 4;
		} else {
			$args['posts_per_page'] = 5;
			$args['columns']        = 5;
		}

		return $args;
	}

	/**
	 * Change upsell products args
	 * It contains 'posts_per_page' and 'columns'
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function upsell_products_args( $args ) {
		global $woocommerce_loop;

		$product_style = sober_get_option( 'single_product_style' );

		if ( 'style-3' == $product_style ) {
			$args['posts_per_page'] = 20;

			if ( isset( $woocommerce_loop['name'] ) && $woocommerce_loop['name'] == 'up-sells' ) {
				$args['columns'] = 2;
			} else {
				$args['columns'] = 4;
			}
		} elseif ( 'style-4' == $product_style ) {
			$args['posts_per_page'] = 4;
			$args['columns']        = 4;
		} else {
			$args['posts_per_page'] = 5;
			$args['columns']        = 5;
		}

		return $args;
	}

	/**
	 * Change cross sell product columns
	 *
	 * @return int
	 */
	public function cross_sell_columns() {
		return 4;
	}

	/**
	 * Change the default option none text
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function dropdown_variation_options_args( $args ) {
		$args['show_option_none'] = esc_html__( 'Select', 'sober' );

		return $args;
	}

	/**
	 * Output the proceed to checkout button.
	 */
	public function widget_shopping_cart_button_checkout() {
		?>

		<p>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout wc-forward">
				<span class="subtotal"><?php echo WC()->cart->get_cart_subtotal() ?></span>
				<span><?php esc_html_e( 'Checkout', 'sober' ); ?></span>
			</a>
		</p>

		<?php
	}

	/**
	 * Output the view cart button.
	 */
	public function widget_shopping_cart_button_view_cart() {
		?>

		<p>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="line-hover active wc-forward">
				<?php esc_html_e( 'View Cart', 'sober' ); ?>
			</a>
		</p>

		<?php
	}
}
