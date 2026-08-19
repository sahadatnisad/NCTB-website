<?php
/**
 * Public page: FAQ.
 *
 * Presentation only. Auto-applies to the page with slug "faq".
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$nctb_contact_url = home_url( '/contact/' );
?>
<div class="nctb-mkt">
	<section class="mkt-hero">
		<div class="mkt-wrap mkt-center">
			<span class="mkt-eyebrow"><?php esc_html_e( 'FAQ', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'Frequently asked questions', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'Everything students and parents usually ask before starting.', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-faq">
				<details open><summary><?php esc_html_e( 'Is this aligned to the NCTB curriculum?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Yes. The official NCTB book and structure decide what is taught. AI only helps you understand and practise it — it never changes the curriculum.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Which classes are supported?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'We launch with SSC (Class 9–10) and HSC (Class 11–12) English. More subjects and classes follow on the same engine.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Do I need a fast internet connection?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'No. Pages are light and mobile-first, designed to work on Android phones and slower mobile data.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Can the AI tutor explain in Bangla?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Yes. Choose Bangla, English, or a bilingual mix, and change it any time from your profile.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Will the AI just give me the answers?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'No. It gives a hint first, explains the rule, and asks you to try again. It will not complete assessed work for you.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Are the board questions authentic?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Verified past board questions are stored as authentic source material and clearly separated from AI-generated practice. AI never labels its own questions as real board questions.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Is my data safe?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'We collect only what is needed, keep your writing and progress private, and never show one student’s data to another.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Is there a free option?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Yes. Create a free account to try sample lessons and limited practice before buying anything.', 'nctb-theme' ); ?></p></details>
			</div>
			<p class="mkt-center" style="margin-top:1.5rem;">
				<?php esc_html_e( 'Still have a question?', 'nctb-theme' ); ?>
				<a href="<?php echo esc_url( $nctb_contact_url ); ?>" style="color:#0b6e4f;font-weight:700;"><?php esc_html_e( 'Contact us', 'nctb-theme' ); ?> →</a>
			</p>
		</div>
	</section>
</div>
<?php
get_footer();
