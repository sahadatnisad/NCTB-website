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
}
