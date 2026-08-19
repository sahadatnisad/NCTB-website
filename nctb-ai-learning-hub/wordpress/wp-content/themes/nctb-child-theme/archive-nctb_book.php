<?php
/**
 * Archive template: list of curriculum Books (the "browse" entry point).
 *
 * Presentation only. Curriculum data comes from the plugin — nothing is
 * hard-coded here.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$nctb_books = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_books() : array();
?>
<main id="primary" class="nctb-main nctb-curriculum">
	<header class="nctb-page-head">
		<h1>📚 <?php esc_html_e( 'Books / বইসমূহ', 'nctb-theme' ); ?></h1>
		<p class="lead"><?php esc_html_e( 'Choose a book to browse its units and lessons.', 'nctb-theme' ); ?></p>
	</header>

	<?php if ( empty( $nctb_books ) ) : ?>
		<div class="nctb-empty"><?php esc_html_e( 'No books have been published yet.', 'nctb-theme' ); ?></div>
	<?php else : ?>
		<div class="nctb-card-grid">
			<?php foreach ( $nctb_books as $nctb_book ) : ?>
				<a class="nctb-browse-card" href="<?php echo esc_url( get_permalink( $nctb_book ) ); ?>">
					<div class="browse-card-icon">📖</div>
					<div class="browse-card-body">
						<div class="browse-card-title"><?php echo esc_html( $nctb_book->post_title ); ?></div>
						<?php
						$nctb_terms = wp_get_post_terms( $nctb_book->ID, 'nctb_class_level', array( 'fields' => 'names' ) );
						if ( ! empty( $nctb_terms ) && ! is_wp_error( $nctb_terms ) ) :
							?>
							<div class="browse-card-meta"><?php echo esc_html( implode( ', ', $nctb_terms ) ); ?></div>
						<?php endif; ?>
					</div>
					<div class="browse-card-arrow">→</div>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</main>
<?php
get_footer();
