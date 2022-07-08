<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One class that rules them all.
 */
class CFW_Init {
	public static function instance() {

		// Store the instance locally to avoid private static replication.
		static $instance = null;

		// Only run these methods if they haven't been run previously.
		if ( null === $instance ) {
			$instance = new CFW_Init();
			$instance->constants();
			$instance->init();
			$instance->widgets();
		}

		// Always return the instance.
		return $instance;
	}

	/**
	 * CFW_Init constructor.
	 * Nothing to see here, move along...
	 */
	public function __construct() {}

	/**
	 * Bootstrap constants.
	 */
	private function constants() {

		// Define a constant that we can use to construct file paths throughout the component
		define( 'CFW_PLUGIN_DIR', plugin_dir_url( __DIR__ ) );
		define( 'CFW_INCLUDES_DIR', CFW_PLUGIN_DIR . 'includes/' );
		define( 'CFW_ASSETS_DIR', CFW_PLUGIN_DIR . 'assets/' );
	}

	/**
	 * Do some enqueuing. Very lightweight.
	 */
	private function init() {
		// Require our functions file
		require_once __DIR__ . '/cfw-functions.php';

		// Require our templates file
		require_once __DIR__ . '/cfw-templates.php';

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueues' ) );
	}

	/**
	 * Do some enqueuing. Very lightweight.
	 */
	public function enqueues() {
		wp_enqueue_style( 'cfw-styles', CFW_ASSETS_DIR . 'styles.css', array(), '1.0.0', 'all' );
	}

	/**
	 * Init the init, innit. Seriously though, this is where we boot up all the widgets the plugin makes available.
	 */
	private function widgets() {
		require_once __DIR__ . '/widgets/class-cfw-image-full.php';
		require_once __DIR__ . '/widgets/class-cfw-image-box.php';
		require_once __DIR__ . '/widgets/class-cfw-fieldgroup.php';
		require_once __DIR__ . '/widgets/class-cfw-field.php';
	}
}
