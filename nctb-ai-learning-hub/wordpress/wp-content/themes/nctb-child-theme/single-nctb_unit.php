<?php
/**
 * Single Unit: shows the unit and its ordered lessons, with breadcrumb.
 *
 * Presentation only.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$nctb_unit_id = get_the_ID();
	$nctb_book_id = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_unit_book( $nctb_unit_id ) : 0;
	$nctb_lessons = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_lessons( $nctb_unit_id ) : array();
	?>
	<main id="primary" class="nctb-main nctb-curriculum">
		<nav class="nctb-breadcrumb">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>"><?php esc_html_e( 'Books', 'nctb-theme' ); ?></a>
			<?php if ( $nctb_book_id ) : ?>
				<span>›</span> <a href="<?php echo esc_url( get_permalink( $nctb_book_id ) ); ?>"><?php echo esc_html( get_the_title( $nctb_book_id ) ); ?></a>
			<?php endif; ?>
			<span>›</span> <?php the_title(); ?>
		</nav>

		<header class="nctb-page-head">
			<h1>🗂️ <?php the_title(); ?></h1>
			<?php if ( get_the_content() ) : ?>
				<div class="nctb-prose"><?php the_content(); ?></div>
			<?php endif; ?>
		</header>

		<h2 class="nctb-section-title"><?php esc_html_e( 'Lessons / পাঠসমূহ', 'nctb-theme' ); ?></h2>

		<?php if ( empty( $nctb_lessons ) ) : ?>
			<div class="nctb-empty"><?php esc_html_e( 'No lessons yet.', 'nctb-theme' ); ?></div>
		<?php else : ?>
			<ol class="nctb-lesson-cards">
				<?php foreach ( $nctb_lessons as $nctb_lesson ) : ?>
					<li>
						<a class="nctb-lesson-card" href="<?php echo esc_url( get_permalink( $nctb_lesson ) ); ?>">
							<span class="lesson-card-title"><?php echo esc_html( $nctb_lesson->post_title ); ?></span>
							<span class="lesson-card-go"><?php esc_html_e( 'Open', 'nctb-theme' ); ?> →</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</main>
	<?php
endwhile;

get_footer();
