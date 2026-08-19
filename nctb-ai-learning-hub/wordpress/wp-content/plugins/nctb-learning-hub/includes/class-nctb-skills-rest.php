<?php
/**
 * REST API controller for Writing, Listening & Speaking (Phase 10).
 *
 * Exposes endpoints for saving multi-stage writing drafts, requesting feedback,
 * submitting final writing, and managing speaking recordings.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Skills_REST
 */
class NCTB_Skills_REST extends WP_REST_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'nctb/v1/skills';

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// POST /nctb/v1/skills/writing/draft
		register_rest_route(
			$this->namespace,
			'/writing/draft',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_writing_draft' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'lesson_id'   => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'activity_id' => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'stage'       => array( 'required' => false, 'default' => 'draft', 'sanitize_callback' => 'sanitize_key' ),
						'draft_text'  => array( 'required' => true, 'sanitize_callback' => 'wp_kses_post' ),
					),
				),
			)
		);

		// POST /nctb/v1/skills/writing/feedback
		register_rest_route(
			$this->namespace,
			'/writing/feedback',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'request_writing_feedback' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'lesson_id'   => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'activity_id' => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'draft_text'  => array( 'required' => true, 'sanitize_callback' => 'wp_kses_post' ),
					),
				),
			)
		);

		// POST /nctb/v1/skills/writing/final
		register_rest_route(
			$this->namespace,
			'/writing/final',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'submit_writing_final' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'lesson_id'   => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'activity_id' => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'final_text'  => array( 'required' => true, 'sanitize_callback' => 'wp_kses_post' ),
					),
				),
			)
		);

		// GET /nctb/v1/skills/writing/submission
		register_rest_route(
			$this->namespace,
			'/writing/submission',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_writing_submission' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'lesson_id'   => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'activity_id' => array( 'required' => true, 'sanitize_callback' => 'absint' ),
					),
				),
			)
		);

		// POST /nctb/v1/skills/speaking/submit
		register_rest_route(
			$this->namespace,
			'/speaking/submit',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'submit_speaking' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'lesson_id'        => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'activity_id'      => array( 'required' => true, 'sanitize_callback' => 'absint' ),
						'audio_url'        => array( 'required' => false, 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
						'duration_seconds' => array( 'required' => false, 'default' => 0, 'sanitize_callback' => 'absint' ),
						'transcript_text'  => array( 'required' => false, 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ),
					),
				),
			)
		);
	}

	/**
	 * Save writing draft.
	 */
	public function save_writing_draft( $request ) {
		$user_id     = get_current_user_id() ?: 1;
		$lesson_id   = absint( $request['lesson_id'] );
		$activity_id = absint( $request['activity_id'] );
		$stage       = sanitize_key( $request['stage'] );
		$draft_text  = (string) $request['draft_text'];

		$sub_id = NCTB_Writing_Service::save_draft( $user_id, $lesson_id, $activity_id, $stage, $draft_text );

		return rest_ensure_response(
			array(
				'success'       => ( $sub_id > 0 ),
				'submission_id' => $sub_id,
				'stage'         => $stage,
			)
		);
	}

	/**
	 * Request feedback.
	 */
	public function request_writing_feedback( $request ) {
		$user_id     = get_current_user_id() ?: 1;
		$lesson_id   = absint( $request['lesson_id'] );
		$activity_id = absint( $request['activity_id'] );
		$draft_text  = (string) $request['draft_text'];

		$feedback = NCTB_Writing_Service::generate_feedback( $user_id, $lesson_id, $activity_id, $draft_text );

		return rest_ensure_response( $feedback );
	}

	/**
	 * Submit final writing.
	 */
	public function submit_writing_final( $request ) {
		$user_id     = get_current_user_id() ?: 1;
		$lesson_id   = absint( $request['lesson_id'] );
		$activity_id = absint( $request['activity_id'] );
		$final_text  = (string) $request['final_text'];

		$ok = NCTB_Writing_Service::submit_final( $user_id, $lesson_id, $activity_id, $final_text );

		return rest_ensure_response( array( 'success' => $ok, 'status' => 'completed' ) );
	}

	/**
	 * Get writing submission.
	 */
	public function get_writing_submission( $request ) {
		$user_id     = get_current_user_id() ?: 1;
		$lesson_id   = absint( $request['lesson_id'] );
		$activity_id = absint( $request['activity_id'] );

		$sub = NCTB_Writing_Service::get_submission( $user_id, $lesson_id, $activity_id );

		return rest_ensure_response(
			array(
				'exists'     => ! empty( $sub ),
				'submission' => $sub,
			)
		);
	}

	/**
	 * Submit speaking recording.
	 */
	public function submit_speaking( $request ) {
		$user_id          = get_current_user_id() ?: 1;
		$lesson_id        = absint( $request['lesson_id'] );
		$activity_id      = absint( $request['activity_id'] );
		$audio_url        = (string) $request['audio_url'];
		$duration_seconds = absint( $request['duration_seconds'] );
		$transcript_text  = (string) $request['transcript_text'];

		$result = NCTB_Speaking_Service::submit_recording( $user_id, $lesson_id, $activity_id, $audio_url, $duration_seconds, $transcript_text );

		return rest_ensure_response( $result );
	}
}
