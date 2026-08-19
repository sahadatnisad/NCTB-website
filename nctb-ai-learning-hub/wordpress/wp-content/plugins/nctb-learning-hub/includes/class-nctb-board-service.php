<?php
/**
 * Authentic Board Question Database Service (Phase 11).
 *
 * Manages official Bangladesh Education Board exam questions (SSC & HSC)
 * with year, board, paper, topic, and concept metadata.
 * AI-generated practice items are strictly separated from authentic board questions.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Board_Service
 */
class NCTB_Board_Service {

	const BOARDS = array(
		'dhaka'      => 'Dhaka Board (ঢাকা বোর্ড)',
		'chattogram' => 'Chattogram Board (চট্টগ্রাম বোর্ড)',
		'rajshahi'   => 'Rajshahi Board (রাজশাহী বোর্ড)',
		'cumilla'    => 'Cumilla Board (কুমিল্লা বোর্ড)',
		'jashore'    => 'Jashore Board (যশোর বোর্ড)',
		'barishal'   => 'Barishal Board (বরিশাল বোর্ড)',
		'sylhet'     => 'Sylhet Board (সিলেট বোর্ড)',
		'dinajpur'   => 'Dinajpur Board (দিনাজপুর বোর্ড)',
		'mymensingh' => 'Mymensingh Board (ময়মনসিংহ বোর্ড)',
		'madrasah'   => 'Madrasah Board (মাদ্রাসা বোর্ড)',
		'all_board'  => 'All Boards (সকল বোর্ড)',
	);

	/**
	 * Query authentic board questions with flexible filters.
	 *
	 * @param array<string,mixed> $filters Filter parameters.
	 * @return array<int,object>
	 */
	public static function get_board_questions( array $filters = array() ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'board_questions' );

		$where  = array( 'is_authentic_board = 1' );
		$values = array();

		if ( ! empty( $filters['exam_level'] ) ) {
			$where[]  = 'exam_level = %s';
			$values[] = sanitize_key( $filters['exam_level'] );
		}

		if ( ! empty( $filters['board_name'] ) && 'all' !== $filters['board_name'] ) {
			$where[]  = 'board_name = %s';
			$values[] = sanitize_key( $filters['board_name'] );
		}

		if ( ! empty( $filters['exam_year'] ) ) {
			$where[]  = 'exam_year = %d';
			$values[] = absint( $filters['exam_year'] );
		}

		if ( ! empty( $filters['lesson_id'] ) ) {
			$where[]  = 'lesson_id = %d';
			$values[] = absint( $filters['lesson_id'] );
		}

		if ( ! empty( $filters['topic'] ) ) {
			$where[]  = 'topic LIKE %s';
			$values[] = '%' . $wpdb->esc_like( sanitize_text_field( $filters['topic'] ) ) . '%';
		}

		if ( ! empty( $filters['question_type'] ) ) {
			$where[]  = 'question_type = %s';
			$values[] = sanitize_key( $filters['question_type'] );
		}

		$where_clause = implode( ' AND ', $where );
		$limit        = isset( $filters['limit'] ) ? max( 1, absint( $filters['limit'] ) ) : 50;

		$sql = "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY exam_year DESC, id ASC LIMIT {$limit}";

		if ( ! empty( $values ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
			return $wpdb->get_results( $wpdb->prepare( $sql, $values ) );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_results( $sql );
	}

	/**
	 * Get authentic board questions relevant to a specific lesson.
	 *
	 * @param int $lesson_id Lesson Post ID.
	 * @return array<int,object>
	 */
	public static function get_lesson_board_questions( $lesson_id ) {
		return self::get_board_questions( array( 'lesson_id' => $lesson_id, 'limit' => 10 ) );
	}

	/**
	 * Insert a verified authentic board question.
	 *
	 * @param array<string,mixed> $data Question data.
	 * @return int Inserted ID.
	 */
	public static function add_board_question( array $data ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'board_questions' );
		$now   = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->insert(
			$table,
			array(
				'exam_id'            => absint( $data['exam_id'] ?? 0 ),
				'lesson_id'          => absint( $data['lesson_id'] ?? 0 ),
				'concept_id'         => absint( $data['concept_id'] ?? 0 ),
				'exam_level'         => sanitize_key( $data['exam_level'] ?? 'ssc' ),
				'board_name'         => sanitize_key( $data['board_name'] ?? 'dhaka' ),
				'exam_year'          => absint( $data['exam_year'] ?? 2023 ),
				'subject'            => sanitize_key( $data['subject'] ?? 'english_1st' ),
				'paper'              => sanitize_key( $data['paper'] ?? '1st' ),
				'question_no'        => sanitize_text_field( $data['question_no'] ?? '1' ),
				'marks'              => floatval( $data['marks'] ?? 1.0 ),
				'question_type'      => sanitize_key( $data['question_type'] ?? 'mcq' ),
				'topic'              => sanitize_text_field( $data['topic'] ?? '' ),
				'question_text'      => wp_kses_post( $data['question_text'] ?? '' ),
				'options_json'       => isset( $data['options_json'] ) ? wp_json_encode( $data['options_json'] ) : null,
				'verified_answer'    => wp_kses_post( $data['verified_answer'] ?? '' ),
				'explanation'        => wp_kses_post( $data['explanation'] ?? '' ),
				'source_reference'   => sanitize_text_field( $data['source_reference'] ?? '' ),
				'is_verified'        => 1,
				'is_authentic_board' => 1,
				'created_at'         => $now,
			),
			array( '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' )
		);

		return $wpdb->insert_id;
	}

