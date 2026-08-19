<?php
/**
 * REST API controller for Teacher / Educator Hub (Phase 16).
 *
 * Exposes secure endpoints for teacher onboarding, profile management,
 * and teacher dashboard data.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Teacher_REST
 */
class NCTB_Teacher_REST extends WP_REST_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'nctb/v1/teacher';

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// GET /nctb/v1/teacher/options
		register_rest_route(
			$this->namespace,
			'/options',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_options' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		// GET /nctb/v1/teacher/profile
		register_rest_route(
			$this->namespace,
			'/profile',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_profile' ),
					'permission_callback' => array( $this, 'check_authenticated_permission' ),
				),
			)
		);

		// POST /nctb/v1/teacher/onboarding/step
		register_rest_route(
			$this->namespace,
			'/onboarding/step',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_step' ),
					'permission_callback' => array( $this, 'check_authenticated_permission' ),
					'args'                => array(
						'step' => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		// POST /nctb/v1/teacher/onboarding/complete
		register_rest_route(
			$this->namespace,
			'/onboarding/complete',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'complete_onboarding' ),
					'permission_callback' => array( $this, 'check_authenticated_permission' ),
				),
			)
		);

		// GET /nctb/v1/teacher/dashboard
		register_rest_route(
			$this->namespace,
			'/dashboard',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_dashboard' ),
					'permission_callback' => array( $this, 'check_authenticated_permission' ),
				),
			)
		);
	}

	/**
	 * Permission check: ensure user is logged in.
	 *
	 * @return bool|WP_Error
	 */
	public function check_authenticated_permission() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to access the Teacher Hub.', 'nctb-learning-hub' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	/**
	 * Get teacher onboarding options catalog.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_options( $request ) {
		return rest_ensure_response(
			array(
				'divisions' => NCTB_Teacher_Profile::$allowed_divisions,
				'classes'   => NCTB_Teacher_Profile::$allowed_classes,
				'subjects'  => NCTB_Teacher_Profile::$allowed_subjects,
				'goals'     => NCTB_Teacher_Profile::$allowed_goals,
			)
		);
	}

	/**
	 * Get current teacher's profile.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_profile( $request ) {
		$user_id = get_current_user_id();
		$profile = NCTB_Teacher_Profile::get_profile( $user_id );
		return rest_ensure_response( $profile );
	}

	/**
	 * Save teacher onboarding step.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_step( $request ) {
		$user_id = get_current_user_id();
		$step    = absint( $request['step'] );
		$params  = $request->get_json_params() ?: $request->get_body_params();

		$profile = NCTB_Teacher_Profile::save_step( $user_id, $step, $params );

		return rest_ensure_response(
			array(
				'success'      => true,
				'current_step' => $step,
				'next_step'    => min( 3, $step + 1 ),
				'profile'      => $profile,
			)
		);
	}

	/**
	 * Complete teacher onboarding.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function complete_onboarding( $request ) {
		$user_id = get_current_user_id();
		$profile = NCTB_Teacher_Profile::complete_onboarding( $user_id );

		return rest_ensure_response(
			array(
				'success'      => true,
				'redirect_url' => home_url( '/teacher-dashboard/' ),
				'profile'      => $profile,
			)
		);
	}

	/**
	 * Get aggregated teacher dashboard data.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_dashboard( $request ) {
		$user_id = get_current_user_id();
		$profile = NCTB_Teacher_Profile::get_profile( $user_id );

		// Quick pedagogical resources
		$resources = array(
			array(
				'title'       => 'HSC English 1st Paper Lesson Plans',
				'subject'     => 'English 1st Paper',
				'class'       => 'HSC',
				'type'        => 'Lesson Plan Guide',
				'icon'        => '📝',
				'download_url'=> home_url( '/books/' ),
			),
			array(
				'title'       => 'Nelson Mandela — Classroom Discussion & Activities',
				'subject'     => 'English 1st Paper',
				'class'       => 'HSC (Unit 1, Lesson 1)',
				'type'        => 'Pedagogy Guide',
				'icon'        => '💡',
				'download_url'=> home_url( '/books/' ),
			),
			array(
				'title'       => 'Class 9–10 ICT Practical Setup & Code Guide',
				'subject'     => 'ICT',
				'class'       => 'SSC (Class 9-10)',
				'type'        => 'ICT Lab Guide',
				'icon'        => '💻',
				'download_url'=> home_url( '/subjects/' ),
			),
		);

		return rest_ensure_response(
			array(
				'teacher'   => $profile,
				'resources' => $resources,
				'quick_tools' => array(
					array( 'id' => 'lesson_planner', 'title' => 'পাঠ পরিকল্পনা জেনারেটর (AI Lesson Planner)', 'icon' => '🤖' ),
					array( 'id' => 'question_maker', 'title' => 'ক্লাসরুম মূল্যায়ন প্রশ্নপত্র তৈরি (Quiz Creator)', 'icon' => '⚡' ),
					array( 'id' => 'misconceptions', 'title' => 'শিক্ষার্থীদের সাধারণ ভুল ও সমাধান গাইড', 'icon' => '🔍' ),
					array( 'id' => 'curriculum_map', 'title' => 'NCTB পূর্ণাঙ্গ সিলেবাস ও শিখনফল ম্যাপিং', 'icon' => '📚' ),
				),
			)
		);
	}
}
