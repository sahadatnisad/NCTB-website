<?php
/**
 * Single Lesson: Interactive Gold-Standard Lesson Experience (Phase 4).
 *
 * Dynamically renders the Class → Subject → Book › Unit › Lesson hierarchy,
 * learning outcomes, linked concepts, and ordered activity blocks (warm-up,
 * reading, vocabulary, grammar, examples, guided/independent practice,
 * writing, listening, speaking, summary, quiz placeholder, tutor placeholder).
 *
 * Presentation only. All curriculum and activity data comes from the plugin.
 * NO lesson-specific PHP template required.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$nctb_lesson_id = get_the_ID();
	$nctb_has_cpt   = class_exists( 'NCTB_Curriculum_CPT' );
	$nctb_has_data  = class_exists( 'NCTB_Curriculum_Data' );
	$nctb_has_types = class_exists( 'NCTB_Lesson_Activity_Types' );

	$nctb_unit_id   = $nctb_has_cpt ? NCTB_Curriculum_CPT::get_lesson_unit( $nctb_lesson_id ) : 0;
	$nctb_book_id   = ( $nctb_has_cpt && $nctb_unit_id ) ? NCTB_Curriculum_CPT::get_unit_book( $nctb_unit_id ) : 0;
	$nctb_outcomes  = $nctb_has_data ? NCTB_Curriculum_Data::get_lesson_outcomes( $nctb_lesson_id ) : array();
	$nctb_concepts  = $nctb_has_data ? NCTB_Curriculum_Data::get_lesson_concepts( $nctb_lesson_id ) : array();
	$nctb_activities = $nctb_has_data ? NCTB_Curriculum_Data::get_lesson_activities( $nctb_lesson_id ) : array();
	$nctb_total_acts = count( $nctb_activities );
	?>
	<main id="primary" class="nctb-main nctb-curriculum nctb-lesson" data-lesson-id="<?php echo esc_attr( $nctb_lesson_id ); ?>" data-total-steps="<?php echo esc_attr( $nctb_total_acts ); ?>">
		<!-- Breadcrumb -->
		<nav class="nctb-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'nctb-theme' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>"><?php esc_html_e( 'Books', 'nctb-theme' ); ?></a>
			<?php if ( $nctb_book_id ) : ?>
				<span>›</span> <a href="<?php echo esc_url( get_permalink( $nctb_book_id ) ); ?>"><?php echo esc_html( get_the_title( $nctb_book_id ) ); ?></a>
			<?php endif; ?>
			<?php if ( $nctb_unit_id ) : ?>
				<span>›</span> <a href="<?php echo esc_url( get_permalink( $nctb_unit_id ) ); ?>"><?php echo esc_html( get_the_title( $nctb_unit_id ) ); ?></a>
			<?php endif; ?>
			<span>›</span> <span class="current-crumb"><?php the_title(); ?></span>
		</nav>

		<!-- Lesson Header -->
		<header class="nctb-lesson-header">
			<div class="nctb-lesson-title-row">
				<span class="nctb-badge-lesson"><?php esc_html_e( 'Interactive Lesson', 'nctb-theme' ); ?></span>
				<h1 class="nctb-lesson-title"><?php the_title(); ?></h1>
			</div>

			<?php if ( ! empty( $nctb_concepts ) ) : ?>
				<div class="nctb-lesson-meta-chips">
					<span class="meta-label">🔑 <?php esc_html_e( 'Concepts:', 'nctb-theme' ); ?></span>
					<?php foreach ( $nctb_concepts as $nctb_concept ) : ?>
						<span class="nctb-chip"><?php echo esc_html( $nctb_concept->name ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</header>

		<?php
		$nctb_access = class_exists( 'NCTB_Entitlements' ) ? NCTB_Entitlements::can_access_lesson( get_current_user_id(), $nctb_lesson_id ) : array( 'granted' => true );
		if ( ! $nctb_access['granted'] ) :
			echo class_exists( 'NCTB_Commerce' ) ? NCTB_Commerce::render_paywall_card( $nctb_lesson_id ) : '<div class="nctb-card"><h2>🔒 ' . esc_html__( 'Lesson Locked', 'nctb-theme' ) . '</h2></div>';
		elseif ( empty( $nctb_activities ) ) :
		?>
			<!-- Fallback Static View if no activities exist -->
			<?php if ( ! empty( $nctb_outcomes ) ) : ?>
				<section class="nctb-outcomes-box">
					<h2>🎯 <?php esc_html_e( 'Learning Outcomes', 'nctb-theme' ); ?></h2>
					<ul class="nctb-outcomes-list">
						<?php foreach ( $nctb_outcomes as $outcome ) : ?>
							<li><?php echo esc_html( $outcome->outcome_text ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<section class="nctb-prose nctb-lesson-content">
				<?php the_content(); ?>
			</section>
		<?php else : ?>
			<!-- Interactive Lesson Stepper & Activities View (Phase 4) -->
			<div class="nctb-stepper-panel" id="nctb-stepper-panel">
				<!-- Stepper Controls & Progress Bar -->
				<div class="nctb-progress-status-bar">
					<div class="status-left">
						<span class="step-counter-text">
							<?php esc_html_e( 'Activity', 'nctb-theme' ); ?> <strong id="nctb-current-step-num">1</strong> / <?php echo esc_html( (string) $nctb_total_acts ); ?>:
						</span>
						<span class="step-title-text" id="nctb-current-step-title"><?php echo esc_html( $nctb_activities[0]->title ); ?></span>
					</div>
					<div class="status-right">
						<button type="button" class="nctb-btn-view-toggle" id="btn-toggle-linear-view" title="<?php esc_attr_e( 'Switch between step-by-step and linear full lesson view', 'nctb-theme' ); ?>">
							📑 <?php esc_html_e( 'Full Lesson View', 'nctb-theme' ); ?>
						</button>
					</div>
				</div>

				<div class="nctb-progress-track" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="<?php echo esc_attr( $nctb_total_acts ); ?>">
					<div class="nctb-progress-fill" id="nctb-progress-fill" style="width: <?php echo esc_attr( round( ( 1 / $nctb_total_acts ) * 100 ) ); ?>%;"></div>
				</div>

				<!-- Step Navigation Pills Bar -->
				<div class="nctb-step-pills-bar" id="nctb-step-pills">
					<?php
					$idx = 1;
					foreach ( $nctb_activities as $act ) :
						$type_info = $nctb_has_types ? NCTB_Lesson_Activity_Types::get_type_info( $act->activity_type ) : null;
						$icon      = $type_info ? $type_info['icon'] : '📄';
						$label     = $type_info ? $type_info['label_en'] : $act->activity_type;
						?>
						<button type="button" class="nctb-step-pill <?php echo 1 === $idx ? 'active' : ''; ?>" data-step="<?php echo esc_attr( $idx ); ?>" title="<?php echo esc_attr( $idx . '. ' . ( $act->title ?: $label ) ); ?>">
							<span class="pill-icon"><?php echo esc_html( $icon ); ?></span>
							<span class="pill-num"><?php echo esc_html( (string) $idx ); ?></span>
						</button>
						<?php
						$idx++;
					endforeach;
					?>
				</div>
			</div>

			<!-- Activities Container -->
			<div class="nctb-activities-wrapper" id="nctb-activities-wrapper">
				<?php
				$idx = 1;
				foreach ( $nctb_activities as $act ) :
					$type_info = $nctb_has_types ? NCTB_Lesson_Activity_Types::get_type_info( $act->activity_type ) : null;
					$icon      = $type_info ? $type_info['icon'] : '📄';
					$label_en  = $type_info ? $type_info['label_en'] : $act->activity_type;
					$label_bn  = $type_info ? $type_info['label_bn'] : '';
					$meta      = is_array( $act->meta_data ) ? $act->meta_data : array();
					?>
					<article class="nctb-activity-view-card <?php echo 1 === $idx ? 'active' : ''; ?>" id="activity-step-<?php echo esc_attr( $idx ); ?>" data-step="<?php echo esc_attr( $idx ); ?>" data-type="<?php echo esc_attr( $act->activity_type ); ?>">
						<!-- Activity Header -->
						<div class="activity-card-header">
							<div class="activity-type-badge">
								<span class="type-icon"><?php echo esc_html( $icon ); ?></span>
								<span class="type-name-en"><?php echo esc_html( $label_en ); ?></span>
								<?php if ( $label_bn ) : ?>
									<span class="type-name-bn"><?php echo esc_html( $label_bn ); ?></span>
								<?php endif; ?>
							</div>
							<span class="activity-step-indicator"><?php echo esc_html( sprintf( __( 'Step %1$d of %2$d', 'nctb-theme' ), $idx, $nctb_total_acts ) ); ?></span>
						</div>

						<h2 class="activity-card-title"><?php echo esc_html( $act->title ); ?></h2>

						<!-- Activity Body -->
						<div class="activity-card-body nctb-prose">
							<?php echo apply_filters( 'the_content', $act->content ); ?>

							<!-- Type-specific rich rendering -->
							<?php if ( 'vocabulary' === $act->activity_type && ! empty( $meta['words'] ) && is_array( $meta['words'] ) ) : ?>
								<div class="nctb-vocab-cards-grid">
									<?php foreach ( $meta['words'] as $w ) : ?>
										<div class="vocab-word-card">
											<div class="vocab-word-head">
												<span class="vocab-term"><?php echo esc_html( $w['term'] ); ?></span>
												<?php if ( ! empty( $w['pos'] ) ) : ?>
													<span class="vocab-pos"><?php echo esc_html( $w['pos'] ); ?></span>
												<?php endif; ?>
												<?php if ( ! empty( $w['pronunciation'] ) ) : ?>
													<span class="vocab-phonetic"><?php echo esc_html( $w['pronunciation'] ); ?></span>
												<?php endif; ?>
											</div>
											<?php if ( ! empty( $w['meaning_bn'] ) ) : ?>
												<div class="vocab-meaning-bn"><strong>বাংলা:</strong> <?php echo esc_html( $w['meaning_bn'] ); ?></div>
											<?php endif; ?>
											<?php if ( ! empty( $w['meaning_en'] ) ) : ?>
												<div class="vocab-meaning-en"><strong>English:</strong> <?php echo esc_html( $w['meaning_en'] ); ?></div>
											<?php endif; ?>
											<?php if ( ! empty( $w['example'] ) ) : ?>
												<div class="vocab-example"><em>"<?php echo esc_html( $w['example'] ); ?>"</em></div>
											<?php endif; ?>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<?php if ( 'writing' === $act->activity_type ) : ?>
								<div class="nctb-writing-draft-zone">
									<label for="writing-draft-area-<?php echo esc_attr( $idx ); ?>"><strong>📝 <?php esc_html_e( 'Practice Drafting Area (Live Word Counter):', 'nctb-theme' ); ?></strong></label>
									<textarea id="writing-draft-area-<?php echo esc_attr( $idx ); ?>" class="nctb-draft-textarea" rows="5" placeholder="<?php esc_attr_e( 'Write your paragraph or response here to test your word count...', 'nctb-theme' ); ?>"></textarea>
									<div class="word-counter-display"><?php esc_html_e( 'Word Count:', 'nctb-theme' ); ?> <span class="counter-num">0</span> <?php esc_html_e( 'words', 'nctb-theme' ); ?></div>
								</div>
							<?php endif; ?>

							<?php
							if ( 'quiz_placeholder' === $act->activity_type && class_exists( 'NCTB_Practice_Data' ) ) :
								$nctb_questions = NCTB_Practice_Data::get_lesson_questions( $nctb_lesson_id, true, false );
								if ( ! empty( $nctb_questions ) ) :
									$total_q = count( $nctb_questions );
									?>
									<div class="nctb-practice-engine-container" id="nctb-practice-engine" data-lesson-id="<?php echo esc_attr( $nctb_lesson_id ); ?>" data-total-q="<?php echo esc_attr( $total_q ); ?>">
										<div class="practice-quiz-top-bar">
											<div class="quiz-status-pill">⚡ <?php esc_html_e( 'Interactive Practice Quiz', 'nctb-theme' ); ?></div>
											<div class="quiz-q-counter">
												<?php esc_html_e( 'Question', 'nctb-theme' ); ?> <strong id="quiz-current-q-num">1</strong> / <?php echo esc_html( (string) $total_q ); ?>
											</div>
										</div>

										<div class="practice-questions-list">
											<?php
											$q_num = 1;
											foreach ( $nctb_questions as $pq ) :
												$pq_types = class_exists( 'NCTB_Question_Types' ) ? NCTB_Question_Types::get_all() : array();
												$pq_info  = $pq_types[ $pq->question_type ] ?? null;
												$pq_icon  = $pq_info ? $pq_info['icon'] : '❓';
												$pq_label = $pq_info ? $pq_info['label_en'] : $pq->question_type;
												?>
												<div class="practice-question-card <?php echo 1 === $q_num ? 'active' : ''; ?>" id="practice-q-card-<?php echo esc_attr( $q_num ); ?>" data-q-index="<?php echo esc_attr( $q_num ); ?>" data-q-id="<?php echo esc_attr( $pq->id ); ?>" data-q-type="<?php echo esc_attr( $pq->question_type ); ?>" style="<?php echo 1 === $q_num ? '' : 'display:none;'; ?>">
													<div class="pq-meta-row">
														<span class="pq-type-badge"><?php echo esc_html( $pq_icon . ' ' . $pq_label ); ?></span>
														<span class="pq-diff-badge diff-<?php echo esc_attr( $pq->difficulty ); ?>"><?php echo esc_html( ucfirst( $pq->difficulty ) ); ?></span>
													</div>

													<h3 class="pq-prompt"><?php echo esc_html( $pq->prompt ); ?></h3>

													<?php if ( ! empty( $pq->content ) ) : ?>
														<div class="pq-context-box"><em><?php echo esc_html( $pq->content ); ?></em></div>
													<?php endif; ?>

													<!-- Interactive Input Area -->
													<div class="pq-input-zone">
														<?php if ( 'mcq' === $pq->question_type && ! empty( $pq->options ) ) : ?>
															<div class="pq-mcq-options-list">
																<?php foreach ( $pq->options as $opt ) : ?>
																	<label class="pq-mcq-option-label" data-opt-key="<?php echo esc_attr( $opt->option_key ); ?>">
																		<input type="radio" name="practice_q_<?php echo esc_attr( $pq->id ); ?>" value="<?php echo esc_attr( $opt->option_key ); ?>" class="pq-radio-input">
																		<span class="opt-key-badge"><?php echo esc_html( $opt->option_key ); ?></span>
																		<span class="opt-text"><?php echo esc_html( $opt->option_text ); ?></span>
																	</label>
																<?php endforeach; ?>
															</div>
														<?php elseif ( 'fill_in_blank' === $pq->question_type || 'short_answer' === $pq->question_type ) : ?>
															<div class="pq-text-input-zone">
																<input type="text" class="pq-text-field" placeholder="<?php esc_attr_e( 'Type your answer here...', 'nctb-theme' ); ?>">
															</div>
														<?php elseif ( 'error_correction' === $pq->question_type ) : ?>
															<div class="pq-text-input-zone">
																<label class="pq-input-label"><strong><?php esc_html_e( 'Provide the corrected word or phrase:', 'nctb-theme' ); ?></strong></label>
																<input type="text" class="pq-text-field" placeholder="<?php esc_attr_e( 'Type the corrected text...', 'nctb-theme' ); ?>">
															</div>
														<?php endif; ?>
													</div>

													<!-- Progressive Hint Zone -->
													<div class="pq-hint-container" style="display:none;">
														<div class="pq-hint-box"></div>
													</div>

													<!-- Feedback Banner -->
													<div class="pq-feedback-banner" style="display:none;"></div>

													<!-- Actions Row -->
													<div class="pq-actions-row">
														<button type="button" class="nctb-btn nctb-btn-secondary pq-btn-hint" data-q-id="<?php echo esc_attr( $pq->id ); ?>" data-hint-level="1">
															💡 <?php esc_html_e( 'Get Hint', 'nctb-theme' ); ?>
														</button>
														<button type="button" class="nctb-btn nctb-btn-primary pq-btn-submit" data-q-id="<?php echo esc_attr( $pq->id ); ?>">
															✅ <?php esc_html_e( 'Submit Answer', 'nctb-theme' ); ?>
														</button>
														<button type="button" class="nctb-btn nctb-btn-secondary pq-btn-retry" style="display:none;">
															🔄 <?php esc_html_e( 'Try Again', 'nctb-theme' ); ?>
														</button>
														<button type="button" class="nctb-btn nctb-btn-primary pq-btn-next" data-next="<?php echo esc_attr( $q_num + 1 ); ?>" style="display:none;">
															<?php echo $q_num === $total_q ? esc_html__( 'Finish Quiz 🎉', 'nctb-theme' ) : esc_html__( 'Next Question →', 'nctb-theme' ); ?>
														</button>
													</div>
												</div>
												<?php
												$q_num++;
											endforeach;
											?>

											<!-- Quiz Completed Summary -->
											<div class="practice-quiz-summary-card" id="practice-quiz-summary" style="display:none;">
												<div class="summary-icon">🏆</div>
												<h3><?php esc_html_e( 'Practice Quiz Completed!', 'nctb-theme' ); ?></h3>
												<div class="summary-score-display">
													<span class="score-label"><?php esc_html_e( 'Your Total Score:', 'nctb-theme' ); ?></span>
													<strong class="score-val" id="quiz-final-score">0 / <?php echo esc_html( (string) $total_q ); ?></strong>
												</div>
												<p class="summary-message" id="quiz-final-message"></p>
												<button type="button" class="nctb-btn nctb-btn-primary" id="btn-retake-quiz">
													🔄 <?php esc_html_e( 'Retake Practice Quiz', 'nctb-theme' ); ?>
												</button>
											</div>
										</div>
									</div>
									<?php
								endif;
							endif;

							// Phase 10: Writing Workbench Module
							if ( 'writing' === $act->activity_type ) :
								?>
								<div class="nctb-writing-workbench" data-activity-id="<?php echo esc_attr( $act->id ); ?>">
									<div class="writing-stepper-stages">
										<span class="stage-pill active" data-stage="draft">1. 📝 Draft</span>
										<span class="stage-pill" data-stage="feedback">2. 💡 AI Feedback</span>
										<span class="stage-pill" data-stage="revision">3. ✍️ Revision</span>
										<span class="stage-pill" data-stage="final">4. 🏆 Final Polish</span>
									</div>

									<div class="writing-input-area">
										<label for="writing-textarea-<?php echo esc_attr( $act->id ); ?>">
											<strong><?php esc_html_e( 'Write your response below (100–150 words):', 'nctb-theme' ); ?></strong>
										</label>
										<textarea id="writing-textarea-<?php echo esc_attr( $act->id ); ?>" class="writing-textarea" rows="6" placeholder="<?php esc_attr_e( 'Type your draft here...', 'nctb-theme' ); ?>"></textarea>
										
										<div class="writing-bar-meta">
											<span class="word-counter"><?php esc_html_e( 'Words:', 'nctb-theme' ); ?> <strong class="word-count-val">0</strong></span>
											<div class="writing-actions-row">
												<button type="button" class="nctb-btn nctb-btn-secondary btn-save-draft">
													💾 <?php esc_html_e( 'Save Draft', 'nctb-theme' ); ?>
												</button>
												<button type="button" class="nctb-btn nctb-btn-primary btn-get-feedback">
													✨ <?php esc_html_e( 'Get AI Feedback', 'nctb-theme' ); ?>
												</button>
												<button type="button" class="nctb-btn nctb-btn-success btn-submit-final" style="display:none;">
													🏆 <?php esc_html_e( 'Submit Final', 'nctb-theme' ); ?>
												</button>
											</div>
										</div>
									</div>

									<div class="writing-feedback-display" style="display:none;"></div>
								</div>
								<?php
							endif;

							// Phase 10: Listening Player Module
							if ( 'listening' === $act->activity_type ) :
								$track = class_exists( 'NCTB_Listening_Service' ) ? NCTB_Listening_Service::get_audio_track( $act->id ) : array();
								?>
								<div class="nctb-listening-player-box" data-activity-id="<?php echo esc_attr( $act->id ); ?>">
									<div class="listening-player-head">
										<div class="player-icon">🎧</div>
										<div class="player-info">
											<h4><?php echo esc_html( $track['title'] ?? 'Listening Comprehension' ); ?></h4>
											<span class="audio-len">⏱️ <?php echo esc_html( (string) round( ( $track['duration_seconds'] ?? 120 ) / 60, 1 ) ); ?> mins</span>
										</div>
									</div>

									<audio class="nctb-audio-element" controls style="width:100%; margin: 1rem 0;">
										<source src="<?php echo esc_url( $track['audio_url'] ?? '' ); ?>" type="audio/ogg">
										<?php esc_html_e( 'Your browser does not support audio playback.', 'nctb-theme' ); ?>
									</audio>

									<div class="listening-transcript-section">
										<button type="button" class="nctb-btn nctb-btn-secondary btn-toggle-transcript">
											📜 <?php esc_html_e( 'Show / Hide Audio Transcript', 'nctb-theme' ); ?>
										</button>
										<div class="listening-transcript-text" style="display:none; margin-top: 0.75rem; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.9rem;">
											<?php echo nl2br( esc_html( $track['transcript'] ?? '' ) ); ?>
										</div>
									</div>
								</div>
								<?php
							endif;

							// Phase 10: Speaking Practice Module
							if ( 'speaking' === $act->activity_type ) :
								?>
								<div class="nctb-speaking-workbench" data-activity-id="<?php echo esc_attr( $act->id ); ?>">
									<div class="speaking-prompt-box">
										<div class="speaking-badge">🎙️ <?php esc_html_e( 'Speaking Practice', 'nctb-theme' ); ?></div>
										<p><?php esc_html_e( 'Record yourself reading the key lesson sentences aloud to build clear pronunciation and fluency.', 'nctb-theme' ); ?></p>
									</div>

									<div class="speaking-controls-row">
										<button type="button" class="nctb-btn nctb-btn-primary btn-record-speaking">
											🔴 <?php esc_html_e( 'Start Speaking', 'nctb-theme' ); ?>
										</button>
										<button type="button" class="nctb-btn nctb-btn-secondary btn-stop-speaking" style="display:none;">
											⏹️ <?php esc_html_e( 'Stop & Submit', 'nctb-theme' ); ?>
										</button>
										<span class="speaking-timer-val" style="display:none; font-weight: bold; color: #dc2626;">00:00</span>
									</div>

									<div class="speaking-feedback-box" style="display:none; margin-top: 1rem; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 1rem; font-size: 0.9rem;"></div>
								</div>
								<?php
							endif;
							?>
						</div>

						<!-- Activity Card Footer Actions -->
						<div class="activity-card-footer">
							<div class="step-nav-buttons">
								<?php if ( $idx > 1 ) : ?>
									<button type="button" class="nctb-btn nctb-btn-secondary btn-step-prev" data-target="<?php echo esc_attr( $idx - 1 ); ?>">
										← <?php esc_html_e( 'Previous Activity', 'nctb-theme' ); ?>
									</button>
								<?php endif; ?>

								<?php if ( $idx < $nctb_total_acts ) : ?>
									<button type="button" class="nctb-btn nctb-btn-primary btn-step-next" data-target="<?php echo esc_attr( $idx + 1 ); ?>">
										<?php esc_html_e( 'Next Activity', 'nctb-theme' ); ?> →
									</button>
								<?php else : ?>
									<span class="nctb-lesson-completed-badge">🎉 <?php esc_html_e( 'Lesson Completed!', 'nctb-theme' ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</article>
					<?php
					$idx++;
				endforeach;
				?>
			</div>

			<!-- Sticky / Contextual Tutor Bar (Phase 9 Live) -->
			<div class="nctb-tutor-callout-bar">
				<div class="tutor-callout-inner">
					<div class="tutor-text">
						<span class="tutor-bot-icon">🤖</span>
						<span><strong><?php esc_html_e( 'Need help with this lesson?', 'nctb-theme' ); ?></strong> <?php esc_html_e( 'Ask your contextual AI Tutor for instant explanations in Bangla or English.', 'nctb-theme' ); ?></span>
					</div>
					<button type="button" class="nctb-btn-tutor-trigger" id="btn-tutor-trigger" title="<?php esc_attr_e( 'Open AI Tutor Drawer', 'nctb-theme' ); ?>">
						💬 <?php esc_html_e( 'Ask AI Tutor', 'nctb-theme' ); ?>
					</button>
				</div>
			</div>

			<!-- AI Tutor Slide-Out Drawer (Phase 9) -->
			<div class="nctb-tutor-drawer-overlay" id="nctb-tutor-overlay" style="display:none;"></div>
			<aside class="nctb-tutor-drawer" id="nctb-tutor-drawer" aria-label="<?php esc_attr_e( 'Contextual AI Tutor', 'nctb-theme' ); ?>">
				<div class="tutor-drawer-head">
					<div class="tutor-head-title">
						<span class="bot-badge">🤖</span>
						<h3><?php esc_html_e( 'NCTB AI Tutor', 'nctb-theme' ); ?></h3>
					</div>
					<div class="tutor-head-actions">
						<span class="tutor-quota-badge" id="tutor-quota-badge" title="<?php esc_attr_e( 'Daily interactions remaining', 'nctb-theme' ); ?>">⚡ 50 left</span>
						<button type="button" class="btn-close-tutor" id="btn-close-tutor" aria-label="<?php esc_attr_e( 'Close AI Tutor', 'nctb-theme' ); ?>">✕</button>
					</div>
				</div>

				<div class="tutor-quick-chips" id="tutor-quick-chips">
					<button type="button" class="tutor-chip" data-action="explain">💡 <?php esc_html_e( 'Explain Step', 'nctb-theme' ); ?></button>
					<button type="button" class="tutor-chip" data-action="bangla">🇧🇩 <?php esc_html_e( 'বাংলায় ব্যাখ্যা', 'nctb-theme' ); ?></button>
					<button type="button" class="tutor-chip" data-action="hint">🔍 <?php esc_html_e( 'Give a Hint', 'nctb-theme' ); ?></button>
					<button type="button" class="tutor-chip" data-action="example">📝 <?php esc_html_e( 'Sentence Example', 'nctb-theme' ); ?></button>
					<button type="button" class="tutor-chip" data-action="why_wrong">❓ <?php esc_html_e( 'Why was I wrong?', 'nctb-theme' ); ?></button>
				</div>

				<div class="tutor-messages-stream" id="tutor-messages-stream">
					<div class="tutor-msg msg-ai">
						<div class="msg-bubble">
							👋 <strong><?php esc_html_e( 'Hello! I am your NCTB Lesson Tutor.', 'nctb-theme' ); ?></strong><br>
							<?php esc_html_e( 'I am grounded in this lesson\'s concepts. Tap any quick button above or ask me any question about vocabulary, grammar, or reading comprehension!', 'nctb-theme' ); ?>
						</div>
					</div>
				</div>

				<form class="tutor-input-box" id="tutor-input-form">
					<input type="text" id="tutor-user-input" class="tutor-input-field" placeholder="<?php esc_attr_e( 'Ask a question in Bangla or English...', 'nctb-theme' ); ?>" autocomplete="off">
					<button type="submit" class="nctb-btn nctb-btn-primary btn-send-tutor" id="btn-send-tutor">
						➤
					</button>
				</form>
			</aside>
		<?php endif; ?>

		<?php
		$nctb_lesson_board_qs = class_exists( 'NCTB_Board_Service' ) ? NCTB_Board_Service::get_lesson_board_questions( $nctb_lesson_id ) : array();
		if ( ! empty( $nctb_lesson_board_qs ) ) :
		?>
			<section class="nctb-lesson-board-questions-section">
				<div class="board-section-head">
					<div class="board-badge-icon">🏛️</div>
					<div>
						<h2><?php esc_html_e( 'Authentic Board Exam Questions on this Topic', 'nctb-theme' ); ?></h2>
						<p><?php esc_html_e( 'এই পাঠের বিষয়বস্তু থেকে বিগত বছরগুলোতে বিভিন্ন শিক্ষা বোর্ডে আসা প্রামাণ্য প্রশ্নসমূহ। (AI জেনারেটেড নয়, শতভাগ প্রামাণ্য)', 'nctb-theme' ); ?></p>
					</div>
				</div>

				<div class="nctb-board-questions-list">
					<?php foreach ( $nctb_lesson_board_qs as $bq ) :
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

							<details class="board-answer-details">
								<summary class="btn-reveal-answer">
									<span>👁️ <?php esc_html_e( 'বোর্ড নির্দেশিত উত্তর ও ব্যাখ্যা (Verified Answer)', 'nctb-theme' ); ?></span>
								</summary>
								<div class="board-answer-body">
									<div class="verified-status-tag">✅ <?php esc_html_e( 'Official Board Answer Scheme', 'nctb-theme' ); ?></div>
									<div class="ans-text"><strong>সঠিক উত্তর:</strong> <?php echo nl2br( esc_html( $bq->verified_answer ) ); ?></div>
									<?php if ( ! empty( $bq->explanation ) ) : ?>
										<div class="ans-expl"><strong>ব্যাখ্যা:</strong> <?php echo nl2br( esc_html( $bq->explanation ) ); ?></div>
									<?php endif; ?>
									<div class="ans-source"><small>📌 সূত্র: <?php echo esc_html( $bq->source_reference ); ?></small></div>
								</div>
							</details>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<div class="nctb-lesson-foot">
			<a href="<?php echo $nctb_unit_id ? esc_url( get_permalink( $nctb_unit_id ) ) : esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>" class="back-link">
				← <?php esc_html_e( 'Back to Unit Overview', 'nctb-theme' ); ?>
			</a>
		</div>
	</main>
	<?php
endwhile;

get_footer();
