<?php
/**
 * Public page: Subjects overview.
 *
 * Presentation only. Auto-applies to the page with slug "subjects".
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
			<span class="mkt-eyebrow"><?php esc_html_e( 'Subjects', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'English now — the full NCTB curriculum next', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'The learning engine is built for every subject. We launch with English (SSC & HSC) and expand once it is proven.', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-grid">
				<a class="mkt-subject" href="<?php echo esc_url( home_url( '/ssc-english/' ) ); ?>">
					<span class="tag"><?php esc_html_e( 'SSC · Class 9–10', 'nctb-theme' ); ?></span>
					<h3>📖 <?php esc_html_e( 'SSC English', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'Reading, grammar, vocabulary and writing, lesson by lesson.', 'nctb-theme' ); ?></p>
					<span class="go"><?php esc_html_e( 'Explore SSC English', 'nctb-theme' ); ?> →</span>
				</a>
				<a class="mkt-subject" href="<?php echo esc_url( home_url( '/hsc-english/' ) ); ?>">
					<span class="tag"><?php esc_html_e( 'HSC · Class 11–12', 'nctb-theme' ); ?></span>
					<h3>📗 <?php esc_html_e( 'HSC English', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'Deeper reading and writing with board-exam transfer.', 'nctb-theme' ); ?></p>
					<span class="go"><?php esc_html_e( 'Explore HSC English', 'nctb-theme' ); ?> →</span>
				</a>
				<div class="mkt-subject" style="opacity:0.7;cursor:default;">
					<span class="tag"><?php esc_html_e( 'Coming soon', 'nctb-theme' ); ?></span>
					<h3>🧪 <?php esc_html_e( 'ICT · Bangla · Math · Science', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'The same engine — users, lessons, practice, AI and analytics — will power more subjects.', 'nctb-theme' ); ?></p>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
