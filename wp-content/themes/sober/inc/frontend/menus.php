<?php

/**
 * Class Sober_Walker_Mega_Menu
 *
 * Walker class for mega menu
 */
class Sober_Walker_Mega_Menu extends Walker_Nav_Menu {
	/**
	 * Tells child items know it is in a mega menu or not
	 *
	 * @var boolean
	 */
	protected $in_mega = false;

	/**
	 * Store menu item mega data
	 *
	 * @var array
	 */
	protected $mega_data = array();

	/**
	 * Custom CSS for menu items
	 *
	 * @var string
	 */
	protected $css = '';

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see   Walker::start_lvl()
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );

		if ( ! $depth && $this->in_mega ) {
			$style = $this->get_mega_inline_css();
			$output .= "\n$indent<ul class=\"sub-menu mega-menu-container\" $style>\n";
		} else {
			$output .= "\n$indent<ul class=\"sub-menu\">\n";
		}
	}

	/**
	 * Start the element output.
	 * Display item description text and classes
	 *
	 * @see   Walker::start_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		// Get mega data from post meta
		$item_mega = get_post_meta( $item->ID, '_menu_item_mega', true );
		$item_mega = sober_parse_args( $item_mega, sober_get_mega_menu_setting_default() );

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $args  An array of arguments.
		 * @param object $item  Menu item data object.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		if ( $item_mega['icon'] ) {
			$classes[] = 'menu-item-has-icon';
		}
		if ( $item_mega['mega'] && ! $depth ) {
			$classes[] = 'menu-item-mega';

			if ( $item_mega['background']['image'] ) {
				$classes[] = 'menu-item-has-background';
			}
		}

		if ( 1 == $depth && $this->in_mega ) {
			$classes[] = 'mega-sub-menu ' . $this->get_css_column( $item_mega['width'] );

			if ( $item_mega['disable_link'] ) {
				$classes[] = 'link-disabled';
			}

			if ( $item_mega['hide_text'] ) {
				$classes[] = 'menu-item-title-hidden';
			}

			if ( $item_mega['border']['left'] ) {
				$classes[] = 'has-border-left';
			}
		}

		// Check if this is top level and is mega menu
		if ( ! $depth ) {
			$this->in_mega = $item_mega['mega'];
			$this->mega_data = $item_mega;
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$item_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );

		$item_id = $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '';

		$output .= $indent . '<li' . $item_id . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		// Check if link is disable
		if ( $this->in_mega && $depth == 1 && $item_mega['disable_link'] ) {
			$link_open = '<span>';
		} else {
			$link_open = '<a' . $attributes . '>';
		}

		// Adds icon
		if ( $item_mega['icon'] ) {
			$icon = '<i class="' . esc_attr( $item_mega['icon'] ) . '"></i>';
		} else {
			$icon = '';
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string $title The menu item's title.
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of wp_nav_menu() arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		// Check if link is disable
		if ( $this->in_mega && $depth == 1 && $item_mega['disable_link'] ) {
			$link_close = '</span>';
		} else {
			$link_close = '</a>';
		}

		$item_output = $args->before;
		$item_output .= $link_open;
		$item_output .= $args->link_before . $icon . $title . $args->link_after;
		$item_output .= $link_close;
		$item_output .= $args->after;

		if ( 1 <= $depth && ! empty( $item_mega['content'] ) ) {
			$item_output .= '<div class="menu-item-content">' . do_shortcode( $item_mega['content'] ) . '</div>';
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Get CSS column class name
	 *
	 * @param string $width
	 *
	 * @return string
	 */
	private function get_css_column( $width = '25.00%' ) {
		$columns = array(
			3  => '25.00%',
			4  => '33.33%',
			6  => '50.00%',
			8  => '66.66%',
			9  => '75.00%',
			12 => '100.00%',
		);

		$column = array_search( $width, $columns );
		$column = false === $column ? 3 : $column;

		return 'col-md-' . $column;
	}

	/**
	 * Get inline css for mega menu container
	 *
	 * @return string
	 */
	private function get_mega_inline_css() {
		if ( ! $this->in_mega ) {
			return '';
		}

		$props = array();

		if ( $this->mega_data['width'] ) {
			$props['width'] = $this->mega_data['width'];
		}

		if ( $this->mega_data['background']['color'] ) {
			$props['background-color'] = $this->mega_data['background']['color'];
		}

		if ( $this->mega_data['background']['image'] ) {
			$props['background-image'] = 'url(' . $this->mega_data['background']['image'] . ')';
			$props['background-attachment'] = $this->mega_data['background']['attachment'];
			$props['background-repeat'] = $this->mega_data['background']['repeat'];

			if ( $this->mega_data['background']['size'] ) {
				$props['background-size'] = $this->mega_data['background']['size'];
			}

			if ( $this->mega_data['background']['position']['x'] == 'custom' ) {
				$position_x = $this->mega_data['background']['position']['custom']['x'];
			} else {
				$position_x = $this->mega_data['background']['position']['x'];
			}

			if ( $this->mega_data['background']['position']['y'] == 'custom' ) {
				$position_y = $this->mega_data['background']['position']['custom']['y'];
			} else {
				$position_y = $this->mega_data['background']['position']['y'];
			}

			$props['background-position'] = $position_x . ' ' . $position_y;
		}

		if ( empty( $props ) ) {
			return '';
		}

		$style = '';
		foreach ( $props as $prop => $value ) {
			$style .= $prop . ':' . esc_attr( $value ) . ';';
		}

		return 'style="' . $style . '"';
	}
}

/**
 * Add a walder object for all nav menu
 *
 * @since  1.0.0
 *
 * @param  array $args The default args
 *
 * @return array
 */
function sober_nav_menu_args( $args ) {
	if ( ! in_array( $args['theme_location'], array( 'topbar', 'footer', 'socials' ) ) ) {
		$args['walker'] = new Sober_Walker_Mega_Menu;
	}

	if ( in_array( $args['theme_location'], array( 'primary', 'secondary' ) ) ) {
		$args['fallback_cb'] = false;
	}

	return $args;
}

add_filter( 'wp_nav_menu_args', 'sober_nav_menu_args' );