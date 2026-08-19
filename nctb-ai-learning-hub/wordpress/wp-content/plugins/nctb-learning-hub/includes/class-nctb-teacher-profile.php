<?php
/**
 * Teacher Profile & Educator Management Service (Phase 16).
 *
 * Manages teacher metadata, institution affiliation, subjects/classes taught,
 * teaching goals, and onboarding progression in `wp_nctb_teacher_profiles`.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Teacher_Profile
 */
class NCTB_Teacher_Profile {

	const STATUS_UNVERIFIED = 'unverified';
	const STATUS_PENDING    = 'pending';
	const STATUS_VERIFIED   = 'verified';

	public static $allowed_divisions = array(
		'dhaka'      => 'Dhaka (ঢাকা)',
		'chattogram' => 'Chattogram (চট্টগ্রাম)',
		'rajshahi'   => 'Rajshahi (রাজশাহী)',
		'khulna'     => 'Khulna (খুলনা)',
		'barishal'   => 'Barishal (বরিশাল)',
		'sylhet'     => 'Sylhet (সিলেট)',
		'rangpur'    => 'Rangpur (রংপুর)',
		'mymensingh' => 'Mymensingh (ময়মনসিংহ)',
	);

	public static $allowed_classes = array(
		'class_6' => 'Class 6 (ষষ্ঠ শ্রেণি)',
		'class_7' => 'Class 7 (সপ্তম শ্রেণি)',
		'class_8' => 'Class 8 (অষ্টম শ্রেণি - JSC)',
		'class_9' => 'Class 9 (নবম শ্রেণি)',
		'class_10'=> 'Class 10 (দশম শ্রেণি - SSC)',
		'class_11'=> 'Class 11 (একাদশ শ্রেণি - HSC 1st)',
		'class_12'=> 'Class 12 (দ্বাদশ শ্রেণি - HSC 2nd)',
	);

	public static $allowed_subjects = array(
		'english_1st' => 'English 1st Paper',
		'english_2nd' => 'English 2nd Paper',
		'bangla_1st'  => 'Bangla 1st Paper (বাংলা ১ম)',
		'bangla_2nd'  => 'Bangla 2nd Paper (বাংলা ২য়)',
		'ict'         => 'ICT (তথ্য ও যোগাযোগ প্রযুক্তি)',
		'math'        => 'Mathematics (সাধারণ গণিত)',
		'higher_math' => 'Higher Mathematics (উচ্চতর গণিত)',
		'physics'     => 'Physics (পদার্থবিজ্ঞান)',
		'chemistry'   => 'Chemistry (রসায়ন)',
		'biology'     => 'Biology (জীববিজ্ঞান)',
	);

	public static $allowed_goals = array(
		'lesson_plans'      => 'Classroom Lesson Plans & Pedagogy (পাঠ পরিকল্পনা)',
		'misconceptions'    => 'Understanding Common Student Errors (শিক্ষার্থীদের ভুল নির্ণয়)',
		'ai_question_maker' => 'AI Classroom Test & Quiz Creation (প্রশ্নপত্র প্রণয়ন)',
		'subject_upskilling'=> 'Modern Teaching & ICT Skills (শিক্ষক দক্ষতা বৃদ্ধি)',
	);

	/**
	 * Get a teacher's profile. Creates a default unverified record if not yet existing.
	 *
	 * @param int $user_id User ID.
	 * @return array<string,mixed> Profile dictionary.
	 */
	public static function get_profile( $user_id ) {
		global $wpdb;
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return array();
		}

