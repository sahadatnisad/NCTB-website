<?php
/**
 * Versioned schema / migration runner.
 *
 * PHASE 0 foundation only. No custom tables are created yet — the plan
 * mandates that curriculum, questions, attempts, mastery, entitlements and
 * AI-usage tables arrive in their own later phases. This class provides the
 * single, safe entry point those phases will extend so schema changes are
 * always versioned and idempotent.
 *
 * How later phases add a migration:
 *   1. Bump NCTB_LH_VERSION in the main plugin file.
 *   2. Add a private step method (e.g. upgrade_to_0_2_0()).
 *   3. Register it in get_steps() keyed by the version it introduces.
 * The runner applies only steps newer than the stored DB version.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Migrations
 */
class NCTB_Migrations {

	/**
	 * Ordered migration steps, keyed by the schema version each one produces.
	 *
	 * Empty in Phase 0 by design. Each callable receives no arguments and must
	 * be idempotent (safe to run more than once) — prefer dbDelta() for tables.
	 *
	 * @return array<string,callable> Map of version => callable.
	 */
	protected static function get_steps() {
		return array(
			'0.3.0' => array( __CLASS__, 'upgrade_to_0_3_0' ),
		);
	}

	/**
	 * Phase 3 schema: curriculum concepts, per-lesson learning outcomes, and
	 * the lesson↔concept link table. Idempotent via dbDelta().
	 *
	 * @return void
	 */
	protected static function upgrade_to_0_3_0() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$concepts        = self::table( 'concepts' );
		$outcomes        = self::table( 'learning_outcomes' );
		$lesson_concepts = self::table( 'lesson_concepts' );

		$sql_concepts = "CREATE TABLE {$concepts} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(191) NOT NULL,
			slug VARCHAR(191) NOT NULL DEFAULT '',
			subject VARCHAR(64) NOT NULL DEFAULT '',
			description TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			PRIMARY KEY  (id),
			KEY slug (slug),
			KEY subject (subject)
		) {$charset_collate};";

		$sql_outcomes = "CREATE TABLE {$outcomes} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lesson_id BIGINT UNSIGNED NOT NULL,
			outcome_text TEXT NOT NULL,
			sort_order INT NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			PRIMARY KEY  (id),
			KEY lesson_id (lesson_id)
		) {$charset_collate};";

		$sql_links = "CREATE TABLE {$lesson_concepts} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lesson_id BIGINT UNSIGNED NOT NULL,
			concept_id BIGINT UNSIGNED NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY lesson_concept (lesson_id, concept_id),
			KEY concept_id (concept_id)
		) {$charset_collate};";

		dbDelta( $sql_concepts );
		dbDelta( $sql_outcomes );
		dbDelta( $sql_links );
	}

	/**
	 * Run any migration steps newer than the currently installed version.
	 *
	 * Called on activation and on admin init (to catch upgrades applied by
	 * simply replacing plugin files).
	 *
	 * @return void
	 */
	public static function run() {
		$installed = get_option( NCTB_LH_DB_VERSION_OPTION, '0.0.0' );

		if ( version_compare( $installed, NCTB_LH_VERSION, '>=' ) ) {
			return;
		}

		foreach ( self::get_steps() as $version => $callback ) {
			if ( version_compare( $installed, $version, '<' ) && is_callable( $callback ) ) {
				call_user_func( $callback );
				NCTB_Logger::info( 'Applied migration step', array( 'version' => $version ) );
			}
		}

		update_option( NCTB_LH_DB_VERSION_OPTION, NCTB_LH_VERSION, false );
		NCTB_Logger::info( 'Schema version updated', array( 'to' => NCTB_LH_VERSION ) );
	}

	/**
	 * Convenience accessor for the prefixed table name of a logical table.
	 *
	 * Later phases call this instead of hard-coding table names, so the
	 * WordPress prefix is always respected.
	 *
	 * @param string $logical Logical table suffix, e.g. 'concepts'.
	 * @return string Fully prefixed table name, e.g. wp_nctb_concepts.
	 */
	public static function table( $logical ) {
		global $wpdb;
		return $wpdb->prefix . 'nctb_' . ltrim( $logical, '_' );
	}
}
