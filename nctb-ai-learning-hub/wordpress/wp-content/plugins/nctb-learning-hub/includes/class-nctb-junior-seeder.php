<?php
/**
 * Junior Secondary (Class 6-8 / JSC / JDC) Curriculum Seeder (Phase 23).
 *
 * Ingests authentic Class 8 Mathematics, English for Today, and General Science
 * curriculum data, interactive lessons, practice questions, and formula/revision notes.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Junior_Seeder
 */
class NCTB_Junior_Seeder {

	/**
	 * Seed Junior secondary curriculum if not already present.
	 *
	 * @return void
	 */
	public static function maybe_seed_junior() {
		$existing = get_page_by_title( 'Class 8 Mathematics (অষ্টম শ্রেণি গণিত)', OBJECT, 'nctb_book' );
		if ( $existing ) {
			return;
		}

		global $wpdb;
		$act_table = NCTB_Migrations::table( 'lesson_activities' );
		$q_table   = NCTB_Migrations::table( 'questions' );
		$bq_table  = NCTB_Migrations::table( 'board_questions' );

		/* ==========================================================================
		   1. CLASS 8 MATHEMATICS (অষ্টম শ্রেণি গণিত)
		   ========================================================================== */
		$b_math8 = wp_insert_post(
			array(
				'post_title'   => 'Class 8 Mathematics (অষ্টম শ্রেণি গণিত)',
				'post_content' => 'অষ্টম শ্রেণির গণিত পাঠ্যক্রম। প্যাটার্ন, মুনাফা, পরিমাপ, বীজগাণিতিক সূত্রাবলি ও জ্যামিতি।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_book',
			)
		);
		update_post_meta( $b_math8, '_nctb_class', 'class_8' );
		update_post_meta( $b_math8, '_nctb_subject', 'Mathematics' );

		$u_math8 = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ২: মুনাফা (Profit / Interest)',
				'post_content' => 'সরল মুনাফা, মুনাফা-আসল, চক্রবৃদ্ধি মূলধন ও চক্রবৃদ্ধি মুনাফার ধারণা ও গাণিতিক সমস্যা।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u_math8, '_nctb_book_id', $b_math8 );

		$l_math8 = wp_insert_post(
			array(
				'post_title'   => '২.১ সরল মুনাফা ও মুনাফা-আসল (Simple Profit & Total Amount)',
				'post_content' => '$I = Pnr$ এবং $A = P + I = P(1+nr)$ সূত্রের প্রয়োগ ও বাস্তব সমস্যা সমাধান।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 0,
			)
		);
		update_post_meta( $l_math8, '_nctb_unit_id', $u_math8 );
		update_post_meta( $l_math8, '_nctb_is_free', 1 );

		// Activity
		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l_math8,
				'step_number'   => 1,
				'title'         => 'সরল মুনাফার মূল সূত্র ও প্রতীক পরিচিতি',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>সরল মুনাফা হিসাব করার মৌলিক সূত্র:</p>
<div class="nctb-formula-box">
  $$I = Pnr$$
</div>
<ul>
<li>\(I =\) সরল মুনাফা (Profit)</li>
<li>\(P =\) মূলধন বা আসল (Principal)</li>
<li>\(n =\) সময় / বছর (Time in years)</li>
<li>\(r =\) মুনাফার হার (Rate of profit, \(\frac{r}{100}\))</li>
<li>\(A = P + I = P(1+nr)\) (মুনাফা-আসল / Total Amount)</li>
</ul>',
			)
		);

		// Question
		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l_math8,
				'activity_id'    => 0,
				'question_type'  => 'math_numeric',
				'prompt'         => 'বার্ষিক \(10\%\) মুনাফায় \(১০০০\) টাকার \(৩\) বছরের সরল মুনাফা কত টাকা?',
				'options'        => '[]',
				'correct_answer' => '300 | ৩০০',
				'explanation'    => '\(I = Pnr = 1000 \times 3 \times \frac{10}{100} = 300\) টাকা।',
				'difficulty'     => 'easy',
				'hints'          => wp_json_encode( array( '\(I = Pnr\) সূত্রে মান বসান।' ) ),
			)
		);

		// Note
		$n_math8 = wp_insert_post(
			array(
				'post_title'   => 'Class 8 Math: মুনাফা ও পরিমাপ সূত্রাবলি Formula Sheet',
				'post_content' => '<h2>📐 অষ্টম শ্রেণি গণিত: মুনাফা ও পরিমাপের সকল সূত্র</h2>
<ul>
<li><strong>সরল মুনাফা:</strong> \(I = Pnr\)</li>
<li><strong>মুনাফা-আসল:</strong> \(A = P + I = P(1+nr)\)</li>
<li><strong>চক্রবৃদ্ধি মূলধন:</strong> \(C = P(1+r)^n\)</li>
<li><strong>চক্রবৃদ্ধি মুনাফা:</strong> \(C - P = P(1+r)^n - P\)</li>
<li><strong>আয়তক্ষেত্রের ক্ষেত্রফল:</strong> \(\text{দৈর্ঘ্য} \times \text{প্রস্থ}\)</li>
<li><strong>ত্রিভুজের ক্ষেত্রফল:</strong> \(\frac{1}{2} \times \text{ভূমি} \times \text{উচ্চতা}\)</li>
</ul>',
				'post_excerpt' => 'সরল ও চক্রবৃদ্ধি মুনাফা, আয়তক্ষেত্র ও বর্গের ক্ষেত্রফল পরিমাপের ফর্মুলা শিট।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_note',
			)
		);
		update_post_meta( $n_math8, '_nctb_note_class', 'class_8' );
		update_post_meta( $n_math8, '_nctb_note_subject', 'Mathematics' );
		update_post_meta( $n_math8, '_nctb_note_audience', 'both' );
		update_post_meta( $n_math8, '_nctb_note_difficulty', 'foundation' );
		update_post_meta( $n_math8, '_nctb_note_lesson_id', $l_math8 );
		wp_set_object_terms( $n_math8, 'formula_sheet', 'note_type' );

		/* ==========================================================================
		   2. CLASS 8 ENGLISH FOR TODAY
		   ========================================================================== */
		$b_eng8 = wp_insert_post(
			array(
				'post_title'   => 'Class 8 English for Today',
				'post_content' => 'Class 8 English for Today textbook. Culture, folklore, sports, media, and reading comprehension.',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_book',
			)
		);
		update_post_meta( $b_eng8, '_nctb_class', 'class_8' );
		update_post_meta( $b_eng8, '_nctb_subject', 'English' );

		$u_eng8 = wp_insert_post(
			array(
				'post_title'   => 'Unit 1: A Glimpse of Our Culture',
				'post_content' => 'Folk songs, Nakshi Kantha, ethnic friends, and traditional Bangladeshi cuisine.',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u_eng8, '_nctb_book_id', $b_eng8 );

		$l_eng8 = wp_insert_post(
			array(
				'post_title'   => 'Lesson 1: Our Folk Songs',
				'post_content' => 'Folk songs sung in traditional style by common people with indigenous musical instruments like Dotara, Ektara, and Dhol.',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 0,
			)
		);
		update_post_meta( $l_eng8, '_nctb_unit_id', $u_eng8 );
		update_post_meta( $l_eng8, '_nctb_is_free', 1 );

		// Activity
		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l_eng8,
				'step_number'   => 1,
				'title'         => 'Reading Passage: Bangladeshi Folk Songs',
				'activity_type' => 'reading_passage',
				'content'       => '<p>Folk songs are songs sung in the traditional style of a community or country. Here the traditional style includes the themes, words and tunes of the songs that have existed for a long time among the common people.</p><p>We have a rich history and collection of folk songs in Bangladesh. Of them <em>Palligiti</em>, <em>Bhatiyali</em>, <em>Bhawaiya</em>, <em>Jari</em>, <em>Sari</em>, <em>Gambhira</em>, <em>Lalon giti</em>, and <em>Hason Raja songs</em> are very popular.</p>',
			)
		);

		// Question
		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l_eng8,
				'activity_id'    => 0,
				'question_type'  => 'mcq',
				'prompt'         => 'Which of the following is a popular folk song genre of Bangladesh?',
				'options'        => wp_json_encode( array( 'Bhatiyali', 'Hip-hop', 'Jazz', 'Rock' ) ),
				'correct_answer' => 'Bhatiyali',
				'explanation'    => 'Bhatiyali is a traditional boatman folk song genre deeply rooted in Bangladesh riverine culture.',
				'difficulty'     => 'easy',
				'hints'          => wp_json_encode( array( 'It is sung by boatmen in riverine regions.' ) ),
			)
		);

		/* ==========================================================================
		   3. CLASS 8 GENERAL SCIENCE (অষ্টম শ্রেণি বিজ্ঞান)
		   ========================================================================== */
		$b_sci8 = wp_insert_post(
			array(
				'post_title'   => 'Class 8 General Science (অষ্টম শ্রেণি বিজ্ঞান)',
				'post_content' => 'প্রাণিজগতের শ্রেণিবিন্যাস, জীবের বৃদ্ধি ও বংশগতি, রাসায়নিক বিক্রিয়া, পদার্থের গঠন ও পৃথিবী-মহাকর্ষ।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_book',
			)
		);
		update_post_meta( $b_sci8, '_nctb_class', 'class_8' );
		update_post_meta( $b_sci8, '_nctb_subject', 'General Science' );

		$u_sci8 = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ১: প্রাণিজগতের শ্রেণিবিন্যাস (Classification of Animals)',
				'post_content' => 'অ্যানিম্যালিয়া জগতের পর্বসমূহ: পরিফেরা, নিডারিয়া, প্লাটিহেলমিনথিস, অ্যানেলিডা, আর্থ্রোপোডা, মলাস্কা ও কর্ডাটা।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u_sci8, '_nctb_book_id', $b_sci8 );

		$l_sci8 = wp_insert_post(
			array(
				'post_title'   => '১.১ অমেরুদণ্ডী প্রাণীর শ্রেণিবিন্যাস (Invertebrate Phyla)',
				'post_content' => 'আর্থ্রোপোডা ও মলাস্কা পর্বের প্রাণীদের সাধারণ বৈশিষ্ট্য ও উদাহরণ (প্রজাপতি, চিংড়ি, শামুক, ঝিনুক)।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 0,
			)
		);
		update_post_meta( $l_sci8, '_nctb_unit_id', $u_sci8 );
		update_post_meta( $l_sci8, '_nctb_is_free', 1 );

		// Activity
		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l_sci8,
				'step_number'   => 1,
				'title'         => 'আর্থ্রোপোডা ও মলাস্কা পর্বের বৈশিষ্ট্য',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>প্রাণিজগতের দুটি বৃহত্তম অমেরুদণ্ডী পর্ব:</p>
