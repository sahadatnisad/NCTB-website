<?php
/**
 * Student profile data management.
 *
 * Encapsulates reading, validating, and updating student profile fields
 * stored in WordPress user metadata.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Student_Profile
 */
class NCTB_Student_Profile {

	/**
	 * Meta key prefix.
	 */
	const META_PREFIX = '_nctb_';

	/**
	 * Allowed education levels.
	 */
	const ALLOWED_LEVELS = array(
		'class_6'  => 'Class 6 (ষষ্ঠ শ্রেণি)',
		'class_7'  => 'Class 7 (সপ্তম শ্রেণি)',
		'class_8'  => 'Class 8 (অষ্টম শ্রেণি)',
		'ssc'      => 'SSC / Class 9–10 (নবম-দশম শ্রেণি)',
		'hsc'      => 'HSC / Class 11–12 (একাদশ-দ্বাদশ শ্রেণি)',
	);

	/**
	 * Allowed explanation languages.
	 */
	const ALLOWED_LANGUAGES = array(
		'bn'        => 'বাংলায় ব্যাখ্যা (Bangla)',
		'bilingual' => 'দ্বিভাষিক / সহজ ইংরেজি ও বাংলা (Bilingual)',
		'en'        => 'English Only',
	);

	/**
	 * Allowed subjects catalog.
	 */
	const ALLOWED_SUBJECTS = array(
		'english_1st' => array(
			'title_en' => 'English 1st Paper',
			'title_bn' => 'ইংরেজি ১ম পত্র',
			'icon'     => 'book-open',
		),
		'english_2nd' => array(
			'title_en' => 'English 2nd Paper (Grammar & Writing)',
			'title_bn' => 'ইংরেজি ২য় পত্র (গ্রামার ও রাইটিং)',
			'icon'     => 'edit-3',
		),
		'ict'         => array(
			'title_en' => 'Information & Communication Technology (ICT)',
			'title_bn' => 'তথ্য ও যোগাযোগ প্রযুক্তি (আইসিটি)',
			'icon'     => 'cpu',
		),
		'bangla_1st'  => array(
			'title_en' => 'Bangla 1st Paper',
			'title_bn' => 'বাংলা ১ম পত্র (সাহিত্য)',
			'icon'     => 'feather',
		),
		'bangla_2nd'  => array(
			'title_en' => 'Bangla 2nd Paper',
			'title_bn' => 'বাংলা ২য় পত্র (ব্যাকরণ ও নির্মিতি)',
			'icon'     => 'file-text',
		),
	);

	/**
	 * Get the complete profile data for a student.
	 *
	 * @param int $user_id WordPress User ID.
	 * @return array
	 */
	public static function get_profile( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return array();
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return array();
		}

		$level          = get_user_meta( $user_id, self::META_PREFIX . 'education_level', true );
		$language       = get_user_meta( $user_id, self::META_PREFIX . 'explanation_language', true );
		$session        = get_user_meta( $user_id, self::META_PREFIX . 'class_session', true );
		$target_exam    = get_user_meta( $user_id, self::META_PREFIX . 'target_exam_session', true );
		$chosen_raw     = get_user_meta( $user_id, self::META_PREFIX . 'chosen_subjects', true );
		$step           = get_user_meta( $user_id, self::META_PREFIX . 'onboarding_step', true );
		$is_complete    = get_user_meta( $user_id, self::META_PREFIX . 'onboarding_complete', true );
		$updated_at     = get_user_meta( $user_id, self::META_PREFIX . 'profile_updated_at', true );

		$chosen_subjects = is_array( $chosen_raw ) ? $chosen_raw : array();

