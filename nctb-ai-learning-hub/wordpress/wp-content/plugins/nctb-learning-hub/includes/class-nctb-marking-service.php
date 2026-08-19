<?php
/**
 * Central Question Marking Service.
 *
 * Single source of truth for marking practice submissions without AI.
 * Handles MCQ, fill-in-the-blank, short text answer, and error correction
 * with normalization, multiple accepted variants, hint penalty scoring,
 * and educational feedback.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Marking_Service
 */
class NCTB_Marking_Service {

	/**
	 * Evaluate a student's answer against a question.
	 *
	 * @param object $question     Question record object (with options if MCQ).
	 * @param string $given_answer Raw student submission.
	 * @param int    $hints_used   Number of hints requested.
	 * @return array<string,mixed> Evaluation result.
	 */
	public static function evaluate( $question, $given_answer, $hints_used = 0 ) {
		$type         = $question->question_type;
		$given_answer = trim( (string) $given_answer );
		$hints_used   = absint( $hints_used );

		$is_correct = false;
		$feedback   = '';

		switch ( $type ) {
			case NCTB_Question_Types::TYPE_MCQ:
				$res        = self::mark_mcq( $question, $given_answer );
				$is_correct = $res['is_correct'];
				$feedback   = $res['feedback'];
				break;

			case NCTB_Question_Types::TYPE_FILL_IN_BLANK:
				$res        = self::mark_text_match( $question->correct_answer, $given_answer );
				$is_correct = $res['is_correct'];
				$feedback   = $is_correct ? __( 'Excellent! Correct answer.', 'nctb-learning-hub' ) : __( 'Not quite right. Check the text carefully.', 'nctb-learning-hub' );
				break;

			case NCTB_Question_Types::TYPE_SHORT_ANSWER:
				$res        = self::mark_text_match( $question->correct_answer, $given_answer );
				$is_correct = $res['is_correct'];
				$feedback   = $is_correct ? __( 'Correct! Well done.', 'nctb-learning-hub' ) : __( 'Incorrect. Review the passage and try again.', 'nctb-learning-hub' );
				break;

			case NCTB_Question_Types::TYPE_ERROR_CORRECTION:
				$res        = self::mark_text_match( $question->correct_answer, $given_answer );
				$is_correct = $res['is_correct'];
				$feedback   = $is_correct ? __( 'Correctly identified and fixed the error!', 'nctb-learning-hub' ) : __( 'Incorrect correction. Pay attention to verb tenses or subject-verb agreement.', 'nctb-learning-hub' );
				break;

			default:
				$is_correct = false;
				$feedback   = __( 'Unknown question type.', 'nctb-learning-hub' );
				break;
		}

		// Calculate score: 1.0 base, small reduction if hints were used (min 0.6)
		$score = 0.0;
		if ( $is_correct ) {
			$penalty = min( 0.4, $hints_used * 0.15 );
			$score   = round( max( 0.6, 1.0 - $penalty ), 2 );
		}

		return array(
			'is_correct'   => $is_correct,
			'score'        => $score,
			'feedback'     => $feedback,
			'hints_used'   => $hints_used,
			'explanation'  => ( $is_correct || ! empty( $question->explanation ) ) ? $question->explanation : '',
			'given_answer' => $given_answer,
		);
	}

	/**
	 * Mark an MCQ question.
	 *
	 * @param object $question     Question object with options.
	 * @param string $given_answer Option key (e.g. 'A', 'B') or Option ID.
	 * @return array<string,mixed>
	 */
	protected static function mark_mcq( $question, $given_answer ) {
		$options = NCTB_Practice_Data::get_question_options( $question->id, true );
		$matched = null;

		foreach ( $options as $opt ) {
			if ( (string) $opt->id === $given_answer || strcasecmp( $opt->option_key, $given_answer ) === 0 ) {
				$matched = $opt;
				break;
			}
		}

		if ( ! $matched ) {
			return array(
				'is_correct' => false,
				'feedback'   => __( 'Please select a valid option.', 'nctb-learning-hub' ),
			);
		}

		$is_correct = (bool) $matched->is_correct;
		$feedback   = ! empty( $matched->feedback ) ? $matched->feedback : ( $is_correct ? __( 'Correct answer! Great job.', 'nctb-learning-hub' ) : __( 'Incorrect option. Try again or check the hint.', 'nctb-learning-hub' ) );

		return array(
			'is_correct' => $is_correct,
			'feedback'   => $feedback,
		);
	}

	/**
	 * Mark text matching (for fill in blank, short answer, error correction).
	 *
	 * Supports pipe-separated multiple accepted answers:
	 * e.g. "27 | twenty seven | twenty-seven | 27 years"
	 *
	 * @param string $correct_string Target answer string.
	 * @param string $student_answer Student submission.
	 * @return array<string,bool>
	 */
	protected static function mark_text_match( $correct_string, $student_answer ) {
		$norm_student = self::normalize_text( $student_answer );
		if ( '' === $norm_student ) {
			return array( 'is_correct' => false );
		}

		$variants = array_map( array( __CLASS__, 'normalize_text' ), explode( '|', $correct_string ) );

		foreach ( $variants as $variant ) {
			if ( '' !== $variant && $variant === $norm_student ) {
				return array( 'is_correct' => true );
			}
		}

		return array( 'is_correct' => false );
	}

	/**
	 * Normalize text for clean deterministic comparison:
	 * lowercase, trim, remove non-alphanumeric punctuation, collapse whitespace.
	 *
	 * @param string $text Raw text.
	 * @return string Normalized text.
	 */
	public static function normalize_text( $text ) {
		$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
		$text = mb_strtolower( trim( $text ), 'UTF-8' );
		// Replace curly quotes with straight quotes
		$text = str_replace( array( '’', '‘', '“', '”' ), array( "'", "'", '"', '"' ), $text );
		// Remove trailing full stops, commas, question marks
		$text = trim( $text, " \t\n\r\0\x0B.,?!;:" );
		// Collapse multiple spaces
		$text = preg_replace( '/\s+/', ' ', $text );
		return $text;
	}
}
