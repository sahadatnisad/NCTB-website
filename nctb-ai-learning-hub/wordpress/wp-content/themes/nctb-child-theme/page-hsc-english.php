<?php
/**
 * Public landing page: HSC English.
 *
 * Presentation only. Auto-applies to the page with slug "hsc-english".
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$nctb_reg_url   = wp_registration_url();
$nctb_books_url = get_post_type_archive_link( 'nctb_book' );
?>
<div class="nctb-mkt">
	<section class="mkt-hero">
		<div class="mkt-wrap">
			<span class="mkt-eyebrow"><?php esc_html_e( 'HSC · Class 11–12 English', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'HSC English, built for board-exam confidence', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'Go deeper into reading, writing and language use — with an AI tutor, real practice, and authentic board questions aligned to the HSC syllabus.', 'nctb-theme' ); ?></p>
			<div class="mkt-hero-actions">
				<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Start free', 'nctb-theme' ); ?></a>
				<a class="mkt-btn mkt-btn-ghost mkt-btn-lg" href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse lessons', 'nctb-theme' ); ?></a>
			</div>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Focus areas', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Depth where HSC marks are won', 'nctb-theme' ); ?></h2>
			</div>
			<div class="mkt-grid">
				<div class="mkt-card"><div class="ico">📚</div><h3><?php esc_html_e( 'Advanced reading', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Longer passages, inference, and higher-order comprehension tasks.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">✍️</div><h3><?php esc_html_e( 'Composition & report', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Structured writing with planning, drafting, feedback and revision.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🔤</div><h3><?php esc_html_e( 'Applied grammar', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Transformation, completing sentences and usage under exam conditions.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🏆</div><h3><?php esc_html_e( 'Board question bank', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Practise verified HSC board questions by year, board and topic.', 'nctb-theme' ); ?></p></div>
			</div>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-cta">
				<h2><?php esc_html_e( 'Prepare for HSC English the smart way', 'nctb-theme' ); ?></h2>
				<p><?php esc_html_e( 'Start free and see your weak areas turn into strengths.', 'nctb-theme' ); ?></p>
				<div class="mkt-hero-actions" style="justify-content:center;">
					<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Start free', 'nctb-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
