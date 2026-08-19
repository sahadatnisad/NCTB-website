<?php
/**
 * Custom Post Type & Taxonomy for Modules & Video Courses (Phase 17).
 *
 * Registers `nctb_module` CPT and `module_category` taxonomy for video masterclasses,
 * grammar series, practical ICT lab guides, and teacher pedagogy courses.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Module_CPT
 */
class NCTB_Module_CPT {

	const POST_TYPE = 'nctb_module';
	const TAXONOMY  = 'module_category';

	const META_AUDIENCE   = '_nctb_module_audience';
	const META_CLASS      = '_nctb_module_class';
	const META_SUBJECT    = '_nctb_module_subject';
	const META_DURATION   = '_nctb_module_duration';
	const META_INSTRUCTOR = '_nctb_module_instructor';
	const META_ITEMS      = '_nctb_module_items';

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
	 * Register nctb_module CPT.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'               => _x( 'Video Modules & Courses', 'post type general name', 'nctb-learning-hub' ),
			'singular_name'      => _x( 'Course Module', 'post type singular name', 'nctb-learning-hub' ),
			'menu_name'          => _x( 'Video Modules', 'admin menu', 'nctb-learning-hub' ),
			'add_new'            => _x( 'Add New Module', 'module', 'nctb-learning-hub' ),
			'add_new_item'       => __( 'Add New Course Module', 'nctb-learning-hub' ),
			'edit_item'          => __( 'Edit Course Module', 'nctb-learning-hub' ),
			'new_item'           => __( 'New Course Module', 'nctb-learning-hub' ),
			'view_item'          => __( 'View Course Module', 'nctb-learning-hub' ),
			'search_items'       => __( 'Search Modules', 'nctb-learning-hub' ),
			'not_found'          => __( 'No modules found', 'nctb-learning-hub' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=nctb_lesson',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'modules' ),
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
	 * Register module_category taxonomy.
	 *
	 * @return void
	 */
	public static function register_taxonomy() {
		$labels = array(
			'name'              => _x( 'Module Categories', 'taxonomy general name', 'nctb-learning-hub' ),
			'singular_name'     => _x( 'Module Category', 'taxonomy singular name', 'nctb-learning-hub' ),
			'search_items'      => __( 'Search Categories', 'nctb-learning-hub' ),
			'all_items'         => __( 'All Categories', 'nctb-learning-hub' ),
			'edit_item'         => __( 'Edit Category', 'nctb-learning-hub' ),
			'update_item'       => __( 'Update Category', 'nctb-learning-hub' ),
			'add_new_item'      => __( 'Add New Category', 'nctb-learning-hub' ),
			'new_item_name'     => __( 'New Category Name', 'nctb-learning-hub' ),
			'menu_name'         => __( 'Module Categories', 'nctb-learning-hub' ),
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
				'rewrite'           => array( 'slug' => 'module-category' ),
				'show_in_rest'      => true,
			)
		);
	}

