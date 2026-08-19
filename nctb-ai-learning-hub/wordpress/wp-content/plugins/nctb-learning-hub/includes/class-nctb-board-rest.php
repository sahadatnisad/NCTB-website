<?php
/**
 * REST API controller for Authentic Board Questions (Phase 11).
 *
 * Exposes endpoints for querying verified board exam questions,
 * retrieving lesson-specific authentic exam items, and filter options.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Board_REST
 */
class NCTB_Board_REST extends WP_REST_Controller {

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
		// GET /nctb/v1/board/questions
		register_rest_route(
			$this->namespace,
			'/board/questions',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_questions' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'level' => array( 'required' => false, 'sanitize_callback' => 'sanitize_key' ),
						'board' => array( 'required' => false, 'sanitize_callback' => 'sanitize_key' ),
						'year'  => array( 'required' => false, 'sanitize_callback' => 'absint' ),
						'topic' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					),
				),
			)
		);

		// GET /nctb/v1/board/lesson/{lesson_id}
		register_rest_route(
			$this->namespace,
			'/board/lesson/(?P<lesson_id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_lesson_questions' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'lesson_id' => array( 'required' => true, 'sanitize_callback' => 'absint' ),
					),
				),
			)
		);

		// GET /nctb/v1/board/analytics
		register_rest_route(
			$this->namespace,
			'/board/analytics',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_analytics' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'level' => array( 'required' => false, 'default' => 'hsc', 'sanitize_callback' => 'sanitize_key' ),
					),
				),
			)
		);
	}

	/**
	 * Get filtered board questions.
	 */
	public function get_questions( $request ) {
		$filters = array(
			'exam_level' => $request['level'] ?? '',
			'board_name' => $request['board'] ?? '',
			'exam_year'  => $request['year'] ?? 0,
			'topic'      => $request['topic'] ?? '',
		);

		$questions = NCTB_Board_Service::get_board_questions( array_filter( $filters ) );

		return rest_ensure_response(
			array(
				'count'     => count( $questions ),
				'questions' => $questions,
			)
		);
	}

	/**
	 * Get board questions for lesson.
	 */
	public function get_lesson_questions( $request ) {
		$lesson_id = absint( $request['lesson_id'] );
		$questions = NCTB_Board_Service::get_lesson_board_questions( $lesson_id );

		return rest_ensure_response(
			array(
				'lesson_id' => $lesson_id,
				'count'     => count( $questions ),
				'questions' => $questions,
			)
		);
	}

	/**
	 * Get aggregated board pattern analytics.
	 */
	public function get_analytics( $request ) {
		$level = sanitize_key( $request['level'] ?? 'hsc' );
		$report = NCTB_Board_Analytics_Service::get_full_analytics_report( $level );

		return rest_ensure_response( $report );
	}
}
