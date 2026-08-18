<?php
/*
Plugin Name: NCTB AI Learning Hub
Description: Educational platform plugin for Bangladesh curriculum
Version: 0.1.0
Author: NCTB Team
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load plugin text domain
load_plugin_textdomain('nctb-learning-hub', false, dirname(__FILE__) . '/languages');

// Include core classes
require_once __DIR__ . '/includes/NCTB_Migrations.php';
require_once __DIR__ . '/includes/NCTB_Roles.php';
require_once __DIR__ . '/includes/NCTB_Enrollment.php';
require_once __DIR__ . '/includes/NCTB_Logger.php';
require_once __DIR__ . '/includes/NCTB_Lifecycle.php';
require_once __DIR__ . '/includes/NCTB_Certificate.php';
require_once __DIR__ . '/includes/NCTB_Messaging.php';
require_once __DIR__ . '/includes/NCTB_Analytics.php';
require_once __DIR__ . '/includes/NCTB_Gamification.php';

// Hook into WordPress initialization
add_action('init', 'nctb_learning_hub_init');
function nctb_learning_hub_init() {
    // Register post types
    register_post_type('lesson', array(
        'labels' => array(
            'name' => 'Lessons',
            'singular_name' => 'Lesson'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'lessons'),
    ));

    register_taxonomy('subject', 'lesson', array(
        'label' => 'Subject',
        'rewrite' => array('slug' => 'subject'),
        'hierarchical' => true,
    ));
}

// Admin menu
add_action('admin_menu', 'nctb_learning_hub_admin_menu');
function nctb_learning_hub_admin_menu() {
    add_menu_page(
        'NCTB Learning Hub',
        'NCTB Learning Hub',
        'manage_options',
        'nctb-learning-hub',
        'nctb_learning_hub_dashboard_page'
    );
}

// Dashboard page
function nctb_learning_hub_dashboard_page() {
    ?>
    <div class="wrap">
        <h1>NCTB Learning Hub Dashboard</h1>
        <p>Welcome to the NCTB AI Learning Hub admin panel.</p>
        <p><strong>Current Status:</strong> Plugin is active and ready for configuration.</p>
    </div>
    <?php
}

// AJAX endpoint for connection check
add_action('wp_ajax_nctb_check_connection', 'nctb_check_connection_ajax');
add_action('wp_ajax_nopriv_nctb_check_connection', 'nctb_check_connection_ajax');
function nctb_check_connection_ajax() {
    $response = array(
        'success' => true,
        'message' => 'Connection established',
        'timestamp' => time()
    );
    wp_send_json($response);
    wp_die();
}

// AJAX endpoint for setting language
add_action('wp_ajax_nctb_set_language', 'nctb_set_language_ajax');
add_action('wp_ajax_nopriv_nctb_set_language', 'nctb_set_language_ajax');
function nctb_set_language_ajax() {
    // Security check
    check_ajax_referer('nctb_ajax_nonce', 'nonce');

    $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : 'en';

    // Validate language
    if (!in_array($language, array('en', 'bn'))) {
        $language = 'en';
    }

    // Set language cookie
    setcookie('nctb_language', $language, time() + (365 * 24 * 60 * 60), COOKIEPATH, COOKIE_DOMAIN);

    wp_send_json_success(array(
        'message' => 'Language set successfully',
        'language' => $language
    ));
    wp_die();
}

// AJAX endpoint for updating lesson progress
add_action('wp_ajax_nctb_update_lesson_progress', 'nctb_update_lesson_progress');
add_action('wp_ajax_nopriv_nctb_update_lesson_progress', 'nctb_update_lesson_progress');
function nctb_update_lesson_progress() {
    // Security check
    check_ajax_referer('nctb_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Please log in'), 401);
    }

    $user_id = get_current_user_id();
    $lesson_id = isset($_POST['lesson_id']) ? absint($_POST['lesson_id']) : 0;
    $progress = isset($_POST['progress']) ? absint($_POST['progress']) : 0;

    if (!$lesson_id || !$progress || $progress > 100) {
        wp_send_json_error(array('message' => 'Invalid progress data'));
    }

    // Update progress
    update_user_meta($user_id, 'nctb_lesson_progress_' . $lesson_id, $progress);

    wp_send_json_success(array(
        'message' => 'Progress updated',
        'progress' => $progress,
        'lesson_id' => $lesson_id
    ));
}

// AJAX endpoint for creating new lesson
add_action('wp_ajax_nctb_create_lesson', 'nctb_create_lesson');
function nctb_create_lesson() {
    check_ajax_referer('nctb_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Please log in'), 401);
    }

    $user_id = get_current_user_id();
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';

    if (empty($title)) {
        wp_send_json_error(array('message' => 'Lesson title is required'));
    }

    $lesson_id = wp_insert_post(array(
        'post_type' => 'lesson',
        'post_title' => $title,
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => $user_id,
    ));

    if (is_wp_error($lesson_id)) {
        wp_send_json_error(array('message' => $lesson_id->get_error_message()));
    }

    // Set subject taxonomy (default to English if not specified)
    wp_set_post_tags($lesson_id, '', 'subject');
    wp_set_object_terms($lesson_id, 'English', 'subject', false);

    wp_send_json_success(array(
        'message' => 'Lesson created successfully',
        'lesson_id' => $lesson_id
    ));
}

// AJAX endpoint for deleting lesson
add_action('wp_ajax_nctb_delete_lesson', 'nctc_delete_lesson');
function nctc_delete_lesson() {
    check_ajax_referer('nctb_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Please log in'), 401);
    }

    $lesson_id = isset($_POST['lesson_id']) ? absint($_POST['lesson_id']) : 0;
    if (!$lesson_id) {
        wp_send_json_error(array('message' => 'Invalid lesson ID'));
    }

    // Check if user owns this lesson
    $lesson_author = get_post_field('post_author', $lesson_id);
    if ($lesson_author != get_current_user_id()) {
        wp_send_json_error(array('message' => 'You cannot delete this lesson'));
    }

    $deleted = wp_delete_post($lesson_id, true);
    if (is_wp_error($deleted)) {
        wp_send_json_error(array('message' => $deleted->get_error_message()));
    }

    wp_send_json_success(array('message' => 'Lesson deleted successfully'));
}

// AJAX endpoint for getting analytics data
add_action('wp_ajax_nctb_get_analytics_data', 'nctb_get_analytics_data');
add_action('wp_ajax_nopriv_nctb_get_analytics_data', 'nctb_get_analytics_data');
function nctb_get_analytics_data() {
    // Security check
    check_ajax_referer('nctb_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Please log in'), 401);
    }

    // Check if user is teacher or admin
    $user = wp_get_current_user();
    $roles = (array) $user->roles;
    $is_teacher = !empty(array_intersect($roles, array('author', 'editor', 'administrator')));
    if (!$is_teacher) {
        wp_send_json_error(array('message' => 'Access denied'), 403);
    }

    $data_type = isset($_POST['data_type']) ? sanitize_text_field($_POST['data_type']) : 'all';

    // Initialize NCTB_Analytics and get data
    if (class_exists('NCTB_Analytics')) {
        $analytics = NCTB_Analytics::instance();
        $response_data = array();

        if ($data_type === 'all' || $data_type === 'lesson_completion') {
            $response_data['lesson_completion'] = $analytics->get_lesson_completion_data();
        }
        if ($data_type === 'all' || $data_type === 'student_progress') {
            $response_data['student_progress'] = $analytics->get_student_progress_data();
        }
        if ($data_type === 'all' || $data_type === 'enrollment_trends') {
            $response_data['enrollment_trends'] = $analytics->get_enrollment_trends_data();
        }
        if ($data_type === 'all' || $data_type === 'avg_completion_rate') {
            $response_data['avg_completion_rate'] = $analytics->get_avg_completion_rate();
        }

        wp_send_json_success(array('data' => $response_data));
    } else {
        wp_send_json_error(array('message' => 'Analytics class not found'));
    }
    wp_die();
}

// AJAX endpoint for getting user badges
add_action('wp_ajax_nctb_get_user_badges', 'nctb_get_user_badges');
add_action('wp_ajax_nopriv_nctb_get_user_badges', 'nctb_get_user_badges');
function nctb_get_user_badges() {
    // Security check
    check_ajax_referer('nctb_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Please log in'), 401);
    }

    if (class_exists('NCTB_Gamification')) {
        $gamification = NCTB_Gamification::instance();
        $user_id = get_current_user_id();
        $badges = $gamification->get_user_badges($user_id);

        wp_send_json_success(array(
            'badges' => $badges
        ));
    } else {
        wp_send_json_error(array('message' => 'Gamification class not found'));
    }
    wp_die();
}

// AJAX endpoint for getting user points
add_action('wp_ajax_nctb_get_user_points', 'nctb_get_user_points');
add_action('wp_ajax_nopriv_nctb_get_user_points', 'nctb_get_user_points');
function nctb_get_user_points() {
    // Security check
    check_ajax_referer('nctb_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Please log in'), 401);
    }

    if (class_exists('NCTB_Gamification')) {
        $gamification = NCTB_Gamification::instance();
        $user_id = get_current_user_id();
        $points = $gamification->get_user_points($user_id);

        wp_send_json_success(array(
            'points' => $points
        ));
    } else {
        wp_send_json_error(array('message' => 'Gamification class not found'));
    }
    wp_die();
}

// AJAX endpoint for getting leaderboard
add_action('wp_ajax_nctb_get_leaderboard', 'nctb_get_leaderboard');
add_action('wp_ajax_nopriv_nctb_get_leaderboard', 'nctb_get_leaderboard');
function nctb_get_leaderboard() {
    // Security check
    check_ajax_referer('nctb_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Please log in'), 401);
    }

    if (class_exists('NCTB_Gamification')) {
        $gamification = NCTB_Gamification::instance();
        $leaderboard = $gamification->get_leaderboard(10);

        wp_send_json_success(array(
            'leaderboard' => $leaderboard
        ));
    } else {
        wp_send_json_error(array('message' => 'Gamification class not found'));
    }
    wp_die();
}