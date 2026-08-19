<?php
/**
 * Teacher Resources REST API Controller (Phase 24).
 *
 * Exposes endpoints for teachers to fetch and search downloadable lesson plans,
 * classroom worksheets, slide outlines, and assessment rubrics.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Teacher_Resources_REST
 */
class NCTB_Teacher_Resources_REST {

	const NAMESPACE = 'nctb/v1';

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/teacher/resources',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_resources' ),
				'permission_callback' => array( __CLASS__, 'check_permission' ),
				'args'                => array(
					'subject' => array( 'type' => 'string' ),
					'class'   => array( 'type' => 'string' ),
					'type'    => array( 'type' => 'string' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/teacher/resources/(?P<id>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_resource' ),
				'permission_callback' => array( __CLASS__, 'check_permission' ),
			)
		);
	}

	/**
	 * Permissions: Logged in or public read.
	 *
	 * @return bool
	 */
	public static function check_permission() {
		return true;
	}

	/**
	 * Get resources.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function get_resources( $request ) {
		$subject = sanitize_text_field( (string) $request->get_param( 'subject' ) );
		$class   = sanitize_text_field( (string) $request->get_param( 'class' ) );
		$type    = sanitize_text_field( (string) $request->get_param( 'type' ) );

		$args = array();
		if ( ! empty( $subject ) ) {
			$args['subject'] = $subject;
		}
		if ( ! empty( $class ) ) {
			$args['class'] = $class;
		}
		if ( ! empty( $type ) ) {
			$args['type'] = $type;
		}

		$data = NCTB_Teacher_Resources_Service::get_resources( $args );

		return rest_ensure_response(
			array(
				'success'   => true,
				'count'     => count( $data ),
				'resources' => $data,
			)
		);
	}

	/**
	 * Get single resource.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function get_resource( $request ) {
		$id = sanitize_text_field( (string) $request->get_param( 'id' ) );
		$r  = NCTB_Teacher_Resources_Service::get_resource( $id );

		if ( ! $r ) {
			return new WP_Error( 'not_found', __( 'Resource not found.', 'nctb-learning-hub' ), array( 'status' => 404 ) );
		}

		return rest_ensure_response(
			array(
				'success'  => true,
				'resource' => $r,
			)
		);
	}
}
