<?php
/**
 * Uninstall handler.
 *
 * Runs ONLY when the site owner deletes the plugin from WordPress. Removes
 * the plugin's own options. It deliberately does NOT drop learning tables in
 * Phase 0 because none exist yet; later phases must add explicit, documented
 * data-removal here (guarded so accidental deletion cannot wipe student data
 * without intent).
 *
 * @package NCTB\LearningHub
 */

// Exit if not called by WordPress uninstall lifecycle.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove Phase 0 options.
delete_option( 'nctb_lh_db_version' );
delete_option( 'nctb_lh_installed_at' );

/*
 * NOTE for later phases: dropping student data (attempts, mastery, mistakes,
 * writing samples, AI logs) is destructive and irreversible. Guard any such
 * removal behind an explicit site-owner setting, and document it in
 * docs/BACKUP_RESTORE.md before enabling.
 */
