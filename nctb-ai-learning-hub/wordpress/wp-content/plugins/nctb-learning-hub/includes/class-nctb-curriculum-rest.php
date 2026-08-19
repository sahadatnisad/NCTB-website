<?php
/**
 * Read-only REST controller for the curriculum browse tree.
 *
 * Exposes the Class → Subject → Book → Unit → Lesson hierarchy so the
 * front-end (and later phases) can render navigation without hard-coding
 * curriculum. Published content only; no student-private data here.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Curriculum_REST
 */
class NCTB_Curriculum_REST extends WP_REST_Controller {

	/**
	 * REST namespace (shared with the rest of the plugin).
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
		// GET /nctb/v1/curriculum/books
		register_rest_route(
			$this->namespace,
			'/curriculum/books',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_books' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		// GET /nctb/v1/curriculum/book/{id}
		register_rest_route(
			$this->namespace,
			'/curriculum/book/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_book_tree' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'id' => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		// GET /nctb/v1/curriculum/lesson/{id}
		register_rest_route(
			$this->namespace,
			'/curriculum/lesson/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_lesson' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'id' => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);
	}

	/**
	 * List published books.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_books( $request ) {
		$out = array();
		foreach ( NCTB_Curriculum_CPT::get_books() as $book ) {
			$out[] = $this->book_summary( $book );
		}
		return rest_ensure_response( $out );
	}

	/**
	 * Return a book with its nested units and lessons.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_book_tree( $request ) {
		$book_id = absint( $request['id'] );
		$book    = get_post( $book_id );

		if ( ! $book || NCTB_Curriculum_CPT::CPT_BOOK !== $book->post_type || 'publish' !== $book->post_status ) {
			return new WP_Error( 'nctb_not_found', __( 'Book not found.', 'nctb-learning-hub' ), array( 'status' => 404 ) );
		}

		$data          = $this->book_summary( $book );
		$data['units'] = array();

		foreach ( NCTB_Curriculum_CPT::get_units( $book_id ) as $unit ) {
			$unit_data = array(
				'id'      => $unit->ID,
				'title'   => $unit->post_title,
				'link'    => get_permalink( $unit ),
				'lessons' => array(),
			);
			foreach ( NCTB_Curriculum_CPT::get_lessons( $unit->ID ) as $lesson ) {
				$unit_data['lessons'][] = array(
					'id'    => $lesson->ID,
					'title' => $lesson->post_title,
					'link'  => get_permalink( $lesson ),
				);
			}
			$data['units'][] = $unit_data;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Return a single lesson with outcomes and concepts.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_lesson( $request ) {
		$lesson_id = absint( $request['id'] );
		$lesson    = get_post( $lesson_id );

		if ( ! $lesson || NCTB_Curriculum_CPT::CPT_LESSON !== $lesson->post_type || 'publish' !== $lesson->post_status ) {
			return new WP_Error( 'nctb_not_found', __( 'Lesson not found.', 'nctb-learning-hub' ), array( 'status' => 404 ) );
		}

		$outcomes = array();
		foreach ( NCTB_Curriculum_Data::get_lesson_outcomes( $lesson_id ) as $row ) {
			$outcomes[] = $row->outcome_text;
		}

		$concepts = array();
		foreach ( NCTB_Curriculum_Data::get_lesson_concepts( $lesson_id ) as $concept ) {
			$concepts[] = array(
				'id'   => (int) $concept->id,
				'name' => $concept->name,
			);
		}

		$unit_id = NCTB_Curriculum_CPT::get_lesson_unit( $lesson_id );
		$book_id = $unit_id ? NCTB_Curriculum_CPT::get_unit_book( $unit_id ) : 0;

		return rest_ensure_response(
			array(
				'id'                => $lesson->ID,
				'title'             => $lesson->post_title,
				'content'           => apply_filters( 'the_content', $lesson->post_content ),
				'link'              => get_permalink( $lesson ),
				'unit'              => $unit_id ? array( 'id' => $unit_id, 'title' => get_the_title( $unit_id ) ) : null,
				'book'              => $book_id ? array( 'id' => $book_id, 'title' => get_the_title( $book_id ) ) : null,
				'learning_outcomes' => $outcomes,
				'concepts'          => $concepts,
			)
		);
	}

	/**
	 * Build a compact book summary with its taxonomy terms.
	 *
	 * @param WP_Post $book Book post.
	 * @return array
	 */
	protected function book_summary( $book ) {
		return array(
			'id'      => $book->ID,
			'title'   => $book->post_title,
			'link'    => get_permalink( $book ),
			'classes' => wp_get_post_terms( $book->ID, 'nctb_class_level', array( 'fields' => 'names' ) ),
			'subjects' => wp_get_post_terms( $book->ID, 'nctb_subject', array( 'fields' => 'names' ) ),
		);
	}
}
