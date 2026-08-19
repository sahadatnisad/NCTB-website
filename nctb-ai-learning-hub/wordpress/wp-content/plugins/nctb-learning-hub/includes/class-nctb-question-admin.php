<?php
/**
 * Admin Practice Questions Manager (Phase 5).
 *
 * Dedicated admin screen under Lessons → Practice Questions to create, edit,
 * list, and delete practice questions, MCQ options, progressive hints, and
 * concept tags.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Question_Admin
 */
class NCTB_Question_Admin {

	const MENU_SLUG    = 'nctb-questions';
	const NONCE_ACTION = 'nctb_question_admin_action';
	const NONCE_FIELD  = 'nctb_question_admin_nonce';

	/**
	 * Wire admin hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_post_nctb_save_question', array( $this, 'handle_save' ) );
		add_action( 'admin_post_nctb_delete_question', array( $this, 'handle_delete' ) );
	}

	/**
	 * Register submenu under Lessons.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_submenu_page(
			'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON,
			__( 'Practice Questions', 'nctb-learning-hub' ),
			__( 'Questions (Phase 5)', 'nctb-learning-hub' ),
			'edit_posts',
			self::MENU_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the questions admin page.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nctb-learning-hub' ) );
		}

		$action      = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'list';
		$question_id = isset( $_GET['question_id'] ) ? absint( $_GET['question_id'] ) : 0;
		$filter_lid  = isset( $_GET['lesson_id'] ) ? absint( $_GET['lesson_id'] ) : 0;

		$lessons   = get_posts( array( 'post_type' => NCTB_Curriculum_CPT::CPT_LESSON, 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		$all_types = NCTB_Question_Types::get_all();

		echo '<div class="wrap">';
		echo '<h1>📝 ' . esc_html__( 'Practice Questions Manager', 'nctb-learning-hub' ) . '</h1>';

		if ( isset( $_GET['message'] ) ) {
			$msg = sanitize_text_field( wp_unslash( $_GET['message'] ) );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
		}

		if ( 'edit' === $action || 'new' === $action ) {
			$question = $question_id ? NCTB_Practice_Data::get_question( $question_id, true ) : null;
			$this->render_form( $question, $lessons, $all_types );
		} else {
			$this->render_list( $lessons, $all_types, $filter_lid );
		}

		echo '</div>';
	}

	/**
	 * Render questions list table.
	 *
	 * @param array $lessons   All lessons.
	 * @param array $all_types Question types.
	 * @param int   $filter_lid Current lesson filter.
	 * @return void
	 */
	protected function render_list( array $lessons, array $all_types, $filter_lid = 0 ) {
		global $wpdb;
		$table = NCTB_Migrations::table( 'questions' );

		if ( $filter_lid ) {
			$sql = $wpdb->prepare( "SELECT * FROM {$table} WHERE lesson_id = %d ORDER BY sort_order ASC, id ASC", $filter_lid ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		} else {
			$sql = "SELECT * FROM {$table} ORDER BY lesson_id ASC, sort_order ASC, id ASC"; // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$questions = $wpdb->get_results( $sql );
		$new_url   = admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON . '&page=' . self::MENU_SLUG . '&action=new' );
		?>
		<div class="tablenav top" style="display:flex;justify-content:space-between;align-items:center;margin:15px 0;">
			<form method="get" action="">
				<input type="hidden" name="post_type" value="<?php echo esc_attr( NCTB_Curriculum_CPT::CPT_LESSON ); ?>">
				<input type="hidden" name="page" value="<?php echo esc_attr( self::MENU_SLUG ); ?>">
				<label for="filter-lesson"><?php esc_html_e( 'Filter by Lesson:', 'nctb-learning-hub' ); ?></label>
				<select name="lesson_id" id="filter-lesson" onchange="this.form.submit()">
					<option value="0"><?php esc_html_e( '— All Lessons —', 'nctb-learning-hub' ); ?></option>
					<?php foreach ( $lessons as $l ) : ?>
						<option value="<?php echo esc_attr( $l->ID ); ?>" <?php selected( $filter_lid, $l->ID ); ?>>
							<?php echo esc_html( $l->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</form>
			<a href="<?php echo esc_url( $new_url ); ?>" class="button button-primary">➕ <?php esc_html_e( 'Add New Question', 'nctb-learning-hub' ); ?></a>
		</div>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
					<th style="width:90px;"><?php esc_html_e( 'Type', 'nctb-learning-hub' ); ?></th>
					<th><?php esc_html_e( 'Question Prompt', 'nctb-learning-hub' ); ?></th>
					<th style="width:180px;"><?php esc_html_e( 'Lesson', 'nctb-learning-hub' ); ?></th>
					<th style="width:80px;"><?php esc_html_e( 'Difficulty', 'nctb-learning-hub' ); ?></th>
					<th style="width:70px;"><?php esc_html_e( 'Hints', 'nctb-learning-hub' ); ?></th>
					<th style="width:130px;"><?php esc_html_e( 'Actions', 'nctb-learning-hub' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $questions ) ) : ?>
					<tr>
						<td colspan="7" style="text-align:center;padding:20px;">
							<?php esc_html_e( 'No practice questions found. Click "Add New Question" above.', 'nctb-learning-hub' ); ?>
						</td>
					</tr>
				<?php else : ?>
					<?php foreach ( $questions as $q ) :
						$type_info = $all_types[ $q->question_type ] ?? null;
						$edit_url  = admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON . '&page=' . self::MENU_SLUG . '&action=edit&question_id=' . $q->id );
						$del_url   = wp_nonce_url( admin_url( 'admin-post.php?action=nctb_delete_question&question_id=' . $q->id ), self::NONCE_ACTION, self::NONCE_FIELD );
						$hints_cnt = ( ! empty( $q->hint_1 ) ? 1 : 0 ) + ( ! empty( $q->hint_2 ) ? 1 : 0 ) + ( ! empty( $q->hint_3 ) ? 1 : 0 );
					?>
						<tr>
							<td><strong>#<?php echo esc_html( (string) $q->id ); ?></strong></td>
							<td>
								<span title="<?php echo esc_attr( $type_info ? $type_info['label_en'] : $q->question_type ); ?>">
									<?php echo esc_html( ( $type_info ? $type_info['icon'] : '' ) . ' ' . strtoupper( $q->question_type ) ); ?>
								</span>
							</td>
							<td>
								<strong><a href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $q->prompt ), 12 ) ); ?></a></strong>
							</td>
							<td><?php echo esc_html( get_the_title( $q->lesson_id ) ?: '—' ); ?></td>
							<td><span class="badge"><?php echo esc_html( ucfirst( $q->difficulty ) ); ?></span></td>
							<td><?php echo esc_html( $hints_cnt . ' hints' ); ?></td>
							<td>
								<a href="<?php echo esc_url( $edit_url ); ?>" class="button button-small"><?php esc_html_e( 'Edit', 'nctb-learning-hub' ); ?></a>
								<a href="<?php echo esc_url( $del_url ); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php esc_attr_e( 'Delete this question?', 'nctb-learning-hub' ); ?>');"><?php esc_html_e( 'Delete', 'nctb-learning-hub' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Render Add / Edit form.
	 *
	 * @param object|null $q         Question object if editing.
	 * @param array       $lessons   Lessons list.
	 * @param array       $all_types Question types.
	 * @return void
	 */
	protected function render_form( $q, array $lessons, array $all_types ) {
		$is_edit      = ! empty( $q );
		$title        = $is_edit ? __( 'Edit Practice Question', 'nctb-learning-hub' ) : __( 'Add New Practice Question', 'nctb-learning-hub' );
		$options      = $is_edit && ! empty( $q->options ) ? $q->options : array();
		$linked_cids  = $is_edit ? array_map( function( $c ) { return (int) $c->id; }, $q->concepts ) : array();
		$all_concepts = NCTB_Curriculum_Data::get_concepts();
		$back_url     = admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON . '&page=' . self::MENU_SLUG );
		?>
		<h2><?php echo esc_html( $title ); ?> <a href="<?php echo esc_url( $back_url ); ?>" class="page-title-action">← <?php esc_html_e( 'Back to List', 'nctb-learning-hub' ); ?></a></h2>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:850px;background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:8px;margin-top:15px;">
			<input type="hidden" name="action" value="nctb_save_question">
			<input type="hidden" name="question_id" value="<?php echo esc_attr( $is_edit ? $q->id : 0 ); ?>">
			<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>

			<table class="form-table">
				<tr>
					<th scope="row"><label for="q-lesson-id"><strong><?php esc_html_e( 'Target Lesson:', 'nctb-learning-hub' ); ?></strong></label></th>
					<td>
						<select name="lesson_id" id="q-lesson-id" class="regular-text" required>
							<option value="0">— <?php esc_html_e( 'Select Lesson', 'nctb-learning-hub' ); ?> —</option>
							<?php foreach ( $lessons as $l ) : ?>
								<option value="<?php echo esc_attr( $l->ID ); ?>" <?php selected( $is_edit ? $q->lesson_id : 0, $l->ID ); ?>>
									<?php echo esc_html( $l->post_title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="q-type"><strong><?php esc_html_e( 'Question Type:', 'nctb-learning-hub' ); ?></strong></label></th>
					<td>
						<select name="question_type" id="q-type" class="regular-text">
							<?php foreach ( $all_types as $t_key => $t_info ) : ?>
								<option value="<?php echo esc_attr( $t_key ); ?>" <?php selected( $is_edit ? $q->question_type : 'mcq', $t_key ); ?>>
									<?php echo esc_html( $t_info['icon'] . ' ' . $t_info['label_en'] . ' (' . $t_info['label_bn'] . ')' ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="q-prompt"><strong><?php esc_html_e( 'Question Prompt / Instruction:', 'nctb-learning-hub' ); ?></strong></label></th>
					<td>
						<textarea name="prompt" id="q-prompt" rows="4" class="large-text" required placeholder="<?php esc_attr_e( 'e.g. In what year was Nelson Mandela awarded the Nobel Peace Prize?', 'nctb-learning-hub' ); ?>"><?php echo esc_textarea( $is_edit ? $q->prompt : '' ); ?></textarea>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="q-content"><?php esc_html_e( 'Context / Sentence Snippet (Optional):', 'nctb-learning-hub' ); ?></label></th>
					<td>
						<textarea name="content" id="q-content" rows="3" class="large-text" placeholder="<?php esc_attr_e( 'Optional passage excerpt or sentence with blank...', 'nctb-learning-hub' ); ?>"><?php echo esc_textarea( $is_edit ? $q->content : '' ); ?></textarea>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="q-diff"><?php esc_html_e( 'Difficulty Level:', 'nctb-learning-hub' ); ?></label></th>
					<td>
						<select name="difficulty" id="q-diff">
							<option value="easy" <?php selected( $is_edit ? $q->difficulty : 'medium', 'easy' ); ?>>🟢 Easy</option>
							<option value="medium" <?php selected( $is_edit ? $q->difficulty : 'medium', 'medium' ); ?>>🟡 Medium</option>
							<option value="hard" <?php selected( $is_edit ? $q->difficulty : 'medium', 'hard' ); ?>>🔴 Hard</option>
						</select>
					</td>
				</tr>

				<!-- Text Answer / Fill in Blank answer -->
				<tr id="row-text-answer">
					<th scope="row"><label for="q-answer"><strong><?php esc_html_e( 'Correct Answer / Target Text:', 'nctb-learning-hub' ); ?></strong></label></th>
					<td>
						<input type="text" name="correct_answer" id="q-answer" class="large-text" value="<?php echo esc_attr( $is_edit ? ( $q->correct_answer ?? '' ) : '' ); ?>" placeholder="<?php esc_attr_e( 'For fill-in-blank or short answer. Use | for multiple accepted answers e.g. 27 | twenty seven', 'nctb-learning-hub' ); ?>">
						<p class="description"><?php esc_html_e( 'For Fill in Blank / Short Answer / Error Correction. Multiple valid variants can be separated by a pipe (|).', 'nctb-learning-hub' ); ?></p>
					</td>
				</tr>

				<!-- MCQ Options Section -->
				<tr id="row-mcq-options">
					<th scope="row"><strong><?php esc_html_e( 'MCQ Options (for MCQ type):', 'nctb-learning-hub' ); ?></strong></th>
					<td>
						<table class="widefat" style="margin-bottom:10px;">
							<thead>
								<tr>
									<th style="width:40px;text-align:center;"><?php esc_html_e( 'Key', 'nctb-learning-hub' ); ?></th>
									<th><?php esc_html_e( 'Option Text', 'nctb-learning-hub' ); ?></th>
									<th style="width:80px;text-align:center;"><?php esc_html_e( 'Correct?', 'nctb-learning-hub' ); ?></th>
									<th><?php esc_html_e( 'Feedback (Optional)', 'nctb-learning-hub' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$keys = array( 'A', 'B', 'C', 'D' );
								for ( $i = 0; $i < 4; $i++ ) :
									$opt = $options[ $i ] ?? null;
									$is_c = $opt && ! empty( $opt->is_correct );
								?>
									<tr>
										<td style="text-align:center;">
											<strong><?php echo esc_html( $keys[ $i ] ); ?></strong>
											<input type="hidden" name="mcq_options[<?php echo esc_attr( $i ); ?>][option_key]" value="<?php echo esc_attr( $keys[ $i ] ); ?>">
										</td>
										<td>
											<input type="text" name="mcq_options[<?php echo esc_attr( $i ); ?>][option_text]" class="widefat" value="<?php echo esc_attr( $opt ? $opt->option_text : '' ); ?>" placeholder="<?php echo esc_attr( sprintf( __( 'Option %s text...', 'nctb-learning-hub' ), $keys[ $i ] ) ); ?>">
										</td>
										<td style="text-align:center;">
											<input type="radio" name="mcq_correct_index" value="<?php echo esc_attr( $i ); ?>" <?php checked( $is_c || ( ! $is_edit && 0 === $i ) ); ?>>
										</td>
										<td>
											<input type="text" name="mcq_options[<?php echo esc_attr( $i ); ?>][feedback]" class="widefat" value="<?php echo esc_attr( $opt ? $opt->feedback : '' ); ?>" placeholder="<?php esc_attr_e( 'Why right/wrong...', 'nctb-learning-hub' ); ?>">
										</td>
									</tr>
								<?php endfor; ?>
							</tbody>
						</table>
					</td>
				</tr>

				<!-- Progressive Hints -->
				<tr>
					<th scope="row"><strong><?php esc_html_e( 'Progressive Hints:', 'nctb-learning-hub' ); ?></strong></th>
					<td>
						<p><label><strong>💡 Hint 1 (Subtle Orientation):</strong></label><br>
						<input type="text" name="hint_1" class="large-text" value="<?php echo esc_attr( $is_edit ? $q->hint_1 : '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Check paragraph 3 for the specific year...', 'nctb-learning-hub' ); ?>"></p>

						<p><label><strong>💡 Hint 2 (Contextual Clue):</strong></label><br>
						<input type="text" name="hint_2" class="large-text" value="<?php echo esc_attr( $is_edit ? $q->hint_2 : '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. It happened one year before he was elected president in 1994.', 'nctb-learning-hub' ); ?>"></p>

						<p><label><strong>💡 Hint 3 (Strong Clue):</strong></label><br>
						<input type="text" name="hint_3" class="large-text" value="<?php echo esc_attr( $is_edit ? $q->hint_3 : '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. The year was 1993.', 'nctb-learning-hub' ); ?>"></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="q-explanation"><strong><?php esc_html_e( 'Full Explanation:', 'nctb-learning-hub' ); ?></strong></label></th>
					<td>
						<textarea name="explanation" id="q-explanation" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Detailed explanation shown after student completes question or completes attempts...', 'nctb-learning-hub' ); ?>"><?php echo esc_textarea( $is_edit ? $q->explanation : '' ); ?></textarea>
					</td>
				</tr>

				<!-- Concepts -->
				<?php if ( ! empty( $all_concepts ) ) : ?>
					<tr>
						<th scope="row"><?php esc_html_e( 'Linked Concepts:', 'nctb-learning-hub' ); ?></th>
						<td>
							<div style="max-height:140px;overflow:auto;background:#f8fafc;padding:8px;border:1px solid #e2e8f0;border-radius:6px;">
								<?php foreach ( $all_concepts as $c ) : ?>
									<label style="display:inline-block;margin-right:15px;margin-bottom:6px;">
										<input type="checkbox" name="concept_ids[]" value="<?php echo esc_attr( $c->id ); ?>" <?php checked( in_array( (int) $c->id, $linked_cids, true ) ); ?>>
										<?php echo esc_html( $c->name ); ?>
									</label>
								<?php endforeach; ?>
							</div>
						</td>
					</tr>
				<?php endif; ?>
			</table>

			<p class="submit">
				<button type="submit" class="button button-primary button-large"><?php echo esc_html( $is_edit ? __( 'Update Question', 'nctb-learning-hub' ) : __( 'Save Question', 'nctb-learning-hub' ) ); ?></button>
				<a href="<?php echo esc_url( $back_url ); ?>" class="button button-secondary"><?php esc_html_e( 'Cancel', 'nctb-learning-hub' ); ?></a>
			</p>
		</form>
		<?php
	}

	/**
	 * Handle save/update action.
	 *
	 * @return void
	 */
	public function handle_save() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'nctb-learning-hub' ) );
		}

		check_admin_referer( self::NONCE_ACTION, self::NONCE_FIELD );

		$question_id = isset( $_POST['question_id'] ) ? absint( wp_unslash( $_POST['question_id'] ) ) : 0;
		$lesson_id   = isset( $_POST['lesson_id'] ) ? absint( wp_unslash( $_POST['lesson_id'] ) ) : 0;
		$type        = isset( $_POST['question_type'] ) ? sanitize_key( wp_unslash( $_POST['question_type'] ) ) : 'mcq';

		$data = array(
			'lesson_id'           => $lesson_id,
			'question_type'       => $type,
			'prompt'              => isset( $_POST['prompt'] ) ? wp_kses_post( wp_unslash( $_POST['prompt'] ) ) : '',
			'content'             => isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '',
			'difficulty'          => isset( $_POST['difficulty'] ) ? sanitize_key( wp_unslash( $_POST['difficulty'] ) ) : 'medium',
			'correct_answer'      => isset( $_POST['correct_answer'] ) ? sanitize_text_field( wp_unslash( $_POST['correct_answer'] ) ) : '',
			'explanation'         => isset( $_POST['explanation'] ) ? wp_kses_post( wp_unslash( $_POST['explanation'] ) ) : '',
			'hint_1'              => isset( $_POST['hint_1'] ) ? sanitize_text_field( wp_unslash( $_POST['hint_1'] ) ) : '',
			'hint_2'              => isset( $_POST['hint_2'] ) ? sanitize_text_field( wp_unslash( $_POST['hint_2'] ) ) : '',
			'hint_3'              => isset( $_POST['hint_3'] ) ? sanitize_text_field( wp_unslash( $_POST['hint_3'] ) ) : '',
			'source_type'         => 'nctb_textbook',
			'verification_status' => 'verified',
		);

		// Handle MCQ options
		$options       = array();
		$correct_index = isset( $_POST['mcq_correct_index'] ) ? absint( wp_unslash( $_POST['mcq_correct_index'] ) ) : 0;

		if ( 'mcq' === $type && isset( $_POST['mcq_options'] ) && is_array( $_POST['mcq_options'] ) ) {
			$raw_opts = (array) wp_unslash( $_POST['mcq_options'] );
			foreach ( $raw_opts as $idx => $opt ) {
				$options[] = array(
					'option_key'  => sanitize_text_field( $opt['option_key'] ?? chr( 65 + $idx ) ),
					'option_text' => sanitize_text_field( $opt['option_text'] ?? '' ),
					'is_correct'  => ( $idx === $correct_index ) ? 1 : 0,
					'feedback'    => sanitize_text_field( $opt['feedback'] ?? '' ),
				);
			}
		}

		$concept_ids = isset( $_POST['concept_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['concept_ids'] ) ) : array();

		if ( $question_id ) {
			NCTB_Practice_Data::update_question( $question_id, $data, $options, $concept_ids );
			$msg = __( 'Question updated successfully.', 'nctb-learning-hub' );
		} else {
			$new_id = NCTB_Practice_Data::create_question( $data, $options, $concept_ids );
			$msg    = __( 'Question created successfully.', 'nctb-learning-hub' );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON . '&page=' . self::MENU_SLUG . '&message=' . rawurlencode( $msg ) ) );
		exit;
	}

	/**
	 * Handle question delete action.
	 *
	 * @return void
	 */
	public function handle_delete() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'nctb-learning-hub' ) );
		}

		check_admin_referer( self::NONCE_ACTION, self::NONCE_FIELD );
		$question_id = isset( $_GET['question_id'] ) ? absint( wp_unslash( $_GET['question_id'] ) ) : 0;

		if ( $question_id ) {
			NCTB_Practice_Data::delete_question( $question_id );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON . '&page=' . self::MENU_SLUG . '&message=' . rawurlencode( __( 'Question deleted.', 'nctb-learning-hub' ) ) ) );
		exit;
	}
}
