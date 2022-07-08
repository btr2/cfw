<?php
/**
 * Plugin Name: Custom Field Widgets
 * Plugin URI:  https://www.bouncingsprout.com/
 * Description: Custom Field Widgets display your post's custom field metadata inside a widget. And that's it!
 * Author:      Ben Roberts
 * Author URI:  https://www.bouncingsprout.com/
 * Version:     1.0.0
 * Text Domain: custom-field-widgets
 * Domain Path: /languages/
 * License: GPLv2

Copyright 2022  Ben Roberts

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Start the engines, Captain...
 *
 * @return CFW_Init|null
 */
function cfw_init() {
	if ( ! function_exists( 'get_field' ) ) {
		add_action( 'admin_notices', 'cfw_acf_required_error' );
		return;
	}

	require_once dirname( __FILE__ ) . '/includes/class-init.php';

	return CFW_Init::instance();
}
add_action( 'plugins_loaded', 'cfw_init' );

/**
 * Activate CFW.
 *
 * @return void
 */
function cfw_activate() {
	if ( ! function_exists( 'get_field' ) ) {
		add_action( 'admin_notices', 'cfw_acf_required_error' );
	}

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cfw_activate' );

function cfw_acf_required_error() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Custom Field Widgets requires Advanced Custom Fields to run. Please ensure it is activated.',
				'custom-field-widgets' ); ?></p>
	</div>
	<?php
}