<ul>
<li><strong>আর্থ্রোপোডা (Arthropoda):</strong> প্রাণিজগতের বৃহত্তম পর্ব। দেহ খণ্ডায়িত ও সন্ধিযুক্ত উপাঙ্গ বিদ্যমান। মাথায় একজোড়া পুঞ্জাক্ষি ও এন্টেনা থাকে। কাইটিনযুক্ত শক্ত বহিঃকঙ্কাল রয়েছে (উদাঃ প্রজাপতি, চিংড়ি, আরশোলা)।</li>
<li><strong>মলাস্কা (Mollusca):</strong> দেহ নরম, সাধারণত শক্ত খোলস বা ক্যালসিয়াম কার্বনেটের আবরণী দ্বারা আবৃত। পেশিবহুল পা দিয়ে চলাচল করে (উদাঃ শামুক, ঝিনুক, অক্টোপাস)।</li>
</ul>',
			)
		);

		// Question
		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l_sci8,
				'activity_id'    => 0,
				'question_type'  => 'fill_blank',
				'prompt'         => 'প্রাণিজগতের সর্ববৃহৎ পর্বের নাম হলো ____।',
				'options'        => '[]',
				'correct_answer' => 'আর্থ্রোপোডা | Arthropoda',
				'explanation'    => 'আর্থ্রোপোডা পর্বের প্রাণীদের সংখ্যা সবচেয়ে বেশি এবং এটি প্রাণিজগতের সর্ববৃহৎ পর্ব।',
				'difficulty'     => 'foundation',
				'hints'          => wp_json_encode( array( 'সন্ধিপদযুক্ত প্রাণীদের পর্ব।' ) ),
			)
		);
	}
}
