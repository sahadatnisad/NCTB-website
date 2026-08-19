<?php
/**
 * Public homepage (marketing landing page).
 *
 * Presentation only. A single-page marketing site with anchored sections so it
 * needs no extra WordPress pages. Copy is marketing content (not curriculum),
 * kept translation-ready.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$nctb_books_url = get_post_type_archive_link( 'nctb_book' );
$nctb_reg_url   = wp_registration_url();
$nctb_login_url = wp_login_url( home_url( '/onboarding' ) );
?>
<div class="nctb-mkt">

	<!-- HERO -->
	<section class="mkt-hero">
		<div class="mkt-wrap">
			<span class="mkt-eyebrow">🇧🇩 <?php esc_html_e( 'NCTB Curriculum · SSC & HSC English', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'Learn your school lesson better — with a personal AI English tutor.', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead">
				<?php esc_html_e( 'A lesson-by-lesson companion to the NCTB textbook. Learn, practise, get contextual help in Bangla or English, fix your mistakes, and prepare for board exams — right from your phone.', 'nctb-theme' ); ?>
			</p>
			<div class="mkt-hero-actions">
				<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Start free', 'nctb-theme' ); ?> →</a>
				<a class="mkt-btn mkt-btn-ghost mkt-btn-lg" href="#how"><?php esc_html_e( 'See how it works', 'nctb-theme' ); ?></a>
			</div>
			<p class="mkt-hero-note">✓ <?php esc_html_e( 'Free sample lessons · No credit card · Works on any Android phone', 'nctb-theme' ); ?></p>

			<div class="mkt-hero-stats">
				<div class="stat"><b>SSC + HSC</b><span><?php esc_html_e( 'English, aligned to NCTB', 'nctb-theme' ); ?></span></div>
				<div class="stat"><b>6</b><span><?php esc_html_e( 'skills: grammar, vocab, reading, writing, listening, speaking', 'nctb-theme' ); ?></span></div>
				<div class="stat"><b>AI</b><span><?php esc_html_e( 'tutor inside every lesson', 'nctb-theme' ); ?></span></div>
			</div>
		</div>
	</section>

	<!-- TRUST STRIP -->
	<section class="mkt-section" style="padding:1.75rem 0;">
		<div class="mkt-wrap mkt-trust">
			<span class="mkt-pill">📘 <?php esc_html_e( 'Follows the official NCTB book', 'nctb-theme' ); ?></span>
			<span class="mkt-pill">🧠 <?php esc_html_e( 'Hints before answers', 'nctb-theme' ); ?></span>
			<span class="mkt-pill">📝 <?php esc_html_e( 'Verified board questions', 'nctb-theme' ); ?></span>
			<span class="mkt-pill">📈 <?php esc_html_e( 'Progress & mastery tracking', 'nctb-theme' ); ?></span>
			<span class="mkt-pill">🇧🇩 <?php esc_html_e( 'Bangla + English support', 'nctb-theme' ); ?></span>
		</div>
	</section>

	<!-- HOW IT WORKS -->
	<section class="mkt-section mkt-section-alt" id="how">
		<div class="mkt-wrap mkt-center">
			<span class="mkt-eyebrow"><?php esc_html_e( 'How it works', 'nctb-theme' ); ?></span>
			<h2 class="mkt-h2"><?php esc_html_e( 'One lesson at a time, from learning to mastery', 'nctb-theme' ); ?></h2>
			<p class="mkt-lead"><?php esc_html_e( 'The same loop for every lesson keeps studying simple and effective.', 'nctb-theme' ); ?></p>
			<div class="mkt-steps" style="text-align:left;">
				<div class="mkt-step"><h3><?php esc_html_e( 'Learn', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Read the NCTB lesson explained clearly, with vocabulary and grammar in focus.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Practise', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Answer questions, get progressive hints, and retry until it clicks.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Ask the tutor', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Stuck? The AI tutor explains in Bangla or English, using your lesson.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-step"><h3><?php esc_html_e( 'Master & revise', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Mistakes become a review list; spaced revision brings them back on time.', 'nctb-theme' ); ?></p></div>
			</div>
		</div>
	</section>

	<!-- SUBJECTS -->
	<section class="mkt-section" id="subjects">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Subjects', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Start with English — more subjects coming', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'The engine is built for the whole NCTB curriculum. English (SSC & HSC) is live first.', 'nctb-theme' ); ?></p>
			</div>
			<div class="mkt-grid">
				<a class="mkt-subject" href="<?php echo esc_url( $nctb_books_url ); ?>">
					<span class="tag"><?php esc_html_e( 'SSC · Class 9–10', 'nctb-theme' ); ?></span>
					<h3>📖 <?php esc_html_e( 'SSC English', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'Reading, grammar, vocabulary and writing — lesson by lesson.', 'nctb-theme' ); ?></p>
					<span class="go"><?php esc_html_e( 'Browse lessons', 'nctb-theme' ); ?> →</span>
				</a>
				<a class="mkt-subject" href="<?php echo esc_url( $nctb_books_url ); ?>">
					<span class="tag"><?php esc_html_e( 'HSC · Class 11–12', 'nctb-theme' ); ?></span>
					<h3>📗 <?php esc_html_e( 'HSC English', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'Deeper reading, writing and exam transfer for board preparation.', 'nctb-theme' ); ?></p>
					<span class="go"><?php esc_html_e( 'Browse lessons', 'nctb-theme' ); ?> →</span>
				</a>
				<div class="mkt-subject" style="opacity:0.7;cursor:default;">
					<span class="tag"><?php esc_html_e( 'Coming soon', 'nctb-theme' ); ?></span>
					<h3>🧪 <?php esc_html_e( 'ICT · Bangla · Science', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'The same lesson engine will power more NCTB subjects.', 'nctb-theme' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- FEATURES -->
	<section class="mkt-section mkt-section-alt" id="features">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Why students learn faster here', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Not a video pile. A guided learning system.', 'nctb-theme' ); ?></h2>
			</div>
			<div class="mkt-grid">
				<div class="mkt-card"><div class="ico">🎯</div><h3><?php esc_html_e( 'Curriculum-first', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'The NCTB book decides what you learn. AI supports it — it never replaces it.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🤖</div><h3><?php esc_html_e( 'Contextual AI tutor', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Explains your exact lesson, gives a hint first, and shows why an answer was wrong.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">📝</div><h3><?php esc_html_e( 'Real practice', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'MCQ, fill-in-the-blank and short answers with instant marking — no AI cost for you.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🔁</div><h3><?php esc_html_e( 'Mistakes & spaced revision', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Wrong answers return for review at the right time so they actually stick.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🏆</div><h3><?php esc_html_e( 'Verified board questions', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Practise authentic past board questions — clearly marked, never faked by AI.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">📱</div><h3><?php esc_html_e( 'Built for mobile data', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Fast, light pages designed for Android phones and slower connections.', 'nctb-theme' ); ?></p></div>
			</div>
		</div>
	</section>

	<!-- PRICING -->
	<section class="mkt-section" id="pricing">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Pricing', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Start free. Upgrade when you are ready.', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Try sample lessons for free. Buy a single lesson, or subscribe monthly for full access.', 'nctb-theme' ); ?></p>
			</div>
			<div class="mkt-prices">
				<div class="mkt-price">
					<h3><?php esc_html_e( 'Free', 'nctb-theme' ); ?></h3>
					<div class="amt">৳0</div>
					<ul>
						<li><?php esc_html_e( 'Account & profile', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Selected sample lessons', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Limited practice', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Basic progress', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-ghost" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Create free account', 'nctb-theme' ); ?></a>
				</div>
				<div class="mkt-price pop">
					<span class="badge"><?php esc_html_e( 'Most popular', 'nctb-theme' ); ?></span>
					<h3><?php esc_html_e( 'Monthly', 'nctb-theme' ); ?></h3>
					<div class="amt">৳—<span> / <?php esc_html_e( 'month', 'nctb-theme' ); ?></span></div>
					<ul>
						<li><?php esc_html_e( 'Full subscribed course access', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Larger AI tutor allowance', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Mistakes, revision & mastery', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Verified board questions', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-primary" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Get started', 'nctb-theme' ); ?></a>
				</div>
				<div class="mkt-price">
					<h3><?php esc_html_e( 'Per lesson', 'nctb-theme' ); ?></h3>
					<div class="amt">৳—<span> / <?php esc_html_e( 'lesson', 'nctb-theme' ); ?></span></div>
					<ul>
						<li><?php esc_html_e( 'Unlock one lesson', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Its practice & assessment', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Keep your progress', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'No subscription', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-ghost" href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse lessons', 'nctb-theme' ); ?></a>
				</div>
			</div>
			<p class="mkt-center mkt-hero-note"><?php esc_html_e( 'Prices shown when checkout is enabled. AI usage has its own fair-use allowance.', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<!-- FAQ -->
	<section class="mkt-section mkt-section-alt" id="faq">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'FAQ', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Questions, answered', 'nctb-theme' ); ?></h2>
			</div>
			<div class="mkt-faq">
				<details open><summary><?php esc_html_e( 'Is this aligned to the NCTB curriculum?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Yes. The official NCTB book and structure decide what is taught. AI only helps you understand and practise it.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Do I need a fast internet connection?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'No. Pages are light and mobile-first, designed to work on Android phones and slower mobile data.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Can the AI explain in Bangla?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Yes. You can choose Bangla, English, or a bilingual mix, and change it any time.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Are the board questions real?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Verified past board questions are stored as authentic source material and clearly separated from AI-generated practice.', 'nctb-theme' ); ?></p></details>
				<details><summary><?php esc_html_e( 'Is there a free option?', 'nctb-theme' ); ?></summary><p><?php esc_html_e( 'Yes. Create a free account to try sample lessons and limited practice before buying anything.', 'nctb-theme' ); ?></p></details>
			</div>
		</div>
	</section>

	<!-- FINAL CTA -->
	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-cta">
				<h2><?php esc_html_e( 'Ready to study smarter for your board exam?', 'nctb-theme' ); ?></h2>
				<p><?php esc_html_e( 'Create a free account and open your first lesson in minutes.', 'nctb-theme' ); ?></p>
				<div class="mkt-hero-actions" style="justify-content:center;">
					<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Start free', 'nctb-theme' ); ?></a>
					<a class="mkt-btn mkt-btn-ghost mkt-btn-lg" href="<?php echo esc_url( $nctb_login_url ); ?>"><?php esc_html_e( 'Log in', 'nctb-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<!-- MARKETING FOOTER -->
	<footer class="mkt-footer">
		<div class="mkt-wrap mkt-footer-grid">
			<div class="brand">
				<b>🇧🇩 <?php bloginfo( 'name' ); ?></b>
				<p><?php esc_html_e( 'A lesson-by-lesson digital companion to the Bangladesh NCTB curriculum, with a contextual AI English tutor.', 'nctb-theme' ); ?></p>
			</div>
			<div>
				<h4><?php esc_html_e( 'Learn', 'nctb-theme' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse lessons', 'nctb-theme' ); ?></a></li>
					<li><a href="#subjects"><?php esc_html_e( 'Subjects', 'nctb-theme' ); ?></a></li>
					<li><a href="#how"><?php esc_html_e( 'How it works', 'nctb-theme' ); ?></a></li>
					<li><a href="#pricing"><?php esc_html_e( 'Pricing', 'nctb-theme' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Account', 'nctb-theme' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( $nctb_reg_url ); ?>"><?php esc_html_e( 'Sign up free', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $nctb_login_url ); ?>"><?php esc_html_e( 'Log in', 'nctb-theme' ); ?></a></li>
					<li><a href="#faq"><?php esc_html_e( 'FAQ', 'nctb-theme' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Legal', 'nctb-theme' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'nctb-theme' ); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="mkt-wrap mkt-footer-bottom">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Aligned to the NCTB curriculum. Not affiliated with a government body.', 'nctb-theme' ); ?>
		</div>
	</footer>

</div>
<?php
get_footer();
