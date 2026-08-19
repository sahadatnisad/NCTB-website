<?php
/**
 * REST API controller for Notes & Explanations (Phase 18).
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Notes_REST
 */
class NCTB_Notes_REST extends WP_REST_Controller {

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
		// GET /nctb/v1/notes
		register_rest_route(
			$this->namespace,
			'/notes',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_notes' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		// GET /nctb/v1/notes/{id}
		register_rest_route(
			$this->namespace,
			'/notes/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_note' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * List notes.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_notes( $request ) {
		$class    = $request->get_param( 'class' );
		$subject  = $request->get_param( 'subject' );
		$audience = $request->get_param( 'audience' );

		$args = array(
			'post_type'      => NCTB_Note_CPT::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$meta_query = array();
		if ( ! empty( $class ) ) {
			$meta_query[] = array(
				'key'     => NCTB_Note_CPT::META_CLASS,
				'value'   => array( sanitize_key( $class ), 'all' ),
				'compare' => 'IN',
			);
		}
		if ( ! empty( $audience ) ) {
			$meta_query[] = array(
				'key'     => NCTB_Note_CPT::META_AUDIENCE,
				'value'   => array( sanitize_key( $audience ), 'both' ),
				'compare' => 'IN',
			);
		}
		if ( ! empty( $meta_query ) ) {
			$meta_query['relation'] = 'AND';
			$args['meta_query']     = $meta_query;
		}

		$query = new WP_Query( $args );
		$notes = array();

		foreach ( $query->posts as $post ) {
			$n = NCTB_Notes_Service::get_note( $post->ID );
			if ( $n ) {
				$notes[] = $n;
			}
		}

		return rest_ensure_response( array( 'notes' => $notes ) );
	}

	/**
	 * Get note detail.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_note( $request ) {
		$note_id = absint( $request['id'] );
		$note    = NCTB_Notes_Service::get_note( $note_id );

		if ( ! $note ) {
			return new WP_Error( 'not_found', __( 'Note not found', 'nctb-learning-hub' ), array( 'status' => 404 ) );
		}

		return rest_ensure_response( $note );
	}
}
