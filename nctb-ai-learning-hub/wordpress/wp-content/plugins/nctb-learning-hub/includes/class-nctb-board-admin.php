<?php
/**
 * Admin Authentic Board Questions Manager (Phase 11).
 *
 * Provides wp-admin interface for reviewing, filtering, and inserting
 * official verified Bangladesh Education Board exam questions with full metadata.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Board_Admin
 */
class NCTB_Board_Admin {

	const MENU_SLUG = 'nctb-board-questions';

	/**
	 * Wire admin hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_post_nctb_add_board_question', array( $this, 'handle_add' ) );
		add_action( 'admin_post_nctb_seed_board_questions', array( $this, 'handle_seed' ) );
	}

	/**
	 * Register submenu under Lessons.
	 */
	public function register_menu() {
		add_submenu_page(
			'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON,
			__( 'Board Questions Archive', 'nctb-learning-hub' ),
			__( 'Board Questions', 'nctb-learning-hub' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_screen' )
		);
	}

	/**
	 * Handle add board question.
	 */
	public function handle_add() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'nctb-learning-hub' ) );
		}

		check_admin_referer( 'nctb_board_admin_action', 'nctb_nonce' );

		$data = array(
			'exam_level'       => sanitize_key( $_POST['exam_level'] ?? 'ssc' ),
			'board_name'       => sanitize_key( $_POST['board_name'] ?? 'dhaka' ),
			'exam_year'        => absint( $_POST['exam_year'] ?? 2023 ),
			'subject'          => sanitize_key( $_POST['subject'] ?? 'english_1st' ),
			'paper'            => sanitize_key( $_POST['paper'] ?? '1st' ),
			'question_no'      => sanitize_text_field( $_POST['question_no'] ?? '1' ),
			'marks'            => floatval( $_POST['marks'] ?? 1.0 ),
			'question_type'    => sanitize_key( $_POST['question_type'] ?? 'mcq' ),
			'topic'            => sanitize_text_field( $_POST['topic'] ?? '' ),
			'question_text'    => wp_kses_post( $_POST['question_text'] ?? '' ),
			'verified_answer'  => wp_kses_post( $_POST['verified_answer'] ?? '' ),
			'explanation'      => wp_kses_post( $_POST['explanation'] ?? '' ),
			'source_reference' => sanitize_text_field( $_POST['source_reference'] ?? '' ),
		);

		NCTB_Board_Service::add_board_question( $data );

		wp_safe_redirect( add_query_arg( array( 'page' => self::MENU_SLUG, 'message' => 'added' ), admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON ) ) );
		exit;
	}

	/**
	 * Handle seeding sample authentic board questions.
	 */
	public function handle_seed() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'nctb-learning-hub' ) );
		}

		check_admin_referer( 'nctb_board_admin_action', 'nctb_nonce' );

		$count = NCTB_Board_Service::seed_sample_board_questions();

		wp_safe_redirect( add_query_arg( array( 'page' => self::MENU_SLUG, 'message' => 'seeded', 'count' => $count ), admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON ) ) );
		exit;
	}

	/**
	 * Render admin screen.
	 */
	public function render_screen() {
		$questions = NCTB_Board_Service::get_board_questions( array( 'limit' => 100 ) );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Official Board Questions Repository', 'nctb-learning-hub' ); ?></h1>

			<?php if ( isset( $_GET['message'] ) && 'seeded' === $_GET['message'] ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Sample authentic board questions seeded successfully.', 'nctb-learning-hub' ); ?></p></div>
			<?php elseif ( isset( $_GET['message'] ) && 'added' === $_GET['message'] ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Board question added successfully.', 'nctb-learning-hub' ); ?></p></div>
			<?php endif; ?>

			<div style="margin: 1rem 0;">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;">
					<?php wp_nonce_field( 'nctb_board_admin_action', 'nctb_nonce' ); ?>
					<input type="hidden" name="action" value="nctb_seed_board_questions">
					<button type="submit" class="button button-secondary">🌱 <?php esc_html_e( 'Seed Authentic Historical Board Questions (SSC & HSC)', 'nctb-learning-hub' ); ?></button>
				</form>
			</div>

			<div style="display:flex; gap:2rem; margin-top:1.5rem;">
				<!-- Questions Table -->
				<div style="flex:2;">
					<h2><?php esc_html_e( 'Verified Board Questions', 'nctb-learning-hub' ); ?> (<?php echo count( $questions ); ?>)</h2>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th style="width:18%;"><?php esc_html_e( 'Board & Year', 'nctb-learning-hub' ); ?></th>
								<th style="width:12%;"><?php esc_html_e( 'Q No & Type', 'nctb-learning-hub' ); ?></th>
								<th><?php esc_html_e( 'Question & Topic', 'nctb-learning-hub' ); ?></th>
								<th style="width:25%;"><?php esc_html_e( 'Verified Source', 'nctb-learning-hub' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if ( empty( $questions ) ) : ?>
								<tr><td colspan="4"><?php esc_html_e( 'No board questions added yet. Click Seed above to load authentic historical exam items.', 'nctb-learning-hub' ); ?></td></tr>
							<?php else : ?>
								<?php foreach ( $questions as $q ) : ?>
									<tr>
										<td><strong><?php echo esc_html( strtoupper( $q->exam_level ) . ' ' . ucfirst( $q->board_name ) . ' ' . $q->exam_year ); ?></strong></td>
										<td><code>Q<?php echo esc_html( $q->question_no ); ?> (<?php echo esc_html( $q->question_type ); ?>)</code></td>
										<td>
											<strong><?php echo esc_html( $q->topic ); ?></strong><br>
											<?php echo esc_html( wp_trim_words( $q->question_text, 15 ) ); ?>
										</td>
										<td><small><?php echo esc_html( $q->source_reference ); ?></small></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<!-- Add Board Question Form -->
				<div style="flex:1; background:#fff; padding:1.25rem; border:1px solid #ccd0d4; border-radius:8px; height:fit-content;">
					<h2><?php esc_html_e( 'Add Verified Board Question', 'nctb-learning-hub' ); ?></h2>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php wp_nonce_field( 'nctb_board_admin_action', 'nctb_nonce' ); ?>
						<input type="hidden" name="action" value="nctb_add_board_question">

						<p>
							<label><strong><?php esc_html_e( 'Exam Level:', 'nctb-learning-hub' ); ?></strong></label><br>
							<select name="exam_level" style="width:100%;">
								<option value="ssc">SSC</option>
								<option value="hsc">HSC</option>
							</select>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Board Name:', 'nctb-learning-hub' ); ?></strong></label><br>
							<select name="board_name" style="width:100%;">
								<?php foreach ( NCTB_Board_Service::BOARDS as $code => $lbl ) : ?>
									<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $lbl ); ?></option>
								<?php endforeach; ?>
							</select>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Exam Year:', 'nctb-learning-hub' ); ?></strong></label><br>
							<input type="number" name="exam_year" value="2023" style="width:100%;" required>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Question No & Marks:', 'nctb-learning-hub' ); ?></strong></label><br>
							<input type="text" name="question_no" value="1" placeholder="e.g. 1.A" style="width:48%;">
							<input type="number" step="0.5" name="marks" value="5.0" style="width:48%;">
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Question Type:', 'nctb-learning-hub' ); ?></strong></label><br>
							<select name="question_type" style="width:100%;">
								<option value="mcq">MCQ</option>
								<option value="short_answer">Short Answer</option>
								<option value="fill_in_blank">Fill in the blank</option>
								<option value="flow_chart">Flow Chart</option>
								<option value="summary">Summary</option>
								<option value="theme">Theme Writing</option>
							</select>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Topic / Chapter Title:', 'nctb-learning-hub' ); ?></strong></label><br>
							<input type="text" name="topic" style="width:100%;" placeholder="e.g. Nelson Mandela">
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Question Text:', 'nctb-learning-hub' ); ?></strong></label><br>
							<textarea name="question_text" rows="3" style="width:100%;" required></textarea>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Verified Official Answer:', 'nctb-learning-hub' ); ?></strong></label><br>
							<textarea name="verified_answer" rows="2" style="width:100%;" required></textarea>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Marking Scheme / Explanation:', 'nctb-learning-hub' ); ?></strong></label><br>
							<textarea name="explanation" rows="2" style="width:100%;"></textarea>
						</p>

						<p>
							<label><strong><?php esc_html_e( 'Source Reference:', 'nctb-learning-hub' ); ?></strong></label><br>
							<input type="text" name="source_reference" style="width:100%;" placeholder="e.g. Dhaka Board HSC 2023 Q1">
						</p>

						<p>
							<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Verified Board Question', 'nctb-learning-hub' ); ?></button>
						</p>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}
