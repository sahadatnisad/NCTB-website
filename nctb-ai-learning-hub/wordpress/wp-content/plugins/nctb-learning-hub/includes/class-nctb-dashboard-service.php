<?php
/**
 * Student Dashboard Aggregation & Study Guide Service (Phase 7).
 *
 * Rules-based engine that aggregates student learning progress, active mistakes,
 * spaced revisions due, and enrolled curriculum to provide actionable study
 * recommendations without AI overhead.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Dashboard_Service
 */
class NCTB_Dashboard_Service {

	/**
	 * Get aggregated dashboard study guide data for a student.
	 *
	 * @param int $user_id Student User ID.
	 * @return array<string,mixed>
	 */
	public static function get_dashboard_data( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return array();
		}

		$profile   = NCTB_Student_Profile::get_profile( $user_id );
		$kpis      = NCTB_Progress_Service::get_user_summary( $user_id );
		$continue  = self::get_continue_learning( $user_id );
		$revisions = class_exists( 'NCTB_Spaced_Revision_Service' ) ? NCTB_Spaced_Revision_Service::get_due_reviews( $user_id, null, 5 ) : array();
		$mistakes  = class_exists( 'NCTB_Mistakes_Service' ) ? NCTB_Mistakes_Service::get_active_mistakes( $user_id, 3 ) : array();
		$mastery   = class_exists( 'NCTB_Mastery_Service' ) ? NCTB_Mastery_Service::get_all_user_mastery( $user_id ) : array();
		$books     = self::get_enrolled_books_progress( $user_id, $profile['chosen_subjects'] ?? array() );

		return array(
			'user_id'           => $user_id,
			'profile'           => $profile,
			'kpis'              => $kpis,
			'continue_learning' => $continue,
			'due_revisions'     => $revisions,
			'needs_attention'   => $mistakes,
			'concept_mastery'   => $mastery,
			'enrolled_books'    => $books,
		);
	}

	/**
	 * Get the next or currently active lesson for the student to continue.
	 *
	 * @param int $user_id Student User ID.
	 * @return array<string,mixed>|null
	 */
	public static function get_continue_learning( $user_id ) {
		global $wpdb;
		$prog_table = NCTB_Migrations::table( 'progress' );

		// 1. Look for most recent in_progress or updated lesson
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$active_row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$prog_table} WHERE user_id = %d ORDER BY updated_at DESC LIMIT 1",
				$user_id
			)
		);

		$lesson_id   = $active_row ? (int) $active_row->lesson_id : 0;
		$step_num    = $active_row ? max( 1, (int) $active_row->last_activity_step ) : 1;
		$status      = $active_row ? $active_row->status : 'not_started';

		// 2. If no progress record, fallback to the first published lesson
		if ( ! $lesson_id ) {
			$lessons = get_posts(
				array(
					'post_type'   => NCTB_Curriculum_CPT::CPT_LESSON,
					'post_status' => 'publish',
					'numberposts' => 1,
					'orderby'     => 'menu_order',
					'order'       => 'ASC',
				)
			);
			if ( ! empty( $lessons ) ) {
				$lesson_id = $lessons[0]->ID;
			}
		}

		if ( ! $lesson_id ) {
			return null;
		}

		$lesson = get_post( $lesson_id );
		if ( ! $lesson ) {
			return null;
		}

		$unit_id = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_lesson_unit( $lesson_id ) : 0;
		$book_id = ( class_exists( 'NCTB_Curriculum_CPT' ) && $unit_id ) ? NCTB_Curriculum_CPT::get_unit_book( $unit_id ) : 0;

		$activities  = class_exists( 'NCTB_Curriculum_Data' ) ? NCTB_Curriculum_Data::get_lesson_activities( $lesson_id ) : array();
		$total_steps = count( $activities ) ?: 14;

		return array(
			'lesson_id'    => $lesson_id,
			'lesson_title' => $lesson->post_title,
			'lesson_url'   => get_permalink( $lesson_id ) . '#activity-' . $step_num,
			'unit_title'   => $unit_id ? get_the_title( $unit_id ) : '',
			'book_title'   => $book_id ? get_the_title( $book_id ) : '',
			'step_num'     => $step_num,
			'total_steps'  => $total_steps,
			'status'       => $status,
			'pct'          => min( 100, round( ( $step_num / $total_steps ) * 100 ) ),
		);
	}

	/**
	 * Get progress metrics across enrolled NCTB books.
	 *
	 * @param int      $user_id         Student User ID.
	 * @param string[] $chosen_subjects Subject slugs from profile.
	 * @return array<int,array<string,mixed>>
	 */
	public static function get_enrolled_books_progress( $user_id, array $chosen_subjects ) {
		global $wpdb;
		$prog_table = NCTB_Migrations::table( 'progress' );

		$books = get_posts(
			array(
				'post_type'   => NCTB_Curriculum_CPT::CPT_BOOK,
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			)
		);

		$out = array();

		foreach ( $books as $b ) {
			$units = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_units( $b->ID ) : array();
			$total_lessons = 0;
			$lesson_ids    = array();

			foreach ( $units as $u ) {
				$u_lessons = NCTB_Curriculum_CPT::get_lessons( $u->ID );
				$total_lessons += count( $u_lessons );
				foreach ( $u_lessons as $ul ) {
					$lesson_ids[] = (int) $ul->ID;
				}
			}

			$completed_count = 0;
			if ( ! empty( $lesson_ids ) ) {
				$in_clause = implode( ',', array_map( 'absint', $lesson_ids ) );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$completed_count = (int) $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM {$prog_table} WHERE user_id = %d AND lesson_id IN ({$in_clause}) AND status = 'completed'", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$user_id
					)
				);
			}

			$pct = $total_lessons > 0 ? round( ( $completed_count / $total_lessons ) * 100 ) : 0;

			$out[] = array(
				'book_id'           => $b->ID,
				'book_title'        => $b->post_title,
				'book_url'          => get_permalink( $b->ID ),
				'total_units'       => count( $units ),
				'total_lessons'     => $total_lessons,
				'completed_lessons' => $completed_count,
				'progress_pct'      => $pct,
			);
		}

		return $out;
	}
}
