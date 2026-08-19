<?php
/**
 * Practice Question Types registry & definition.
 *
 * Defines the question types supported by the practice engine (MCQ,
 * fill-in-the-blank, short text answer, error correction), difficulty levels,
 * verification statuses, and validation/sanitization helpers.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Question_Types
 */
class NCTB_Question_Types {

	const TYPE_MCQ              = 'mcq';
	const TYPE_FILL_IN_BLANK    = 'fill_in_blank';
	const TYPE_SHORT_ANSWER     = 'short_answer';
	const TYPE_ERROR_CORRECTION = 'error_correction';
	const TYPE_MATH_NUMERIC     = 'math_numeric';
	const TYPE_MATH_EXPRESSION  = 'math_expression';

	const DIFFICULTY_EASY   = 'easy';
	const DIFFICULTY_MEDIUM = 'medium';
	const DIFFICULTY_HARD   = 'hard';

	const STATUS_VERIFIED     = 'verified';
	const STATUS_DRAFT        = 'draft';
	const STATUS_NEEDS_REVIEW = 'needs_review';

	const SOURCE_NCTB_TEXTBOOK = 'nctb_textbook';
	const SOURCE_BOARD_EXAM    = 'board_exam';
	const SOURCE_MODEL_TEST    = 'model_test';
	const SOURCE_TEACHER       = 'teacher_created';

	/**
	 * Get all registered question types with metadata.
	 *
	 * @return array<string,array<string,string>>
	 */
	public static function get_all() {
		return array(
			self::TYPE_MCQ => array(
				'label_en' => __( 'Multiple Choice (MCQ)', 'nctb-learning-hub' ),
				'label_bn' => 'বহুনির্বাচনী প্রশ্ন (MCQ)',
				'icon'     => '🔘',
			),
			self::TYPE_FILL_IN_BLANK => array(
				'label_en' => __( 'Fill in the Blank', 'nctb-learning-hub' ),
				'label_bn' => 'শূন্যস্থান পূরণ (Fill in the Blank)',
				'icon'     => '✏️',
			),
			self::TYPE_SHORT_ANSWER => array(
				'label_en' => __( 'Short Text Answer', 'nctb-learning-hub' ),
				'label_bn' => 'সংক্ষিপ্ত উত্তর (Short Answer)',
				'icon'     => '📝',
			),
			self::TYPE_ERROR_CORRECTION => array(
				'label_en' => __( 'Error Correction', 'nctb-learning-hub' ),
				'label_bn' => 'ভুল সংশোধন (Error Correction)',
				'icon'     => '🔍',
			),
			self::TYPE_MATH_NUMERIC => array(
				'label_en' => __( 'Math Numeric (Number / Fraction)', 'nctb-learning-hub' ),
				'label_bn' => 'গাণিতিক মান / ভগ্নাংশ (Numeric)',
				'icon'     => '🔢',
			),
			self::TYPE_MATH_EXPRESSION => array(
				'label_en' => __( 'Math Algebraic Expression', 'nctb-learning-hub' ),
				'label_bn' => 'বীজগাণিতিক সমীকরণ / রাশি (Expression)',
				'icon'     => '📐',
			),
		);
	}

	/**
	 * Check if a question type is valid.
	 *
	 * @param string $type Type slug.
	 * @return bool
	 */
	public static function is_valid_type( $type ) {
		$all = self::get_all();
		return isset( $all[ $type ] );
	}

	/**
	 * Sanitize a question data array.
	 *
	 * @param array $raw Unsanitized question data.
	 * @return array Sanitized question data.
	 */
	public static function sanitize_question( array $raw ) {
		$type = isset( $raw['question_type'] ) ? sanitize_key( $raw['question_type'] ) : self::TYPE_MCQ;
		if ( ! self::is_valid_type( $type ) ) {
			$type = self::TYPE_MCQ;
		}

		$diff = isset( $raw['difficulty'] ) ? sanitize_key( $raw['difficulty'] ) : self::DIFFICULTY_MEDIUM;
		if ( ! in_array( $diff, array( self::DIFFICULTY_EASY, self::DIFFICULTY_MEDIUM, self::DIFFICULTY_HARD ), true ) ) {
			$diff = self::DIFFICULTY_MEDIUM;
		}

		$status = isset( $raw['verification_status'] ) ? sanitize_key( $raw['verification_status'] ) : self::STATUS_VERIFIED;
		if ( ! in_array( $status, array( self::STATUS_VERIFIED, self::STATUS_DRAFT, self::STATUS_NEEDS_REVIEW ), true ) ) {
			$status = self::STATUS_VERIFIED;
		}

		$source = isset( $raw['source_type'] ) ? sanitize_key( $raw['source_type'] ) : self::SOURCE_NCTB_TEXTBOOK;

		// Meta data decoding & sanitizing
		$meta_data = array();
		if ( isset( $raw['meta_data'] ) ) {
			if ( is_string( $raw['meta_data'] ) ) {
				$decoded = json_decode( wp_unslash( $raw['meta_data'] ), true );
				if ( is_array( $decoded ) ) {
					$meta_data = $decoded;
				}
			} elseif ( is_array( $raw['meta_data'] ) ) {
				$meta_data = $raw['meta_data'];
			}
		}

		return array(
			'lesson_id'           => isset( $raw['lesson_id'] ) ? absint( $raw['lesson_id'] ) : 0,
			'question_type'       => $type,
			'prompt'              => isset( $raw['prompt'] ) ? wp_kses_post( $raw['prompt'] ) : '',
			'content'             => isset( $raw['content'] ) ? wp_kses_post( $raw['content'] ) : '',
			'difficulty'          => $diff,
			'correct_answer'      => isset( $raw['correct_answer'] ) ? sanitize_text_field( $raw['correct_answer'] ) : '',
			'explanation'         => isset( $raw['explanation'] ) ? wp_kses_post( $raw['explanation'] ) : '',
			'hint_1'              => isset( $raw['hint_1'] ) ? sanitize_text_field( $raw['hint_1'] ) : '',
			'hint_2'              => isset( $raw['hint_2'] ) ? sanitize_text_field( $raw['hint_2'] ) : '',
			'hint_3'              => isset( $raw['hint_3'] ) ? sanitize_text_field( $raw['hint_3'] ) : '',
			'source_type'         => $source,
			'verification_status' => $status,
			'meta_data'           => $meta_data,
			'sort_order'          => isset( $raw['sort_order'] ) ? intval( $raw['sort_order'] ) : 0,
			'is_active'           => isset( $raw['is_active'] ) ? ( $raw['is_active'] ? 1 : 0 ) : 1,
		);
	}

	/**
	 * Sanitize MCQ options array.
	 *
	 * @param array $raw_options Array of raw option rows.
	 * @return array<int,array<string,mixed>>
	 */
	public static function sanitize_options( array $raw_options ) {
		$clean = array();
		$order = 0;

		foreach ( $raw_options as $opt ) {
			if ( ! is_array( $opt ) || empty( $opt['option_text'] ) ) {
				continue;
			}
			$clean[] = array(
				'option_key'  => isset( $opt['option_key'] ) ? sanitize_text_field( $opt['option_key'] ) : chr( 65 + $order ),
				'option_text' => sanitize_text_field( $opt['option_text'] ),
				'is_correct'  => ! empty( $opt['is_correct'] ) ? 1 : 0,
				'feedback'    => isset( $opt['feedback'] ) ? sanitize_text_field( $opt['feedback'] ) : '',
				'sort_order'  => $order,
			);
			$order++;
		}

		return $clean;
	}
}
