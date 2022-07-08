<?php
/**
 * A widget that holds a custom meta fieldgroup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'CFW_Fieldgroup' ) ) && ( class_exists( 'WP_Widget' ) ) ) {

	/**
	 * @uses WP_Widget
	 */
	class CFW_Fieldgroup extends WP_Widget {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound

		protected $defaults;

		/**
		 * Public constructor for Widget Class.
		 */
		public function __construct() {
			$this->defaults = array(
				'prefix'  => class_exists( 'WPE_WPS' ) ? 'SE' : 'CFW',
				'title'   => '',
				'images' => 'on',
			);

			parent::__construct(
				'cfw_fieldgroup', // Base ID
				'(' . $this->defaults['prefix'] . ') ' . esc_attr__( 'Fieldgroup', 'custom-field-widgets' ), // Name
				array( 'description' => esc_attr__( 'A complete custom fieldgroup, in a widget', 'custom-field-widgets' ) ) // Args
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

			if ( ! empty( $instance['fieldgroup'] ) ) {
				$fieldgroup = acf_get_field_group( $instance['fieldgroup'] );
			}

            if ( ! cfw_fieldgroup_has_values( $instance['fieldgroup'] ) ) {
                 return;
            }

			if ( ! empty( $instance['title'] ) ) {
				$title = apply_filters( 'widget_title', $instance['title'] );
			} else {
				$title = apply_filters( 'widget_title', $fieldgroup['title'] );
            }

			if ( ! empty( $instance['images'] ) && 'on' === $instance['images']  ) {
				$images = true;
			} else {
				$images = false;
			}

			echo $before_widget;

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			cfw_get_fieldgroup_template( $instance['fieldgroup'], $images );

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
			$instance               = $old_instance;
			$instance['title']      = strip_tags( $new_instance['title'] );
			$instance['images']     = $new_instance['images'];
			$instance['fieldgroup'] = $new_instance['fieldgroup'];

			return $instance;
		}

		/**
		 * Display widget form in admin
		 *
		 * @param array $instance widget instance.
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );
			$selected_fieldgroup = ! empty( $instance['fieldgroup'] ) ? $instance['fieldgroup'] : null;
			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title (or leave blank to show fieldgroup name)', 'wpe-wps' ); ?></label>
				<input class="widefat"  id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>
				<label for="<?php echo $this->get_field_id( 'fieldgroup' ); ?>"><?php esc_html_e( 'Select fieldgroup:', 'custom-field-widgets' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'fieldgroup' ); ?>" name="<?php echo $this->get_field_name( 'fieldgroup' ); ?>">
					<?php

					$fieldgroups = cfw_get_fieldgroups();

					foreach ( $fieldgroups as $fieldgroup => $value ) {
						$selected = $fieldgroup == $selected_fieldgroup ? 'selected="selected"' : '';
						echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $fieldgroup ) . '">' . esc_html( $value ) . '</option>';
					}
					?>
				</select>
			</p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked( $instance[ 'images' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'images' ); ?>" name="<?php echo $this->get_field_name( 'images' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'images' ); ?>"><?php esc_html_e( 'Show Images?', 'wpe-wps' ); ?></label>
            </p>

			<?php
		}
	}

	add_action(
		'widgets_init',
		function() {
			return register_widget( 'CFW_Fieldgroup' );
		}
	);
}
