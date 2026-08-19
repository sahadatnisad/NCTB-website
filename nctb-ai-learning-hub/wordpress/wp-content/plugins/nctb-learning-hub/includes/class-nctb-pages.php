<?php
/**
 * Required-page provisioner.
 *
 * The onboarding and dashboard flows depend on WordPress pages existing at
 * fixed slugs (the redirect logic keys off is_page('onboarding') /
 * is_page('dashboard')). Historically those pages were created by hand in the
 * dev database, so a fresh clone/install would not have them and the flow
 * would break. This provisioner recreates them idempotently on activation so
 * the site is reproducible from the repository alone.
 *
 * It never duplicates an existing page (matches by slug) and never touches
 * pages a site owner created.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Pages
 */
class NCTB_Pages {

	const PROVISIONED_OPTION = 'nctb_lh_pages_provisioned';

	/**
	 * Required pages: slug => definition.
	 *
	 * @return array<string,array<string,string>>
	 */
	protected static function definitions() {
		return array(
			'onboarding' => array(
				'title'     => __( 'Student Onboarding', 'nctb-learning-hub' ),
				'content'   => '[nctb_onboarding]',
				'template'  => 'page-onboarding.php',
			),
			'dashboard'  => array(
				'title'     => __( 'Dashboard', 'nctb-learning-hub' ),
				'content'   => '[nctb_student_dashboard]',
				'template'  => 'page-dashboard.php',
			),
			'mistakes'   => array(
				'title'     => __( 'My Mistakes', 'nctb-learning-hub' ),
				'content'   => '[nctb_mistakes]',
				'template'  => 'page-mistakes.php',
			),
			'revision'   => array(
				'title'     => __( 'Revision Due', 'nctb-learning-hub' ),
				'content'   => '[nctb_revision_due]',
				'template'  => 'page-revision.php',
			),
			'progress'   => array(
				'title'     => __( 'Learning Progress', 'nctb-learning-hub' ),
				'content'   => '[nctb_progress]',
				'template'  => 'page-progress.php',
			),
			'purchases'       => array(
				'title'     => __( 'My Purchases & Passes', 'nctb-learning-hub' ),
				'content'   => '[nctb_my_purchases]',
				'template'  => 'page-purchases.php',
			),
			'board-questions' => array(
				'title'     => __( 'Board Questions Bank', 'nctb-learning-hub' ),
				'content'   => '[nctb_board_questions]',
				'template'  => 'page-board-questions.php',
			),
			'board-analytics' => array(
				'title'     => __( 'Board Pattern Analytics', 'nctb-learning-hub' ),
				'content'   => '[nctb_board_analytics]',
				'template'  => 'page-board-analytics.php',
			),
			'how-it-works'    => array(
				'title'     => __( 'How It Works', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-how-it-works.php',
			),
			'subjects'        => array(
				'title'     => __( 'Subjects Directory', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-subjects.php',
			),
			'ssc-english'     => array(
				'title'     => __( 'SSC English', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-ssc-english.php',
			),
			'hsc-english'     => array(
				'title'     => __( 'HSC English', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-hsc-english.php',
			),
			'pricing'         => array(
				'title'     => __( 'Pricing & Passes', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-pricing.php',
			),
			'faq'             => array(
				'title'     => __( 'FAQ', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-faq.php',
			),
			'contact'         => array(
				'title'     => __( 'Contact & Support', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-contact.php',
			),
			'privacy-policy'  => array(
				'title'     => __( 'Privacy Policy', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-privacy-policy.php',
			),
			'terms'           => array(
				'title'     => __( 'Terms of Service', 'nctb-learning-hub' ),
				'content'   => '',
				'template'  => 'page-terms.php',
			),
			'teacher-onboarding' => array(
				'title'     => __( 'Teacher Onboarding', 'nctb-learning-hub' ),
				'content'   => '[nctb_teacher_onboarding]',
				'template'  => 'page-teacher-onboarding.php',
			),
			'teacher-dashboard'  => array(
				'title'     => __( 'Teacher Dashboard', 'nctb-learning-hub' ),
				'content'   => '[nctb_teacher_dashboard]',
				'template'  => 'page-teacher-dashboard.php',
			),
		);
	}

	/**
	 * Ensure every required page exists. Idempotent.
	 *
	 * @return void
	 */
	public static function ensure_pages() {
		foreach ( self::definitions() as $slug => $def ) {
			$existing = get_page_by_path( $slug );
			if ( $existing instanceof WP_Post ) {
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $def['title'],
					'post_content' => $def['content'],
				)
			);

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				// Assign the matching page template only if it exists in the theme.
				if ( ! empty( $def['template'] ) && locate_template( $def['template'] ) ) {
					update_post_meta( $page_id, '_wp_page_template', $def['template'] );
				}
				NCTB_Logger::info( 'Provisioned page', array( 'slug' => $slug, 'id' => $page_id ) );
			}
		}

		update_option( self::PROVISIONED_OPTION, 1, false );
	}

	/**
	 * Run provisioning once for in-place upgrades (guarded by an option),
	 * so sites updated by replacing files also gain the pages.
	 *
	 * @return void
	 */
	public static function maybe_provision() {
		if ( get_option( self::PROVISIONED_OPTION ) ) {
			return;
		}
		self::ensure_pages();
	}
}