		$table = NCTB_Migrations::table( 'teacher_profiles' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ), ARRAY_A );

		if ( ! $row ) {
			$user = get_userdata( $user_id );
			$now  = current_time( 'mysql', true );
			$def  = array(
				'user_id'              => $user_id,
				'display_name'         => $user ? $user->display_name : '',
				'school_name'          => '',
				'district'             => '',
				'division'             => 'dhaka',
				'subjects_taught'      => wp_json_encode( array( 'english_1st' ) ),
				'classes_taught'       => wp_json_encode( array( 'class_10', 'class_11' ) ),
				'teaching_goals'       => wp_json_encode( array( 'lesson_plans' ) ),
				'bio'                  => '',
				'verification_status'  => self::STATUS_UNVERIFIED,
				'onboarding_completed' => 0,
				'created_at'           => $now,
				'updated_at'           => $now,
			);

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert( $table, $def );
			$row = $def;
		}

		return array(
			'user_id'              => (int) $row['user_id'],
			'display_name'         => $row['display_name'],
			'school_name'          => $row['school_name'],
			'district'             => $row['district'],
			'division'             => $row['division'],
			'subjects_taught'      => ! empty( $row['subjects_taught'] ) ? json_decode( $row['subjects_taught'], true ) : array(),
			'classes_taught'       => ! empty( $row['classes_taught'] ) ? json_decode( $row['classes_taught'], true ) : array(),
			'teaching_goals'       => ! empty( $row['teaching_goals'] ) ? json_decode( $row['teaching_goals'], true ) : array(),
			'bio'                  => $row['bio'] ?? '',
			'verification_status'  => $row['verification_status'],
			'onboarding_completed' => (bool) $row['onboarding_completed'],
		);
	}

	/**
	 * Save teacher onboarding step data.
	 *
	 * @param int                $user_id User ID.
	 * @param int                $step    Step number (1, 2, 3).
	 * @param array<string,mixed> $params  Step fields.
	 * @return array<string,mixed> Updated profile.
	 */
	public static function save_step( $user_id, $step, array $params ) {
		global $wpdb;
		$user_id = absint( $user_id );
		$table   = NCTB_Migrations::table( 'teacher_profiles' );
		$update  = array( 'updated_at' => current_time( 'mysql', true ) );

		// Ensure profile exists
		self::get_profile( $user_id );

		if ( 1 === $step ) {
			if ( isset( $params['display_name'] ) ) {
				$update['display_name'] = sanitize_text_field( $params['display_name'] );
			}
			if ( isset( $params['school_name'] ) ) {
				$update['school_name'] = sanitize_text_field( $params['school_name'] );
			}
			if ( isset( $params['district'] ) ) {
				$update['district'] = sanitize_text_field( $params['district'] );
			}
			if ( isset( $params['division'] ) ) {
				$update['division'] = sanitize_key( $params['division'] );
			}
		} elseif ( 2 === $step ) {
			if ( isset( $params['subjects_taught'] ) && is_array( $params['subjects_taught'] ) ) {
				$sanitized_sub = array_map( 'sanitize_key', $params['subjects_taught'] );
				$update['subjects_taught'] = wp_json_encode( array_values( $sanitized_sub ) );
			}
			if ( isset( $params['classes_taught'] ) && is_array( $params['classes_taught'] ) ) {
				$sanitized_cls = array_map( 'sanitize_key', $params['classes_taught'] );
				$update['classes_taught'] = wp_json_encode( array_values( $sanitized_cls ) );
			}
		} elseif ( 3 === $step ) {
			if ( isset( $params['teaching_goals'] ) && is_array( $params['teaching_goals'] ) ) {
				$sanitized_goals = array_map( 'sanitize_key', $params['teaching_goals'] );
				$update['teaching_goals'] = wp_json_encode( array_values( $sanitized_goals ) );
			}
			if ( isset( $params['bio'] ) ) {
				$update['bio'] = sanitize_textarea_field( $params['bio'] );
			}
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->update( $table, $update, array( 'user_id' => $user_id ) );

		// Ensure user has nctb_teacher role
		$user = get_userdata( $user_id );
		if ( $user && ! in_array( 'nctb_teacher', (array) $user->roles, true ) ) {
			$user->add_role( 'nctb_teacher' );
		}

		return self::get_profile( $user_id );
	}

	/**
	 * Mark teacher onboarding complete.
	 *
	 * @param int $user_id User ID.
	 * @return array<string,mixed> Completed profile.
	 */
	public static function complete_onboarding( $user_id ) {
		global $wpdb;
		$user_id = absint( $user_id );
		$table   = NCTB_Migrations::table( 'teacher_profiles' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->update(
			$table,
			array(
				'onboarding_completed' => 1,
				'verification_status'  => self::STATUS_PENDING,
				'updated_at'           => current_time( 'mysql', true ),
			),
			array( 'user_id' => $user_id )
		);

		return self::get_profile( $user_id );
	}
}
