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

			case NCTB_Question_Types::TYPE_MATH_NUMERIC:
				$res        = self::mark_math_numeric( $question->correct_answer, $given_answer );
				$is_correct = $res['is_correct'];
				$feedback   = $is_correct ? __( 'গাণিতিক সমাধান সঠিক হয়েছে! চমৎকার।', 'nctb-learning-hub' ) : __( 'গণনাটি সঠিক হয়নি। সূত্র ও হিসাব পুনরায় পরীক্ষা করুন।', 'nctb-learning-hub' );
				break;

			case NCTB_Question_Types::TYPE_MATH_EXPRESSION:
				$res        = self::mark_math_expression( $question->correct_answer, $given_answer );
				$is_correct = $res['is_correct'];
				$feedback   = $is_correct ? __( 'বীজগাণিতিক সমীকরণ / রাশিটি সম্পূর্ণ সঠিক!', 'nctb-learning-hub' ) : __( 'রাশিটি মেলেনি। চিহ্ন বা বন্ধনী পুনরায় পরীক্ষা করুন।', 'nctb-learning-hub' );
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
	 * Mark numeric mathematical values (decimals, fractions, negative numbers, percentages).
	 *
	 * @param string $correct_string Target correct value (supports pipe separation e.g. "3/4 | 0.75").
	 * @param string $student_answer Student submission.
	 * @param float  $tolerance      Accepted float delta (default 0.001).
	 * @return array<string,bool>
	 */
	protected static function mark_math_numeric( $correct_string, $student_answer, $tolerance = 0.001 ) {
		$student_answer = self::convert_bengali_numerals( trim( $student_answer ) );
		$student_num    = self::parse_numeric_value( $student_answer );

		$variants = explode( '|', $correct_string );

		foreach ( $variants as $variant ) {
			$variant_clean = self::convert_bengali_numerals( trim( $variant ) );
			// Exact string match check
			if ( strcasecmp( $variant_clean, $student_answer ) === 0 ) {
				return array( 'is_correct' => true );
			}

			// Numeric float comparison
			$variant_num = self::parse_numeric_value( $variant_clean );
			if ( null !== $student_num && null !== $variant_num ) {
				if ( abs( $student_num - $variant_num ) <= $tolerance ) {
					return array( 'is_correct' => true );
				}
			}
		}

		return array( 'is_correct' => false );
	}

	/**
	 * Mark algebraic mathematical expressions (polynomials, formulas, equations).
	 *
	 * @param string $correct_string Target correct expression (supports pipe separation).
	 * @param string $student_answer Student submission.
	 * @return array<string,bool>
	 */
	protected static function mark_math_expression( $correct_string, $student_answer ) {
		$norm_student = self::normalize_math_expression( $student_answer );
		$variants     = explode( '|', $correct_string );

		foreach ( $variants as $variant ) {
			$norm_variant = self::normalize_math_expression( $variant );
			if ( $norm_student === $norm_variant ) {
				return array( 'is_correct' => true );
			}
		}

		return array( 'is_correct' => false );
	}

	/**
	 * Normalize algebraic expression string for clean comparison.
	 *
	 * @param string $expr Expression.
	 * @return string
	 */
	public static function normalize_math_expression( $expr ) {
		$expr = self::convert_bengali_numerals( $expr );
		$expr = html_entity_decode( $expr, ENT_QUOTES, 'UTF-8' );
		$expr = mb_strtolower( trim( $expr ), 'UTF-8' );
		// Normalize latex symbols
		$expr = str_replace( array( '\\cdot', '\\times', '*' ), '*', $expr );
		$expr = str_replace( array( '\\frac', '{', '}' ), array( '', '(', ')' ), $expr );
		$expr = str_replace( array( ' ', '\t', '\n' ), '', $expr );
		return $expr;
	}

	/**
	 * Parse string into float (supports fractions like "3/4" or percentages like "75%").
	 *
	 * @param string $val Value.
	 * @return float|null
	 */
	public static function parse_numeric_value( $val ) {
		$val = trim( $val );
		if ( '' === $val ) {
			return null;
		}

		// Check percentage: "75%" -> 0.75
		if ( str_ends_with( $val, '%' ) ) {
			$num = floatval( rtrim( $val, '%' ) );
			return $num / 100.0;
		}

		// Check fraction: "3/4" -> 0.75
		if ( preg_match( '/^(-?\d+(?:\.\d+)?)\s*\/\s*(\d+(?:\.\d+)?)$/', $val, $matches ) ) {
			$denominator = floatval( $matches[2] );
			if ( 0.0 !== $denominator ) {
				return floatval( $matches[1] ) / $denominator;
			}
		}

		if ( is_numeric( $val ) ) {
			return floatval( $val );
		}

		return null;
	}

	/**
	 * Convert Bengali digits (০-৯) to English ASCII digits (0-9).
	 *
	 * @param string $str Input string.
	 * @return string
	 */
	public static function convert_bengali_numerals( $str ) {
		$bn = array( '০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯' );
		$en = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
		return str_replace( $bn, $en, (string) $str );
	}
}

