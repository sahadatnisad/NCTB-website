<?php
/**
 * REST API controller for Student Onboarding & Profile.
 *
 * Exposes secure REST endpoints with strict permission callbacks and nonces.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Onboarding_REST
 */
class NCTB_Onboarding_REST extends WP_REST_Controller {

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
		// GET /wp-json/nctb/v1/meta/options
		register_rest_route(
			$this->namespace,
			'/meta/options',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_options' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		// GET /wp-json/nctb/v1/student/profile
		register_rest_route(
			$this->namespace,
			'/student/profile',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_profile' ),
					'permission_callback' => array( $this, 'check_authenticated_permission' ),
				),
			)
		);

		// POST /wp-json/nctb/v1/student/onboarding/step
		register_rest_route(
			$this->namespace,
			'/student/onboarding/step',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_step' ),
					'permission_callback' => array( $this, 'check_authenticated_permission' ),
					'args'                => array(
						'step' => array(
							'required'          => true,
							'type'              => 'integer',
							'sanitize_callback' => 'absint',
							'validate_callback' => function( $param ) {
								return is_numeric( $param ) && $param >= 1 && $param <= 4;
							},
						),
					),
				),
			)
		);

		// POST /wp-json/nctb/v1/student/onboarding/complete
		register_rest_route(
			$this->namespace,
			'/student/onboarding/complete',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'complete_onboarding' ),
					'permission_callback' => array( $this, 'check_authenticated_permission' ),
				),
			)
		);
	}

	/**
	 * Permission callback: ensure user is logged in.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function check_authenticated_permission( $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to access student profile data.', 'nctb-learning-hub' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	/**
	 * Get options catalog (levels, languages, subjects).
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_options( $request ) {
		return rest_ensure_response(
			array(
				'levels'    => NCTB_Student_Profile::ALLOWED_LEVELS,
				'languages' => NCTB_Student_Profile::ALLOWED_LANGUAGES,
				'subjects'  => NCTB_Student_Profile::ALLOWED_SUBJECTS,
			)
		);
	}

	/**
	 * Get authenticated student profile.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_profile( $request ) {
		$user_id = get_current_user_id();
		$profile = NCTB_Student_Profile::get_profile( $user_id );
		return rest_ensure_response( $profile );
	}

	/**
	 * Save onboarding step for authenticated student.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_step( $request ) {
		$user_id = get_current_user_id();
		$step    = absint( $request->get_param( 'step' ) );
		$params  = $request->get_json_params();

		if ( empty( $params ) ) {
			$params = $request->get_body_params();
		}

		$result = NCTB_Student_Profile::save_step( $user_id, $step, $params );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return rest_ensure_response(
			array(
				'success'      => true,
				'current_step' => $step,
				'next_step'    => min( 4, $step + 1 ),
				'profile'      => $result,
			)
		);
	}

	/**
	 * Mark onboarding complete.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function complete_onboarding( $request ) {
		$user_id = get_current_user_id();
		$profile = NCTB_Student_Profile::complete_onboarding( $user_id );

		return rest_ensure_response(
			array(
				'success'      => true,
				'redirect_url' => home_url( '/dashboard' ),
				'profile'      => $profile,
			)
		);
	}
}
