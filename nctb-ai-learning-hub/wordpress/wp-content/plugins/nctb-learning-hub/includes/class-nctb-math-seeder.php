<?php
/**
 * Mathematics Curriculum & Engine Seeder (Phase 21).
 *
 * Seeds authentic SSC General Mathematics curriculum data, interactive math lessons
 * with KaTeX formulas, math_numeric and math_expression questions, and revision notes.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Math_Seeder
 */
class NCTB_Math_Seeder {

	/**
	 * Seed Mathematics curriculum if not already present.
	 *
	 * @return void
	 */
	public static function maybe_seed_math() {
		$existing = get_page_by_title( 'SSC General Mathematics (নবম-দশম শ্রেণি সাধারণ গণিত)', OBJECT, 'nctb_book' );
		if ( $existing ) {
			return;
		}

		global $wpdb;

		// 1. Create Math Book
		$book_id = wp_insert_post(
			array(
				'post_title'   => 'SSC General Mathematics (নবম-দশম শ্রেণি সাধারণ গণিত)',
				'post_content' => 'মাধ্যমিক সাধারণ গণিত সম্পূর্ণ পাঠ্যক্রম। সেট ও ফাংশন, বীজগাণিতিক রাশি, ত্রিকোণমিতি ও পরিমিতি।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_book',
			)
		);
		if ( ! $book_id || is_wp_error( $book_id ) ) {
			return;
		}
		update_post_meta( $book_id, '_nctb_class', 'class_10' );
		update_post_meta( $book_id, '_nctb_subject', 'General Mathematics' );

		// 2. Unit 1: বীজগাণিতিক রাশি
		$u1 = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ৩: বীজগাণিতিক রাশি (Algebraic Expressions)',
				'post_content' => 'বর্গ ও ঘন সংবলিত সূত্রাবলি, অনুসিদ্ধান্ত, উৎপাদকে বিশ্লেষণ এবং বাস্তব সমস্যা সমাধান।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u1, '_nctb_book_id', $book_id );

		// Lesson 1: বর্গের সূত্রাবলি ও মান নির্ণয়
		$l1 = wp_insert_post(
			array(
				'post_title'   => '৩.১ বর্গের সূত্রাবলি ও মান নির্ণয় (Square Formulas & Values)',
				'post_content' => '$(a+b)^2 = a^2 + 2ab + b^2$ এবং $(a-b)^2 = a^2 - 2ab + b^2$ সূত্রের প্রয়োগ ও মান নির্ণয়।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 0,
			)
		);
		update_post_meta( $l1, '_nctb_unit_id', $u1 );
		update_post_meta( $l1, '_nctb_is_free', 1 );

		// 3. Unit 2: ত্রিকোণমিতিক অনুপাত
		$u2 = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ৯: ত্রিকোণমিতিক অনুপাত (Trigonometric Ratios)',
				'post_content' => 'সমকোণী ত্রিভুজের বাহু ও কোণের সম্পর্ক, ত্রিকোণমিতিক অভেদাবলি এবং কোণের মান নির্ণয়।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u2, '_nctb_book_id', $book_id );

		// Lesson 2: ত্রিকোণমিতিক অভেদাবলি
		$l2 = wp_insert_post(
			array(
				'post_title'   => '৯.১ ত্রিকোণমিতিক অনুপাত ও মৌলিক অভেদ (Trigonometric Identities)',
				'post_content' => '$\sin^2\theta + \cos^2\theta = 1$, $\sec^2\theta - \tan^2\theta = 1$ অভেদের প্রমাণ ও গাণিতিক সমস্যা।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 1,
			)
		);
		update_post_meta( $l2, '_nctb_unit_id', $u2 );

		// 4. Insert Interactive Lesson Activities into wp_nctb_lesson_activities
		$act_table = NCTB_Migrations::table( 'lesson_activities' );
		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l1,
				'step_number'   => 1,
				'title'         => 'বর্গের মূল সূত্র ও অনুসিদ্ধান্ত',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>বীজগাণিতিক বর্গের সূত্রসমূহ এবং মান নির্ণয়ে এদের ব্যবহার:</p>
<ul>
<li><strong>সূত্র ১:</strong> \((a+b)^2 = a^2 + 2ab + b^2\)</li>
<li><strong>সূত্র ২:</strong> \((a-b)^2 = a^2 - 2ab + b^2\)</li>
<li><strong>অনুসিদ্ধান্ত ১:</strong> \(a^2 + b^2 = (a+b)^2 - 2ab\)</li>
<li><strong>অনুসিদ্ধান্ত ২:</strong> \(a^2 + b^2 = (a-b)^2 + 2ab\)</li>
<li><strong>অনুসিদ্ধান্ত ৩:</strong> \(4ab = (a+b)^2 - (a-b)^2\)</li>
</ul>',
			)
		);

		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l2,
				'step_number'   => 1,
				'title'         => 'ত্রিকোণমিতিক অনুপাতের অনুপাত সূত্র',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>সমকোণী ত্রিভুজে কোণ \(\theta\) এর সাপেক্ষে ত্রিকোণমিতিক অনুপাত:</p>
