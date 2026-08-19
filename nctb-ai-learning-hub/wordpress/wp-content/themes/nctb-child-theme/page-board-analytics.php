<?php
/**
 * Template Name: Board Pattern Analytics
 *
 * Presentation only. Renders [nctb_board_analytics].
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
