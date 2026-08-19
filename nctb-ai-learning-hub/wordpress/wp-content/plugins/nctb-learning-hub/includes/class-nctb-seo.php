<?php
/**
 * SEO & Schema.org Structured Data Service (Phase 15).
 *
 * Injects Google-compliant educational Schema.org structured data (Course,
 * LearningResource, EducationalOccupationalCredential, BreadcrumbList),
 * OpenGraph meta tags, Twitter cards, and sitemaps.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_SEO
 */
class NCTB_SEO {

	/**
	 * Initialize SEO hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'render_head_meta' ), 1 );
		add_action( 'wp_head', array( __CLASS__, 'render_schema_json_ld' ), 2 );
		add_filter( 'wp_sitemaps_post_types', array( __CLASS__, 'add_cpts_to_sitemap' ) );
	}

	/**
	 * Output OpenGraph, Twitter, and SEO meta tags in <head>.
	 *
	 * @return void
	 */
	public static function render_head_meta() {
		if ( is_admin() ) {
			return;
		}

		$title       = wp_get_document_title();
		$description = __( 'Bangla medium NCTB digital learning hub. Interactive lessons, practice quizzes, board question database, and personal AI tutor.', 'nctb-learning-hub' );
		$url         = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
		$image       = get_stylesheet_directory_uri() . '/assets/og-cover.png';
		$site_name   = get_bloginfo( 'name' );

		if ( is_singular( 'nctb_lesson' ) ) {
			$post_id   = get_the_ID();
			$lesson    = get_post( $post_id );
			$book_id   = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_lesson_book( $post_id ) : 0;
			$book_name = $book_id ? get_the_title( $book_id ) : 'NCTB';

			$title       = sprintf( '%s — %s | %s', get_the_title( $post_id ), $book_name, $site_name );
			$description = sprintf( 'NCTB পাঠ ও অনুশীলন: %s। বিস্তারিত ব্যাখ্যা, প্রশ্নব্যাংক, বিগত বোর্ড প্রশ্ন এবং এআই টিউটর সহায়তা।', get_the_title( $post_id ) );
			$url         = get_permalink( $post_id );
		} elseif ( is_singular( 'nctb_book' ) ) {
			$post_id     = get_the_ID();
			$title       = sprintf( '%s — সম্পূর্ণ ডিজিটাল বই | %s', get_the_title( $post_id ), $site_name );
			$description = sprintf( '%s এর সব ইউনিট, পাঠ, গ্রামার এবং মডেল টেস্ট। বোর্ড পরীক্ষার সম্পূর্ণ প্রস্তুতি।', get_the_title( $post_id ) );
			$url         = get_permalink( $post_id );
		}

		echo "\n<!-- NCTB Educational SEO Meta -->\n";
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";

		// OpenGraph
		echo '<meta property="og:locale" content="bn_BD">' . "\n";
		echo '<meta property="og:type" content="' . ( is_singular() ? 'article' : 'website' ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";

		// Twitter
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
		echo "<!-- /NCTB Educational SEO Meta -->\n\n";
	}

	/**
	 * Output Schema.org JSON-LD structured data.
	 *
	 * @return void
	 */
	public static function render_schema_json_ld() {
		if ( is_admin() ) {
			return;
		}

		$site_url  = home_url( '/' );
		$site_name = get_bloginfo( 'name' );
		$schemas   = array();

		// Organization / Website Schema
		$schemas[] = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'EducationalOrganization',
			'name'        => $site_name,
			'url'         => $site_url,
			'description' => 'Comprehensive NCTB digital learning and teaching platform in Bangladesh.',
			'areaServed'  => array(
				'@type' => 'Country',
				'name'  => 'Bangladesh',
			),
		);

		if ( is_singular( 'nctb_book' ) ) {
			$post_id   = get_the_ID();
			$schemas[] = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'Course',
				'name'            => get_the_title( $post_id ),
				'description'     => sprintf( 'NCTB curriculum aligned textbook course: %s', get_the_title( $post_id ) ),
				'provider'        => array(
					'@type' => 'Organization',
					'name'  => $site_name,
					'sameAs' => $site_url,
				),
				'educationalLevel'=> 'Secondary / Higher Secondary',
				'inLanguage'      => array( 'bn', 'en' ),
				'isAccessibleForFree' => true,
			);
		} elseif ( is_singular( 'nctb_lesson' ) ) {
			$post_id   = get_the_ID();
			$book_id   = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_lesson_book( $post_id ) : 0;
			$unit_id   = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_lesson_unit( $post_id ) : 0;

			// BreadcrumbList Schema
			$breadcrumbs = array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => 'Home',
					'item'     => $site_url,
				),
			);
			$pos = 2;
			if ( $book_id ) {
				$breadcrumbs[] = array(
					'@type'    => 'ListItem',
					'position' => $pos++,
					'name'     => get_the_title( $book_id ),
					'item'     => get_permalink( $book_id ),
				);
			}
			if ( $unit_id ) {
				$breadcrumbs[] = array(
					'@type'    => 'ListItem',
					'position' => $pos++,
					'name'     => get_the_title( $unit_id ),
					'item'     => get_permalink( $unit_id ),
				);
			}
			$breadcrumbs[] = array(
				'@type'    => 'ListItem',
				'position' => $pos,
				'name'     => get_the_title( $post_id ),
				'item'     => get_permalink( $post_id ),
			);

			$schemas[] = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $breadcrumbs,
			);

			// LearningResource Schema
			$schemas[] = array(
				'@context'             => 'https://schema.org',
				'@type'                => 'LearningResource',
				'name'                 => get_the_title( $post_id ),
				'learningResourceType' => 'Interactive Lesson Plan and Practice Assessment',
				'educationalAlignment' => array(
					'@type'               => 'AlignmentObject',
					'alignmentType'       => 'educationalSubject',
					'educationalFramework'=> 'Bangladesh National Curriculum and Textbook Board (NCTB)',
					'targetName'          => get_the_title( $post_id ),
				),
				'inLanguage'           => array( 'bn', 'en' ),
				'accessMode'           => array( 'textual', 'visual', 'auditory' ),
			);
		}

		echo "\n<!-- NCTB Schema.org JSON-LD -->\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . "</script>\n";
		echo "<!-- /NCTB Schema.org JSON-LD -->\n\n";
	}

	/**
	 * Include NCTB custom post types in WordPress XML Sitemaps.
	 *
	 * @param array<string,WP_Post_Type> $post_types Sitemapped post types.
	 * @return array<string,WP_Post_Type>
	 */
	public static function add_cpts_to_sitemap( $post_types ) {
		$cpts = array( 'nctb_book', 'nctb_unit', 'nctb_lesson', 'nctb_note' );
		foreach ( $cpts as $cpt ) {
			if ( post_type_exists( $cpt ) ) {
				$post_types[ $cpt ] = get_post_type_object( $cpt );
			}
		}
		return $post_types;
	}
}
