<?php
/**
 * Front-end placeholder.
 *
 * PHASE 0: intentionally minimal. Exists so later phases have a stable class
 * to extend (lesson rendering data, practice endpoints, tutor drawer asset
 * loading). No student-facing output or asset enqueues are added yet — assets
 * must only load on pages that need them (performance rule).
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Public
 */
class NCTB_Public {

	/**
	 * Wire front-end hooks.
	 */
	public function __construct() {
		// Placeholder for Phase 1+ (shortcodes/blocks, conditional asset loading).
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}
}
