<?php
/**
 * Template Name: Student Dashboard
 *
 * Dedicated student dashboard page template.
 *
 * @package NCTB\Theme
 */

get_header();
?>

<main id="primary" class="site-main nctb-page-content">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( shortcode_exists( 'nctb_student_dashboard' ) ) {
			echo do_shortcode( '[nctb_student_dashboard]' );
		} else {
			the_content();
		}
	endwhile;
	?>
</main>

<?php
get_footer();