	/**
	 * Add custom meta boxes.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'nctb_module_details',
			__( 'Course Module Configuration & Lectures', 'nctb-learning-hub' ),
			array( __CLASS__, 'render_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render module details meta box.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'nctb_module_meta_nonce', 'nctb_module_nonce' );

		$audience   = get_post_meta( $post->ID, self::META_AUDIENCE, true ) ?: 'student';
		$class      = get_post_meta( $post->ID, self::META_CLASS, true ) ?: 'class_10';
		$subject    = get_post_meta( $post->ID, self::META_SUBJECT, true ) ?: 'english_1st';
		$duration   = get_post_meta( $post->ID, self::META_DURATION, true ) ?: '1 hour 30 mins';
		$instructor = get_post_meta( $post->ID, self::META_INSTRUCTOR, true ) ?: '';
		$items_json = get_post_meta( $post->ID, self::META_ITEMS, true );

		if ( empty( $items_json ) ) {
			$default_items = array(
				array(
					'id'          => 'item_1',
					'title'       => 'Introduction & Fundamental Concept',
					'youtube_id'  => 'dQw4w9WgXcQ',
					'duration'    => '12 mins',
					'description' => 'Overview of the core curriculum topic and learning goals.',
				),
			);
			$items_json = wp_json_encode( $default_items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		}
		?>
		<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
			<p>
				<label><strong><?php esc_html_e( 'Target Audience:', 'nctb-learning-hub' ); ?></strong></label><br>
				<select name="nctb_module_audience" style="width:100%;">
					<option value="student" <?php selected( $audience, 'student' ); ?>>👨‍🎓 Students Only</option>
					<option value="teacher" <?php selected( $audience, 'teacher' ); ?>>🎓 Teachers Only (Pedagogy & Training)</option>
					<option value="both" <?php selected( $audience, 'both' ); ?>>👥 Both Students & Teachers</option>
				</select>
			</p>
			<p>
				<label><strong><?php esc_html_e( 'Target Class / Level:', 'nctb-learning-hub' ); ?></strong></label><br>
				<select name="nctb_module_class" style="width:100%;">
					<option value="all" <?php selected( $class, 'all' ); ?>>All Classes (সকল শ্রেণি)</option>
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
				<input type="text" name="nctb_module_subject" value="<?php echo esc_attr( $subject ); ?>" style="width:100%;" placeholder="e.g. English, ICT, Math, Pedagogy">
			</p>
			<p>
				<label><strong><?php esc_html_e( 'Estimated Total Duration:', 'nctb-learning-hub' ); ?></strong></label><br>
				<input type="text" name="nctb_module_duration" value="<?php echo esc_attr( $duration ); ?>" style="width:100%;" placeholder="e.g. 2 hours 15 mins">
			</p>
			<p style="grid-column: span 2;">
				<label><strong><?php esc_html_e( 'Instructor / Source Channel:', 'nctb-learning-hub' ); ?></strong></label><br>
				<input type="text" name="nctb_module_instructor" value="<?php echo esc_attr( $instructor ); ?>" style="width:100%;" placeholder="e.g. NCTB Master Trainer / Expert Educator">
			</p>
		</div>

		<p>
			<label><strong><?php esc_html_e( 'Module Video Lectures / Items (JSON):', 'nctb-learning-hub' ); ?></strong></label><br>
			<span class="description"><?php esc_html_e( 'Array of lectures: [{"id": "item_1", "title": "Lecture Title", "youtube_id": "video_id", "duration": "10 mins"}]', 'nctb-learning-hub' ); ?></span>
			<textarea name="nctb_module_items" rows="8" style="width:100%;font-family:monospace;font-size:13px;"><?php echo esc_textarea( $items_json ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Save module metadata.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function save_meta( $post_id ) {
		if ( ! isset( $_POST['nctb_module_nonce'] ) || ! wp_verify_nonce( $_POST['nctb_module_nonce'], 'nctb_module_meta_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['nctb_module_audience'] ) ) {
			update_post_meta( $post_id, self::META_AUDIENCE, sanitize_key( $_POST['nctb_module_audience'] ) );
		}
		if ( isset( $_POST['nctb_module_class'] ) ) {
			update_post_meta( $post_id, self::META_CLASS, sanitize_key( $_POST['nctb_module_class'] ) );
		}
		if ( isset( $_POST['nctb_module_subject'] ) ) {
			update_post_meta( $post_id, self::META_SUBJECT, sanitize_text_field( $_POST['nctb_module_subject'] ) );
		}
		if ( isset( $_POST['nctb_module_duration'] ) ) {
			update_post_meta( $post_id, self::META_DURATION, sanitize_text_field( $_POST['nctb_module_duration'] ) );
		}
		if ( isset( $_POST['nctb_module_instructor'] ) ) {
			update_post_meta( $post_id, self::META_INSTRUCTOR, sanitize_text_field( $_POST['nctb_module_instructor'] ) );
		}
		if ( isset( $_POST['nctb_module_items'] ) ) {
			update_post_meta( $post_id, self::META_ITEMS, wp_unslash( $_POST['nctb_module_items'] ) );
		}
	}
}
