<?php
/**
 * NCTB Lifecycle
 * Handles user progress and lesson completion logic
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Lifecycle {
    private static $instance = null;
    private $ajax_nonce = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('wp_ajax_nctb_update_lesson_progress', array($this, 'update_lesson_progress'));
        add_action('wp_ajax_nopriv_nctb_update_lesson_progress', array($this, 'update_lesson_progress'));
    }

    public function update_lesson_progress() {
        // Security checks
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

        // Verify user access
        if (!current_user_can('read')) {
            wp_send_json_error(array('message' => 'Access denied'));
        }

        // Update progress
        update_user_meta($user_id, 'nctb_lesson_progress_' . $lesson_id, $progress);

        // If lesson completed, award certificate
        if ($progress == 100) {
            do_action('nctb_lesson_completed', $user_id, $lesson_id);
        };

        wp_send_json_success(array(
            'message' => 'Progress updated',
            'progress' => $progress,
            'lesson_id' => $lesson_id
        ));
    }
}

NCTB_Lifecycle::instance();
