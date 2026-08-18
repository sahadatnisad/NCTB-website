<?php
/**
 * NCTB Logger
 * Simple logging system for the learning hub
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Logger {
    private static $instance = null;
    private $log_file = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $uploads_dir = wp_upload_dir();
        $this->log_file = trailingslashit($uploads_dir['basedir']) . 'nctb-logs.log';
    }

    public function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] [$level] $message
";
        @file_put_contents($this->log_file, $log_entry, FILE_APPEND);
    }

    public function error($message) {
        $this->log($message, 'ERROR');
    }

    public function warning($message) {
        $this->log($message, 'WARNING');
    }

    public function info($message) {
        $this->log($message, 'INFO');
    }
}

NCTB_Logger::instance();
