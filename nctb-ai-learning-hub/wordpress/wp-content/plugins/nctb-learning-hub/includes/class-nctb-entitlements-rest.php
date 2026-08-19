<?php
/**
 * REST API controller for Entitlements & Access (Phase 8).
 *
 * Exposes endpoints for checking lesson access permissions, retrieving
 * active student purchases, and handling purchase interactions.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Entitlements_REST
 */
class NCTB_Entitlements_REST extends WP_REST_Controller {

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
		// GET /nctb/v1/entitlements/check
		register_rest_route(
			$this->namespace,
			'/entitlements/check',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'check_lesson_access' ),
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

		// GET /nctb/v1/student/purchases
		register_rest_route(
			$this->namespace,
			'/student/purchases',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_student_purchases' ),
					'permission_callback' => array( $this, 'check_authenticated_permission' ),
				),
			)
		);

		// POST /nctb/v1/entitlements/purchase-demo (Sandbox dev purchase grant)
		register_rest_route(
			$this->namespace,
			'/entitlements/purchase-demo',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'demo_purchase_pass' ),
					'permission_callback' => array( $this, 'check_demo_permission' ),
					'args'                => array(
						'type'    => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_key',
						),
						'item_id' => array(
							'required'          => false,
							'default'           => 0,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);
	}

	/**
	 * Permission check: ensure user is authenticated.
	 *
	 * @return bool|WP_Error
	 */
	public function check_authenticated_permission() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to view your purchases.', 'nctb-learning-hub' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	/**
	 * Permission check for demo sandbox purchase.
	 *
	 * @return bool|WP_Error
	 */
	public function check_demo_permission() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden', __( 'Authentication required.', 'nctb-learning-hub' ), array( 'status' => 401 ) );
		}
		if ( ! current_user_can( 'manage_options' ) && ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Sandbox purchase is disabled in production.', 'nctb-learning-hub' ), array( 'status' => 403 ) );
		}
		return true;
	}

	/**
	 * Check access decision for a lesson.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function check_lesson_access( $request ) {
		$lesson_id = absint( $request['lesson_id'] );
		$user_id   = get_current_user_id();

		$decision = NCTB_Entitlements::can_access_lesson( $user_id, $lesson_id );

		return rest_ensure_response(
			array(
				'lesson_id' => $lesson_id,
				'decision'  => $decision,
			)
		);
	}

	/**
	 * Get active purchases for student.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_student_purchases( $request ) {
		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Authentication required.', 'nctb-learning-hub' ), array( 'status' => 401 ) );
		}
		$entitlements = NCTB_Entitlements::get_user_entitlements( $user_id );

		return rest_ensure_response(
			array(
				'count'        => count( $entitlements ),
				'entitlements' => $entitlements,
			)
		);
	}

	/**
	 * Sandbox / Demo purchase pass trigger.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function demo_purchase_pass( $request ) {
		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Authentication required.', 'nctb-learning-hub' ), array( 'status' => 401 ) );
		}
		$type    = sanitize_key( $request['type'] );
		$item_id = absint( $request['item_id'] );

		$item_type   = ( 'subscription' === $type ) ? 'all_access' : ( $item_id ? 'lesson' : 'all_access' );
		$duration    = ( 'subscription' === $type ) ? 30 : 0;
		$expires_at  = $duration > 0 ? gmdate( 'Y-m-d H:i:s', strtotime( "+{$duration} days", current_time( 'timestamp', true ) ) ) : null;

		$ent_id = NCTB_Entitlements::grant_entitlement(
			$user_id,
			$type,
			$item_type,
			$item_id,
			'woocommerce',
			'SANDBOX-' . time(),
			$user_id,
			$expires_at,
			'Sandbox Instant Purchase'
		);

		return rest_ensure_response(
			array(
				'success'        => is_int( $ent_id ),
				'entitlement_id' => $ent_id,
				'item_type'      => $item_type,
				'expires_at'     => $expires_at,
			)
		);
	}
}
