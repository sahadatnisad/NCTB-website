<?php
/**
 * Curriculum editor meta boxes.
 *
 * Lets editors, without code:
 *   - place a Unit inside a Book,
 *   - place a Lesson inside a Unit,
 *   - enter a Lesson's learning outcomes,
 *   - link a Lesson to reusable concepts.
 *
 * All saves are guarded by nonce + capability checks and fully sanitized.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Curriculum_Meta
 */
class NCTB_Curriculum_Meta {

	const NONCE_ACTION = 'nctb_curriculum_meta_save';
	const NONCE_FIELD  = 'nctb_curriculum_meta_nonce';

	/**
	 * Wire hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_boxes' ) );
		add_action( 'save_post_' . NCTB_Curriculum_CPT::CPT_UNIT, array( $this, 'save_unit' ), 10, 2 );
		add_action( 'save_post_' . NCTB_Curriculum_CPT::CPT_LESSON, array( $this, 'save_lesson' ), 10, 2 );
	}

	/**
	 * Register meta boxes.
	 *
	 * @return void
	 */
	public function add_boxes() {
		add_meta_box(
			'nctb_unit_parent',
			__( 'Belongs to Book', 'nctb-learning-hub' ),
			array( $this, 'render_unit_parent' ),
			NCTB_Curriculum_CPT::CPT_UNIT,
			'side',
			'high'
		);

		add_meta_box(
			'nctb_lesson_parent',
			__( 'Belongs to Unit', 'nctb-learning-hub' ),
			array( $this, 'render_lesson_parent' ),
			NCTB_Curriculum_CPT::CPT_LESSON,
			'side',
			'high'
		);

		add_meta_box(
			'nctb_lesson_outcomes',
			__( 'Learning Outcomes', 'nctb-learning-hub' ),
			array( $this, 'render_lesson_outcomes' ),
			NCTB_Curriculum_CPT::CPT_LESSON,
			'normal',
			'default'
		);

		add_meta_box(
			'nctb_lesson_concepts',
			__( 'Linked Concepts', 'nctb-learning-hub' ),
			array( $this, 'render_lesson_concepts' ),
			NCTB_Curriculum_CPT::CPT_LESSON,
			'side',
			'default'
		);
	}

	/**
	 * Output the shared nonce field once.
	 *
	 * @return void
	 */
	protected function nonce_field() {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
	}

