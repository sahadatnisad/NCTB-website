<?php
/**
 * Template Name: Student Purchases & Passes
 *
 * Presentation only. Renders [nctb_my_purchases].
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

?>
<main id="primary" class="nctb-main nctb-student-page">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php

get_footer();
