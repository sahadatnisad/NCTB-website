<?php
/**
 * REST API controller for Video Modules & Courses (Phase 17).
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Module_REST
 */
class NCTB_Module_REST extends WP_REST_Controller {

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
		// GET /nctb/v1/modules
		register_rest_route(
			$this->namespace,
			'/modules',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_modules' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		// GET /nctb/v1/modules/{id}
		register_rest_route(
			$this->namespace,
			'/modules/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_module' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		// POST /nctb/v1/modules/{id}/toggle-item
		register_rest_route(
			$this->namespace,
			'/modules/(?P<id>\d+)/toggle-item',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'toggle_item' ),
					'permission_callback' => array( $this, 'check_auth_permission' ),
					'args'                => array(
						'item_id' => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_key',
						),
						'completed' => array(
							'default'           => true,
							'sanitize_callback' => 'rest_sanitize_boolean',
						),
					),
				),
			)
		);
	}

	/**
	 * Permission callback: requires user login.
	 *
	 * @return bool|WP_Error
	 */
	public function check_auth_permission() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden', __( 'Authentication required.', 'nctb-learning-hub' ), array( 'status' => 401 ) );
		}
		return true;
	}

	/**
	 * List modules.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_modules( $request ) {
		$user_id  = get_current_user_id();
		$audience = $request->get_param( 'audience' );
		$class    = $request->get_param( 'class' );

		$args = array(
			'post_type'      => NCTB_Module_CPT::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$meta_query = array();
		if ( ! empty( $audience ) ) {
			$meta_query[] = array(
				'key'     => NCTB_Module_CPT::META_AUDIENCE,
				'value'   => array( sanitize_key( $audience ), 'both' ),
				'compare' => 'IN',
			);
		}
		if ( ! empty( $class ) ) {
			$meta_query[] = array(
				'key'     => NCTB_Module_CPT::META_CLASS,
				'value'   => array( sanitize_key( $class ), 'all' ),
				'compare' => 'IN',
			);
		}
		if ( ! empty( $meta_query ) ) {
			$meta_query['relation'] = 'AND';
			$args['meta_query']     = $meta_query;
		}

		$query   = new WP_Query( $args );
		$modules = array();

		foreach ( $query->posts as $post ) {
			$mod = NCTB_Module_Service::get_module( $post->ID, $user_id );
			if ( $mod ) {
				$modules[] = $mod;
			}
		}

		return rest_ensure_response( array( 'modules' => $modules ) );
	}

	/**
	 * Get module detail.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_module( $request ) {
		$module_id = absint( $request['id'] );
		$user_id   = get_current_user_id();

		$module = NCTB_Module_Service::get_module( $module_id, $user_id );
		if ( ! $module ) {
			return new WP_Error( 'not_found', __( 'Module not found', 'nctb-learning-hub' ), array( 'status' => 404 ) );
		}

		return rest_ensure_response( $module );
	}

	/**
	 * Toggle item completion.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function toggle_item( $request ) {
		$module_id = absint( $request['id'] );
		$item_id   = sanitize_key( $request['item_id'] );
		$completed = (bool) $request['completed'];
		$user_id   = get_current_user_id();

		$result = NCTB_Module_Service::toggle_item( $user_id, $module_id, $item_id, $completed );
		return rest_ensure_response( $result );
	}
}
