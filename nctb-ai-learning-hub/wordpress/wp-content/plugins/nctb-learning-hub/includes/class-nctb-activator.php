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

		// Apply versioned schema (no tables in Phase 0).
		NCTB_Migrations::run();

		// Rewrite rules — harmless now, required once CPTs/endpoints exist.
		flush_rewrite_rules();

		NCTB_Logger::info( 'Plugin activated', array( 'version' => NCTB_LH_VERSION ) );
	}
}
