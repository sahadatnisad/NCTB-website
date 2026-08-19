<?php
/**
 * Plugin Name:       NCTB Learning Hub
 * Plugin URI:        https://example.com/nctb-learning-hub
 * Description:       Lesson-by-lesson digital companion to the Bangladesh NCTB curriculum. Contains all learning and business logic (curriculum, lessons, practice, mastery, revision, entitlements, AI tutor). Presentation lives in the theme.
 * Version:           0.12.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            NCTB AI Learning Hub
 * Author URI:        https://example.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nctb-learning-hub
 * Domain Path:       /languages
 *
 * @package NCTB\LearningHub
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Current plugin version.
 */
define( 'NCTB_LH_VERSION', '0.23.0' );

/**
 * Absolute filesystem path to the plugin directory, with trailing slash.
 */
define( 'NCTB_LH_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Public URL to the plugin directory, with trailing slash.
 */
define( 'NCTB_LH_URL', plugin_dir_url( __FILE__ ) );

/**
 * Basename of the main plugin file.
 */
define( 'NCTB_LH_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Option key that stores the currently installed schema version.
 */
define( 'NCTB_LH_DB_VERSION_OPTION', 'nctb_lh_db_version' );

/*
 * ---------------------------------------------------------------------------
 * Bootstrap files.
 * ---------------------------------------------------------------------------
 */
require_once NCTB_LH_PATH . 'includes/class-nctb-logger.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-migrations.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-roles.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-pages.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-student-profile.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-teacher-profile.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-onboarding-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-teacher-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-lesson-activity-types.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-curriculum-data.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-curriculum-cpt.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-curriculum-meta.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-curriculum-admin.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-curriculum-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-curriculum-seeder.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-ict-seeder.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-math-seeder.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-science-seeder.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-junior-seeder.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-content-library-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-content-library-admin.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-question-types.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-practice-data.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-marking-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-hint-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-practice-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-question-admin.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-progress-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-mastery-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-mistakes-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-spaced-revision-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-progress-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-student-views.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-teacher-views.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-dashboard-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-dashboard-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-entitlements.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-commerce.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-entitlements-admin.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-entitlements-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-ai-adapter.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-ai-context-builder.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-ai-usage.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-ai-tutor.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-ai-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-writing-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-listening-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-speaking-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-skills-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-board-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-board-admin.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-board-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-board-analytics-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-module-cpt.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-module-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-module-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-note-cpt.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-notes-service.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-notes-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-teacher-ai-rest.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-seo.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-notifications.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-activator.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-deactivator.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-plugin.php';

/**
 * Activation hook.
 *
 * @return void
 */
function nctb_lh_activate() {
	NCTB_Activator::activate();
}
register_activation_hook( __FILE__, 'nctb_lh_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function nctb_lh_deactivate() {
	NCTB_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'nctb_lh_deactivate' );

/**
 * Boot the plugin on plugins_loaded.
 *
 * @return void
 */
function nctb_lh_bootstrap() {
	NCTB_Plugin::instance()->run();
}
add_action( 'plugins_loaded', 'nctb_lh_bootstrap' );
