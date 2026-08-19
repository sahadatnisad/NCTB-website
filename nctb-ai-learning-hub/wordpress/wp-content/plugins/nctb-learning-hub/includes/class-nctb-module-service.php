<?php
/**
 * Module & Video Course Business Logic Service (Phase 17).
 *
 * Manages video playlist retrieval, item completion states, progress percentage,
 * and default course seeding for students and teachers.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Module_Service
 */
class NCTB_Module_Service {

	/**
	 * Get a module with user progress.
	 *
	 * @param int $module_id Module Post ID.
	 * @param int $user_id   User ID.
	 * @return array<string,mixed>|null
	 */
	public static function get_module( $module_id, $user_id = 0 ) {
		$post = get_post( $module_id );
		if ( ! $post || NCTB_Module_CPT::POST_TYPE !== $post->post_type ) {
			return null;
		}

		$items_raw = get_post_meta( $post->ID, NCTB_Module_CPT::META_ITEMS, true );
		$items     = ! empty( $items_raw ) ? json_decode( $items_raw, true ) : array();
		if ( ! is_array( $items ) ) {
			$items = array();
		}

		$progress = self::get_user_progress( $user_id, $post->ID, count( $items ) );

		return array(
			'id'               => $post->ID,
			'title'            => $post->post_title,
			'description'      => $post->post_content,
			'excerpt'          => $post->post_excerpt,
			'audience'         => get_post_meta( $post->ID, NCTB_Module_CPT::META_AUDIENCE, true ) ?: 'student',
			'class_level'      => get_post_meta( $post->ID, NCTB_Module_CPT::META_CLASS, true ) ?: 'all',
			'subject'          => get_post_meta( $post->ID, NCTB_Module_CPT::META_SUBJECT, true ) ?: 'General',
			'duration'         => get_post_meta( $post->ID, NCTB_Module_CPT::META_DURATION, true ) ?: '1 hour',
			'instructor'       => get_post_meta( $post->ID, NCTB_Module_CPT::META_INSTRUCTOR, true ) ?: '',
			'items'            => $items,
			'total_items'      => count( $items ),
			'progress_percent' => $progress['progress_percent'],
			'completed_items'  => $progress['completed_items'],
			'is_completed'     => $progress['is_completed'],
			'permalink'        => get_permalink( $post->ID ),
		);
	}

	/**
	 * Get user progress for a module.
	 *
	 * @param int $user_id     User ID.
	 * @param int $module_id   Module Post ID.
	 * @param int $total_items Total items count.
	 * @return array<string,mixed>
	 */
	public static function get_user_progress( $user_id, $module_id, $total_items = 0 ) {
		$def = array(
			'completed_items'  => array(),
			'progress_percent' => 0.0,
			'is_completed'     => false,
		);

		$user_id   = absint( $user_id );
		$module_id = absint( $module_id );

		if ( ! $user_id || ! $module_id ) {
			return $def;
		}

		global $wpdb;
		$table = NCTB_Migrations::table( 'module_progress' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT completed_items, progress_percent, is_completed FROM {$table} WHERE user_id = %d AND module_id = %d",
				$user_id,
				$module_id
			),
			ARRAY_A
		);

		if ( ! $row ) {
			return $def;
		}

