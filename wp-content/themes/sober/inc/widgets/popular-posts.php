<?php

class Sober_Popular_Posts_Widget extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Class constructor
	 * Set up the widget
	 */
	function __construct() {
		$this->defaults = array(
			'title'         => '',
			'limit'         => 3,
			'show_thumb'    => 1,
			'show_date'     => 1,
		);

		parent::__construct(
			'popular-posts-widget',
			esc_html__( 'Sober - Popular Post', 'sober' ),
			array(
				'classname'   => 'popular-posts-widget',
				'description' => esc_html__( 'Display most popular posts', 'sober' ),
			)
		);
	}

	/**
	 * Display widget
	 *
	 * @param array $args     Sidebar configuration
	 * @param array $instance Widget settings
	 *
	 * @return void
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );

		$query_args = array(
			'posts_per_page'      => $instance['limit'],
			'post_type'           => 'post',
			'orderby'             => 'comment_count',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
		);

		$query = new WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			return;
		}

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		while ( $query->have_posts() ) : $query->the_post();
			$class = $instance['show_thumb'] && has_post_thumbnail() ? '' : 'no-thumbnail';
			?>
			<div class="popular-post post clearfix <?php echo esc_attr( $class ); ?>">
				<?php
				if ( ! $class ) {
					printf(
						'<a class="widget-thumb" href="%s" rel="bookmark">%s</a>',
						esc_url( get_permalink() ),
						get_the_post_thumbnail( get_the_ID(), 'sober-widget-thumbnail' )
					);
				}
				?>

				<h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
				<?php
				if ( $instance['show_date'] ) {
					echo '<time class="entry-date" datetime="' . esc_attr( get_the_time( 'c' ) ) . '">' . get_the_date( 'd.m Y' ) . '</time>';
				}
				?>
			</div>
			<?php
		endwhile;
		wp_reset_postdata();

		echo $args['after_widget'];
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance New widget settings
	 * @param array $old_instance Old widget settings
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$new_instance['title']         = strip_tags( $new_instance['title'] );
		$new_instance['limit']         = intval( $new_instance['limit'] );
		$new_instance['show_thumb']    = ! empty( $new_instance['show_thumb'] );
		$new_instance['show_date']     = ! empty( $new_instance['show_date'] );

		return $new_instance;
	}

	/**
	 * Display widget settings
	 *
	 * @param array $instance Widget settings
	 *
	 * @return void
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'sober' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number Of Posts', 'sober' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" size="2" value="<?php echo intval( $instance['limit'] ); ?>">
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_date'] ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>"><?php esc_html_e( 'Show Date', 'sober' ); ?></label>
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumb' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_thumb'] ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>"><?php esc_html_e( 'Show Thumbnail', 'sober' ); ?></label>
		</p>

		<?php
	}
}