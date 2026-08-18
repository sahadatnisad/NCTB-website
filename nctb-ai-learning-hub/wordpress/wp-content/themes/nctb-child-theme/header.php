<?php
/**
 * Theme header — Phase 0 minimal.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="nctb-site-header" style="border-bottom:1px solid #e2e8f0;">
	<div style="max-width:var(--nctb-max-width);margin:0 auto;padding:1rem;">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" style="font-weight:700;text-decoration:none;color:var(--nctb-color-accent);">
			<?php bloginfo( 'name' ); ?>
		</a>
	</div>
</header>