		$completed = ! empty( $row['completed_items'] ) ? json_decode( $row['completed_items'], true ) : array();
		return array(
			'completed_items'  => is_array( $completed ) ? $completed : array(),
			'progress_percent' => (float) $row['progress_percent'],
			'is_completed'     => (bool) $row['is_completed'],
		);
	}

	/**
	 * Toggle a module item as completed or incomplete.
	 *
	 * @param int    $user_id   User ID.
	 * @param int    $module_id Module ID.
	 * @param string $item_id   Lecture Item ID.
	 * @param bool   $completed State.
	 * @return array<string,mixed> Updated progress.
	 */
	public static function toggle_item( $user_id, $module_id, $item_id, $completed = true ) {
		$user_id   = absint( $user_id );
		$module_id = absint( $module_id );
		$item_id   = sanitize_key( $item_id );

		if ( ! $user_id || ! $module_id || empty( $item_id ) ) {
			return array( 'success' => false );
		}

		$module = self::get_module( $module_id, $user_id );
		if ( ! $module ) {
			return array( 'success' => false );
		}

		$completed_list = $module['completed_items'];
		if ( $completed ) {
			if ( ! in_array( $item_id, $completed_list, true ) ) {
				$completed_list[] = $item_id;
			}
		} else {
			$completed_list = array_values( array_diff( $completed_list, array( $item_id ) ) );
		}

		$total_items = max( 1, $module['total_items'] );
		$pct         = round( ( count( $completed_list ) / $total_items ) * 100, 1 );
		$is_done     = ( $pct >= 100.0 );
		$now         = current_time( 'mysql', true );

		global $wpdb;
		$table = NCTB_Migrations::table( 'module_progress' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->replace(
			$table,
			array(
				'user_id'          => $user_id,
				'module_id'        => $module_id,
				'completed_items'  => wp_json_encode( $completed_list ),
				'progress_percent' => $pct,
				'is_completed'     => $is_done ? 1 : 0,
				'last_activity_at' => $now,
				'updated_at'       => $now,
			),
			array( '%d', '%d', '%s', '%f', '%d', '%s', '%s' )
		);

		return array(
			'success'          => true,
			'module_id'        => $module_id,
			'item_id'          => $item_id,
			'completed_items'  => $completed_list,
			'progress_percent' => $pct,
			'is_completed'     => $is_done,
		);
	}

	/**
	 * Seed sample modules for students and teachers if none exist.
	 *
	 * @return void
	 */
	public static function maybe_seed_modules() {
		$count = wp_count_posts( NCTB_Module_CPT::POST_TYPE );
		if ( ! empty( $count->publish ) && $count->publish > 0 ) {
			return;
		}

		// 1. Student Course: English Grammar Masterclass
		$student_items = array(
			array(
				'id'          => 'lecture_1',
				'title'       => 'Mastering Modifiers & Pre-modifiers for Board Exams',
				'youtube_id'  => 'dQw4w9WgXcQ',
				'duration'    => '14 mins',
				'description' => 'Comprehensive rules and board shortcut techniques for modifiers.',
			),
			array(
				'id'          => 'lecture_2',
				'title'       => 'Right Form of Verbs — 10 Golden Rules',
				'youtube_id'  => 'dQw4w9WgXcQ',
				'duration'    => '18 mins',
				'description' => 'Subject-verb agreement and conditional clauses simplified with examples.',
			),
			array(
				'id'          => 'lecture_3',
				'title'       => 'Transformation of Sentences: Simple, Complex & Compound',
				'youtube_id'  => 'dQw4w9WgXcQ',
				'duration'    => '22 mins',
				'description' => 'Step-by-step transformation techniques without changing sentence meaning.',
			),
		);

		$mod1_id = wp_insert_post(
			array(
				'post_title'   => 'HSC English 2nd Paper: Complete Grammar Masterclass',
				'post_content' => 'এইচএসসি ইংরেজি ২য় পত্রের ব্যাকরণ অংশের গুরুত্বপূর্ণ টপিকগুলোর ধারাবাহিক ভিডিও লেকচার ও প্র্যাকটিস গাইড।',
				'post_excerpt' => 'মডিফায়ার, ভার্ব ও ট্রান্সফরমেশন অব সেন্টেন্সেস এর পূর্ণাঙ্গ ভিডিও সিরিজ।',
				'post_status'  => 'publish',
				'post_type'    => NCTB_Module_CPT::POST_TYPE,
			)
		);

		if ( $mod1_id && ! is_wp_error( $mod1_id ) ) {
			update_post_meta( $mod1_id, NCTB_Module_CPT::META_AUDIENCE, 'student' );
			update_post_meta( $mod1_id, NCTB_Module_CPT::META_CLASS, 'class_11' );
			update_post_meta( $mod1_id, NCTB_Module_CPT::META_SUBJECT, 'English 2nd Paper' );
			update_post_meta( $mod1_id, NCTB_Module_CPT::META_DURATION, '54 mins' );
			update_post_meta( $mod1_id, NCTB_Module_CPT::META_INSTRUCTOR, 'NCTB English Specialist' );
			update_post_meta( $mod1_id, NCTB_Module_CPT::META_ITEMS, wp_json_encode( $student_items, JSON_UNESCAPED_UNICODE ) );
		}

		// 2. Teacher Course: Classroom Pedagogy & Problem-Solving
		$teacher_items = array(
			array(
				'id'          => 'teach_1',
				'title'       => 'Effective 45-Minute Lesson Planning & Student Engagement',
				'youtube_id'  => 'dQw4w9WgXcQ',
				'duration'    => '15 mins',
				'description' => 'How to divide a 45-minute period: Warm-up, Guided practice, and Formative assessment.',
			),
			array(
				'id'          => 'teach_2',
				'title'       => 'Addressing Common Student Misconceptions in English Grammar',
				'youtube_id'  => 'dQw4w9WgXcQ',
				'duration'    => '20 mins',
				'description' => 'Targeted pedagogical strategies to correct recurring learner mistakes.',
			),
		);

		$mod2_id = wp_insert_post(
			array(
				'post_title'   => 'শিক্ষক নির্দেশিকা: শ্রেণিকক্ষে কার্যকর পাঠদান ও শিক্ষার্থী মূল্যায়ন',
				'post_content' => 'জাতীয় শিক্ষাক্রমের আলোকে আধুনিক শ্রেণিকক্ষ পরিচালনা, পাঠ পরিকল্পনা এবং শিক্ষার্থীদের সমস্যা সমাধানের প্রশিক্ষণ মডিউল।',
				'post_excerpt' => 'শিক্ষকদের জন্য ৪৫ মিনিটের পিরিয়ড পরিচালনা ও মূল্যায়ন কৌশল।',
				'post_status'  => 'publish',
				'post_type'    => NCTB_Module_CPT::POST_TYPE,
			)
		);

		if ( $mod2_id && ! is_wp_error( $mod2_id ) ) {
			update_post_meta( $mod2_id, NCTB_Module_CPT::META_AUDIENCE, 'teacher' );
			update_post_meta( $mod2_id, NCTB_Module_CPT::META_CLASS, 'all' );
			update_post_meta( $mod2_id, NCTB_Module_CPT::META_SUBJECT, 'Pedagogy & Teaching Skills' );
			update_post_meta( $mod2_id, NCTB_Module_CPT::META_DURATION, '35 mins' );
			update_post_meta( $mod2_id, NCTB_Module_CPT::META_INSTRUCTOR, 'Master Trainer' );
			update_post_meta( $mod2_id, NCTB_Module_CPT::META_ITEMS, wp_json_encode( $teacher_items, JSON_UNESCAPED_UNICODE ) );
		}
	}
}
