<?php
/**
 * Smart Mistake Notebook Service (Phase 6).
 *
 * Automatically records student incorrect practice attempts, tracks error
 * counts and correct streaks, and transitions mastered mistakes out of the
 * active review list.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Mistakes_Service
 */
class NCTB_Mistakes_Service {

	const STATUS_ACTIVE   = 'active';
	const STATUS_RESOLVED = 'resolved';
	const STATUS_MASTERED = 'mastered';

	/**
	 * Process a student attempt result in the mistake notebook.
	 *
	 * @param int    $user_id      Student User ID.
	 * @param int    $question_id  Question ID.
	 * @param int    $lesson_id    Lesson Post ID.
	 * @param int    $attempt_id   Attempt ID.
	 * @param bool   $is_correct   Correctness.
	 * @param string $given_answer Submitted answer.
	 * @return void
	 */
	public static function handle_attempt_result( $user_id, $question_id, $lesson_id, $attempt_id, $is_correct, $given_answer ) {
		global $wpdb;
		$user_id     = absint( $user_id );
		$question_id = absint( $question_id );
		$lesson_id   = absint( $lesson_id );
		$attempt_id  = absint( $attempt_id );

		if ( ! $user_id || ! $question_id ) {
			return;
		}

		$table = NCTB_Migrations::table( 'mistakes' );
		$now   = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$existing = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d AND question_id = %d", $user_id, $question_id )
		);

		if ( ! $is_correct ) {
			// Failed attempt -> record or update active mistake
			if ( $existing ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->update(
					$table,
					array(
						'last_attempt_id' => $attempt_id,
						'wrong_answer'    => sanitize_textarea_field( $given_answer ),
						'status'          => self::STATUS_ACTIVE,
						'error_count'     => (int) $existing->error_count + 1,
						'correct_streak'  => 0,
						'last_error_at'   => $now,
						'resolved_at'     => null,
					),
					array( 'id' => $existing->id ),
					array( '%d', '%s', '%s', '%d', '%d', '%s', '%s' ),
					array( '%d' )
				);
			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->insert(
					$table,
					array(
						'user_id'         => $user_id,
						'question_id'     => $question_id,
						'lesson_id'       => $lesson_id,
						'last_attempt_id' => $attempt_id,
						'wrong_answer'    => sanitize_textarea_field( $given_answer ),
						'status'          => self::STATUS_ACTIVE,
						'error_count'     => 1,
						'correct_streak'  => 0,
						'last_error_at'   => $now,
					),
					array( '%d', '%d', '%d', '%d', '%s', '%s', '%d', '%d', '%s' )
				);
			}

			// Automatically schedule spaced review for tomorrow
			if ( class_exists( 'NCTB_Spaced_Revision_Service' ) ) {
				NCTB_Spaced_Revision_Service::schedule_review( $user_id, 'question', $question_id, $lesson_id, 1 );
			}
		} else {
			// Successful attempt -> check if existing active mistake can be graduated
			if ( $existing && self::STATUS_ACTIVE === $existing->status ) {
				$new_streak = (int) $existing->correct_streak + 1;
				// Graduate after 1 clean retry with correct answer
				$new_status  = ( $new_streak >= 1 ) ? self::STATUS_MASTERED : self::STATUS_ACTIVE;
				$resolved_at = ( self::STATUS_MASTERED === $new_status ) ? $now : null;

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->update(
					$table,
					array(
						'correct_streak' => $new_streak,
						'status'         => $new_status,
						'resolved_at'    => $resolved_at,
					),
					array( 'id' => $existing->id ),
					array( '%d', '%s', '%s' ),
					array( '%d' )
				);
			}
		}
	}

	/**
	 * Get active mistakes for a student notebook.
	 *
	 * @param int $user_id Student User ID.
	 * @param int $limit   Max items.
	 * @return array<int,object>
	 */
	public static function get_active_mistakes( $user_id, $limit = 50 ) {
		global $wpdb;
		$user_id = absint( $user_id );
		$limit   = absint( $limit );

		if ( ! $user_id ) {
			return array();
		}

		$m_table = NCTB_Migrations::table( 'mistakes' );
		$q_table = NCTB_Migrations::table( 'questions' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					m.*, 
					q.prompt as question_prompt,
					q.content as question_content,
					q.question_type,
					q.difficulty,
					q.explanation as question_explanation,
					q.hint_1,
					q.hint_2
				FROM {$m_table} m
				INNER JOIN {$q_table} q ON q.id = m.question_id
				WHERE m.user_id = %d AND m.status = %s
				ORDER BY m.last_error_at DESC
				LIMIT %d",
				$user_id,
				self::STATUS_ACTIVE,
				$limit
			)
		);

		if ( ! empty( $rows ) ) {
			foreach ( $rows as &$r ) {
				$r->lesson_title = get_the_title( $r->lesson_id ) ?: '';
				if ( NCTB_Question_Types::TYPE_MCQ === $r->question_type ) {
					$r->options = NCTB_Practice_Data::get_question_options( $r->question_id, false );
				} else {
					$r->options = array();
				}
			}
		}

		return $rows;
	}

	/**
	 * Manually mark a mistake as resolved.
	 *
	 * @param int $mistake_id Mistake row ID.
	 * @param int $user_id    Student User ID.
	 * @return bool
	 */
	public static function resolve_mistake( $mistake_id, $user_id ) {
		global $wpdb;
		$mistake_id = absint( $mistake_id );
		$user_id    = absint( $user_id );
		$table      = NCTB_Migrations::table( 'mistakes' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$updated = $wpdb->update(
			$table,
			array(
				'status'      => self::STATUS_RESOLVED,
				'resolved_at' => current_time( 'mysql', true ),
			),
			array( 'id' => $mistake_id, 'user_id' => $user_id ),
			array( '%s', '%s' ),
			array( '%d', '%d' )
		);

		return false !== $updated;
	}
}
