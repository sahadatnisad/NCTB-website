<?php
/**
 * Admin Entitlements & Access Grants Manager (Phase 8).
 *
 * Provides wp-admin interface for reviewing active student passes,
 * manually granting access, extending durations, and revoking entitlements
 * with mandatory audit logging.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Entitlements_Admin
 */
class NCTB_Entitlements_Admin {

	const MENU_SLUG = 'nctb-entitlements';

	/**
	 * Wire admin hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_post_nctb_grant_entitlement', array( $this, 'handle_grant' ) );
		add_action( 'admin_post_nctb_revoke_entitlement', array( $this, 'handle_revoke' ) );
	}

	/**
	 * Register submenu under Lessons.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_submenu_page(
			'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON,
			__( 'Entitlements & Passes', 'nctb-learning-hub' ),
			__( 'Entitlements', 'nctb-learning-hub' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_screen' )
		);
	}

	/**
	 * Handle admin manual grant action.
	 *
	 * @return void
	 */
	public function handle_grant() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'nctb-learning-hub' ) );
		}

		check_admin_referer( 'nctb_entitlement_admin_action', 'nctb_nonce' );

		$user_id   = absint( $_POST['user_id'] ?? 0 );
		$ent_type  = sanitize_key( $_POST['entitlement_type'] ?? 'direct_lesson' );
		$item_type = sanitize_key( $_POST['item_type'] ?? 'lesson' );
		$item_id   = absint( $_POST['item_id'] ?? 0 );
		$days      = absint( $_POST['duration_days'] ?? 0 );
		$notes     = sanitize_textarea_field( $_POST['notes'] ?? '' );

		if ( ! $user_id ) {
			wp_safe_redirect( add_query_arg( array( 'page' => self::MENU_SLUG, 'error' => 'missing_user' ), admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON ) ) );
			exit;
		}

		$expires_at = null;
		if ( $days > 0 ) {
			$expires_at = gmdate( 'Y-m-d H:i:s', strtotime( "+{$days} days", current_time( 'timestamp', true ) ) );
		}

		NCTB_Entitlements::grant_entitlement(
			$user_id,
			$ent_type,
			$item_type,
			$item_id,
			'manual',
			'admin_grant',
			get_current_user_id(),
			$expires_at,
			$notes ?: 'Manual grant by administrator'
		);

		wp_safe_redirect( add_query_arg( array( 'page' => self::MENU_SLUG, 'message' => 'granted' ), admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON ) ) );
		exit;
	}

	/**
	 * Handle admin revoke action.
	 *
	 * @return void
	 */
	public function handle_revoke() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'nctb-learning-hub' ) );
		}

		check_admin_referer( 'nctb_entitlement_admin_action', 'nctb_nonce' );

		$ent_id = absint( $_POST['entitlement_id'] ?? 0 );
		$notes  = sanitize_textarea_field( $_POST['notes'] ?? '' );

		if ( $ent_id ) {
			NCTB_Entitlements::revoke_entitlement( $ent_id, get_current_user_id(), $notes ?: 'Revoked by admin' );
		}

		wp_safe_redirect( add_query_arg( array( 'page' => self::MENU_SLUG, 'message' => 'revoked' ), admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON ) ) );
		exit;
	}

	/**
	 * Render Entitlements admin screen.
	 *
	 * @return void
	 */
	public function render_screen() {
		global $wpdb;
		$ent_table = NCTB_Migrations::table( 'entitlements' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results( "SELECT * FROM {$ent_table} ORDER BY id DESC LIMIT 100" );
		$users = get_users( array( 'number' => 100, 'fields' => array( 'ID', 'display_name', 'user_email' ) ) );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Student Entitlements & Access Grants', 'nctb-learning-hub' ); ?></h1>

			<?php if ( isset( $_GET['message'] ) && 'granted' === $_GET['message'] ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Entitlement granted successfully.', 'nctb-learning-hub' ); ?></p></div>
			<?php elseif ( isset( $_GET['message'] ) && 'revoked' === $_GET['message'] ) : ?>
				<div class="notice notice-warning is-dismissible"><p><?php esc_html_e( 'Entitlement revoked.', 'nctb-learning-hub' ); ?></p></div>
			<?php endif; ?>

			<div style="display:flex; gap:2rem; margin-top:1.5rem;">
				<!-- List Active & Historical Entitlements -->
				<div style="flex:2;">
					<h2><?php esc_html_e( 'Granted Entitlements', 'nctb-learning-hub' ); ?></h2>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Student', 'nctb-learning-hub' ); ?></th>
								<th><?php esc_html_e( 'Type', 'nctb-learning-hub' ); ?></th>
								<th><?php esc_html_e( 'Target Item', 'nctb-learning-hub' ); ?></th>
								<th><?php esc_html_e( 'Source', 'nctb-learning-hub' ); ?></th>
								<th><?php esc_html_e( 'Status', 'nctb-learning-hub' ); ?></th>
								<th><?php esc_html_e( 'Expires', 'nctb-learning-hub' ); ?></th>
								<th><?php esc_html_e( 'Actions', 'nctb-learning-hub' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if ( empty( $rows ) ) : ?>
								<tr><td colspan="7"><?php esc_html_e( 'No entitlements recorded yet.', 'nctb-learning-hub' ); ?></td></tr>
							<?php else : ?>
								<?php foreach ( $rows as $r ) :
									$user = get_userdata( $r->user_id );
									$item_label = ( 'all_access' === $r->item_type ) ? __( 'All-Access (Everything)', 'nctb-learning-hub' ) : ( get_the_title( $r->item_id ) ?: ucfirst( $r->item_type ) . ' #' . $r->item_id );
								?>
									<tr>
										<td><strong><?php echo esc_html( $user ? $user->display_name : 'User #' . $r->user_id ); ?></strong><br><small><?php echo esc_html( $user ? $user->user_email : '' ); ?></small></td>
										<td><code><?php echo esc_html( $r->entitlement_type ); ?></code></td>
										<td><?php echo esc_html( $item_label ); ?></td>
										<td><?php echo esc_html( $r->source_type ); ?></td>
										<td><span class="badge"><?php echo esc_html( $r->status ); ?></span></td>
										<td><?php echo esc_html( $r->expires_at ? gmdate( 'd M Y', strtotime( $r->expires_at ) ) : __( 'Lifetime', 'nctb-learning-hub' ) ); ?></td>
										<td>
											<?php if ( 'active' === $r->status ) : ?>
												<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;" onsubmit="return confirm('Revoke this entitlement?');">
													<?php wp_nonce_field( 'nctb_entitlement_admin_action', 'nctb_nonce' ); ?>
													<input type="hidden" name="action" value="nctb_revoke_entitlement">
													<input type="hidden" name="entitlement_id" value="<?php echo esc_attr( $r->id ); ?>">
													<button type="submit" class="button button-small button-link-delete"><?php esc_html_e( 'Revoke', 'nctb-learning-hub' ); ?></button>
												</form>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<!-- Grant Entitlement Form -->
				<div style="flex:1; background:#fff; padding:1.25rem; border:1px solid #ccd0d4; border-radius:8px; height:fit-content;">
					<h2><?php esc_html_e( 'Grant Access Manually', 'nctb-learning-hub' ); ?></h2>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php wp_nonce_field( 'nctb_entitlement_admin_action', 'nctb_nonce' ); ?>
						<input type="hidden" name="action" value="nctb_grant_entitlement">

						<p>
							<label><strong><?php esc_html_e( 'Student User:', 'nctb-learning-hub' ); ?></strong></label><br>
							<select name="user_id" style="width:100%;" required>
								<option value=""><?php esc_html_e( '— Select Student —', 'nctb-learning-hub' ); ?></option>
								<?php foreach ( $users as $u ) : ?>
									<option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name . ' (' . $u->user_email . ')' ); ?></option>
								<?php endforeach; ?>
							</select>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Entitlement Type:', 'nctb-learning-hub' ); ?></strong></label><br>
							<select name="entitlement_type" style="width:100%;">
								<option value="direct_lesson"><?php esc_html_e( 'Single Lesson Pass', 'nctb-learning-hub' ); ?></option>
								<option value="pack_unit"><?php esc_html_e( 'Unit Pack', 'nctb-learning-hub' ); ?></option>
								<option value="pack_book"><?php esc_html_e( 'Full Book Pack', 'nctb-learning-hub' ); ?></option>
								<option value="subscription"><?php esc_html_e( 'All-Access Subscription', 'nctb-learning-hub' ); ?></option>
								<option value="admin_grant"><?php esc_html_e( 'Admin Free Grant', 'nctb-learning-hub' ); ?></option>
							</select>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Item Scope:', 'nctb-learning-hub' ); ?></strong></label><br>
							<select name="item_type" style="width:100%;">
								<option value="lesson"><?php esc_html_e( 'Lesson', 'nctb-learning-hub' ); ?></option>
								<option value="unit"><?php esc_html_e( 'Unit', 'nctb-learning-hub' ); ?></option>
								<option value="book"><?php esc_html_e( 'Book', 'nctb-learning-hub' ); ?></option>
								<option value="all_access"><?php esc_html_e( 'All-Access (All Curriculum)', 'nctb-learning-hub' ); ?></option>
							</select>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Target Item Post ID (0 for all-access):', 'nctb-learning-hub' ); ?></strong></label><br>
							<input type="number" name="item_id" value="0" style="width:100%;">
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Duration in Days (0 = Lifetime):', 'nctb-learning-hub' ); ?></strong></label><br>
							<input type="number" name="duration_days" value="0" style="width:100%;">
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Audit Note / Reason:', 'nctb-learning-hub' ); ?></strong></label><br>
							<textarea name="notes" rows="2" style="width:100%;" placeholder="<?php esc_attr_e( 'e.g. Scholarship grant or support resolution', 'nctb-learning-hub' ); ?>"></textarea>
						</p>

						<p>
							<button type="submit" class="button button-primary"><?php esc_html_e( 'Grant Entitlement', 'nctb-learning-hub' ); ?></button>
						</p>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}
