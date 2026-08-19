<?php
/**
 * Lesson Activity Types registry & definitions.
 *
 * Defines the 14 gold-standard activity types that compose an interactive
 * NCTB lesson, their labels (Bangla & English), icons, default structure,
 * and sanitization rules.
 *
 * All business logic lives in this plugin class. Presentation lives in the theme.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Lesson_Activity_Types
 */
class NCTB_Lesson_Activity_Types {

	const TYPE_OBJECTIVE            = 'objective';
	const TYPE_WARMUP               = 'warmup';
	const TYPE_READING              = 'reading';
	const TYPE_VOCABULARY           = 'vocabulary';
	const TYPE_GRAMMAR              = 'grammar';
	const TYPE_EXAMPLE              = 'example';
	const TYPE_GUIDED_PRACTICE      = 'guided_practice';
	const TYPE_INDEPENDENT_PRACTICE = 'independent_practice';
	const TYPE_WRITING              = 'writing';
	const TYPE_LISTENING            = 'listening';
	const TYPE_SPEAKING             = 'speaking';
	const TYPE_SUMMARY              = 'summary';
	const TYPE_QUIZ_PLACEHOLDER     = 'quiz_placeholder';
	const TYPE_TUTOR_PLACEHOLDER    = 'tutor_placeholder';

	/**
	 * Get the full dictionary of supported activity types with metadata.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function get_all() {
		return array(
			self::TYPE_OBJECTIVE => array(
				'label_en'    => __( 'Learning Objective', 'nctb-learning-hub' ),
				'label_bn'    => 'শিখন উদ্দেশ্য (Objective)',
				'icon'        => '🎯',
				'description' => __( 'Clear learning goals and competencies for this lesson.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_WARMUP => array(
				'label_en'    => __( 'Warm-up Activity', 'nctb-learning-hub' ),
				'label_bn'    => 'ওয়ার্ম-আপ ও ব্রেনস্টর্মিং (Warm-up)',
				'icon'        => '💡',
				'description' => __( 'Thought-provoking question, image prompt, or discussion trigger to activate prior knowledge.', 'nctb-learning-hub' ),
				'has_meta'    => false,
			),
			self::TYPE_READING => array(
				'label_en'    => __( 'Main Reading / Content', 'nctb-learning-hub' ),
				'label_bn'    => 'মূল পাঠ ও বিষয়বস্তু (Reading)',
				'icon'        => '📖',
				'description' => __( 'The primary NCTB textbook passage or instructional text with paragraph numbering.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_VOCABULARY => array(
				'label_en'    => __( 'Vocabulary & Word Power', 'nctb-learning-hub' ),
				'label_bn'    => 'শব্দার্থ ও শব্দভাণ্ডার (Vocabulary)',
				'icon'        => '🔤',
				'description' => __( 'Key terms with pronunciation, parts of speech, English & Bangla definitions, and context sentences.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_GRAMMAR => array(
				'label_en'    => __( 'Grammar & Language Focus', 'nctb-learning-hub' ),
				'label_bn'    => 'গ্রামার ও ভাষারীতি (Grammar Focus)',
				'icon'        => '📐',
				'description' => __( 'Grammatical rules, syntax formulas, sentence structures, and language patterns from the text.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_EXAMPLE => array(
				'label_en'    => __( 'Worked Examples & Analysis', 'nctb-learning-hub' ),
				'label_bn'    => 'উদাহরণ ও বিশ্লেষণ (Worked Examples)',
				'icon'        => '🔍',
				'description' => __( 'Step-by-step breakdowns, sentence analyses, and contextual demonstrations.', 'nctb-learning-hub' ),
				'has_meta'    => false,
			),
			self::TYPE_GUIDED_PRACTICE => array(
				'label_en'    => __( 'Guided Practice', 'nctb-learning-hub' ),
				'label_bn'    => 'নির্দেশিত অনুশীলন (Guided Practice)',
				'icon'        => '✍️',
				'description' => __( 'Scaffolded practice exercises with interactive hint reveals and step-by-step explanations.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_INDEPENDENT_PRACTICE => array(
				'label_en'    => __( 'Independent Practice', 'nctb-learning-hub' ),
				'label_bn'    => 'একক অনুশীলন (Independent Practice)',
				'icon'        => '📝',
				'description' => __( 'Self-check exercises where students apply what they learned without immediate hints.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_WRITING => array(
				'label_en'    => __( 'Writing Task', 'nctb-learning-hub' ),
				'label_bn'    => 'লিখন দক্ষতা (Writing Task)',
				'icon'        => '🖋️',
				'description' => __( 'NCTB-aligned written composition task with guidelines, word counter, and model answer.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_LISTENING => array(
				'label_en'    => __( 'Listening Activity', 'nctb-learning-hub' ),
				'label_bn'    => 'শ্রবণ দক্ষতা (Listening Activity)',
				'icon'        => '🎧',
				'description' => __( 'Audio playback with speed controls, collapsible transcript, and listening check questions.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_SPEAKING => array(
				'label_en'    => __( 'Speaking Activity', 'nctb-learning-hub' ),
				'label_bn'    => 'কথন দক্ষতা (Speaking Activity)',
				'icon'        => '🗣️',
				'description' => __( 'Pair discussion prompt, monologue task, pronunciation guide, and practice timer.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_SUMMARY => array(
				'label_en'    => __( 'Lesson Summary & Recap', 'nctb-learning-hub' ),
				'label_bn'    => 'পাঠ সারসংক্ষেপ (Lesson Summary)',
				'icon'        => '📌',
				'description' => __( 'Key takeaway bullet points, revision highlights, and concluding summary.', 'nctb-learning-hub' ),
				'has_meta'    => false,
			),
			self::TYPE_QUIZ_PLACEHOLDER => array(
				'label_en'    => __( 'Lesson Quiz (Phase 5 Placeholder)', 'nctb-learning-hub' ),
				'label_bn'    => 'পাঠ মূল্যায়ন কুইজ (Phase 5 Placeholder)',
				'icon'        => '⚡',
				'description' => __( 'Connects to Phase 5 Practice Engine for board-standard questions and mastery tracking.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
			self::TYPE_TUTOR_PLACEHOLDER => array(
				'label_en'    => __( 'AI Tutor Assistance (Phase 9 Placeholder)', 'nctb-learning-hub' ),
				'label_bn'    => 'এআই টিউটর সহায়তা (Phase 9 Placeholder)',
				'icon'        => '🤖',
				'description' => __( 'Contextual AI tutor button linking lesson concepts to conversational AI assistance.', 'nctb-learning-hub' ),
				'has_meta'    => true,
			),
		);
	}

	/**
	 * Check if an activity type is valid.
	 *
	 * @param string $type Activity type slug.
	 * @return bool
	 */
	public static function is_valid_type( $type ) {
		$all = self::get_all();
		return isset( $all[ $type ] );
	}

