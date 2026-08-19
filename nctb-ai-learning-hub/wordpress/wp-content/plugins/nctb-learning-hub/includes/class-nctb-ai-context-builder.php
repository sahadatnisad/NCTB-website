<?php
/**
 * Context Builder & Prompt Grounder for AI Tutor (Phase 9).
 *
 * Gathers relevant student profile parameters, current lesson text, learning
 * outcomes, vocabulary terms, and mistake attempt context to build compact,
 * curriculum-grounded system prompts with strict pedagogical guardrails.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_AI_Context_Builder
 */
class NCTB_AI_Context_Builder {

	/**
	 * Build the grounded system prompt for a student interaction.
	 *
	 * @param int      $user_id     Student User ID.
	 * @param int      $lesson_id   Lesson Post ID.
	 * @param int|null $question_id Optional question ID for error context.
	 * @param int      $step_num    Current activity step number.
	 * @return string Grounded system prompt.
	 */
	public static function build_system_prompt( $user_id, $lesson_id, $question_id = null, $step_num = 1 ) {
		$user_id     = absint( $user_id );
		$lesson_id   = absint( $lesson_id );
		$question_id = absint( $question_id );

		// 1. Student Profile Context
		$profile  = class_exists( 'NCTB_Student_Profile' ) ? NCTB_Student_Profile::get_profile( $user_id ) : array();
		$level    = $profile['education_level'] ?? 'ssc';
		$level_lbl = NCTB_Student_Profile::ALLOWED_LEVELS[ $level ] ?? 'SSC';
		$lang     = $profile['explanation_language'] ?? 'bangla';
		$lang_lbl = NCTB_Student_Profile::ALLOWED_LANGUAGES[ $lang ] ?? 'Bangla (বাংলায় ব্যাখ্যা)';

		// 2. Curriculum & Lesson Context
		$lesson = get_post( $lesson_id );
		$lesson_title = $lesson ? $lesson->post_title : 'NCTB Lesson';

		$unit_id    = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_lesson_unit( $lesson_id ) : 0;
		$unit_title = $unit_id ? get_the_title( $unit_id ) : '';

		$book_id    = ( class_exists( 'NCTB_Curriculum_CPT' ) && $unit_id ) ? NCTB_Curriculum_CPT::get_unit_book( $unit_id ) : 0;
		$book_title = $book_id ? get_the_title( $book_id ) : '';

		$outcomes   = class_exists( 'NCTB_Curriculum_Data' ) ? NCTB_Curriculum_Data::get_lesson_outcomes( $lesson_id ) : array();
		$outcomes_txt = '';
		foreach ( $outcomes as $o ) {
			$outcomes_txt .= '- ' . $o->outcome_text . "\n";
		}

		$activities = class_exists( 'NCTB_Curriculum_Data' ) ? NCTB_Curriculum_Data::get_lesson_activities( $lesson_id ) : array();
		$current_act_txt = '';
		if ( ! empty( $activities ) ) {
			$act_idx = max( 0, min( count( $activities ) - 1, $step_num - 1 ) );
			$act     = $activities[ $act_idx ];
			$current_act_txt = "Step {$step_num}: {$act->title} ({$act->activity_type})\n" . wp_strip_all_tags( $act->content );
		}

		// 3. Question & Mistake Context (if applicable)
		$mistake_ctx = '';
		if ( $question_id ) {
			$q = class_exists( 'NCTB_Practice_Data' ) ? NCTB_Practice_Data::get_question( $question_id, true ) : null;
			if ( $q ) {
				$mistake_ctx = "\nTARGET PRACTICE QUESTION CONTEXT:\n";
				$mistake_ctx .= "- Question: {$q->prompt}\n";
				if ( ! empty( $q->content ) ) {
					$mistake_ctx .= "- Question Context: {$q->content}\n";
				}
				$mistake_ctx .= "- Correct Answer: {$q->correct_answer}\n";
				$mistake_ctx .= "- Explanation: {$q->explanation}\n";

				// Get student's last attempt
				global $wpdb;
				$att_table = NCTB_Migrations::table( 'attempts' );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$last_att = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT * FROM {$att_table} WHERE user_id = %d AND question_id = %d ORDER BY id DESC LIMIT 1",
						$user_id,
						$question_id
					)
				);
				if ( $last_att && ! $last_att->is_correct ) {
					$mistake_ctx .= "- Student's Last Incorrect Answer: {$last_att->given_answer}\n";
				}
			}
		}

		// 4. Assemble System Prompt
		$prompt = "You are the NCTB AI Learning Hub Tutor, a friendly, encouraging, and pedagogically sound digital teacher tailored strictly for Bangladesh NCTB curriculum students.\n\n";
		$prompt .= "=== CURRENT CURRICULUM CONTEXT ===\n";
		$prompt .= "Book: {$book_title}\n";
		$prompt .= "Unit: {$unit_title}\n";
		$prompt .= "Lesson: {$lesson_title}\n";
		$prompt .= "Student Education Level: {$level_lbl}\n";
		$prompt .= "Student Preferred Language: {$lang_lbl}\n";

		if ( $outcomes_txt ) {
			$prompt .= "\nLearning Outcomes:\n{$outcomes_txt}";
		}

		if ( $current_act_txt ) {
			$prompt .= "\nCurrent Activity:\n{$current_act_txt}\n";
		}

		if ( $mistake_ctx ) {
			$prompt .= "{$mistake_ctx}\n";
		}

		$prompt .= "=== PEDAGOGICAL & SAFETY RULES ===\n";
		$prompt .= "1. Grounding: Stick strictly to the approved NCTB lesson concepts above. Do not introduce outside syllabus trivia.\n";
		$prompt .= "2. Socratic Scaffolding: Never give away direct answers to quizzes or tests. Guide the student's thinking with hints, clues, and analogies.\n";
		$prompt .= "3. Language: If the student prefers Bangla or asks in Bangla, provide clear, warm explanations in natural Bengali with English key terms in bold.\n";
		$prompt .= "4. Formatting: Use concise markdown paragraphs, bold key phrases, and bullet points. Keep replies short and readable on mobile screens.\n";
		$prompt .= "5. Anti-Hallucination: Never fabricate board examination questions. If asked for board questions, refer the student to the official Board Questions section.\n";

		return $prompt;
	}

	/**
	 * Build prompt for Teacher 45-Minute Lesson Planner.
	 *
	 * @param string $class    Class Level (e.g. Class 9, HSC 1st).
	 * @param string $subject  Subject Name.
	 * @param string $topic    Topic or Chapter.
	 * @param int    $duration Duration in minutes (default 45).
	 * @return string Grounded prompt.
	 */
	public static function build_lesson_plan_prompt( $class, $subject, $topic, $duration = 45 ) {
		return "You are an expert Bangladeshi Master Teacher & Pedagogical Consultant specializing in the NCTB National Curriculum.
Generate a structured, highly practical {$duration}-minute classroom lesson plan for:
- Target Class: {$class}
- Subject: {$subject}
- Topic: {$topic}

Format the response in clean, professional Markdown with these exact sections:
# 📋 পাঠ পরিকল্পনা (Lesson Plan): {$topic} ({$class} - {$subject})
- **সময়:** {$duration} মিনিট
- **সাধারণ ও শিখনফল (Learning Outcomes):** (3 measurable bullet points)
- **প্রয়োজনীয় উপকরণ (Teaching Materials):** (Whiteboard, Markers, Flashcards, Handouts)

---
### ১. প্রারম্ভিক ও পূর্বজ্ঞান যাচাই (Warm-up & Hook - 8 মিনিট)
- শিক্ষকের প্রাথমিক প্রশ্ন ও মনোযোগ আকর্ষণের কৌশল।

### ২. মূল ধারণা উপস্থাপন ও পাঠদান (Direct Instruction & Modeling - 15 মিনিট)
- সহজ বাংলায় ব্যাখ্যা, বোর্ডে লেখার জন্য কী-পয়েন্টস, এবং বাস্তবসম্মত উদাহরণ/এনালজি।

### ৩. দলীয় / জোড়ায় অনুশীলন (Group / Pair Activity - 12 মিনিট)
- শ্রেণিকক্ষের শিক্ষার্থীদের সম্পৃক্ত করার জন্য ১টি নির্দিষ্ট সমস্যা সমাধান বা টাস্ক।

### ৪. মূল্যায়ন ও সমাপ্তি (Formative Assessment & Exit Ticket - 10 মিনিট)
- ৩টি কুইক প্রশ্ন এবং ১টি সংক্ষিপ্ত বাড়ির কাজ।

**ভাষা নির্দেশিকা:** সম্পূর্ণ পরিকল্পনাটি বাংলাদেশি শিক্ষকদের জন্য স্পষ্ট ও প্রাঞ্জল বাংলায় উপস্থাপন করুন, সাথে প্রয়োজনীয় ইংরেজি টার্ম বন্ধনীতে রাখুন।";
	}

	/**
	 * Build prompt for Classroom Quiz & Test Generator.
	 *
	 * @param string $class      Class Level.
	 * @param string $subject    Subject Name.
	 * @param string $topic      Topic Name.
	 * @param int    $count      Number of questions.
	 * @param string $difficulty Difficulty level (foundation, medium, advanced).
	 * @return string Grounded prompt.
	 */
	public static function build_quiz_maker_prompt( $class, $subject, $topic, $count = 5, $difficulty = 'medium' ) {
		return "You are a senior NCTB Board Examiner and Item Writer.
Generate a balanced, classroom-ready quiz of {$count} questions for:
- Target Class: {$class}
- Subject: {$subject}
- Topic: {$topic}
- Difficulty: {$difficulty}

Format the response in clean Markdown with 2 separate sections:

# 📝 শ্রেণিকক্ষ মূল্যায়ন পরীক্ষা (Class Test Paper)
- **শ্রেণি:** {$class} | **বিষয়:** {$subject} | **টপিক:** {$topic}
- **পূর্ণমান:** " . ( $count * 2 ) . " | **সময়:** " . ( $count * 3 ) . " মিনিট

(List {$count} authentic, syllabus-accurate questions. Include a mix of Multiple Choice Questions with options A, B, C, D, and Fill in the Blanks / Short Concept questions).

---
# 🔑 উত্তরপত্র ও মূল্যায়ন নির্দেশিকা (Answer Key & Grading Rubric)
(Provide step-by-step correct answers and 1-line explanations for each question so the teacher can quickly grade and explain in class).";
	}

	/**
	 * Build prompt for Student Misconception Diagnosis Tool.
	 *
	 * @param string $class   Class Level.
	 * @param string $subject Subject Name.
	 * @param string $topic   Topic Name.
	 * @return string Grounded prompt.
	 */
	public static function build_misconception_prompt( $class, $subject, $topic ) {
		return "You are a cognitive educational diagnostician specializing in secondary and higher secondary education in Bangladesh.
Analyze student common misconceptions and board exam pitfalls for:
- Class: {$class}
- Subject: {$subject}
- Topic: {$topic}

Provide a structured remedial guide in Markdown:
# 🔍 ভুল ধারণা বিশ্লেষণ ও প্রতিকার নির্দেশিকা (Misconception & Remedial Guide)

### ১. শিক্ষার্থীরা যেখানে সবচেয়ে বেশি ভুল করে (Top 3 Common Pitfalls)
- Identify 3 exact conceptual mistakes students repeatedly make in board exams.

### ২. সঠিক ধারণা বনাম ভুল ধারণার তুলনা (Contrast Table)
- Create a clear table with: **ভুল ধারণা (Wrong Intuition)** | **সঠিক ধারণা (Correct Concept)** | **বোর্ড পরীক্ষার ট্রিকস**

### ৩. ক্লাসরুমে শিক্ষকের প্রতিকারমূলক পদক্ষেপ (Actionable Remedial Strategy)
- 2 practical classroom demonstrations or analogies to permanently clarify this confusion.
- 1 diagnostic practice problem with common trap options.";
	}
}

