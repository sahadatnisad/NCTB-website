<?php
/**
 * One-time sample curriculum seeder.
 *
 * Creates a single prototype tree (one SSC English book → one unit → one
 * lesson, with concepts and learning outcomes) so the browse experience can be
 * demonstrated. Runs once, guarded by an option flag, and only if no books
 * exist. It never overwrites editor-created content.
 *
 * Per the plan (Phase 3): enter only enough data for one prototype lesson.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Curriculum_Seeder
 */
class NCTB_Curriculum_Seeder {

	const SEEDED_OPTION = 'nctb_lh_sample_seeded';

	/**
	 * Seed the sample tree once.
	 *
	 * @return void
	 */
	public static function maybe_seed() {
		if ( get_option( self::SEEDED_OPTION ) ) {
			return;
		}

		// Do not seed if an editor already created books.
		$existing = get_posts(
			array(
				'post_type'   => NCTB_Curriculum_CPT::CPT_BOOK,
				'post_status' => 'any',
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		if ( ! empty( $existing ) ) {
			update_option( self::SEEDED_OPTION, 1, false );
			return;
		}

		// Book.
		$book_id = wp_insert_post(
			array(
				'post_type'    => NCTB_Curriculum_CPT::CPT_BOOK,
				'post_status'  => 'publish',
				'post_title'   => 'English For Today — SSC (Sample)',
				'post_content' => 'Sample NCTB SSC English book used to demonstrate the browse experience. Replace with real curriculum content.',
				'menu_order'   => 1,
			)
		);
		if ( ! $book_id || is_wp_error( $book_id ) ) {
			return;
		}
		wp_set_object_terms( $book_id, 'SSC', 'nctb_class_level' );
		wp_set_object_terms( $book_id, 'English 1st Paper', 'nctb_subject' );

		// Unit.
		$unit_id = wp_insert_post(
			array(
				'post_type'    => NCTB_Curriculum_CPT::CPT_UNIT,
				'post_status'  => 'publish',
				'post_title'   => 'Unit 1 — People and Relationships (Sample)',
				'post_content' => 'Sample unit.',
				'menu_order'   => 1,
			)
		);
		update_post_meta( $unit_id, NCTB_Curriculum_CPT::META_BOOK_ID, $book_id );

		// Lesson.
		$lesson_id = wp_insert_post(
			array(
				'post_type'    => NCTB_Curriculum_CPT::CPT_LESSON,
				'post_status'  => 'publish',
				'post_title'   => 'Lesson 1 — Nelson Mandela, from Prisoner to President (Sample)',
				'post_content' => "This is a sample lesson used to prove the curriculum browse flow. Real lessons will contain reading, vocabulary, grammar focus and practice activities.\n\nReplace this content from the WordPress admin.",
				'menu_order'   => 1,
			)
		);
		update_post_meta( $lesson_id, NCTB_Curriculum_CPT::META_UNIT_ID, $unit_id );
		wp_set_object_terms( $lesson_id, 'Reading', 'nctb_topic' );

		// Concepts.
		$c1 = NCTB_Curriculum_Data::create_concept(
			array(
				'name'        => 'Main idea of a text',
				'subject'     => 'english_1st',
				'description' => 'Identifying the central point of a paragraph or passage.',
			)
		);
		$c2 = NCTB_Curriculum_Data::create_concept(
			array(
				'name'        => 'Context clues for vocabulary',
				'subject'     => 'english_1st',
				'description' => 'Guessing word meaning from surrounding text.',
			)
		);

		$concept_ids = array();
		if ( is_int( $c1 ) ) {
			$concept_ids[] = $c1;
		}
		if ( is_int( $c2 ) ) {
			$concept_ids[] = $c2;
		}
		if ( $concept_ids ) {
			NCTB_Curriculum_Data::set_lesson_concepts( $lesson_id, $concept_ids );
		}

		// Learning outcomes.
		NCTB_Curriculum_Data::set_lesson_outcomes(
			$lesson_id,
			array(
				'Identify the main idea of a reading passage.',
				'Guess the meaning of new words from context.',
				'Answer comprehension questions in complete sentences.',
			)
		);

		update_option( self::SEEDED_OPTION, 1, false );
		NCTB_Logger::info( 'Seeded sample curriculum tree', array( 'book' => $book_id, 'unit' => $unit_id, 'lesson' => $lesson_id ) );
	}
}
