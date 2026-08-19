<?php
/**
 * Theme header — NCTB Learning Hub.
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
<header class="nctb-site-header">
	<div class="nctb-header-inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="nctb-logo">
			<span class="logo-badge">🇧🇩 NCTB</span>
			<span class="logo-title"><?php bloginfo( 'name' ); ?></span>
		</a>

		<?php
		// Decide which menu to show based on the PAGE context, not just login.
		// Marketing/public pages always get the marketing menu; the student app
		// menu only appears on the actual learning pages.
		$nctb_marketing_slugs = array( 'how-it-works', 'subjects', 'ssc-english', 'hsc-english', 'pricing', 'faq', 'contact', 'privacy-policy', 'terms' );
		$nctb_is_marketing    = is_front_page() || is_page( $nctb_marketing_slugs );
		$nctb_logged_in       = is_user_logged_in();
		$nctb_is_complete     = $nctb_logged_in && class_exists( 'NCTB_Student_Profile' ) && NCTB_Student_Profile::is_onboarding_complete( get_current_user_id() );
		?>
		<nav class="nctb-nav">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-link">হোম (Home)</a>

			<?php if ( $nctb_is_marketing || ! $nctb_logged_in ) : ?>
				<?php // Marketing menu (visitors, and everyone on public pages). ?>
				<a href="<?php echo esc_url( home_url( '/how-it-works/' ) ); ?>" class="nav-link"><?php esc_html_e( 'How it works', 'nctb-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/subjects/' ) ); ?>" class="nav-link"><?php esc_html_e( 'Subjects', 'nctb-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>" class="nav-link"><?php esc_html_e( 'Pricing', 'nctb-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>" class="nav-link"><?php esc_html_e( 'FAQ', 'nctb-theme' ); ?></a>
				<?php if ( $nctb_logged_in ) : ?>
					<a href="<?php echo esc_url( home_url( $nctb_is_complete ? '/dashboard' : '/onboarding' ) ); ?>" class="nctb-btn-sm btn-login"><?php echo esc_html( $nctb_is_complete ? __( 'Dashboard', 'nctb-theme' ) : __( 'Setup', 'nctb-theme' ) ); ?></a>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="nav-link"><?php esc_html_e( 'Logout', 'nctb-theme' ); ?></a>
				<?php else : ?>
					<a href="<?php echo esc_url( wp_login_url( home_url( '/onboarding' ) ) ); ?>" class="nctb-btn-sm btn-login">লগইন (Login)</a>
				<?php endif; ?>

			<?php else : ?>
				<?php // Student app menu (logged in, on learning pages). ?>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>" class="nav-link">পাঠ্যবই (Learn)</a>
				<a href="<?php echo esc_url( home_url( '/mistakes' ) ); ?>" class="nav-link">ভুলখাতা (Mistakes)</a>
				<a href="<?php echo esc_url( home_url( '/revision' ) ); ?>" class="nav-link">রিভিশন (Revision)</a>
				<a href="<?php echo esc_url( home_url( '/progress' ) ); ?>" class="nav-link">অগ্রগতি (Progress)</a>
				<a href="<?php echo esc_url( home_url( '/purchases' ) ); ?>" class="nav-link">পাস (Passes)</a>
				<?php if ( ! $nctb_is_complete ) : ?>
					<a href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>" class="nav-link highlight">অনবোর্ডিং (Setup)</a>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>" class="nav-link">ড্যাশবোর্ড (Dashboard)</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="nctb-btn-sm btn-logout">লগআউট (Logout)</a>
			<?php endif; ?>
		</nav>
	</div>
</header>
