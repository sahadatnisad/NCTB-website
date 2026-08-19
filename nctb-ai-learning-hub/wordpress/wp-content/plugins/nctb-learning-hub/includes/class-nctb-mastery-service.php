<?php
/**
 * Concept Mastery Service (Phase 6).
 *
 * Centralized service for calculating and updating concept-level mastery
 * scores and levels (novice, developing, proficient, mastered) based on
 * student practice attempts. Enforces separation between lesson completion
 * and academic concept mastery.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Mastery_Service
 */
class NCTB_Mastery_Service {

	const LEVEL_NOVICE     = 'novice';
	const LEVEL_DEVELOPING = 'developing';
	const LEVEL_PROFICIENT = 'proficient';
	const LEVEL_MASTERED   = 'mastered';

	/**
	 * Recalculate concept mastery for all concepts attached to an attempted question.
	 *
	 * @param int $user_id     Student User ID.
	 * @param int $question_id Question ID.
	 * @return void
	 */
	public static function recalculate_for_question( $user_id, $question_id ) {
		global $wpdb;
		$user_id     = absint( $user_id );
		$question_id = absint( $question_id );

		if ( ! $user_id || ! $question_id ) {
			return;
		}

		$concepts = NCTB_Practice_Data::get_question_concepts( $question_id );
		if ( empty( $concepts ) ) {
			return;
		}

		foreach ( $concepts as $c ) {
			self::calculate_concept_mastery( $user_id, (int) $c->id );
		}
	}

	/**
	 * Calculate and upsert mastery for a single concept and user.
	 *
	 * @param int $user_id    Student User ID.
	 * @param int $concept_id Concept ID.
	 * @return array<string,mixed> Calculated mastery data.
	 */
	public static function calculate_concept_mastery( $user_id, $concept_id ) {
		global $wpdb;
		$user_id    = absint( $user_id );
		$concept_id = absint( $concept_id );

		$qc_table  = NCTB_Migrations::table( 'question_concepts' );
		$att_table = NCTB_Migrations::table( 'attempts' );
		$m_table   = NCTB_Migrations::table( 'mastery' );

		// Query attempts for this concept
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$stats = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT 
					COUNT(a.id) as total_attempts,
					SUM(CASE WHEN a.is_correct = 1 THEN 1 ELSE 0 END) as correct_attempts,
					AVG(a.score) as avg_score,
					MAX(a.created_at) as last_attempt_at
				FROM {$att_table} a
				INNER JOIN {$qc_table} qc ON qc.question_id = a.question_id
				WHERE a.user_id = %d AND qc.concept_id = %d",
				$user_id,
				$concept_id
			)
		);

		$total_attempts   = $stats ? (int) $stats->total_attempts : 0;
		$correct_attempts = $stats ? (int) $stats->correct_attempts : 0;
		$last_attempt_at  = ( $stats && ! empty( $stats->last_attempt_at ) ) ? $stats->last_attempt_at : null;

		$score = 0.0;
		if ( $total_attempts > 0 ) {
			$score = round( ( $correct_attempts / $total_attempts ) * 100, 1 );
		}

		// Determine Mastery Level
		$level = self::LEVEL_NOVICE;
		if ( $score >= 90.0 && $correct_attempts >= 2 ) {
			$level = self::LEVEL_MASTERED;
		} elseif ( $score >= 70.0 && $correct_attempts >= 1 ) {
			$level = self::LEVEL_PROFICIENT;
		} elseif ( $score >= 40.0 ) {
			$level = self::LEVEL_DEVELOPING;
		}

		$now = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$existing = $wpdb->get_row(
			$wpdb->prepare( "SELECT id FROM {$m_table} WHERE user_id = %d AND concept_id = %d", $user_id, $concept_id )
		);

		if ( $existing ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update(
				$m_table,
				array(
					'mastery_score'    => $score,
					'mastery_level'    => $level,
					'total_attempts'   => $total_attempts,
					'correct_attempts' => $correct_attempts,
					'last_attempt_at'  => $last_attempt_at,
					'updated_at'       => $now,
				),
				array( 'id' => $existing->id ),
				array( '%f', '%s', '%d', '%d', '%s', '%s' ),
				array( '%d' )
			);
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$m_table,
				array(
					'user_id'          => $user_id,
					'concept_id'       => $concept_id,
					'mastery_score'    => $score,
					'mastery_level'    => $level,
					'total_attempts'   => $total_attempts,
					'correct_attempts' => $correct_attempts,
					'last_attempt_at'  => $last_attempt_at,
					'updated_at'       => $now,
				),
				array( '%d', '%d', '%f', '%s', '%d', '%d', '%s', '%s' )
			);
		}

		return array(
			'concept_id'       => $concept_id,
			'mastery_score'    => $score,
			'mastery_level'    => $level,
			'total_attempts'   => $total_attempts,
			'correct_attempts' => $correct_attempts,
		);
	}

	/**
	 * Get all concept mastery records for a student.
	 *
	 * @param int $user_id Student User ID.
	 * @return array<int,object>
	 */
	public static function get_all_user_mastery( $user_id ) {
		global $wpdb;
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return array();
		}

		$m_table = NCTB_Migrations::table( 'mastery' );
		$c_table = NCTB_Migrations::table( 'concepts' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT m.*, c.name as concept_name, c.subject as concept_subject, c.description as concept_desc
				FROM {$m_table} m
				INNER JOIN {$c_table} c ON c.id = m.concept_id
				WHERE m.user_id = %d
				ORDER BY m.mastery_score DESC, m.updated_at DESC",
				$user_id
			)
		);
	}
}
