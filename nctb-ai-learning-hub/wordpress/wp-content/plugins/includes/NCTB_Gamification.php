<?php
/**
 * NCTB Gamification
 * Handles points, badges, and leaderboards
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Gamification {
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Hook into lesson completion to award points and check for badges
        add_action('nctb_lesson_completed', array($this, 'handle_lesson_completion'), 10, 2);

        // AJAX endpoints for gamification data
        add_action('wp_ajax_nctb_get_user_points', array($this, 'get_user_points_ajax'));
        add_action('wp_ajax_nopriv_nctb_get_user_points', array($this, 'get_user_points_ajax'));
        add_action('wp_ajax_nctb_get_user_badges', array($this, 'get_user_badges_ajax'));
        add_action('wp_ajax_nopriv_nctb_get_user_badges', array($this, 'get_user_badges_ajax'));
        add_action('wp_ajax_nctb_get_leaderboard', array($this, 'get_leaderboard_ajax'));
        add_action('wp_ajax_nopriv_nctb_get_leaderboard', array($this, 'get_leaderboard_ajax'));

        // Register shortcodes
        add_shortcode('nctb_user_points', array($this, 'user_points_shortcode'));
        add_shortcode('nctb_user_badges', array($this, 'user_badges_shortcode'));
        add_shortcode('nctb_leaderboard', array($this, 'leaderboard_shortcode'));
    }

    /**
     * Handle lesson completion event
     */
    public function handle_lesson_completion($user_id, $lesson_id) {
        // Award points for completing a lesson
        $this->award_points($user_id, 'lesson_completion', 10, 'Completed a lesson');

        // Check for badge awards
        $this->check_and_award_badges($user_id, $lesson_id);
    }

    /**
     * Award points to a user
     */
    public function award_points($user_id, $point_type, $points, $description = '') {
        // Get current points
        $current_points = get_user_meta($user_id, 'nctb_points', true);
        if ($current_points === '') {
            $current_points = 0;
        }
        $new_points = intval($current_points) + intval($points);

        // Update user meta
        update_user_meta($user_id, 'nctb_points', $new_points);

        // Log the points transaction (optional)
        $this->log_points_transaction($user_id, $point_type, $points, $description, $new_points);

        // Do something with the points (e.g., check for level-ups, etc.)
        // For now, just store the total.

        return $new_points;
    }

    /**
     * Log points transaction (for history)
     */
    private function log_points_transaction($user_id, $point_type, $points, $description, $new_total) {
        // We'll store transactions as a serialized array in user meta for simplicity
        $transactions = get_user_meta($user_id, 'nctb_points_transactions', true);
        if (!is_array($transactions)) {
            $transactions = array();
        }

        $transactions[] = array(
            'type' => $point_type,
            'points' => $points,
            'description' => $description,
            'total_after' => $new_total,
            'timestamp' => current_time('mysql')
        );

        // Keep only last 50 transactions
        if (count($transactions) > 50) {
            $transactions = array_slice($transactions, -50);
        }

        update_user_meta($user_id, 'nctb_points_transactions', $transactions);
    }

    /**
     * Get user's total points
     */
    public function get_user_points($user_id) {
        $points = get_user_meta($user_id, 'nctb_points', true);
        return $points === '' ? 0 : intval($points);
    }

    /**
     * Check and award badges based on user activity
     */
    private function check_and_award_badges($user_id, $lesson_id) {
        // Get user's badges
        $badges = get_user_meta($user_id, 'nctb_badges', true);
        if (!is_array($badges)) {
            $badges = array();
        }

        // Badge: First Lesson
        if (!in_array('first_lesson', $badges)) {
            $lesson_count = count_user_posts($user_id, 'lesson');
            if ($lesson_count >= 1) {
                $this->award_badge($user_id, 'first_lesson', 'First Lesson', 'Completed your first lesson');
            }
        }

        // Badge: Lesson Streak (simplified: 5 lessons in a row - we don't track streaks yet, so we'll skip for now)
        // We would need to track lesson completion dates to calculate streaks.

        // Badge: 10 Lessons Completed
        if (!in_array('ten_lessons', $badges)) {
            $lesson_count = count_user_posts($user_id, 'lesson');
            if ($lesson_count >= 10) {
                $this->award_badge($user_id, 'ten_lessons', 'Dedicated Learner', 'Completed 10 lessons');
            }
        }

        // Badge: 50 Lessons Completed
        if (!in_array('fifty_lessons', $badges)) {
            $lesson_count = count_user_posts($user_id, 'lesson');
            if ($lesson_count >= 50) {
                $this->award_badge($user_id, 'fifty_lessons', 'Learning Master', 'Completed 50 lessons');
            }
        }
    }

    /**
     * Award a badge to a user
     */
    public function award_badge($user_id, $badge_id, $badge_name, $badge_description) {
        // Get user's badges
        $badges = get_user_meta($user_id, 'nctb_badges', true);
        if (!is_array($badges)) {
            $badges = array();
        }

        // If user doesn't already have this badge, award it
        if (!in_array($badge_id, $badges)) {
            $badges[] = $badge_id;
            update_user_meta($user_id, 'nctb_badges', $badges);

            // Optionally, log the badge award
            $this->log_badge_award($user_id, $badge_id, $badge_name, $badge_description);

            return true;
        }

        return false;
    }

    /**
     * Log badge award
     */
    private function log_badge_award($user_id, $badge_id, $badge_name, $badge_description) {
        // Store badge awards as a serialized array
        $awards = get_user_meta($user_id, 'nctb_badge_awards', true);
        if (!is_array($awards)) {
            $awards = array();
        }

        $awards[] = array(
            'badge_id' => $badge_id,
            'badge_name' => $badge_name,
            'badge_description' => $badge_description,
            'timestamp' => current_time('mysql')
        );

        update_user_meta($user_id, 'nctb_badge_awards', $awards);
    }

    /**
     * Get user's badges
     */
    public function get_user_badges($user_id) {
        $badges = get_user_meta($user_id, 'nctb_badges', true);
        if (!is_array($badges)) {
            return array();
        }

        // We need to return badge details, not just IDs
        $badge_details = array(
            'first_lesson' => array(
                'name' => 'First Lesson',
                'description' => 'Completed your first lesson',
                'icon' => '🏆' // or use a dashicon or image
            ),
            'ten_lessons' => array(
                'name' => 'Dedicated Learner',
                'description' => 'Completed 10 lessons',
                'icon' => '📚'
            ),
            'fifty_lessons' => array(
                'name' => 'Learning Master',
                'description' => 'Completed 50 lessons',
                'icon' => '👑'
            )
        );

        $result = array();
        foreach ($badges as $badge_id) {
            if (isset($badge_details[$badge_id])) {
                $result[$badge_id] = $badge_details[$badge_id];
            }
        }

        return $result;
    }

    /**
     * Get leaderboard data (top users by points)
     */
    public function get_leaderboard($limit = 10) {
        // Get all users with at least the subscriber role
        $args = array(
            'role' => 'subscriber',
            'number' => -1 // get all
        );
        $users = get_users($args);

        $leaderboard = array();
        foreach ($users as $user) {
            $points = $this->get_user_points($user->ID);
            $leaderboard[] = array(
                'user_id' => $user->ID,
                'display_name' => $user->display_name,
                'points' => $points
            );
        }

        // Sort by points descending
        usort($leaderboard, function($a, $b) {
            return $b['points'] - $a['points'];
        });

        // Limit results
        if ($limit > 0) {
            $leaderboard = array_slice($leaderboard, 0, $limit);
        }

        return $leaderboard;
    }

    /**
     * AJAX endpoint for getting user points
     */
    public function get_user_points_ajax() {
        // Security check
        check_ajax_referer('nctb_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please log in'), 401);
        }

        $user_id = get_current_user_id();
        $points = $this->get_user_points($user_id);

        wp_send_json_success(array(
            'points' => $points
        ));
    }

    /**
     * AJAX endpoint for getting user badges
     */
    public function get_user_badges_ajax() {
        // Security check
        check_ajax_referer('nctb_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please log in'), 401);
        }

        $user_id = get_current_user_id();
        $badges = $this->get_user_badges($user_id);

        wp_send_json_success(array(
            'badges' => $badges
        ));
    }

    /**
     * AJAX endpoint for getting leaderboard
     */
    public function get_leaderboard_ajax() {
        // Security check
        check_ajax_referer('nctb_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please log in'), 401);
        }

        $leaderboard = $this->get_leaderboard(10);

        wp_send_json_success(array(
            'leaderboard' => $leaderboard
        ));
    }

    /**
     * Shortcode to display user points
     */
    public function user_points_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '';
        }

        $user_id = get_current_user_id();
        $points = $this->get_user_points($user_id);

        return '<span class="nctb-points">' . esc_html($points) . ' points</span>';
    }

    /**
     * Shortcode to display user badges
     */
    public function user_badges_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '';
        }

        $user_id = get_current_user_id();
        $badges = $this->get_user_badges($user_id);

        if (empty($badges)) {
            return '<p>No badges earned yet.</p>';
        }

        $output = '<div class="nctb-badges">';
        foreach ($badges as $badge_id => $badge) {
            $output .= '<div class="nctb-badge" title="' . esc_attr($badge['description']) . '">';
            $output .= '<span class="nctb-badge-icon">' . esc_html($badge['icon']) . '</span>';
            $output .= '<span class="nctb-badge-name">' . esc_html($badge['name']) . '</span>';
            $output .= '</div>';
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * Shortcode to display leaderboard
     */
    public function leaderboard_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '';
        }

        $leaderboard = $this->get_leaderboard(10);

        if (empty($leaderboard)) {
            return '<p>No data available.</p>';
        }

        $output = '<div class="nctb-leaderboard"><h3>Leaderboard</h3><ul>';
        foreach ($leaderboard as $entry) {
            $output .= '<li>';
            $output .= '<span class="nctb-leaderboard-rank">' . esc_html($entry['display_name']) . '</span>';
            $output .= '<span class="nctb-leaderboard-points">' . esc_html($entry['points']) . ' points</span>';
            $output .= '</li>';
        }
        $output .= '</ul></div>';

        return $output;
    }
}

NCTB_Gamification::instance();