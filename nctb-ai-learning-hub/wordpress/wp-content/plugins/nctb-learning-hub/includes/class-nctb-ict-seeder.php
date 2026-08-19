<?php
/**
 * Content Seeder for Second Subject: ICT (Phase 20).
 *
 * Demonstrates engine generality by creating authentic HSC ICT curriculum data
 * (Books, Units, Lessons, Interactive Activities, Questions, Board Questions, Notes, and Video Modules)
 * without requiring any engine modifications.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_ICT_Seeder
 */
class NCTB_ICT_Seeder {

	/**
	 * Seed ICT curriculum if not already present.
	 *
	 * @return void
	 */
	public static function maybe_seed_ict() {
		// Check if ICT book already exists
		$existing = get_page_by_title( 'HSC Information & Communication Technology (তথ্য ও যোগাযোগ প্রযুক্তি)', OBJECT, 'nctb_book' );
		if ( $existing ) {
			return;
		}

		global $wpdb;

		// 1. Create ICT Book
		$book_id = wp_insert_post(
			array(
				'post_title'   => 'HSC Information & Communication Technology (তথ্য ও যোগাযোগ প্রযুক্তি)',
				'post_content' => 'উচ্চ মাধ্যমিক তথ্য ও যোগাযোগ প্রযুক্তি (ICT) সম্পূর্ণ পাঠ্যক্রম। সংখ্যা পদ্ধতি, ডিজিটাল ডিভাইস, ওয়েব ডিজাইন ও সি প্রোগ্রামিং।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_book',
			)
		);
		if ( ! $book_id || is_wp_error( $book_id ) ) {
			return;
		}
		update_post_meta( $book_id, '_nctb_class', 'class_11' );
		update_post_meta( $book_id, '_nctb_subject', 'ICT' );

		// 2. Unit 1: বিশ্ব ও বাংলাদেশ প্রেক্ষিত
		$u1 = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ১: তথ্য ও যোগাযোগ প্রযুক্তি: বিশ্ব ও বাংলাদেশ প্রেক্ষিত',
				'post_content' => 'ভার্চুয়াল রিয়েলিটি, কৃত্রিম বুদ্ধিমত্তা, বায়োমেট্রিক্স ও সাইবার নিরাপত্তা।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u1, '_nctb_book_id', $book_id );

		// Lesson 1: ভার্চুয়াল রিয়েলিটি ও কৃত্রিম বুদ্ধিমত্তা
		$l1 = wp_insert_post(
			array(
				'post_title'   => '১.১ ভার্চুয়াল রিয়েলিটি ও কৃত্রিম বুদ্ধিমত্তা (VR & Artificial Intelligence)',
				'post_content' => 'কম্পিউটার সিমুলেশনের মাধ্যমে কৃত্রিম ত্রিমাত্রিক পরিবেশ সৃষ্টি ও মানুষের বুদ্ধিমত্তাকে যন্ত্রে প্রয়োগ করার কৌশল।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 0,
			)
		);
		update_post_meta( $l1, '_nctb_unit_id', $u1 );
		update_post_meta( $l1, '_nctb_is_free', 1 );

		// 3. Unit 2: সংখ্যা পদ্ধতি ও ডিজিটাল ডিভাইস
		$u2 = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ৩: সংখ্যা পদ্ধতি ও ডিজিটাল ডিভাইস (Number Systems & Logic Gates)',
				'post_content' => 'বাইনারি, অক্টাল, হেক্সাডেসিমেল রূপান্তর, ২-এর পরিপূরক এবং মৌলিক ও সার্বজনীন লজিক গেইট।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u2, '_nctb_book_id', $book_id );

		// Lesson 2: লজিক গেইট ও সত্যক সারণী
		$l2 = wp_insert_post(
			array(
				'post_title'   => '৩.২ মৌলিক ও সার্বজনীন লজিক গেইট (Logic Gates & Truth Tables)',
				'post_content' => 'AND, OR, NOT, NAND, NOR এবং XOR গেইটের সমীকরণ ও সত্যক সারণী বিশ্লেষণ।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 1,
			)
		);
		update_post_meta( $l2, '_nctb_unit_id', $u2 );

		// 4. Unit 3: ওয়েব ডিজাইন পরিচিতি এবং HTML
		$u3 = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ৪: ওয়েব ডিজাইন পরিচিতি এবং HTML',
				'post_content' => 'ওয়েবসাইটের কাঠামো, HTML মৌলিক ট্যাগ, টেবিল, হাইপারলিংক ও ইমেজ সংযোজন।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u3, '_nctb_book_id', $book_id );

		// Lesson 3: HTML বেসিক ও টেবিল ডিজাইন
		$l3 = wp_insert_post(
			array(
				'post_title'   => '৪.১ HTML ফরম্যাটিং ট্যাগ ও টেবিল তৈরি (HTML Tags & Tables)',
				'post_content' => 'ওয়েব পেজে টেক্সট ফরম্যাটিং, অর্ডারড/আনঅর্ডারড লিস্ট এবং <table> ট্যাগের ব্যবহার।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 2,
			)
		);
		update_post_meta( $l3, '_nctb_unit_id', $u3 );

		// 5. Unit 4: প্রোগ্রামিং ভাষা (C Programming)
		$u4 = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ৫: প্রোগ্রামিং ভাষা (Programming in C)',
				'post_content' => 'অ্যালগরিদম, ফ্লোচার্ট, সি প্রোগ্রামের গঠন, ডেটা টাইপ, কন্ডিশনাল স্টেটমেন্ট ও লুপ।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u4, '_nctb_book_id', $book_id );

		// Lesson 4: সি প্রোগ্রামিং কন্ট্রোল স্টেটমেন্ট ও লুপ
		$l4 = wp_insert_post(
			array(
				'post_title'   => '৫.১ সি প্রোগ্রামিং: if-else ও for লুপ (C Control Structures)',
				'post_content' => 'ধারার যোগফল নির্ণয়, জোড়/বিজোড় সংখ্যা যাচাই এবং লুপের ব্যবহার।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 3,
			)
		);
		update_post_meta( $l4, '_nctb_unit_id', $u4 );

		// 6. Insert Interactive Lesson Activities into wp_nctb_lesson_activities
		$act_table = NCTB_Migrations::table( 'lesson_activities' );
		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l2,
				'step_number'   => 1,
				'title'         => 'মৌলিক লজিক গেইটসমূহ (Basic Logic Gates)',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>বুলিয়ান অ্যালজেবরায় তিনটি মৌলিক লজিক গেইট রয়েছে:</p><ul><li><strong>AND Gate:</strong> গুণনের নিয়ম ($Y = A \cdot B$)। উভয় ইনপুট ১ হলেই আউটপুট ১ হবে।</li><li><strong>OR Gate:</strong> যোগের নিয়ম ($Y = A + B$)। যেকোনো একটি ইনপুট ১ হলেই আউটপুট ১ হবে।</li><li><strong>NOT Gate:</strong> ইনভার্টার ($Y = \overline{A}$)। ইনপুট ০ হলে আউটপুট ১, ইনপুট ১ হলে আউটপুট ০।</li></ul>',
			)
		);

		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l3,
				'step_number'   => 1,
				'title'         => 'HTML টেবিলের মৌলিক গঠন (HTML Table Structure)',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>ওয়েব পেজে ডেটা টেবিল আকারে প্রদর্শনের জন্য <code>&lt;table&gt;</code> ট্যাগ ব্যবহার করা হয়:</p><pre><code>&lt;table border="1"&gt;
  &lt;tr&gt;
    &lt;th&gt;Roll&lt;/th&gt;
    &lt;th&gt;Name&lt;/th&gt;
  &lt;/tr&gt;
  &lt;tr&gt;
    &lt;td&gt;101&lt;/td&gt;
    &lt;td&gt;Rahim&lt;/td&gt;
  &lt;/tr&gt;
