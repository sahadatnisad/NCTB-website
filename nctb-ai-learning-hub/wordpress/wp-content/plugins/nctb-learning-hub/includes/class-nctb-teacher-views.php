<?php
/**
 * Teacher Views & Shortcodes ("Shikkhok Hub") (Phase 16).
 *
 * Implements:
 *   - [nctb_teacher_onboarding]: Multi-step teacher profile setup wizard
 *   - [nctb_teacher_dashboard]: Dedicated teacher hub dashboard
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Teacher_Views
 */
class NCTB_Teacher_Views {

	/**
	 * Initialize shortcodes.
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'nctb_teacher_onboarding', array( __CLASS__, 'render_onboarding' ) );
		add_shortcode( 'nctb_teacher_dashboard', array( __CLASS__, 'render_dashboard' ) );
	}

	/**
	 * Render Teacher Onboarding Wizard.
	 *
	 * @return string HTML output.
	 */
	public static function render_onboarding() {
		if ( ! is_user_logged_in() ) {
			$login_url = wp_login_url( home_url( '/teacher-onboarding/' ) );
			return '<div class="nctb-card text-center" style="max-width:500px;margin:40px auto;padding:32px;">' .
				'<h2>🔒 শিক্ষক পোর্টালে প্রবেশ</h2>' .
				'<p>শিক্ষক অনবোর্ডিং সম্পন্ন করতে অনুগ্রহ করে আপনার অ্যাকাউন্টে লগইন করুন।</p>' .
				'<a href="' . esc_url( $login_url ) . '" class="nctb-btn nctb-btn-primary">🔑 লগইন করুন</a>' .
				'</div>';
		}

		$user_id = get_current_user_id();
		$profile = NCTB_Teacher_Profile::get_profile( $user_id );

		ob_start();
		?>
		<div class="nctb-teacher-onboarding-wrap" id="nctb-teacher-onboarding-app">
			<div class="onboarding-header text-center">
				<span class="badge-tag">🎓 শিক্ষক পোর্টাল (Shikkhok Hub)</span>
				<h1>শিক্ষক প্রোফাইল ও ক্লাসরুম সেটআপ</h1>
				<p class="lead">আপনার বিদ্যালয়, পাঠদানের বিষয় এবং ক্লাসরুম লক্ষ্য নির্বাচন করুন।</p>
			</div>

			<div class="onboarding-steps-bar">
				<div class="step-indicator active" data-step="1">
					<span class="step-num">১</span>
					<span class="step-lbl">প্রাতিষ্ঠানিক তথ্য</span>
				</div>
				<div class="step-line"></div>
				<div class="step-indicator" data-step="2">
					<span class="step-num">২</span>
					<span class="step-lbl">শ্রেণি ও বিষয়সমূহ</span>
				</div>
				<div class="step-line"></div>
				<div class="step-indicator" data-step="3">
					<span class="step-num">৩</span>
					<span class="step-lbl">শিক্ষাদান লক্ষ্য</span>
				</div>
			</div>

			<div class="onboarding-card">
				<!-- Step 1 -->
				<div class="wizard-step-pane" id="wizard-step-1">
					<h3>১. শিক্ষক ও প্রতিষ্ঠানের বিবরণ</h3>
					<div class="nctb-form-group">
						<label for="teacher-name">আপনার পূর্ণ নাম</label>
						<input type="text" id="teacher-name" class="nctb-input" value="<?php echo esc_attr( $profile['display_name'] ); ?>" placeholder="উদাঃ মোঃ রফিকুল ইসলাম">
					</div>
					<div class="nctb-form-group">
						<label for="teacher-school">বিদ্যালয় / কলেজ / মাদ্রাসার নাম</label>
						<input type="text" id="teacher-school" class="nctb-input" value="<?php echo esc_attr( $profile['school_name'] ); ?>" placeholder="উদাঃ ঢাকা কলেজিয়েট স্কুল">
					</div>
					<div class="nctb-form-row">
						<div class="nctb-form-group">
							<label for="teacher-division">বিভাগ</label>
							<select id="teacher-division" class="nctb-input">
								<?php foreach ( NCTB_Teacher_Profile::$allowed_divisions as $code => $lbl ) : ?>
									<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $profile['division'], $code ); ?>><?php echo esc_html( $lbl ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="nctb-form-group">
							<label for="teacher-district">জেলা</label>
							<input type="text" id="teacher-district" class="nctb-input" value="<?php echo esc_attr( $profile['district'] ); ?>" placeholder="উদাঃ ঢাকা">
						</div>
					</div>
					<div class="wizard-actions">
						<button type="button" class="nctb-btn nctb-btn-primary btn-next-step" data-current="1">পরবর্তী ধাপ ➔</button>
					</div>
				</div>

				<!-- Step 2 -->
				<div class="wizard-step-pane" id="wizard-step-2" style="display:none;">
					<h3>২. আপনি কোন কোন শ্রেণিতে ও বিষয়ে পাঠদান করেন?</h3>
					
					<label class="group-title">পাঠদানের শ্রেণি নির্বাচন করুন:</label>
					<div class="checkbox-grid">
						<?php foreach ( NCTB_Teacher_Profile::$allowed_classes as $cls_key => $cls_lbl ) : ?>
							<label class="nctb-choice-chip">
								<input type="checkbox" name="classes_taught" value="<?php echo esc_attr( $cls_key ); ?>" <?php checked( in_array( $cls_key, $profile['classes_taught'], true ) ); ?>>
								<span><?php echo esc_html( $cls_lbl ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>

					<label class="group-title" style="margin-top:20px;">পাঠদানের বিষয়সমূহ নির্বাচন করুন:</label>
					<div class="checkbox-grid">
						<?php foreach ( NCTB_Teacher_Profile::$allowed_subjects as $sub_key => $sub_lbl ) : ?>
							<label class="nctb-choice-chip">
								<input type="checkbox" name="subjects_taught" value="<?php echo esc_attr( $sub_key ); ?>" <?php checked( in_array( $sub_key, $profile['subjects_taught'], true ) ); ?>>
								<span><?php echo esc_html( $sub_lbl ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="wizard-actions">
						<button type="button" class="nctb-btn nctb-btn-outline btn-prev-step" data-current="2">⬅ পূর্ববর্তী</button>
						<button type="button" class="nctb-btn nctb-btn-primary btn-next-step" data-current="2">পরবর্তী ধাপ ➔</button>
					</div>
				</div>

				<!-- Step 3 -->
				<div class="wizard-step-pane" id="wizard-step-3" style="display:none;">
					<h3>৩. আপনার শিক্ষাদান লক্ষ্য ও প্রয়োজনসমূহ</h3>
					<div class="checkbox-grid vertical">
						<?php foreach ( NCTB_Teacher_Profile::$allowed_goals as $goal_key => $goal_lbl ) : ?>
							<label class="nctb-choice-chip">
								<input type="checkbox" name="teaching_goals" value="<?php echo esc_attr( $goal_key ); ?>" <?php checked( in_array( $goal_key, $profile['teaching_goals'], true ) ); ?>>
								<span><?php echo esc_html( $goal_lbl ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="nctb-form-group" style="margin-top:20px;">
						<label for="teacher-bio">সংক্ষিপ্ত শিক্ষক পরিচিতি (ঐচ্ছিক)</label>
						<textarea id="teacher-bio" class="nctb-input" rows="3" placeholder="আপনার শিক্ষকতা অভিজ্ঞতা ও আগ্রহ সম্পর্কে লিখুন..."><?php echo esc_textarea( $profile['bio'] ); ?></textarea>
					</div>

					<div class="wizard-actions">
						<button type="button" class="nctb-btn nctb-btn-outline btn-prev-step" data-current="3">⬅ পূর্ববর্তী</button>
						<button type="button" class="nctb-btn nctb-btn-success" id="btn-complete-onboarding">🚀 প্রোফাইল সংরক্ষণ ও ড্যাশবোর্ডে প্রবেশ</button>
					</div>
				</div>
			</div>
		</div>

		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const nonce = '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>';
			const apiUrl = '<?php echo esc_url_raw( rest_url( 'nctb/v1/teacher/onboarding/' ) ); ?>';

			function showStep(step) {
				document.querySelectorAll('.wizard-step-pane').forEach(p => p.style.display = 'none');
				document.querySelectorAll('.step-indicator').forEach(ind => {
					const s = parseInt(ind.getAttribute('data-step'));
					ind.classList.toggle('active', s === step);
					ind.classList.toggle('completed', s < step);
				});
				const pane = document.getElementById('wizard-step-' + step);
				if (pane) pane.style.display = 'block';
			}

			document.querySelectorAll('.btn-next-step').forEach(btn => {
				btn.addEventListener('click', function() {
					const current = parseInt(this.getAttribute('data-current'));
					let payload = {};

					if (current === 1) {
						payload = {
							display_name: document.getElementById('teacher-name').value,
							school_name: document.getElementById('teacher-school').value,
							division: document.getElementById('teacher-division').value,
							district: document.getElementById('teacher-district').value,
						};
					} else if (current === 2) {
						const classes = Array.from(document.querySelectorAll('input[name="classes_taught"]:checked')).map(c => c.value);
						const subjects = Array.from(document.querySelectorAll('input[name="subjects_taught"]:checked')).map(s => s.value);
						payload = { classes_taught: classes, subjects_taught: subjects };
					}

					fetch(apiUrl + 'step?step=' + current, {
						method: 'POST',
						headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
						body: JSON.stringify(payload)
					}).then(r => r.json()).then(res => {
						if (res.success) {
							showStep(current + 1);
						}
					});
				});
			});

			document.querySelectorAll('.btn-prev-step').forEach(btn => {
				btn.addEventListener('click', function() {
					const current = parseInt(this.getAttribute('data-current'));
					showStep(current - 1);
				});
			});

			const btnComplete = document.getElementById('btn-complete-onboarding');
			if (btnComplete) {
				btnComplete.addEventListener('click', function() {
					const goals = Array.from(document.querySelectorAll('input[name="teaching_goals"]:checked')).map(g => g.value);
					const bio = document.getElementById('teacher-bio').value;

					fetch(apiUrl + 'step?step=3', {
						method: 'POST',
						headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
						body: JSON.stringify({ teaching_goals: goals, bio: bio })
					}).then(() => {
						return fetch(apiUrl + 'complete', {
							method: 'POST',
							headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }
						});
					}).then(r => r.json()).then(res => {
						if (res.success && res.redirect_url) {
							window.location.href = res.redirect_url;
						}
					});
				});
			}
		});
		</script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Teacher Dashboard.
	 *
	 * @return string HTML output.
	 */
	public static function render_dashboard() {
		if ( ! is_user_logged_in() ) {
			$login_url = wp_login_url( home_url( '/teacher-dashboard/' ) );
			return '<div class="nctb-card text-center" style="max-width:500px;margin:40px auto;padding:32px;">' .
				'<h2>🔒 শিক্ষক ড্যাশবোর্ড</h2>' .
				'<p>শিক্ষক ড্যাশবোর্ডে প্রবেশ করতে অনুগ্রহ করে আপনার অ্যাকাউন্টে লগইন করুন।</p>' .
				'<a href="' . esc_url( $login_url ) . '" class="nctb-btn nctb-btn-primary">🔑 লগইন করুন</a>' .
				'</div>';
		}

		$user_id = get_current_user_id();
		$profile = NCTB_Teacher_Profile::get_profile( $user_id );

		ob_start();
		?>
		<div class="nctb-teacher-dashboard-screen">
			<!-- Teacher Hero Banner -->
			<div class="teacher-hero-card">
				<div class="teacher-hero-content">
					<div class="teacher-badge-row">
						<span class="role-badge">🎓 শিক্ষক হাব (Shikkhok Hub)</span>
						<span class="status-badge status-<?php echo esc_attr( $profile['verification_status'] ); ?>">
							<?php echo 'verified' === $profile['verification_status'] ? '✓ ভেরিফায়েড শিক্ষক' : '⏳ প্রোফাইল সক্রিয়'; ?>
						</span>
					</div>
					<h1>স্বাগতম, <?php echo esc_html( $profile['display_name'] ?: 'শিক্ষক মহোদয়' ); ?>!</h1>
					<p class="school-lead">🏫 <?php echo esc_html( $profile['school_name'] ?: 'বিদ্যালয়ের নাম যুক্ত করুন' ); ?> · 📍 <?php echo esc_html( $profile['district'] ?: 'বাংলাদেশ' ); ?></p>
				</div>
				<div class="teacher-hero-actions">
					<a href="<?php echo esc_url( home_url( '/teacher-onboarding/' ) ); ?>" class="nctb-btn nctb-btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.4);">
						⚙️ প্রোফাইল সম্পাদনা
					</a>
				</div>
			</div>

			<!-- Pedagogical Quick Tools Grid -->
			<div class="teacher-section-title">
				<h2>🛠️ ক্লাসরুম ও পাঠদান টুলস (Teaching Tools)</h2>
				<p>শিক্ষাদানের প্রস্তুতি সহজ করতে আধুনিক এআই ও কারিকুলাম অ্যাসিস্ট্যান্ট।</p>
			</div>

			<div class="teacher-tools-grid">
				<div class="tool-card">
					<div class="tool-icon">📝</div>
					<h3>এআই লেসন প্ল্যানার (AI Lesson Planner)</h3>
					<p>NCTB শিখনফল ভিত্তিক ৪৫ মিনিটের সুবিন্যস্ত ক্লাস পাঠ পরিকল্পনা ও অ্যাক্টিভিটি তৈরি করুন।</p>
					<a href="<?php echo esc_url( home_url( '/books/' ) ); ?>" class="tool-link">লেসন প্ল্যান তৈরি করুন ➔</a>
				</div>

				<div class="tool-card">
					<div class="tool-icon">⚡</div>
					<h3>প্রশ্নপত্র ও কুইজ প্রণয়ন (Quiz Maker)</h3>
					<p>শ্রেণিকক্ষের মূল্যায়ন বা মডেল টেস্টের জন্য বহুনির্বাচনী ও সৃজনশীল প্রশ্ন জেনারেট করুন।</p>
					<a href="<?php echo esc_url( home_url( '/board-questions/' ) ); ?>" class="tool-link">প্রশ্নব্যাংক খুলুন ➔</a>
				</div>

				<div class="tool-card">
					<div class="tool-icon">🔍</div>
					<h3>শিক্ষার্থীদের ভুল নির্ণয় ও সমাধান</h3>
					<p>বোর্ড পরীক্ষা ও অনুশীলনে শিক্ষার্থীরা যেসব ধারণায় বেশি ভুল করে তার বিশ্লেষণ ও প্রতিকার।</p>
					<a href="<?php echo esc_url( home_url( '/board-analytics/' ) ); ?>" class="tool-link">অ্যানালিটিক্স দেখুন ➔</a>
				</div>

				<div class="tool-card">
					<div class="tool-icon">📚</div>
					<h3>NCTB ডিজিটাল শিক্ষক নির্দেশিকা</h3>
					<p>জাতীয় শিক্ষাক্রমের পূর্ণাঙ্গ সিলেবাস, পাঠ্যবই এবং শিক্ষক সহায়িকা ডাউনলোড করুন।</p>
					<a href="<?php echo esc_url( home_url( '/books/' ) ); ?>" class="tool-link">পাঠ্যবইসমূহ দেখুন ➔</a>
				</div>
			</div>

			<!-- Teacher AI Pedagogical Workbench (Phase 19) -->
			<div class="teacher-ai-workbench-card" id="teacher-ai-workbench">
				<div class="workbench-header">
					<div class="workbench-title-wrap">
						<span class="ai-badge">🤖 NCTB Teacher AI</span>
						<h2>শিক্ষক এআই ওয়ার্কবেঞ্চ (AI Pedagogical Assistant)</h2>
						<p>শ্রেণিকক্ষের পাঠ পরিকল্পনা, কুইজ পেপার এবং ভুল ধারণা বিশ্লেষণ তৈরি করুন এক ক্লিকে।</p>
					</div>
					<div class="workbench-quota-badge" id="ai-quota-display">
						<span>⏳ দৈনিক এআই কোটা: যাচাই হচ্ছে...</span>
					</div>
				</div>

				<!-- Workbench Tabs Navigation -->
				<div class="workbench-nav-tabs">
					<button type="button" class="tab-btn active" data-tab="lesson-planner">📝 ৪৫-মিনিট লেসন প্ল্যানার</button>
					<button type="button" class="tab-btn" data-tab="quiz-maker">⚡ ক্লাসরুম কুইজ ও প্রশ্নপত্র</button>
					<button type="button" class="tab-btn" data-tab="misconceptions">🔍 ভুল ধারণা ও প্রতিকার গাইড</button>
				</div>

				<!-- Tab 1: Lesson Planner Form -->
				<div class="workbench-tab-pane active" id="tab-lesson-planner">
					<div class="workbench-form-grid">
						<div class="nctb-form-group">
							<label>টার্গেট শ্রেণি</label>
							<select id="lp-class" class="nctb-input">
								<option value="Class 9-10 (SSC)">Class 9-10 (SSC)</option>
								<option value="HSC 1st Year">HSC 1st Year (Class 11)</option>
								<option value="HSC 2nd Year">HSC 2nd Year (Class 12)</option>
								<option value="Class 8 (JSC)">Class 8 (JSC)</option>
								<option value="Class 6-7">Class 6-7</option>
							</select>
						</div>
						<div class="nctb-form-group">
							<label>বিষয়</label>
							<input type="text" id="lp-subject" class="nctb-input" value="English 2nd Paper" placeholder="উদাঃ English, ICT, Math, Physics">
						</div>
						<div class="nctb-form-group">
							<label>টপিক / অধ্যায়</label>
							<input type="text" id="lp-topic" class="nctb-input" value="Right Form of Verbs: Conditionals" placeholder="উদাঃ Modifiers, Right Form of Verbs, ইত্যাদি">
						</div>
						<div class="nctb-form-group">
							<label>ক্লাসের সময়কাল</label>
							<select id="lp-duration" class="nctb-input">
								<option value="45">৪৫ মিনিট (স্ট্যান্ডার্ড)</option>
								<option value="40">৪০ মিনিট</option>
								<option value="50">৫০ মিনিট</option>
							</select>
						</div>
					</div>
					<button type="button" class="nctb-btn nctb-btn-primary" id="btn-generate-lesson-plan">🚀 পাঠ পরিকল্পনা তৈরি করুন</button>
				</div>

				<!-- Tab 2: Quiz Maker Form -->
				<div class="workbench-tab-pane" id="tab-quiz-maker" style="display:none;">
					<div class="workbench-form-grid">
						<div class="nctb-form-group">
							<label>টার্গেট শ্রেণি</label>
							<select id="qm-class" class="nctb-input">
								<option value="Class 10 (SSC)">Class 10 (SSC)</option>
								<option value="HSC 2nd Year">HSC 2nd Year</option>
								<option value="Class 9">Class 9</option>
								<option value="Class 8 (JSC)">Class 8 (JSC)</option>
							</select>
						</div>
						<div class="nctb-form-group">
							<label>বিষয়</label>
							<input type="text" id="qm-subject" class="nctb-input" value="English 1st Paper" placeholder="উদাঃ English, ICT, Math">
						</div>
						<div class="nctb-form-group">
							<label>টপিক / প্যাসেজ</label>
							<input type="text" id="qm-topic" class="nctb-input" value="Nelson Mandela Comprehension & Vocabulary" placeholder="উদাঃ Completing Sentences">
						</div>
						<div class="nctb-form-group">
							<label>প্রশ্নের সংখ্যা ও মান</label>
							<select id="qm-count" class="nctb-input">
								<option value="5">৫টি প্রশ্ন (কুইক ক্লাস টেস্ট)</option>
								<option value="10">১০টি প্রশ্ন (মডেল টেস্ট)</option>
							</select>
						</div>
					</div>
					<button type="button" class="nctb-btn nctb-btn-primary" id="btn-generate-quiz">⚡ প্রশ্নপত্র ও উত্তরমালা তৈরি করুন</button>
				</div>

				<!-- Tab 3: Misconceptions Form -->
				<div class="workbench-tab-pane" id="tab-misconceptions" style="display:none;">
					<div class="workbench-form-grid">
						<div class="nctb-form-group">
							<label>টার্গেট শ্রেণি</label>
							<select id="mc-class" class="nctb-input">
								<option value="HSC 2nd Year">HSC 2nd Year</option>
								<option value="SSC (Class 10)">SSC (Class 10)</option>
								<option value="Class 8 (JSC)">Class 8</option>
							</select>
						</div>
						<div class="nctb-form-group">
							<label>বিষয়</label>
							<input type="text" id="mc-subject" class="nctb-input" value="English Grammar" placeholder="বিষয়">
						</div>
						<div class="nctb-form-group" style="grid-column: span 2;">
							<label>টপিক</label>
							<input type="text" id="mc-topic" class="nctb-input" value="Modifiers vs Connectors" placeholder="টপিক">
						</div>
					</div>
					<button type="button" class="nctb-btn nctb-btn-primary" id="btn-generate-misconceptions">🔍 ভুল ধারণা বিশ্লেষণ করুন</button>
				</div>

				<!-- AI Output Display Box -->
				<div class="workbench-output-box" id="workbench-output-box" style="display:none;">
					<div class="output-header-bar">
						<span class="output-title">📄 এআই ফলাফল</span>
						<div class="output-actions">
							<button type="button" class="nctb-btn nctb-btn-sm nctb-btn-outline" id="btn-copy-ai-output">📋 কপি করুন</button>
							<button type="button" class="nctb-btn nctb-btn-sm nctb-btn-outline" onclick="window.print();">🖨️ প্রিন্ট / PDF</button>
						</div>
					</div>
					<div class="output-content nctb-prose" id="workbench-output-content"></div>
				</div>
			</div>

			<!-- Classroom Resources & Ready-Made Lesson Plans (Phase 24) -->
			<div class="teacher-resources-section" id="classroom-resources">
				<div class="teacher-section-title">
					<h2>📚 ক্লাসরুম রিসোর্স ও রেডিমেড লেসন প্ল্যান (Classroom Teaching Aids)</h2>
					<p>ফটোকপি ও ক্লাসরুম পরীক্ষার উপযোগী প্রশ্নপত্র, ৪৫ মিনিটের পাঠ পরিকল্পনা এবং স্লাইড ডেক।</p>
				</div>

				<div class="teacher-resources-grid">
					<?php
					$resources = NCTB_Teacher_Resources_Service::get_resources();
					foreach ( $resources as $res ) :
						$type_icons = array(
							'lesson_plan' => '📋 লেসন প্ল্যান',
							'worksheet'   => '📝 কুইজ শিট',
							'slides'      => '📽️ স্লাইড ডেক',
							'rubric'      => '📊 মূল্যায়ন রুব্রিক',
						);
						$type_badge = $type_icons[ $res['type'] ] ?? $res['type'];
						?>
						<div class="resource-card" data-id="<?php echo esc_attr( $res['id'] ); ?>">
							<div class="resource-card-head">
								<span class="resource-type-badge"><?php echo esc_html( $type_badge ); ?></span>
								<span class="resource-duration"><?php echo esc_html( $res['duration'] ); ?></span>
							</div>
							<h3 class="resource-title"><?php echo esc_html( $res['title'] ); ?></h3>
							<p class="resource-desc"><?php echo esc_html( $res['description'] ); ?></p>
							<div class="resource-meta-row">
								<span class="res-tag">📘 <?php echo esc_html( $res['subject'] ); ?></span>
								<span class="res-tag">🏫 <?php echo esc_html( $res['class'] ); ?></span>
							</div>
							<div class="resource-card-actions">
								<button type="button" class="nctb-btn nctb-btn-sm nctb-btn-primary btn-view-resource" data-resource-id="<?php echo esc_attr( $res['id'] ); ?>">
									👁️ ওপেন ও প্রিন্ট
								</button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Resource Modal -->
			<div class="nctb-modal" id="resource-viewer-modal" style="display:none;">
				<div class="nctb-modal-dialog" style="max-width:800px;background:#fff;border-radius:12px;padding:24px;margin:50px auto;box-shadow:0 20px 40px rgba(0,0,0,0.2);">
					<div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #e2e8f0;padding-bottom:12px;margin-bottom:16px;">
						<h3 id="res-modal-title" style="margin:0;">রিসোর্স প্রিভিউ</h3>
						<button type="button" class="modal-close-btn" id="btn-close-res-modal" style="background:none;border:none;font-size:24px;cursor:pointer;">&times;</button>
					</div>
					<div class="modal-actions-row" style="margin-bottom:16px;display:flex;gap:10px;">
						<button type="button" class="nctb-btn nctb-btn-sm nctb-btn-outline" id="btn-copy-res-content">📋 কপি করুন</button>
						<button type="button" class="nctb-btn nctb-btn-sm nctb-btn-primary" onclick="window.print();">🖨️ প্রিন্ট / PDF ডাউনলোড</button>
					</div>
					<div class="modal-body nctb-prose" id="res-modal-content" style="max-height:60vh;overflow-y:auto;padding:12px;background:#f8fafc;border-radius:8px;"></div>
				</div>
			</div>

			<!-- Teaching Classes & Subjects Widget -->
			<div class="teacher-classes-card">
				<h3>📖 আপনার পাঠদানের বিষয় ও শ্রেণিসমূহ</h3>
				<div class="chips-container">
					<?php
					if ( ! empty( $profile['subjects_taught'] ) ) {
						foreach ( $profile['subjects_taught'] as $sub ) {
							$lbl = NCTB_Teacher_Profile::$allowed_subjects[ $sub ] ?? $sub;
							echo '<span class="class-chip subject">📘 ' . esc_html( $lbl ) . '</span>';
						}
					}
					if ( ! empty( $profile['classes_taught'] ) ) {
						foreach ( $profile['classes_taught'] as $cls ) {
							$lbl = NCTB_Teacher_Profile::$allowed_classes[ $cls ] ?? $cls;
							echo '<span class="class-chip grade">🏫 ' . esc_html( $lbl ) . '</span>';
						}
					}
					?>
				</div>
			</div>
		</div>

		<!-- Teacher AI Workbench Script -->
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const nonce = '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>';
			const baseAiUrl = '<?php echo esc_url_raw( rest_url( 'nctb/v1/teacher/ai/' ) ); ?>';

			const quotaDisplay = document.getElementById('ai-quota-display');
			const outputBox = document.getElementById('workbench-output-box');
			const outputContent = document.getElementById('workbench-output-content');
			const copyBtn = document.getElementById('btn-copy-ai-output');

			// Refresh Quota
			function checkQuota() {
				fetch(baseAiUrl + 'quota', {
					headers: { 'X-WP-Nonce': nonce }
				}).then(r => r.json()).then(res => {
					if (quotaDisplay && res.usage) {
						quotaDisplay.innerHTML = `<span>⚡ দৈনিক এআই কোটা: ${res.usage.remaining} / ${res.usage.daily_limit} বাকি</span>`;
					}
				}).catch(() => {});
			}
			checkQuota();

			// Tab Switching
			document.querySelectorAll('.workbench-nav-tabs .tab-btn').forEach(btn => {
				btn.addEventListener('click', function() {
					document.querySelectorAll('.workbench-nav-tabs .tab-btn').forEach(b => b.classList.remove('active'));
					document.querySelectorAll('.workbench-tab-pane').forEach(p => p.style.display = 'none');

					this.classList.add('active');
					const tabId = this.getAttribute('data-tab');
					const pane = document.getElementById('tab-' + tabId);
					if (pane) pane.style.display = 'block';
				});
			});

			function callAi(endpoint, body, btn) {
				const originalText = btn.innerHTML;
				btn.disabled = true;
				btn.innerHTML = '⏳ এআই তৈরি করছে...';
				outputBox.style.display = 'none';

				fetch(baseAiUrl + endpoint, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
					body: JSON.stringify(body)
				}).then(r => r.json()).then(res => {
					btn.disabled = false;
					btn.innerHTML = originalText;

					if (res.code === 'ai_paywall_required') {
						outputBox.style.display = 'block';
						outputContent.innerHTML = `<div class="nctb-paywall-notice">
							<h3>🔒 এআই এক্সেস পাস প্রয়োজন</h3>
							<p>${res.message}</p>
							<a href="${res.data.upgrade_url}" class="nctb-btn nctb-btn-primary">⭐ এআই সাবস্ক্রিপশন নিন</a>
						</div>`;
						return;
					}

					if (res.success) {
						outputBox.style.display = 'block';
						const text = res.plan || res.quiz || res.guide || '';
						outputContent.innerHTML = text.replace(/\\n/g, '<br>').replace(/### (.*?)(<br>|$)/g, '<h3>$1</h3>').replace(/## (.*?)(<br>|$)/g, '<h2>$1</h2>').replace(/# (.*?)(<br>|$)/g, '<h1>$1</h1>').replace(/\\*\\*(.*?)\\*\\*/g, '<strong>$1</strong>');
						checkQuota();
						outputBox.scrollIntoView({ behavior: 'smooth' });
					} else {
						alert(res.message || 'Error running AI tool');
					}
				}).catch(() => {
					btn.disabled = false;
					btn.innerHTML = originalText;
					alert('Connection error');
				});
			}

			// Generate Lesson Plan
			const btnLp = document.getElementById('btn-generate-lesson-plan');
			if (btnLp) {
				btnLp.addEventListener('click', function() {
					callAi('lesson-plan', {
						class: document.getElementById('lp-class').value,
						subject: document.getElementById('lp-subject').value,
						topic: document.getElementById('lp-topic').value,
						duration: parseInt(document.getElementById('lp-duration').value)
					}, this);
				});
			}

			// Generate Quiz
			const btnQm = document.getElementById('btn-generate-quiz');
			if (btnQm) {
				btnQm.addEventListener('click', function() {
					callAi('quiz-maker', {
						class: document.getElementById('qm-class').value,
						subject: document.getElementById('qm-subject').value,
						topic: document.getElementById('qm-topic').value,
						count: parseInt(document.getElementById('qm-count').value),
						difficulty: 'medium'
					}, this);
				});
			}

			// Generate Misconceptions
			const btnMc = document.getElementById('btn-generate-misconceptions');
			if (btnMc) {
				btnMc.addEventListener('click', function() {
					callAi('misconceptions', {
						class: document.getElementById('mc-class').value,
						subject: document.getElementById('mc-subject').value,
						topic: document.getElementById('mc-topic').value
					}, this);
				});
			}

			// Copy Output
			if (copyBtn) {
				copyBtn.addEventListener('click', function() {
					navigator.clipboard.writeText(outputContent.innerText).then(() => {
						const orig = copyBtn.innerText;
						copyBtn.innerText = '✓ কপি হয়েছে!';
						setTimeout(() => copyBtn.innerText = orig, 2000);
					});
				});
			}

			// Resource Viewer Modal
			const resModal = document.getElementById('resource-viewer-modal');
			const resTitle = document.getElementById('res-modal-title');
			const resContent = document.getElementById('res-modal-content');
			const resClose = document.getElementById('btn-close-res-modal');
			const resCopy = document.getElementById('btn-copy-res-content');

			document.querySelectorAll('.btn-view-resource').forEach(b => {
				b.addEventListener('click', function() {
					const id = this.getAttribute('data-resource-id');
					fetch(`${window.location.origin}/wp-json/nctb/v1/teacher/resources/${id}`)
						.then(r => r.json())
						.then(data => {
							if (data.success && data.resource) {
								resTitle.innerText = data.resource.title;
								let html = data.resource.content;
								if (!html.includes('<div class="nctb-print-exam-sheet">')) {
									html = html.replace(/\n/g, '<br>').replace(/### (.*?)(<br>|$)/g, '<h3>$1</h3>').replace(/## (.*?)(<br>|$)/g, '<h2>$1</h2>').replace(/#### (.*?)(<br>|$)/g, '<h4>$1</h4>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
								}
								resContent.innerHTML = html;
								resModal.style.display = 'block';
							}
						});
				});
			});

			if (resClose) {
				resClose.addEventListener('click', () => resModal.style.display = 'none');
			}
			if (resCopy) {
				resCopy.addEventListener('click', function() {
					navigator.clipboard.writeText(resContent.innerText).then(() => {
						const orig = resCopy.innerText;
						resCopy.innerText = '✓ কপি হয়েছে!';
						setTimeout(() => resCopy.innerText = orig, 2000);
					});
				});
			}
		});
		</script>
		<?php
		return ob_get_clean();
	}
}
