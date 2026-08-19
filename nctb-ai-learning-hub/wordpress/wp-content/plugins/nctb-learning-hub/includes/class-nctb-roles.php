<?php
/**
 * Role management for NCTB Learning Hub.
 *
 * Registers the student role (`nctb_student`) with appropriate capabilities.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Roles
 */
class NCTB_Roles {

	/**
	 * Register custom roles and capabilities.
	 *
	 * @return void
	 */
	public static function register_roles() {
		// Add student role if not already present.
		if ( ! get_role( 'nctb_student' ) ) {
			add_role(
				'nctb_student',
				__( 'Student', 'nctb-learning-hub' ),
				array(
					'read'                 => true,
					'view_nctb_content'    => true,
					'edit_nctb_profile'    => true,
					'submit_nctb_practice' => true,
					'upload_files'         => false,
					'edit_posts'           => false,
					'delete_posts'         => false,
				)
			);
		}

		// Add teacher role if not already present (Phase 16).
		if ( ! get_role( 'nctb_teacher' ) ) {
			add_role(
				'nctb_teacher',
				__( 'Teacher / Educator', 'nctb-learning-hub' ),
				array(
					'read'                           => true,
					'view_nctb_content'              => true,
					'nctb_access_teacher_portal'     => true,
					'nctb_manage_teacher_profile'    => true,
					'nctb_download_teacher_resources'=> true,
					'nctb_use_teacher_ai'            => true,
					'upload_files'                   => false,
					'edit_posts'                     => false,
					'delete_posts'                   => false,
				)
			);
		}

		// Ensure administrators also have student and teacher capabilities.
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( 'view_nctb_content' );
			$admin->add_cap( 'edit_nctb_profile' );
			$admin->add_cap( 'submit_nctb_practice' );
			$admin->add_cap( 'nctb_access_teacher_portal' );
			$admin->add_cap( 'nctb_manage_teacher_profile' );
			$admin->add_cap( 'nctb_download_teacher_resources' );
			$admin->add_cap( 'nctb_use_teacher_ai' );
		}
	}
}
