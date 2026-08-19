<?php
/**
 * Listening Activity & Audio Player Service (Phase 10).
 *
 * Handles WordPress-hosted audio playback tracks, transcripts with
 * post-attempt reveal rules, and listening comprehension checks.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Listening_Service
 */
class NCTB_Listening_Service {

	/**
	 * Get audio metadata for a listening activity.
	 *
	 * @param int $activity_id Activity ID.
	 * @return array<string,mixed>
	 */
	public static function get_audio_track( $activity_id ) {
		$activity_id = absint( $activity_id );

		// Sample audio asset for Nelson Mandela lesson
		return array(
			'activity_id'      => $activity_id,
			'title'            => __( 'Listening: Nelson Mandela\'s Historical Address', 'nctb-learning-hub' ),
			'audio_url'        => 'https://upload.wikimedia.org/wikipedia/commons/4/4b/Nelson_Mandela_1994_inauguration_speech_extract.ogg',
			'duration_seconds' => 125,
			'transcript'       => __( "I am here before you not as a prophet, but as a humble servant of you, the people. Your tireless and heroic sacrifices have made it possible for me to be here today. I therefore place the remaining years of my life in your hands.\n\nToday, all of us do, by our presence here, and by our celebrations in other parts of our country and the world, confer glory and hope to newborn liberty.", 'nctb-learning-hub' ),
		);
	}
}
