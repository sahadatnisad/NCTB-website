<?php
/**
 * Theme footer — Phase 0 minimal.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="nctb-site-footer" style="border-top:1px solid #e2e8f0;margin-top:2rem;">
	<div style="max-width:var(--nctb-max-width);margin:0 auto;padding:1rem;font-size:.875rem;color:#64748b;">
		&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
