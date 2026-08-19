<?php
/**
 * Public page: How It Works.
 *
 * Presentation only. Auto-applies to the page with slug "how-it-works".
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
			<span class="mkt-eyebrow"><?php esc_html_e( 'How it works', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'Every lesson follows one simple, proven loop', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'You open the same NCTB lesson you study at school — then learn it more clearly, practise it, get help, and lock it into memory.', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-steps" style="text-align:left;">
				<div class="mkt-step"><h3><?php esc_html_e( 'Learn', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'The NCTB lesson explained clearly — reading, vocabulary and grammar in focus.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Practise', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Answer questions with instant marking and progressive hints before the answer.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Ask the tutor', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'The AI tutor explains your exact lesson in Bangla or English and diagnoses mistakes.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Test', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Short lesson quizzes and verified board questions check what you really know.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Fix mistakes', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Wrong answers collect in your mistake notebook so nothing is forgotten.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Revise', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Spaced revision brings each item back at the right time to build long-term memory.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Master', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Progress and mastery are tracked separately so you know what is truly solid.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Board practice', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Practise authentic past board questions linked to each concept you learn.', 'nctb-theme' ); ?></p></div>
			</div>
		</div>
	</section>

	<section class="mkt-section mkt-section-alt">
		<div class="mkt-wrap mkt-center">
			<span class="mkt-eyebrow"><?php esc_html_e( 'Six skills, one lesson', 'nctb-theme' ); ?></span>
			<h2 class="mkt-h2"><?php esc_html_e( 'English is taught as a whole', 'nctb-theme' ); ?></h2>
			<div class="mkt-trust" style="margin-top:1.5rem;">
				<span class="mkt-pill">📖 <?php esc_html_e( 'Reading', 'nctb-theme' ); ?></span>
				<span class="mkt-pill">✍️ <?php esc_html_e( 'Writing', 'nctb-theme' ); ?></span>
				<span class="mkt-pill">🔤 <?php esc_html_e( 'Grammar', 'nctb-theme' ); ?></span>
				<span class="mkt-pill">💬 <?php esc_html_e( 'Vocabulary', 'nctb-theme' ); ?></span>
				<span class="mkt-pill">🎧 <?php esc_html_e( 'Listening', 'nctb-theme' ); ?></span>
				<span class="mkt-pill">🗣️ <?php esc_html_e( 'Speaking', 'nctb-theme' ); ?></span>
			</div>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-cta">
				<h2><?php esc_html_e( 'See it on a real lesson', 'nctb-theme' ); ?></h2>
				<p><?php esc_html_e( 'Open a sample lesson free, or create an account to track your progress.', 'nctb-theme' ); ?></p>
				<div class="mkt-hero-actions" style="justify-content:center;">
					<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse lessons', 'nctb-theme' ); ?></a>
					<a class="mkt-btn mkt-btn-ghost mkt-btn-lg" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Start free', 'nctb-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
