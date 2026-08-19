<?php
/**
 * Archive Template for Notes & Explanations (Phase 18).
 *
 * Displays revision notes, formula sheets, and grammar rules directory.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$args = array(
	'post_type'      => NCTB_Note_CPT::POST_TYPE,
	'post_status'    => 'publish',
	'posts_per_page' => 30,
);

$notes_query = new WP_Query( $args );
?>

<div class="nctb-mkt">
	<section class="mkt-hero">
		<div class="mkt-wrap">
			<span class="mkt-eyebrow"><?php esc_html_e( 'Revision Handouts & Formula Sheets', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'নোটস, ফর্মুলা ও ব্যাকরণ শিট', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'দ্রুত রিভিশন, অধ্যায়ভিত্তিক সারসংক্ষেপ, চিত্র ও সূত্র সম্বলিত প্রিন্টযোগ্য স্টাডি মেটেরিয়াল।', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<?php if ( $notes_query->have_posts() ) : ?>
				<div class="notes-cards-grid">
					<?php
					while ( $notes_query->have_posts() ) :
						$notes_query->the_post();
						$note_id = get_the_ID();
						$note    = NCTB_Notes_Service::get_note( $note_id );
						?>
						<article class="note-card">
							<div class="note-card-badges">
								<span class="note-type-chip">📑 <?php echo esc_html( $note['type'] ); ?></span>
								<span class="note-sub-chip">📘 <?php echo esc_html( $note['subject'] ); ?></span>
							</div>

							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="note-card-desc"><?php echo esc_html( $note['excerpt'] ?: wp_trim_words( get_the_content(), 18 ) ); ?></p>

							<div class="note-card-footer">
								<a href="<?php the_permalink(); ?>" class="note-read-link">নোট পড়ুন ➔</a>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else : ?>
				<div class="nctb-empty-state">
					<p>কোনো নোটস পাওয়া যায়নি।</p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</div>

<?php
get_footer();