		return array(
			'user_id'              => $user_id,
			'display_name'         => $user->display_name,
			'email'                => $user->user_email,
			'education_level'      => $level ? sanitize_key( $level ) : '',
			'explanation_language' => $language ? sanitize_key( $language ) : 'bilingual',
			'class_session'        => $session ? sanitize_text_field( $session ) : date( 'Y' ),
			'target_exam_session'  => $target_exam ? sanitize_text_field( $target_exam ) : '',
			'chosen_subjects'      => array_values( array_intersect( $chosen_subjects, array_keys( self::ALLOWED_SUBJECTS ) ) ),
			'onboarding_step'      => $step ? absint( $step ) : 1,
			'onboarding_complete'  => (bool) $is_complete,
			'profile_updated_at'   => $updated_at ? absint( $updated_at ) : 0,
		);
	}

	/**
	 * Save onboarding step data safely for a student.
	 *
	 * @param int   $user_id User ID.
	 * @param int   $step    Step number (1-4).
	 * @param array $data    Submitted form data.
	 * @return array|WP_Error Updated profile array or WP_Error on validation failure.
	 */
	public static function save_step( $user_id, $step, array $data ) {
		$user_id = absint( $user_id );
		$step    = absint( $step );

		if ( ! $user_id || ! get_userdata( $user_id ) ) {
			return new WP_Error( 'invalid_user', __( 'Invalid user ID.', 'nctb-learning-hub' ), array( 'status' => 400 ) );
		}

		switch ( $step ) {
			case 1:
				// Step 1: Education level and class session.
				if ( empty( $data['education_level'] ) || ! array_key_exists( $data['education_level'], self::ALLOWED_LEVELS ) ) {
					return new WP_Error( 'invalid_level', __( 'Please select a valid education level.', 'nctb-learning-hub' ), array( 'status' => 422 ) );
				}
				update_user_meta( $user_id, self::META_PREFIX . 'education_level', sanitize_key( $data['education_level'] ) );

				if ( ! empty( $data['class_session'] ) ) {
					update_user_meta( $user_id, self::META_PREFIX . 'class_session', sanitize_text_field( $data['class_session'] ) );
				}
				update_user_meta( $user_id, self::META_PREFIX . 'onboarding_step', 2 );
				break;

			case 2:
				// Step 2: Chosen subjects.
				$subjects = isset( $data['chosen_subjects'] ) && is_array( $data['chosen_subjects'] ) ? $data['chosen_subjects'] : array();
				$sanitized_subjects = array();
				foreach ( $subjects as $sub ) {
					$clean_sub = sanitize_key( $sub );
					if ( array_key_exists( $clean_sub, self::ALLOWED_SUBJECTS ) ) {
						$sanitized_subjects[] = $clean_sub;
					}
				}
				if ( empty( $sanitized_subjects ) ) {
					return new WP_Error( 'missing_subjects', __( 'Please select at least one subject.', 'nctb-learning-hub' ), array( 'status' => 422 ) );
				}
				update_user_meta( $user_id, self::META_PREFIX . 'chosen_subjects', array_values( array_unique( $sanitized_subjects ) ) );
				update_user_meta( $user_id, self::META_PREFIX . 'onboarding_step', 3 );
				break;

			case 3:
				// Step 3: Explanation language preference.
				$lang = ! empty( $data['explanation_language'] ) ? sanitize_key( $data['explanation_language'] ) : 'bilingual';
				if ( ! array_key_exists( $lang, self::ALLOWED_LANGUAGES ) ) {
					$lang = 'bilingual';
				}
				update_user_meta( $user_id, self::META_PREFIX . 'explanation_language', $lang );
				update_user_meta( $user_id, self::META_PREFIX . 'onboarding_step', 4 );
				break;

			case 4:
				// Step 4: Target exam / goal session.
				if ( ! empty( $data['target_exam_session'] ) ) {
					update_user_meta( $user_id, self::META_PREFIX . 'target_exam_session', sanitize_text_field( $data['target_exam_session'] ) );
				}
				// Mark onboarding complete.
				update_user_meta( $user_id, self::META_PREFIX . 'onboarding_complete', 1 );
				update_user_meta( $user_id, self::META_PREFIX . 'onboarding_step', 4 );
				break;

			default:
				return new WP_Error( 'invalid_step', __( 'Invalid onboarding step.', 'nctb-learning-hub' ), array( 'status' => 400 ) );
		}

		update_user_meta( $user_id, self::META_PREFIX . 'profile_updated_at', time() );

		return self::get_profile( $user_id );
	}

	/**
	 * Mark onboarding complete directly.
	 *
	 * @param int $user_id User ID.
	 * @return array
	 */
	public static function complete_onboarding( $user_id ) {
		$user_id = absint( $user_id );
		update_user_meta( $user_id, self::META_PREFIX . 'onboarding_complete', 1 );
		update_user_meta( $user_id, self::META_PREFIX . 'profile_updated_at', time() );
		return self::get_profile( $user_id );
	}

	/**
	 * Check if a student has completed onboarding.
	 *
	 * @param int $user_id User ID.
	 * @return bool
	 */
	public static function is_onboarding_complete( $user_id ) {
		return (bool) get_user_meta( absint( $user_id ), self::META_PREFIX . 'onboarding_complete', true );
	}
}
