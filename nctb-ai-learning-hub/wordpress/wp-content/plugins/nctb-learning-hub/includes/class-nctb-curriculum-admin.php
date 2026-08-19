<?php
/**
 * Concepts admin screen.
 *
 * A submenu under Lessons where editors manage reusable concepts (stored in the
 * custom nctb_concepts table) without touching code. Add and delete are guarded
 * by capability + nonce; output is escaped.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Curriculum_Admin
 */
class NCTB_Curriculum_Admin {

	const PAGE_SLUG    = 'nctb-concepts';
	const NONCE_ACTION = 'nctb_concepts_manage';
	const CAPABILITY   = 'edit_posts';

	/**
	 * Wire hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_post_nctb_add_concept', array( $this, 'handle_add' ) );
		add_action( 'admin_post_nctb_delete_concept', array( $this, 'handle_delete' ) );
	}

	/**
	 * Register the Concepts submenu under Lessons.
	 *
	 * @return void
	 */
	public function add_menu() {
		add_submenu_page(
			'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON,
			__( 'Concepts', 'nctb-learning-hub' ),
			__( 'Concepts', 'nctb-learning-hub' ),
			self::CAPABILITY,
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Subject choices, reused from the student profile catalog.
	 *
	 * @return array<string,string> slug => label
	 */
	protected function subject_choices() {
		$choices = array();
		foreach ( NCTB_Student_Profile::ALLOWED_SUBJECTS as $slug => $sub ) {
			$choices[ $slug ] = $sub['title_en'];
		}
		return $choices;
	}

	/**
	 * Render the management screen.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to manage concepts.', 'nctb-learning-hub' ) );
		}

		$concepts = NCTB_Curriculum_Data::get_concepts();
		$subjects = $this->subject_choices();
		$notice   = isset( $_GET['nctb_msg'] ) ? sanitize_key( wp_unslash( $_GET['nctb_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Concepts', 'nctb-learning-hub' ); ?></h1>
			<p><?php esc_html_e( 'Reusable teaching concepts. Link them to lessons from the lesson editor.', 'nctb-learning-hub' ); ?></p>

			<?php if ( 'added' === $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Concept added.', 'nctb-learning-hub' ); ?></p></div>
			<?php elseif ( 'deleted' === $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Concept deleted.', 'nctb-learning-hub' ); ?></p></div>
			<?php endif; ?>

			<h2><?php esc_html_e( 'Add a concept', 'nctb-learning-hub' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="nctb_add_concept">
				<?php wp_nonce_field( self::NONCE_ACTION ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="nctb_concept_name"><?php esc_html_e( 'Name', 'nctb-learning-hub' ); ?></label></th>
						<td><input name="name" id="nctb_concept_name" type="text" class="regular-text" required></td>
					</tr>
					<tr>
						<th scope="row"><label for="nctb_concept_subject"><?php esc_html_e( 'Subject', 'nctb-learning-hub' ); ?></label></th>
						<td>
							<select name="subject" id="nctb_concept_subject">
								<option value=""><?php esc_html_e( '— None —', 'nctb-learning-hub' ); ?></option>
								<?php foreach ( $subjects as $slug => $label ) : ?>
									<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="nctb_concept_desc"><?php esc_html_e( 'Description', 'nctb-learning-hub' ); ?></label></th>
						<td><textarea name="description" id="nctb_concept_desc" class="large-text" rows="3"></textarea></td>
					</tr>
				</table>
				<?php submit_button( __( 'Add Concept', 'nctb-learning-hub' ) ); ?>
			</form>

			<h2><?php esc_html_e( 'Existing concepts', 'nctb-learning-hub' ); ?></h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'nctb-learning-hub' ); ?></th>
						<th><?php esc_html_e( 'Subject', 'nctb-learning-hub' ); ?></th>
						<th><?php esc_html_e( 'Description', 'nctb-learning-hub' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'nctb-learning-hub' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $concepts ) ) : ?>
						<tr><td colspan="4"><?php esc_html_e( 'No concepts yet.', 'nctb-learning-hub' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $concepts as $concept ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $concept->name ); ?></strong></td>
								<td><?php echo esc_html( isset( $subjects[ $concept->subject ] ) ? $subjects[ $concept->subject ] : $concept->subject ); ?></td>
								<td><?php echo esc_html( $concept->description ); ?></td>
								<td>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Delete this concept?', 'nctb-learning-hub' ) ); ?>');">
										<input type="hidden" name="action" value="nctb_delete_concept">
										<input type="hidden" name="concept_id" value="<?php echo esc_attr( $concept->id ); ?>">
										<?php wp_nonce_field( self::NONCE_ACTION ); ?>
										<button type="submit" class="button button-link-delete"><?php esc_html_e( 'Delete', 'nctb-learning-hub' ); ?></button>
									</form>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Handle the add-concept form.
	 *
	 * @return void
	 */
	public function handle_add() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Permission denied.', 'nctb-learning-hub' ) );
		}
		check_admin_referer( self::NONCE_ACTION );

		NCTB_Curriculum_Data::create_concept(
			array(
				'name'        => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'subject'     => isset( $_POST['subject'] ) ? sanitize_key( wp_unslash( $_POST['subject'] ) ) : '',
				'description' => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
			)
		);

		$this->redirect_back( 'added' );
	}

	/**
	 * Handle the delete-concept form.
	 *
	 * @return void
	 */
	public function handle_delete() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Permission denied.', 'nctb-learning-hub' ) );
		}
		check_admin_referer( self::NONCE_ACTION );

		$concept_id = isset( $_POST['concept_id'] ) ? absint( wp_unslash( $_POST['concept_id'] ) ) : 0;
		if ( $concept_id ) {
			NCTB_Curriculum_Data::delete_concept( $concept_id );
		}

		$this->redirect_back( 'deleted' );
	}

	/**
	 * Redirect back to the page with a status message.
	 *
	 * @param string $msg Status key.
	 * @return void
	 */
	protected function redirect_back( $msg ) {
		$url = add_query_arg(
			array(
				'post_type' => NCTB_Curriculum_CPT::CPT_LESSON,
				'page'      => self::PAGE_SLUG,
				'nctb_msg'  => $msg,
			),
			admin_url( 'edit.php' )
		);
		wp_safe_redirect( $url );
		exit;
	}
}
