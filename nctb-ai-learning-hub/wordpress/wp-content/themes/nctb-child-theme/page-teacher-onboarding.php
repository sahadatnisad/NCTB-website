<?php
/**
 * Template Name: Teacher Onboarding (Shikkhok Hub)
 *
 * Dedicated teacher onboarding wizard page template.
 *
 * @package NCTB\Theme
 */

get_header();
?>

<main id="primary" class="site-main nctb-page-content">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( shortcode_exists( 'nctb_teacher_onboarding' ) ) {
			echo do_shortcode( '[nctb_teacher_onboarding]' );
		} else {
			the_content();
		}
	endwhile;
	?>
</main>

<?php
get_footer();
