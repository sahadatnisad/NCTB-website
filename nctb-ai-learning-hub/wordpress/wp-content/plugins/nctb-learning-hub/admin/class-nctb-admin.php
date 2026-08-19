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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue admin scripts & styles on lesson edit screens.
	 *
	 * @param string $hook_suffix Current admin page.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || NCTB_Curriculum_CPT::CPT_LESSON !== $screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'nctb-admin-activities',
			NCTB_LH_URL . 'admin/css/nctb-admin-activities.css',
			array(),
			NCTB_LH_VERSION
		);

		wp_enqueue_script(
			'nctb-admin-activities',
			NCTB_LH_URL . 'admin/js/nctb-admin-activities.js',
			array( 'jquery' ),
			NCTB_LH_VERSION,
			true
		);
	}
}
