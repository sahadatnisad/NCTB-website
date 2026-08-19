<?php
/**
 * Content Library & Human Review Workflow Admin Screen — Phase 13.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Content_Library_Admin
 */
class NCTB_Content_Library_Admin {

	/**
	 * Register admin submenu and handlers.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_actions' ) );
	}

	/**
	 * Register submenu under Lessons.
	 *
	 * @return void
	 */
	public static function register_menu() {
		add_submenu_page(
			'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON,
			__( 'Content Library', 'nctb-learning-hub' ),
			__( 'Content Library', 'nctb-learning-hub' ),
			'manage_options',
			'nctb-content-library',
			array( __CLASS__, 'render_screen' )
		);
	}

	/**
	 * Handle admin actions: seed library or update review status.
	 *
	 * @return void
	 */
	public static function handle_actions() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['nctb_seed_mvp_library'] ) && check_admin_referer( 'nctb_seed_mvp_library_nonce' ) ) {
			$stats = NCTB_Content_Library_Service::seed_mvp_content_library();
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => 'nctb-content-library',
						'seeded'  => '1',
						'lessons' => $stats['lessons'],
					),
					admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON )
				)
			);
			exit;
		}

		if ( isset( $_POST['nctb_update_review_status'] ) && check_admin_referer( 'nctb_review_action_nonce' ) ) {
			$lesson_id = isset( $_POST['lesson_id'] ) ? absint( $_POST['lesson_id'] ) : 0;
			$status    = isset( $_POST['review_status'] ) ? sanitize_key( $_POST['review_status'] ) : 'reviewed';
			$notes     = isset( $_POST['reviewer_notes'] ) ? sanitize_textarea_field( $_POST['reviewer_notes'] ) : '';

			if ( $lesson_id ) {
				update_post_meta( $lesson_id, NCTB_Content_Library_Service::REVIEW_META_KEY, $status );
				if ( ! empty( $notes ) ) {
					update_post_meta( $lesson_id, NCTB_Content_Library_Service::REVIEWER_NOTE_KEY, $notes );
				}
			}

			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => 'nctb-content-library',
						'updated' => '1',
					),
					admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON )
				)
			);
			exit;
		}
	}

	/**
	 * Render the management screen.
	 *
	 * @return void
	 */
	public static function render_screen() {
		$summary = NCTB_Content_Library_Service::get_library_summary();
		$lessons = get_posts(
			array(
				'post_type'   => NCTB_Curriculum_CPT::CPT_LESSON,
				'post_status' => 'publish',
				'numberposts' => 50,
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			)
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'English MVP Content Library & Human Review', 'nctb-learning-hub' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Manage the official NCTB SSC & HSC English curriculum library, track lesson decomposition, practice questions, and editorial review workflow.', 'nctb-learning-hub' ); ?></p>

			<?php if ( isset( $_GET['seeded'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php printf( esc_html__( 'Content library seeded successfully! Added %d lessons.', 'nctb-learning-hub' ), (int) $_GET['lessons'] ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( isset( $_GET['updated'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Lesson review status updated successfully.', 'nctb-learning-hub' ); ?></p>
				</div>
			<?php endif; ?>

			<!-- KPIs -->
			<div style="display:flex;gap:1.5rem;margin:1.5rem 0;flex-wrap:wrap;">
				<div style="background:#fff;border:1px solid #ccd0d4;border-radius:8px;padding:1.25rem 1.75rem;min-width:180px;">
					<span style="font-size:0.85rem;color:#646970;display:block;"><?php esc_html_e( 'Published Lessons', 'nctb-learning-hub' ); ?></span>
					<strong style="font-size:1.8rem;color:#0b6e4f;"><?php echo esc_html( $summary['total_lessons'] ); ?></strong>
				</div>
				<div style="background:#fff;border:1px solid #ccd0d4;border-radius:8px;padding:1.25rem 1.75rem;min-width:180px;">
					<span style="font-size:0.85rem;color:#646970;display:block;"><?php esc_html_e( 'Curriculum Units', 'nctb-learning-hub' ); ?></span>
					<strong style="font-size:1.8rem;color:#1e40af;"><?php echo esc_html( $summary['total_units'] ); ?></strong>
				</div>
				<div style="background:#fff;border:1px solid #ccd0d4;border-radius:8px;padding:1.25rem 1.75rem;min-width:180px;">
					<span style="font-size:0.85rem;color:#646970;display:block;"><?php esc_html_e( 'Textbooks Covered', 'nctb-learning-hub' ); ?></span>
					<strong style="font-size:1.8rem;color:#334155;">SSC & HSC English</strong>
				</div>
			</div>

			<!-- Seed Library Action -->
			<div style="background:#fff;border:1px solid #ccd0d4;border-radius:8px;padding:1.5rem;margin-bottom:2rem;">
				<h2><?php esc_html_e( 'Seed English MVP Library (49 Authentic Lessons)', 'nctb-learning-hub' ); ?></h2>
				<p><?php esc_html_e( 'Populates 25 SSC English lessons (8 units) and 24 HSC English lessons (8 units) with learning outcomes, 14 standard activity blocks, practice question banks, 3-level progressive hints, and vocabulary records.', 'nctb-learning-hub' ); ?></p>
				<form method="post">
					<?php wp_nonce_field( 'nctb_seed_mvp_library_nonce' ); ?>
					<input type="submit" name="nctb_seed_mvp_library" class="button button-primary button-large" value="<?php esc_attr_e( 'Seed / Sync 49 English Lessons', 'nctb-learning-hub' ); ?>">
				</form>
			</div>

			<!-- Lessons Table -->
			<h2><?php esc_html_e( 'Curriculum Lessons & Review Status', 'nctb-learning-hub' ); ?></h2>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Lesson Title', 'nctb-learning-hub' ); ?></th>
						<th><?php esc_html_e( 'Unit & Book', 'nctb-learning-hub' ); ?></th>
						<th><?php esc_html_e( 'Review Status', 'nctb-learning-hub' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'nctb-learning-hub' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $lessons ) ) : ?>
						<tr><td colspan="4"><?php esc_html_e( 'No lessons found.', 'nctb-learning-hub' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $lessons as $l ) : ?>
							<?php
							$unit_id = get_post_meta( $l->ID, NCTB_Curriculum_CPT::META_UNIT_ID, true );
							$unit_title = $unit_id ? get_the_title( $unit_id ) : '—';
							$status = get_post_meta( $l->ID, NCTB_Content_Library_Service::REVIEW_META_KEY, true );
							if ( empty( $status ) ) {
								$status = 'reviewed';
							}
							?>
							<tr>
								<td><strong><a href="<?php echo esc_url( get_edit_post_link( $l->ID ) ); ?>"><?php echo esc_html( $l->post_title ); ?></a></strong></td>
								<td><?php echo esc_html( $unit_title ); ?></td>
								<td>
									<span class="badge" style="padding:3px 8px;border-radius:4px;font-weight:bold;background:<?php echo 'published' === $status ? '#dcfce7;color:#166534' : ('reviewed' === $status ? '#eff6ff;color:#1e40af' : '#fef3c7;color:#92400e'); ?>;">
										<?php echo esc_html( ucfirst( $status ) ); ?>
									</span>
								</td>
								<td>
									<a href="<?php echo esc_url( get_permalink( $l->ID ) ); ?>" class="button button-small" target="_blank"><?php esc_html_e( 'View Interactive Lesson', 'nctb-learning-hub' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
