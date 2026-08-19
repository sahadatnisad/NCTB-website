<?php
/**
 * Lesson Progress Service (Phase 6).
 *
 * Tracks individual student lesson progression, step position, and completion
 * state with strict per-student isolation. Enforces separation between
 * lesson completion and concept mastery.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Progress_Service
 */
class NCTB_Progress_Service {

	const STATUS_IN_PROGRESS = 'in_progress';
	const STATUS_COMPLETED   = 'completed';

	/**
	 * Record or update a student's step progress on a lesson.
	 *
	 * @param int  $user_id      Student User ID.
	 * @param int  $lesson_id    Lesson Post ID.
	 * @param int  $step_num     Current step number (1-based).
	 * @param int  $total_steps  Total number of activities.
	 * @param bool $is_completed Explicit completion flag.
	 * @return bool|WP_Error
	 */
	public static function record_step( $user_id, $lesson_id, $step_num, $total_steps, $is_completed = false ) {
		global $wpdb;
		$user_id     = absint( $user_id );
		$lesson_id   = absint( $lesson_id );
		$step_num    = max( 1, absint( $step_num ) );
		$total_steps = max( 1, absint( $total_steps ) );

		if ( ! $user_id || ! $lesson_id ) {
			return new WP_Error( 'nctb_invalid_progress', __( 'Valid user and lesson are required.', 'nctb-learning-hub' ) );
		}

		$table = NCTB_Migrations::table( 'progress' );
		$unit_id = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_lesson_unit( $lesson_id ) : 0;
		$book_id = ( class_exists( 'NCTB_Curriculum_CPT' ) && $unit_id ) ? NCTB_Curriculum_CPT::get_unit_book( $unit_id ) : 0;

		$now = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$existing = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d AND lesson_id = %d", $user_id, $lesson_id )
		);

		$status = ( $is_completed || $step_num >= $total_steps ) ? self::STATUS_COMPLETED : self::STATUS_IN_PROGRESS;

		if ( $existing ) {
			$completed_steps = ! empty( $existing->completed_activities ) ? json_decode( $existing->completed_activities, true ) : array();
			if ( ! is_array( $completed_steps ) ) {
				$completed_steps = array();
			}
			if ( ! in_array( $step_num, $completed_steps, true ) ) {
				$completed_steps[] = $step_num;
				sort( $completed_steps );
			}

			// If already completed previously, retain completed status
			if ( self::STATUS_COMPLETED === $existing->status ) {
				$status = self::STATUS_COMPLETED;
			}

			$completed_at = ( self::STATUS_COMPLETED === $status && empty( $existing->completed_at ) ) ? $now : $existing->completed_at;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update(
				$table,
				array(
					'last_activity_step'   => $step_num,
					'completed_activities' => wp_json_encode( $completed_steps ),
					'status'               => $status,
					'completed_at'         => $completed_at,
					'updated_at'           => $now,
				),
				array( 'id' => $existing->id ),
				array( '%d', '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);
		} else {
			$completed_steps = array( $step_num );
			$completed_at    = ( self::STATUS_COMPLETED === $status ) ? $now : null;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$table,
				array(
					'user_id'              => $user_id,
					'lesson_id'            => $lesson_id,
					'unit_id'              => $unit_id,
					'book_id'              => $book_id,
					'status'               => $status,
					'last_activity_step'   => $step_num,
					'completed_activities' => wp_json_encode( $completed_steps ),
					'completed_at'         => $completed_at,
					'created_at'           => $now,
					'updated_at'           => $now,
				),
				array( '%d', '%d', '%d', '%d', '%s', '%d', '%s', '%s', '%s', '%s' )
			);
		}

		return true;
	}

	/**
	 * Get a student's progress on a single lesson.
	 *
	 * @param int $user_id   Student User ID.
	 * @param int $lesson_id Lesson Post ID.
	 * @return object|null
	 */
	public static function get_lesson_progress( $user_id, $lesson_id ) {
		global $wpdb;
		$table     = NCTB_Migrations::table( 'progress' );
		$user_id   = absint( $user_id );
		$lesson_id = absint( $lesson_id );

		if ( ! $user_id || ! $lesson_id ) {
			return null;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d AND lesson_id = %d", $user_id, $lesson_id )
		);

		if ( $row && ! empty( $row->completed_activities ) ) {
			$row->completed_activities = json_decode( $row->completed_activities, true );
		}

		return $row;
	}

	/**
	 * Get overall learning progress metrics for a student.
	 *
	 * @param int $user_id Student User ID.
	 * @return array<string,mixed>
	 */
	public static function get_user_summary( $user_id ) {
		global $wpdb;
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return array();
		}

		$progress_table = NCTB_Migrations::table( 'progress' );
		$attempts_table = NCTB_Migrations::table( 'attempts' );
		$mistakes_table = NCTB_Migrations::table( 'mistakes' );
		$schedule_table = NCTB_Migrations::table( 'review_schedule' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$completed_lessons = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$progress_table} WHERE user_id = %d AND status = %s", $user_id, self::STATUS_COMPLETED )
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$in_progress_lessons = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$progress_table} WHERE user_id = %d AND status = %s", $user_id, self::STATUS_IN_PROGRESS )
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$total_attempts = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$attempts_table} WHERE user_id = %d", $user_id )
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$active_mistakes = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$mistakes_table} WHERE user_id = %d AND status = 'active'", $user_id )
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$today = current_time( 'Y-m-d' );
		$due_reviews = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$schedule_table} WHERE user_id = %d AND status = 'pending' AND due_date <= %s", $user_id, $today )
		);

		return array(
			'user_id'             => $user_id,
			'completed_lessons'   => $completed_lessons,
			'in_progress_lessons' => $in_progress_lessons,
			'total_attempts'      => $total_attempts,
			'active_mistakes'     => $active_mistakes,
			'due_reviews'         => $due_reviews,
		);
	}
}
