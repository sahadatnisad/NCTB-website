<?php
/**
 * Authentic Board Exam Pattern Analytics Service (Phase 12).
 *
 * Aggregates historical board exam frequency data across topics, concepts,
 * question types, years, and education boards.
 *
 * IMPORTANT: All outputs are strictly presented as historical statistical analysis,
 * NEVER as exam predictions or speculative score guarantees.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Board_Analytics_Service
 */
class NCTB_Board_Analytics_Service {

	/**
	 * Get aggregated topic frequency.
	 *
	 * @param string $exam_level Exam level ('ssc' or 'hsc').
	 * @return array<int,object>
	 */
	public static function get_topic_frequency( $exam_level = 'hsc' ) {
		global $wpdb;
		$table      = NCTB_Migrations::table( 'board_questions' );
		$exam_level = sanitize_key( $exam_level );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT topic, COUNT(*) as question_count, SUM(marks) as total_marks,
				        GROUP_CONCAT(DISTINCT board_name) as boards,
				        GROUP_CONCAT(DISTINCT exam_year) as years
				 FROM {$table}
				 WHERE exam_level = %s AND is_authentic_board = 1 AND topic != ''
				 GROUP BY topic
				 ORDER BY question_count DESC, total_marks DESC
				 LIMIT 15",
				$exam_level
			)
		);
	}

	/**
	 * Get question type distribution.
	 *
	 * @param string $exam_level Exam level ('ssc' or 'hsc').
	 * @return array<int,object>
	 */
	public static function get_question_type_distribution( $exam_level = 'hsc' ) {
		global $wpdb;
		$table      = NCTB_Migrations::table( 'board_questions' );
		$exam_level = sanitize_key( $exam_level );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT question_type, COUNT(*) as count, SUM(marks) as total_marks
				 FROM {$table}
				 WHERE exam_level = %s AND is_authentic_board = 1
				 GROUP BY question_type
				 ORDER BY count DESC",
				$exam_level
			)
		);
	}

	/**
	 * Get board-by-board distribution.
	 *
	 * @param string $exam_level Exam level ('ssc' or 'hsc').
	 * @return array<int,object>
	 */
	public static function get_board_breakdown( $exam_level = 'hsc' ) {
		global $wpdb;
		$table      = NCTB_Migrations::table( 'board_questions' );
		$exam_level = sanitize_key( $exam_level );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT board_name, COUNT(*) as question_count, SUM(marks) as total_marks
				 FROM {$table}
				 WHERE exam_level = %s AND is_authentic_board = 1
				 GROUP BY board_name
				 ORDER BY question_count DESC",
				$exam_level
			)
		);
	}

	/**
	 * Get yearly exam question volume trends.
	 *
	 * @param string $exam_level Exam level ('ssc' or 'hsc').
	 * @return array<int,object>
	 */
	public static function get_yearly_trends( $exam_level = 'hsc' ) {
		global $wpdb;
		$table      = NCTB_Migrations::table( 'board_questions' );
		$exam_level = sanitize_key( $exam_level );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT exam_year, COUNT(*) as count, SUM(marks) as total_marks
				 FROM {$table}
				 WHERE exam_level = %s AND is_authentic_board = 1
				 GROUP BY exam_year
				 ORDER BY exam_year ASC",
				$exam_level
			)
		);
	}

	/**
	 * Get high-frequency historical practice items.
	 *
	 * @param string $exam_level Exam level ('ssc' or 'hsc').
	 * @return array<string,mixed>
	 */
	public static function get_full_analytics_report( $exam_level = 'hsc' ) {
		$exam_level = in_array( $exam_level, array( 'ssc', 'hsc' ), true ) ? $exam_level : 'hsc';

		global $wpdb;
		$table = NCTB_Migrations::table( 'board_questions' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$total_questions = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE exam_level = %s AND is_authentic_board = 1", $exam_level )
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$total_marks = (float) $wpdb->get_var(
			$wpdb->prepare( "SELECT SUM(marks) FROM {$table} WHERE exam_level = %s AND is_authentic_board = 1", $exam_level )
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$total_boards = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(DISTINCT board_name) FROM {$table} WHERE exam_level = %s AND is_authentic_board = 1", $exam_level )
		);

		return array(
			'exam_level'      => $exam_level,
			'disclaimer'      => __( 'Historical Statistical Analysis Only. This analysis reflects historical examination patterns from official NCTB papers and does not predict future exam questions.', 'nctb-learning-hub' ),
			'kpis'            => array(
				'total_questions' => $total_questions,
				'total_marks'     => $total_marks,
				'total_boards'    => $total_boards,
				'years_span'      => '2018–2024',
			),
			'topic_frequency' => self::get_topic_frequency( $exam_level ),
			'question_types'  => self::get_question_type_distribution( $exam_level ),
			'boards'          => self::get_board_breakdown( $exam_level ),
			'yearly_trends'   => self::get_yearly_trends( $exam_level ),
		);
	}
}