	/**
	 * Get type config by key.
	 *
	 * @param string $type Activity type.
	 * @return array<string,mixed>|null
	 */
	public static function get_type_info( $type ) {
		$all = self::get_all();
		return isset( $all[ $type ] ) ? $all[ $type ] : null;
	}

	/**
	 * Sanitize raw activity payload.
	 *
	 * @param array $raw Unsanitized activity data.
	 * @return array Sanitized activity data.
	 */
	public static function sanitize_activity( array $raw ) {
		$type = isset( $raw['activity_type'] ) ? sanitize_key( $raw['activity_type'] ) : self::TYPE_READING;
		if ( ! self::is_valid_type( $type ) ) {
			$type = self::TYPE_READING;
		}

		$title   = isset( $raw['title'] ) ? sanitize_text_field( $raw['title'] ) : '';
		$content = isset( $raw['content'] ) ? wp_kses_post( $raw['content'] ) : '';
		$sort    = isset( $raw['sort_order'] ) ? intval( $raw['sort_order'] ) : 0;
		$active  = isset( $raw['is_active'] ) ? ( $raw['is_active'] ? 1 : 0 ) : 1;

		// Meta data sanitization: can be array or JSON string.
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

		$sanitized_meta = self::sanitize_meta_by_type( $type, $meta_data );

		return array(
			'activity_type' => $type,
			'title'         => $title,
			'content'       => $content,
			'meta_data'     => $sanitized_meta,
			'sort_order'    => $sort,
			'is_active'     => $active,
		);
	}