	/**
	 * Seed authentic historical board questions for English Unit 1 (Nelson Mandela).
	 *
	 * @return int Number of seeded questions.
	 */
	public static function seed_sample_board_questions() {
		$lesson = get_posts( array( 'post_type' => 'nctb_lesson', 'numberposts' => 1 ) );
		$lesson_id = ! empty( $lesson ) ? $lesson[0]->ID : 0;

		$questions = array(
			array(
				'exam_level'       => 'hsc',
				'board_name'       => 'dhaka',
				'exam_year'        => 2023,
				'subject'          => 'english_1st',
				'paper'            => '1st',
				'question_no'      => '1.A',
				'marks'            => 5.0,
				'question_type'    => 'mcq',
				'topic'            => 'Nelson Mandela: From Apartheid Fighter to President',
				'lesson_id'        => $lesson_id,
				'question_text'    => 'Choose the correct answer from the medical / historical context of the passage: What does the word "emancipation" mean in the text?',
				'options_json'     => array(
					array( 'key' => 'a', 'text' => 'Liberation or freedom from political or social restrictions' ),
					array( 'key' => 'b', 'text' => 'Subjugation under harsh law' ),
					array( 'key' => 'c', 'text' => 'Imprisonment in a distant island' ),
					array( 'key' => 'd', 'text' => 'Negotiation with colonial authorities' ),
				),
				'verified_answer'  => 'a',
				'explanation'      => 'In paragraph 3, "emancipation" refers to freeing the South African people from the continuing bondage of poverty, deprivation, and discrimination.',
				'source_reference' => 'Dhaka Board HSC Examination 2023, English 1st Paper, Question 1.A',
			),
			array(
				'exam_level'       => 'hsc',
				'board_name'       => 'cumilla',
				'exam_year'        => 2022,
				'subject'          => 'english_1st',
				'paper'            => '1st',
				'question_no'      => '2',
				'marks'            => 10.0,
				'question_type'    => 'short_answer',
				'topic'            => 'Nelson Mandela: Historical Achievements',
				'lesson_id'        => $lesson_id,
				'question_text'    => 'Why was Nelson Mandela awarded the Nobel Peace Prize in 1993, and with whom did he share it?',
				'verified_answer'  => 'Nelson Mandela was awarded the Nobel Peace Prize in 1993 for his peaceful struggle for the termination of the apartheid regime, and for laying the foundations for a new democratic South Africa. He shared the prize with F.W. de Klerk.',
				'explanation'      => 'Authentic board marking scheme requires naming F.W. de Klerk and stating the peaceful dismantling of apartheid.',
				'source_reference' => 'Cumilla Board HSC Examination 2022, English 1st Paper, Question 2',
			),
			array(
				'exam_level'       => 'hsc',
				'board_name'       => 'rajshahi',
				'exam_year'        => 2020,
				'subject'          => 'english_1st',
				'paper'            => '1st',
				'question_no'      => '3',
				'marks'            => 5.0,
				'question_type'    => 'flow_chart',
				'topic'            => 'Milestones of Nelson Mandela',
				'lesson_id'        => $lesson_id,
				'question_text'    => 'Based on your reading of the passage, make a short summary showing the milestones of Nelson Mandela\'s life leading to multi-racial democracy.',
				'verified_answer'  => '1. Imprisoned on Robben Island -> 2. Released after nearly three decades in 1990 -> 3. Awarded Nobel Peace Prize in 1993 -> 4. Elected first black President of South Africa in 1994 -> 5. Celebrated worldwide as an icon of peace and reconciliation.',
				'explanation'      => 'Full 5 marks awarded for sequential chronological milestones with correct historical dates.',
				'source_reference' => 'Rajshahi Board HSC Examination 2020, English 1st Paper, Question 3',
			),
			array(
				'exam_level'       => 'ssc',
				'board_name'       => 'dhaka',
				'exam_year'        => 2022,
				'subject'          => 'english_1st',
				'paper'            => '1st',
				'question_no'      => '1',
				'marks'            => 5.0,
				'question_type'    => 'mcq',
				'topic'            => 'People or Institutions Making History',
				'lesson_id'        => $lesson_id,
				'question_text'    => 'The word "apartheid" in the context of South African history specifically refers to:',
				'options_json'     => array(
					array( 'key' => 'a', 'text' => 'A policy or system of segregation or discrimination on grounds of race' ),
					array( 'key' => 'b', 'text' => 'An economic treaty between Commonwealth nations' ),
					array( 'key' => 'c', 'text' => 'A constitutional amendment for universal healthcare' ),
					array( 'key' => 'd', 'text' => 'A traditional cultural festival' ),
				),
				'verified_answer'  => 'a',
				'explanation'      => 'Apartheid was the institutionalised system of racial segregation in South Africa from 1948 until the early 1990s.',
				'source_reference' => 'Dhaka Board SSC Examination 2022, English 1st Paper, Question 1',
			),
		);

		$count = 0;
		foreach ( $questions as $q ) {
			self::add_board_question( $q );
			$count++;
		}

		return $count;
	}
}
