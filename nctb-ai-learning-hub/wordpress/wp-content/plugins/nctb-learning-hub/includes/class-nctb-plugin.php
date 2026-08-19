<?php
/**
 * Central plugin loader / orchestrator.
 *
 * Singleton that wires the plugin's modules to WordPress.
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
	 * Private constructor.
	 */
	private function __construct() {}

	/**
	 * Register hooks and load modules.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( 'NCTB_Roles', 'register_roles' ) );

		// Curriculum backbone (CPTs, taxonomies, editor meta boxes, admin).
		$this->load_curriculum();

		// Register REST API endpoints.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		// Schema upgrades.
		add_action( 'admin_init', array( 'NCTB_Migrations', 'run' ) );

		// Ensure required pages exist after an in-place file upgrade (once).
		add_action( 'admin_init', array( 'NCTB_Pages', 'maybe_provision' ) );

		$this->load_admin();
		$this->load_public();

		/**
		 * Fires after the core plugin has booted.
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
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_rest_routes() {
		$onboarding_rest = new NCTB_Onboarding_REST();
		$onboarding_rest->register_routes();

		$curriculum_rest = new NCTB_Curriculum_REST();
		$curriculum_rest->register_routes();

		$practice_rest = new NCTB_Practice_REST();
		$practice_rest->register_routes();
	}

	/**
	 * Instantiate the curriculum modules.
	 *
	 * CPTs/taxonomies register on all requests; meta boxes, concepts, and
	 * questions admin screens load in wp-admin.
	 *
	 * @return void
	 */
	private function load_curriculum() {
		$cpt = new NCTB_Curriculum_CPT();
		$cpt->init();

		if ( is_admin() ) {
			$meta = new NCTB_Curriculum_Meta();
			$meta->init();

			$admin = new NCTB_Curriculum_Admin();
			$admin->init();

			$q_admin = new NCTB_Question_Admin();
			$q_admin->init();
		}
	}

	/**
	 * Load the admin-side controller.
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
	 * Load the public/front-end controller.
	 *
	 * @return void
	 */
	private function load_public() {
		require_once NCTB_LH_PATH . 'public/class-nctb-public.php';
		new NCTB_Public();
	}
}
