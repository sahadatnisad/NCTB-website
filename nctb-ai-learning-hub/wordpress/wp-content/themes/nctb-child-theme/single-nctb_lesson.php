<?php
/**
 * Single Lesson: learning outcomes, content, and linked concepts, with
 * breadcrumb back up the Book › Unit hierarchy.
 *
 * Presentation only. Outcomes and concepts come from the plugin data service.
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

	$nctb_unit_id = $nctb_has_cpt ? NCTB_Curriculum_CPT::get_lesson_unit( $nctb_lesson_id ) : 0;
	$nctb_book_id = ( $nctb_has_cpt && $nctb_unit_id ) ? NCTB_Curriculum_CPT::get_unit_book( $nctb_unit_id ) : 0;
	$nctb_outcomes = $nctb_has_data ? NCTB_Curriculum_Data::get_lesson_outcomes( $nctb_lesson_id ) : array();
	$nctb_concepts = $nctb_has_data ? NCTB_Curriculum_Data::get_lesson_concepts( $nctb_lesson_id ) : array();
	?>
	<main id="primary" class="nctb-main nctb-curriculum nctb-lesson">
		<nav class="nctb-breadcrumb">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>"><?php esc_html_e( 'Books', 'nctb-theme' ); ?></a>
			<?php if ( $nctb_book_id ) : ?>
				<span>›</span> <a href="<?php echo esc_url( get_permalink( $nctb_book_id ) ); ?>"><?php echo esc_html( get_the_title( $nctb_book_id ) ); ?></a>
			<?php endif; ?>
			<?php if ( $nctb_unit_id ) : ?>
				<span>›</span> <a href="<?php echo esc_url( get_permalink( $nctb_unit_id ) ); ?>"><?php echo esc_html( get_the_title( $nctb_unit_id ) ); ?></a>
			<?php endif; ?>
			<span>›</span> <?php the_title(); ?>
		</nav>

		<header class="nctb-page-head">
			<h1>📝 <?php the_title(); ?></h1>
		</header>

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

		<?php if ( ! empty( $nctb_concepts ) ) : ?>
			<section class="nctb-concepts">
				<h2>🔑 <?php esc_html_e( 'Concepts in this lesson', 'nctb-theme' ); ?></h2>
				<div class="nctb-concept-chips">
					<?php foreach ( $nctb_concepts as $nctb_concept ) : ?>
						<span class="nctb-chip"><?php echo esc_html( $nctb_concept->name ); ?></span>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<div class="nctb-lesson-foot">
			<span class="soon-tag"><?php esc_html_e( 'Practice, tutor and quiz arrive in later phases.', 'nctb-theme' ); ?></span>
		</div>
	</main>
	<?php
endwhile;

get_footer();
