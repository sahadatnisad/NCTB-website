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

		<?php if ( empty( $nctb_activities ) ) : ?>
			<!-- Fallback if no activity blocks created yet -->
			<?php if ( ! empty( $nctb_outcomes ) ) : ?>
				<section class="nctb-outcomes">
					<h2>🎯 <?php esc_html_e( 'Learning Outcomes', 'nctb-theme' ); ?></h2>
					<ul>
						<?php foreach ( $nctb_outcomes as $nctb_outcome ) : ?>
							<li><?php echo esc_html( $nctb_outcome->outcome_text ); ?></li>
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

			<!-- Sticky / Contextual Tutor Bar (Phase 9 Bridge) -->
			<div class="nctb-tutor-callout-bar">
				<div class="tutor-callout-inner">
					<div class="tutor-text">
						<span class="tutor-bot-icon">🤖</span>
						<span><strong><?php esc_html_e( 'Need help with this lesson?', 'nctb-theme' ); ?></strong> <?php esc_html_e( 'Ask the AI Tutor for instant explanations in Bangla or English.', 'nctb-theme' ); ?></span>
					</div>
					<button type="button" class="nctb-btn-tutor-trigger" id="btn-tutor-trigger" title="<?php esc_attr_e( 'AI Tutor Engine arrives in Phase 9', 'nctb-theme' ); ?>">
						💬 <?php esc_html_e( 'Ask AI Tutor (Phase 9)', 'nctb-theme' ); ?>
					</button>
				</div>
			</div>
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
