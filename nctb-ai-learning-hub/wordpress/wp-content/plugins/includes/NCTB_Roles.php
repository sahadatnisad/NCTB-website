<?php
/**
 * NCTB Roles
 * Manages user role assignments and permissions
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Roles {
    private static $instance = null;
    private $role_capabilities = array(
        'student' => array('read', 'edit_posts'),
        'teacher' => array('edit_posts', 'delete_posts', 'publish_posts'),
        'admin' => array('manage_options', 'edit_users', 'delete_users'),
    );

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('user_register', array($this, 'assign_default_role'));
        add_action('admin_notices', array($this, 'show_role_notice'));
        add_filter('user_has_cap', array($this, 'restrict_capabilities'), 10, 3);
    }

    public function assign_default_role($user_id) {
        $user = new WP_User($user_id);
        $roles = $user->roles;
        if (empty($roles)) {
            // Assign student role by default
            $user->add_role('subscriber');
            // Add to student group
            update_user_meta($user_id, 'nctb_user_group', 'student');
        }
    }

    public function show_role_notice() {
        $user = wp_get_current_user();
        $roles = $user->roles;
        if (!empty($roles)) {
            $role = array_shift($roles);
            if ($role === 'subscriber') {
                echo '<div class="notice notice-info">';
                echo '<p><strong>' . __('Your role: Student', 'nctb-learning-hub') . '</strong></p>';
                echo '<p>' . __('You can view lessons and track your progress.', 'nctb-learning-hub') . '</p>';
                echo '</div>';
            } elseif ($role === 'author' || $role === 'editor' || $role === 'administrator') {
                echo '<div class="notice notice-success">';
                echo '<p><strong>' . __('Your role: Teacher/Admin', 'nctb-learning-hub') . '</strong></p>';
                echo '<p>' . __('You can create, edit, and manage lessons.', 'nctb-learning-hub') . '</p>';
                echo '</div>';
            }
        }
    }

    public function restrict_capabilities($user_caps, $required_caps, $args) {
        $user_id = isset($args['user_id']) ? $args['user_id'] : get_current_user_id();
        $user = new WP_User($user_id);
        $roles = $user->roles;
        $role = !empty($roles) ? array_shift($roles) : '';

        if ($role === 'subscriber') {
            // Students can only read and edit their own posts
            foreach ($required_caps as $cap) {
                if (!in_array($cap, $this->role_capabilities['student'])) {
                    unset($user_caps[$cap]);
                }
            }
        }

        return $user_caps;
    }

    public function get_role_capabilities($role) {
        return isset($this->role_capabilities[$role]) ? $this->role_capabilities[$role] : array();
    }

    public function is_student($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        return in_array('subscriber', (array)wp_get_current_user()->roles);
    }

    public function is_teacher($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $roles = (array)wp_get_current_user()->roles;
        return in_array('author', $roles) || in_array('editor', $roles) || in_array('administrator', $roles);
    }

    public function is_admin($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        return in_array('administrator', (array)wp_get_current_user()->roles);
    }

    public function get_user_group($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        return get_user_meta($user_id, 'nctb_user_group', true);
    }

    public function set_user_group($user_id, $group) {
        update_user_meta($user_id, 'nctb_user_group', $group);
    }
}

NCTB_Roles::instance();