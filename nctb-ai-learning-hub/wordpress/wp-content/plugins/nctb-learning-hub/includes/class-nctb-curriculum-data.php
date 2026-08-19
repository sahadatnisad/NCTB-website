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

	/* ------------------------------------------------------------------ */
	/* Lesson Activity Blocks (Phase 4)                                   */
	/* ------------------------------------------------------------------ */

	/**
	 * Get ordered activity blocks for a lesson.
	 *
	 * @param int  $lesson_id   Lesson post ID.
	 * @param bool $only_active Only return active activities.
	 * @return array<int,object>
	 */
	public static function get_lesson_activities( $lesson_id, $only_active = true ) {
		global $wpdb;
		$table     = NCTB_Migrations::table( 'lesson_activities' );
		$lesson_id = absint( $lesson_id );

		if ( $only_active ) {
			$sql = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE lesson_id = %d AND is_active = 1 ORDER BY sort_order ASC, id ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$lesson_id
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE lesson_id = %d ORDER BY sort_order ASC, id ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$lesson_id
			);
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $wpdb->get_results( $sql );

		if ( ! empty( $rows ) ) {
			foreach ( $rows as &$row ) {
				$row->meta_data = ! empty( $row->meta_data ) ? json_decode( $row->meta_data, true ) : array();
				if ( ! is_array( $row->meta_data ) ) {
					$row->meta_data = array();
				}
			}
		}

		return $rows;
	}

	/**
	 * Get a single activity block by ID.
	 *
	 * @param int $activity_id Activity ID.
	 * @return object|null
	 */
	public static function get_activity( $activity_id ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'lesson_activities' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", absint( $activity_id ) ) );

		if ( $row ) {
			$row->meta_data = ! empty( $row->meta_data ) ? json_decode( $row->meta_data, true ) : array();
			if ( ! is_array( $row->meta_data ) ) {
				$row->meta_data = array();
			}
		}

		return $row;
	}

	/**
	 * Create an activity block.
	 *
	 * @param array $data Raw activity fields.
	 * @return int|WP_Error New activity ID or error.
	 */
	public static function create_activity( array $data ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'lesson_activities' );

		$lesson_id = isset( $data['lesson_id'] ) ? absint( $data['lesson_id'] ) : 0;
		if ( ! $lesson_id ) {
			return new WP_Error( 'nctb_missing_lesson_id', __( 'Valid lesson ID is required.', 'nctb-learning-hub' ) );
		}

		$clean = NCTB_Lesson_Activity_Types::sanitize_activity( $data );
		$now   = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ok = $wpdb->insert(
			$table,
			array(
				'lesson_id'     => $lesson_id,
				'activity_type' => $clean['activity_type'],
				'title'         => $clean['title'],
				'content'       => $clean['content'],
				'meta_data'     => ! empty( $clean['meta_data'] ) ? wp_json_encode( $clean['meta_data'] ) : null,
				'sort_order'    => $clean['sort_order'],
				'is_active'     => $clean['is_active'],
				'created_at'    => $now,
				'updated_at'    => $now,
			),
			array( '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' )
		);

		if ( false === $ok ) {
			return new WP_Error( 'nctb_db_error', __( 'Could not save the activity block.', 'nctb-learning-hub' ) );
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Update an activity block.
	 *
	 * @param int   $activity_id Activity ID.
	 * @param array $data        Fields to update.
	 * @return bool
	 */
	public static function update_activity( $activity_id, array $data ) {
		global $wpdb;
		$activity_id = absint( $activity_id );
		if ( ! $activity_id ) {
			return false;
		}

		$table = NCTB_Migrations::table( 'lesson_activities' );
		$clean = NCTB_Lesson_Activity_Types::sanitize_activity( $data );

		$fields = array(
			'activity_type' => $clean['activity_type'],
			'title'         => $clean['title'],
			'content'       => $clean['content'],
			'meta_data'     => ! empty( $clean['meta_data'] ) ? wp_json_encode( $clean['meta_data'] ) : null,
			'sort_order'    => $clean['sort_order'],
			'is_active'     => $clean['is_active'],
			'updated_at'    => current_time( 'mysql', true ),
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$updated = $wpdb->update(
			$table,
			$fields,
			array( 'id' => $activity_id ),
			array( '%s', '%s', '%s', '%s', '%d', '%d', '%s' ),
			array( '%d' )
		);

		return false !== $updated;
	}

	/**
	 * Delete an activity block.
	 *
	 * @param int $activity_id Activity ID.
	 * @return bool
	 */
	public static function delete_activity( $activity_id ) {
		global $wpdb;
		$activity_id = absint( $activity_id );
		if ( ! $activity_id ) {
			return false;
		}
		$table = NCTB_Migrations::table( 'lesson_activities' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (bool) $wpdb->delete( $table, array( 'id' => $activity_id ), array( '%d' ) );
	}

	/**
	 * Delete all activity blocks for a lesson.
	 *
	 * @param int $lesson_id Lesson post ID.
	 * @return bool
	 */
	public static function delete_lesson_activities( $lesson_id ) {
		global $wpdb;
		$lesson_id = absint( $lesson_id );
		if ( ! $lesson_id ) {
			return false;
		}
		$table = NCTB_Migrations::table( 'lesson_activities' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (bool) $wpdb->delete( $table, array( 'lesson_id' => $lesson_id ), array( '%d' ) );
	}

	/**
	 * Reorder activities for a lesson given an ordered list of IDs.
	 *
	 * @param int   $lesson_id   Lesson post ID.
	 * @param int[] $ordered_ids Array of activity IDs in desired order.
	 * @return bool
	 */
	public static function reorder_activities( $lesson_id, array $ordered_ids ) {
		global $wpdb;
		$lesson_id = absint( $lesson_id );
		if ( ! $lesson_id || empty( $ordered_ids ) ) {
			return false;
		}

		$table = NCTB_Migrations::table( 'lesson_activities' );
		$order = 0;

		foreach ( $ordered_ids as $id ) {
			$id = absint( $id );
			if ( ! $id ) {
				continue;
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update(
				$table,
				array( 'sort_order' => $order ),
				array( 'id' => $id, 'lesson_id' => $lesson_id ),
				array( '%d' ),
				array( '%d', '%d' )
			);
			$order++;
		}

		return true;
	}

	/**
	 * Atomic replace of all activities for a lesson (used by admin form saves).
	 *
	 * @param int   $lesson_id  Lesson post ID.
	 * @param array $activities Array of raw activity arrays.
	 * @return void
	 */
	public static function set_lesson_activities( $lesson_id, array $activities ) {
		global $wpdb;
		$lesson_id = absint( $lesson_id );
		if ( ! $lesson_id ) {
			return;
		}

		self::delete_lesson_activities( $lesson_id );

		$order = 0;
		foreach ( $activities as $raw ) {
			if ( ! is_array( $raw ) ) {
				continue;
			}
			$raw['lesson_id']  = $lesson_id;
			$raw['sort_order'] = $order;
			self::create_activity( $raw );
			$order++;
		}
	}
}

