<?php
/**
 * Public landing page: SSC English.
 *
 * Presentation only. Auto-applies to the page with slug "ssc-english".
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
			<span class="mkt-eyebrow"><?php esc_html_e( 'SSC · Class 9–10 English', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'Master SSC English, one NCTB lesson at a time', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'Follow your English For Today textbook with clear teaching, practice, an AI tutor, and verified board questions — built for the SSC board exam.', 'nctb-theme' ); ?></p>
			<div class="mkt-hero-actions">
				<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Start free', 'nctb-theme' ); ?></a>
				<a class="mkt-btn mkt-btn-ghost mkt-btn-lg" href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse lessons', 'nctb-theme' ); ?></a>
			</div>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( "What you'll build", 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Everything the SSC exam expects', 'nctb-theme' ); ?></h2>
			</div>
			<div class="mkt-grid">
				<div class="mkt-card"><div class="ico">📖</div><h3><?php esc_html_e( 'Reading & comprehension', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Understand passages, find main ideas, and answer in complete sentences.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🔤</div><h3><?php esc_html_e( 'Grammar & usage', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Tenses, articles, prepositions and more — taught in context, then practised.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">✍️</div><h3><?php esc_html_e( 'Writing', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Paragraphs, letters and compositions with model responses and feedback.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🏆</div><h3><?php esc_html_e( 'Board readiness', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Verified past board questions linked to each lesson and concept.', 'nctb-theme' ); ?></p></div>
			</div>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-cta">
				<h2><?php esc_html_e( 'Begin your SSC English journey free', 'nctb-theme' ); ?></h2>
				<p><?php esc_html_e( 'Create an account and open your first lesson in minutes.', 'nctb-theme' ); ?></p>
				<div class="mkt-hero-actions" style="justify-content:center;">
					<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Start free', 'nctb-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
