<?php
/**
 * Admin-side placeholder.
 *
 * PHASE 0: intentionally minimal. It exists so later phases have a stable
 * class to extend (curriculum menus, AI review queue, analytics, settings).
 * No admin pages or curriculum logic are added here yet.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Admin
 */
class NCTB_Admin {

	/**
	 * Wire admin hooks.
	 */
	public function __construct() {
		// Placeholder for Phase 3+ (curriculum CMS, settings, review queue).
		// Example extension point kept commented so activation stays clean:
		// add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}
}
