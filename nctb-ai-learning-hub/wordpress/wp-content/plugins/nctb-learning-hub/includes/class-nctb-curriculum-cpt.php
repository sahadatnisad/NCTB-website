<?php
/**
 * Curriculum content types & taxonomies.
 *
 * Registers the editorial backbone as WordPress-native content:
 *   - CPTs:       nctb_book, nctb_unit, nctb_lesson
 *   - Taxonomies: class level, subject, paper, curriculum version, session, topic
 *
 * Hierarchy is expressed by meta relationships (unit→book, lesson→unit) and
 * native menu_order for sequencing, so admins create/edit/reorder without code.
 * No curriculum content is hard-coded here — this only defines the structures.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Curriculum_CPT
 */
class NCTB_Curriculum_CPT {

	const CPT_BOOK   = 'nctb_book';
	const CPT_UNIT   = 'nctb_unit';
	const CPT_LESSON = 'nctb_lesson';

	const META_BOOK_ID = '_nctb_book_id';
	const META_UNIT_ID = '_nctb_unit_id';

	/**
	 * Wire hooks. Called from the plugin loader.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );

		// Admin list columns for the relationship + order.
		add_filter( 'manage_' . self::CPT_UNIT . '_posts_columns', array( $this, 'unit_columns' ) );
		add_action( 'manage_' . self::CPT_UNIT . '_posts_custom_column', array( $this, 'unit_column_content' ), 10, 2 );
		add_filter( 'manage_' . self::CPT_LESSON . '_posts_columns', array( $this, 'lesson_columns' ) );
		add_action( 'manage_' . self::CPT_LESSON . '_posts_custom_column', array( $this, 'lesson_column_content' ), 10, 2 );

		// Default ordering in admin lists = menu_order.
		add_action( 'pre_get_posts', array( $this, 'default_admin_order' ) );
	}

	/**
	 * Register CPTs and taxonomies. Safe to call on activation and on init.
	 *
	 * @return void
	 */
	public static function register() {
		self::register_post_types();
		self::register_taxonomies();
	}

	/**
	 * Register the three curriculum post types.
	 *
	 * @return void
	 */
	protected static function register_post_types() {
		$supports = array( 'title', 'editor', 'page-attributes', 'thumbnail' );

		register_post_type(
			self::CPT_BOOK,
			self::post_type_args(
				__( 'Books', 'nctb-learning-hub' ),
				__( 'Book', 'nctb-learning-hub' ),
				'dashicons-book-alt',
				'book',
				$supports,
				20
			)
		);

		register_post_type(
			self::CPT_UNIT,
			self::post_type_args(
				__( 'Units', 'nctb-learning-hub' ),
				__( 'Unit', 'nctb-learning-hub' ),
				'dashicons-category',
				'unit',
				$supports,
				21
			)
		);

		register_post_type(
			self::CPT_LESSON,
			self::post_type_args(
				__( 'Lessons', 'nctb-learning-hub' ),
				__( 'Lesson', 'nctb-learning-hub' ),
				'dashicons-welcome-learn-more',
				'lesson',
				$supports,
				22
			)
		);
	}

	/**
	 * Build register_post_type() args.
	 *
	 * @param string $plural   Plural label.
	 * @param string $singular Singular label.
	 * @param string $icon     Dashicon.
	 * @param string $slug     URL slug base.
	 * @param array  $supports Supported features.
	 * @param int    $menu_pos Admin menu position.
	 * @return array
	 */
	protected static function post_type_args( $plural, $singular, $icon, $slug, $supports, $menu_pos ) {
		return array(
			'labels'            => array(
				'name'          => $plural,
				'singular_name' => $singular,
				/* translators: %s: singular content type name. */
				'add_new_item'  => sprintf( __( 'Add New %s', 'nctb-learning-hub' ), $singular ),
				/* translators: %s: singular content type name. */
				'edit_item'     => sprintf( __( 'Edit %s', 'nctb-learning-hub' ), $singular ),
				'menu_name'     => $plural,
			),
			'public'            => true,
			'show_in_rest'      => true,
			'has_archive'       => true,
			'hierarchical'      => false,
			'menu_icon'         => $icon,
			'menu_position'     => $menu_pos,
			'supports'          => $supports,
			'rewrite'           => array( 'slug' => $slug ),
			'capability_type'   => 'post',
			'show_in_nav_menus' => true,
		);
	}

	/**
	 * Register classification taxonomies.
	 *
	 * @return void
	 */
	protected static function register_taxonomies() {
		$all_types    = array( self::CPT_BOOK, self::CPT_UNIT, self::CPT_LESSON );
		$book_and_les = array( self::CPT_BOOK, self::CPT_LESSON );

		self::register_tax( 'nctb_class_level', __( 'Class / Level', 'nctb-learning-hub' ), __( 'Class / Level', 'nctb-learning-hub' ), $all_types, true );
		self::register_tax( 'nctb_subject', __( 'Subjects', 'nctb-learning-hub' ), __( 'Subject', 'nctb-learning-hub' ), $all_types, true );
		self::register_tax( 'nctb_paper', __( 'Papers', 'nctb-learning-hub' ), __( 'Paper', 'nctb-learning-hub' ), $book_and_les, true );
		self::register_tax( 'nctb_curriculum_version', __( 'Curriculum Versions', 'nctb-learning-hub' ), __( 'Curriculum Version', 'nctb-learning-hub' ), array( self::CPT_BOOK ), true );
		self::register_tax( 'nctb_session', __( 'Academic Sessions', 'nctb-learning-hub' ), __( 'Session', 'nctb-learning-hub' ), array( self::CPT_BOOK ), true );
		self::register_tax( 'nctb_topic', __( 'Topics', 'nctb-learning-hub' ), __( 'Topic', 'nctb-learning-hub' ), array( self::CPT_LESSON ), false );
	}

