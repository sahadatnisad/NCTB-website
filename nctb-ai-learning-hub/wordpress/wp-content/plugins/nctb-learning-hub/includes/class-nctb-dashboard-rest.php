<?php
/**
 * REST API controller for Student Home Study Guide Dashboard (Phase 7).
 *
 * Exposes aggregated dashboard data (continue learning, today's practice,
 * revision due, mistake alerts, book progress) with per-student data isolation.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Dashboard_REST
 */
class NCTB_Dashboard_REST extends WP_REST_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'nctb/v1';

	/**
	 * Register dashboard routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// GET /nctb/v1/student/dashboard
		register_rest_route(
			$this->namespace,
			'/student/dashboard',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_dashboard_summary' ),
					'permission_callback' => array( $this, 'check_auth_permission' ),
				),
			)
		);
	}

	/**
	 * Permission check: ensure user is authenticated.
	 *
	 * @return bool|WP_Error
	 */
	public function check_auth_permission() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to view your dashboard.', 'nctb-learning-hub' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	/**
	 * Get aggregated dashboard data.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_dashboard_summary( $request ) {
		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Authentication required.', 'nctb-learning-hub' ), array( 'status' => 401 ) );
		}
		$data = NCTB_Dashboard_Service::get_dashboard_data( $user_id );

		return rest_ensure_response(
			array(
				'success' => true,
				'data'    => $data,
			)
		);
	}
}
