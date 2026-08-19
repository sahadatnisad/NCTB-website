<?php
/**
 * Practice & Question Engine Data Service.
 *
 * Provides database operations for questions, MCQ options, question-concept
 * links, and student practice attempts. All queries use $wpdb->prepare().
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Practice_Data
 */
class NCTB_Practice_Data {

	/* ------------------------------------------------------------------ */
	/* Questions CRUD & Queries                                           */
	/* ------------------------------------------------------------------ */

	/**
	 * Get questions for a lesson.
	 *
	 * @param int  $lesson_id       Lesson post ID.
	 * @param bool $only_active     Filter by is_active.
	 * @param bool $include_answers Whether to include correct_answer and is_correct flags.
	 * @return array<int,object>
	 */
	public static function get_lesson_questions( $lesson_id, $only_active = true, $include_answers = false ) {
		global $wpdb;
		$table     = NCTB_Migrations::table( 'questions' );
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

				if ( NCTB_Question_Types::TYPE_MCQ === $row->question_type ) {
					$row->options = self::get_question_options( $row->id, $include_answers );
				} else {
					$row->options = array();
				}

				$row->concepts = self::get_question_concepts( $row->id );

				if ( ! $include_answers ) {
					unset( $row->correct_answer );
				}
			}
		}

		return $rows;
	}

	/**
	 * Get a single question by ID.
	 *
	 * @param int  $question_id     Question ID.
	 * @param bool $include_answers Whether to include answer fields.
	 * @return object|null
	 */
	public static function get_question( $question_id, $include_answers = true ) {
		global $wpdb;
		$table       = NCTB_Migrations::table( 'questions' );
		$question_id = absint( $question_id );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $question_id ) );

		if ( $row ) {
			$row->meta_data = ! empty( $row->meta_data ) ? json_decode( $row->meta_data, true ) : array();
			if ( ! is_array( $row->meta_data ) ) {
				$row->meta_data = array();
			}

			if ( NCTB_Question_Types::TYPE_MCQ === $row->question_type ) {
				$row->options = self::get_question_options( $row->id, $include_answers );
			} else {
				$row->options = array();
			}

			$row->concepts = self::get_question_concepts( $row->id );

			if ( ! $include_answers ) {
				unset( $row->correct_answer );
			}
		}

		return $row;
	}

	/**
	 * Create a new question with options and concept links.
	 *
	 * @param array $data        Question fields.
	 * @param array $options     MCQ options array.
	 * @param array $concept_ids Concept IDs.
	 * @return int|WP_Error New question ID or WP_Error.
	 */
	public static function create_question( array $data, array $options = array(), array $concept_ids = array() ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'questions' );
		$clean = NCTB_Question_Types::sanitize_question( $data );

		if ( empty( $clean['prompt'] ) ) {
			return new WP_Error( 'nctb_missing_prompt', __( 'Question prompt is required.', 'nctb-learning-hub' ) );
		}

		$now = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ok = $wpdb->insert(
			$table,
			array(
				'lesson_id'           => $clean['lesson_id'],
				'question_type'       => $clean['question_type'],
				'prompt'              => $clean['prompt'],
				'content'             => $clean['content'],
				'difficulty'          => $clean['difficulty'],
				'correct_answer'      => $clean['correct_answer'],
				'explanation'         => $clean['explanation'],
				'hint_1'              => $clean['hint_1'],
				'hint_2'              => $clean['hint_2'],
				'hint_3'              => $clean['hint_3'],
				'source_type'         => $clean['source_type'],
				'verification_status' => $clean['verification_status'],
				'meta_data'           => ! empty( $clean['meta_data'] ) ? wp_json_encode( $clean['meta_data'] ) : null,
				'sort_order'          => $clean['sort_order'],
				'is_active'           => $clean['is_active'],
				'created_at'          => $now,
				'updated_at'          => $now,
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' )
		);

		if ( false === $ok ) {
			return new WP_Error( 'nctb_db_error', __( 'Could not save the question.', 'nctb-learning-hub' ) );
		}

		$question_id = (int) $wpdb->insert_id;

		if ( ! empty( $options ) ) {
			self::set_question_options( $question_id, $options );
		}

		if ( ! empty( $concept_ids ) ) {
			self::set_question_concepts( $question_id, $concept_ids );
		}

		return $question_id;
	}

	/**
	 * Update an existing question.
	 *
	 * @param int   $question_id Question ID.
	 * @param array $data        Question fields.
	 * @param array $options     MCQ options array.
	 * @param array $concept_ids Concept IDs.
	 * @return bool
	 */
	public static function update_question( $question_id, array $data, array $options = array(), array $concept_ids = array() ) {
		global $wpdb;
		$question_id = absint( $question_id );
		if ( ! $question_id ) {
			return false;
		}

		$table = NCTB_Migrations::table( 'questions' );
		$clean = NCTB_Question_Types::sanitize_question( $data );

		$fields = array(
			'lesson_id'           => $clean['lesson_id'],
			'question_type'       => $clean['question_type'],
			'prompt'              => $clean['prompt'],
			'content'             => $clean['content'],
			'difficulty'          => $clean['difficulty'],
			'correct_answer'      => $clean['correct_answer'],
			'explanation'         => $clean['explanation'],
			'hint_1'              => $clean['hint_1'],
			'hint_2'              => $clean['hint_2'],
			'hint_3'              => $clean['hint_3'],
			'source_type'         => $clean['source_type'],
			'verification_status' => $clean['verification_status'],
			'meta_data'           => ! empty( $clean['meta_data'] ) ? wp_json_encode( $clean['meta_data'] ) : null,
			'sort_order'          => $clean['sort_order'],
			'is_active'           => $clean['is_active'],
			'updated_at'          => current_time( 'mysql', true ),
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$updated = $wpdb->update(
			$table,
			$fields,
			array( 'id' => $question_id ),
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ),
			array( '%d' )
		);

		if ( ! empty( $options ) ) {
			self::set_question_options( $question_id, $options );
		}

		if ( ! empty( $concept_ids ) ) {
			self::set_question_concepts( $question_id, $concept_ids );
		}

		return false !== $updated;
	}

	/**
	 * Delete a question and its options, concept links, and attempts.
	 *
	 * @param int $question_id Question ID.
	 * @return bool
	 */
	public static function delete_question( $question_id ) {
		global $wpdb;
		$question_id = absint( $question_id );
		if ( ! $question_id ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( NCTB_Migrations::table( 'question_options' ), array( 'question_id' => $question_id ), array( '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( NCTB_Migrations::table( 'question_concepts' ), array( 'question_id' => $question_id ), array( '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( NCTB_Migrations::table( 'attempts' ), array( 'question_id' => $question_id ), array( '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (bool) $wpdb->delete( NCTB_Migrations::table( 'questions' ), array( 'id' => $question_id ), array( '%d' ) );
	}

	/* ------------------------------------------------------------------ */
	/* Question Options (MCQ)                                             */
	/* ------------------------------------------------------------------ */

	/**
	 * Get options for a question.
	 *
	 * @param int  $question_id       Question ID.
	 * @param bool $include_is_correct Whether to include is_correct flag.
	 * @return array<int,object>
	 */
	public static function get_question_options( $question_id, $include_is_correct = false ) {
		global $wpdb;
		$table       = NCTB_Migrations::table( 'question_options' );
		$question_id = absint( $question_id );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE question_id = %d ORDER BY sort_order ASC, id ASC", $question_id ) );

		if ( ! empty( $rows ) && ! $include_is_correct ) {
			foreach ( $rows as &$r ) {
				unset( $r->is_correct );
			}
		}

		return $rows;
	}

	/**
	 * Replace options for a question.
	 *
	 * @param int   $question_id Question ID.
	 * @param array $options     Raw options array.
	 * @return void
	 */
	public static function set_question_options( $question_id, array $options ) {
		global $wpdb;
		$question_id = absint( $question_id );
		$table       = NCTB_Migrations::table( 'question_options' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( $table, array( 'question_id' => $question_id ), array( '%d' ) );

		$clean = NCTB_Question_Types::sanitize_options( $options );
		foreach ( $clean as $opt ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$table,
				array(
					'question_id' => $question_id,
					'option_key'  => $opt['option_key'],
					'option_text' => $opt['option_text'],
					'is_correct'  => $opt['is_correct'],
					'feedback'    => $opt['feedback'],
					'sort_order'  => $opt['sort_order'],
				),
				array( '%d', '%s', '%s', '%d', '%s', '%d' )
			);
		}
	}

	/* ------------------------------------------------------------------ */
	/* Question Concept Links                                             */
	/* ------------------------------------------------------------------ */

	/**
	 * Get concepts linked to a question.
	 *
	 * @param int $question_id Question ID.
	 * @return array<int,object>
	 */
	public static function get_question_concepts( $question_id ) {
		global $wpdb;
		$concepts = NCTB_Migrations::table( 'concepts' );
		$links    = NCTB_Migrations::table( 'question_concepts' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT c.* FROM {$concepts} c INNER JOIN {$links} l ON l.concept_id = c.id WHERE l.question_id = %d ORDER BY c.name ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				absint( $question_id )
			)
		);
	}

	/**
	 * Set linked concepts for a question.
	 *
	 * @param int   $question_id Question ID.
	 * @param int[] $concept_ids Array of concept IDs.
	 * @return void
	 */
	public static function set_question_concepts( $question_id, array $concept_ids ) {
		global $wpdb;
		$question_id = absint( $question_id );
		$table       = NCTB_Migrations::table( 'question_concepts' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( $table, array( 'question_id' => $question_id ), array( '%d' ) );

		foreach ( array_unique( array_map( 'absint', $concept_ids ) ) as $cid ) {
			if ( ! $cid ) {
				continue;
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$table,
				array(
					'question_id' => $question_id,
					'concept_id'  => $cid,
				),
				array( '%d', '%d' )
			);
		}
	}

	/* ------------------------------------------------------------------ */
	/* Student Attempts                                                   */
	/* ------------------------------------------------------------------ */

	/**
	 * Record a student practice attempt.
	 *
	 * @param array $data Attempt fields.
	 * @return int|WP_Error
	 */
	public static function record_attempt( array $data ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'attempts' );

		$user_id     = isset( $data['user_id'] ) ? absint( $data['user_id'] ) : get_current_user_id();
		$question_id = isset( $data['question_id'] ) ? absint( $data['question_id'] ) : 0;
		$lesson_id   = isset( $data['lesson_id'] ) ? absint( $data['lesson_id'] ) : 0;

		if ( ! $user_id || ! $question_id ) {
			return new WP_Error( 'nctb_invalid_attempt', __( 'Valid user and question are required.', 'nctb-learning-hub' ) );
		}

		// Calculate attempt number
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$prev_count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE user_id = %d AND question_id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$user_id,
				$question_id
			)
		);

		$attempt_number = $prev_count + 1;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ok = $wpdb->insert(
			$table,
			array(
				'user_id'        => $user_id,
				'question_id'    => $question_id,
				'lesson_id'      => $lesson_id,
				'given_answer'   => isset( $data['given_answer'] ) ? sanitize_textarea_field( $data['given_answer'] ) : '',
				'is_correct'     => ! empty( $data['is_correct'] ) ? 1 : 0,
				'score'          => isset( $data['score'] ) ? floatval( $data['score'] ) : ( ! empty( $data['is_correct'] ) ? 1.0 : 0.0 ),
				'hints_used'     => isset( $data['hints_used'] ) ? absint( $data['hints_used'] ) : 0,
				'attempt_number' => $attempt_number,
				'feedback_given' => isset( $data['feedback_given'] ) ? sanitize_text_field( $data['feedback_given'] ) : '',
				'created_at'     => current_time( 'mysql', true ),
			),
			array( '%d', '%d', '%d', '%s', '%d', '%f', '%d', '%d', '%s', '%s' )
		);

		if ( false === $ok ) {
			return new WP_Error( 'nctb_db_error', __( 'Could not record attempt.', 'nctb-learning-hub' ) );
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Get attempts for a student with per-student isolation.
	 *
	 * @param int $user_id   Student User ID.
	 * @param int $lesson_id Optional Lesson ID filter.
	 * @param int $limit     Max rows.
	 * @return array<int,object>
	 */
	public static function get_student_attempts( $user_id, $lesson_id = 0, $limit = 50 ) {
		global $wpdb;
		$table   = NCTB_Migrations::table( 'attempts' );
		$user_id = absint( $user_id );
		$limit   = absint( $limit );

		if ( ! $user_id ) {
			return array();
		}

		if ( $lesson_id ) {
			$sql = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE user_id = %d AND lesson_id = %d ORDER BY created_at DESC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$user_id,
				absint( $lesson_id ),
				$limit
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$user_id,
				$limit
			);
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return $wpdb->get_results( $sql );
	}

	/**
	 * Get the latest attempt for a student on a question.
	 *
	 * @param int $user_id     Student User ID.
	 * @param int $question_id Question ID.
	 * @return object|null
	 */
	public static function get_latest_attempt( $user_id, $question_id ) {
		global $wpdb;
		$table       = NCTB_Migrations::table( 'attempts' );
		$user_id     = absint( $user_id );
		$question_id = absint( $question_id );

		if ( ! $user_id || ! $question_id ) {
			return null;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE user_id = %d AND question_id = %d ORDER BY attempt_number DESC, id DESC LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$user_id,
				$question_id
			)
		);
	}
}