	/**
	 * Register one taxonomy.
	 *
	 * @param string $tax          Taxonomy key.
	 * @param string $plural       Plural label.
	 * @param string $singular     Singular label.
	 * @param array  $object_types Attached post types.
	 * @param bool   $hierarchical Category-like (true) or tag-like (false).
	 * @return void
	 */
	protected static function register_tax( $tax, $plural, $singular, $object_types, $hierarchical ) {
		register_taxonomy(
			$tax,
			$object_types,
			array(
				'labels'            => array(
					'name'          => $plural,
					'singular_name' => $singular,
					'menu_name'     => $plural,
				),
				'public'            => true,
				'hierarchical'      => $hierarchical,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => str_replace( 'nctb_', 'nctb-', $tax ) ),
			)
		);
	}

	/* ------------------------------------------------------------------ */
	/* Relationship query helpers (used by templates & REST)               */
	/* ------------------------------------------------------------------ */

	/**
	 * Get published books, ordered by menu_order then title.
	 *
	 * @return WP_Post[]
	 */
	public static function get_books() {
		return get_posts(
			array(
				'post_type'      => self::CPT_BOOK,
				'post_status'    => 'publish',
				'numberposts'    => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			)
		);
	}

	/**
	 * Get units belonging to a book, in order.
	 *
	 * @param int $book_id Book post ID.
	 * @return WP_Post[]
	 */
	public static function get_units( $book_id ) {
		return get_posts(
			array(
				'post_type'      => self::CPT_UNIT,
				'post_status'    => 'publish',
				'numberposts'    => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'meta_key'       => self::META_BOOK_ID, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => absint( $book_id ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
	}

	/**
	 * Get lessons belonging to a unit, in order.
	 *
	 * @param int $unit_id Unit post ID.
	 * @return WP_Post[]
	 */
	public static function get_lessons( $unit_id ) {
		return get_posts(
			array(
				'post_type'      => self::CPT_LESSON,
				'post_status'    => 'publish',
				'numberposts'    => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'meta_key'       => self::META_UNIT_ID, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => absint( $unit_id ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
	}

	/**
	 * Get the book ID a unit belongs to.
	 *
	 * @param int $unit_id Unit post ID.
	 * @return int
	 */
	public static function get_unit_book( $unit_id ) {
		return absint( get_post_meta( $unit_id, self::META_BOOK_ID, true ) );
	}

	/**
	 * Get the unit ID a lesson belongs to.
	 *
	 * @param int $lesson_id Lesson post ID.
	 * @return int
	 */
	public static function get_lesson_unit( $lesson_id ) {
		return absint( get_post_meta( $lesson_id, self::META_UNIT_ID, true ) );
	}

	/* ------------------------------------------------------------------ */
	/* Admin list columns                                                  */
	/* ------------------------------------------------------------------ */

	/**
	 * Add Book + Order columns to the units list.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function unit_columns( $columns ) {
		$columns['nctb_book']  = __( 'Book', 'nctb-learning-hub' );
		$columns['nctb_order'] = __( 'Order', 'nctb-learning-hub' );
		return $columns;
	}

	/**
	 * Render unit list column values.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function unit_column_content( $column, $post_id ) {
		if ( 'nctb_book' === $column ) {
			$book_id = self::get_unit_book( $post_id );
			echo $book_id ? esc_html( get_the_title( $book_id ) ) : '—';
		}
		if ( 'nctb_order' === $column ) {
			echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
		}
	}

	/**
	 * Add Unit + Order columns to the lessons list.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function lesson_columns( $columns ) {
		$columns['nctb_unit']  = __( 'Unit', 'nctb-learning-hub' );
		$columns['nctb_order'] = __( 'Order', 'nctb-learning-hub' );
		return $columns;
	}

	/**
	 * Render lesson list column values.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function lesson_column_content( $column, $post_id ) {
		if ( 'nctb_unit' === $column ) {
			$unit_id = self::get_lesson_unit( $post_id );
			echo $unit_id ? esc_html( get_the_title( $unit_id ) ) : '—';
		}
		if ( 'nctb_order' === $column ) {
			echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
		}
	}

	/**
	 * Default admin ordering by menu_order for our CPTs.
	 *
	 * @param WP_Query $query Query.
	 * @return void
	 */
	public function default_admin_order( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		$screen_types = array( self::CPT_UNIT, self::CPT_LESSON, self::CPT_BOOK );
		if ( in_array( $query->get( 'post_type' ), $screen_types, true ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'menu_order title' );
			$query->set( 'order', 'ASC' );
		}
	}
}