	/**
	 * Sanitize type-specific metadata.
	 *
	 * @param string $type Activity type.
	 * @param array  $meta Raw metadata array.
	 * @return array Sanitized metadata.
	 */
	public static function sanitize_meta_by_type( $type, array $meta ) {
		$out = array();

		switch ( $type ) {
			case self::TYPE_VOCABULARY:
				$words = isset( $meta['words'] ) && is_array( $meta['words'] ) ? $meta['words'] : array();
				$clean_words = array();
				foreach ( $words as $w ) {
					if ( empty( $w['term'] ) ) {
						continue;
					}
					$clean_words[] = array(
						'term'          => sanitize_text_field( $w['term'] ),
						'pronunciation' => isset( $w['pronunciation'] ) ? sanitize_text_field( $w['pronunciation'] ) : '',
						'pos'           => isset( $w['pos'] ) ? sanitize_text_field( $w['pos'] ) : '',
						'meaning_en'    => isset( $w['meaning_en'] ) ? sanitize_text_field( $w['meaning_en'] ) : '',
						'meaning_bn'    => isset( $w['meaning_bn'] ) ? sanitize_text_field( $w['meaning_bn'] ) : '',
						'example'       => isset( $w['example'] ) ? sanitize_text_field( $w['example'] ) : '',
					);
				}
				$out['words'] = $clean_words;
				break;

			case self::TYPE_GUIDED_PRACTICE:
				$hints = isset( $meta['hints'] ) && is_array( $meta['hints'] ) ? $meta['hints'] : array();
				$out['hints'] = array_map( 'sanitize_text_field', $hints );
				$out['explanation'] = isset( $meta['explanation'] ) ? sanitize_textarea_field( $meta['explanation'] ) : '';
				$out['model_answer'] = isset( $meta['model_answer'] ) ? sanitize_textarea_field( $meta['model_answer'] ) : '';
				break;

			case self::TYPE_WRITING:
				$out['word_limit']     = isset( $meta['word_limit'] ) ? sanitize_text_field( $meta['word_limit'] ) : '100-120 words';
				$out['outline']        = isset( $meta['outline'] ) ? sanitize_textarea_field( $meta['outline'] ) : '';
				$out['model_response'] = isset( $meta['model_response'] ) ? sanitize_textarea_field( $meta['model_response'] ) : '';
				break;

			case self::TYPE_LISTENING:
				$out['audio_url']    = isset( $meta['audio_url'] ) ? esc_url_raw( $meta['audio_url'] ) : '';
				$out['duration']     = isset( $meta['duration'] ) ? sanitize_text_field( $meta['duration'] ) : '';
				$out['transcript']   = isset( $meta['transcript'] ) ? wp_kses_post( $meta['transcript'] ) : '';
				$out['check_prompt'] = isset( $meta['check_prompt'] ) ? sanitize_text_field( $meta['check_prompt'] ) : '';
				break;

			case self::TYPE_SPEAKING:
				$out['prompt']         = isset( $meta['prompt'] ) ? sanitize_textarea_field( $meta['prompt'] ) : '';
				$out['time_limit']     = isset( $meta['time_limit'] ) ? sanitize_text_field( $meta['time_limit'] ) : '2 minutes';
				$out['talking_points'] = isset( $meta['talking_points'] ) && is_array( $meta['talking_points'] ) ? array_map( 'sanitize_text_field', $meta['talking_points'] ) : array();
				break;

			case self::TYPE_GRAMMAR:
				$out['rules']     = isset( $meta['rules'] ) && is_array( $meta['rules'] ) ? array_map( 'sanitize_text_field', $meta['rules'] ) : array();
				$out['formula']   = isset( $meta['formula'] ) ? sanitize_text_field( $meta['formula'] ) : '';
				$out['structure'] = isset( $meta['structure'] ) ? sanitize_textarea_field( $meta['structure'] ) : '';
				break;

			case self::TYPE_QUIZ_PLACEHOLDER:
				$out['question_count'] = isset( $meta['question_count'] ) ? absint( $meta['question_count'] ) : 5;
				$out['target_time']    = isset( $meta['target_time'] ) ? sanitize_text_field( $meta['target_time'] ) : '5 mins';
				$out['note']           = isset( $meta['note'] ) ? sanitize_text_field( $meta['note'] ) : '';
				break;

			case self::TYPE_TUTOR_PLACEHOLDER:
				$out['context_topic']     = isset( $meta['context_topic'] ) ? sanitize_text_field( $meta['context_topic'] ) : '';
				$out['suggested_prompts'] = isset( $meta['suggested_prompts'] ) && is_array( $meta['suggested_prompts'] ) ? array_map( 'sanitize_text_field', $meta['suggested_prompts'] ) : array();
				break;

			default:
				// Generic sanitized key-value store.
				foreach ( $meta as $k => $v ) {
					$clean_k = sanitize_key( $k );
					if ( is_string( $v ) ) {
						$out[ $clean_k ] = sanitize_textarea_field( $v );
					} elseif ( is_numeric( $v ) ) {
						$out[ $clean_k ] = $v;
					}
				}
				break;
		}

		return $out;
	}
}
