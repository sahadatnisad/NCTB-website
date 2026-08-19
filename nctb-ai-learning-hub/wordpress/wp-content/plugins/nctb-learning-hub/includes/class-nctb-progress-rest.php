<?php
/**
 * REST API controller for Progress, Mastery, Mistakes, and Spaced Revision (Phase 6).
 *
 * Exposes endpoints for tracking lesson step progress, retrieving concept
 * mastery breakdowns, managing the mistake notebook, and servicing the
 * spaced revision queue with strict per-student isolation.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Progress_REST
 */
class NCTB_Progress_REST extends WP_REST_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'nctb/v1';

	/**
	 * Register progress routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// POST /nctb/v1/progress/step
		register_rest_route(
			$this->namespace,
			'/progress/step',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_step_progress' ),
					'permission_callback' => array( $this, 'check_auth_permission' ),
					'args'                => array(
						'lesson_id'    => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
						'step_num'     => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
						'total_steps'  => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
						'is_completed' => array(
							'required'          => false,
							'default'           => false,
							'sanitize_callback' => 'rest_sanitize_boolean',
						),
					),
				),
			)
		);

		// GET /nctb/v1/progress/summary
		register_rest_route(
			$this->namespace,
			'/progress/summary',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_progress_summary' ),
					'permission_callback' => array( $this, 'check_auth_permission' ),
				),
			)
		);

		// GET /nctb/v1/mistakes
		register_rest_route(
			$this->namespace,
			'/mistakes',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_my_mistakes' ),
					'permission_callback' => array( $this, 'check_auth_permission' ),
				),
			)
		);

		// POST /nctb/v1/mistakes/resolve
		register_rest_route(
			$this->namespace,
			'/mistakes/resolve',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'resolve_mistake' ),
					'permission_callback' => array( $this, 'check_auth_permission' ),
					'args'                => array(
						'mistake_id' => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		// GET /nctb/v1/revision/due
		register_rest_route(
			$this->namespace,
			'/revision/due',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_revision_due' ),
					'permission_callback' => array( $this, 'check_auth_permission' ),
				),
			)
		);

		// POST /nctb/v1/revision/complete
		register_rest_route(
			$this->namespace,
			'/revision/complete',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'complete_revision' ),
					'permission_callback' => array( $this, 'check_auth_permission' ),
					'args'                => array(
						'review_id' => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
						'score'     => array(
							'required'          => false,
							'default'           => 1.0,
							'sanitize_callback' => 'floatval',
						),
					),
				),
			)
		);
	}

	/**
	 * Check authentication and student permissions.
	 *
	 * @return bool|WP_Error
	 */
	public function check_auth_permission() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to view or update student progress data.', 'nctb-learning-hub' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	/**
	 * Get the authenticated student User ID.
	 *
	 * @return int
	 */
	protected function get_student_id() {
		return get_current_user_id();
	}

	/**
	 * Save lesson step progression.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_step_progress( $request ) {
		$student_id = $this->get_student_id();
		if ( empty( $student_id ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Authentication required.', 'nctb-learning-hub' ), array( 'status' => 401 ) );
		}
		$lesson_id    = absint( $request['lesson_id'] );
		$step_num     = absint( $request['step_num'] );
		$total_steps  = absint( $request['total_steps'] );
		$is_completed = (bool) $request['is_completed'];

		$ok = NCTB_Progress_Service::record_step( $student_id, $lesson_id, $step_num, $total_steps, $is_completed );

		return rest_ensure_response(
			array(
				'success'      => ( true === $ok ),
				'lesson_id'    => $lesson_id,
				'step_num'     => $step_num,
				'is_completed' => $is_completed || ( $step_num >= $total_steps ),
			)
		);
	}

	/**
	 * Get student learning progress and mastery summary.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_progress_summary( $request ) {
		$student_id = $this->get_student_id();
		$summary    = NCTB_Progress_Service::get_user_summary( $student_id );
		$mastery    = NCTB_Mastery_Service::get_all_user_mastery( $student_id );

		return rest_ensure_response(
			array(
				'summary' => $summary,
				'mastery' => $mastery,
			)
		);
	}

	/**
	 * Get active mistakes for student notebook.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_my_mistakes( $request ) {
		$student_id = $this->get_student_id();
		$mistakes   = NCTB_Mistakes_Service::get_active_mistakes( $student_id, 50 );

		return rest_ensure_response(
			array(
				'count'    => count( $mistakes ),
				'mistakes' => $mistakes,
			)
		);
	}

	/**
	 * Manually resolve a mistake item.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function resolve_mistake( $request ) {
		$student_id = $this->get_student_id();
		$mistake_id = absint( $request['mistake_id'] );
		$ok         = NCTB_Mistakes_Service::resolve_mistake( $mistake_id, $student_id );

		return rest_ensure_response( array( 'success' => $ok ) );
	}

	/**
	 * Get spaced revision items due today.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_revision_due( $request ) {
		$student_id = $this->get_student_id();
		$due_items  = NCTB_Spaced_Revision_Service::get_due_reviews( $student_id );

		return rest_ensure_response(
			array(
				'count'    => count( $due_items ),
				'due_date' => current_time( 'Y-m-d' ),
				'items'    => $due_items,
			)
		);
	}

	/**
	 * Complete a spaced review item.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function complete_revision( $request ) {
		$student_id = $this->get_student_id();
		$review_id  = absint( $request['review_id'] );
		$score      = floatval( $request['score'] );

		$ok = NCTB_Spaced_Revision_Service::complete_review( $review_id, $student_id, $score );

		return rest_ensure_response( array( 'success' => $ok ) );
	}
}
