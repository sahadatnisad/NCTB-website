<?php
/**
 * Activation routine.
 *
 * Runs once when the plugin is activated. Keeps side effects minimal and
 * reversible: seed baseline options, run the (currently empty) migration
 * runner, and flush rewrite rules so any custom post types / endpoints added
 * in later phases resolve immediately.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Activator
 */
class NCTB_Activator {

	/**
	 * Perform activation.
	 *
	 * @return void
	 */
	public static function activate() {
		// Record install time once, without autoloading it on every request.
		if ( false === get_option( 'nctb_lh_installed_at' ) ) {
			add_option( 'nctb_lh_installed_at', time(), '', false );
		}

		// Apply versioned schema (Phase 3 adds curriculum tables).
		NCTB_Migrations::run();

		// Register roles/caps so editors can manage curriculum immediately.
		NCTB_Roles::register_roles();

		// Register CPTs now so their rewrite rules are included in the flush.
		NCTB_Curriculum_CPT::register();

		// Seed a single sample book/unit/lesson tree for the browse demo (once).
		NCTB_Curriculum_Seeder::maybe_seed();

		// Rewrite rules — required now that CPTs exist.
		flush_rewrite_rules();

		NCTB_Logger::info( 'Plugin activated', array( 'version' => NCTB_LH_VERSION ) );
	}
}
