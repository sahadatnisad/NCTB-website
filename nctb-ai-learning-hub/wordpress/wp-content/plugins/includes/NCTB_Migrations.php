<?php
/**
 * NCTB Migrations
 * Handles database schema migrations for the learning hub
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Migrations {
    private static $instance = null;
    private $version = '1.0.0';

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('init', array($this, 'run_migrations'));
    }

    public function run_migrations() {
        // Register post types and taxonomies
        $this->create_lesson_post_type();
        $this->create_subject_taxonomy();

        // Update version option if needed
        if (get_option('nctb_version') !== $this->version) {
            update_option('nctb_version', $this->version);
        }
    }

    private function create_lesson_post_type() {
        if (!post_type_exists('lesson')) {
            register_post_type('lesson', array(
                'labels' => array(
                    'name' => 'Lessons',
                    'singular_name' => 'Lesson'
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'editor', 'thumbnail'),
                'rewrite' => array('slug' => 'lessons'),
                'show_in_menu' => true,
            ));
        }
    }

    private function create_subject_taxonomy() {
        if (!taxonomy_exists('subject')) {
            register_taxonomy('subject', 'lesson', array(
                'label' => 'Subject',
                'rewrite' => array('slug' => 'subject'),
                'hierarchical' => true,
                'show_in_menu' => true,
            ));
        }
    }
}

NCTB_Migrations::instance();
