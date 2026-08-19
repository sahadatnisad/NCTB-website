<?php
/**
 * Curriculum custom-table data service.
 *
 * Single access point for the high-volume relational curriculum data that is
 * NOT stored as WordPress posts: concepts, per-lesson learning outcomes, and
 * the lesson↔concept links. All queries use $wpdb->prepare().
 *
 * Tables (created by NCTB_Migrations step 0.3.0):
 *   - nctb_concepts          reusable teaching concepts (subject-scoped)
 *   - nctb_learning_outcomes per-lesson learning outcomes
 *   - nctb_lesson_concepts   many-to-many lesson↔concept links
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Curriculum_Data
 */
class NCTB_Curriculum_Data {

	/* ------------------------------------------------------------------ */
	/* Concepts                                                            */
	/* ------------------------------------------------------------------ */

	/**
	 * List concepts, optionally filtered by subject.
	 *
	 * @param string $subject Optional subject slug to filter by.
	 * @return array<int,object> Rows.
	 */
	public static function get_concepts( $subject = '' ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'concepts' );

		if ( $subject ) {
			$sql = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE subject = %s ORDER BY name ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				sanitize_key( $subject )
			);
		} else {
			$sql = "SELECT * FROM {$table} ORDER BY name ASC"; // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return $wpdb->get_results( $sql );
	}

	/**
	 * Get a single concept by ID.
	 *
	 * @param int $concept_id Concept ID.
	 * @return object|null
	 */
	public static function get_concept( $concept_id ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'concepts' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", absint( $concept_id ) ) );
	}

	/**
	 * Create a concept.
	 *
	 * @param array $data name, subject, description.
	 * @return int|WP_Error New concept ID or error.
	 */
	public static function create_concept( array $data ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'concepts' );

		$name = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
		if ( '' === $name ) {
			return new WP_Error( 'nctb_missing_name', __( 'Concept name is required.', 'nctb-learning-hub' ) );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ok = $wpdb->insert(
			$table,
			array(
				'name'        => $name,
				'slug'        => sanitize_title( $name ),
				'subject'     => isset( $data['subject'] ) ? sanitize_key( $data['subject'] ) : '',
				'description' => isset( $data['description'] ) ? sanitize_textarea_field( $data['description'] ) : '',
				'created_at'  => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%s', '%s' )
		);

		if ( false === $ok ) {
			return new WP_Error( 'nctb_db_error', __( 'Could not save the concept.', 'nctb-learning-hub' ) );
		}
		return (int) $wpdb->insert_id;
	}

	/**
	 * Delete a concept and its lesson links.
	 *
	 * @param int $concept_id Concept ID.
	 * @return bool
	 */
	public static function delete_concept( $concept_id ) {
		global $wpdb;
		$concept_id = absint( $concept_id );
		if ( ! $concept_id ) {
			return false;
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( NCTB_Migrations::table( 'lesson_concepts' ), array( 'concept_id' => $concept_id ), array( '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (bool) $wpdb->delete( NCTB_Migrations::table( 'concepts' ), array( 'id' => $concept_id ), array( '%d' ) );
	}

	/* ------------------------------------------------------------------ */
	/* Learning outcomes (per lesson)                                      */
	/* ------------------------------------------------------------------ */

	/**
	 * Get learning outcomes for a lesson, ordered.
	 *
	 * @param int $lesson_id Lesson post ID.
	 * @return array<int,object>
	 */
	public static function get_lesson_outcomes( $lesson_id ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'learning_outcomes' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE lesson_id = %d ORDER BY sort_order ASC, id ASC", absint( $lesson_id ) ) );
	}

	/**
	 * Replace all learning outcomes for a lesson with the provided list.
	 *
	 * @param int   $lesson_id Lesson post ID.
	 * @param array $outcomes  Ordered list of outcome strings.
	 * @return void
	 */
	public static function set_lesson_outcomes( $lesson_id, array $outcomes ) {
		global $wpdb;
		$lesson_id = absint( $lesson_id );
		$table     = NCTB_Migrations::table( 'learning_outcomes' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( $table, array( 'lesson_id' => $lesson_id ), array( '%d' ) );

		$order = 0;
		foreach ( $outcomes as $text ) {
			$text = sanitize_textarea_field( $text );
			if ( '' === trim( $text ) ) {
				continue;
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$table,
				array(
					'lesson_id'   => $lesson_id,
					'outcome_text' => $text,
					'sort_order'  => $order,
					'created_at'  => current_time( 'mysql', true ),
				),
				array( '%d', '%s', '%d', '%s' )
			);
			$order++;
		}
	}

	/* ------------------------------------------------------------------ */
	/* Lesson ↔ concept links                                              */
	/* ------------------------------------------------------------------ */

	/**
	 * Get concepts linked to a lesson (joined with concept rows).
	 *
	 * @param int $lesson_id Lesson post ID.
	 * @return array<int,object>
	 */
	public static function get_lesson_concepts( $lesson_id ) {
		global $wpdb;
		$concepts = NCTB_Migrations::table( 'concepts' );
		$links    = NCTB_Migrations::table( 'lesson_concepts' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT c.* FROM {$concepts} c INNER JOIN {$links} l ON l.concept_id = c.id WHERE l.lesson_id = %d ORDER BY c.name ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				absint( $lesson_id )
			)
		);
	}

	/**
	 * Get linked concept IDs for a lesson.
	 *
	 * @param int $lesson_id Lesson post ID.
	 * @return int[]
	 */
	public static function get_lesson_concept_ids( $lesson_id ) {
		global $wpdb;
		$links = NCTB_Migrations::table( 'lesson_concepts' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT concept_id FROM {$links} WHERE lesson_id = %d", absint( $lesson_id ) ) );
		return array_map( 'intval', (array) $ids );
	}

	/**
	 * Replace the concept links for a lesson with the given concept IDs.
	 *
	 * @param int   $lesson_id   Lesson post ID.
	 * @param int[] $concept_ids Concept IDs.
	 * @return void
	 */
	public static function set_lesson_concepts( $lesson_id, array $concept_ids ) {
		global $wpdb;
		$lesson_id = absint( $lesson_id );
		$table     = NCTB_Migrations::table( 'lesson_concepts' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( $table, array( 'lesson_id' => $lesson_id ), array( '%d' ) );

		foreach ( array_unique( array_map( 'absint', $concept_ids ) ) as $cid ) {
			if ( ! $cid ) {
				continue;
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$table,
				array(
					'lesson_id'  => $lesson_id,
					'concept_id' => $cid,
				),
				array( '%d', '%d' )
			);
		}
	}
}
