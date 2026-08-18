<?php
/**
 * NCTB Certificate
 * Handles certificate generation and awarding for completed lessons
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Certificate {
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Hook into lesson completion to award certificate
        add_action('nctb_lesson_completed', array($this, 'maybe_award_certificate'), 10, 2);
        // AJAX endpoint to get certificate
        add_action('wp_ajax_nctb_get_certificate', array($this, 'get_certificate'));
        add_action('wp_ajax_nopriv_nctb_get_certificate', array($this, 'get_certificate'));
        // Add certificate menu to student dashboard
        add_filter('nctb_student_dashboard_tabs', array($this, 'add_certificate_tab'));
    }

    /**
     * Award certificate when lesson is completed
     */
    public function maybe_award_certificate($user_id, $lesson_id) {
        // Check if certificate already awarded
        $awarded = get_user_meta($user_id, 'nctb_certificate_awarded_' . $lesson_id, true);
        if (!empty($awarded)) {
            return;
        }

        // Award certificate
        update_user_meta($user_id, 'nctb_certificate_awarded_' . $lesson_id, true);
        update_user_meta($user_id, 'nctb_certificate_awarded_date_' . $lesson_id, current_time('mysql'));

        // Do something else? Maybe send email or show notice.
        // For now, just record.
    }

    /**
     * Get certificate HTML for a lesson and user
     */
    public function get_certificate_html($user_id, $lesson_id) {
        $lesson = get_post($lesson_id);
        if (!$lesson) {
            return '';
        }

        $user = get_userdata($user_id);
        if (!$user) {
            return '';
        }

        $awarded_date = get_user_meta($user_id, 'nctb_certificate_awarded_date_' . $lesson_id, true);
        $awarded_date_formatted = $awarded_date ? date('F j, Y', strtotime($awarded_date)) : date('F j, Y');

        ob_start();
        ?>
        <div class="certificate" style="width: 800px; height: 600px; padding: 40px; border: 10px solid #2c5aa0; background: #f8f9fa; position: relative; font-family: 'Georgia', serif;">
            <div style="text-align: center;">
                <h1 style="color: #2c5aa0; margin-bottom: 10px;">Certificate of Completion</h1>
                <div style="width: 150px; height: 150px; border: 3px solid #2c5aa0; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 48px; color: #2c5aa0;">🎓</span>
                </div>
                <p style="font-size: 24px; margin-bottom: 20px;">This certifies that</p>
                <h2 style="color: #2c5aa0; margin-bottom: 10px;"><?php echo esc_html($user->display_name); ?></h2>
                <p style="font-size: 18px; margin-bottom: 30px;">has successfully completed the course</p>
                <h3 style="color: #2c5aa0; margin-bottom: 20px;"><?php echo esc_html($lesson->post_title); ?></h3>
                <p style="font-size: 16px;">Awarded on: <?php echo esc_html($awarded_date_formatted); ?></p>
                <div style="margin-top: 40px; text-align: center;">
                    <p style="font-size: 14px;">NCTB AI Learning Hub</p>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX endpoint to get certificate
     */
    public function get_certificate() {
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

        // Check if lesson is completed (progress 100)
        $progress = get_user_meta($user_id, 'nctb_lesson_progress_' . $lesson_id, true);
        if (!$progress || $progress < 100) {
            wp_send_json_error(array('message' => 'Lesson not completed yet'));
        }

        $certificate_html = $this->get_certificate_html($user_id, $lesson_id);
        if (!$certificate_html) {
            wp_send_json_error(array('message' => 'Unable to generate certificate'));
        }

        wp_send_json_success(array(
            'message' => 'Certificate generated',
            'certificate_html' => $certificate_html
        ));
    }

    /**
     * Add certificate tab to student dashboard
     */
    public function add_certificate_tab($tabs) {
        $tabs['certificates'] = array(
            'title' => 'My Certificates',
            'icon' => '🏆',
            'callback' => array($this, 'render_certificates_tab')
        );
        return $tabs;
    }

    /**
     * Render certificates tab content
     */
    public function render_certificates_tab() {
        $user_id = get_current_user_id();
        // Get all lessons the user has completed (progress 100)
        $completed_lessons = array();
        // We'll get all lessons and check progress
        $args = array(
            'post_type' => 'lesson',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        $lessons_query = new WP_Query($args);
        if ($lessons_query->have_posts()) {
            while ($lessons_query->have_posts()) {
                $lessons_query->the_post();
                $lesson_id = get_the_ID();
                $progress = get_user_meta($user_id, 'nctb_lesson_progress_' . $lesson_id, true);
                if ($progress && $progress >= 100) {
                    $completed_lessons[] = $lesson_id;
                }
            }
        }
        wp_reset_postdata();

        if (empty($completed_lessons)) {
            echo '<p>You have not completed any lessons yet. Complete lessons to earn certificates!</p>';
            return;
        }

        echo '<div class="certificates-grid">';
        foreach ($completed_lessons as $lesson_id) {
            $lesson = get_post($lesson_id);
            $awarded_date = get_user_meta($user_id, 'nctb_certificate_awarded_date_' . $lesson_id, true);
            $awarded_date_formatted = $awarded_date ? date('F j, Y', strtotime($awarded_date)) : '';
            ?>
            <div class="certificate-card">
                <div class="card-header">
                    <h3><?php echo esc_html($lesson->post_title); ?></h3>
                    <?php if ($awarded_date_formatted): ?>
                        <p class="date">Awarded: <?php echo esc_html($awarded_date_formatted); ?></p>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p>Congratulations on completing this lesson!</p>
                    <button class="button-primary" onclick="viewCertificate(<?php echo $lesson_id; ?>)">View Certificate</button>
                </div>
            </div>
            <?php
        }
        echo '</div>';

        // Certificate modal
        ?>
        <div id="certificate-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="certificate-content"></div>
            </div>
        </div>
        <?php
    }
}

NCTB_Certificate::instance();