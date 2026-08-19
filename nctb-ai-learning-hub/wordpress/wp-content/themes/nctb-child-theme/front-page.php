<?php
/**
 * Public Homepage (Marketing Landing Page) — Phase 1-12 Design Spec.
 *
 * Implements the complete Public Marketing Frontend Design Plan:
 * 1. Interactive Hero with right-side live product mockup
 * 2. Credibility strip (NCTB-aligned, step-by-step, hints, revision)
 * 3. "Not another video course" comparative matrix
 * 4. 7-Step learning loop
 * 5. Interactive 6-tab product showcase (Lesson, Practice, AI Tutor, Mistakes, Progress, Writing)
 * 6. 6 English skills mastery
 * 7. Contextual AI tutor socratic spotlight
 * 8. Subjects & Board exam hubs (SSC & HSC)
 * 9. Transparent pricing matrix & interactive FAQ accordion
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
				<span class="mkt-eyebrow">🇧🇩 <?php esc_html_e( 'NCTB Curriculum • Lesson by lesson • AI-supported', 'nctb-theme' ); ?></span>
				<h1><?php esc_html_e( 'Your NCTB lesson. Learn it. Practise it. Master it.', 'nctb-theme' ); ?></h1>
				<p class="mkt-lead">
					<?php esc_html_e( 'Follow the exact SSC and HSC English lessons you study in school. Get clear explanations, step-by-step practice, mistake review, spaced revision, and contextual AI help when you need it.', 'nctb-theme' ); ?>
				</p>
				<div class="mkt-hero-actions">
					<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>"><?php esc_html_e( 'Start a Free Lesson', 'nctb-theme' ); ?> →</a>
					<a class="mkt-btn mkt-btn-ghost mkt-btn-lg" href="<?php echo esc_url( home_url( '/how-it-works' ) ); ?>"><?php esc_html_e( 'See How It Works', 'nctb-theme' ); ?></a>
				</div>
				<p class="mkt-hero-note">✓ <?php esc_html_e( 'Free sample lessons • No card required • Optimized for mobile data', 'nctb-theme' ); ?></p>
			</div>

			<!-- Live Product Mockup -->
			<div class="mkt-hero-mockup">
				<div class="product-preview-frame">
					<div class="preview-browser-bar">
						<span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
						<span class="url-bar">nctb-learning-hub.com/lesson/nelson-mandela</span>
					</div>
					<div class="preview-card-body">
						<div class="preview-head">
							<span class="book-tag">📖 HSC English 1st Paper • Unit 1, Lesson 1</span>
							<h4>Nelson Mandela: From Apartheid Fighter to President</h4>
							<div class="progress-bar-wrap">
								<div class="progress-bar-fill" style="width: 72%;"></div>
							</div>
							<span class="progress-label">Step 5 of 7 (72% Completed)</span>
						</div>

						<div class="preview-stepper">
							<span class="step-badge done">✓ 1. Warm-up</span>
							<span class="step-badge done">✓ 2. Reading</span>
							<span class="step-badge done">✓ 3. Vocabulary</span>
							<span class="step-badge active">● 4. Practice Quiz</span>
							<span class="step-badge pending">○ 5. Writing</span>
						</div>

						<div class="preview-q-box">
							<span class="q-label">Question 2 (MCQ):</span>
							<p>What does "emancipation" mean in paragraph 3?</p>
							<div class="q-options">
								<div class="opt-btn selected">A. Liberation or freedom from bondage</div>
								<div class="opt-btn">B. Imprisonment on an island</div>
							</div>
						</div>

						<!-- Floating AI Chip -->
						<div class="floating-ai-chip">
							<div class="ai-chip-head">
								<span>🤖 AI Tutor</span>
								<span class="badge">Active</span>
							</div>
							<p>💡 <em>"Notice how Mandela contrasts 'emancipation' with 'continuing bondage'..."</em></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 2. CREDIBILITY STRIP -->
	<section class="mkt-section mkt-credibility-strip">
		<div class="mkt-wrap mkt-trust-grid">
			<div class="trust-item">
				<span class="icon">📘</span>
				<div>
					<strong><?php esc_html_e( 'Official NCTB Books', 'nctb-theme' ); ?></strong>
					<small><?php esc_html_e( 'Directly follows school syllabus', 'nctb-theme' ); ?></small>
				</div>
			</div>
			<div class="trust-item">
				<span class="icon">🧠</span>
				<div>
					<strong><?php esc_html_e( 'Socratic Hints', 'nctb-theme' ); ?></strong>
					<small><?php esc_html_e( 'Scaffolded clues before answers', 'nctb-theme' ); ?></small>
				</div>
			</div>
			<div class="trust-item">
				<span class="icon">🏛️</span>
				<div>
					<strong><?php esc_html_e( 'Verified Board Questions', 'nctb-theme' ); ?></strong>
					<small><?php esc_html_e( 'Authentic past board archive', 'nctb-theme' ); ?></small>
				</div>
			</div>
			<div class="trust-item">
				<span class="icon">🔁</span>
				<div>
					<strong><?php esc_html_e( 'Spaced Revision', 'nctb-theme' ); ?></strong>
					<small><?php esc_html_e( 'SM-2 memory interval ladder', 'nctb-theme' ); ?></small>
				</div>
			</div>
		</div>
	</section>

	<!-- 3. NOT ANOTHER VIDEO COURSE (COMPARISON) -->
	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'The Product Difference', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'A digital home guide, not just a video library.', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Open the lesson you studied in school today. Understand the text, practise difficult questions, and get hints until the concept is locked.', 'nctb-theme' ); ?></p>
			</div>

			<div class="comparison-matrix-grid">
				<div class="comp-col generic-course">
					<div class="comp-header">
						<h3>❌ Typical Online Course</h3>
						<p>Passive watching without verification</p>
					</div>
					<ul class="comp-list">
						<li><span>📹</span> Watch a 45-minute passive video lecture</li>
						<li><span>⏭️</span> Move to next video without checking mastery</li>
						<li><span>❓</span> See right or wrong with zero diagnostic explanation</li>
						<li><span>🗑️</span> Old mistakes are forgotten and never reviewed</li>
						<li><span>💬</span> Generic disconnected chatbot with no lesson context</li>
					</ul>
				</div>

				<div class="comp-col nctb-hub">
					<div class="comp-header">
						<span class="badge-featured">⭐ Designed for NCTB</span>
						<h3>✅ NCTB Learning Hub</h3>
						<p>Active lesson-by-lesson guided mastery</p>
					</div>
					<ul class="comp-list">
						<li><span>📖</span> Follow your school NCTB textbook lesson by lesson</li>
						<li><span>🎯</span> Step-by-step practice before advancing</li>
						<li><span>💡</span> Socratic hints diagnose misconceptions</li>
						<li><span>🔁</span> Smart Mistake Notebook schedules spaced review</li>
						<li><span>🤖</span> Contextual AI Tutor knows your exact paragraph and step</li>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- 4. 7-STEP LEARNING LOOP -->
	<section class="mkt-section mkt-section-alt" id="how">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Learning Method', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'One lesson. One complete learning loop.', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Every NCTB lesson flows through a proven cognitive cycle from understanding to permanent board-exam retention.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-learning-loop-grid">
				<div class="loop-step-card"><span class="step-num">1</span><h4>📖 Learn</h4><p>Understand the text clearly with vocabulary & grammar focus.</p></div>
				<div class="loop-step-card"><span class="step-num">2</span><h4>📝 Practise</h4><p>Answer guided questions with instant marking feedback.</p></div>
				<div class="loop-step-card"><span class="step-num">3</span><h4>💡 Get Help</h4><p>Scaffolded hints & contextual Bangla/English AI tutor.</p></div>
				<div class="loop-step-card"><span class="step-num">4</span><h4>🏆 Test</h4><p>Lesson assessment & authentic past board exam items.</p></div>
				<div class="loop-step-card"><span class="step-num">5</span><h4>📕 Fix Mistakes</h4><p>Wrong answers enter your Smart Mistake Notebook.</p></div>
				<div class="loop-step-card"><span class="step-num">6</span><h4>🔁 Revise</h4><p>Spaced repetition brings difficult items back on schedule.</p></div>
				<div class="loop-step-card highlight"><span class="step-num">7</span><h4>🎓 Master</h4><p>Concept mastery & syllabus progress tracked separately.</p></div>
			</div>
		</div>
	</section>

	<!-- 5. INTERACTIVE 6-TAB PRODUCT SHOWCASE -->
	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Platform Showcase', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Explore what learning feels like inside', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Click the tabs below to preview the core learning modules already built in the platform.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-showcase-container">
				<!-- Tab Navigation Buttons -->
				<div class="showcase-tabs-nav">
					<button type="button" class="tab-btn active" data-tab="tab-lesson">📖 Lesson Stepper</button>
					<button type="button" class="tab-btn" data-tab="tab-practice">📝 Practice & Hints</button>
					<button type="button" class="tab-btn" data-tab="tab-tutor">🤖 AI Tutor Drawer</button>
					<button type="button" class="tab-btn" data-tab="tab-mistakes">📕 Mistake Notebook</button>
					<button type="button" class="tab-btn" data-tab="tab-writing">✍️ Writing Workbench</button>
					<button type="button" class="tab-btn" data-tab="tab-analytics">📊 Board Analytics</button>
				</div>

				<!-- Tab Contents -->
				<div class="showcase-tab-panels">
					<!-- 1. Lesson Stepper Tab -->
					<div class="showcase-panel active" id="tab-lesson">
						<div class="showcase-card-preview">
							<div class="preview-meta">
								<span class="pill">Activity 3 of 7</span>
								<span class="title">HSC English 1st Paper • Nelson Mandela</span>
							</div>
							<h3>Reading Passage: Historical Address</h3>
							<p class="passage-sample"><em>"I am here before you not as a prophet, but as a humble servant of you, the people. Your tireless and heroic sacrifices have made it possible for me to be here today..."</em></p>
							<div class="vocab-chips-row">
								<span class="chip">📌 <strong>Prophet:</strong> নবী / ধর্মপ্রবক্তা</span>
								<span class="chip">📌 <strong>Emancipation:</strong> মুক্তি / স্বাধীনতা</span>
							</div>
						</div>
					</div>

					<!-- 2. Practice & Hints Tab -->
					<div class="showcase-panel" id="tab-practice" style="display:none;">
						<div class="showcase-card-preview">
							<span class="q-type-badge">MCQ Practice • 3-Level Progressive Hint</span>
							<h3>Q: What is the antonym of the word "apartheid"?</h3>
							<div class="demo-options-list">
								<div class="demo-opt">A. Segregation</div>
								<div class="demo-opt correct">B. Integration / Equality ✅</div>
								<div class="demo-opt">C. Discrimination</div>
							</div>
							<div class="hint-ladder-preview">
								<strong>💡 Level 1 Hint:</strong> <span>Apartheid means racial separation. Think of bringing people together.</span>
							</div>
						</div>
					</div>

					<!-- 3. AI Tutor Tab -->
					<div class="showcase-panel" id="tab-tutor" style="display:none;">
						<div class="showcase-card-preview">
							<div class="tutor-chat-head">
								<span>🤖 Contextual AI English Tutor</span>
								<span class="status">⚡ 50 Daily Interactions</span>
							</div>
							<div class="tutor-quick-chips-preview">
								<span class="chip">💡 Explain Step</span>
								<span class="chip">🇧🇩 বাংলায় ব্যাখ্যা</span>
								<span class="chip">🎯 Why was I wrong?</span>
							</div>
							<div class="chat-bubble-sample bot">
								<p><strong>AI Tutor:</strong> "Mandela used 'emancipation' to signify liberation from social deprivation, not just physical release from prison. Would you like an example sentence in Bangla?"</p>
							</div>
						</div>
					</div>

					<!-- 4. Mistake Notebook Tab -->
					<div class="showcase-panel" id="tab-mistakes" style="display:none;">
						<div class="showcase-card-preview">
							<div class="mistake-header-preview">
								<h3>📕 Active Mistake Notebook</h3>
								<span class="streak">🎓 Graduation Rule: 2 Consecutive Correct Attempts</span>
							</div>
							<div class="mistake-card-sample">
								<div class="head"><span>Subject-Verb Agreement</span><span class="badge-attn">Needs Attention</span></div>
								<p><strong>Your Error:</strong> Neither Rahim nor his friends <em>was</em> present.</p>
								<p><strong>Correct:</strong> Neither Rahim nor his friends <strong>were</strong> present (Verb agrees with closest subject).</p>
							</div>
						</div>
					</div>

					<!-- 5. Writing Workbench Tab -->
					<div class="showcase-panel" id="tab-writing" style="display:none;">
						<div class="showcase-card-preview">
							<div class="writing-stages-preview">
								<span class="pill done">1. Draft</span>
								<span class="pill active">2. AI Rubric Feedback</span>
								<span class="pill">3. Revision</span>
								<span class="pill">4. Final Polish</span>
							</div>
							<div class="rubric-feedback-sample">
								<h4>✨ AI Writing Diagnostic Breakdown</h4>
								<ul>
									<li><strong>Structure (8/10):</strong> Good topic sentence and logical chronological progression.</li>
									<li><strong>Grammar (7/10):</strong> Watch past tense consistency in paragraph 2.</li>
									<li><strong>Vocabulary (8/10):</strong> Excellent usage of lesson terms (*reconciliation*, *emancipation*).</li>
								</ul>
							</div>
						</div>
					</div>

					<!-- 6. Board Analytics Tab -->
					<div class="showcase-panel" id="tab-analytics" style="display:none;">
						<div class="showcase-card-preview">
							<h3>📊 Authentic Board Pattern Intelligence (2018–2024)</h3>
							<div class="topic-frequency-sample">
								<div class="topic-row"><span>#1 Nelson Mandela: Historic Address</span><span class="bar-val">15 Questions (45 marks)</span></div>
								<div class="topic-row"><span>#2 The Unbeaten Path: Valentina Tereshkova</span><span class="bar-val">12 Questions (35 marks)</span></div>
							</div>
							<small class="disclaimer-mini">⚠️ Historical statistical analysis from official NCTB papers, not predictions.</small>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 6. ENGLISH 6-SKILLS MASTERY -->
	<section class="mkt-section mkt-section-alt">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Comprehensive English Mastery', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'English is more than just grammar rules', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Our interactive lessons develop all six core language skills required for academic fluency and board exam excellence.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-grid">
				<div class="mkt-card"><div class="ico">🔤</div><h3><?php esc_html_e( 'Grammar in Context', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Notice grammatical patterns directly inside authentic reading passages and correct misconceptions with guided practice.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">💬</div><h3><?php esc_html_e( 'Active Vocabulary', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Learn high-frequency NCTB vocabulary with Bengali translations, synonyms, antonyms, and spaced memory recall.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">📖</div><h3><?php esc_html_e( 'Guided Reading', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Work through school passages with paragraph summaries, main ideas, detail comprehension, and inference questions.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">✍️</div><h3><?php esc_html_e( '6-Stage Writing', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Brainstorm, draft, receive structured rubric evaluation (Structure, Grammar, Vocab), and revise before final submission.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🎧</div><h3><?php esc_html_e( 'Listening Audio', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Listen to historical speeches and dialogues with native player controls and optional toggleable transcripts.', 'nctb-theme' ); ?></p></div>
				<div class="mkt-card"><div class="ico">🗣️</div><h3><?php esc_html_e( 'Speaking Practice', 'nctb-theme' ); ?></h3><p><?php esc_html_e( 'Record spoken sentences to build pronunciation confidence with constructive formative feedback.', 'nctb-theme' ); ?></p></div>
			</div>
		</div>
	</section>

	<!-- 7. CURRICULUM SUBJECTS DIRECTORY -->
	<section class="mkt-section" id="subjects">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Curriculum Hubs', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'SSC & HSC English — Live Now', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Select your level to explore syllabus breakdowns, unit structures, and authentic board questions.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-grid">
				<a class="mkt-subject" href="<?php echo esc_url( home_url( '/ssc-english/' ) ); ?>">
					<span class="tag"><?php esc_html_e( 'SSC • Class 9–10', 'nctb-theme' ); ?></span>
					<h3>📖 <?php esc_html_e( 'SSC English (1st & 2nd Paper)', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'Reading passages, grammar rules, vocabulary banks, and board questions.', 'nctb-theme' ); ?></p>
					<span class="go"><?php esc_html_e( 'Explore SSC Syllabus', 'nctb-theme' ); ?> →</span>
				</a>
				<a class="mkt-subject" href="<?php echo esc_url( home_url( '/hsc-english/' ) ); ?>">
					<span class="tag"><?php esc_html_e( 'HSC • Class 11–12', 'nctb-theme' ); ?></span>
					<h3>📗 <?php esc_html_e( 'HSC English (1st & 2nd Paper)', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'Advanced passages, theme writing, flow charts, and board exam intelligence.', 'nctb-theme' ); ?></p>
					<span class="go"><?php esc_html_e( 'Explore HSC Syllabus', 'nctb-theme' ); ?> →</span>
				</a>
				<a class="mkt-subject" href="<?php echo esc_url( home_url( '/board-analytics/' ) ); ?>">
					<span class="tag"><?php esc_html_e( 'Exam Intelligence', 'nctb-theme' ); ?></span>
					<h3>📊 <?php esc_html_e( 'Board Pattern Analytics', 'nctb-theme' ); ?></h3>
					<p class="mkt-lead" style="font-size:0.95rem;"><?php esc_html_e( 'Historical question frequency data from 10 Bangladesh Education Boards.', 'nctb-theme' ); ?></p>
					<span class="go"><?php esc_html_e( 'View Analytics Hub', 'nctb-theme' ); ?> →</span>
				</a>
			</div>
		</div>
	</section>

	<!-- 8. TRANSPARENT PRICING -->
	<section class="mkt-section mkt-section-alt" id="pricing">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Transparent Pricing', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Start free. Upgrade when you are ready.', 'nctb-theme' ); ?></h2>
				<p class="mkt-lead"><?php esc_html_e( 'Study sample lessons for free. Unlock single lessons or choose an all-access monthly pass.', 'nctb-theme' ); ?></p>
			</div>

			<div class="mkt-prices">
				<div class="mkt-price">
					<h3><?php esc_html_e( 'Free Starter', 'nctb-theme' ); ?></h3>
					<div class="amt">৳0</div>
					<ul>
						<li><?php esc_html_e( 'Student profile & setup', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Free sample lessons & reading', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Practice quiz with hints', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( '50 AI Tutor interactions / day', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-ghost" href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>"><?php esc_html_e( 'Start Free', 'nctb-theme' ); ?></a>
				</div>

				<div class="mkt-price pop">
					<span class="badge"><?php esc_html_e( 'Most Popular', 'nctb-theme' ); ?></span>
					<h3><?php esc_html_e( 'All-Access Pass', 'nctb-theme' ); ?></h3>
					<div class="amt">৳২৯৯<span> / <?php esc_html_e( 'month', 'nctb-theme' ); ?></span></div>
					<ul>
						<li><?php esc_html_e( 'All SSC & HSC English lessons', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( '200 AI Tutor interactions / day', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Mistake Notebook & Spaced Revision', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'All 10 Board Questions & Analytics', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Writing, listening & speaking practice', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-primary" href="<?php echo esc_url( home_url( '/pricing' ) ); ?>"><?php esc_html_e( 'Unlock Full Access', 'nctb-theme' ); ?></a>
				</div>

				<div class="mkt-price">
					<h3><?php esc_html_e( 'Single Lesson Pass', 'nctb-theme' ); ?></h3>
					<div class="amt">৳১৯<span> / <?php esc_html_e( 'lesson', 'nctb-theme' ); ?></span></div>
					<ul>
						<li><?php esc_html_e( 'Unlock any single lesson permanently', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Full practice & test bank', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'Related board exam questions', 'nctb-theme' ); ?></li>
						<li><?php esc_html_e( 'No recurring subscription', 'nctb-theme' ); ?></li>
					</ul>
					<a class="mkt-btn mkt-btn-ghost" href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse Lessons', 'nctb-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<!-- 9. FAQ ACCORDION -->
	<section class="mkt-section" id="faq">
		<div class="mkt-wrap">
			<div class="mkt-center">
				<span class="mkt-eyebrow"><?php esc_html_e( 'Frequently Asked Questions', 'nctb-theme' ); ?></span>
				<h2 class="mkt-h2"><?php esc_html_e( 'Everything you need to know', 'nctb-theme' ); ?></h2>
			</div>
			<div class="mkt-faq">
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
					<summary><?php esc_html_e( 'Will it work smoothly on Android phones with slow mobile data?', 'nctb-theme' ); ?></summary>
					<p><?php esc_html_e( 'Yes. The entire platform is built mobile-first with clean, lightweight CSS and scripts, designed to run smoothly on any smartphone with standard 3G/4G connections.', 'nctb-theme' ); ?></p>
				</details>
			</div>
		</div>
	</section>

	<!-- 10. FINAL CONVERSION CTA -->
	<section class="mkt-section mkt-section-alt">
		<div class="mkt-wrap">
			<div class="mkt-cta">
				<h2><?php esc_html_e( 'Ready to master your NCTB lessons at home?', 'nctb-theme' ); ?></h2>
				<p><?php esc_html_e( 'Join students across Bangladesh studying smarter with structured practice and personal AI tutoring.', 'nctb-theme' ); ?></p>
				<div class="mkt-hero-actions" style="justify-content:center;">
					<a class="mkt-btn mkt-btn-primary mkt-btn-lg" href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>"><?php esc_html_e( 'Start Your Free Lesson', 'nctb-theme' ); ?> →</a>
					<a class="mkt-btn mkt-btn-ghost mkt-btn-lg" href="<?php echo esc_url( $nctb_books_url ); ?>"><?php esc_html_e( 'Browse Books', 'nctb-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<!-- 11. FOOTER -->
	<footer class="mkt-footer">
		<div class="mkt-wrap mkt-footer-grid">
			<div class="brand">
				<b>🇧🇩 <?php bloginfo( 'name' ); ?></b>
				<p><?php esc_html_e( 'Lesson-by-lesson digital companion to the Bangladesh NCTB curriculum, with an interactive practice engine and contextual AI English tutor.', 'nctb-theme' ); ?></p>
			</div>
			<div>
				<h4><?php esc_html_e( 'Curriculum', 'nctb-theme' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/ssc-english/' ) ); ?>"><?php esc_html_e( 'SSC English', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/hsc-english/' ) ); ?>"><?php esc_html_e( 'HSC English', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/board-questions/' ) ); ?>"><?php esc_html_e( 'Board Questions', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/board-analytics/' ) ); ?>"><?php esc_html_e( 'Board Analytics', 'nctb-theme' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Platform', 'nctb-theme' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/how-it-works/' ) ); ?>"><?php esc_html_e( 'How It Works', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>"><?php esc_html_e( 'Pricing & Passes', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'nctb-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Support & Contact', 'nctb-theme' ); ?></a></li>
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
