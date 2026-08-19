<?php
/**
 * Public Homepage (Marketing Landing Page) — Professional Redesign (P1).
 *
 * Reference: docs/plans/frontend-design-plan-2.md
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

	<!-- 1. HERO SECTION -->
	<section class="mkt-hero">
		<div class="mkt-wrap mkt-hero-grid">
			<div class="mkt-hero-content">
				<span class="mkt-eyebrow"><?php esc_html_e( 'NCTB-aligned • SSC & HSC English', 'nctb-theme' ); ?></span>
				<h1><?php esc_html_e( 'Your NCTB lesson. Learn it. Practise it. Master it.', 'nctb-theme' ); ?></h1>
				<p class="mkt-lead">
					<?php esc_html_e( 'Follow the same lessons you study in school, with clear explanations, guided practice, mistake review, revision, and contextual AI support.', 'nctb-theme' ); ?>
				</p>
				<div class="mkt-hero-actions">
					<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>"><?php esc_html_e( 'Start Free Lesson', 'nctb-theme' ); ?> →</a>
					<a class="mkt-btn mkt-btn-secondary mkt-btn-lg" href="#how"><?php esc_html_e( 'See How It Works', 'nctb-theme' ); ?></a>
				</div>
				<p class="mkt-hero-note">✓ <?php esc_html_e( 'No card required for the free lesson.', 'nctb-theme' ); ?></p>
			</div>

			<!-- Real Product Hero Mockup -->
			<div class="mkt-hero-preview-frame">
				<div class="mkt-preview-header">
					<div class="dots">
						<span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
					</div>
					<span class="address-bar">nctb-learning-hub.com/lesson/nelson-mandela</span>
				</div>
				<div class="mkt-preview-body">
					<span class="book-context"><?php esc_html_e( 'HSC English 1st Paper • Unit 1, Lesson 1', 'nctb-theme' ); ?></span>
					<h4><?php esc_html_e( 'Nelson Mandela: From Apartheid Fighter to President', 'nctb-theme' ); ?></h4>

					<div class="mkt-stepper-preview">
						<span class="done">✓ 1. Warm-up</span>
						<span class="done">✓ 2. Reading</span>
						<span class="done">✓ 3. Vocabulary</span>
						<span class="active">● 4. Practice</span>
						<span class="pending">○ 5. Writing</span>
					</div>

					<div class="mkt-sample-q">
						<p><?php esc_html_e( 'Q2: What does "emancipation" mean in paragraph 3?', 'nctb-theme' ); ?></p>
						<div class="mkt-sample-opt selected"><?php esc_html_e( 'A. Liberation or freedom from social bondage', 'nctb-theme' ); ?></div>
						<div class="mkt-sample-opt"><?php esc_html_e( 'B. Imprisonment on an island', 'nctb-theme' ); ?></div>
					</div>

					<div class="mkt-floating-ai-card">
						<div class="ai-label">
							<span>🤖 <?php esc_html_e( 'AI Tutor', 'nctb-theme' ); ?></span>
							<span style="font-size:0.75rem;color:var(--ai);"><?php esc_html_e( 'Active on this step', 'nctb-theme' ); ?></span>
						</div>
						<p><em>"Notice how Mandela contrasts emancipation with continuing bondage..."</em></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 2. PRODUCT PROOF STRIP -->
	<section class="mkt-proof-strip">
		<div class="mkt-wrap mkt-proof-flex">
			<div class="mkt-proof-item"><span class="check">✓</span> <?php esc_html_e( 'NCTB-aligned', 'nctb-theme' ); ?></div>
			<div class="mkt-proof-item"><span class="check">✓</span> <?php esc_html_e( 'Lesson-by-lesson', 'nctb-theme' ); ?></div>
			<div class="mkt-proof-item"><span class="check">✓</span> <?php esc_html_e( 'Practice with hints', 'nctb-theme' ); ?></div>
			<div class="mkt-proof-item"><span class="check">✓</span> <?php esc_html_e( 'Mistake review', 'nctb-theme' ); ?></div>
			<div class="mkt-proof-item"><span class="check">✓</span> <?php esc_html_e( 'Spaced revision', 'nctb-theme' ); ?></div>
			<div class="mkt-proof-item"><span class="check">✓</span> <?php esc_html_e( 'Contextual AI tutor', 'nctb-theme' ); ?></div>
		</div>
	</section>

	<!-- 3. THREE-PART PRODUCT STORY (LEARN, PRACTISE, IMPROVE) -->
	<section class="mkt-section" id="learn">
		<div class="mkt-wrap">

			<!-- Chapter A: Learn -->
			<div class="mkt-story-row">
				<div class="mkt-story-text">
					<span class="mkt-eyebrow"><?php esc_html_e( 'Chapter A • Learn', 'nctb-theme' ); ?></span>
					<h3><?php esc_html_e( 'Follow your school textbook with structured clarity', 'nctb-theme' ); ?></h3>
					<p><?php esc_html_e( 'Every lesson breaks down the official NCTB text into clear learning outcomes, guided reading passages, and focused vocabulary.', 'nctb-theme' ); ?></p>
					<ul class="mkt-story-points">
						<li><span class="bullet">✓</span> <?php esc_html_e( '14 standard lesson activity blocks designed for deep understanding', 'nctb-theme' ); ?></li>
						<li><span class="bullet">✓</span> <?php esc_html_e( 'Contextual vocabulary with Bangla meanings, synonyms, and examples', 'nctb-theme' ); ?></li>
						<li><span class="bullet">✓</span> <?php esc_html_e( 'Grammar rules highlighted and explained within authentic reading passages', 'nctb-theme' ); ?></li>
					</ul>
				</div>
				<div class="mkt-story-preview">
					<div class="mkt-card-preview">
						<span style="font-size:0.8rem;color:var(--brand);font-weight:700;"><?php esc_html_e( 'Activity 2 • Reading Passage', 'nctb-theme' ); ?></span>
						<div class="mkt-reading-passage">
							<em>"I am here before you not as a prophet, but as a humble servant of you, the people. Your heroic sacrifices have made it possible for me to be here today..."</em>
						</div>
						<div class="mkt-vocab-row">
							<span class="mkt-vocab-chip">📌 <strong>Prophet:</strong> নবী / ধর্মপ্রবক্তা</span>
							<span class="mkt-vocab-chip">📌 <strong>Emancipation:</strong> মুক্তি / স্বাধীনতা</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Chapter B: Practise -->
			<div class="mkt-story-row reverse">
				<div class="mkt-story-text">
					<span class="mkt-eyebrow"><?php esc_html_e( 'Chapter B • Practise', 'nctb-theme' ); ?></span>
					<h3><?php esc_html_e( 'Instant marking with progressive hints before answers', 'nctb-theme' ); ?></h3>
					<p><?php esc_html_e( 'Practise MCQs, fill-in-the-blanks, and short answers. When stuck, get scaffolded hints instead of having the answer given away.', 'nctb-theme' ); ?></p>
					<ul class="mkt-story-points">
						<li><span class="bullet">✓</span> <?php esc_html_e( 'Deterministic marking without AI hallucination or wait times', 'nctb-theme' ); ?></li>
						<li><span class="bullet">✓</span> <?php esc_html_e( '3-level progressive hint ladder diagnosing misconceptions', 'nctb-theme' ); ?></li>
						<li><span class="bullet">✓</span> <?php esc_html_e( 'Immediate retry opportunity to solidify concept retention', 'nctb-theme' ); ?></li>
					</ul>
				</div>
				<div class="mkt-story-preview">
					<div class="mkt-card-preview">
						<span style="font-size:0.8rem;color:var(--text-faint);font-weight:700;"><?php esc_html_e( 'Question 3 • Practice Quiz', 'nctb-theme' ); ?></span>
						<p style="font-size:0.95rem;font-weight:600;margin:8px 0 12px;"><?php esc_html_e( 'What is the antonym of the word "apartheid"?', 'nctb-theme' ); ?></p>
						<div class="mkt-sample-opt"><?php esc_html_e( 'A. Segregation', 'nctb-theme' ); ?></div>
						<div class="mkt-sample-opt selected"><?php esc_html_e( 'B. Integration / Equality ✅', 'nctb-theme' ); ?></div>
						<div class="mkt-hint-box">
							💡 <strong><?php esc_html_e( 'Level 1 Hint:', 'nctb-theme' ); ?></strong> <?php esc_html_e( 'Apartheid means separation. Look for the word describing bringing people together.', 'nctb-theme' ); ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Chapter C: Improve -->
			<div class="mkt-story-row">
				<div class="mkt-story-text">
					<span class="mkt-eyebrow"><?php esc_html_e( 'Chapter C • Improve', 'nctb-theme' ); ?></span>
					<h3><?php esc_html_e( 'Mistakes become your personalized revision schedule', 'nctb-theme' ); ?></h3>
					<p><?php esc_html_e( 'Every wrong answer is automatically logged in your Mistake Notebook and scheduled for SM-2 spaced revision until mastered.', 'nctb-theme' ); ?></p>
					<ul class="mkt-story-points">
						<li><span class="bullet">✓</span> <?php esc_html_e( 'Smart Mistake Notebook with graduation tracking (2 consecutive correct attempts)', 'nctb-theme' ); ?></li>
						<li><span class="bullet">✓</span> <?php esc_html_e( 'SM-2 spaced review ladder (1, 3, 7, 14, 30 days) to prevent forgetting', 'nctb-theme' ); ?></li>
						<li><span class="bullet">✓</span> <?php esc_html_e( '6-stage writing feedback with multi-criteria rubric evaluation', 'nctb-theme' ); ?></li>
					</ul>
				</div>
				<div class="mkt-story-preview">
					<div class="mkt-card-preview">
						<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
							<span style="font-size:0.8rem;color:var(--danger);font-weight:700;"><?php esc_html_e( 'Mistake Notebook', 'nctb-theme' ); ?></span>
							<span style="font-size:0.75rem;background:var(--brand-soft);color:var(--brand-dark);padding:2px 8px;border-radius:4px;font-weight:700;"><?php esc_html_e( 'Graduation: 2/2 streak', 'nctb-theme' ); ?></span>
						</div>
						<div class="mkt-mistake-box">
							<span class="tag"><?php esc_html_e( 'Subject-Verb Agreement', 'nctb-theme' ); ?></span>
							<p><strong><?php esc_html_e( 'Error:', 'nctb-theme' ); ?></strong> Neither Rahim nor his friends <em>was</em> present.</p>
							<p style="margin-top:4px;color:var(--brand-dark);"><strong><?php esc_html_e( 'Correction:', 'nctb-theme' ); ?></strong> ...friends <strong>were</strong> present (agrees with nearest subject).</p>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>

	<!-- 4. SIMPLIFIED LEARNING LOOP -->
	<section class="mkt-section mkt-section-alt" id="how">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Learning Method', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'One lesson. One complete learning loop.', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Every lesson follows a clean, proven cognitive sequence that turns daily school topics into permanent board exam mastery.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-timeline">
				<div class="mkt-timeline-step"><span class="num">01</span><strong>Learn</strong></div>
				<div class="mkt-timeline-step"><span class="num">02</span><strong>Practise</strong></div>
				<div class="mkt-timeline-step"><span class="num">03</span><strong>AI Tutor</strong></div>
				<div class="mkt-timeline-step"><span class="num">04</span><strong>Test</strong></div>
				<div class="mkt-timeline-step"><span class="num">05</span><strong>Fix Mistakes</strong></div>
				<div class="mkt-timeline-step"><span class="num">06</span><strong>Revise</strong></div>
				<div class="mkt-timeline-step master"><span class="num">07</span><strong>Master</strong></div>
			</div>
		</div>
	</section>

	<!-- 5. WHY THIS IS DIFFERENT (TWO-COLUMN COMPARISON) -->
	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'The Product Difference', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Built around the lesson you are already studying', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'A digital home guide that reinforces your school lessons, not another detached video course.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-comparison-grid">
				<div class="mkt-comp-card">
					<h4><?php esc_html_e( 'Typical Online Course', 'nctb-theme' ); ?></h4>
					<span class="subtitle"><?php esc_html_e( 'Passive watching without guided practice', 'nctb-theme' ); ?></span>
					<ul class="mkt-comp-list">
						<li><span class="sym bad">✕</span> <?php esc_html_e( 'Watch a 45-minute passive video lecture', 'nctb-theme' ); ?></li>
						<li><span class="sym bad">✕</span> <?php esc_html_e( 'Move to next chapter without verifying mastery', 'nctb-theme' ); ?></li>
						<li><span class="sym bad">✕</span> <?php esc_html_e( 'Forget previous mistakes with zero review schedule', 'nctb-theme' ); ?></li>
						<li><span class="sym bad">✕</span> <?php esc_html_e( 'Generic chatbot with no textbook lesson context', 'nctb-theme' ); ?></li>
					</ul>
				</div>

				<div class="mkt-comp-card highlight">
					<h4><?php esc_html_e( 'NCTB Learning Hub', 'nctb-theme' ); ?></h4>
					<span class="subtitle" style="color:var(--brand);"><?php esc_html_e( 'Active lesson-by-lesson guided mastery', 'nctb-theme' ); ?></span>
					<ul class="mkt-comp-list">
						<li><span class="sym good">✓</span> <?php esc_html_e( 'Learn your exact NCTB textbook lesson', 'nctb-theme' ); ?></li>
						<li><span class="sym good">✓</span> <?php esc_html_e( 'Practise questions with progressive hints before answers', 'nctb-theme' ); ?></li>
						<li><span class="sym good">✓</span> <?php esc_html_e( 'Mistakes automatically enter a spaced revision queue', 'nctb-theme' ); ?></li>
						<li><span class="sym good">✓</span> <?php esc_html_e( 'Contextual Socratic AI Tutor grounded in your exact passage', 'nctb-theme' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- 6. CONTEXTUAL AI TUTOR SECTION -->
	<section class="mkt-section mkt-section-alt">
		<div class="mkt-wrap">
			<div class="mkt-ai-split">
				<div class="mkt-ai-text">
					<span class="mkt-eyebrow" style="background:var(--ai-soft);color:var(--ai);"><?php esc_html_e( 'Contextual AI Support', 'nctb-theme' ); ?></span>
					<h2 class="mkt-h2"><?php esc_html_e( 'AI that knows what lesson you are studying', 'nctb-theme' ); ?></h2>
					<p class="mkt-lead"><?php esc_html_e( 'The tutor receives lesson context, your level, and relevant mistakes. It helps with the current lesson instead of acting like a generic chatbot.', 'nctb-theme' ); ?></p>
					<p style="font-size:0.92rem;color:var(--text-soft);margin-top:16px;">
						🔒 <em><?php esc_html_e( 'Verified curriculum and board content remain strictly separate from generated AI explanations.', 'nctb-theme' ); ?></em>
					</p>
				</div>

				<div class="mkt-ai-preview">
					<div class="mkt-ai-card-preview">
						<div class="ai-header">
							<strong>🤖 <?php esc_html_e( 'AI Tutor Drawer', 'nctb-theme' ); ?></strong>
							<span style="font-size:0.75rem;color:var(--ai);font-weight:700;"><?php esc_html_e( 'Grounded in Active Step', 'nctb-theme' ); ?></span>
						</div>
						<div class="mkt-ai-chips-list">
							<span class="chip">💡 <?php esc_html_e( 'Explain this', 'nctb-theme' ); ?></span>
							<span class="chip">🇧🇩 <?php esc_html_e( 'বাংলায় বুঝিয়ে দিন', 'nctb-theme' ); ?></span>
							<span class="chip">🎯 <?php esc_html_e( 'Give me a hint', 'nctb-theme' ); ?></span>
							<span class="chip">❓ <?php esc_html_e( 'Why was I wrong?', 'nctb-theme' ); ?></span>
						</div>
						<div class="mkt-ai-chat-bubble">
							<p><strong><?php esc_html_e( 'AI Tutor:', 'nctb-theme' ); ?></strong> "Mandela used 'emancipation' in paragraph 3 to describe liberation from socio-economic hardship, not just his personal release from Robben Island."</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 7. HOME STUDY DASHBOARD PREVIEW -->
	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Personalized Learning Guide', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Know what to study next', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'The platform remembers your lesson progress, active mistakes, and revision schedule so you can resume effortlessly from where you left off.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-card-preview" style="margin-top:40px;">
				<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;margin-bottom:24px;">
					<div style="background:var(--surface-alt);padding:16px;border-radius:var(--radius-sm);border:1px solid var(--border);">
						<span style="font-size:0.8rem;color:var(--text-faint);display:block;"><?php esc_html_e( 'Active Lesson', 'nctb-theme' ); ?></span>
						<strong style="font-size:1.1rem;color:var(--text);"><?php esc_html_e( 'Nelson Mandela (Step 4)', 'nctb-theme' ); ?></strong>
					</div>
					<div style="background:var(--warning-soft);padding:16px;border-radius:var(--radius-sm);border:1px solid var(--warning-border);">
						<span style="font-size:0.8rem;color:var(--warning);display:block;"><?php esc_html_e( 'Spaced Revision Due', 'nctb-theme' ); ?></span>
						<strong style="font-size:1.1rem;color:var(--warning);"><?php esc_html_e( '3 Items Ready', 'nctb-theme' ); ?></strong>
					</div>
					<div style="background:var(--danger-soft);padding:16px;border-radius:var(--radius-sm);border:1px solid var(--danger-border);">
						<span style="font-size:0.8rem;color:var(--danger);display:block;"><?php esc_html_e( 'Mistakes to Fix', 'nctb-theme' ); ?></span>
						<strong style="font-size:1.1rem;color:var(--danger);"><?php esc_html_e( '2 Active Errors', 'nctb-theme' ); ?></strong>
					</div>
				</div>
				<div style="display:flex;justify-content:space-between;align-items:center;background:var(--surface-alt);padding:16px 20px;border-radius:var(--radius-sm);border:1px solid var(--border);">
					<div>
						<strong style="font-size:0.95rem;display:block;"><?php esc_html_e( 'HSC English 1st Paper', 'nctb-theme' ); ?></strong>
						<small style="color:var(--text-soft);"><?php esc_html_e( 'Unit 1: People or Institutions Making History • 72% Completed', 'nctb-theme' ); ?></small>
					</div>
					<a href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>" class="mkt-btn mkt-btn-primary" style="height:38px;padding:0 16px;font-size:0.88rem;"><?php esc_html_e( 'Resume Study', 'nctb-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<!-- 8. SSC & HSC ENGLISH HUBS -->
	<section class="mkt-section mkt-section-alt" id="subjects">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Curriculum Coverage', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Built for SSC & HSC Board Candidates', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Master the exact English textbooks assigned by the National Curriculum and Textbook Board.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-levels-grid">
				<div class="mkt-level-card">
					<div>
						<span class="tag"><?php esc_html_e( 'Classes 9–10', 'nctb-theme' ); ?></span>
						<h3><?php esc_html_e( 'SSC English (1st & 2nd Paper)', 'nctb-theme' ); ?></h3>
						<p><?php esc_html_e( 'Lesson-by-lesson reading passages, vocabulary drills, grammar foundations, writing practice, and authentic past board questions.', 'nctb-theme' ); ?></p>
					</div>
					<a href="<?php echo esc_url( home_url( '/ssc-english/' ) ); ?>" class="mkt-link-arrow"><?php esc_html_e( 'Explore SSC English Syllabus', 'nctb-theme' ); ?> →</a>
				</div>

				<div class="mkt-level-card">
					<div>
						<span class="tag"><?php esc_html_e( 'Classes 11–12', 'nctb-theme' ); ?></span>
						<h3><?php esc_html_e( 'HSC English (1st & 2nd Paper)', 'nctb-theme' ); ?></h3>
						<p><?php esc_html_e( 'Advanced passage comprehension, theme writing, flow charts, vocabulary in context, and statistical board pattern analytics.', 'nctb-theme' ); ?></p>
					</div>
					<a href="<?php echo esc_url( home_url( '/hsc-english/' ) ); ?>" class="mkt-link-arrow"><?php esc_html_e( 'Explore HSC English Syllabus', 'nctb-theme' ); ?> →</a>
				</div>
			</div>

			<p class="mkt-center" style="margin-top:36px;font-size:0.92rem;color:var(--text-soft);">
				<?php esc_html_e( 'Coming later: ICT • Bangla • Mathematics • General Science', 'nctb-theme' ); ?>
			</p>
		</div>
	</section>

	<!-- 9. TRANSPARENT PRICING -->
	<section class="mkt-section mkt-section-warm" id="pricing">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Transparent Pricing', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Start free. Upgrade when you are ready.', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Try sample lessons for free. Unlock single lessons permanently or subscribe for full monthly access.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-pricing-grid">
				<div class="mkt-price-card">
					<h3><?php esc_html_e( 'Free Starter', 'nctb-theme' ); ?></h3>
					<div class="price-amount">৳০</div>
					<p style="font-size:0.88rem;color:var(--text-soft);margin-bottom:20px;"><?php esc_html_e( 'Perfect for trying the lesson engine', 'nctb-theme' ); ?></p>
					<ul class="mkt-price-features">
						<li><span class="check">✓</span> <?php esc_html_e( 'Student profile & setup', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( 'Free sample lessons & reading', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( 'Practice quizzes with hints', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( '50 AI Tutor interactions / day', 'nctb-theme' ); ?></li>
					</ul>
					<a href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>" class="mkt-btn mkt-btn-secondary"><?php esc_html_e( 'Start Free', 'nctb-theme' ); ?></a>
				</div>

				<div class="mkt-price-card featured">
					<span class="badge-popular"><?php esc_html_e( 'Most Popular', 'nctb-theme' ); ?></span>
					<h3><?php esc_html_e( 'All-Access Pass', 'nctb-theme' ); ?></h3>
					<div class="price-amount">৳২৯৯<span> / <?php esc_html_e( 'month', 'nctb-theme' ); ?></span></div>
					<p style="font-size:0.88rem;color:var(--text-soft);margin-bottom:20px;"><?php esc_html_e( 'Complete preparation for board candidates', 'nctb-theme' ); ?></p>
					<ul class="mkt-price-features">
						<li><span class="check">✓</span> <?php esc_html_e( 'All SSC & HSC English lessons', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( '200 AI Tutor interactions / day', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( 'Mistake Notebook & Spaced Revision', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( 'All 10 Board Questions & Analytics', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( 'Writing, listening & speaking practice', 'nctb-theme' ); ?></li>
					</ul>
					<a href="<?php echo esc_url( home_url( '/pricing' ) ); ?>" class="mkt-btn mkt-btn-primary"><?php esc_html_e( 'Unlock Full Access', 'nctb-theme' ); ?></a>
				</div>

				<div class="mkt-price-card">
					<h3><?php esc_html_e( 'Single Lesson Pass', 'nctb-theme' ); ?></h3>
					<div class="price-amount">৳১৯<span> / <?php esc_html_e( 'lesson', 'nctb-theme' ); ?></span></div>
					<p style="font-size:0.88rem;color:var(--text-soft);margin-bottom:20px;"><?php esc_html_e( 'For mastering difficult specific topics', 'nctb-theme' ); ?></p>
					<ul class="mkt-price-features">
						<li><span class="check">✓</span> <?php esc_html_e( 'Permanent unlock for single lesson', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( 'Full practice quiz & answer bank', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( 'Related board exam questions', 'nctb-theme' ); ?></li>
						<li><span class="check">✓</span> <?php esc_html_e( 'No recurring subscription', 'nctb-theme' ); ?></li>
					</ul>
					<a href="<?php echo esc_url( $nctb_books_url ); ?>" class="mkt-btn mkt-btn-secondary"><?php esc_html_e( 'Browse Lessons', 'nctb-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<!-- 10. FAQ ACCORDION -->
	<section class="mkt-section" id="faq">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Frequently Asked Questions', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Questions, Answered', 'nctb-theme' ); ?></h2>
			</div>

			<div class="mkt-faq-single-col">
				<details open>
					<summary><?php esc_html_e( 'Is this platform strictly aligned to the official NCTB textbook?', 'nctb-theme' ); ?></summary>
					<p><?php esc_html_e( 'Yes, 100%. The official NCTB book and chapter structure control what is taught. AI is used solely as a patient tutor to explain difficult grammar, provide progressive hints, and evaluate student writing.', 'nctb-theme' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'How does the Socratic AI English Tutor work?', 'nctb-theme' ); ?></summary>
					<p><?php esc_html_e( 'Instead of giving away quiz answers directly, the AI Tutor is grounded in your active lesson text. It gives progressive hints, explains grammar in Bangla or English, and diagnoses exactly why a chosen answer was incorrect.', 'nctb-theme' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'Are the board questions authentic past exam papers?', 'nctb-theme' ); ?></summary>
					<p><?php esc_html_e( 'Yes. Our database contains verified questions from Dhaka, Chattogram, Rajshahi, Cumilla, and other boards (2018–2024). They are strictly authenticated with official paper references and separated from AI practice.', 'nctb-theme' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'Will it work smoothly on Android phones with mobile data?', 'nctb-theme' ); ?></summary>
					<p><?php esc_html_e( 'Yes. The entire platform is built mobile-first with clean, lightweight CSS and scripts, designed to run smoothly on any smartphone with standard mobile data connections.', 'nctb-theme' ); ?></p>
				</details>
			</div>
		</div>
	</section>

	<!-- 11. FINAL CONVERSION CTA BANNER -->
	<section class="mkt-final-cta-band">
		<div class="mkt-wrap">
			<h2><?php esc_html_e( 'Ready to study smarter for your board exam?', 'nctb-theme' ); ?></h2>
			<p><?php esc_html_e( 'Join students across Bangladesh mastering their NCTB lessons step by step with personal AI tutoring.', 'nctb-theme' ); ?></p>
			<div class="mkt-hero-actions" style="justify-content:center;">
				<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>"><?php esc_html_e( 'Start Free Lesson', 'nctb-theme' ); ?> →</a>
				<a class="mkt-btn mkt-btn-secondary mkt-btn-lg" href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse Textbooks', 'nctb-theme' ); ?></a>
			</div>
		</div>
	</section>

	<!-- 12. EDITORIAL FOOTER -->
	<footer class="mkt-footer">
		<div class="mkt-wrap mkt-footer-grid">
			<div class="mkt-footer-brand">
				<b>🇧🇩 <?php bloginfo( 'name' ); ?></b>
				<p><?php esc_html_e( 'A digital home guide for the Bangladesh NCTB curriculum, offering lesson-by-lesson practice, mistake review, and contextual AI English tutoring.', 'nctb-theme' ); ?></p>
			</div>
			<div>
				<h4><?php esc_html_e( 'Curriculum', 'nctb-theme' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/ssc-english/' ) ); ?>"><?php esc_html_e( 'SSC English', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/hsc-english/' ) ); ?>"><?php esc_html_e( 'HSC English', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/board-questions/' ) ); ?>"><?php esc_html_e( 'Board Questions Bank', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/board-analytics/' ) ); ?>"><?php esc_html_e( 'Board Pattern Analytics', 'nctb-theme' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Platform', 'nctb-theme' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/how-it-works/' ) ); ?>"><?php esc_html_e( 'How It Works', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>"><?php esc_html_e( 'Pricing & Passes', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Support & Helpline', 'nctb-theme' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Legal', 'nctb-theme' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/#terms' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'nctb-theme' ); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="mkt-wrap mkt-footer-bottom">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Aligned to the NCTB curriculum. Independent educational technology initiative.', 'nctb-theme' ); ?>
		</div>
	</footer>

</div>
<?php
get_footer();
