<?php
/**
 * Custom Post Type & Taxonomy for Notes & Explanations (Phase 18).
 *
 * Registers `nctb_note` CPT and `note_type` taxonomy for formula sheets,
 * graphical diagrams, grammar summaries, and printable revision handouts.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Note_CPT
 */
class NCTB_Note_CPT {

	const POST_TYPE = 'nctb_note';
	const TAXONOMY  = 'note_type';

	const META_LESSON_ID  = '_nctb_note_lesson_id';
	const META_BOOK_ID    = '_nctb_note_book_id';
	const META_CLASS      = '_nctb_note_class';
	const META_SUBJECT    = '_nctb_note_subject';
	const META_AUDIENCE   = '_nctb_note_audience';
	const META_DIFFICULTY = '_nctb_note_difficulty';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save_meta' ) );
	}

	/**
	 * Register nctb_note CPT.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'               => _x( 'Notes & Explanations', 'post type general name', 'nctb-learning-hub' ),
			'singular_name'      => _x( 'Revision Note', 'post type singular name', 'nctb-learning-hub' ),
			'menu_name'          => _x( 'Revision Notes', 'admin menu', 'nctb-learning-hub' ),
			'add_new'            => _x( 'Add New Note', 'note', 'nctb-learning-hub' ),
			'add_new_item'       => __( 'Add New Revision Note', 'nctb-learning-hub' ),
			'edit_item'          => __( 'Edit Revision Note', 'nctb-learning-hub' ),
			'new_item'           => __( 'New Revision Note', 'nctb-learning-hub' ),
			'view_item'          => __( 'View Revision Note', 'nctb-learning-hub' ),
			'search_items'       => __( 'Search Notes', 'nctb-learning-hub' ),
			'not_found'          => __( 'No notes found', 'nctb-learning-hub' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=nctb_lesson',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'notes' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'show_in_rest'       => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Register note_type taxonomy.
	 *
	 * @return void
	 */
	public static function register_taxonomy() {
		$labels = array(
			'name'              => _x( 'Note Types', 'taxonomy general name', 'nctb-learning-hub' ),
			'singular_name'     => _x( 'Note Type', 'taxonomy singular name', 'nctb-learning-hub' ),
			'search_items'      => __( 'Search Note Types', 'nctb-learning-hub' ),
			'all_items'         => __( 'All Note Types', 'nctb-learning-hub' ),
			'edit_item'         => __( 'Edit Note Type', 'nctb-learning-hub' ),
			'update_item'       => __( 'Update Note Type', 'nctb-learning-hub' ),
			'add_new_item'      => __( 'Add New Note Type', 'nctb-learning-hub' ),
			'new_item_name'     => __( 'New Note Type Name', 'nctb-learning-hub' ),
			'menu_name'         => __( 'Note Types', 'nctb-learning-hub' ),
		);

		register_taxonomy(
			self::TAXONOMY,
			array( self::POST_TYPE ),
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'note-type' ),
				'show_in_rest'      => true,
			)
		);
	}

	/**
	 * Add meta boxes.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'nctb_note_details',
			__( 'Note Association & Metadata', 'nctb-learning-hub' ),
			array( __CLASS__, 'render_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render note metadata meta box.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'nctb_note_meta_nonce', 'nctb_note_nonce' );

		$lesson_id  = (int) get_post_meta( $post->ID, self::META_LESSON_ID, true );
		$class      = get_post_meta( $post->ID, self::META_CLASS, true ) ?: 'class_10';
		$subject    = get_post_meta( $post->ID, self::META_SUBJECT, true ) ?: 'English';
		$audience   = get_post_meta( $post->ID, self::META_AUDIENCE, true ) ?: 'both';
		$difficulty = get_post_meta( $post->ID, self::META_DIFFICULTY, true ) ?: 'medium';
		?>
		<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
			<p>
				<label><strong><?php esc_html_e( 'Target Class / Level:', 'nctb-learning-hub' ); ?></strong></label><br>
				<select name="nctb_note_class" style="width:100%;">
					<option value="all" <?php selected( $class, 'all' ); ?>>All Classes</option>
					<option value="class_6" <?php selected( $class, 'class_6' ); ?>>Class 6</option>
					<option value="class_7" <?php selected( $class, 'class_7' ); ?>>Class 7</option>
					<option value="class_8" <?php selected( $class, 'class_8' ); ?>>Class 8 (JSC)</option>
					<option value="class_9" <?php selected( $class, 'class_9' ); ?>>Class 9</option>
					<option value="class_10" <?php selected( $class, 'class_10' ); ?>>Class 10 (SSC)</option>
					<option value="class_11" <?php selected( $class, 'class_11' ); ?>>Class 11 (HSC 1st)</option>
					<option value="class_12" <?php selected( $class, 'class_12' ); ?>>Class 12 (HSC 2nd)</option>
				</select>
			</p>
			<p>
				<label><strong><?php esc_html_e( 'Subject:', 'nctb-learning-hub' ); ?></strong></label><br>
				<input type="text" name="nctb_note_subject" value="<?php echo esc_attr( $subject ); ?>" style="width:100%;" placeholder="e.g. English, ICT, Mathematics, Physics">
			</p>
			<p>
				<label><strong><?php esc_html_e( 'Target Audience:', 'nctb-learning-hub' ); ?></strong></label><br>
				<select name="nctb_note_audience" style="width:100%;">
					<option value="both" <?php selected( $audience, 'both' ); ?>>👥 Both Students & Teachers</option>
					<option value="student" <?php selected( $audience, 'student' ); ?>>👨‍🎓 Students Only</option>
					<option value="teacher" <?php selected( $audience, 'teacher' ); ?>>🎓 Teachers Only (Lesson Handout)</option>
				</select>
			</p>
			<p>
				<label><strong><?php esc_html_e( 'Difficulty / Scope:', 'nctb-learning-hub' ); ?></strong></label><br>
				<select name="nctb_note_difficulty" style="width:100%;">
					<option value="foundation" <?php selected( $difficulty, 'foundation' ); ?>>🟢 Basic / Foundation</option>
					<option value="medium" <?php selected( $difficulty, 'medium' ); ?>>🟡 Standard Board Level</option>
					<option value="advanced" <?php selected( $difficulty, 'advanced' ); ?>>🔴 Advanced / Master</option>
				</select>
			</p>
			<p style="grid-column: span 2;">
				<label><strong><?php esc_html_e( 'Associated Lesson Post ID (Optional):', 'nctb-learning-hub' ); ?></strong></label><br>
				<input type="number" name="nctb_note_lesson_id" value="<?php echo esc_attr( $lesson_id ? $lesson_id : '' ); ?>" style="width:100%;" placeholder="e.g. 104 (ID of connected lesson)">
			</p>
		</div>
		<?php
	}

	/**
	 * Save note metadata.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function save_meta( $post_id ) {
		if ( ! isset( $_POST['nctb_note_nonce'] ) || ! wp_verify_nonce( $_POST['nctb_note_nonce'], 'nctb_note_meta_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['nctb_note_class'] ) ) {
			update_post_meta( $post_id, self::META_CLASS, sanitize_key( $_POST['nctb_note_class'] ) );
		}
		if ( isset( $_POST['nctb_note_subject'] ) ) {
			update_post_meta( $post_id, self::META_SUBJECT, sanitize_text_field( $_POST['nctb_note_subject'] ) );
		}
		if ( isset( $_POST['nctb_note_audience'] ) ) {
			update_post_meta( $post_id, self::META_AUDIENCE, sanitize_key( $_POST['nctb_note_audience'] ) );
		}
		if ( isset( $_POST['nctb_note_difficulty'] ) ) {
			update_post_meta( $post_id, self::META_DIFFICULTY, sanitize_key( $_POST['nctb_note_difficulty'] ) );
		}
		if ( isset( $_POST['nctb_note_lesson_id'] ) ) {
			update_post_meta( $post_id, self::META_LESSON_ID, absint( $_POST['nctb_note_lesson_id'] ) );
		}
	}
}
