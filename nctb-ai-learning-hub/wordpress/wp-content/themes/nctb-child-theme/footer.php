<?php
/**
 * Theme footer — NCTB Learning Hub.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nctb_marketing_slugs = array( 'how-it-works', 'subjects', 'ssc-english', 'hsc-english', 'pricing', 'faq', 'contact', 'privacy-policy', 'terms' );
$nctb_is_marketing    = is_front_page() || is_page( $nctb_marketing_slugs );

if ( ! $nctb_is_marketing ) :
?>
<footer class="nctb-site-footer" style="border-top:1px solid #e2e8f0;margin-top:2.5rem;background:#ffffff;">
	<div style="max-width:var(--nctb-max-width);margin:0 auto;padding:1.5rem 1rem;font-size:.875rem;color:#64748b;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
		<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'NCTB Aligned Learning Hub.', 'nctb-theme' ); ?></span>
		<div style="display:flex;gap:1.25rem;">
			<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:#64748b;text-decoration:none;"><?php esc_html_e( 'Privacy', 'nctb-theme' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:#64748b;text-decoration:none;"><?php esc_html_e( 'Support', 'nctb-theme' ); ?></a>
		</div>
	</div>
</footer>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
