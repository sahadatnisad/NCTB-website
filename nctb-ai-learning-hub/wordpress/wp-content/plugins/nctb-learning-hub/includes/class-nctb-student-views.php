<?php
/**
 * Student-facing Views & Shortcodes (Phase 6).
 *
 * Implements shortcodes for:
 *   - [nctb_mistakes]: My Mistake Notebook
 *   - [nctb_revision_due]: Spaced Revision Queue
 *   - [nctb_progress]: Learning Progress & Concept Mastery
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Student_Views
 */
class NCTB_Student_Views {

	/**
	 * Register shortcodes.
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'nctb_mistakes', array( __CLASS__, 'render_mistakes' ) );
		add_shortcode( 'nctb_revision_due', array( __CLASS__, 'render_revision_due' ) );
		add_shortcode( 'nctb_progress', array( __CLASS__, 'render_progress' ) );
		add_shortcode( 'nctb_my_purchases', array( __CLASS__, 'render_purchases' ) );
		add_shortcode( 'nctb_board_questions', array( __CLASS__, 'render_board_questions' ) );
		add_shortcode( 'nctb_board_analytics', array( __CLASS__, 'render_board_analytics' ) );
	}

	/**
	 * Render My Mistakes shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function render_mistakes() {
		$user_id  = get_current_user_id() ?: 1;
		$mistakes = NCTB_Mistakes_Service::get_active_mistakes( $user_id );

		ob_start();
		?>
		<div class="nctb-student-screen nctb-mistakes-screen">
			<header class="nctb-screen-header">
				<h1>📕 <?php esc_html_e( 'My Mistake Notebook', 'nctb-learning-hub' ); ?></h1>
				<p class="lead"><?php esc_html_e( 'Review incorrect practice answers, understand the concepts, and master them through targeted revision.', 'nctb-learning-hub' ); ?></p>
			</header>

			<?php if ( empty( $mistakes ) ) : ?>
				<div class="nctb-empty-state">
					<div class="empty-icon">🎉</div>
					<h3><?php esc_html_e( 'Your mistake notebook is clear!', 'nctb-learning-hub' ); ?></h3>
					<p><?php esc_html_e( 'Great job! You have no active mistakes waiting for review.', 'nctb-learning-hub' ); ?></p>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>" class="nctb-btn nctb-btn-primary">
						📚 <?php esc_html_e( 'Browse Lessons', 'nctb-learning-hub' ); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="nctb-mistakes-list">
					<?php foreach ( $mistakes as $m ) : ?>
						<article class="nctb-mistake-card" id="mistake-card-<?php echo esc_attr( $m->id ); ?>">
							<div class="mistake-card-head">
								<span class="mistake-lesson-badge">📖 <?php echo esc_html( $m->lesson_title ?: __( 'Lesson', 'nctb-learning-hub' ) ); ?></span>
								<span class="mistake-count-badge">⚠️ <?php echo esc_html( sprintf( _n( '%d error', '%d errors', $m->error_count, 'nctb-learning-hub' ), $m->error_count ) ); ?></span>
							</div>

							<h3 class="mistake-prompt"><?php echo esc_html( $m->question_prompt ); ?></h3>

							<?php if ( ! empty( $m->question_content ) ) : ?>
								<div class="mistake-context"><?php echo esc_html( $m->question_content ); ?></div>
							<?php endif; ?>

							<div class="mistake-wrong-submission">
								<strong><?php esc_html_e( 'Your last answer:', 'nctb-learning-hub' ); ?></strong>
								<span class="wrong-answer-chip">❌ <?php echo esc_html( $m->wrong_answer ); ?></span>
							</div>

							<?php if ( ! empty( $m->question_explanation ) ) : ?>
								<div class="mistake-explanation-box">
									<strong>💡 <?php esc_html_e( 'Explanation:', 'nctb-learning-hub' ); ?></strong>
									<p><?php echo esc_html( $m->question_explanation ); ?></p>
								</div>
							<?php endif; ?>

							<div class="mistake-card-actions">
								<?php if ( $m->lesson_id ) : ?>
									<a href="<?php echo esc_url( get_permalink( $m->lesson_id ) ); ?>#activity-13" class="nctb-btn nctb-btn-primary">
										🔄 <?php esc_html_e( 'Retry in Lesson Quiz', 'nctb-learning-hub' ); ?>
									</a>
								<?php endif; ?>
								<button type="button" class="nctb-btn nctb-btn-secondary btn-resolve-mistake" data-mistake-id="<?php echo esc_attr( $m->id ); ?>">
									✅ <?php esc_html_e( 'Mark as Mastered', 'nctb-learning-hub' ); ?>
								</button>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Revision Due shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function render_revision_due() {
		$user_id   = get_current_user_id() ?: 1;
		$due_items = NCTB_Spaced_Revision_Service::get_due_reviews( $user_id );

		ob_start();
		?>
		<div class="nctb-student-screen nctb-revision-screen">
			<header class="nctb-screen-header">
				<h1>⏰ <?php esc_html_e( 'Spaced Revision Queue', 'nctb-learning-hub' ); ?></h1>
				<p class="lead"><?php esc_html_e( 'Scientifically scheduled review intervals to transfer knowledge into long-term memory.', 'nctb-learning-hub' ); ?></p>
			</header>

			<?php if ( empty( $due_items ) ) : ?>
				<div class="nctb-empty-state">
					<div class="empty-icon">🌟</div>
					<h3><?php esc_html_e( 'All caught up for today!', 'nctb-learning-hub' ); ?></h3>
					<p><?php esc_html_e( 'You have completed all scheduled spaced revisions. Check back tomorrow!', 'nctb-learning-hub' ); ?></p>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>" class="nctb-btn nctb-btn-primary">
						📚 <?php esc_html_e( 'Continue Learning', 'nctb-learning-hub' ); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="nctb-revision-list">
					<?php foreach ( $due_items as $r ) : ?>
						<article class="nctb-revision-card" id="revision-card-<?php echo esc_attr( $r->id ); ?>">
							<div class="revision-card-head">
								<span class="rev-type-badge">🔁 <?php echo esc_html( ucfirst( $r->item_type ) ); ?></span>
								<span class="rev-interval-badge">📅 <?php echo esc_html( sprintf( __( 'Interval: %d days', 'nctb-learning-hub' ), $r->interval_days ) ); ?></span>
								<span class="rev-rep-badge">🔥 <?php echo esc_html( sprintf( __( 'Streak: %d', 'nctb-learning-hub' ), $r->repetition_count ) ); ?></span>
							</div>

							<?php if ( ! empty( $r->question_prompt ) ) : ?>
								<h3 class="rev-prompt"><?php echo esc_html( $r->question_prompt ); ?></h3>
							<?php endif; ?>

							<?php if ( ! empty( $r->lesson_title ) ) : ?>
								<div class="rev-meta-lesson">📖 <?php echo esc_html( $r->lesson_title ); ?></div>
							<?php endif; ?>

							<div class="revision-actions">
								<?php if ( $r->lesson_id ) : ?>
									<a href="<?php echo esc_url( get_permalink( $r->lesson_id ) ); ?>" class="nctb-btn nctb-btn-primary">
										⚡ <?php esc_html_e( 'Review Lesson & Practice', 'nctb-learning-hub' ); ?>
									</a>
								<?php endif; ?>
								<button type="button" class="nctb-btn nctb-btn-secondary btn-complete-revision" data-review-id="<?php echo esc_attr( $r->id ); ?>">
									✅ <?php esc_html_e( 'Mark Reviewed Today', 'nctb-learning-hub' ); ?>
								</button>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Learning Progress & Concept Mastery shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function render_progress() {
		$user_id = get_current_user_id() ?: 1;
		$summary = NCTB_Progress_Service::get_user_summary( $user_id );
		$mastery = NCTB_Mastery_Service::get_all_user_mastery( $user_id );

		ob_start();
		?>
		<div class="nctb-student-screen nctb-progress-screen">
			<header class="nctb-screen-header">
				<h1>📊 <?php esc_html_e( 'Learning Progress & Mastery', 'nctb-learning-hub' ); ?></h1>
				<p class="lead"><?php esc_html_e( 'Track completed lessons, concept-level retention, and mistake reduction.', 'nctb-learning-hub' ); ?></p>
			</header>

			<!-- Overall KPI Stats Grid -->
			<div class="nctb-kpi-grid">
				<div class="nctb-kpi-card">
					<div class="kpi-icon">🎓</div>
					<div class="kpi-val"><?php echo esc_html( (string) ( $summary['completed_lessons'] ?? 0 ) ); ?></div>
					<div class="kpi-label"><?php esc_html_e( 'Completed Lessons', 'nctb-learning-hub' ); ?></div>
				</div>

				<div class="nctb-kpi-card">
					<div class="kpi-icon">📝</div>
					<div class="kpi-val"><?php echo esc_html( (string) ( $summary['total_attempts'] ?? 0 ) ); ?></div>
					<div class="kpi-label"><?php esc_html_e( 'Questions Attempted', 'nctb-learning-hub' ); ?></div>
				</div>

				<div class="nctb-kpi-card">
					<div class="kpi-icon">📕</div>
					<div class="kpi-val"><?php echo esc_html( (string) ( $summary['active_mistakes'] ?? 0 ) ); ?></div>
					<div class="kpi-label"><?php esc_html_e( 'Active Mistakes', 'nctb-learning-hub' ); ?></div>
				</div>

				<div class="nctb-kpi-card">
					<div class="kpi-icon">⏰</div>
					<div class="kpi-val"><?php echo esc_html( (string) ( $summary['due_reviews'] ?? 0 ) ); ?></div>
					<div class="kpi-label"><?php esc_html_e( 'Revisions Due', 'nctb-learning-hub' ); ?></div>
				</div>
			</div>

			<!-- Concept Mastery Section -->
			<section class="nctb-mastery-section">
				<h2>🔑 <?php esc_html_e( 'Curriculum Concept Mastery', 'nctb-learning-hub' ); ?></h2>
				<div class="mastery-notice-pill">
					ℹ️ <em><?php esc_html_e( 'Educational Note: Lesson completion (viewing material) is separate from Concept Mastery (retained accuracy over time).', 'nctb-learning-hub' ); ?></em>
				</div>

				<?php if ( empty( $mastery ) ) : ?>
					<div class="nctb-empty-state" style="margin-top:1rem;">
						<p><?php esc_html_e( 'No concept mastery recorded yet. Start practicing questions to build your mastery scores!', 'nctb-learning-hub' ); ?></p>
					</div>
				<?php else : ?>
					<div class="nctb-concept-mastery-list">
						<?php foreach ( $mastery as $m ) :
							$level_class = 'mastery-' . sanitize_html_class( $m->mastery_level );
						?>
							<div class="concept-mastery-card">
								<div class="concept-info">
									<h3 class="concept-title"><?php echo esc_html( $m->concept_name ); ?></h3>
									<?php if ( ! empty( $m->concept_desc ) ) : ?>
										<p class="concept-desc"><?php echo esc_html( $m->concept_desc ); ?></p>
									<?php endif; ?>
								</div>

								<div class="concept-stats">
									<span class="mastery-badge <?php echo esc_attr( $level_class ); ?>">
										<?php echo esc_html( ucfirst( $m->mastery_level ) ); ?>
									</span>
									<div class="mastery-pct-bar">
										<div class="pct-fill" style="width: <?php echo esc_attr( min( 100, max( 0, (int) $m->mastery_score ) ) ); ?>%;"></div>
									</div>
									<span class="mastery-score-text"><?php echo esc_html( $m->mastery_score . '%' ); ?> (<?php echo esc_html( $m->correct_attempts . '/' . $m->total_attempts ); ?>)</span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</section>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render My Purchases & Passes shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function render_purchases() {
		$user_id      = get_current_user_id() ?: 1;
		$entitlements = class_exists( 'NCTB_Entitlements' ) ? NCTB_Entitlements::get_user_entitlements( $user_id ) : array();

		ob_start();
		?>
		<div class="nctb-student-screen nctb-purchases-screen">
			<header class="nctb-screen-header">
				<h1>💳 <?php esc_html_e( 'My Purchases & Passes', 'nctb-learning-hub' ); ?></h1>
				<p class="lead"><?php esc_html_e( 'আপনার সক্রিয় কোর্স পাস, লেসন অ্যাক্সেস এবং সাবস্ক্রিপশন বিশদ।', 'nctb-learning-hub' ); ?></p>
			</header>

			<?php if ( empty( $entitlements ) ) : ?>
				<div class="nctb-empty-state">
					<div class="empty-icon">🎟️</div>
					<h3><?php esc_html_e( 'কোনো পেইড পাস সক্রিয় নেই', 'nctb-learning-hub' ); ?></h3>
					<p><?php esc_html_e( 'ফ্রি লেসনগুলো যেকোনো সময় সরাসরি অধ্যয়ন করতে পারবেন। সম্পূর্ণ সিলেবাস আনলক করতে অল-অ্যাক্সেস পাস বেছে নিন।', 'nctb-learning-hub' ); ?></p>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>" class="nctb-btn nctb-btn-primary">
						📚 <?php esc_html_e( 'পাঠ্যবই ব্রাউজ করুন', 'nctb-learning-hub' ); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="nctb-purchases-list">
					<?php foreach ( $entitlements as $e ) : ?>
						<article class="nctb-purchase-card">
							<div class="purchase-head">
								<span class="purchase-type-badge">🎟️ <?php echo esc_html( ucfirst( str_replace( '_', ' ', $e->entitlement_type ) ) ); ?></span>
								<span class="purchase-status-badge active">✅ <?php echo esc_html( ucfirst( $e->status ) ); ?></span>
							</div>

							<h3 class="purchase-title"><?php echo esc_html( $e->item_title ); ?></h3>

							<div class="purchase-meta-grid">
								<div><strong>পাস শুরু:</strong> <?php echo esc_html( gmdate( 'd M Y', strtotime( $e->granted_at ) ) ); ?></div>
								<div><strong>মেয়াদ:</strong> <?php echo esc_html( $e->expires_at ? gmdate( 'd M Y', strtotime( $e->expires_at ) ) : __( 'আজীবন অ্যাক্সেস (Lifetime)', 'nctb-learning-hub' ) ); ?></div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Board Questions Bank shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function render_board_questions() {
		$selected_level = sanitize_key( $_GET['level'] ?? '' );
		$selected_board = sanitize_key( $_GET['board'] ?? '' );
		$selected_year  = absint( $_GET['year'] ?? 0 );

		$filters = array();
		if ( $selected_level ) {
			$filters['exam_level'] = $selected_level;
		}
		if ( $selected_board ) {
			$filters['board_name'] = $selected_board;
		}
		if ( $selected_year ) {
			$filters['exam_year'] = $selected_year;
		}

		$questions = class_exists( 'NCTB_Board_Service' ) ? NCTB_Board_Service::get_board_questions( $filters ) : array();

		ob_start();
		?>
		<div class="nctb-student-screen nctb-board-questions-screen">
			<header class="nctb-screen-header">
				<div class="screen-badge">🏛️ <?php esc_html_e( 'Official Exam Archive', 'nctb-learning-hub' ); ?></div>
				<h1><?php esc_html_e( 'Authentic NCTB Board Questions', 'nctb-learning-hub' ); ?></h1>
				<p class="lead"><?php esc_html_e( 'বিগত বছরের এসএসসি ও এইচএসসি বোর্ড পরীক্ষার যাচাইকৃত প্রশ্নোত্তর ও ব্যাখ্যা। (AI জেনারেটেড নয়, শতভাগ প্রামাণ্য প্রশ্ন)', 'nctb-learning-hub' ); ?></p>
			</header>

			<!-- Filter Bar -->
			<form method="get" class="nctb-board-filter-bar">
				<div class="filter-group">
					<label><?php esc_html_e( 'Exam Level:', 'nctb-learning-hub' ); ?></label>
					<select name="level" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( 'All Levels (SSC & HSC)', 'nctb-learning-hub' ); ?></option>
						<option value="ssc" <?php selected( $selected_level, 'ssc' ); ?>>SSC</option>
						<option value="hsc" <?php selected( $selected_level, 'hsc' ); ?>>HSC</option>
					</select>
				</div>

				<div class="filter-group">
					<label><?php esc_html_e( 'Education Board:', 'nctb-learning-hub' ); ?></label>
					<select name="board" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( 'All Boards', 'nctb-learning-hub' ); ?></option>
						<?php foreach ( NCTB_Board_Service::BOARDS as $code => $lbl ) : ?>
							<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $selected_board, $code ); ?>><?php echo esc_html( $lbl ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="filter-group">
					<label><?php esc_html_e( 'Exam Year:', 'nctb-learning-hub' ); ?></label>
					<select name="year" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( 'All Years', 'nctb-learning-hub' ); ?></option>
						<?php for ( $y = 2024; $y >= 2018; $y-- ) : ?>
							<option value="<?php echo esc_attr( $y ); ?>" <?php selected( $selected_year, $y ); ?>><?php echo esc_html( (string) $y ); ?></option>
						<?php endfor; ?>
					</select>
				</div>

				<div class="filter-group" style="align-self: flex-end;">
					<a href="<?php echo esc_url( remove_query_arg( array( 'level', 'board', 'year' ) ) ); ?>" class="nctb-btn nctb-btn-secondary">
						🔄 <?php esc_html_e( 'Reset Filters', 'nctb-learning-hub' ); ?>
					</a>
				</div>
			</form>

			<!-- Board Question List -->
			<div class="nctb-board-questions-list">
				<?php if ( empty( $questions ) ) : ?>
					<div class="nctb-empty-state">
						<div class="empty-icon">📂</div>
						<h3><?php esc_html_e( 'কোনো বোর্ড প্রশ্ন পাওয়া যায়নি', 'nctb-learning-hub' ); ?></h3>
						<p><?php esc_html_e( 'অন্য কোনো বোর্ড বা সাল নির্বাচন করে আবার চেষ্টা করুন।', 'nctb-learning-hub' ); ?></p>
					</div>
				<?php else : ?>
					<?php foreach ( $questions as $bq ) :
						$options = ! empty( $bq->options_json ) ? json_decode( $bq->options_json, true ) : array();
					?>
						<article class="nctb-board-card">
							<div class="board-card-head">
								<div class="board-badge-primary">
									🏛️ <?php echo esc_html( strtoupper( $bq->exam_level ) . ' • ' . ( NCTB_Board_Service::BOARDS[ $bq->board_name ] ?? ucfirst( $bq->board_name ) ) . ' ' . $bq->exam_year ); ?>
								</div>
								<div class="board-badge-sub">
									Q<?php echo esc_html( $bq->question_no ); ?> (<?php echo esc_html( $bq->marks ); ?> Marks) • <?php echo esc_html( ucfirst( str_replace( '_', ' ', $bq->question_type ) ) ); ?>
								</div>
							</div>

							<?php if ( ! empty( $bq->topic ) ) : ?>
								<div class="board-topic-tag">🏷️ <?php echo esc_html( $bq->topic ); ?></div>
							<?php endif; ?>

							<div class="board-q-prompt">
								<?php echo nl2br( esc_html( $bq->question_text ) ); ?>
							</div>

							<?php if ( ! empty( $options ) ) : ?>
								<div class="board-options-grid">
									<?php foreach ( $options as $opt ) : ?>
										<div class="board-opt-item">
											<strong>(<?php echo esc_html( $opt['key'] ); ?>)</strong> <?php echo esc_html( $opt['text'] ); ?>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<!-- Expandable Verified Answer Accordion -->
							<details class="board-answer-details">
								<summary class="btn-reveal-answer">
									<span>👁️ <?php esc_html_e( 'বোর্ড নির্দেশিত সঠিক উত্তর ও ব্যাখ্যা দেখুন (Verified Answer)', 'nctb-learning-hub' ); ?></span>
								</summary>
								<div class="board-answer-body">
									<div class="verified-status-tag">✅ <?php esc_html_e( 'Official Verified Answer Scheme', 'nctb-learning-hub' ); ?></div>
									<div class="ans-text"><strong>সঠিক উত্তর:</strong> <?php echo nl2br( esc_html( $bq->verified_answer ) ); ?></div>
									<?php if ( ! empty( $bq->explanation ) ) : ?>
										<div class="ans-expl"><strong>ব্যাখ্যা ও মার্কিং নির্দেশিকা:</strong> <?php echo nl2br( esc_html( $bq->explanation ) ); ?></div>
									<?php endif; ?>
									<div class="ans-source"><small>📌 সূত্র: <?php echo esc_html( $bq->source_reference ); ?></small></div>
								</div>
							</details>
						</article>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Board Pattern Analytics shortcode.
	 *
	 * @return string HTML output.
	 */
	public static function render_board_analytics() {
		$selected_level = sanitize_key( $_GET['level'] ?? 'hsc' );
		$report         = class_exists( 'NCTB_Board_Analytics_Service' ) ? NCTB_Board_Analytics_Service::get_full_analytics_report( $selected_level ) : array();

		ob_start();
		?>
		<div class="nctb-student-screen nctb-board-analytics-screen">
			<header class="nctb-screen-header">
				<div class="screen-badge">📊 <?php esc_html_e( 'Historical Exam Intelligence', 'nctb-learning-hub' ); ?></div>
				<h1><?php esc_html_e( 'Board Exam Pattern Analytics', 'nctb-learning-hub' ); ?></h1>
				<p class="lead"><?php esc_html_e( 'বিগত বছরগুলোর (২০১৮–২০২৪) বোর্ড পরীক্ষার প্রামাণ্য প্রশ্ন বিন্যাস ও টপিকভিত্তিক পরিসংখ্যান।', 'nctb-learning-hub' ); ?></p>
			</header>

			<!-- Prominent Historical-Only Notice Banner -->
			<div class="nctb-analytics-disclaimer-banner">
				<div class="disclaimer-icon">⚠️</div>
				<div class="disclaimer-text">
					<strong><?php esc_html_e( 'ঐতিহাসিক পর্যালোচনা সংক্রান্ত বিজ্ঞপ্তি (Historical Analysis Only):', 'nctb-learning-hub' ); ?></strong>
					<span><?php echo esc_html( $report['disclaimer'] ?? 'This analysis reflects historical examination patterns and does not predict future exam questions.' ); ?></span>
				</div>
			</div>

			<!-- Level Switcher -->
			<div class="nctb-analytics-level-switcher">
				<a href="<?php echo esc_url( add_query_arg( 'level', 'hsc' ) ); ?>" class="level-tab-btn <?php echo 'hsc' === $selected_level ? 'active' : ''; ?>">
					🎓 HSC English Analytics
				</a>
				<a href="<?php echo esc_url( add_query_arg( 'level', 'ssc' ) ); ?>" class="level-tab-btn <?php echo 'ssc' === $selected_level ? 'active' : ''; ?>">
					🎒 SSC English Analytics
				</a>
			</div>

			<!-- Top Metrics KPI Row -->
			<div class="nctb-analytics-kpi-grid">
				<div class="kpi-card">
					<span class="kpi-icon">📝</span>
					<div class="kpi-info">
						<span class="kpi-val"><?php echo esc_html( (string) ( $report['kpis']['total_questions'] ?? 0 ) ); ?></span>
						<span class="kpi-lbl"><?php esc_html_e( 'Total Exam Questions', 'nctb-learning-hub' ); ?></span>
					</div>
				</div>

				<div class="kpi-card">
					<span class="kpi-icon">🎯</span>
					<div class="kpi-info">
						<span class="kpi-val"><?php echo esc_html( (string) ( $report['kpis']['total_marks'] ?? 0 ) ); ?></span>
						<span class="kpi-lbl"><?php esc_html_e( 'Total Marks Evaluated', 'nctb-learning-hub' ); ?></span>
					</div>
				</div>

				<div class="kpi-card">
					<span class="kpi-icon">🏛️</span>
					<div class="kpi-info">
						<span class="kpi-val"><?php echo esc_html( (string) ( $report['kpis']['total_boards'] ?? 0 ) ); ?></span>
						<span class="kpi-lbl"><?php esc_html_e( 'Education Boards', 'nctb-learning-hub' ); ?></span>
					</div>
				</div>

				<div class="kpi-card">
					<span class="kpi-icon">📅</span>
					<div class="kpi-info">
						<span class="kpi-val"><?php echo esc_html( $report['kpis']['years_span'] ?? '2018–2024' ); ?></span>
						<span class="kpi-lbl"><?php esc_html_e( 'Historical Archive Span', 'nctb-learning-hub' ); ?></span>
					</div>
				</div>
			</div>

			<!-- Main 2-Column Analytics Breakdown -->
			<div class="nctb-analytics-breakdown-grid">
				<!-- High Frequency Topics -->
				<div class="analytics-card">
					<div class="card-head">
						<h3>🔥 <?php esc_html_e( 'High-Frequency Exam Topics', 'nctb-learning-hub' ); ?></h3>
						<small><?php esc_html_e( 'Historical recurrence rank', 'nctb-learning-hub' ); ?></small>
					</div>

					<div class="topic-frequency-list">
						<?php if ( empty( $report['topic_frequency'] ) ) : ?>
							<p class="empty-text"><?php esc_html_e( 'No historical topic data available for this level.', 'nctb-learning-hub' ); ?></p>
						<?php else : ?>
							<?php
							$max_q = max( 1, (int) ( $report['topic_frequency'][0]->question_count ?? 1 ) );
							foreach ( $report['topic_frequency'] as $idx => $tf ) :
								$pct = min( 100, round( ( (int) $tf->question_count / $max_q ) * 100 ) );
							?>
								<div class="topic-bar-row">
									<div class="topic-row-info">
										<span class="topic-rank">#<?php echo esc_html( (string) ( $idx + 1 ) ); ?></span>
										<strong class="topic-name"><?php echo esc_html( $tf->topic ); ?></strong>
										<span class="topic-meta"><?php echo esc_html( $tf->question_count ); ?> questions (<?php echo esc_html( $tf->total_marks ); ?> marks)</span>
									</div>
									<div class="topic-bar-track">
										<div class="topic-bar-fill" style="width: <?php echo esc_attr( $pct ); ?>%;"></div>
									</div>
									<div class="topic-action">
										<a href="<?php echo esc_url( add_query_arg( array( 'level' => $selected_level, 'topic' => $tf->topic ), home_url( '/board-questions/' ) ) ); ?>" class="btn-practice-topic">
											🎯 <?php esc_html_e( 'Practise Past Questions', 'nctb-learning-hub' ); ?> →
										</a>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>

				<!-- Question Type & Board Breakdown -->
				<div class="analytics-side-col">
					<!-- Question Type Distribution -->
					<div class="analytics-card">
						<div class="card-head">
							<h3>📋 <?php esc_html_e( 'Question Type Distribution', 'nctb-learning-hub' ); ?></h3>
						</div>
						<div class="qtype-pills-wrap">
							<?php if ( ! empty( $report['question_types'] ) ) : ?>
								<?php foreach ( $report['question_types'] as $qt ) : ?>
									<div class="qtype-pill-box">
										<span class="qtype-name"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $qt->question_type ) ) ); ?></span>
										<span class="qtype-count"><strong><?php echo esc_html( $qt->count ); ?></strong> items (<?php echo esc_html( $qt->total_marks ); ?>m)</span>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>

					<!-- Board Breakdown -->
					<div class="analytics-card" style="margin-top: 1.5rem;">
						<div class="card-head">
							<h3>🏛️ <?php esc_html_e( 'Board Distribution', 'nctb-learning-hub' ); ?></h3>
						</div>
						<div class="board-mini-list">
							<?php if ( ! empty( $report['boards'] ) ) : ?>
								<?php foreach ( $report['boards'] as $b ) : ?>
									<div class="board-mini-item">
										<span><?php echo esc_html( NCTB_Board_Service::BOARDS[ $b->board_name ] ?? ucfirst( $b->board_name ) ); ?></span>
										<span class="badge"><?php echo esc_html( $b->question_count ); ?> qs</span>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
