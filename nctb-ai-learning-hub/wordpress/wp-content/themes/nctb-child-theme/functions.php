<?php
/**
 * NCTB Learning Hub Theme — functions.
 *
 * Presentation only. NO learning/business logic belongs here (see the plan's
 * architecture rules). Phase 0 sets up theme supports, the main stylesheet and
 * UTF-8 Bangla/English typography. The visual shell and navigation arrive in
 * Phase 1.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme setup: register supports and menus.
 *
 * @return void
 */
function nctb_theme_setup() {
	load_theme_textdomain( 'nctb-theme', get_stylesheet_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support( 'responsive-embeds' );

	// Navigation menus used by the Phase 1 shell.
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'nctb-theme' ),
			'student' => __( 'Student Menu', 'nctb-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'nctb_theme_setup' );

/**
 * Enqueue the theme stylesheet.
 *
 * @return void
 */
function nctb_theme_enqueue_assets() {
	wp_enqueue_style(
		'nctb-theme',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Public marketing site styles (homepage + marketing pages).
	if ( is_front_page() || is_page( array( 'how-it-works', 'subjects', 'ssc-english', 'hsc-english', 'pricing', 'faq', 'contact' ) ) ) {
		wp_enqueue_style(
			'nctb-marketing',
			get_stylesheet_directory_uri() . '/css/marketing.css',
			array( 'nctb-theme' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	// Curriculum browse & student study styles.
	if ( is_singular( array( 'nctb_book', 'nctb_unit', 'nctb_lesson' ) ) || is_post_type_archive( 'nctb_book' ) || is_page( array( 'mistakes', 'revision', 'progress', 'dashboard', 'purchases' ) ) ) {
		wp_enqueue_style(
			'nctb-curriculum',
			get_stylesheet_directory_uri() . '/css/curriculum.css',
			array( 'nctb-theme' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	// Interactive lesson & student study script.
	if ( is_singular( 'nctb_lesson' ) || is_page( array( 'mistakes', 'revision', 'progress', 'dashboard', 'purchases' ) ) ) {
		wp_enqueue_script(
			'nctb-lesson-interactive',
			get_stylesheet_directory_uri() . '/js/lesson-interactive.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'nctb_theme_enqueue_assets' );

/**
 * Ensure a UTF-8 charset + mobile viewport are present for Bangla support.
 *
 * @return void
 */
function nctb_theme_head_meta() {
	echo '<meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">' . "\n";
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
}
add_action( 'wp_head', 'nctb_theme_head_meta', 0 );

/**
 * Provision the public marketing pages so the site is reproducible from the
 * repository (the page-{slug}.php templates render them). Idempotent: it only
 * creates a page when one with that slug does not already exist.
 *
 * @return void
 */
function nctb_theme_provision_marketing_pages() {
	if ( get_option( 'nctb_theme_mkt_pages_done' ) ) {
		return;
	}

	$pages = array(
		'how-it-works' => __( 'How It Works', 'nctb-theme' ),
		'subjects'     => __( 'Subjects', 'nctb-theme' ),
		'ssc-english'  => __( 'SSC English', 'nctb-theme' ),
		'hsc-english'  => __( 'HSC English', 'nctb-theme' ),
		'pricing'      => __( 'Pricing', 'nctb-theme' ),
		'faq'          => __( 'FAQ', 'nctb-theme' ),
		'contact'      => __( 'Contact', 'nctb-theme' ),
	);

	foreach ( $pages as $slug => $title ) {
		if ( ! get_page_by_path( $slug ) ) {
			wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $title,
					'post_content' => '',
				)
			);
		}
	}

	update_option( 'nctb_theme_mkt_pages_done', 1 );
}
add_action( 'after_switch_theme', 'nctb_theme_provision_marketing_pages' );
add_action( 'admin_init', 'nctb_theme_provision_marketing_pages' );
