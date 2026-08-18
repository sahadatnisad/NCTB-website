<?php
/**
 * Central plugin loader / orchestrator.
 *
 * Singleton that wires the plugin's modules to WordPress. In Phase 0 it only
 * loads translations, guards upgrades, and instantiates empty admin/public
 * placeholders. Later phases register their services here (curriculum,
 * question engine, mastery, entitlements, AI adapter, REST controllers)
 * without touching the main plugin file.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Plugin
 */
final class NCTB_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var NCTB_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get the shared instance.
	 *
	 * @return NCTB_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor — use instance().
	 */
	private function __construct() {}

	/**
	 * Register hooks and load modules. Idempotent.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Catch schema upgrades applied by replacing files (no reactivation).
		add_action( 'admin_init', array( 'NCTB_Migrations', 'run' ) );

		$this->load_admin();
		$this->load_public();

		/**
		 * Fires after the core plugin has booted. Later phases and add-ons
		 * hook here to register their own services.
		 */
		do_action( 'nctb_lh_loaded' );
	}

	/**
	 * Load translation files.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'nctb-learning-hub',
			false,
			dirname( NCTB_LH_BASENAME ) . '/languages'
		);
	}

	/**
	 * Load the admin-side placeholder (only in wp-admin).
	 *
	 * @return void
	 */
	private function load_admin() {
		if ( ! is_admin() ) {
			return;
		}
		require_once NCTB_LH_PATH . 'admin/class-nctb-admin.php';
		new NCTB_Admin();
	}

	/**
	 * Load the public/front-end placeholder.
	 *
	 * @return void
	 */
	private function load_public() {
		require_once NCTB_LH_PATH . 'public/class-nctb-public.php';
		new NCTB_Public();
	}
}
