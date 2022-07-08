<?php
/**
 * A widget that holds a custom meta image, in an unbordered widget area
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'CFW_Image_Full' ) ) && ( class_exists( 'WP_Widget' ) ) ) {

	/**
	 *
	 *
	 * @uses WP_Widget
	 */
	class CFW_Image_Full extends WP_Widget {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound

		protected $defaults;

		/**
		 * Public constructor for Widget Class.
		 */
		public function __construct() {
			$this->defaults = array(
				'prefix' => class_exists( 'WPE_WPS' ) ? 'SE' : 'BS',
			);

			parent::__construct(
				'cfw_image_full', // Base ID
				'(' . $this->defaults['prefix'] . ') ' . esc_attr__( 'Single Image (Full)', 'custom-field-widgets' ), // Name
				array( 'description' => esc_attr__( 'An unbordered single image', 'custom-field-widgets' ) ) // Args
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

			if ( ! empty( $instance['postmeta_field'] ) ) {
				$postmeta_field = $instance['postmeta_field'];
			}

			$image = $postmeta_field ? get_field( $postmeta_field ) : false;

			if ( ! $image || ! $image['url'] ) {
				return;
			}

			echo $before_widget;

			// Image variables.
			$url   = $image['url'];
			$title = $image['title'];
			$alt   = $image['alt'];
			$size  = 'large';
            $thumb = $image['sizes'][ $size ];
			?>


			<a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( $title ); ?>">
				<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
			</a>

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
			<?php
		}
	}

	add_action(
		'widgets_init',
		function() {
			return register_widget( 'CFW_Image_Full' );
		}
	);
}
