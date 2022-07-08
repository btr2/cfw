<?php
/**
 * A widget that holds a custom meta field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'CFW_Field' ) ) && ( class_exists( 'WP_Widget' ) ) ) {

	/**
	 * @uses WP_Widget
	 */
	class CFW_Field extends WP_Widget {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound

		protected $defaults;

		/**
		 * Public constructor for Widget Class.
		 */
		public function __construct() {
			$this->defaults = array(
				'prefix' => class_exists( 'WPE_WPS' ) ? 'SE' : 'CFW',
				'title'  => '',
			);

			parent::__construct(
				'cfw_field', // Base ID
				'(' . $this->defaults['prefix'] . ') ' . esc_attr__( 'Field', 'custom-field-widgets' ), // Name
				array( 'description' => esc_attr__( 'A custom field, in a widget', 'custom-field-widgets' ) ) // Args
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

			if ( ! empty( $instance['field'] ) ) {
				$value = get_field( $instance['field'] );
			}

			if ( ! $value ) {
				return;
			} else {
				$field = acf_get_field( $instance['field'] );
			}

			if ( ! empty( $instance['title'] ) ) {
				$title = apply_filters( 'widget_title', $instance['title'] );
			} else {
				$title = apply_filters( 'widget_title', $field['label'] );
			}

			echo $before_widget;

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			echo '<p>' . esc_html( $value ) . '</p>';

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
			$instance          = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['field'] = $new_instance['field'];

			return $instance;
		}

		/**
		 * Display widget form in admin
		 *
		 * @param array $instance widget instance.
		 */
		public function form( $instance ) {
			$instance       = wp_parse_args( (array) $instance, $this->defaults );
			$selected_field = ! empty( $instance['field'] ) ? $instance['field'] : null;
			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title (or leave blank to show field name)', 'wpe-wps' ); ?></label>
				<input class="widefat"  id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>
				<label for="<?php echo $this->get_field_id( 'field' ); ?>"><?php esc_html_e( 'Select field:', 'custom-field-widgets' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'field' ); ?>" name="<?php echo $this->get_field_name( 'field' ); ?>">
					<?php

					$fields = cfw_get_field_objects();

					foreach ( $fields as $field ) {
						$selected   = $field['key'] == $selected_field ? 'selected="selected"' : '';
						$fieldgroup = acf_get_field_group( $field['parent'] );
						echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $field['key'] ) . '">' . esc_html( $field['label'] ) . ' (' . esc_html( $fieldgroup['title'] ) . ')</option>';
					}
					?>
				</select>
			</p>

			<?php
		}
	}

	add_action(
		'widgets_init',
		function() {
			return register_widget( 'CFW_Field' );
		}
	);
}
