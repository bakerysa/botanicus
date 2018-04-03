<?php

/**
 * Register portfolio support
 */
class Sober_Addons_Portfolio {
	private $post_type = 'portfolio';
	private $taxonomy_type = 'portfolio_type';
	private $option = 'sober_portfolio';

	/**
	 * The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Initialize
	 */
	static function init() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Add an option to enable the CPT
		add_action( 'admin_init', array( $this, 'settings_api_init' ) );
		add_action( 'current_screen', array( $this, 'save_settings' ) );

		// Make sure the post types are loaded for imports
		add_action( 'import_start', array( $this, 'register_post_type' ) );

		if ( ! get_option( $this->option ) ) {
			return;
		}

		$this->register_post_type();
		add_action( 'add_option_' . $this->post_type, 'flush_rewrite_rules' );
		add_action( 'update_option_' . $this->post_type, 'flush_rewrite_rules' );
		add_action( 'publish_' . $this->post_type, 'flush_rewrite_rules' );

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', $this->post_type ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', $this->post_type ), array(
			$this,
			'manage_custom_columns',
		), 10, 2 );

		// Add image size
		add_image_size( 'sober-portfolio', 451, 451, true );
		add_image_size( 'sober-portfolio-large', 912, 912, true );
		add_image_size( 'sober-portfolio-wide', 912, 451, true );
		add_image_size( 'sober-portfolio-masonry', 565, 10000, false );
		add_image_size( 'sober-portfolio-single', 1140, 740, true );

		// Enqueue style and javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Adjust CPT archive and custom taxonomies to obey CPT reading setting
		// add_filter( 'pre_get_posts', array( $this, 'query_reading_setting' ) );

		// Rewrite url
		add_filter( 'rewrite_rules_array', array( $this, 'rewrite_rules' ), 30 );
		add_filter( 'post_type_link', array( $this, 'portfolio_post_type_link' ), 10, 2 );
		add_filter( 'attachment_link', array( $this, 'portfolio_attachment_link' ), 10, 2 );

		// Template redirect
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );

		// Breadcrumb & archive title
		add_filter( 'sober_breadcrumbs_args', array( $this, 'breadcrumb' ) );
		add_filter( 'get_the_archive_title', array( $this, 'archive_title' ) );
	}

	/**
	 * Register portfolio post type
	 */
	public function register_post_type() {
		if ( post_type_exists( $this->post_type ) ) {
			return;
		}

		$permalinks          = get_option( $this->option . '_permalinks' );
		$portfolio_permalink = empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'sober' ) : $permalinks['portfolio_base'];
		$portfolio_page_id   = get_option( $this->option . '_page_id' );
		$portfolio_type_base = empty( $permalinks['portfolio_type_base'] ) ? _x( 'portfolio-type', 'slug', 'sober' ) : $permalinks['portfolio_type_base'];

		register_post_type( $this->post_type, array(
			'description'         => esc_html__( 'Portfolio Items', 'sober' ),
			'labels'              => array(
				'name'                  => esc_html__( 'Portfolio', 'sober' ),
				'singular_name'         => esc_html__( 'Project', 'sober' ),
				'menu_name'             => esc_html__( 'Portfolio', 'sober' ),
				'all_items'             => esc_html__( 'All Projects', 'sober' ),
				'add_new'               => esc_html__( 'Add New', 'sober' ),
				'add_new_item'          => esc_html__( 'Add New Project', 'sober' ),
				'edit_item'             => esc_html__( 'Edit Project', 'sober' ),
				'new_item'              => esc_html__( 'New Project', 'sober' ),
				'view_item'             => esc_html__( 'View Project', 'sober' ),
				'search_items'          => esc_html__( 'Search Projects', 'sober' ),
				'not_found'             => esc_html__( 'No Projects found', 'sober' ),
				'not_found_in_trash'    => esc_html__( 'No Projects found in Trash', 'sober' ),
				'filter_items_list'     => esc_html__( 'Filter projects list', 'sober' ),
				'items_list_navigation' => esc_html__( 'Project list navigation', 'sober' ),
				'items_list'            => esc_html__( 'Projects list', 'sober' ),
			),
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'author',
			),
			'rewrite'             => $portfolio_permalink ? array(
				'slug'       => untrailingslashit( $portfolio_permalink ),
				'with_front' => false,
				'feeds'      => true,
				'pages'      => true,
			) : false,
			'public'              => true,
			'show_ui'             => true,
			'menu_position'       => 20,                    // below Pages
			'menu_icon'           => 'dashicons-portfolio', // 3.8+ dashicon option
			'capability_type'     => 'page',
			'query_var'           => true,
			'map_meta_cap'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'hierarchical'        => false,
			'has_archive'         => $portfolio_page_id && get_post( $portfolio_page_id ) ? get_page_uri( $portfolio_page_id ) : $this->post_type,
			'show_in_nav_menus'   => true,
		) );

		register_taxonomy( $this->taxonomy_type, $this->post_type, array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'                  => esc_html__( 'Project Types', 'sober' ),
				'singular_name'         => esc_html__( 'Project Type', 'sober' ),
				'menu_name'             => esc_html__( 'Project Types', 'sober' ),
				'all_items'             => esc_html__( 'All Project Types', 'sober' ),
				'edit_item'             => esc_html__( 'Edit Project Type', 'sober' ),
				'view_item'             => esc_html__( 'View Project Type', 'sober' ),
				'update_item'           => esc_html__( 'Update Project Type', 'sober' ),
				'add_new_item'          => esc_html__( 'Add New Project Type', 'sober' ),
				'new_item_name'         => esc_html__( 'New Project Type Name', 'sober' ),
				'parent_item'           => esc_html__( 'Parent Project Type', 'sober' ),
				'parent_item_colon'     => esc_html__( 'Parent Project Type:', 'sober' ),
				'search_items'          => esc_html__( 'Search Project Types', 'sober' ),
				'items_list_navigation' => esc_html__( 'Project type list navigation', 'sober' ),
				'items_list'            => esc_html__( 'Project type list', 'sober' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'         => $portfolio_type_base,
				'with_front'   => false,
				'hierarchical' => true,
			),
		) );
	}

	/**
	 * Add custom column to manage portfolio screen
	 * Add Thumbnail column
	 *
	 * @since  1.0.0
	 *
	 * @param  array $columns Default columns
	 *
	 * @return array
	 */
	public function edit_admin_columns( $columns ) {
		// change 'Title' to 'Project'
		$columns['title'] = esc_html__( 'Project', 'sober' );

		if ( current_theme_supports( 'post-thumbnails' ) ) {
			// add featured image before 'Project'
			$columns = array_slice( $columns, 0, 1, true ) + array( 'thumbnail' => '' ) + array_slice( $columns, 1, null, true );
		}

		return $columns;
	}

	/**
	 * Handle custom column display
	 *
	 * @since  1.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 */
	public function manage_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'thumbnail':
				echo get_the_post_thumbnail( $post_id, array( 50, 50 ) );
				break;
		}
	}

	/**
	 * Load scripts and style for meta box
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();

		if ( 'edit.php' == $hook && $this->post_type == $screen->post_type && current_theme_supports( 'post-thumbnails' ) ) {
			wp_add_inline_style( 'wp-admin', '.manage-column.column-thumbnail { width: 50px; } @media screen and (max-width: 360px) { .column-thumbnail{ display:none; } }' );
		}
	}

	/**
	 * Add a checkbox field in 'Settings' > 'Writing'
	 * for enabling CPT functionality.
	 */
	public function settings_api_init() {
		add_settings_section(
			'sober_portfolio_section',
			'<span id="portfolio-options">' . esc_html__( 'Portfolio', 'sober' ) . '</span>',
			array( $this, 'writing_section_html' ),
			'writing'
		);

		add_settings_field(
			$this->option,
			'<span class="portfolio-options">' . esc_html__( 'Portfolio Projects', 'sober' ) . '</span>',
			array( $this, 'enable_field_html' ),
			'writing',
			'sober_portfolio_section'
		);
		register_setting(
			'writing',
			$this->option,
			'intval'
		);

		// Check if CPT is enabled first so that intval doesn't get set to NULL on re-registering
		if ( get_option( $this->option ) ) {
			// Reading settings
			add_settings_section(
				'sober_portfolio_section',
				'<span id="portfolio-options">' . esc_html__( 'Portfolio', 'sober' ) . '</span>',
				array( $this, 'reading_section_html' ),
				'reading'
			);

			add_settings_field(
				$this->option . '_page_id',
				'<span class="portfolio-options">' . esc_html__( 'Portfolio page', 'sober' ) . '</span>',
				array( $this, 'page_field_html' ),
				'reading',
				'sober_portfolio_section'
			);

			register_setting(
				'reading',
				$this->option . '_page_id',
				'intval'
			);

			add_settings_field(
				$this->option . '_posts_per_page',
				'<label for="portfolio_items_per_page">' . esc_html__( 'Portfolio items show at most', 'sober' ) . '</label>',
				array( $this, 'per_page_field_html' ),
				'reading',
				'sober_portfolio_section'
			);

			register_setting(
				'reading',
				$this->option . '_posts_per_page',
				'intval'
			);

			// Permalink settings
			add_settings_section(
				'sober_portfolio_section',
				'<span id="portfolio-options">' . esc_html__( 'Portfolio Item Permalink', 'sober' ) . '</span>',
				array( $this, 'permalink_section_html' ),
				'permalink'
			);

			add_settings_field(
				'portfolio_type_slug',
				'<label for="portfolio_type_slug">' . esc_html__( 'Portfolio type base', 'sober' ) . '</label>',
				array( $this, 'portfolio_type_slug_field_html' ),
				'permalink',
				'optional'
			);

			register_setting(
				'permalink',
				'portfolio_type_slug',
				'sanitize_text_field'
			);
		}
	}

	/**
	 * Add writing setting section
	 */
	public function writing_section_html() {
		?>
		<p>
			<?php esc_html_e( 'Use these settings to display custom types of content on your site', 'sober' ); ?>
		</p>
		<?php
	}

	/**
	 * Add reading setting section
	 */
	public function reading_section_html() {
		?>
		<p>
			<?php esc_html_e( 'Use these settings to control custom post type content', 'sober' ); ?>
		</p>
		<?php
	}

	/**
	 * Add permalink setting section
	 * and add fields
	 */
	public function permalink_section_html() {
		$permalinks          = get_option( $this->option . '_permalinks' );
		$portfolio_permalink = isset( $permalinks['portfolio_base'] ) ? $permalinks['portfolio_base'] : '';

		$portfolio_page_id = get_option( $this->option . '_page_id' );
		$base_slug         = urldecode( ( $portfolio_page_id > 0 && get_post( $portfolio_page_id ) ) ? get_page_uri( $portfolio_page_id ) : _x( 'portfolio', 'Default slug', 'sober' ) );
		$portfolio_base    = _x( 'portfolio', 'Default slug', 'sober' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $base_slug ),
			2 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%portfolio_type%' ),
		);
		?>
		<p>
			<?php esc_html_e( 'Use these settings to control the permalink used specifically for portfolio.', 'sober' ); ?>
		</p>

		<table class="form-table sober-portfolio-permalink-structure">
			<tbody>
			<tr>
				<th>
					<label><input name="portfolio_permalink" type="radio"
								  value="<?php echo esc_attr( $structures[0] ); ?>" <?php checked( $structures[0], $portfolio_permalink ); ?>
								  class="sober-portfolio-base" /> <?php esc_html_e( 'Default', 'sober' ); ?>
					</label>
				</th>
				<td>
					<code class="default-example"><?php echo esc_html( home_url() ); ?>/?portfolio=sample-portfolio</code>
					<code class="non-default-example"><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $portfolio_base ); ?>/sample-portfolio/</code>
				</td>
			</tr>
			<?php if ( $base_slug !== $portfolio_base ) : ?>
				<tr>
					<th>
						<label><input name="portfolio_permalink" type="radio"
									  value="<?php echo esc_attr( $structures[1] ); ?>" <?php checked( $structures[1], $portfolio_permalink ); ?>
									  class="sober-portfolio-base" /> <?php esc_html_e( 'Portfolio base', 'sober' ); ?>
						</label>
					</th>
					<td>
						<code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?>/sample-portfolio/</code>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<th>
					<label><input name="portfolio_permalink" type="radio"
								  value="<?php echo esc_attr( $structures[2] ); ?>" <?php checked( $structures[2], $portfolio_permalink ); ?>
								  class="sober-portfolio-base" /> <?php esc_html_e( 'Portfolio base with type', 'sober' ); ?>
					</label>
				</th>
				<td>
					<code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?>/portfolio-type/sample-portfolio/</code>
				</td>
			</tr>
			<tr>
				<th>
					<label><input name="portfolio_permalink" id="sober_portfolio_custom_selection" type="radio"
								  value="custom" <?php checked( in_array( $portfolio_permalink, $structures ), false ); ?> /> <?php esc_html_e( 'Custom Base', 'sober' ); ?>
					</label>
				</th>
				<td>
					<code><?php echo esc_html( home_url() ); ?></code>
					<input name="portfolio_permalink_structure" id="sober_portfolio_permalink_structure" type="text"
						   value="<?php echo esc_attr( $portfolio_permalink ); ?>" class="regular-text code">
				</td>
			</tr>
			</tbody>
		</table>

		<script type="text/javascript">
			jQuery( function () {
				jQuery( 'input.sober-portfolio-base' ).change( function () {
					jQuery( '#sober_portfolio_permalink_structure' ).val( jQuery( this ).val() );
				} );
				jQuery( '.permalink-structure input' ).change( function () {
					jQuery( '.sober-portfolio-permalink-structure' ).find( 'code.non-default-example, code.default-example' ).hide();
					if ( jQuery( this ).val() ) {
						jQuery( '.sober-portfolio-permalink-structure code.non-default-example' ).show();
						jQuery( '.sober-portfolio-permalink-structure input' ).removeAttr( 'disabled' );
					} else {
						jQuery( '.sober-portfolio-permalink-structure code.default-example' ).show();
						jQuery( '.sober-portfolio-permalink-structure input:eq(0)' ).click();
						jQuery( '.sober-portfolio-permalink-structure input' ).attr( 'disabled', 'disabled' );
					}
				} );
				jQuery( '.permalink-structure input:checked' ).change();
				jQuery( '#sober_portfolio_permalink_structure' ).focus( function () {
					jQuery( '#sober_portfolio_custom_selection' ).click();
				} );
			} );
		</script>
		<?php
	}

	/**
	 * HTML code to display a checkbox true/false option
	 * for the Portfolio CPT setting.
	 */
	public function enable_field_html() {
		?>

		<label for="<?php echo esc_attr( $this->option ); ?>">
			<input name="<?php echo esc_attr( $this->option ); ?>"
				   id="<?php echo esc_attr( $this->option ); ?>" <?php checked( get_option( $this->option ), true ); ?>
				   type="checkbox" value="1" />
			<?php esc_html_e( 'Enable Portfolio Projects for this site.', 'sober' ); ?>
		</label>

		<?php
	}

	/**
	 * HTML code to display a drop-down of option for portfolio page
	 */
	public function page_field_html() {
		wp_dropdown_pages( array(
			'selected'          => get_option( $this->option . '_page_id' ),
			'name'              => $this->option . '_page_id',
			'show_option_none'  => esc_html__( '&mdash; Select &mdash;', 'sober' ),
			'option_none_value' => 0,
		) );
	}

	/**
	 * HTML code to display a input of option for portfolio items per page
	 */
	public function per_page_field_html() {
		$name = $this->option . '_posts_per_page';
		?>

		<label for="portfolio_posts_per_page">
			<input name="<?php echo esc_attr( $name ) ?>" id="portfolio_items_per_page" type="number" step="1" min="1"
				   value="<?php echo esc_attr( get_option( $name, '9' ) ) ?>" class="small-text" />
			<?php _ex( 'items', 'Portfolio items per page', 'sober' ) ?>
		</label>

		<?php
	}

	/**
	 * HTML code to display a input of option for portfolio type slug
	 */
	public function portfolio_type_slug_field_html() {
		$permalinks = get_option( $this->option . '_permalinks' );
		$type_base  = isset( $permalinks['portfolio_type_base'] ) ? $permalinks['portfolio_type_base'] : '';
		?>
		<input name="portfolio_type_slug" id="portfolio_type_slug" type="text"
			   value="<?php echo esc_attr( $type_base ) ?>"
			   placeholder="<?php echo esc_attr( _x( 'portfolio-type', 'Portfolio type base', 'sober' ) ) ?>"
			   class="regular-text code">
		<?php
	}

	/**
	 * Save the settings for permalink
	 * Settings api does not trigger save for the permalink page.
	 */
	public function save_settings() {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! $screen = get_current_screen() ) {
			return;
		}

		if ( 'options-permalink' != $screen->id ) {
			return;
		}

		$permalinks = get_option( $this->option . '_permalinks' );

		if ( ! $permalinks ) {
			$permalinks = array();
		}

		if ( isset( $_POST['portfolio_type_slug'] ) ) {
			$permalinks['portfolio_type_base'] = $this->sanitize_permalink( trim( $_POST['portfolio_type_slug'] ) );
		}

		if ( isset( $_POST['portfolio_permalink'] ) ) {
			$portfolio_permalink = sanitize_text_field( $_POST['portfolio_permalink'] );

			if ( 'custom' === $portfolio_permalink ) {
				if ( isset( $_POST['portfolio_permalink_structure'] ) ) {
					$portfolio_permalink = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', trim( $_POST['portfolio_permalink_structure'] ) ) );
				} else {
					$portfolio_permalink = '/';
				}

				// This is an invalid base structure and breaks pages.
				if ( '%portfolio_type%' == $portfolio_permalink ) {
					$portfolio_permalink = '/' . _x( 'portfolio', 'slug', 'sober' ) . '/' . $portfolio_permalink;
				}
			} elseif ( empty( $portfolio_permalink ) ) {
				$portfolio_permalink = false;
			}

			$permalinks['portfolio_base'] = $this->sanitize_permalink( $portfolio_permalink );

			// Portfolio base may require verbose page rules if nesting pages.
			$portfolio_page_id   = get_option( $this->option . '_page_id' );
			$portfolio_permalink = ( $portfolio_page_id > 0 && get_post( $portfolio_page_id ) ) ? get_page_uri( $portfolio_page_id ) : _x( 'portfolio', 'Default slug', 'sober' );

			if ( $portfolio_page_id && trim( $permalinks['portfolio_base'], '/' ) === $portfolio_permalink ) {
				$permalinks['use_verbose_page_rules'] = true;
			}
		}

		update_option( $this->option . '_permalinks', $permalinks );
	}

	/**
	 * Follow CPT reading setting on CPT archive and taxonomy pages
	 */
	public function query_reading_setting( $query ) {
		if ( ! is_admin() &&
		     $query->is_main_query() &&
		     ( $query->is_post_type_archive( 'portfolio' ) || $query->is_tax( 'portfolio_type' ) )
		) {
			$query->set( 'posts_per_page', get_option( $this->option . '_posts_per_page', '9' ) );
		}

		if ( ! is_admin() && $query->is_page() ) {
			$portfolio_page_id = intval( get_option( $this->option . '_page_id' ) );
			$permalinks          = get_option( $this->option . '_permalinks' );

			// Fix for verbose page rules
			if ( $permalinks['use_verbose_page_rules'] && isset( $query->queried_object->ID ) && $query->queried_object->ID === $portfolio_page_id ) {
				$query->set( 'post_type', $this->post_type );
				$query->set( 'page', '' );
				$query->set( 'pagename', '' );

				// Fix conditional Functions
				$query->is_archive           = true;
				$query->is_post_type_archive = true;
				$query->is_singular          = false;
				$query->is_page              = false;
			}
		}
	}

	/**
	 * Sanitize permalink
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function sanitize_permalink( $value ) {
		global $wpdb;

		$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );

		if ( is_wp_error( $value ) ) {
			$value = '';
		}

		$value = esc_url_raw( $value );
		$value = str_replace( 'http://', '', $value );

		return untrailingslashit( $value );
	}

	/**
	 * Various rewrite rule fixes.
	 *
	 * @param array $rules
	 *
	 * @return array
	 */
	function rewrite_rules( $rules ) {
		global $wp_rewrite;

		$permalinks          = get_option( $this->option . '_permalinks' );
		$portfolio_permalink = empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'sober' ) : $permalinks['portfolio_base'];

		// Fix the rewrite rules when the portfolio permalink have %portfolio_type% flag.
		if ( preg_match( '`/(.+)(/%portfolio_type%)`', $portfolio_permalink, $matches ) ) {
			foreach ( $rules as $rule => $rewrite ) {
				if ( preg_match( '`^' . preg_quote( $matches[1], '`' ) . '/\(`', $rule ) && preg_match( '/^(index\.php\?portfolio_type)(?!(.*portfolio))/', $rewrite ) ) {
					unset( $rules[ $rule ] );
				}
			}
		}

		// If the portfolio page is used as the base, we need to enable verbose rewrite rules or sub pages will 404.
		if ( ! empty( $permalinks['use_verbose_page_rules'] ) && ( $portfolio_page_id = get_option( $this->option . '_page_id' ) ) ) {
			$page_rewrite_rules = array();
			$subpages           = $this->get_page_children( $portfolio_page_id );

			// Subpage rules
			foreach ( $subpages as $subpage ) {
				$uri = get_page_uri( $subpage );
				$page_rewrite_rules[ $uri . '/?$' ] = 'index.php?pagename=' . $uri;
				$wp_generated_rewrite_rules         = $wp_rewrite->generate_rewrite_rules( $uri, EP_PAGES, true, true, false, false );
				foreach ( $wp_generated_rewrite_rules as $key => $value ) {
					$wp_generated_rewrite_rules[ $key ] = $value . '&pagename=' . $uri;
				}
				$page_rewrite_rules = array_merge( $page_rewrite_rules, $wp_generated_rewrite_rules );
			}

			// Merge with rules
			$rules = array_merge( $page_rewrite_rules, $rules );
		}

		return $rules;
	}

	/**
	 * Prevent portfolio attachment links from breaking when using complex rewrite structures.
	 *
	 * @param  string $link
	 * @param  int    $post_id
	 *
	 * @return string
	 */
	public function portfolio_attachment_link( $link, $post_id ) {
		global $wp_rewrite;

		$post = get_post( $post_id );
		if ( 'portfolio' === get_post_type( $post->post_parent ) ) {
			$permalinks          = get_option( $this->option . '_permalinks' );
			$portfolio_permalink = empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'sober' ) : $permalinks['portfolio_base'];
			if ( preg_match( '/\/(.+)(\/%portfolio_type%)$/', $portfolio_permalink, $matches ) ) {
				$link = home_url( '/?attachment_id=' . $post->ID );
			}
		}

		return $link;
	}

	/**
	 * Handle redirects before content is output - hooked into template_redirect so is_page works.
	 */
	public function template_redirect() {
		if ( ! is_page() ) {
			return;
		}

		// When default permalinks are enabled, redirect portfolio page to post type archive url
		if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && $_GET['page_id'] == get_option( $this->option . '_page_id' ) ) {
			wp_safe_redirect( get_post_type_archive_link( $this->post_type ) );
			exit;
		}
	}

	/**
	 * Filter to allow portfolio_type in the permalinks for portfolios.
	 *
	 * @param  string  $permalink The existing permalink URL.
	 * @param  WP_Post $post
	 *
	 * @return string
	 */
	public function portfolio_post_type_link( $permalink, $post ) {
		// Abort if post is not a portfolio.
		if ( $post->post_type !== 'portfolio' ) {
			return $permalink;
		}

		// Abort early if the placeholder rewrite tag isn't in the generated URL.
		if ( false === strpos( $permalink, '%' ) ) {
			return $permalink;
		}

		// Get the custom taxonomy terms in use by this post.
		$terms = get_the_terms( $post->ID, 'portfolio_type' );

		if ( ! empty( $terms ) ) {
			if ( function_exists( 'wp_list_sort' ) ) {
				$terms = wp_list_sort( $terms, 'term_id', 'ASC' );
			} else {
				usort( $terms, '_usort_terms_by_ID' );
			}
			$type_object    = get_term( $terms[0], 'portfolio_type' );
			$portfolio_type = $type_object->slug;

			if ( $type_object->parent ) {
				$ancestors = get_ancestors( $type_object->term_id, 'portfolio_type' );

				foreach ( $ancestors as $ancestor ) {
					$ancestor_object = get_term( $ancestor, 'portfolio_type' );
					$portfolio_type  = $ancestor_object->slug . '/' . $portfolio_type;
				}
			}
		} else {
			// If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
			$portfolio_type = _x( 'uncategorized', 'slug', 'sober' );
		}

		$find = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			'%post_id%',
			'%portfolio_type%',
		);

		$replace = array(
			date_i18n( 'Y', strtotime( $post->post_date ) ),
			date_i18n( 'm', strtotime( $post->post_date ) ),
			date_i18n( 'd', strtotime( $post->post_date ) ),
			date_i18n( 'H', strtotime( $post->post_date ) ),
			date_i18n( 'i', strtotime( $post->post_date ) ),
			date_i18n( 's', strtotime( $post->post_date ) ),
			$post->ID,
			$portfolio_type,
		);

		$permalink = str_replace( $find, $replace, $permalink );

		return $permalink;
	}

	/**
	 * Change taxonomy for breadcrumb
	 *
	 * @param array $args
	 *
	 * @return array mixed
	 */
	public function breadcrumb( $args ) {
		if ( is_singular( 'portfolio' ) ) {
			$args['taxonomy'] = 'portfolio_type';
		}

		if ( is_post_type_archive( 'portfolio' ) ) {
			$portfolio_page_id = get_option( $this->option . '_page_id' );

			if ( $portfolio_page_id && get_post( $portfolio_page_id ) ) {
				$args['labels']['archive'] = get_the_title( $portfolio_page_id );
			} else {
				$args['labels']['archive'] = _x( 'Portfolio', 'Portfolio post type breadcrumb', 'sober' );
			}
		}

		return $args;
	}

	/**
	 * Change archive title
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function archive_title( $title ) {
		if ( is_post_type_archive( 'portfolio' ) ) {
			$portfolio_page_id = get_option( $this->option . '_page_id' );

			if ( $portfolio_page_id && get_post( $portfolio_page_id ) ) {
				$title = get_the_title( $portfolio_page_id );
			} else {
				$title = _x( 'Portfolio', 'Portfolio post type breadcrumb', 'sober' );
			}
		}

		return $title;
	}

	/**
	 * Recursively get page children.
	 * @param  int $page_id
	 * @return int[]
	 */
	public function get_page_children( $page_id ) {
		$page_ids = get_posts( array(
			'post_parent' => $page_id,
			'post_type'   => 'page',
			'numberposts' => -1,
			'post_status' => 'any',
			'fields'      => 'ids',
		) );

		if ( ! empty( $page_ids ) ) {
			foreach ( $page_ids as $page_id ) {
				$page_ids = array_merge( $page_ids, $this->get_page_children( $page_id ) );
			}
		}

		return $page_ids;
	}
}