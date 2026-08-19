<?php
/**
 * Single Revision Note & Formula Sheet Template (Phase 18).
 *
 * Clean reading view with LaTeX/Math formula rendering support,
 * copy/print toolbar, related lesson link, and print-optimized stylesheet.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$note_id = get_the_ID();
$note    = NCTB_Notes_Service::get_note( $note_id );
?>

<!-- KaTeX / Math Rendering Support for LaTeX Equations -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css" crossorigin="anonymous">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js" crossorigin="anonymous" onload="renderMathInElement(document.body);"></script>

<div class="nctb-note-article-screen">
	<div class="note-container">
		<!-- Note Top Navigation / Action Bar -->
		<div class="note-top-bar">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_note' ) ); ?>" class="back-link">⬅ <?php esc_html_e( 'সকল নোটস ও ফর্মুলা শিট', 'nctb-theme' ); ?></a>
			<div class="note-actions-btn-group">
				<button type="button" class="nctb-btn nctb-btn-sm nctb-btn-outline" onclick="window.print();">
					🖨️ <?php esc_html_e( 'প্রিন্ট / PDF ডাউনলোড', 'nctb-theme' ); ?>
				</button>
			</div>
		</div>

		<!-- Note Content Header -->
		<header class="note-article-header">
			<div class="note-badge-row">
				<span class="note-type-tag">📑 <?php echo esc_html( $note['type'] ); ?></span>
				<span class="note-subject-tag">📘 <?php echo esc_html( $note['subject'] ); ?></span>
				<span class="note-difficulty-tag difficulty-<?php echo esc_attr( $note['difficulty'] ); ?>">
					<?php
					$diff_map = array(
						'foundation' => '🟢 বেসিক',
						'medium'     => '🟡 স্ট্যান্ডার্ড',
						'advanced'   => '🔴 অ্যাডভান্সড',
					);
					echo esc_html( $diff_map[ $note['difficulty'] ] ?? '🟡 স্ট্যান্ডার্ড' );
					?>
				</span>
			</div>
			<h1 class="note-title"><?php the_title(); ?></h1>
			<?php if ( ! empty( $note['excerpt'] ) ) : ?>
				<p class="note-lead"><?php echo esc_html( $note['excerpt'] ); ?></p>
			<?php endif; ?>
		</header>

		<!-- Main Note Body -->
		<article class="note-article-body nctb-prose">
			<?php echo $note['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</article>

		<!-- Note Footer / Related Learning Links -->
		<footer class="note-article-footer">
			<div class="footer-meta-box">
				<div class="meta-desc">
					<h4>💡 এই টপিকের বিস্তারিত অনুশীলন করতে চান?</h4>
					<p>ইন্টারঅ্যাক্টিভ কুইজ, এআই টিউটর সহায়তা এবং বিগত বোর্ড প্রশ্ন সমাধান করুন।</p>
				</div>
				<div class="meta-action">
					<?php if ( $note['lesson_id'] ) : ?>
						<a href="<?php echo esc_url( get_permalink( $note['lesson_id'] ) ); ?>" class="nctb-btn nctb-btn-primary">
							🚀 মূল পাঠে যান
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>" class="nctb-btn nctb-btn-primary">
							📚 পাঠ্যবইসমূহ দেখুন
						</a>
					<?php endif; ?>
				</div>
			</div>
		</footer>
	</div>
</div>

<?php
get_footer();
