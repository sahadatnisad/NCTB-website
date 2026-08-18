<?php
/**
 * Header template
 */

$logo_url = home_url('/');
$dashboard_url = home_url('/dashboard');
$login_url = wp_login_url($dashboard_url);
$register_url = wp_registration_url();
$logout_url = wp_logout_url($dashboard_url);

// Check if user is logged in
$logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
$display_name = $current_user ? $current_user->display_name : '';

// Get current language
$current_language = isset($_COOKIE['nctb_language']) ? $_COOKIE['nctb_language'] : 'en';
if (!in_array($current_language, array('en', 'bn'))) {
    $current_language = 'en';
}

// Get navigation menu
$args = array(
    'theme_location' => 'primary',
    'menu' => 'Primary Menu',
    'echo' => false,
    'walker' => new WP_NavWalker()
);
$nav_menu = wp_nav_menu($args);

// Add dashboard link to navigation if user is logged in
if ($logged_in) {
    $dashboard_link = '<li class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item current_page_item"><a href="' . esc_url($dashboard_url) . '">Dashboard</a></li>';
} else {
    $dashboard_link = '';
}

// Add login/register links to navigation
$auth_links = '';
if (!$logged_in) {
    $auth_links = '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="' . esc_url($login_url) . '">Log In</a></li>' .
                  '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="' . esc_url($register_url) . '">Register</a></li>';
} else {
    $auth_links = '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="' . esc_url($logout_url) . '">Log Out</a></li>';
}

// Language switcher
$language_switcher = '<div class="language-switcher">';
$language_switcher .= '<form id="language-selector" method="post" action="">';
$language_switcher .= '<select name="nctb_language" id="nctb_language_select">';
$language_switcher .= '<option value="en"' . ($current_language == 'en' ? ' selected' : '') . '>English</option>';
$language_switcher .= '<option value="bn"' . ($current_language == 'bn' ? ' selected' : '') . '>বাংলা</option>';
$language_switcher .= '</select>';
$language_switcher .= '<noscript><input type="submit" value="Go" /></noscript>';
$language_switcher .= '</form>';
$language_switcher .= '</div>';

// Combine navigation
$nav_with_dashboard = '<ul class="nav-menu">' . $nav_menu . $dashboard_link . $auth_links . '</ul>';

echo '<header class="site-header">
    <div class="container">
        <div class="site-branding">
            <a href="' . esc_url($logo_url) . '" class="logo">NCTB AI Learning Hub</a>
        </div>
        <nav class="main-navigation" aria-label="Primary Menu">
            ' . $nav_with_dashboard . '
        </nav>
        <div class="mobile-menu-toggle" aria-label="Toggle mobile menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        ' . $language_switcher . '
    </div>
</header>';

// Mobile navigation fallback
if (wp_is_mobile()) {
    echo '<nav class="mobile-nav" aria-label="Mobile Menu">' . $nav_with_dashboard . '</nav>';
}

// Handle language switching
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nctb_language'])) {
    $selected_language = sanitize_text_field($_POST['nctb_language']);
    if (in_array($selected_language, array('en', 'bn'))) {
        setcookie('nctb_language', $selected_language, time() + (365 * 24 * 60 * 60), COOKIEPATH, COOKIE_DOMAIN);
        // Reload the page to apply the language change
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}