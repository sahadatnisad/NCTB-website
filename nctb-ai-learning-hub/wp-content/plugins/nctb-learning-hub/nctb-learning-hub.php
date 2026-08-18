<?php
/**
 * Plugin Name:       NCTB Learning Hub
 * Plugin URI:        https://example.com/nctb-learning-hub
 * Description:       Lesson-by-lesson digital companion to the Bangladesh NCTB curriculum. Contains all learning and business logic (curriculum, lessons, practice, mastery, revision, entitlements, AI tutor). Presentation lives in the theme.
 * Version:           0.1.0
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
 * Current plugin version. Also used as the schema/migration baseline.
 * Follows Semantic Versioning. Bump on every release.
 */
define( 'NCTB_LH_VERSION', '0.1.0' );

/**
 * Absolute filesystem path to the plugin directory, with trailing slash.
 */
define( 'NCTB_LH_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Public URL to the plugin directory, with trailing slash.
 */
define( 'NCTB_LH_URL', plugin_dir_url( __FILE__ ) );

/**
 * Basename of the main plugin file (e.g. nctb-learning-hub/nctb-learning-hub.php).
 */
define( 'NCTB_LH_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Option key that stores the currently installed schema version. Used by the
 * migration runner to decide whether upgrade routines must run.
 */
define( 'NCTB_LH_DB_VERSION_OPTION', 'nctb_lh_db_version' );

/*
 * ---------------------------------------------------------------------------
 * Bootstrap files.
 *
 * PHASE 0 keeps this list intentionally small. Later phases will register
 * additional modules (curriculum CPTs, question engine, mastery service,
 * entitlement service, AI provider adapter, REST controllers) through the
 * central NCTB_Plugin loader so this file rarely needs to change.
 * ---------------------------------------------------------------------------
 */
require_once NCTB_LH_PATH . 'includes/class-nctb-logger.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-migrations.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-activator.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-deactivator.php';
require_once NCTB_LH_PATH . 'includes/class-nctb-plugin.php';

/**
 * Activation hook — create baseline options and run migrations.
 *
 * @return void
 */
function nctb_lh_activate() {
	NCTB_Activator::activate();
}
register_activation_hook( __FILE__, 'nctb_lh_activate' );

/**
 * Deactivation hook — flush transient/scheduled state without destroying data.
 *
 * @return void
 */
function nctb_lh_deactivate() {
	NCTB_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'nctb_lh_deactivate' );

/**
 * Boot the plugin on plugins_loaded so translations and other plugins
 * (e.g. WooCommerce, added in a later phase) are available.
 *
 * @return void
 */
function nctb_lh_bootstrap() {
	NCTB_Plugin::instance()->run();
}
add_action( 'plugins_loaded', 'nctb_lh_bootstrap' );
