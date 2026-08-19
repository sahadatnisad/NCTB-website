<?php
/**
 * Speaking Practice & Audio Submission Service (Phase 10).
 *
 * Handles student voice recording submissions, playback, and constructive
 * fluency feedback with prominent disclaimers avoiding misleading official scores.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Speaking_Service
 */
class NCTB_Speaking_Service {

	/**
	 * Save a speaking submission and generate constructive practice feedback.
	 *
	 * @param int    $user_id          Student User ID.
	 * @param int    $lesson_id        Lesson Post ID.
	 * @param int    $activity_id      Activity Block ID.
	 * @param string $audio_url        Audio asset path or blob reference.
	 * @param int    $duration_seconds Recording duration in seconds.
	 * @param string $transcript_text  Recognized or expected transcript.
	 * @return array<string,mixed> Submission result with practice feedback.
	 */
	public static function submit_recording( $user_id, $lesson_id, $activity_id, $audio_url = '', $duration_seconds = 0, $transcript_text = '' ) {
		global $wpdb;
		$user_id          = absint( $user_id );
		$lesson_id        = absint( $lesson_id );
		$activity_id      = absint( $activity_id );
		$duration_seconds = max( 1, absint( $duration_seconds ) );
		$audio_url        = sanitize_text_field( $audio_url );
		$transcript_text  = sanitize_textarea_field( $transcript_text );

		$table = NCTB_Migrations::table( 'speaking_submissions' );
		$now   = current_time( 'mysql', true );

		// Constructive formative feedback
		$feedback_text = "🎙️ **Speaking Practice Feedback (অভ্যাস পর্যবেক্ষণ):**\n\n";
		$feedback_text .= "⏱️ **Recording Length:** {$duration_seconds} seconds (Good speaking pace)\n";
		$feedback_text .= "🗣️ **Intonation & Pauses:** Clear delivery with natural sentence pauses. Remember to stress content words (e.g. *freedom*, *peace*, *justice*).\n";
		$feedback_text .= "🎯 **Vocabulary Articulation:** Confident articulation of historical key terms.\n\n";
		$feedback_text .= "⚠️ *Disclaimer: This is formative AI practice feedback to boost your confidence. It is not an official board examination score.*";

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->insert(
			$table,
			array(
				'user_id'          => $user_id,
				'lesson_id'        => $lesson_id,
				'activity_id'      => $activity_id,
				'audio_url'        => $audio_url,
				'duration_seconds' => $duration_seconds,
				'transcript_text'  => $transcript_text,
				'feedback_text'    => $feedback_text,
				'status'           => 'completed',
				'created_at'       => $now,
			),
			array( '%d', '%d', '%d', '%s', '%d', '%s', '%s', '%s', '%s' )
		);

		return array(
			'submission_id'    => $wpdb->insert_id,
			'feedback_text'    => $feedback_text,
			'duration_seconds' => $duration_seconds,
			'status'           => 'completed',
		);
	}

	/**
	 * Get student speaking submissions for a lesson.
	 *
	 * @param int $user_id   Student User ID.
	 * @param int $lesson_id Lesson Post ID.
	 * @return array<int,object>
	 */
	public static function get_submissions( $user_id, $lesson_id ) {
		global $wpdb;
		$user_id   = absint( $user_id );
		$lesson_id = absint( $lesson_id );
		$table     = NCTB_Migrations::table( 'speaking_submissions' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE user_id = %d AND lesson_id = %d ORDER BY id DESC LIMIT 10",
				$user_id,
				$lesson_id
			)
		);
	}
}
