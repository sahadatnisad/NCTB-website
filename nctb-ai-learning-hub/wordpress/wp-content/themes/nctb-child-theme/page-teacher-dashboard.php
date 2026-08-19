<?php
/**
 * Template Name: Teacher Dashboard (Shikkhok Hub)
 *
 * Dedicated teacher portal dashboard page template.
 *
 * @package NCTB\Theme
 */

get_header();
?>

<main id="primary" class="site-main nctb-page-content">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( shortcode_exists( 'nctb_teacher_dashboard' ) ) {
			echo do_shortcode( '[nctb_teacher_dashboard]' );
		} else {
			the_content();
		}
	endwhile;
	?>
</main>

<?php
get_footer();
