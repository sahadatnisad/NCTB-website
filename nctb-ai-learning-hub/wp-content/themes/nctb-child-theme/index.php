<?php
/**
 * Main template — Phase 0 minimal fallback.
 *
 * This is a deliberately bare, valid template so the theme activates cleanly.
 * The real public/student visual shell is built in Phase 1. No learning logic
 * or hard-coded curriculum content lives here.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="nctb-main" style="max-width:var(--nctb-max-width);margin:0 auto;padding:1rem;">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
			<?php
		}
	} else {
		?>
		<p><?php esc_html_e( 'Nothing here yet.', 'nctb-theme' ); ?></p>
		<?php
	}
	?>
</main>

<?php
get_footer();
