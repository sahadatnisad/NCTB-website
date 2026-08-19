<?php
/**
 * Spaced Revision & Review Scheduling Service (Phase 6).
 *
 * Implements automated spaced repetition intervals (1 day → 3 days → 7 days →
 * 14 days → 30 days) for practice questions, mistake revisions, and concept
 * refreshers.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Spaced_Revision_Service
 */
class NCTB_Spaced_Revision_Service {

	const STATUS_PENDING   = 'pending';
	const STATUS_COMPLETED = 'completed';

	/**
	 * Interval ladder in days based on consecutive successful reviews.
	 *
	 * @var int[]
	 */
	protected static $intervals = array( 1, 3, 7, 14, 30 );

	/**
	 * Schedule a spaced review for an item.
	 *
	 * @param int    $user_id       Student User ID.
	 * @param string $item_type     'question', 'concept', or 'lesson'.
	 * @param int    $item_id       Target Item ID.
	 * @param int    $lesson_id     Lesson Post ID.
	 * @param int    $interval_days Days until due.
	 * @return bool
	 */
	public static function schedule_review( $user_id, $item_type, $item_id, $lesson_id = 0, $interval_days = 1 ) {
		global $wpdb;
		$user_id       = absint( $user_id );
		$item_id       = absint( $item_id );
		$lesson_id     = absint( $lesson_id );
		$item_type     = sanitize_key( $item_type ) ?: 'question';
		$interval_days = max( 1, absint( $interval_days ) );

		if ( ! $user_id || ! $item_id ) {
			return false;
		}

		$table = NCTB_Migrations::table( 'review_schedule' );
		$due_date = gmdate( 'Y-m-d', strtotime( "+{$interval_days} days", current_time( 'timestamp', true ) ) );
		$now      = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$existing = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, repetition_count FROM {$table} WHERE user_id = %d AND item_type = %s AND item_id = %d AND status = %s",
				$user_id,
				$item_type,
				$item_id,
				self::STATUS_PENDING
			)
		);

		if ( $existing ) {
			// Update due date
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update(
				$table,
				array(
					'due_date'      => $due_date,
					'interval_days' => $interval_days,
				),
				array( 'id' => $existing->id ),
				array( '%s', '%d' ),
				array( '%d' )
			);
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$table,
				array(
					'user_id'          => $user_id,
					'item_type'        => $item_type,
					'item_id'          => $item_id,
					'lesson_id'        => $lesson_id,
					'interval_days'    => $interval_days,
					'ease_factor'      => 2.5,
					'repetition_count' => 0,
					'due_date'         => $due_date,
					'status'           => self::STATUS_PENDING,
					'created_at'       => $now,
				),
				array( '%d', '%s', '%d', '%d', '%d', '%f', '%d', '%s', '%s', '%s' )
			);
		}

		return true;
	}

	/**
	 * Get items currently due for revision for a student.
	 *
	 * @param int         $user_id Student User ID.
	 * @param string|null $date    Target date (defaults to today).
	 * @param int         $limit   Max items.
	 * @return array<int,object>
	 */
	public static function get_due_reviews( $user_id, $date = null, $limit = 50 ) {
		global $wpdb;
		$user_id = absint( $user_id );
		$limit   = absint( $limit );
		$date    = $date ? sanitize_text_field( $date ) : current_time( 'Y-m-d' );

		if ( ! $user_id ) {
			return array();
		}

		$table   = NCTB_Migrations::table( 'review_schedule' );
		$q_table = NCTB_Migrations::table( 'questions' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					r.*,
					q.prompt as question_prompt,
					q.question_type,
					q.difficulty,
					q.explanation as question_explanation
				FROM {$table} r
				LEFT JOIN {$q_table} q ON q.id = r.item_id AND r.item_type = 'question'
				WHERE r.user_id = %d AND r.status = %s AND r.due_date <= %s
				ORDER BY r.due_date ASC, r.id ASC
				LIMIT %d",
				$user_id,
				self::STATUS_PENDING,
				$date,
				$limit
			)
		);

		if ( ! empty( $rows ) ) {
			foreach ( $rows as &$r ) {
				$r->lesson_title = get_the_title( $r->lesson_id ) ?: '';
				if ( 'question' === $r->item_type ) {
					$r->options = NCTB_Practice_Data::get_question_options( $r->item_id, false );
				}
			}
		}

		return $rows;
	}

	/**
	 * Complete a spaced review and schedule next interval.
	 *
	 * @param int   $review_id Review schedule row ID.
	 * @param int   $user_id   Student User ID.
	 * @param float $score     Score (0.0 to 1.0).
	 * @return bool
	 */
	public static function complete_review( $review_id, $user_id, $score = 1.0 ) {
		global $wpdb;
		$review_id = absint( $review_id );
		$user_id   = absint( $user_id );
		$table     = NCTB_Migrations::table( 'review_schedule' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$item = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND user_id = %d", $review_id, $user_id )
		);

		if ( ! $item ) {
			return false;
		}

		$repetition = (int) $item->repetition_count;
		if ( $score >= 0.7 ) {
			$repetition++;
			$interval_idx = min( count( self::$intervals ) - 1, $repetition );
			$next_days    = self::$intervals[ $interval_idx ];
		} else {
			$repetition = 0;
			$next_days  = 1;
		}

		$now          = current_time( 'mysql', true );
		$new_due_date = gmdate( 'Y-m-d', strtotime( "+{$next_days} days", current_time( 'timestamp', true ) ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->update(
			$table,
			array(
				'repetition_count' => $repetition,
				'interval_days'    => $next_days,
				'due_date'         => $new_due_date,
				'last_reviewed_at' => $now,
				'status'           => self::STATUS_PENDING,
			),
			array( 'id' => $item->id ),
			array( '%d', '%d', '%s', '%s', '%s' ),
			array( '%d' )
		);

		return true;
	}
}
