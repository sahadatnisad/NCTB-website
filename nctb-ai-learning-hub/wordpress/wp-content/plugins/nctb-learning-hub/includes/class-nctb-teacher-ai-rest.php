<?php
/**
 * Teacher AI REST API Controller (Phase 19).
 *
 * Exposes server-side AI endpoints for Teacher 45-Minute Lesson Planner,
 * Classroom Quiz Generator, and Misconception Diagnostics with strict
 * entitlement checks and daily quota enforcement.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Teacher_AI_REST
 */
class NCTB_Teacher_AI_REST extends WP_REST_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'nctb/v1';

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// POST /nctb/v1/teacher/ai/lesson-plan
		register_rest_route(
			$this->namespace,
			'/teacher/ai/lesson-plan',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'generate_lesson_plan' ),
					'permission_callback' => array( $this, 'check_teacher_auth' ),
				),
			)
		);

		// POST /nctb/v1/teacher/ai/quiz-maker
		register_rest_route(
			$this->namespace,
			'/teacher/ai/quiz-maker',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'generate_quiz' ),
					'permission_callback' => array( $this, 'check_teacher_auth' ),
				),
			)
		);

		// POST /nctb/v1/teacher/ai/misconceptions
		register_rest_route(
			$this->namespace,
			'/teacher/ai/misconceptions',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'diagnose_misconceptions' ),
					'permission_callback' => array( $this, 'check_teacher_auth' ),
				),
			)
		);

		// GET /nctb/v1/teacher/ai/quota
		register_rest_route(
			$this->namespace,
			'/teacher/ai/quota',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_ai_quota' ),
					'permission_callback' => array( $this, 'check_teacher_auth' ),
				),
			)
		);
	}

	/**
	 * Permission callback: verify user is logged in.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return bool|WP_Error
	 */
	public function check_teacher_auth( $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_not_logged_in', __( 'You must be logged in to use Teacher AI tools.', 'nctb-learning-hub' ), array( 'status' => 401 ) );
		}
		return true;
	}

	/**
	 * Generate 45-min Lesson Plan.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function generate_lesson_plan( $request ) {
		$user_id = get_current_user_id();

		// 1. Entitlement check
		$ent = NCTB_Entitlements::can_access_ai( $user_id );
		if ( ! $ent['granted'] ) {
			return new WP_Error(
				'ai_paywall_required',
				__( 'Active AI Pass or Subscription required to use Teacher AI Lesson Planner.', 'nctb-learning-hub' ),
				array(
					'status'       => 403,
					'upgrade_url'  => home_url( '/pricing' ),
					'reason'       => $ent['reason'],
				)
			);
		}

		// 2. Quota check
		$quota = NCTB_AI_Usage::check_quota( $user_id );
		if ( ! $quota['allowed'] ) {
			return new WP_Error(
				'quota_exceeded',
				__( 'Daily AI quota reached. Resets at midnight.', 'nctb-learning-hub' ),
				array( 'status' => 429 )
			);
		}

		$params   = $request->get_json_params();
		$class    = sanitize_text_field( $params['class'] ?? 'Class 9-10 (SSC)' );
		$subject  = sanitize_text_field( $params['subject'] ?? 'English 2nd Paper' );
		$topic    = sanitize_text_field( $params['topic'] ?? 'Right Form of Verbs' );
		$duration = absint( $params['duration'] ?? 45 );

		$prompt = NCTB_AI_Context_Builder::build_lesson_plan_prompt( $class, $subject, $topic, $duration );

		$response = NCTB_AI_Adapter::generate(
			array(
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
			array(
				'model'       => 'gemini-1.5-flash',
				'temperature' => 0.4,
				'max_tokens'  => 1500,
			)
		);

		if ( ! $response['success'] ) {
			return new WP_Error( 'ai_error', $response['error'] ?? 'AI generation failed', array( 'status' => 500 ) );
		}

		NCTB_AI_Usage::record_usage( $user_id, 0, $response['prompt_tokens'], $response['completion_tokens'], $response['model'] );
		self::increment_trial( $user_id, $ent );

		return rest_ensure_response(
			array(
				'success' => true,
				'plan'    => $response['reply'],
				'usage'   => NCTB_AI_Usage::get_daily_usage( $user_id ),
			)
		);
	}

	/**
	 * Generate Classroom Quiz & Test Paper.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function generate_quiz( $request ) {
		$user_id = get_current_user_id();

		$ent = NCTB_Entitlements::can_access_ai( $user_id );
		if ( ! $ent['granted'] ) {
			return new WP_Error(
				'ai_paywall_required',
				__( 'Active AI Pass or Subscription required to use Teacher Quiz Generator.', 'nctb-learning-hub' ),
				array( 'status' => 403, 'upgrade_url' => home_url( '/pricing' ) )
			);
		}

		$quota = NCTB_AI_Usage::check_quota( $user_id );
		if ( ! $quota['allowed'] ) {
			return new WP_Error( 'quota_exceeded', __( 'Daily AI quota reached.', 'nctb-learning-hub' ), array( 'status' => 429 ) );
		}

		$params     = $request->get_json_params();
		$class      = sanitize_text_field( $params['class'] ?? 'Class 10 (SSC)' );
		$subject    = sanitize_text_field( $params['subject'] ?? 'English 1st Paper' );
		$topic      = sanitize_text_field( $params['topic'] ?? 'Completing Sentences' );
		$count      = min( 15, max( 3, absint( $params['count'] ?? 5 ) ) );
		$difficulty = sanitize_key( $params['difficulty'] ?? 'medium' );

		$prompt = NCTB_AI_Context_Builder::build_quiz_maker_prompt( $class, $subject, $topic, $count, $difficulty );

		$response = NCTB_AI_Adapter::generate(
			array(
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
			array(
				'model'       => 'gemini-1.5-flash',
				'temperature' => 0.5,
				'max_tokens'  => 1800,
			)
		);

		if ( ! $response['success'] ) {
			return new WP_Error( 'ai_error', $response['error'] ?? 'Quiz generation failed', array( 'status' => 500 ) );
		}

		NCTB_AI_Usage::record_usage( $user_id, 0, $response['prompt_tokens'], $response['completion_tokens'], $response['model'] );
		self::increment_trial( $user_id, $ent );

		return rest_ensure_response(
			array(
				'success' => true,
				'quiz'    => $response['reply'],
				'usage'   => NCTB_AI_Usage::get_daily_usage( $user_id ),
			)
		);
	}

	/**
	 * Generate Misconceptions & Remedial Guide.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function diagnose_misconceptions( $request ) {
		$user_id = get_current_user_id();

		$ent = NCTB_Entitlements::can_access_ai( $user_id );
		if ( ! $ent['granted'] ) {
			return new WP_Error(
				'ai_paywall_required',
				__( 'Active AI Pass or Subscription required.', 'nctb-learning-hub' ),
				array( 'status' => 403, 'upgrade_url' => home_url( '/pricing' ) )
			);
		}

		$quota = NCTB_AI_Usage::check_quota( $user_id );
		if ( ! $quota['allowed'] ) {
			return new WP_Error( 'quota_exceeded', __( 'Daily AI quota reached.', 'nctb-learning-hub' ), array( 'status' => 429 ) );
		}

		$params  = $request->get_json_params();
		$class   = sanitize_text_field( $params['class'] ?? 'HSC 2nd Year' );
		$subject = sanitize_text_field( $params['subject'] ?? 'English 2nd Paper' );
		$topic   = sanitize_text_field( $params['topic'] ?? 'Modifiers' );

		$prompt = NCTB_AI_Context_Builder::build_misconception_prompt( $class, $subject, $topic );

		$response = NCTB_AI_Adapter::generate(
			array(
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
			array(
				'model'       => 'gemini-1.5-flash',
				'temperature' => 0.4,
				'max_tokens'  => 1500,
			)
		);

		if ( ! $response['success'] ) {
			return new WP_Error( 'ai_error', $response['error'] ?? 'Diagnostics failed', array( 'status' => 500 ) );
		}

		NCTB_AI_Usage::record_usage( $user_id, 0, $response['prompt_tokens'], $response['completion_tokens'], $response['model'] );
		self::increment_trial( $user_id, $ent );

		return rest_ensure_response(
			array(
				'success' => true,
				'guide'   => $response['reply'],
				'usage'   => NCTB_AI_Usage::get_daily_usage( $user_id ),
			)
		);
	}

	/**
	 * Get daily AI quota status for current teacher.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_ai_quota( $request ) {
		$user_id = get_current_user_id();
		$ent     = NCTB_Entitlements::can_access_ai( $user_id );
		$quota   = NCTB_AI_Usage::check_quota( $user_id );

		return rest_ensure_response(
			array(
				'entitled'   => $ent['granted'],
				'reason'     => $ent['reason'],
				'expires_at' => $ent['expires_at'] ?? null,
				'trial_left' => $ent['trial_left'] ?? null,
				'usage'      => $quota,
			)
		);
	}

	/**
	 * Increment trial count if user is on free trial.
	 *
	 * @param int                  $user_id User ID.
	 * @param array<string,mixed> $ent     Entitlement decision.
	 * @return void
	 */
	private static function increment_trial( $user_id, $ent ) {
		if ( 'free_trial' === ( $ent['reason'] ?? '' ) ) {
			$cur = (int) get_user_meta( $user_id, '_nctb_ai_trial_count', true );
			update_user_meta( $user_id, '_nctb_ai_trial_count', $cur + 1 );
		}
	}
}
