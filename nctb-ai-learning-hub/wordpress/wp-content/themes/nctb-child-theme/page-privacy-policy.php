<?php
/**
 * Public page: Privacy Policy (starter template).
 *
 * Presentation only. Auto-applies to the page with slug "privacy-policy".
 * This is starter copy for the site owner to review with a qualified
 * professional — it is not legal advice.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="nctb-mkt">
	<section class="mkt-hero">
		<div class="mkt-wrap">
			<span class="mkt-eyebrow"><?php esc_html_e( 'Privacy', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'Privacy Policy', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'How we collect, use and protect student information. We collect only what is needed to help you learn.', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap nctb-prose" style="max-width:760px;">
			<p><em><?php esc_html_e( 'Last updated: please set on publish. This is a starter policy to be reviewed by a qualified professional before launch.', 'nctb-theme' ); ?></em></p>

			<h2><?php esc_html_e( 'What we collect', 'nctb-theme' ); ?></h2>
			<p><?php esc_html_e( 'Account details (name, email), your chosen class/level and subjects, your learning activity (lessons, practice attempts, progress and mistakes), and preferences such as your explanation language.', 'nctb-theme' ); ?></p>

			<h2><?php esc_html_e( 'How we use it', 'nctb-theme' ); ?></h2>
			<p><?php esc_html_e( 'To deliver lessons and practice, personalise your revision and progress, provide contextual AI help, process purchases, and improve the service. We do not sell personal data.', 'nctb-theme' ); ?></p>

			<h2><?php esc_html_e( 'Students who are minors', 'nctb-theme' ); ?></h2>
			<p><?php esc_html_e( 'Many of our learners are under 18. We minimise the data we collect, keep student writing and speaking private by default, and never expose one student’s data to another.', 'nctb-theme' ); ?></p>

			<h2><?php esc_html_e( 'AI and your content', 'nctb-theme' ); ?></h2>
			<p><?php esc_html_e( 'The AI tutor receives only the relevant lesson and learning context needed to help you. We minimise storage of AI conversations and do not use them to identify you unnecessarily.', 'nctb-theme' ); ?></p>

			<h2><?php esc_html_e( 'Data protection & your choices', 'nctb-theme' ); ?></h2>
			<p><?php esc_html_e( 'We use secure practices to protect your data. You may request access to, export of, or deletion of your personal data where applicable. Contact us to make a request.', 'nctb-theme' ); ?></p>

			<h2><?php esc_html_e( 'Contact', 'nctb-theme' ); ?></h2>
			<p><?php printf( /* translators: %s: contact page link */ esc_html__( 'Questions about privacy? Reach us via the %s page.', 'nctb-theme' ), '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'nctb-theme' ) . '</a>' ); ?></p>
		</div>
	</section>
</div>
<?php
get_footer();
