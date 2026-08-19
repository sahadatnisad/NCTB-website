<?php
/**
 * Front-end controller for NCTB Learning Hub.
 *
 * Handles student shortcodes, asset loading, and redirect logic for onboarding.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Public
 */
class NCTB_Public {

	/**
	 * Wire front-end hooks.
	 */
	public function __construct() {
		add_shortcode( 'nctb_onboarding', array( $this, 'render_onboarding_shortcode' ) );
		add_shortcode( 'nctb_student_dashboard', array( $this, 'render_dashboard_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'template_redirect', array( $this, 'handle_onboarding_redirects' ) );
	}

	/**
	 * Enqueue front-end assets conditionally.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Only enqueue on onboarding or dashboard pages/shortcodes.
		global $post;
		$is_onboarding = is_page( 'onboarding' ) || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'nctb_onboarding' ) );
		$is_dashboard  = is_page( 'dashboard' ) || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'nctb_student_dashboard' ) );

		if ( $is_onboarding || $is_dashboard ) {
			wp_enqueue_style(
				'nctb-onboarding-style',
				get_stylesheet_directory_uri() . '/css/onboarding.css',
				array(),
				NCTB_LH_VERSION
			);

			wp_enqueue_script(
				'nctb-onboarding-script',
				get_stylesheet_directory_uri() . '/js/onboarding.js',
				array( 'jquery' ),
				NCTB_LH_VERSION,
				true
			);

			$current_user_id = get_current_user_id();
			$profile         = $current_user_id ? NCTB_Student_Profile::get_profile( $current_user_id ) : null;

			wp_localize_script(
				'nctb-onboarding-script',
				'nctbData',
				array(
					'root'         => esc_url_raw( rest_url() ),
					'nonce'        => wp_create_nonce( 'wp_rest' ),
					'isLoggedIn'   => is_user_logged_in(),
					'loginUrl'     => wp_login_url( home_url( '/onboarding' ) ),
					'dashboardUrl' => home_url( '/dashboard' ),
					'profile'      => $profile,
					'levels'       => NCTB_Student_Profile::ALLOWED_LEVELS,
					'languages'    => NCTB_Student_Profile::ALLOWED_LANGUAGES,
					'subjects'     => NCTB_Student_Profile::ALLOWED_SUBJECTS,
				)
			);
		}
	}

	/**
	 * Handle automatic redirects between onboarding and dashboard.
	 *
	 * @return void
	 */
	public function handle_onboarding_redirects() {
		if ( ! is_user_logged_in() ) {
			if ( is_page( 'onboarding' ) || is_page( 'dashboard' ) ) {
				// Let the page render a friendly login card instead of raw redirect.
				return;
			}
			return;
		}

		$user_id     = get_current_user_id();
		$is_complete = NCTB_Student_Profile::is_onboarding_complete( $user_id );

		// If onboarding is complete and visiting /onboarding, send to /dashboard.
		if ( is_page( 'onboarding' ) && $is_complete && ! isset( $_GET['edit'] ) ) {
			wp_safe_redirect( home_url( '/dashboard' ) );
			exit;
		}

		// If visiting dashboard but onboarding is incomplete, redirect to /onboarding.
		if ( is_page( 'dashboard' ) && ! $is_complete ) {
			wp_safe_redirect( home_url( '/onboarding' ) );
			exit;
		}
	}

	/**
	 * Render Onboarding UI shortcode.
	 *
	 * @return string
	 */
	public function render_onboarding_shortcode() {
		if ( ! is_user_logged_in() ) {
			return $this->render_auth_required_card( __( 'Student Onboarding', 'nctb-learning-hub' ) );
		}

		$user_id = get_current_user_id();
		$profile = NCTB_Student_Profile::get_profile( $user_id );

		ob_start();
		?>
		<div class="nctb-onboarding-container" id="nctb-onboarding-app" data-step="<?php echo esc_attr( $profile['onboarding_step'] ); ?>">
			<!-- Progress Header -->
			<div class="nctb-onboarding-header">
				<div class="nctb-badge">🎯 NCTB Learning Hub</div>
				<h1 class="nctb-title">শিক্ষার্থী প্রোফাইল সেটআপ (Student Setup)</h1>
				<p class="nctb-subtitle">আপনার শ্রেণি এবং পাঠ্যক্রম বেছে নিয়ে ব্যক্তিগতকৃত এআই টিউটর চালু করুন।</p>
				
				<div class="nctb-stepper">
					<div class="step-indicator active" data-step="1">
						<span class="step-num">১</span>
						<span class="step-name">শ্রেণি / লেভেল</span>
					</div>
					<div class="step-line"></div>
					<div class="step-indicator" data-step="2">
						<span class="step-num">২</span>
						<span class="step-name">বিষয় নির্বাচন</span>
					</div>
					<div class="step-line"></div>
					<div class="step-indicator" data-step="3">
						<span class="step-num">৩</span>
						<span class="step-name">ব্যাখ্যার ভাষা</span>
					</div>
					<div class="step-line"></div>
					<div class="step-indicator" data-step="4">
						<span class="step-num">৪</span>
						<span class="step-name">পরীক্ষার লক্ষ্য</span>
					</div>
				</div>
			</div>

			<!-- Form Card -->
			<div class="nctb-card nctb-onboarding-card">
				<div id="nctb-alert" class="nctb-alert" style="display:none;"></div>

				<!-- Step 1: Education Level -->
				<div class="onboarding-step-view" id="step-1-view">
					<h2>ধাপ ১: আপনার শ্রেণি বা শিক্ষাস্তর নির্বাচন করুন</h2>
					<p class="desc">আপনার বর্তমান শ্রেণি অনুযায়ী এনসিটিবি সিলেবাস লোড হবে।</p>
					
					<div class="nctb-grid-options">
						<?php foreach ( NCTB_Student_Profile::ALLOWED_LEVELS as $key => $label ) : ?>
							<label class="nctb-option-card">
								<input type="radio" name="education_level" value="<?php echo esc_attr( $key ); ?>" <?php checked( $profile['education_level'], $key ); ?>>
								<div class="option-content">
									<div class="option-title"><?php echo esc_html( $label ); ?></div>
								</div>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="nctb-field-group">
						<label for="class_session">শিক্ষাবর্ষ / ব্যাচ (Session):</label>
						<input type="text" id="class_session" name="class_session" class="nctb-input" value="<?php echo esc_attr( $profile['class_session'] ); ?>" placeholder="e.g. 2026">
					</div>

					<div class="nctb-actions">
						<button type="button" class="nctb-btn nctb-btn-primary" id="btn-step-1-next">পরবর্তী ধাপ (Next) →</button>
					</div>
				</div>

				<!-- Step 2: Subject Selection -->
				<div class="onboarding-step-view" id="step-2-view" style="display:none;">
					<h2>ধাপ ২: আপনার অধ্যয়নের বিষয়সমূহ বেছে নিন</h2>
					<p class="desc">আপনি যে বিষয়গুলোতে এআই টিউটর ও প্র্যাকটিস করতে চান সেগুলো সিলেক্ট করুন।</p>
					
					<div class="nctb-grid-options">
						<?php foreach ( NCTB_Student_Profile::ALLOWED_SUBJECTS as $slug => $sub ) : ?>
							<label class="nctb-option-card checkbox-card">
								<input type="checkbox" name="chosen_subjects[]" value="<?php echo esc_attr( $slug ); ?>" <?php echo in_array( $slug, $profile['chosen_subjects'], true ) ? 'checked' : ''; ?>>
								<div class="option-content">
									<div class="option-title"><?php echo esc_html( $sub['title_bn'] ); ?></div>
									<div class="option-sub"><?php echo esc_html( $sub['title_en'] ); ?></div>
								</div>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="nctb-actions">
						<button type="button" class="nctb-btn nctb-btn-secondary btn-prev" data-target="1">← পূর্ববর্তী</button>
						<button type="button" class="nctb-btn nctb-btn-primary" id="btn-step-2-next">পরবর্তী ধাপ (Next) →</button>
					</div>
				</div>

				<!-- Step 3: Explanation Language Preference -->
				<div class="onboarding-step-view" id="step-3-view" style="display:none;">
					<h2>ধাপ ৩: এআই টিউটরের ব্যাখ্যার ভাষা পছন্দ করুন</h2>
					<p class="desc">টিউটর পাঠ বুঝিয়ে দেওয়ার সময় আপনার পছন্দের ভাষা প্রাধান্য দেবে।</p>
					
					<div class="nctb-grid-options">
						<?php foreach ( NCTB_Student_Profile::ALLOWED_LANGUAGES as $lang_key => $lang_label ) : ?>
							<label class="nctb-option-card">
								<input type="radio" name="explanation_language" value="<?php echo esc_attr( $lang_key ); ?>" <?php checked( $profile['explanation_language'], $lang_key ); ?>>
								<div class="option-content">
									<div class="option-title"><?php echo esc_html( $lang_label ); ?></div>
								</div>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="nctb-actions">
						<button type="button" class="nctb-btn nctb-btn-secondary btn-prev" data-target="2">← পূর্ববর্তী</button>
						<button type="button" class="nctb-btn nctb-btn-primary" id="btn-step-3-next">পরবর্তী ধাপ (Next) →</button>
					</div>
				</div>

				<!-- Step 4: Target Exam Session & Finalize -->
				<div class="onboarding-step-view" id="step-4-view" style="display:none;">
					<h2>ধাপ ৪: লক্ষ্য ও পরীক্ষা সেশন</h2>
					<p class="desc">আপনার লক্ষ্য নির্ধারণ করুন যাতে এআই রিভিশন ও প্র্যাকটিস শিডিউল সাজাতে পারে।</p>
					
					<div class="nctb-field-group">
						<label for="target_exam_session">টার্গেট বোর্ড পরীক্ষা / লক্ষ্য (ঐচ্ছিক):</label>
						<input type="text" id="target_exam_session" name="target_exam_session" class="nctb-input" value="<?php echo esc_attr( $profile['target_exam_session'] ); ?>" placeholder="e.g. SSC Exam 2026 / Annual Exam">
					</div>

					<div class="nctb-summary-box">
						<h3>📋 আপনার অনবোর্ডিং সারসংক্ষেপ:</h3>
						<p><strong>শিক্ষার্থী:</strong> <?php echo esc_html( $profile['display_name'] ); ?></p>
						<p><strong>ইমেইল:</strong> <?php echo esc_html( $profile['email'] ); ?></p>
					</div>

					<div class="nctb-actions">
						<button type="button" class="nctb-btn nctb-btn-secondary btn-prev" data-target="3">← পূর্ববর্তী</button>
						<button type="button" class="nctb-btn nctb-btn-success" id="btn-step-4-complete">🎉 প্রোফাইল সম্পন্ন করুন ও ড্যাশবোর্ডে যান</button>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Student Dashboard UI shortcode.
	 *
	 * @return string
	 */
	public function render_dashboard_shortcode() {
		if ( ! is_user_logged_in() ) {
			return $this->render_auth_required_card( __( 'Student Dashboard', 'nctb-learning-hub' ) );
		}

		$user_id = get_current_user_id();
		$profile = NCTB_Student_Profile::get_profile( $user_id );

		ob_start();
		?>
		<div class="nctb-dashboard-container">
			<div class="nctb-dash-header">
				<div class="nctb-welcome-text">
					<h1>স্বাগতম, <?php echo esc_html( $profile['display_name'] ); ?>! 👋</h1>
					<p class="lead">এনসিটিবি ডিজিটাল লার্নিং হাবে আপনার পাঠ্যক্রম প্রস্তুত।</p>
				</div>
				<div class="nctb-dash-meta-badge">
					<span class="badge-tag"><?php echo esc_html( NCTB_Student_Profile::ALLOWED_LEVELS[ $profile['education_level'] ] ?? $profile['education_level'] ); ?></span>
					<span class="badge-tag badge-lang"><?php echo esc_html( NCTB_Student_Profile::ALLOWED_LANGUAGES[ $profile['explanation_language'] ] ?? $profile['explanation_language'] ); ?></span>
				</div>
			</div>

			<!-- Quick Status Grid -->
			<div class="nctb-grid-stats">
				<div class="stat-card">
					<div class="stat-icon">📚</div>
					<div class="stat-info">
						<div class="stat-num"><?php echo count( $profile['chosen_subjects'] ); ?> টি</div>
						<div class="stat-lbl">নিবন্ধিত বিষয়</div>
					</div>
				</div>
				<div class="stat-card">
					<div class="stat-icon">🎯</div>
					<div class="stat-info">
						<div class="stat-num"><?php echo esc_html( $profile['target_exam_session'] ?: 'নিয়মিত পাঠ' ); ?></div>
						<div class="stat-lbl">টার্গেট লক্ষ্য</div>
					</div>
				</div>
				<div class="stat-card">
					<div class="stat-icon">🤖</div>
					<div class="stat-info">
						<div class="stat-num">সক্রিয়</div>
						<div class="stat-lbl">এআই টিউটর মোড</div>
					</div>
				</div>
			</div>

			<!-- Enrolled Subjects Section -->
			<div class="nctb-card">
				<div class="card-header-flex">
					<h2>📖 আপনার অধ্যয়নের বিষয়সমূহ</h2>
					<a href="<?php echo esc_url( home_url( '/onboarding?edit=1' ) ); ?>" class="btn-text">⚙️ প্রোফাইল পরিবর্তন</a>
				</div>
				<div class="subjects-card-grid">
					<?php if ( ! empty( $profile['chosen_subjects'] ) ) : ?>
						<?php foreach ( $profile['chosen_subjects'] as $sub_slug ) :
							$sub = NCTB_Student_Profile::ALLOWED_SUBJECTS[ $sub_slug ] ?? null;
							if ( ! $sub ) continue;
						?>
							<a class="subject-item-card" href="<?php echo esc_url( get_post_type_archive_link( NCTB_Curriculum_CPT::CPT_BOOK ) ); ?>">
								<div class="sub-title"><?php echo esc_html( $sub['title_bn'] ); ?></div>
								<div class="sub-en"><?php echo esc_html( $sub['title_en'] ); ?></div>
								<div class="sub-status">📚 পাঠ্যবই দেখুন (Browse lessons) →</div>
							</a>
						<?php endforeach; ?>
					<?php else : ?>
						<p>কোনো বিষয় এখনো যোগ করা হয়নি। <a href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>">অনবোর্ডিং সম্পন্ন করুন</a></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render login required message card.
	 *
	 * @param string $feature_name Title of protected area.
	 * @return string
	 */
	private function render_auth_required_card( $feature_name ) {
		$login_url = wp_login_url( get_permalink() );
		$reg_url   = wp_registration_url();

		return sprintf(
			'<div class="nctb-card nctb-auth-card">
				<h2>🔒 %s এ প্রবেশ করতে লগইন করুন</h2>
				<p>শিক্ষার্থী প্রোফাইল এবং এআই লার্নিং ফিচার ব্যবহার করতে একাউন্টে প্রবেশ করুন অথবা নতুন একাউন্ট খুলুন।</p>
				<div class="nctb-actions">
					<a href="%s" class="nctb-btn nctb-btn-primary">লগইন করুন (Login)</a>
					%s
				</div>
			</div>',
			esc_html( $feature_name ),
			esc_url( $login_url ),
			get_option( 'users_can_register' ) ? '<a href="' . esc_url( $reg_url ) . '" class="nctb-btn nctb-btn-secondary">নতুন একাউন্ট (Register)</a>' : ''
		);
	}
}
