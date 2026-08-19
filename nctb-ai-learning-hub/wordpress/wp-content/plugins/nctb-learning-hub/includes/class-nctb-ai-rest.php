<?php
/**
 * REST API controller for Contextual AI Tutor (Phase 9).
 *
 * Exposes secure endpoints for querying the lesson-anchored AI tutor,
 * retrieving conversation history, and checking daily token quota.
 * API keys are never returned or handled on the client.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_AI_REST
 */
class NCTB_AI_REST extends WP_REST_Controller {

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
		// POST /nctb/v1/tutor/ask
		register_rest_route(
			$this->namespace,
			'/tutor/ask',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'handle_ask' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'lesson_id'   => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
						'action_type' => array(
							'required'          => false,
							'default'           => 'explain',
							'sanitize_callback' => 'sanitize_key',
						),
						'prompt'      => array(
							'required'          => false,
							'default'           => '',
							'sanitize_callback' => 'sanitize_textarea_field',
						),
						'question_id' => array(
							'required'          => false,
							'default'           => 0,
							'sanitize_callback' => 'absint',
						),
						'step_num'    => array(
							'required'          => false,
							'default'           => 1,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		// GET /nctb/v1/tutor/history
		register_rest_route(
			$this->namespace,
			'/tutor/history',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_history' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'lesson_id' => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		// GET /nctb/v1/tutor/quota
		register_rest_route(
			$this->namespace,
			'/tutor/quota',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_quota' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * Handle asking the AI Tutor.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function handle_ask( $request ) {
		$user_id     = get_current_user_id() ?: 1;
		$lesson_id   = absint( $request['lesson_id'] );
		$action_type = sanitize_key( $request['action_type'] );
		$prompt      = (string) $request['prompt'];
		$question_id = absint( $request['question_id'] );
		$step_num    = absint( $request['step_num'] );

		$response = NCTB_AI_Tutor::ask( $user_id, $lesson_id, $action_type, $prompt, $question_id, $step_num );

		return rest_ensure_response( $response );
	}

	/**
	 * Get conversation history for this lesson.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_history( $request ) {
		$user_id   = get_current_user_id() ?: 1;
		$lesson_id = absint( $request['lesson_id'] );
		$history   = NCTB_AI_Usage::get_recent_history( $user_id, $lesson_id );

		return rest_ensure_response(
			array(
				'count'   => count( $history ),
				'history' => $history,
			)
		);
	}

	/**
	 * Get current daily quota status.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_quota( $request ) {
		$user_id = get_current_user_id() ?: 1;
		$quota   = NCTB_AI_Usage::check_daily_quota( $user_id );

		return rest_ensure_response( $quota );
	}
}
