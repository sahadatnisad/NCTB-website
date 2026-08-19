<?php
/**
 * Single Book: shows the book and its ordered units (with nested lessons).
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
	$nctb_book_id = get_the_ID();
	$nctb_units   = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_units( $nctb_book_id ) : array();
	?>
	<main id="primary" class="nctb-main nctb-curriculum">
		<nav class="nctb-breadcrumb">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>"><?php esc_html_e( 'Books', 'nctb-theme' ); ?></a>
			<span>›</span> <?php the_title(); ?>
		</nav>

		<header class="nctb-page-head">
			<h1>📖 <?php the_title(); ?></h1>
			<?php if ( get_the_content() ) : ?>
				<div class="nctb-prose"><?php the_content(); ?></div>
			<?php endif; ?>
		</header>

		<h2 class="nctb-section-title"><?php esc_html_e( 'Units / অধ্যায়সমূহ', 'nctb-theme' ); ?></h2>

		<?php if ( empty( $nctb_units ) ) : ?>
			<div class="nctb-empty"><?php esc_html_e( 'No units yet.', 'nctb-theme' ); ?></div>
		<?php else : ?>
			<div class="nctb-unit-list">
				<?php
				foreach ( $nctb_units as $nctb_index => $nctb_unit ) :
					$nctb_lessons = NCTB_Curriculum_CPT::get_lessons( $nctb_unit->ID );
					?>
					<section class="nctb-unit-block">
						<a class="nctb-unit-head" href="<?php echo esc_url( get_permalink( $nctb_unit ) ); ?>">
							<span class="unit-num"><?php echo esc_html( (string) ( $nctb_index + 1 ) ); ?></span>
							<span class="unit-title"><?php echo esc_html( $nctb_unit->post_title ); ?></span>
							<span class="unit-count"><?php echo esc_html( count( $nctb_lessons ) ); ?> <?php esc_html_e( 'lessons', 'nctb-theme' ); ?></span>
						</a>
						<?php if ( ! empty( $nctb_lessons ) ) : ?>
							<ul class="nctb-lesson-list">
								<?php foreach ( $nctb_lessons as $nctb_lesson ) : ?>
									<li>
										<a href="<?php echo esc_url( get_permalink( $nctb_lesson ) ); ?>">
											<span class="lesson-dot">•</span> <?php echo esc_html( $nctb_lesson->post_title ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</main>
	<?php
endwhile;

get_footer();
