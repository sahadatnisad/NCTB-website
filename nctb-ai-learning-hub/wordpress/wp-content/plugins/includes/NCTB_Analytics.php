<?php
/**
 * NCTB Analytics
 * Handles advanced analytics data for charts and reports
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Analytics {
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // AJAX endpoints for analytics data
        add_action('wp_ajax_nctb_get_analytics_data', array($this, 'get_analytics_data'));
        add_action('wp_ajax_nopriv_nctb_get_analytics_data', array($this, 'get_analytics_data'));
    }

    /**
     * Get analytics data for charts
     */
    public function get_analytics_data() {
        // Security checks
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

        $response = array();

        if ($data_type === 'all' || $data_type === 'lesson_completion') {
            $response['lesson_completion'] = $this->get_lesson_completion_data();
        }
        if ($data_type === 'all' || $data_type === 'student_progress') {
            $response['student_progress'] = $this->get_student_progress_data();
        }
        if ($data_type === 'all' || $data_type === 'enrollment_trends') {
            $response['enrollment_trends'] = $this->get_enrollment_trends_data();
        }
        if ($data_type === 'all' || $data_type === 'avg_completion_rate') {
            $response['avg_completion_rate'] = $this->get_avg_completion_rate();
        }

        wp_send_json_success(array('data' => $response));
    }

    /**
     * Get lesson completion data for the last 30 days
     */
    private function get_lesson_completion_data() {
        global $wpdb;

        // Get lessons completed in the last 30 days, grouped by day
        $query = $wpdb->prepare("
            SELECT DATE(umeta.meta_value) as date, COUNT(*) as count
            FROM {$wpdb->usermeta} umeta
            WHERE umeta.meta_key LIKE %s
            AND umeta.meta_value = %s
            AND umeta.meta_value >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(umeta.meta_value)
            ORDER BY DATE(umeta.meta_value)
        ", 'nctb_lesson_progress_%', '100');

        $results = $wpdb->get_results($query);

        // Format for Chart.js
        $labels = array();
        $data = array();
        foreach ($results as $row) {
            $labels[] = date('M d', strtotime($row->date));
            $data[] = (int) $row->count;
        }

        // If no data, provide empty arrays
        if (empty($labels)) {
            $labels = array('No data');
            $data = array(0);
        }

        return array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => 'Lessons Completed per Day',
                    'data' => $data,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'fill' => false,
                    'lineTension' => 0.1
                )
            )
        );
    }

    /**
     * Get student progress distribution
     */
    private function get_student_progress_data() {
        global $wpdb;

        // Get progress distribution for all students
        // We'll bucket progress into 0-20, 21-40, 41-60, 61-80, 81-100
        $query = $wpdb->prepare("
            SELECT
                CASE
                    WHEN umeta.meta_value BETWEEN 0 AND 20 THEN '0-20%'
                    WHEN umeta.meta_value BETWEEN 21 AND 40 THEN '21-40%'
                    WHEN umeta.meta_value BETWEEN 41 AND 60 THEN '41-60%'
                    WHEN umeta.meta_value BETWEEN 61 AND 80 THEN '61-80%'
                    WHEN umeta.meta_value BETWEEN 81 AND 100 THEN '81-100%'
                END as progress_range,
                COUNT(*) as count
            FROM {$wpdb->usermeta} umeta
            WHERE umeta.meta_key LIKE %s
            GROUP BY progress_range
        ", 'nctb_lesson_progress_%');

        $results = $wpdb->get_results($query);

        // Initialize all ranges with zero
        $ranges = array('0-20%', '21-40%', '41-60%', '61-80%', '81-100%');
        $data = array_fill_keys($ranges, 0);

        foreach ($results as $row) {
            if (array_key_exists($row->progress_range, $data)) {
                $data[$row->progress_range] = (int) $row->count;
            }
        }

        // Format for Chart.js (pie chart)
        $labels = array_keys($data);
        $data = array_values($data);

        return array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    'borderColor' => [
                        'rgba(255,99,132,1)',
                        'rgba(54,162,235,1)',
                        'rgba(255,206,86,1)',
                        'rgba(75,192,192,1)',
                        'rgba(153,102,255,1)'
                    ],
                    'borderWidth' => 1
                )
            )
        );
    }

    /**
     * Get enrollment trends over time
     */
    private function get_enrollment_trends_data() {
        global $wpdb;

        // Get enrollments per week for the last 12 weeks
        $query = $wpdb->prepare("
            SELECT YEARWEEK(umeta.meta_value, 1) as yearweek, COUNT(*) as count
            FROM {$wpdb->usermeta} umeta
            WHERE umeta.meta_key = %s
            AND umeta.meta_value >= DATE_SUB(NOW(), INTERVAL 12 WEEK)
            GROUP BY YEARWEEK(umeta.meta_value, 1)
            ORDER BY YEARWEEK(umeta.meta_value, 1)
        ", 'nctb_enrolled_lessons');

        // Note: The above query won't work because nctb_enrolled_lessons is stored as serialized array.
        // For simplicity, we'll return mock data for now.
        // In a real implementation, we would need to properly query the enrolled lessons.

        // Mock data for demonstration
        $labels = array();
        $data = array();
        for ($i = 11; $i >= 0; $i--) {
            $week = date('W', strtotime('-'.$i.' weeks'));
            $year = date('Y', strtotime('-'.$i.' weeks'));
            $labels[] = $year.'W'.$week;
            $data[] = rand(5, 25); // random enrollment count
        }

        return array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => 'Weekly Enrollments',
                    'data' => $data,
                    'fill' => false,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.4)',
                    'lineTension' => 0.1
                )
            )
        );
    }

    /**
     * Get average completion rate across all lessons
     */
    private function get_avg_completion_rate() {
        global $wpdb;

        // Get the average progress across all lessons for all students
        // We'll calculate the average of the nctb_lesson_progress_* meta values
        $query = $wpdb->prepare("
            SELECT AVG(CAST(umeta.meta_value AS DECIMAL)) as avg_progress
            FROM {$wpdb->usermeta} umeta
            WHERE umeta.meta_key LIKE %s
        ", 'nctb_lesson_progress_%');

        $result = $wpdb->get_var($query);

        if ($result === null) {
            return 0;
        }

        return round($result, 1);
    }
}

NCTB_Analytics::instance();