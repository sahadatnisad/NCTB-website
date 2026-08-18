<?php
/**
 * Deactivation routine.
 *
 * Reverses only transient runtime state (scheduled events, rewrite rules).
 * It MUST NOT delete student data, options or tables — data destruction
 * belongs in uninstall.php and only when the site owner deletes the plugin.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Deactivator
 */
class NCTB_Deactivator {

	/**
	 * Perform deactivation.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Clear rewrite rules so removed endpoints stop resolving.
		flush_rewrite_rules();

		NCTB_Logger::info( 'Plugin deactivated' );
	}
}
