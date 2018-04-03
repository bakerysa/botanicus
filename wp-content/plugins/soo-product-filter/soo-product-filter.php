<?php
/**
 * Plugin Name: Soo Product Filter
 * Plugin URI: https://themeforest.net/item/sober-woocommerce-wordpress-theme/18332889?ref=uixthemes
 * Description: An extension of WooCommerce for filtering product
 * Version: 1.0.5
 * Author: SooThemes
 * Author URI: http://uix.store/
 * Text Domain: soopf
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load plugin text domain
 */
add_action( 'init', function () {
	load_plugin_textdomain( 'soopf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );

/**
 * Register widget
 */
add_action(
	'widgets_init', function () {
	register_widget( 'Soo_Product_Filter_Widget' );
} );

/**
 * Class product filter widget
 */
class Soo_Product_Filter_Widget extends WP_Widget {
	/**
	 * Default widget settings
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Store active filter field to avoid duplicate fields
	 *
	 * @var array
	 */
	protected $active_fields;

	/**
	 * Store other filters from URL
	 *
	 * @var array
	 */
	protected $current_filters;

	/**
	 * Widget constructor.
	 */
	public function __construct() {
		$this->defaults = array(
			'title'  => '',
			'ajax'   => true,
			'filter' => array(),
		);

		$this->active_fields = array();
		$this->current_filters = array();

		parent::__construct(
			'soo-product-filter',
			esc_html__( 'Soo Product Filter', 'soopf' ),
			array(
				'classname'   => 'woocommerce soo-product-filter-widget',
				'description' => esc_html__( 'Display product filters', 'soopf' ),
			),
			array( 'width' => 780 )
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts on the frontend
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'soopf', plugins_url( '/assets/css/frontend.css', __FILE__ ), array(), '20160623' );
		wp_enqueue_script( 'soopf', plugins_url( '/assets/js/frontend.js', __FILE__ ), array(
			'jquery-ui-slider',
			'wp-util',
			'jquery-serialize-object',
		), '20160623', true );

		wp_localize_script( 'soopf', 'sooFilter', array(
			'currency' => array(
				'position' => get_option( 'woocommerce_currency_pos' ),
				'symbol'   => get_woocommerce_currency_symbol(),
			),
			'selector' => apply_filters( 'soopf_ajax_selector', array(
				'counter'  => '.woocommerce-result-count',
				'products' => '#primary ul.products',
				'nav'      => '#primary nav.woocommerce-pagination',
				'notfound' => '#primary .woocommerce-info',
			) ),
		) );
	}

	/**
	 * Remember current filters/search
	 */
	protected function get_current_filters( $active_filter = array() ) {
		$request = $_GET;

		if ( get_search_query() ) {
			$this->current_filters['s'] = get_search_query();
			if ( isset( $request['s'] ) ) {
				unset( $request['s'] );
			}
		}

		if ( ! empty( $request['post_type'] ) ) {
			$this->current_filters['post_type'] = $request['post_type'];
			unset( $request['post_type'] );
		}

		if ( ! empty ( $request['product_cat'] ) ) {
			$this->current_filters['product_cat'] = $request['product_cat'];
			unset( $request['product_cat'] );
		}

		if ( ! empty( $request['product_tag'] ) ) {
			$this->current_filters['product_tag'] = $request['product_tag'];
			unset( $request['product_tag'] );
		}

		if ( ! empty( $request['orderby'] ) ) {
			$this->current_filters['orderby'] = $request['orderby'];
			unset( $request['orderby'] );
		}

		if ( ! empty( $request['min_rating'] ) ) {
			$this->current_filters['min_rating'] = $request['min_rating'];
			unset( $request['min_rating'] );
		}

		if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) {
			foreach ( $_chosen_attributes as $attribute => $data ) {
				$taxonomy_filter = 'filter_' . str_replace( 'pa_', '', $attribute );
				if ( isset( $request[ $taxonomy_filter ] ) ) {
					unset( $request[ $taxonomy_filter ] );
				}
				$this->current_filters[ $taxonomy_filter ] = implode( ',', $data['terms'] );

				if ( 'or' == $data['query_type'] ) {
					$query_type                           = str_replace( 'pa_', 'query_type_', $attribute );
					$this->current_filters[ $query_type ] = 'or';

					if ( isset( $request[ $query_type ] ) ) {
						unset( $request[ $query_type ] );
					}
				}
			}
		}

		if ( is_product_taxonomy() ) {
			$taxonomy                           = get_queried_object()->taxonomy;
			$term                               = get_queried_object()->slug;
			$this->current_filters[ $taxonomy ] = $term;
		}

		// Remove other active filters
		foreach ( $active_filter as $filter ) {
			if ( 'slider' == $filter['display'] ) {
				$min_name = 'min_' . $filter['source'];
				$max_name = 'max_' . $filter['source'];

				if ( isset( $request[ $min_name ] ) ) {
					unset( $request[ $min_name ] );
				}

				if ( isset( $request[ $max_name ] ) ) {
					unset( $request[ $max_name ] );
				}
			}
		}

		foreach ( $request as $name => $value ) {
			$this->current_filters[ $name ] = $value;
		}
	}

	/**
	 * Echoes the widget content.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments
	 * @param array $instance Saved values from database
	 */
	public function widget( $args, $instance ) {
		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		global $wp_the_query;

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['filter'] ) ) {
			return;
		}

		// if ( ! $wp_the_query->post_count ) {
		// 	return;
		// }

		// Remember current filters
		$this->get_current_filters( $instance['filter'] );

		// get form action url
		$form_action = wc_get_page_permalink( 'shop' );

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		echo '<form action="' . esc_url( $form_action ) . '" method="get" class="' . ( $instance['ajax'] ? 'ajax-filter' : '' ) . '">';

		// Reset active fields
		$this->active_fields = array();

		foreach ( (array) $instance['filter'] as $filter ) {
			$this->display_filter( $filter );
		}

		foreach ( $this->current_filters as $name => $value ) {
			if ( in_array( $name, $this->active_fields ) ) {
				continue;
			}

			printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $name ), esc_attr( $value ) );
		}

		// Add param post_type when the shop page is home page
		if ( trailingslashit( $form_action ) == trailingslashit( home_url() ) ) {
			echo '<input type="hidden" name="post_type" value="product">';
		}

		echo '<input type="submit" value="' . esc_attr( apply_filters( 'soopf_filter_button_text', __( 'Filter', 'soopf' ) ) ) . '" class="btn button filter-button">';

		echo '</form>';

		echo $args['after_widget'];
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * This function should check that `$new_instance` is set correctly. The newly-calculated
	 * value of `$instance` should be returned. If false is returned, the instance won't be
	 * saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $new_instance;
		$instance['title'] = strip_tags( $instance['title'] );
		$instance['ajax']  = isset( $instance['ajax'] );

		// Reorder filters
		if ( isset( $instance['filter'] ) ) {
			unset( $instance['filter'] );

			$index = 0;
			foreach ( $new_instance['filter'] as $filter ) {
				$instance['filter'][ $index ] = $filter;
				$index ++;
			}
		}

		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'soopf' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'ajax' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ajax' ) ); ?>" value="1" <?php checked( 1, $instance['ajax'] ) ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ajax' ) ); ?>"><?php esc_html_e( 'Use ajax for filtering', 'soopf' ); ?></label>
		</p>

		<hr>
		<p></p>

		<div class="soopf-filters">
			<p class="no-filter <?php echo empty( $instance['filter'] ) ? '' : 'hidden' ?>"><?php esc_html_e( 'There is no filter yet.', 'soopf' ) ?></p>

			<?php foreach ( (array) $instance['filter'] as $index => $filter ) : ?>

				<div class="soopf-filter-fields">
					<div class="name">
						<label>
							<?php esc_html_e( 'Filter Name', 'soopf' ) ?>
							<input type="text" name="<?php echo esc_attr( $this->get_field_name( "filter[$index]" ) ) ?>[name]" value="<?php echo esc_attr( $filter['name'] ) ?>" class="widefat">
						</label>
					</div>
					<div class="source">
						<label>
							<?php esc_html_e( 'Filter By', 'soopf' ) ?>
							<select name="<?php echo esc_attr( $this->get_field_name( "filter[$index]" ) ) ?>[source]" class="widefat filter-by">
								<?php
								foreach ( Soo_Product_Filter()->sources as $source => $name ) {
									printf( '<option value="%s" %s>%s</option>', esc_attr( $source ), selected( $source, $filter['source'], false ), esc_html( $name ) );
								}
								?>
							</select>
							<select name="<?php echo esc_attr( $this->get_field_name( "filter[$index]" ) ) ?>[attribute]" class="widefat <?php echo 'attribute' == $filter['source'] ? '' : 'hidden' ?>">
								<?php
								foreach ( Soo_Product_Filter()->attributes as $attribute => $name ) {
									printf( '<option value="%s" %s>%s</option>', esc_attr( $attribute ), selected( $attribute, $filter['attribute'], false ), esc_html( $name ) );
								}
								?>
							</select>
						</label>
					</div>
					<div class="display">
						<label>
							<?php esc_html_e( 'Display Type', 'soopf' ) ?>
							<select name="<?php echo esc_attr( $this->get_field_name( "filter[$index]" ) ) ?>[display]" class="widefat display-type">
								<?php
								foreach ( Soo_Product_Filter()->display[ $filter['source'] ] as $display => $name ) {
									printf( '<option value="%s" %s>%s</option>', esc_attr( $display ), selected( $display, $filter['display'], false ), esc_html( $name ) );
								}
								?>
							</select>
							<select name="<?php echo esc_attr( $this->get_field_name( "filter[$index]" ) ) ?>[multiple]" class="widefat <?php echo 'attribute' != $filter['source'] || 'dropdown' == $filter['display'] ? 'hidden' : '' ?>">
								<option value="1" <?php selected( 1, $filter['multiple'] ) ?>><?php esc_html_e( 'Multiple select', 'soopf' ) ?></option>
								<option value="0" <?php selected( 0, $filter['multiple'] ) ?>><?php esc_html_e( 'Single select', 'soopf' ) ?></option>
							</select>
						</label>
					</div>
					<div class="actions">
						<a href="#" class="remove-filter dashicons dashicons-no-alt"><span class="screen-reader-text"><?php esc_html_e( 'Remove filter', 'soopf' ) ?></span></a>
					</div>
				</div>

			<?php endforeach; ?>

		</div>

		<p style="text-align: center">
			<a href="#" class="soopf-add-new" data-number="<?php echo esc_attr( $this->number ) ?>" data-name="<?php echo esc_attr( $this->get_field_name( 'filter' ) ) ?>" data-count="<?php echo count( $instance['filter'] ) ?>">+ <?php esc_html_e( 'Add new filter', 'soopf' ) ?></a>
		</p>

		<?php
	}

	/**
	 * Print HTML for single filter
	 *
	 * @param array $filter
	 */
	protected function display_filter( $filter = array() ) {
		$filter = wp_parse_args( $filter, array(
			'name'      => '',
			'source'    => 'price',
			'display'   => 'slider',
			'attribute' => '',
			'multiple'  => false, // Use for attribute only
		) );

		// Build filter args
		$options = $this->get_filter_options( $filter );
		$names   = array(
			'price'     => 'price',
			'category'  => 'product_cat',
			'tag'       => 'product_tag',
			'attribute' => 'filter_' . $filter['attribute'],
		);
		$args    = array(
			'name'    => $names[ $filter['source'] ],
			'current' => array(),
			'options' => $options,
		);

		if ( 'attribute' == $filter['source'] ) {
			$attr = $this->get_tax_attribute( $filter['attribute'] );

			// Stop if attribute isn't exists
			if ( ! $attr ) {
				return;
			}

			$args['type']     = $attr->attribute_type;
			$args['multiple'] = true;
			$args['all']      = sprintf( esc_html__( 'Any %s', 'soopf' ), wc_attribute_label( $attr->attribute_label ) );
		} elseif ( in_array( $filter['source'], array( 'category', 'tag' ) ) ) {
			$taxonomy = str_replace( array( 'category', 'tag' ), array( 'product_cat', 'product_tag' ), $filter['source'] );
			$taxonomy = get_taxonomy( $taxonomy );
			$args['all'] = sprintf( esc_html__( 'Select a %s', 'soopf' ), $taxonomy->labels->singular_name );
		}

		if ( 'slider' == $filter['display'] ) {
			$args['current']['min'] = isset( $_GET[ 'min_' . $args['name'] ] ) ? $_GET[ 'min_' . $args['name'] ] : '';
			$args['current']['max'] = isset( $_GET[ 'max_' . $args['name'] ] ) ? $_GET[ 'max_' . $args['name'] ] : '';
		} elseif ( 'attribute' == $filter['source'] ) {
			$args['current'] = isset( $_GET[ 'filter_' . $filter['attribute'] ] ) ? explode( ',', $_GET[ 'filter_' . $filter['attribute'] ] ) : array();
		} else {
			$args['current'] = isset( $this->current_filters[ $args['name'] ] ) ? explode( ',', $this->current_filters[ $args['name'] ] ) : array();
		}

		$args = apply_filters( 'soopf_filter_args', $args, $filter );

		// Only apply multiple select to attributes
		if ( 'attribute' != $filter['source'] ) {
			$args['multiple'] = false;
		}

		// Don't duplicate fields
		if ( in_array( $args['name'], $this->active_fields ) ) {
			return;
		} else {
			$this->active_fields[] = $args['name'];
		}

		$classes   = array();
		$classes[] = ! empty( $filter['name'] ) ? sanitize_title( $filter['name'] ) : '';
		$classes[] = $filter['source'];
		$classes[] = $filter['display'];
		$classes[] = 'attribute' == $filter['source'] ? $filter['attribute'] : '';
		$classes[] = $filter['multiple'] ? 'multiple' : '';
		$classes   = array_unique( $classes );
		$classes   = apply_filters( 'soopf_single_filter_class', array_filter( $classes ), $filter );
		?>

		<div class="product-filter <?php echo join( ' ', $classes ) ?>">
			<?php if ( ! empty( $filter['name'] ) ) : ?>
				<span class="filter-name"><?php echo esc_html( $filter['name'] ) ?></span>
			<?php endif; ?>

			<div class="filter-control">
				<?php
				switch ( $filter['display'] ) {
					case 'slider':
						$this->display_slider( $args );
						break;

					case 'dropdown':
						$this->display_dropdown( $args );
						break;

					case 'list':
					case 'h-list':
						$this->display_list( $args );
						break;

					case 'auto':
						$this->display_auto( $args );
						break;

					default:
						$this->display_dropdown( $args );
						break;
				}
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * Get filter options
	 *
	 * @param array $filter
	 *
	 * @return array
	 */
	protected function get_filter_options( $filter ) {
		$options = array();

		switch ( $filter['source'] ) {
			case 'price':
				// Find min and max price in current result set
				$prices = $this->get_filtered_price();
				$min    = floor( $prices->min_price );
				$max    = ceil( $prices->max_price );

				/**
				 * Adjust max if the store taxes are not displayed how they are stored.
				 * Min is left alone because the product may not be taxable.
				 * Kicks in when prices excluding tax are displayed including tax.
				 */
				if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
					$tax_classes = array_merge( array( '' ), WC_Tax::get_tax_classes() );
					$class_max   = $max;

					foreach ( $tax_classes as $tax_class ) {
						if ( $tax_rates = WC_Tax::get_rates( $tax_class ) ) {
							$class_max = $max + WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max, $tax_rates ) );
						}
					}

					$max = $class_max;
				}
				$options['min'] = $min;
				$options['max'] = $max;

				break;

			case 'category':
			case 'tag':
				$taxonomy = 'category' == $filter['source'] ? 'product_cat' : 'product_tag';
				$terms    = get_terms( $taxonomy );

				foreach ( $terms as $term ) {
					$options[ $term->slug ] = array(
						'name'  => $term->name,
						'count' => $term->count,
						'id'    => $term->term_id,
					);
				}
				break;

			case 'attribute':
				$taxonomy = 'pa_' . $filter['attribute'];
				$terms    = get_terms( $taxonomy );

				if ( is_wp_error( $terms ) ) {
					break;
				}

				foreach ( $terms as $term ) {
					$options[ $term->slug ] = array(
						'name'  => $term->name,
						'count' => $term->count,
						'id'    => $term->term_id,
					);
				}
				break;
		}

		return apply_filters( 'soopf_filter_options', $options, $filter );
	}

	/**
	 * Print HTML of slider
	 *
	 * @param array $args
	 */
	protected function display_slider( $args ) {
		$args = wp_parse_args( $args, array(
			'name'    => '',
			'current' => array(
				'min' => '',
				'max' => '',
			),
			'options' => array(
				'min' => 0,
				'max' => 0,
			),
		) );

		if ( $args['options']['min'] == $args['options']['max'] ) {
			return;
		}
		?>

		<div class="filter-slider hidden"></div>
		<div class="slider-amount">
			<input type="text" name="min_<?php echo esc_attr( $args['name'] ) ?>" value="<?php echo esc_attr( $args['current']['min'] ) ?>" data-min="<?php echo esc_attr( $args['options']['min'] ) ?>" />
			<input type="text" name="max_<?php echo esc_attr( $args['name'] ) ?>" value="<?php echo esc_attr( $args['current']['max'] ) ?>" data-max="<?php echo esc_attr( $args['options']['max'] ) ?>" />
			<div class="slider-label hidden">
				<span class="range"><?php esc_html_e( 'Range:', 'soopf' ) ?></span><span class="from"></span> &mdash;
				<span class="to"></span>
			</div>
			<div class="clear"></div>
		</div>

		<?php
	}

	/**
	 * Print HTML of dropdown
	 *
	 * @param array $args
	 */
	protected function display_dropdown( $args ) {
		$args = wp_parse_args( $args, array(
			'name'    => '',
			'current' => array(),
			'options' => array(),
			'all'     => esc_html__( 'Any', 'soopf' ),
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		echo '<select name="' . esc_attr( $args['name'] ) . '">';

		echo '<option value="">' . $args['all'] . '</option>';
		foreach ( $args['options'] as $slug => $option ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $slug ),
				selected( true, in_array( $slug, (array) $args['current'] ), false ),
				esc_html( $option['name'] )
			);
		}

		echo '</select>';
	}

	/**
	 * Print HTML of list
	 *
	 * @param array $args
	 */
	protected function display_list( $args ) {
		$args = wp_parse_args( $args, array(
			'name'    => '',
			'current' => array(),
			'options' => array(),
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		echo '<ul class="filter-list">';
		foreach ( $args['options'] as $slug => $option ) {
			printf(
				'<li class="filter-list-item %s" data-value="%s"><span class="name">%s</span><span class="count">%d</span></li>',
				in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
				esc_attr( $slug ),
				esc_html( $option['name'] ),
				esc_html( $option['count'] )
			);
		}
		echo '</ul>';

		printf(
			'<input type="hidden" name="%s" value="%s" %s>',
			esc_attr( $args['name'] ),
			esc_attr( implode( ',', $args['current'] ) ),
			empty( $args['current'] ) ? 'disabled' : ''
		);
	}

	/**
	 * Display attribute filter automatically
	 *
	 * @param array $args
	 */
	protected function display_auto( $args ) {
		$args = wp_parse_args( $args, array(
			'name'    => '',
			'type'    => 'select',
			'current' => array(),
			'options' => array(),
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		// Use select by default if plugin Soo Product Attribute Swatches is not installed
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active( 'soo-product-attribute-swatches' ) ) {
			$args['type'] = 'select';
		}

		switch ( $args['type'] ) {
			case 'color':
				echo '<div class="filter-swatches">';
				foreach ( $args['options'] as $slug => $option ) {
					$color = get_term_meta( $option['id'], 'color', true );
					list( $r, $g, $b ) = sscanf( $color, "#%02x%02x%02x" );

					printf(
						'<span class="swatch swatch-color swatch-%s %s" data-value="%s" style="background-color:%s;color:%s;" title="%s">%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $color ),
						esc_attr( "rgba($r,$g,$b,0.5)" ),
						esc_attr( $option['name'] ),
						esc_html( $option['name'] )
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);
				break;

			case 'image':
				echo '<div class="filter-swatches">';
				foreach ( $args['options'] as $slug => $option ) {
					$image = get_term_meta( $option['id'], 'image', true );
					$image = $image ? wp_get_attachment_image_src( $image ) : '';
					$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

					printf(
						'<span class="swatch swatch-image swatch-%s %s" data-value="%s" title="%s"><img src="%s" alt="%s"></span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $option['name'] ),
						esc_url( $image ),
						esc_attr( $option['name'] )
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);
				break;

			case 'label':
				echo '<div class="filter-swatches">';
				foreach ( $args['options'] as $slug => $option ) {
					$label = get_term_meta( $option['id'], 'label', true );
					$label = $label ? $label : $option['name'];

					printf(
						'<span class="swatch swatch-label swatch-%s %s" data-value="%s" title="%s">%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $option['name'] ),
						esc_html( $label )
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);
				break;

			default:
				$this->display_dropdown( $args );
				break;
		}
	}

	/**
	 * Get filtered min price for current products.
	 *
	 * @return int
	 */
	protected function get_filtered_price() {
		global $wpdb, $wp_the_query;

		$args       = $wp_the_query->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

		if ( ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = array(
				'taxonomy' => $args['taxonomy'],
				'terms'    => array( $args['term'] ),
				'field'    => 'slug',
			);
		}

		foreach ( $meta_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query  = new WP_Tax_Query( $tax_query );

		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql = "SELECT min( CAST( price_meta.meta_value AS UNSIGNED ) ) as min_price, max( CAST( price_meta.meta_value AS UNSIGNED ) ) as max_price FROM {$wpdb->posts} ";
		$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " 	WHERE {$wpdb->posts}.post_type = 'product'
					AND {$wpdb->posts}.post_status = 'publish'
					AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
					AND price_meta.meta_value > '' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		return $wpdb->get_row( $sql );
	}

	/**
	 * Get attribute's properties
	 *
	 * @param string $attribute
	 *
	 * @return object
	 */
	protected function get_tax_attribute( $attribute ) {
		global $wpdb;

		$attr = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute'" );

		return $attr;
	}
}

/**
 * Plugin main class
 */
class Soo_Product_Filter {
	/**
	 * The single instance of the class
	 *
	 * @var Soo_Product_Filter
	 */
	public static $instance = null;

	/**
	 * Filter sources
	 *
	 * @var array
	 */
	public $sources = array();

	/**
	 * Display types for each source
	 *
	 * @var array
	 */
	public $display = array();

	/**
	 * All product attributes
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * Main instance
	 *
	 * @return Soo_Product_Filter
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->sources = apply_filters(
			'soopf_filter_sources', array(
				'price'     => esc_html__( 'Price', 'soopf' ),
				'category'  => esc_html__( 'Category', 'soopf' ),
				'tag'       => esc_html__( 'Tag', 'soopf' ),
				'attribute' => esc_html__( 'Attributes', 'soopf' ),
			)
		);

		$this->display = apply_filters(
			'soopf_filter_display', array(
				'price'     => array(
					'slider' => esc_html__( 'Slider', 'soopf' ),
				),
				'category'  => array(
					'dropdown' => esc_html__( 'Dropdown', 'soopf' ),
					'list'     => esc_html__( 'Vertical List', 'soopf' ),
					'h-list'   => esc_html__( 'Horizontal List', 'soopf' ),
				),
				'tag'       => array(
					'dropdown' => esc_html__( 'Dropdown', 'soopf' ),
					'list'     => esc_html__( 'Vertical List', 'soopf' ),
					'h-list'   => esc_html__( 'Horizontal List', 'soopf' ),
				),
				'attribute' => array(
					'auto'     => esc_html__( 'Auto', 'soopf' ),
					'dropdown' => esc_html__( 'Dropdown', 'soopf' ),
					'list'     => esc_html__( 'Vertical List', 'soopf' ),
					'h-list'   => esc_html__( 'Horizontal List', 'soopf' ),
				),
			)
		);

		$attribute_taxonomies = wc_get_attribute_taxonomies();
		foreach ( $attribute_taxonomies as $tax ) {
			$this->attributes[ $tax->attribute_name ] = $tax->attribute_label;
		}
		$this->attributes = apply_filters( 'soopf_filter_attributes', $this->attributes );

		add_action( 'init', array( $this, 'load_textdomain' ) );

		add_action( 'admin_print_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_footer', array( $this, 'add_form_templates' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'add_form_templates' ) );
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'soopf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Load scripts in the backend
	 */
	public function enqueue_admin_scripts() {
		global $pagenow;

		if ( 'widgets.php' != $pagenow && 'customize.php' != $pagenow ) {
			return;
		}

		wp_enqueue_style( 'soopf-admin', plugins_url( '/assets/css/admin.css', __FILE__ ), array(), '20160617' );
		wp_enqueue_script( 'soopf-admin', plugins_url( '/assets/js/admin.js', __FILE__ ), array( 'wp-util' ), '20160617', true );

		wp_localize_script(
			'soopf-admin', 'sooFilter', array(
				'sources'    => $this->sources,
				'display'    => $this->display,
				'attributes' => $this->attributes,
			)
		);
	}

	/**
	 * Add Underscore template to footer
	 */
	public function add_form_templates() {
		global $pagenow;

		if ( 'widgets.php' != $pagenow && 'customize.php' != $pagenow ) {
			return;
		}
		?>

		<script type="text/template" id="tmpl-soopf-filter">
			<div class="soopf-filter-fields">
				<div class="name">
					<label>
						<?php esc_html_e( 'Filter Name', 'soopf' ) ?>
						<input type="text" name="{{data.name}}[{{data.count}}][name]" class="widefat">
					</label>
				</div>
				<div class="source">
					<label>
						<?php esc_html_e( 'Filter By', 'soopf' ) ?>
						<select name="{{data.name}}[{{data.count}}][source]" class="widefat filter-by">
							<# _.each( data.sources, function( name, source ) { #>
								<option value="{{source}}">{{name}}</option>
								<# } ); #>
						</select>
						<select name="{{data.name}}[{{data.count}}][attribute]" class="widefat hidden">
							<# _.each( data.attributes, function( name, slug ) { #>
								<option value="{{slug}}">{{name}}</option>
								<# } ); #>
						</select>
					</label>
				</div>
				<div class="display">
					<label>
						<?php esc_html_e( 'Display Type', 'soopf' ) ?>
						<select name="{{data.name}}[{{data.count}}][display]" class="widefat display-type">
							<# _.each( data.display[_.keys(data.sources)[0]], function( name, type ) { #>
								<option value="{{type}}">{{name}}</option>
								<# } ); #>
						</select>
						<select name="{{data.name}}[{{data.count}}][multiple]" class="widefat hidden">
							<option value="1"><?php esc_html_e( 'Multiple select', 'soopf' ) ?></option>
							<option value="0"><?php esc_html_e( 'Single select', 'soopf' ) ?></option>
						</select>
					</label>
				</div>
				<div class="actions">
					<a href="#" class="remove-filter dashicons dashicons-no-alt"><span class="screen-reader-text"><?php esc_html_e( 'Remove filter', 'soopf' ) ?></span></a>
				</div>
			</div>
		</script>

		<script type="text/template" id="tmpl-soopf-options">
			<# _.each( data.options, function( name, type ) { #>
				<option value="{{type}}">{{name}}</option>
				<# } ); #>
		</script>

		<?php
	}
}


/**
 * Main instance of plugin
 *
 * @return Soo_Product_Filter
 */
function Soo_Product_Filter() {
	return Soo_Product_Filter::instance();
}


/**
 * Create global instance of plugin
 */
add_action( 'plugins_loaded', function () {
	if ( ! function_exists( 'WC' ) ) {
		add_action(
			'admin_notices', function () {
			?>

			<div class="error">
				<p><?php esc_html_e( 'Soo Product Filter is enabled but not effective. It requires WooCommerce in order to work.', 'soopf' ); ?></p>
			</div>

			<?php
		}
		);
	} else {
		Soo_Product_Filter();
	}
} );
