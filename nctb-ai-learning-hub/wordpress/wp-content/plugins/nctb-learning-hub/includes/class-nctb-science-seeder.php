<?php
/**
 * Science Subjects Curriculum Seeder (Phase 22).
 *
 * Ingests authentic SSC Physics, Chemistry, and Biology curriculum data,
 * interactive lessons, KaTeX science formulas, practice items, board questions,
 * revision notes, and video modules.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Science_Seeder
 */
class NCTB_Science_Seeder {

	/**
	 * Seed Science curriculum if not already present.
	 *
	 * @return void
	 */
	public static function maybe_seed_science() {
		$existing = get_page_by_title( 'SSC Physics (পদার্থবিজ্ঞান)', OBJECT, 'nctb_book' );
		if ( $existing ) {
			return;
		}

		global $wpdb;
		$act_table = NCTB_Migrations::table( 'lesson_activities' );
		$q_table   = NCTB_Migrations::table( 'questions' );
		$bq_table  = NCTB_Migrations::table( 'board_questions' );

		/* ==========================================================================
		   1. PHYSICS (পদার্থবিজ্ঞান)
		   ========================================================================== */
		$b_phys = wp_insert_post(
			array(
				'post_title'   => 'SSC Physics (পদার্থবিজ্ঞান)',
				'post_content' => 'নবম-দশম শ্রেণির পদার্থবিজ্ঞান পাঠ্যক্রম। গতি, বল, কাজ ক্ষমতা ও শক্তি, আলো ও বিদ্যুৎ।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_book',
			)
		);
		update_post_meta( $b_phys, '_nctb_class', 'class_10' );
		update_post_meta( $b_phys, '_nctb_subject', 'Physics' );

		$u_phys = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ২: গতি (Motion)',
				'post_content' => 'দূরত্ব, সরণ, দ্রুতি, বেগ, ত্বরণ এবং গতির সমীকরণ ও গ্রাফ।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u_phys, '_nctb_book_id', $b_phys );

		$l_phys = wp_insert_post(
			array(
				'post_title'   => '২.১ গতির সমীকরণ ও ত্বরণ (Equations of Motion)',
				'post_content' => '$v = u + at$, $s = ut + \frac{1}{2}at^2$, $v^2 = u^2 + 2as$ সমীকরণ এবং গাণিতিক সমস্যা।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 0,
			)
		);
		update_post_meta( $l_phys, '_nctb_unit_id', $u_phys );
		update_post_meta( $l_phys, '_nctb_is_free', 1 );

		// Activity
		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l_phys,
				'step_number'   => 1,
				'title'         => 'গতির মৌলিক ৪টি সমীকরণ',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>সুষম ত্বরণে চলমান কোনো বস্তুর গতির সমীকরণসমূহ:</p>
<ul>
<li><strong>১.</strong> \(v = u + at\)</li>
<li><strong>২.</strong> \(s = \left(\frac{u + v}{2}\right)t\)</li>
<li><strong>৩.</strong> \(s = ut + \frac{1}{2}at^2\)</li>
<li><strong>৪.</strong> \(v^2 = u^2 + 2as\)</li>
</ul>
<p><em>এখানে:</em> \(u =\) আদিবেগ (\(\text{ms}^{-1}\)), \(v =\) শেষবেগ, \(a =\) ত্বরণ (\(\text{ms}^{-2}\)), \(t =\) সময় (\(\text{s}\)), \(s =\) অতিক্রান্ত দূরত্ব (\(\text{m}\))।</p>',
			)
		);

		// Question
		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l_phys,
				'activity_id'    => 0,
				'question_type'  => 'math_numeric',
				'prompt'         => 'একটি গাড়ি স্থির অবস্থান (\(u = 0\)) থেকে \(2\text{ ms}^{-2}\) সুষম ত্বরণে চলা শুরু করলে \(5\text{ s}\) পর এর শেষবেগ কত হবে?',
				'options'        => '[]',
				'correct_answer' => '10 | ১০ | 10 ms-1',
				'explanation'    => '\(v = u + at = 0 + (2 \times 5) = 10\text{ ms}^{-1}\)।',
				'difficulty'     => 'easy',
				'hints'          => wp_json_encode( array( '\(v = u + at\) সূত্র ব্যবহার করুন।' ) ),
			)
		);

		// Board Question
		$wpdb->insert(
			$bq_table,
			array(
				'board_slug'     => 'dhaka',
				'year'           => 2024,
				'subject_slug'   => 'physics',
				'paper'          => 1,
				'question_type'  => 'mcq',
				'topic_slug'     => 'motion',
				'prompt'         => 'মুক্তভাবে পড়ন্ত বস্তুর ক্ষেত্রে ৩ সেকেন্ড পর বেগ কত হবে? (\(g = 9.8\text{ ms}^{-2}\))',
				'options'        => wp_json_encode( array( '29.4 ms⁻¹', '19.6 ms⁻¹', '9.8 ms⁻¹', '44.1 ms⁻¹' ) ),
				'correct_answer' => '29.4 ms⁻¹',
				'marks'          => 1,
				'verified'       => 1,
				'explanation'    => '\(v = gt = 9.8 \times 3 = 29.4\text{ ms}^{-1}\)।',
			)
		);

		// Note & Module
		$n_phys = wp_insert_post(
			array(
				'post_title'   => 'SSC Physics: Motion & Dynamics Complete Formula Sheet',
				'post_content' => '<h2>⚡ পদার্থবিজ্ঞান: গতি ও বল অধ্যায়ের সকল সূত্র</h2>
<ul>
<li>\(v = u + at\)</li>
<li>\(s = ut + \frac{1}{2}at^2\)</li>
<li>\(v^2 = u^2 + 2as\)</li>
<li>\(F = ma\) (নিউটনের ২য় সূত্র)</li>
<li>\(P = mv\) (ভরবেগ)</li>
<li>\(E_k = \frac{1}{2}mv^2\) (গতিশক্তি)</li>
<li>\(E_p = mgh\) (বিভবশক্তি)</li>
</ul>',
				'post_excerpt' => 'গতি, ত্বরণ, বল, কাজ ও ক্ষমতার সকল সমীকরণ ও মাত্রার কমপ্লিট রিভিশন শিট।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_note',
			)
		);
		update_post_meta( $n_phys, '_nctb_note_class', 'class_10' );
		update_post_meta( $n_phys, '_nctb_note_subject', 'Physics' );
		update_post_meta( $n_phys, '_nctb_note_audience', 'both' );
		update_post_meta( $n_phys, '_nctb_note_difficulty', 'medium' );
		update_post_meta( $n_phys, '_nctb_note_lesson_id', $l_phys );
		wp_set_object_terms( $n_phys, 'formula_sheet', 'note_type' );

		/* ==========================================================================
		   2. CHEMISTRY (রসায়ন)
		   ========================================================================== */
		$b_chem = wp_insert_post(
			array(
				'post_title'   => 'SSC Chemistry (রসায়ন)',
				'post_content' => 'পদার্থের গঠন, পর্যায় সারণি, রাসায়নিক বন্ধন, মোলের ধারণা ও রাসায়নিক গণনা।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_book',
			)
		);
		update_post_meta( $b_chem, '_nctb_class', 'class_10' );
		update_post_meta( $b_chem, '_nctb_subject', 'Chemistry' );

		$u_chem = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ৪: পর্যায় সারণি (Periodic Table)',
				'post_content' => 'পর্যায় সারণির বৈশিষ্ট্য, ইলেকট্রন বিন্যাস হতে পর্যায় ও গ্রুপ নির্ণয়, পর্যায়বৃত্ত ধর্ম।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u_chem, '_nctb_book_id', $b_chem );

		$l_chem = wp_insert_post(
			array(
				'post_title'   => '৪.১ পর্যায় সারণির বৈশিষ্ট্য ও গ্রুপ নির্ণয় (Periodic Table Groups)',
				'post_content' => 'মৌলের পরমাণুর ইলেকট্রন বিন্যাস থেকে পর্যায় ও গ্রুপ সংখ্যা নির্ণয়ের নিয়ম।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 0,
			)
		);
		update_post_meta( $l_chem, '_nctb_unit_id', $u_chem );
		update_post_meta( $l_chem, '_nctb_is_free', 1 );

		// Activity
		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l_chem,
				'step_number'   => 1,
				'title'         => 'ইলেকট্রন বিন্যাস হতে গ্রুপ নির্ণয়ের নিয়ম',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>পর্যায় সারণিতে কোনো মৌলের গ্রুপ নির্ণয়ের ৩টি মূল নিয়ম:</p>
