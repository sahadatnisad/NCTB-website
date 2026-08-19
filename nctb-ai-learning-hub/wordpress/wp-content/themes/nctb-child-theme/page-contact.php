<?php
/**
 * Public page: Contact / Support.
 *
 * Presentation only. Auto-applies to the page with slug "contact".
 * The form uses a mailto action as a lightweight default; a proper form
 * handler / plugin can replace it later.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$nctb_admin_email = antispambot( get_option( 'admin_email' ) );
?>
<div class="nctb-mkt">
	<section class="mkt-hero">
		<div class="mkt-wrap mkt-center">
			<span class="mkt-eyebrow"><?php esc_html_e( 'Contact & support', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'We’re here to help', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'Questions about lessons, payments, or your account? Send us a message.', 'nctb-theme' ); ?></p>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<div class="mkt-grid" style="grid-template-columns:1fr;">
				<div class="mkt-card">
					<h3>✉️ <?php esc_html_e( 'Email', 'nctb-theme' ); ?></h3>
					<p><a href="mailto:<?php echo esc_attr( $nctb_admin_email ); ?>" style="color:#0b6e4f;font-weight:700;"><?php echo esc_html( $nctb_admin_email ); ?></a></p>
				</div>

				<div class="mkt-card">
					<h3>📝 <?php esc_html_e( 'Send a message', 'nctb-theme' ); ?></h3>
					<form action="mailto:<?php echo esc_attr( $nctb_admin_email ); ?>" method="post" enctype="text/plain" style="display:flex;flex-direction:column;gap:0.75rem;margin-top:0.75rem;">
						<input type="text" name="name" placeholder="<?php esc_attr_e( 'Your name', 'nctb-theme' ); ?>" required style="padding:0.7rem;border:1px solid #e2e8f0;border-radius:10px;">
						<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email', 'nctb-theme' ); ?>" required style="padding:0.7rem;border:1px solid #e2e8f0;border-radius:10px;">
						<textarea name="message" rows="5" placeholder="<?php esc_attr_e( 'How can we help?', 'nctb-theme' ); ?>" required style="padding:0.7rem;border:1px solid #e2e8f0;border-radius:10px;"></textarea>
						<button type="submit" class="mkt-btn mkt-btn-primary" style="align-self:flex-start;"><?php esc_html_e( 'Send message', 'nctb-theme' ); ?></button>
					</form>
					<p class="mkt-hero-note"><?php esc_html_e( 'This opens your email app. A built-in contact form can be added later.', 'nctb-theme' ); ?></p>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
