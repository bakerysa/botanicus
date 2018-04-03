<?php

class Sober_Social_Links_Widget extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $default;

	/**
	 * List of supported socials
	 *
	 * @var array
	 */
	protected $socials;

	/**
	 * Constructor
	 */
	function __construct() {
		$socials = array(
			'facebook'    => esc_html__( 'Facebook', 'sober' ),
			'twitter'     => esc_html__( 'Twitter', 'sober' ),
			'google-plus' => esc_html__( 'Google Plus', 'sober' ),
			'tumblr'      => esc_html__( 'Tumblr', 'sober' ),
			'linkedin'    => esc_html__( 'Linkedin', 'sober' ),
			'pinterest'   => esc_html__( 'Pinterest', 'sober' ),
			'flickr'      => esc_html__( 'Flickr', 'sober' ),
			'instagram'   => esc_html__( 'Instagram', 'sober' ),
			'dribbble'    => esc_html__( 'Dribbble', 'sober' ),
			'stumbleupon' => esc_html__( 'StumbleUpon', 'sober' ),
			'github'      => esc_html__( 'Github', 'sober' ),
			'youtube'     => esc_html__( 'Youtube', 'sober' ),
			'houzz'       => esc_html__( 'Houzz', 'sober' ),
			'rss'         => esc_html__( 'RSS', 'sober' ),
		);

		$this->socials = apply_filters( 'sober_social_media', $socials );
		$this->default = array(
			'title' => '',
		);
		foreach ( $this->socials as $k => $v ) {
			$this->default["{$k}_title"] = $v;
			$this->default["{$k}_url"]   = '';
		}

		parent::__construct(
			'social-links-widget',
			esc_html__( 'Sober - Social Links', 'sober' ),
			array(
				'classname'   => 'social-links-widget social-links',
				'description' => esc_html__( 'Display links to social media networks.', 'sober' ),
			),
			array( 'width' => 600 )
		);
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->default );

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		foreach ( $this->socials as $social => $label ) {
			if ( ! empty( $instance[$social . '_url'] ) ) {
				$icon = 'youtube' == $social ? 'youtube-play' : $social;

				printf(
					'<a href="%s" class="share-%s tooltip-enable social" rel="nofollow" title="%s" data-toggle="tooltip" data-placement="top" target="_blank"><i class="fa fa-%s"></i></a>',
					esc_url( $instance[$social . '_url'] ),
					esc_attr( $social ),
					esc_attr( $instance[$social . '_title'] ),
					esc_attr( $icon )
				);
			}
		}

		echo $args['after_widget'];
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->default );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'sober' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<?php
		foreach ( $this->socials as $social => $label ) {
			printf(
				'<div style="width: 280px; float: left; margin-right: 10px;">
					<label>%s</label>
					<p><input type="text" class="widefat" name="%s" placeholder="%s" value="%s"></p>
				</div>',
				$label,
				$this->get_field_name( $social . '_url' ),
				esc_html__( 'URL', 'sober' ),
				$instance[$social . '_url']
			);
		}
	}
}
