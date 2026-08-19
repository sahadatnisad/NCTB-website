<?php
/**
 * Contextual AI Tutor Orchestrator (Phase 9).
 *
 * Core engine handling student tutor queries, action types, prompt safety,
 * context assembly, model execution, and quota accounting.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_AI_Tutor
 */
class NCTB_AI_Tutor {

	const ACTION_EXPLAIN   = 'explain';
	const ACTION_BANGLA    = 'bangla';
	const ACTION_HINT      = 'hint';
	const ACTION_EXAMPLE   = 'example';
	const ACTION_WHY_WRONG = 'why_wrong';
	const ACTION_CHAT      = 'free_chat';

	/**
	 * Process a student query to the AI Tutor.
	 *
	 * @param int         $user_id     Student User ID.
	 * @param int         $lesson_id   Lesson Post ID.
	 * @param string      $action_type Action type (explain, bangla, hint, example, why_wrong, free_chat).
	 * @param string      $prompt      User prompt or query text.
	 * @param int|null    $question_id Optional question ID.
	 * @param int         $step_num    Current activity step number.
	 * @return array<string,mixed> Tutor response payload.
	 */
	public static function ask( $user_id, $lesson_id, $action_type = self::ACTION_EXPLAIN, $prompt = '', $question_id = null, $step_num = 1 ) {
		$user_id     = absint( $user_id ) ?: 1;
		$lesson_id   = absint( $lesson_id );
		$question_id = absint( $question_id );
		$step_num    = max( 1, absint( $step_num ) );
		$action_type = sanitize_key( $action_type ) ?: self::ACTION_EXPLAIN;
		$prompt      = sanitize_textarea_field( $prompt );

		// 1. Quota Check
		$quota = NCTB_AI_Usage::check_daily_quota( $user_id );
		if ( ! $quota['allowed'] ) {
			return array(
				'success'   => false,
				'error'     => 'quota_exceeded',
				'message'   => __( 'আজকের এআই টিউটর দৈনিক লিমিট শেষ হয়েছে। আগামীকাল আবার ব্যবহার করতে পারবেন অথবা অল-অ্যাক্সেস পাস আপগ্রেড করুন।', 'nctb-learning-hub' ),
				'remaining' => 0,
			);
		}

		// 2. Build Grounded Context
		$system_prompt = NCTB_AI_Context_Builder::build_system_prompt( $user_id, $lesson_id, $question_id, $step_num );

		// 3. Format message content based on action type
		$formatted_user_prompt = self::format_user_prompt( $action_type, $prompt );

		$messages = array(
			array(
				'role'    => 'user',
				'content' => $formatted_user_prompt,
			),
		);

		// 4. Generate Response via Server-Side Adapter
		$result = NCTB_AI_Adapter::generate_response( $system_prompt, $messages );

		// 5. Record Usage & Conversation History
		NCTB_AI_Usage::record_interaction(
			$user_id,
			$lesson_id,
			$action_type,
			$formatted_user_prompt,
			$result['content'],
			$result['provider'],
			$result['tokens_used']
		);

		$new_quota = NCTB_AI_Usage::check_daily_quota( $user_id );

		return array(
			'success'     => true,
			'action_type' => $action_type,
			'content'     => $result['content'],
			'provider'    => $result['provider'],
			'tokens_used' => $result['tokens_used'],
			'remaining'   => $new_quota['remaining'],
		);
	}

	/**
	 * Format user prompt based on action type.
	 *
	 * @param string $action_type Action type.
	 * @param string $prompt      Raw prompt.
	 * @return string Formatted user prompt.
	 */
	protected static function format_user_prompt( $action_type, $prompt ) {
		switch ( $action_type ) {
			case self::ACTION_EXPLAIN:
				return $prompt ? "Please explain this concept simply: {$prompt}" : 'Please explain the main concept of this activity step in simple terms.';
			case self::ACTION_BANGLA:
				return $prompt ? "Please explain this in Bengali with key English terms: {$prompt}" : 'Please explain this lesson passage and key vocabulary in natural Bengali.';
			case self::ACTION_HINT:
				return $prompt ? "Give me a clue for this without telling the answer: {$prompt}" : 'Please give me a Socratic clue to help me solve this step without giving away the direct answer.';
			case self::ACTION_WHY_WRONG:
				return $prompt ? "Why was my answer wrong: {$prompt}" : 'Can you explain why my last practice attempt was incorrect and how I should approach thinking about it?';
			case self::ACTION_EXAMPLE:
				return $prompt ? "Give an example for: {$prompt}" : 'Can you give me another realistic sentence example using this vocabulary word or grammar rule?';
			case self::ACTION_CHAT:
			default:
				return $prompt ?: 'Can you help me understand this lesson?';
		}
	}
}
