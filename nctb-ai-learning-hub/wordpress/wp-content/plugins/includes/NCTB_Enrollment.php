<?php
/**
 * NCTB Enrollment
 * Handles lesson enrollment and unrollment workflows
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Enrollment {
    private static $instance = null;
    private $ajax_nonce = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('wp_ajax_nctb_enroll_lesson', array($this, 'enroll_lesson'));
        add_action('wp_ajax_nopriv_nctb_enroll_lesson', array($this, 'enroll_lesson'));
        add_action('wp_ajax_nctb_unenroll_lesson', array($this, 'unenroll_lesson'));
        add_action('wp_ajax_nopriv_nctb_unenroll_lesson', array($this, 'unenroll_lesson'));
    }

    public function enroll_lesson() {
        // Security checks
        check_ajax_referer('nctb_ajax_nonce', 'nonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please log in'), 401);
        }

        $user_id = get_current_user_id();
        $lesson_id = isset($_POST['lesson_id']) ? absint($_POST['lesson_id']) : 0;

        if (!$lesson_id) {
            wp_send_json_error(array('message' => 'Invalid lesson ID'));
        }

        // Check if already enrolled
        $enrolled = get_user_meta($user_id, 'nctb_enrolled_lessons', true);
        $enrolled_array = is_array($enrolled) ? $enrolled : array();
        if (in_array($lesson_id, $enrolled_array)) {
            wp_send_json_error(array('message' => 'Already enrolled'));
        }

        // Add enrollment
        $enrolled_array[] = $lesson_id;
        update_user_meta($user_id, 'nctb_enrolled_lessons', $enrolled_array);
        update_post_meta($lesson_id, 'nctb_enrollment_count', (get_post_meta($lesson_id, 'nctb_enrollment_count', true) ?: 0) + 1);

        wp_send_json_success(array(
            'message' => 'Enrolled successfully',
            'lesson_id' => $lesson_id,
            'enrollment_count' => (int)get_post_meta($lesson_id, 'nctb_enrollment_count', true)
        ));
    }

    public function unenroll_lesson() {
        // Security checks
        check_ajax_referer('nctb_ajax_nonce', 'nonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please log in'), 401);
        }

        $user_id = get_current_user_id();
        $lesson_id = isset($_POST['lesson_id']) ? absint($_POST['lesson_id']) : 0;

        if (!$lesson_id) {
            wp_send_json_error(array('message' => 'Invalid lesson ID'));
        }

        // Remove enrollment
        $enrolled = get_user_meta($user_id, 'nctb_enrolled_lessons', true);
        $enrolled_array = is_array($enrolled) ? $enrolled : array();
        $enrolled_array = array_diff($enrolled_array, array($lesson_id));
        update_user_meta($user_id, 'nctb_enrolled_lessons', $enrolled_array);

        // Update enrollment count
        $current_count = (int)get_post_meta($lesson_id, 'nctb_enrollment_count', true);
        if ($current_count > 0) {
            update_post_meta($lesson_id, 'nctb_enrollment_count', $current_count - 1);
        }

        wp_send_json_success(array(
            'message' => 'Unenrolled successfully',
            'lesson_id' => $lesson_id,
            'enrollment_count' => (int)get_post_meta($lesson_id, 'nctn_enrollment_count', true)
        ));
    }

    public function get_enrollment_status($lesson_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $enrolled = get_user_meta($user_id, 'nctb_enrolled_lessons', true);
        $enrolled_array = is_array($enrolled) ? $enrolled : array();
        return in_array($lesson_id, $enrolled_array);

    }
}

NCTB_Enrollment::instance();