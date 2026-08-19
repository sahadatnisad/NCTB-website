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
	<script>( function () { try { if ( localStorage.getItem( 'nctbTheme' ) === 'dark' ) { document.documentElement.setAttribute( 'data-theme', 'dark' ); } } catch ( e ) {} }() );</script>
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
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-link" data-en="Home" data-bn="হোম">Home</a>

			<?php if ( $nctb_is_marketing || ! $nctb_logged_in ) : ?>
				<?php // Marketing menu (visitors, and everyone on public pages). ?>
				<a href="<?php echo esc_url( home_url( '/how-it-works/' ) ); ?>" class="nav-link" data-en="How it works" data-bn="কীভাবে চলে">How it works</a>
				<a href="<?php echo esc_url( home_url( '/subjects/' ) ); ?>" class="nav-link" data-en="Subjects" data-bn="বিষয়সমূহ">Subjects</a>
				<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>" class="nav-link" data-en="Pricing" data-bn="মূল্য">Pricing</a>
				<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>" class="nav-link" data-en="FAQ" data-bn="প্রশ্নোত্তর">FAQ</a>
				<?php if ( $nctb_logged_in ) : ?>
					<a href="<?php echo esc_url( home_url( $nctb_is_complete ? '/dashboard' : '/onboarding' ) ); ?>" class="nctb-btn-sm btn-login" data-en="<?php echo esc_attr( $nctb_is_complete ? 'Dashboard' : 'Setup' ); ?>" data-bn="<?php echo esc_attr( $nctb_is_complete ? 'ড্যাশবোর্ড' : 'সেটআপ' ); ?>"><?php echo esc_html( $nctb_is_complete ? 'Dashboard' : 'Setup' ); ?></a>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="nav-link" data-en="Logout" data-bn="লগআউট">Logout</a>
				<?php else : ?>
					<a href="<?php echo esc_url( wp_login_url( home_url( '/onboarding' ) ) ); ?>" class="nctb-btn-sm btn-login" data-en="Login" data-bn="লগইন">Login</a>
				<?php endif; ?>

			<?php else : ?>
				<?php // Student app menu (logged in, on learning pages). ?>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_book' ) ); ?>" class="nav-link" data-en="Learn" data-bn="পাঠ্যবই">Learn</a>
				<a href="<?php echo esc_url( home_url( '/board-questions' ) ); ?>" class="nav-link" data-en="Board" data-bn="বোর্ড প্রশ্ন">Board</a>
				<a href="<?php echo esc_url( home_url( '/mistakes' ) ); ?>" class="nav-link" data-en="Mistakes" data-bn="ভুলখাতা">Mistakes</a>
				<a href="<?php echo esc_url( home_url( '/revision' ) ); ?>" class="nav-link" data-en="Revision" data-bn="রিভিশন">Revision</a>
				<a href="<?php echo esc_url( home_url( '/progress' ) ); ?>" class="nav-link" data-en="Progress" data-bn="অগ্রগতি">Progress</a>
				<a href="<?php echo esc_url( home_url( '/purchases' ) ); ?>" class="nav-link" data-en="Passes" data-bn="পাস">Passes</a>
				<?php if ( ! $nctb_is_complete ) : ?>
					<a href="<?php echo esc_url( home_url( '/onboarding' ) ); ?>" class="nav-link highlight" data-en="Setup" data-bn="সেটআপ">Setup</a>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>" class="nav-link" data-en="Dashboard" data-bn="ড্যাশবোর্ড">Dashboard</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="nctb-btn-sm btn-logout" data-en="Logout" data-bn="লগআউট">Logout</a>
			<?php endif; ?>

			<span class="nctb-ui-controls">
				<button type="button" class="nctb-ui-btn" id="nctb-lang-toggle" aria-label="Switch language" title="English / বাংলা"><span class="ico">🌐</span><span id="nctb-lang-label">বাংলা</span></button>
				<button type="button" class="nctb-ui-btn" id="nctb-theme-toggle" aria-label="Toggle dark mode" title="Light / Dark mode"><span class="ico">🌙</span></button>
			</span>
		</nav>
	</div>
</header>