<ul>
<li><strong>নিয়ম ১:</strong> যোজ্যতা স্তরে কেবল <code>s</code> অরবিটাল থাকলে, s-এর মোট ইলেকট্রন সংখ্যাই গ্রুপ সংখ্যা (উদাঃ \(\text{Na}: 1s^2 2s^2 2p^6 3s^1 \rightarrow\) Group 1)।</li>
<li><strong>নিয়ম ২:</strong> যোজ্যতা স্তরে <code>s</code> ও <code>p</code> অরবিটাল থাকলে, ইলেকট্রন সংখ্যার সাথে ১০ যোগ করতে হয় (উদাঃ \(\text{Cl}: 3s^2 3p^5 \rightarrow 2 + 5 + 10 =\) Group 17)।</li>
<li><strong>নিয়ম ৩:</strong> যোজ্যতা স্তরে <code>s</code> এবং পূর্বের স্তরে <code>d</code> অরবিটাল থাকলে, \((s + d)\)-এর মোট ইলেকট্রনই গ্রুপ সংখ্যা।</li>
</ul>',
			)
		);

		// Question
		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l_chem,
				'activity_id'    => 0,
				'question_type'  => 'mcq',
				'prompt'         => 'সোডিয়াম (\(\text{Na}_{11}\)) মৌলটি পর্যায় সারণির কোন গ্রুপে অবস্থিত?',
				'options'        => wp_json_encode( array( 'Group 1', 'Group 2', 'Group 11', 'Group 17' ) ),
				'correct_answer' => 'Group 1',
				'explanation'    => 'সোডিয়ামের ইলেকট্রন বিন্যাস \(1s^2 2s^2 2p^6 3s^1\)। সর্ববহিঃস্থ ৩য় শক্তিস্তরের s অরবিটালে ১টি ইলেকট্রন থাকায় এটি গ্রুপ ১ (ক্ষার ধাতু)।',
				'difficulty'     => 'easy',
				'hints'          => wp_json_encode( array( 'সোডিয়াম একটি ক্ষার ধাতু।' ) ),
			)
		);

		// Note
		$n_chem = wp_insert_post(
			array(
				'post_title'   => 'SSC Chemistry: Periodic Table Trends & Properties Cheat Sheet',
				'post_content' => '<h2>🧪 রসায়ন: পর্যায়বৃত্ত ধর্ম ও গ্রুপ নির্ণয়ের নিয়মাবলি</h2>
<table class="nctb-table">
<thead><tr><th>পর্যায়বৃত্ত ধর্ম</th><th>একই পর্যায়ে (বাম থেকে ডানে)</th><th>একই গ্রুপে (উপর থেকে নিচে)</th></tr></thead>
<tbody>
<tr><td><strong>পারমাণবিক আকার</strong></td><td>হ্রাস পায়</td><td>বৃদ্ধি পায়</td></tr>
<tr><td><strong>আয়নীকরণ শক্তি</strong></td><td>বৃদ্ধি পায়</td><td>হ্রাস পায়</td></tr>
<tr><td><strong>তড়িৎ ঋণাত্মকতা</strong></td><td>বৃদ্ধি পায়</td><td>হ্রাস পায়</td></tr>
<tr><td><strong>ধাতব ধর্ম</strong></td><td>হ্রাস পায়</td><td>বৃদ্ধি পায়</td></tr>
</tbody>
</table>',
				'post_excerpt' => 'আয়নীকরণ শক্তি, তড়িৎ ঋণাত্মকতা ও পরমাণুর আকারের পরিবর্তনের পূর্ণাঙ্গ চার্ট।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_note',
			)
		);
		update_post_meta( $n_chem, '_nctb_note_class', 'class_10' );
		update_post_meta( $n_chem, '_nctb_note_subject', 'Chemistry' );
		update_post_meta( $n_chem, '_nctb_note_audience', 'both' );
		update_post_meta( $n_chem, '_nctb_note_difficulty', 'medium' );
		update_post_meta( $n_chem, '_nctb_note_lesson_id', $l_chem );
		wp_set_object_terms( $n_chem, 'summary', 'note_type' );

		/* ==========================================================================
		   3. BIOLOGY (জীববিজ্ঞান)
		   ========================================================================== */
		$b_bio = wp_insert_post(
			array(
				'post_title'   => 'SSC Biology (জীববিজ্ঞান)',
				'post_content' => 'জীবকোষ ও টিস্যু, কোষ বিভাজন, জীবনীশক্তি, উদ্ভিদের প্রস্বেদন ও প্রাণীর পরিবহন।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_book',
			)
		);
		update_post_meta( $b_bio, '_nctb_class', 'class_10' );
		update_post_meta( $b_bio, '_nctb_subject', 'Biology' );

		$u_bio = wp_insert_post(
			array(
				'post_title'   => 'অধ্যায় ২: জীবকোষ ও টিস্যু (Cell & Tissue)',
				'post_content' => 'উদ্ভিদ ও প্রাণিকোষের অঙ্গাণুসমূহ, মাইটোকন্ড্রিয়া, প্লাস্টিড ও কোষের গঠন।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_unit',
			)
		);
		update_post_meta( $u_bio, '_nctb_book_id', $b_bio );

		$l_bio = wp_insert_post(
			array(
				'post_title'   => '২.১ কোষের সাইটোপ্লাজমীয় অঙ্গাণু (Cell Organelles: Mitochondria & Plastid)',
				'post_content' => 'মাইটোকন্ড্রিয়া (Powerhouse of Cell) ও প্লাস্টিডের গঠন এবং জৈবিক ভূমিকা।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_lesson',
				'menu_order'   => 0,
			)
		);
		update_post_meta( $l_bio, '_nctb_unit_id', $u_bio );
		update_post_meta( $l_bio, '_nctb_is_free', 1 );

		// Activity
		$wpdb->insert(
			$act_table,
			array(
				'lesson_id'     => $l_bio,
				'step_number'   => 1,
				'title'         => 'মাইটোকন্ড্রিয়া ও প্লাস্টিডের প্রধান কাজ',
				'activity_type' => 'concept_explainer',
				'content'       => '<p>কোষের দুটি অত্যন্ত গুরুত্বপূর্ণ সাইটোপ্লাজমীয় অঙ্গাণু:</p>
<ul>
<li><strong>মাইটোকন্ড্রিয়া (Mitochondria):</strong> কোষের শক্তি উৎপাদনের কেন্দ্র (Powerhouse)। ক্রেবস চক্রের বিক্রিয়াগুলো এখানেই সম্পন্ন হয় এবং এটি ATP তৈরি করে।</li>
<li><strong>প্লাস্টিড (Plastid):</strong> উদ্ভিদকোষের প্রধান অঙ্গাণু। ক্লোরোপ্লাস্ট (সবুজ রঞ্জক - সালোকসংশ্লেষণ), ক্রোমোপ্লাস্ট (রঙিন - ফুল ও ফলের পরাগায়নে সহায়তা), লিউকোপ্লাস্ট (বর্ণহীন - খাদ্য সঞ্চয়)।</li>
</ul>',
			)
		);

		// Question
		$wpdb->insert(
			$q_table,
			array(
				'lesson_id'      => $l_bio,
				'activity_id'    => 0,
				'question_type'  => 'fill_blank',
				'prompt'         => 'কোষের শক্তি উৎপাদন কেন্দ্র বা পাওয়ার হাউস বলা হয় ____ কে।',
				'options'        => '[]',
				'correct_answer' => 'মাইটোকন্ড্রিয়া | Mitochondria',
				'explanation'    => 'মাইটোকন্ড্রিয়ায় শ্বসনের প্রধান ধাপগুলো সম্পন্ন হয়ে বিপুল পরিমাণ শক্তি (ATP) উৎপন্ন হয় বলে একে পাওয়ার হাউস বলা হয়।',
				'difficulty'     => 'foundation',
				'hints'          => wp_json_encode( array( 'M দিয়ে শুরু ইংরেজি অঙ্গাণুর নাম।' ) ),
			)
		);

		// Note
		$n_bio = wp_insert_post(
			array(
				'post_title'   => 'SSC Biology: Cell Organelles & Functions Summary Guide',
				'post_content' => '<h2>🌿 জীববিজ্ঞান: কোষীয় অঙ্গাণু ও তাদের প্রধান কাজ</h2>
<ul>
<li><strong>নিউক্লিয়াস:</strong> কোষের প্রাণকেন্দ্র, বংশগতির উপাদান DNA ধারণ করে।</li>
<li><strong>মাইটোকন্ড্রিয়া:</strong> শক্তি উৎপাদন ও ATP সংশ্লেষণ।</li>
<li><strong>ক্লোরোপ্লাস্ট:</strong> সালোকসংশ্লেষণ প্রক্রিয়ায় খাদ্য তৈরি।</li>
<li><strong>রাইবোসোম:</strong> প্রোটিন সংশ্লেষণ কারখানা।</li>
<li><strong>লাইসোজোম:</strong> জীবাণু ধ্বংস ও ফ্যাগোসাইটোসিস।</li>
<li><strong>গলগি বস্তু:</strong> এনজাইম ও হরমোন ক্ষরণ ও প্যাকেজিং।</li>
</ul>',
				'post_excerpt' => 'কোষের সকল সাইটোপ্লাজমীয় অঙ্গাণু, গঠন ও কাজের সহজ বুলেট পয়েন্ট নোট।',
				'post_status'  => 'publish',
				'post_type'    => 'nctb_note',
			)
		);
		update_post_meta( $n_bio, '_nctb_note_class', 'class_10' );
		update_post_meta( $n_bio, '_nctb_note_subject', 'Biology' );
		update_post_meta( $n_bio, '_nctb_note_audience', 'both' );
		update_post_meta( $n_bio, '_nctb_note_difficulty', 'foundation' );
		update_post_meta( $n_bio, '_nctb_note_lesson_id', $l_bio );
		wp_set_object_terms( $n_bio, 'summary', 'note_type' );
	}
}
