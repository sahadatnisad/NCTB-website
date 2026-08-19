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

		<nav class="nctb-nav">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-link">হোম (Home)</a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>" class="nav-link">পাঠ্যবই (Learn)</a>
			<a href="<?php echo esc_url( home_url( '/mistakes' ) ); ?>" class="nav-link">ভুলখাতা (Mistakes)</a>
			<a href="<?php echo esc_url( home_url( '/revision' ) ); ?>" class="nav-link">রিভিশন (Revision)</a>
			<a href="<?php echo esc_url( home_url( '/progress' ) ); ?>" class="nav-link">অগ্রগতি (Progress)</a>
			<a href="<?php echo esc_url( home_url( '/purchases' ) ); ?>" class="nav-link">পাস (Passes)</a>
			<?php if ( is_user_logged_in() ) :
				$current_user_id = get_current_user_id();
				$is_complete = class_exists( 'NCTB_Student_Profile' ) && NCTB_Student_Profile::is_onboarding_complete( $current_user_id );
			?>
				<?php if ( ! $is_complete ) : ?>
					<a href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>" class="nav-link highlight">অনবোর্ডিং (Setup)</a>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>" class="nav-link">ড্যাশবোর্ড (Dashboard)</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="nctb-btn-sm btn-logout">লগআউট (Logout)</a>
			<?php else : ?>
				<a href="<?php echo esc_url( wp_login_url( home_url( '/onboarding' ) ) ); ?>" class="nctb-btn-sm btn-login">লগইন (Login)</a>
			<?php endif; ?>
		</nav>
	</div>
</header>
