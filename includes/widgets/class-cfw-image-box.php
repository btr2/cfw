<?php
/**
 * A widget that holds a custom meta image, inside a box, with a title etc...
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'CFW_Image_Box' ) ) && ( class_exists( 'WP_Widget' ) ) ) {

	/**
	 *
	 *
	 * @uses WP_Widget
	 */
	class CFW_Image_Box extends WP_Widget {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound

		protected $defaults;

		/**
		 * Public constructor for Widget Class.
		 */
		public function __construct() {
			$this->defaults = array(
				'prefix'  => class_exists( 'WPE_WPS' ) ? 'SE' : 'CFW',
				'title'   => '',
				'caption' => 'on',
			);

			parent::__construct(
				'cfw_image_box', // Base ID
				'(' . $this->defaults['prefix'] . ') ' . esc_attr__( 'Single Image (with text)', 'custom-field-widgets' ), // Name
				array( 'description' => esc_attr__( 'A single image in a widget area, with space for a title and text', 'custom-field-widgets' ) ) // Args
			);

		}

		/**
		 * Displays widget
		 *
		 * @param array $args     widget arguments.
		 * @param array $instance widget instance.
		 */
		public function widget( $args, $instance ) {
			extract( $args );

			if ( ! empty( $instance['title'] ) ) {
				$title = apply_filters( 'widget_title', $instance['title'] );
			}

			if ( ! empty( $instance['postmeta_field'] ) ) {
				$postmeta_field = $instance['postmeta_field'];
			}

			$image = get_field( $postmeta_field );

			if ( ! $image || ! $image['url'] ) {
				return;
			}

			echo $before_widget;

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			// Image variables.
			$url     = $image['url'];
			$title   = $image['title'];
			$alt     = $image['alt'];
			$caption = $image['caption'];

			// Thumbnail size attributes.
			$size  = 'large';
			$thumb = $image['sizes'][ $size ];
			?>


			<a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( $title ); ?>">
				<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
			</a>

			<!-- Show caption if enabled -->
			<?php if ( 'on' == $instance['caption'] && $caption ) : ?>
				<p class="wp-caption-text"><?php echo esc_html( $caption ); ?></p>
			<?php endif; ?>

			<?php

			echo $after_widget;
		}

		/**
		 * Handles widget updates in admin
		 *
		 * @param array $new_instance New instance.
		 * @param array $old_instance Old instance.
		 *
		 * @return array $instance
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                   = $old_instance;
			$instance['title']          = strip_tags( $new_instance['title'] );
			$instance['caption']        = $new_instance['caption'];
			$instance['postmeta_field'] = $new_instance['postmeta_field'];

			return $instance;
		}

		/**
		 * Display widget form in admin
		 *
		 * @param array $instance widget instance.
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$selected_field = ! empty( $instance['postmeta_field'] ) ? trim( $instance['postmeta_field'] ) : null;
			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'wpe-wps' ); ?></label>
				<input class="widefat"  id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>
				<label for="<?php echo $this->get_field_id( 'postmeta_field' ); ?>"><?php esc_html_e( 'Select custom field:', 'custom-field-widgets' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'postmeta_field' ); ?>" name="<?php echo $this->get_field_name( 'postmeta_field' ); ?>">
					<?php

					$fields = cfw_get_image_fields();

					foreach ( $fields as $field => $value ) {
						$selected = $field === $selected_field ? 'selected="selected"' : '';
						echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $field ) . '">' . esc_html( $value ) . '</option>';
					}
					?>
				</select>
			</p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked( $instance[ 'caption' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'caption' ); ?>" name="<?php echo $this->get_field_name( 'caption' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'caption' ); ?>"><?php esc_html_e( 'Show caption?', 'wpe-wps' ); ?></label>
            </p>

			<?php
		}
	}

	add_action(
		'widgets_init',
		function() {
			return register_widget( 'CFW_Image_Box' );
		}
	);
}
