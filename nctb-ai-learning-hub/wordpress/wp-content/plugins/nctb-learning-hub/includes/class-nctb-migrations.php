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
			'0.4.0' => array( __CLASS__, 'upgrade_to_0_4_0' ),
			'0.5.0' => array( __CLASS__, 'upgrade_to_0_5_0' ),
			'0.6.0' => array( __CLASS__, 'upgrade_to_0_6_0' ),
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
	 * Phase 4 schema: lesson activity blocks table.
	 *
	 * Stores reusable, reorderable activity blocks (reading, vocabulary,
	 * grammar, guided/independent practice, writing, listening, speaking,
	 * summary, quiz placeholder, tutor placeholder) per lesson.
	 *
	 * @return void
	 */
	protected static function upgrade_to_0_4_0() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$activities      = self::table( 'lesson_activities' );

		$sql_activities = "CREATE TABLE {$activities} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lesson_id BIGINT UNSIGNED NOT NULL,
			activity_type VARCHAR(64) NOT NULL,
			title VARCHAR(255) NOT NULL DEFAULT '',
			content LONGTEXT NOT NULL,
			meta_data LONGTEXT NULL,
			sort_order INT NOT NULL DEFAULT 0,
			is_active TINYINT(1) NOT NULL DEFAULT 1,
			created_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			updated_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			PRIMARY KEY  (id),
			KEY lesson_id (lesson_id),
			KEY sort_order (sort_order)
		) {$charset_collate};";

		dbDelta( $sql_activities );
	}

	/**
	 * Phase 5 schema: practice and question engine tables.
	 *
	 * Creates:
	 *   - nctb_questions: question records (mcq, fill_in_blank, short_answer, error_correction, difficulty, hints)
	 *   - nctb_question_options: options for MCQ questions
	 *   - nctb_question_concepts: many-to-many links between questions and concepts
	 *   - nctb_attempts: student practice submissions and scores
	 *
	 * @return void
	 */
	protected static function upgrade_to_0_5_0() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate   = $wpdb->get_charset_collate();
		$questions         = self::table( 'questions' );
		$question_options  = self::table( 'question_options' );
		$question_concepts = self::table( 'question_concepts' );
		$attempts          = self::table( 'attempts' );

		$sql_questions = "CREATE TABLE {$questions} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lesson_id BIGINT UNSIGNED NOT NULL,
			question_type VARCHAR(32) NOT NULL DEFAULT 'mcq',
			prompt TEXT NOT NULL,
			content LONGTEXT NULL,
			difficulty VARCHAR(20) NOT NULL DEFAULT 'medium',
			correct_answer TEXT NOT NULL,
			explanation LONGTEXT NULL,
			hint_1 TEXT NULL,
			hint_2 TEXT NULL,
			hint_3 TEXT NULL,
			source_type VARCHAR(64) NOT NULL DEFAULT 'nctb_textbook',
			verification_status VARCHAR(32) NOT NULL DEFAULT 'verified',
			meta_data LONGTEXT NULL,
			sort_order INT NOT NULL DEFAULT 0,
			is_active TINYINT(1) NOT NULL DEFAULT 1,
			created_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			updated_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			PRIMARY KEY  (id),
			KEY lesson_id (lesson_id),
			KEY question_type (question_type),
			KEY difficulty (difficulty),
			KEY sort_order (sort_order)
		) {$charset_collate};";

		$sql_options = "CREATE TABLE {$question_options} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			question_id BIGINT UNSIGNED NOT NULL,
			option_key VARCHAR(16) NOT NULL DEFAULT '',
			option_text TEXT NOT NULL,
			is_correct TINYINT(1) NOT NULL DEFAULT 0,
			feedback TEXT NULL,
			sort_order INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (id),
			KEY question_id (question_id)
		) {$charset_collate};";

		$sql_question_concepts = "CREATE TABLE {$question_concepts} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			question_id BIGINT UNSIGNED NOT NULL,
			concept_id BIGINT UNSIGNED NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY question_concept (question_id, concept_id),
			KEY concept_id (concept_id)
		) {$charset_collate};";

		$sql_attempts = "CREATE TABLE {$attempts} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			question_id BIGINT UNSIGNED NOT NULL,
			lesson_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			given_answer LONGTEXT NOT NULL,
			is_correct TINYINT(1) NOT NULL DEFAULT 0,
			score FLOAT NOT NULL DEFAULT 0,
			hints_used INT NOT NULL DEFAULT 0,
			attempt_number INT NOT NULL DEFAULT 1,
			feedback_given TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY question_id (question_id),
			KEY lesson_id (lesson_id),
			KEY user_question (user_id, question_id)
		) {$charset_collate};";

		dbDelta( $sql_questions );
		dbDelta( $sql_options );
		dbDelta( $sql_question_concepts );
		dbDelta( $sql_attempts );
	}

	/**
	 * Phase 6 schema: Progress, Mastery, Mistakes, and Spaced Revision tables.
	 *
	 * Creates:
	 *   - nctb_progress: lesson completion and activity step position
	 *   - nctb_mastery: concept-level mastery scores and levels (novice -> mastered)
	 *   - nctb_mistakes: smart mistake notebook with decay/mastery states
	 *   - nctb_review_schedule: spaced repetition calendar and due queue
	 *
	 * @return void
	 */
	protected static function upgrade_to_0_6_0() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$progress        = self::table( 'progress' );
		$mastery         = self::table( 'mastery' );
		$mistakes        = self::table( 'mistakes' );
		$review_schedule = self::table( 'review_schedule' );

		$sql_progress = "CREATE TABLE {$progress} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			lesson_id BIGINT UNSIGNED NOT NULL,
			unit_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			book_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			status VARCHAR(20) NOT NULL DEFAULT 'in_progress',
			last_activity_step INT NOT NULL DEFAULT 1,
			completed_activities TEXT NULL,
			completed_at DATETIME NULL,
			updated_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			created_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			PRIMARY KEY  (id),
			UNIQUE KEY user_lesson (user_id, lesson_id),
			KEY user_id (user_id),
			KEY status (status)
		) {$charset_collate};";

		$sql_mastery = "CREATE TABLE {$mastery} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			concept_id BIGINT UNSIGNED NOT NULL,
			mastery_score FLOAT NOT NULL DEFAULT 0.0,
			mastery_level VARCHAR(20) NOT NULL DEFAULT 'novice',
			total_attempts INT NOT NULL DEFAULT 0,
			correct_attempts INT NOT NULL DEFAULT 0,
			last_attempt_at DATETIME NULL,
			updated_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			PRIMARY KEY  (id),
			UNIQUE KEY user_concept (user_id, concept_id),
			KEY user_id (user_id)
		) {$charset_collate};";

		$sql_mistakes = "CREATE TABLE {$mistakes} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			question_id BIGINT UNSIGNED NOT NULL,
			lesson_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			last_attempt_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			wrong_answer TEXT NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'active',
			error_count INT NOT NULL DEFAULT 1,
			correct_streak INT NOT NULL DEFAULT 0,
			last_error_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			resolved_at DATETIME NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY user_question (user_id, question_id),
			KEY user_id (user_id),
			KEY status (status)
		) {$charset_collate};";

		$sql_schedule = "CREATE TABLE {$review_schedule} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			item_type VARCHAR(32) NOT NULL DEFAULT 'question',
			item_id BIGINT UNSIGNED NOT NULL,
			lesson_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			interval_days INT NOT NULL DEFAULT 1,
			ease_factor FLOAT NOT NULL DEFAULT 2.5,
			repetition_count INT NOT NULL DEFAULT 0,
			due_date DATE NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			last_reviewed_at DATETIME NULL,
			created_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			PRIMARY KEY  (id),
			KEY user_due (user_id, due_date),
			KEY user_status (user_id, status)
		) {$charset_collate};";

		dbDelta( $sql_progress );
		dbDelta( $sql_mastery );
		dbDelta( $sql_mistakes );
		dbDelta( $sql_schedule );
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