<table class="nctb-table">
<thead>
<tr><th>অনুপাত</th><th>সূত্র</th><th>বিপরীত অনুপাত</th></tr>
</thead>
<tbody>
<tr><td>\(\sin\theta\)</td><td>\(\frac{\text{লম্ব}}{\text{অতিভুজ}}\)</td><td>\(\csc\theta = \frac{1}{\sin\theta}\)</td></tr>
<tr><td>\(\cos\theta\)</td><td>\(\frac{\text{ভূমি}}{\text{অতিভুজ}}\)</td><td>\(\sec\theta = \frac{1}{\cos\theta}\)</td></tr>
<tr><td>\(\tan\theta\)</td><td>\(\frac{\text{লম্ব}}{\text{ভূমি}}\)</td><td>\(\cot\theta = \frac{1}{\tan\theta}\)</td></tr>
</tbody>
</table>',
			)
		);

		// 5. Insert Practice Questions into wp_nctb_questions (using math_numeric & math_expression)
		$q_table = NCTB_Migrations::table( 'questions' );
		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l1,
				'activity_id'    => 0,
				'question_type'  => 'math_numeric',
				'prompt'         => 'যদি \(x + \frac{1}{x} = 4\) হয়, তবে \(x^2 + \frac{1}{x^2}\) এর মান কত?',
				'options'        => '[]',
				'correct_answer' => '14 | ১৪',
				'explanation'    => '\(x^2 + \frac{1}{x^2} = (x + \frac{1}{x})^2 - 2(x)(\frac{1}{x}) = 4^2 - 2 = 16 - 2 = 14\)।',
				'difficulty'     => 'medium',
				'hints'          => wp_json_encode( array( 'অনুসিদ্ধান্ত \(a^2 + b^2 = (a+b)^2 - 2ab\) ব্যবহার করুন।' ) ),
			)
		);

		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l1,
				'activity_id'    => 0,
				'question_type'  => 'math_expression',
				'prompt'         => '\((a+b)^2 - (a-b)^2\) এর সরলীকৃত মান কত?',
				'options'        => '[]',
				'correct_answer' => '4ab | 4*a*b | 4ba',
				'explanation'    => '\((a+b)^2 - (a-b)^2 = (a^2 + 2ab + b^2) - (a^2 - 2ab + b^2) = 4ab\)।',
				'difficulty'     => 'easy',
				'hints'          => wp_json_encode( array( 'এটি ৪ab এর সরাসরি অনুসিদ্ধান্ত।' ) ),
			)
		);

		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l2,
				'activity_id'    => 0,
				'question_type'  => 'math_numeric',
				'prompt'         => '\(\tan 45^\circ + \sin 30^\circ\) এর মান কত?',
				'options'        => '[]',
				'correct_answer' => '1.5 | 3/2 | ১.৫',
				'explanation'    => '\(\tan 45^\circ = 1\) এবং \(\sin 30^\circ = 0.5 = 1/2\)। সুতরাং \(1 + 0.5 = 1.5 = 3/2\)।',
				'difficulty'     => 'medium',
				'hints'          => wp_json_encode( array( 'tan 45 = 1 এবং sin 30 = 1/2।' ) ),
			)
		);

		// 6. Insert Board Questions into wp_nctb_board_questions
		$bq_table = NCTB_Migrations::table( 'board_questions' );
		$wpdb->insert(
			$bq_table,
			array(
				'board_slug'     => 'dhaka',
				'year'           => 2024,
				'subject_slug'   => 'math',
				'paper'          => 1,
				'question_type'  => 'mcq',
				'topic_slug'     => 'trigonometry',
				'prompt'         => 'যদি \(\tan\theta = \frac{4}{3}\) হয়, তবে \(\sec\theta\) এর মান কত?',
				'options'        => wp_json_encode( array( '5/3', '3/5', '4/5', '5/4' ) ),
				'correct_answer' => '5/3',
				'marks'          => 1,
				'verified'       => 1,
				'explanation'    => '\(\sec^2\theta = 1 + \tan^2\theta = 1 + 16/9 = 25/9 \Rightarrow \sec\theta = 5/3\)।',
			)
		);

		// 7. Create Math Revision Note
		$note_content = '<h2>📐 এসএসসি সাধারণ গণিত: বীজগাণিতিক সূত্রাবলি ও ত্রিকোণমিতি চিটশিট</h2>