	/**
	 * Render the Unit→Book selector.
	 *
	 * @param WP_Post $post Unit post.
	 * @return void
	 */
	public function render_unit_parent( $post ) {
		$this->nonce_field();
		$current = NCTB_Curriculum_CPT::get_unit_book( $post->ID );
		$books   = NCTB_Curriculum_CPT::get_books();
		?>
		<p><?php esc_html_e( 'Choose the book this unit belongs to.', 'nctb-learning-hub' ); ?></p>
		<select name="nctb_book_id" class="widefat">
			<option value="0">— <?php esc_html_e( 'Select a book', 'nctb-learning-hub' ); ?> —</option>
			<?php foreach ( $books as $book ) : ?>
				<option value="<?php echo esc_attr( $book->ID ); ?>" <?php selected( $current, $book->ID ); ?>>
					<?php echo esc_html( $book->post_title ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render the Lesson→Unit selector.
	 *
	 * @param WP_Post $post Lesson post.
	 * @return void
	 */
	public function render_lesson_parent( $post ) {
		$this->nonce_field();
		$current = NCTB_Curriculum_CPT::get_lesson_unit( $post->ID );
		$units   = get_posts(
			array(
				'post_type'   => NCTB_Curriculum_CPT::CPT_UNIT,
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
			)
		);
		?>
		<p><?php esc_html_e( 'Choose the unit/chapter this lesson belongs to.', 'nctb-learning-hub' ); ?></p>
		<select name="nctb_unit_id" class="widefat">
			<option value="0">— <?php esc_html_e( 'Select a unit', 'nctb-learning-hub' ); ?> —</option>
			<?php
			foreach ( $units as $unit ) :
				$book_id    = NCTB_Curriculum_CPT::get_unit_book( $unit->ID );
				$book_label = $book_id ? ' (' . get_the_title( $book_id ) . ')' : '';
				?>
				<option value="<?php echo esc_attr( $unit->ID ); ?>" <?php selected( $current, $unit->ID ); ?>>
					<?php echo esc_html( $unit->post_title . $book_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render learning outcomes editor (one per line).
	 *
	 * @param WP_Post $post Lesson post.
	 * @return void
	 */
	public function render_lesson_outcomes( $post ) {
		$this->nonce_field();
		$rows  = NCTB_Curriculum_Data::get_lesson_outcomes( $post->ID );
		$lines = array();
		foreach ( $rows as $row ) {
			$lines[] = $row->outcome_text;
		}
		?>
		<p><?php esc_html_e( 'Enter one learning outcome per line. Students see these at the top of the lesson.', 'nctb-learning-hub' ); ?></p>
		<textarea name="nctb_learning_outcomes" class="widefat" rows="6" placeholder="<?php esc_attr_e( 'e.g. Identify the main idea of a paragraph', 'nctb-learning-hub' ); ?>"><?php echo esc_textarea( implode( "\n", $lines ) ); ?></textarea>
		<?php
	}

	/**
	 * Render the concept-linking checkboxes.
	 *
	 * @param WP_Post $post Lesson post.
	 * @return void
	 */
	public function render_lesson_concepts( $post ) {
		$this->nonce_field();
		$concepts = NCTB_Curriculum_Data::get_concepts();
		$linked   = NCTB_Curriculum_Data::get_lesson_concept_ids( $post->ID );

		if ( empty( $concepts ) ) {
			$url = admin_url( 'edit.php?post_type=' . NCTB_Curriculum_CPT::CPT_LESSON . '&page=nctb-concepts' );
			echo '<p>' . esc_html__( 'No concepts yet.', 'nctb-learning-hub' ) . ' <a href="' . esc_url( $url ) . '">' . esc_html__( 'Add concepts', 'nctb-learning-hub' ) . '</a></p>';
			return;
		}
		?>
		<p><?php esc_html_e( 'Link the concepts this lesson teaches.', 'nctb-learning-hub' ); ?></p>
		<div style="max-height:180px;overflow:auto;">
			<?php foreach ( $concepts as $concept ) : ?>
				<label style="display:block;margin:.25rem 0;">
					<input type="checkbox" name="nctb_concept_ids[]" value="<?php echo esc_attr( $concept->id ); ?>" <?php checked( in_array( (int) $concept->id, $linked, true ) ); ?>>
					<?php echo esc_html( $concept->name ); ?>
				</label>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/* ------------------------------------------------------------------ */
	/* Save handlers                                                       */
	/* ------------------------------------------------------------------ */

	/**
	 * Shared guard: verify autosave, nonce and capability.
	 *
	 * @param int $post_id Post being saved.
	 * @return bool True if it is safe to save.
	 */
	protected function can_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		if ( ! isset( $_POST[ self::NONCE_FIELD ] ) ) {
			return false;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) );
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return false;
		}
		return current_user_can( 'edit_post', $post_id );
	}

	/**
	 * Persist the Unit→Book relationship.
	 *
	 * @param int     $post_id Unit ID.
	 * @param WP_Post $post    Unit post.
	 * @return void
	 */
	public function save_unit( $post_id, $post ) {
		if ( ! $this->can_save( $post_id ) ) {
			return;
		}
		if ( isset( $_POST['nctb_book_id'] ) ) {
			$book_id = absint( wp_unslash( $_POST['nctb_book_id'] ) );
			if ( $book_id ) {
				update_post_meta( $post_id, NCTB_Curriculum_CPT::META_BOOK_ID, $book_id );
			} else {
				delete_post_meta( $post_id, NCTB_Curriculum_CPT::META_BOOK_ID );
			}
		}
	}

	/**
	 * Persist the Lesson→Unit relationship, outcomes and concept links.
	 *
	 * @param int     $post_id Lesson ID.
	 * @param WP_Post $post    Lesson post.
	 * @return void
	 */
	public function save_lesson( $post_id, $post ) {
		if ( ! $this->can_save( $post_id ) ) {
			return;
		}

		// Parent unit.
		if ( isset( $_POST['nctb_unit_id'] ) ) {
			$unit_id = absint( wp_unslash( $_POST['nctb_unit_id'] ) );
			if ( $unit_id ) {
				update_post_meta( $post_id, NCTB_Curriculum_CPT::META_UNIT_ID, $unit_id );
			} else {
				delete_post_meta( $post_id, NCTB_Curriculum_CPT::META_UNIT_ID );
			}
		}

		// Learning outcomes (textarea, one per line).
		if ( isset( $_POST['nctb_learning_outcomes'] ) ) {
			$raw   = sanitize_textarea_field( wp_unslash( $_POST['nctb_learning_outcomes'] ) );
			$lines = array_filter( array_map( 'trim', explode( "\n", $raw ) ), 'strlen' );
			NCTB_Curriculum_Data::set_lesson_outcomes( $post_id, $lines );
		}

		// Concept links.
		$concept_ids = isset( $_POST['nctb_concept_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['nctb_concept_ids'] ) ) : array();
		NCTB_Curriculum_Data::set_lesson_concepts( $post_id, $concept_ids );
	}
}
