<?php
/**
 * Progressive Hint Service.
 *
 * Implements educational hint hierarchy (Level 1 → Level 2 → Level 3)
 * so students receive scaffolded clues without immediate solution spoilers.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Hint_Service
 */
class NCTB_Hint_Service {

	/**
	 * Get a progressive hint for a question.
	 *
	 * @param int $question_id     Question ID.
	 * @param int $requested_level 1, 2, or 3.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function get_hint( $question_id, $requested_level = 1 ) {
		$question = NCTB_Practice_Data::get_question( $question_id, false );
		if ( ! $question ) {
			return new WP_Error( 'nctb_not_found', __( 'Question not found.', 'nctb-learning-hub' ), array( 'status' => 404 ) );
		}

		$level = max( 1, min( 3, absint( $requested_level ) ) );
		$hint_field = 'hint_' . $level;
		$hint_text  = ! empty( $question->$hint_field ) ? $question->$hint_field : '';

		// If requested level is empty, fallback to available lower hint
		if ( empty( $hint_text ) ) {
			if ( ! empty( $question->hint_1 ) ) {
				$hint_text = $question->hint_1;
				$level = 1;
			} else {
				$hint_text = __( 'Review the passage and focus on key historical milestones and vocabulary in this section.', 'nctb-learning-hub' );
			}
		}

		$next_level = $level + 1;
		$next_field = 'hint_' . $next_level;
		$has_next   = ( $next_level <= 3 && ! empty( $question->$next_field ) );

		return array(
			'question_id'         => (int) $question->id,
			'hint_level'          => $level,
			'hint_text'           => $hint_text,
			'next_hint_available' => $has_next,
			'next_hint_level'     => $has_next ? $next_level : null,
		);
	}
}