&lt;/table&gt;</code></pre>',
			)
		);

		// 7. Insert Practice Questions into wp_nctb_questions
		$q_table = NCTB_Migrations::table( 'questions' );
		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l2,
				'activity_id'    => 0,
				'question_type'  => 'mcq',
				'prompt'         => 'কোন লজিক গেইটের সকল ইনপুট ১ হলে আউটপুট ০ হয়?',
				'options'        => wp_json_encode( array( 'AND', 'NAND', 'OR', 'XOR' ) ),
				'correct_answer' => 'NAND',
				'explanation'    => 'NAND গেইট হলো AND গেইটের বিপরীত (NOT-AND)। যখন সব ইনপুট ১ হয়, তখন AND গেইটের আউটপুট ১ হয় এবং তা ইনভার্ট হয়ে ০ হয়।',
				'difficulty'     => 'medium',
				'hints'          => wp_json_encode( array( 'এটি একটি সার্বজনীন গেইট।', 'AND গেইটের ফলাফলের উল্টো আউটপুট দেয়।' ) ),
			)
		);

		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l3,
				'activity_id'    => 0,
				'question_type'  => 'fill_blank',
				'prompt'         => 'HTML ডকুমেন্টে টেবিলের রো (Row) তৈরি করতে ____ ট্যাগ ব্যবহার করা হয়।',
				'options'        => '[]',
				'correct_answer' => '<tr>',
				'explanation'    => '<tr> মানে হলো Table Row, যা টেবিলের অনুভূমিক সারি নির্দেশ করে।',
				'difficulty'     => 'foundation',
				'hints'          => wp_json_encode( array( 'Table Row এর সংক্ষিপ্ত রূপ।' ) ),
			)
		);

		// 8. Insert Board Questions into wp_nctb_board_questions
		$bq_table = NCTB_Migrations::table( 'board_questions' );
		$wpdb->insert(
			$bq_table,
			array(
				'board_slug'     => 'dhaka',
				'year'           => 2024,
				'subject_slug'   => 'ict',
				'paper'          => 1,
				'question_type'  => 'mcq',
				'topic_slug'     => 'logic_gates',
				'prompt'         => 'কোন গেইটকে সার্বজনীন গেইট (Universal Gate) বলা হয়?',
				'options'        => wp_json_encode( array( 'AND ও OR', 'NAND ও NOR', 'XOR ও XNOR', 'NOT ও AND' ) ),
				'correct_answer' => 'NAND ও NOR',
				'marks'          => 1,
				'verified'       => 1,
				'explanation'    => 'NAND এবং NOR গেইট দিয়ে যেকোনো মৌলিক বা যৌগিক সার্কিট বাস্তবায়ন করা যায় বলে এদের সার্বজনীন গেইট বলা হয়।',
			)
		);

		// 9. Create ICT Revision Note
		$note_content = '<h2>⚡ লজিক গেইট ও সত্যক সারণী (Truth Table) কমপ্লিট চিটশিট</h2>