<h3>১. বর্গের সূত্রাবলি ও অনুসিদ্ধান্ত:</h3>
<ul>
<li>\((a+b)^2 = a^2 + 2ab + b^2\)</li>
<li>\((a-b)^2 = a^2 - 2ab + b^2\)</li>
<li>\(a^2 - b^2 = (a+b)(a-b)\)</li>
<li>\(a^2 + b^2 = (a+b)^2 - 2ab = (a-b)^2 + 2ab\)</li>
<li>\(4ab = (a+b)^2 - (a-b)^2\)</li>
<li>\(2(a^2 + b^2) = (a+b)^2 + (a-b)^2\)</li>
</ul>

<h3>২. ত্রিকোণমিতিক মৌলিক অভেদাবলি:</h3>
<ul>
<li>\(\sin^2\theta + \cos^2\theta = 1 \Rightarrow \sin^2\theta = 1 - \cos^2\theta\)</li>
<li>\(\sec^2\theta - \tan^2\theta = 1 \Rightarrow \sec^2\theta = 1 + \tan^2\theta\)</li>
<li>\(\csc^2\theta - \cot^2\theta = 1 \Rightarrow \csc^2\theta = 1 + \cot^2\theta\)</li>
</ul>';

		$n = wp_insert_post(
			array(
				'post_title'   => 'SSC General Math: Algebra & Trigonometry Complete Formula Sheet',
				'post_content' => $note_content,
				'post_excerpt' => 'বীজগণিত ও ত্রিকোণমিতির সকল মূল সূত্র, অনুসিদ্ধান্ত ও কোণের মানের পূর্ণাঙ্গ রিভিশন শিট।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_note',
			)
		);
		if ( $n && ! is_wp_error( $n ) ) {
			update_post_meta( $n, '_nctb_note_class', 'class_10' );
			update_post_meta( $n, '_nctb_note_subject', 'General Mathematics' );
			update_post_meta( $n, '_nctb_note_audience', 'both' );
			update_post_meta( $n, '_nctb_note_difficulty', 'medium' );
			update_post_meta( $n, '_nctb_note_lesson_id', $l1 );
			wp_set_object_terms( $n, 'formula_sheet', 'note_type' );
		}

		// 8. Create Math Video Module
		$mod_items = array(
			array(
				'id'          => 'math_mod_1',
				'title'       => 'বীজগাণিতিক রাশির মান নির্ণয় ও সৃজনশীল কৌশল',
				'youtube_id'  => '3Q8u_V2L2o4',
				'duration'    => '25 mins',
				'description' => 'বর্গ ও ঘনের সূত্রাবলি প্রয়োগ করে বোর্ড পরীক্ষার ৪ নম্বরের সৃজনশীল প্রশ্নের সমাধান।',
			),
			array(
				'id'          => 'math_mod_2',
				'title'       => 'ত্রিকোণমিতি ৯.১ ও ৯.২ এর সম্পূর্ণ বেসিক টু অ্যাডভান্সড',
				'youtube_id'  => 'KJgsSFOSQv0',
				'duration'    => '30 mins',
				'description' => 'ত্রিকোণমিতিক অনুপাতের মান নির্ণয় ও অভেদাবলি প্রমাণের সহজ শর্টকাট।',
			),
		);

		$mod = wp_insert_post(
			array(
				'post_title'   => 'SSC General Math: বীজগণিত ও ত্রিকোণমিতি মাস্টারক্লাস',
				'post_content' => 'নবম-দশম শ্রেণির সাধারণ গণিতের বীজগণিত ও ত্রিকোণমিতির সম্পূর্ণ সমাধান।',
				'post_excerpt' => 'বোর্ড পরীক্ষার শতভাগ প্রস্তুতির জন্য গণিতের ভিডিও লেকচার ও প্রশ্ন সমাধান।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_module',
			)
		);
		if ( $mod && ! is_wp_error( $mod ) ) {
			update_post_meta( $mod, '_nctb_module_audience', 'student' );
			update_post_meta( $mod, '_nctb_module_class', 'class_10' );
			update_post_meta( $mod, '_nctb_module_duration', '55 mins' );
			update_post_meta( $mod, '_nctb_module_instructor', 'NCTB Math Faculty' );
			update_post_meta( $mod, '_nctb_module_items', wp_json_encode( $mod_items ) );
			wp_set_object_terms( $mod, 'student_masterclass', 'module_category' );
		}
	}
}
