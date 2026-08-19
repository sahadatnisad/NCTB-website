<?php
/**
 * Public page: Pricing.
 *
 * Presentation only. Auto-applies to the page with slug "pricing".
 * Amounts are placeholders until checkout (Phase 8) is enabled.
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
		<div class="mkt-wrap mkt-center">
			<span class="mkt-eyebrow"><?php esc_html_e( 'Pricing', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'Simple pricing, made for students', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'Start free. Pay for a single lesson when you need it, or subscribe monthly for full access and a larger AI tutor allowance.', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-prices">
				<div class="mkt-price">
					<h3><?php esc_html_e( 'Free', 'nctb-theme' ); ?></h3>
					<div class="amt">৳0</div>
					<ul>
						<li><?php esc_html_e( 'Account & profile', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Selected sample lessons', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Limited practice', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Basic progress tracking', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Limited AI tutor demo', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-ghost" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Create free account', 'nctb-theme' ); ?></a>
				</div>
				<div class="mkt-price pop">
					<span class="badge"><?php esc_html_e( 'Best value', 'nctb-theme' ); ?></span>
					<h3><?php esc_html_e( 'Monthly', 'nctb-theme' ); ?></h3>
					<div class="amt">৳—<span> / <?php esc_html_e( 'month', 'nctb-theme' ); ?></span></div>
					<ul>
						<li><?php esc_html_e( 'Full subscribed course access', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Larger AI tutor allowance', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Mistakes, revision & mastery', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Verified board questions', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Writing, listening & speaking activities', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-primary" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Get started', 'nctb-theme' ); ?></a>
				</div>
				<div class="mkt-price">
					<h3><?php esc_html_e( 'Per lesson', 'nctb-theme' ); ?></h3>
					<div class="amt">৳—<span> / <?php esc_html_e( 'lesson', 'nctb-theme' ); ?></span></div>
					<ul>
						<li><?php esc_html_e( 'Unlock a single lesson', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Its full practice & assessment', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Keep your progress forever', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'No subscription required', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-ghost" href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse lessons', 'nctb-theme' ); ?></a>
				</div>
			</div>
			<p class="mkt-center mkt-hero-note"><?php esc_html_e( 'Final prices appear when secure checkout is enabled. AI usage has a separate fair-use allowance because it has a real running cost.', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<section class="mkt-section mkt-section-alt">
		<div class="mkt-wrap">
			<div class="mkt-center"><h2 class="mkt-h2"><?php esc_html_e( 'Pricing questions', 'nctb-theme' ); ?></h2></div>
			<div class="mkt-faq">
				<details open><summary><?php esc_html_e( 'Can I try before I pay?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Yes — create a free account and use sample lessons and limited practice with no payment.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'What happens if I cancel my subscription?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'You keep any lessons you bought individually, and your progress is saved. Subscription-only content pauses until you resubscribe.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Is the AI tutor unlimited?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'AI has a fair-use allowance that is larger on paid plans. Routine practice and stored explanations never use your AI allowance.', 'nctb-theme' ); ?></p></details>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