<p>এইচএসসি আইসিটি ৩য় অধ্যায়ের ডিজিটাল ডিভাইসের গুরুত্বপূর্ণ সূত্রাবলি:</p>

<table class="nctb-table">
<thead>
<tr>
<th>গেইটের নাম</th>
<th>বুলিয়ান সমীকরণ</th>
<th>বৈশিষ্ট্য</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>AND</strong></td>
<td>\(Y = A \cdot B\)</td>
<td>সবগুলো ইনপুট ১ হলেই আউটপুট ১ হবে।</td>
</tr>
<tr>
<td><strong>OR</strong></td>
<td>\(Y = A + B\)</td>
<td>যেকোনো একটি ইনপুট ১ হলেই আউটপুট ১ হবে।</td>
</tr>
<tr>
<td><strong>NAND</strong></td>
<td>\(Y = \overline{A \cdot B}\)</td>
<td>সার্বজনীন গেইট (AND এর উল্টো)।</td>
</tr>
<tr>
<td><strong>NOR</strong></td>
<td>\(Y = \overline{A + B}\)</td>
<td>সার্বজনীন গেইট (OR এর উল্টো)।</td>
</tr>
<tr>
<td><strong>XOR</strong></td>
<td>\(Y = A \oplus B = \overline{A}B + A\overline{B}\)</td>
<td>বিজোড় সংখ্যক ১ ইনপুটে আউটপুট ১ হয়।</td>
</tr>
</tbody>
</table>';

		$n = wp_insert_post(
			array(
				'post_title'   => 'HSC ICT: Logic Gates & Truth Tables Complete Formula Sheet',
				'post_content' => $note_content,
				'post_excerpt' => 'মৌলিক ও সার্বজনীন লজিক গেইটের সমীকরণ, সত্যক সারণী এবং ডিমরগ্যানের সূত্রের রিভিশন শিট।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_note',
			)
		);
		if ( $n && ! is_wp_error( $n ) ) {
			update_post_meta( $n, '_nctb_note_class', 'class_11' );
			update_post_meta( $n, '_nctb_note_subject', 'ICT' );
			update_post_meta( $n, '_nctb_note_audience', 'both' );
			update_post_meta( $n, '_nctb_note_difficulty', 'medium' );
			update_post_meta( $n, '_nctb_note_lesson_id', $l2 );
			wp_set_object_terms( $n, 'formula_sheet', 'note_type' );
		}

		// 10. Create ICT Video Module
		$mod_items = array(
			array(
				'id'          => 'ict_mod_1',
				'title'       => 'এইচটিএমএল টেবিল ও ফরম্যাটিং প্র্যাকটিক্যাল ল্যাব',
				'youtube_id'  => 'UB1O30fR-EE',
				'duration'    => '22 mins',
				'description' => 'ভিজ্যুয়াল স্টুডিও কোডে HTML টেবিল, Colspan ও Rowspan এর সরাসরি ব্যবহার।',
			),
			array(
				'id'          => 'ict_mod_2',
				'title'       => 'সি প্রোগ্রামিং: লুপ ও কন্ডিশনাল স্টেটমেন্ট হাতে-কলমে',
				'youtube_id'  => 'KJgsSFOSQv0',
				'duration'    => '28 mins',
				'description' => 'CodeBlocks এ সি প্রোগ্রাম রান করে ১ থেকে ১০০ পর্যন্ত জোড় সংখ্যার যোগফল নির্ণয়।',
			),
		);

		$mod = wp_insert_post(
			array(
				'post_title'   => 'HSC ICT: HTML ও সি প্রোগ্রামিং প্র্যাকটিক্যাল ল্যাব মাস্টারক্লাস',
				'post_content' => 'এইচএসসি আইসিটি ৪র্থ ও ৫ম অধ্যায়ের প্র্যাকটিক্যাল কোডিং ও ল্যাব ওয়ার্ক।',
				'post_excerpt' => 'HTML ওয়েব পেজ তৈরি এবং সি প্রোগ্রামিং এর সম্পূর্ণ প্র্যাকটিক্যাল সমাধান।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_module',
			)
		);
		if ( $mod && ! is_wp_error( $mod ) ) {
			update_post_meta( $mod, '_nctb_module_audience', 'student' );
			update_post_meta( $mod, '_nctb_module_class', 'class_11' );
			update_post_meta( $mod, '_nctb_module_duration', '50 mins' );
			update_post_meta( $mod, '_nctb_module_instructor', 'NCTB Tech Lab' );
			update_post_meta( $mod, '_nctb_module_items', wp_json_encode( $mod_items ) );
			wp_set_object_terms( $mod, 'student_masterclass', 'module_category' );
		}
	}
}
