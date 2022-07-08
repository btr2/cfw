<?php
/**
 * A set of templates to use around the plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate an HTML template for listing out all fields of a fieldgroup.
 *
 * @param $group
 * @param $images
 *
 * @return void
 */
function cfw_get_fieldgroup_template( $group, $images = true ) {
	if ( empty( $group ) ) {
		return;
	}

	$fields = get_posts(
		array(
			'posts_per_page'         => - 1,
			'post_type'              => 'acf-field',
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'suppress_filters'       => true, // DO NOT allow WPML to modify the query
			'post_parent'            => $group,
			'post_status'            => 'any',
			'update_post_meta_cache' => false,
		)
	);

	foreach ( $fields as $field_raw ) {
		$field = get_field_object( $field_raw->post_name );

		if ( ! $images && 'image' === $field['type'] ) {
			continue;
		}

		if ( ( '' === $field['value'] ) || ( is_array( $field['value'] ) && empty( $field['value'] ) ) ) {
			continue;
		}

		if ( ! empty( $field['spaces_about_header'] ) ) {
			continue;
		}

		switch ( $field['type'] ) {
			case 'text':
			case 'textarea':
			case 'number':
				echo '<p class="field"><span class="name">' . esc_html( $field['label'] ) . '</span>: <span class="value">' . esc_html( $field['value'] ) . '</span></p>';
				break;
			case 'true_false':
				$value = $field['value'] ? __( 'Yes', 'wpe-wps' ) : __( 'No', 'wpe-wps' );
				echo '<p class="field"><span class="name">' . esc_html( $field['label'] ) . '</span>: <span class="value">' . esc_html( $value ) . '</span></p>';
				break;
			case 'email':
				echo '<p class="field"><span class="name">' . esc_html( $field['label'] ) . '</span>: <a class="value" href="mailto:' . esc_attr( $field['value'] ) . '">' . esc_html( $field['value'] ) . '</a></p>';
				break;
			case 'url':
				echo '<p class="field"><span class="name">' . esc_html( $field['label'] ) . '</span>: <a class="value" href="' . esc_url( $field['value'] ) . '">' . esc_html( $field['value'] ) . '</a></p>';
				break;
			case 'select':
			case 'checkbox':
			case 'radio':
				$value = is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'];
				echo '<p class="field"><span class="name">' . esc_html( $field['label'] ) . '</span>: <span class="value">' . esc_html( $value ) . '</span></p>';
				break;
			case 'image':
				$image = get_field( 'image' );
				if ( ! empty( $image ) ) {
					echo '<p class="field"><span class="name">' . esc_html( $field['label'] ) . '</span>: ';
					echo '<img class="value" src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '" />';
					echo '</p>';
				}
				break;
		}
	}
}
