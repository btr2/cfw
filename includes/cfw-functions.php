<?php
/**
 * A set of functions to use around the plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** ACF ***************************************************************************************************************/

/**
 * Get all fieldgroups.
 *
 * @return array
 */
function cfw_get_fieldgroups() {
	$options = array();

	$field_groups = acf_get_field_groups();

	foreach ( $field_groups as $group ) {
		$options[ $group['ID'] ] = $group['title'];
	}

	return $options;
}

/**
 * Check if all fieldgroup values for a post are set.
 *
 * @param $fieldgroup_id
 *
 * @return bool
 */
function cfw_fieldgroup_has_values( $fieldgroup_id ) {
	$options = array();

	$fields = get_posts(
		array(
			'posts_per_page'         => - 1,
			'post_type'              => 'acf-field',
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'suppress_filters'       => true, // DO NOT allow WPML to modify the query
			'post_parent'            => $fieldgroup_id,
			'post_status'            => 'any',
			'update_post_meta_cache' => false,
		)
	);

	foreach ( $fields as $field_raw ) {
		$field = get_field( $field_raw->post_name );

		$options[] = $field;
	}

	$options = array_filter( $options );

	if ( empty( $options ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Get array of field objects.
 *
 * @return array
 */
function cfw_get_field_objects() {
	$options = array();

	$field_groups = acf_get_field_groups();

	foreach ( $field_groups as $group ) {
		// DO NOT USE here: $fields = acf_get_fields($group['key']);
		// because it causes repeater field bugs and returns "trashed" fields
		$fields = get_posts(
			array(
				'posts_per_page'         => - 1,
				'post_type'              => 'acf-field',
				'orderby'                => 'menu_order',
				'order'                  => 'ASC',
				'suppress_filters'       => true, // DO NOT allow WPML to modify the query
				'post_parent'            => $group['ID'],
				'post_status'            => 'any',
				'update_post_meta_cache' => false,
			)
		);
		foreach ( $fields as $field ) {
			$options[] = get_field_object( $field->post_name );
		}
	}

	return $options;
}

/**
 * Get all fields.
 *
 * @return array
 */
function cfw_get_fields() {
	$options = array();

	$field_groups = acf_get_field_groups();

	foreach ( $field_groups as $group ) {
		// DO NOT USE here: $fields = acf_get_fields($group['key']);
		// because it causes repeater field bugs and returns "trashed" fields
		$fields = get_posts(
			array(
				'posts_per_page'         => - 1,
				'post_type'              => 'acf-field',
				'orderby'                => 'menu_order',
				'order'                  => 'ASC',
				'suppress_filters'       => true, // DO NOT allow WPML to modify the query
				'post_parent'            => $group['ID'],
				'post_status'            => 'any',
				'update_post_meta_cache' => false,
			)
		);
		foreach ( $fields as $field ) {
			$options[ $field->post_name ] = $field->post_title;
		}
	}

	return $options;
}

/**
 * Get all image fields.
 *
 * @return array
 */
function cfw_get_image_fields() {
	$options = array();

	$field_groups = acf_get_field_groups();
	foreach ( $field_groups as $group ) {
		// DO NOT USE here: $fields = acf_get_fields($group['key']);
		// because it causes repeater field bugs and returns "trashed" fields
		$fields = get_posts(
			array(
				'posts_per_page'         => - 1,
				'post_type'              => 'acf-field',
				'orderby'                => 'menu_order',
				'order'                  => 'ASC',
				'suppress_filters'       => true, // DO NOT allow WPML to modify the query
				'post_parent'            => $group['ID'],
				'post_status'            => 'any',
				'update_post_meta_cache' => false,
			)
		);
		foreach ( $fields as $field_raw ) {
			$field = get_field_object( $field_raw->post_name );

			if ( 'image' !== $field['type'] ) {
				continue;
			}

			$options[ $field_raw->post_name ] = $field_raw->post_title;
		}
	}

	return $options;
}
