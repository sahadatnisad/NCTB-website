<?php
/**
 * Enqueue scripts and styles
 */
function nctb_child_enqueue_scripts() {
    wp_enqueue_style('nctb-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_script('nctb-script', get_template_directory_uri() . '/script.js', array('jquery'), '1.0', true);
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true);
    wp_enqueue_script('nctb-analytics', get_template_directory_uri() . '/analytics.js', array('jquery', 'chartjs'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'nctb_child_enqueue_scripts');

/**
 * Load theme text domain
 */
function nctb_child_theme_setup() {
    load_theme_textdomain('nctb-child-theme', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'nctb_child_theme_setup');

/**
 * Register navigation locations
 */
function nctb_register_nav_menu() {
    register_nav_menu('primary', 'Primary Menu');
    register_nav_menu('student', 'Student Menu');
    register_nav_menu('teacher', 'Teacher Menu');
    register_nav_menu('student-dashboard', 'Student Dashboard Menu');
}
add_action('after_setup_theme', 'nctb_register_nav_menu');

/**
 * Role-based navigation customization
 */
add_filter('wp_nav_menu_args', 'nctb_custom_nav_menu_args', 10, 1);
function nctb_custom_nav_menu_args($args) {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $roles = (array) $user->roles;
        $role = array_shift($roles);

        if ($role === 'subscriber') {
            $args['menu'] = 'student';
            $args['container_class'] = 'student-nav';
        } elseif ($role === 'author' || $role === 'editor' || $role === 'administrator') {
            $args['menu'] = 'teacher';
            $args['container_class'] = 'teacher-nav';
        } else {
            $args['menu'] = 'primary';
            $args['container_class'] = 'main-nav';
        }
    } else {
        $args['menu'] = 'primary';
        $args['container_class'] = 'main-nav';
    }
    return $args;
}

/**
 * Shortcode for login form
 */
function nctb_login_shortcode() {
    if (is_user_logged_in()) {
        return '<p>You are already logged in.</p>';
    }
    $redirect = esc_url(home_url('/dashboard'));
    $login_form = wp_login_form(array(
        'echo' => false,
        'redirect' => $redirect,
        'form_id' => 'nctb-login-form',
        'label_username' => esc_html__('Username', 'nctb-learning-hub'),
        'label_password' => esc_html__('Password', 'nctb-learning-hub'),
        'label_remember' => esc_html__('Remember Me', 'nctb-learning-hub'),
        'label_log_in' => esc_html__('Log In', 'nctb-learning-hub'),
        'id_username' => 'user_login',
        'id_password' => 'user_pass',
        'id_remember' => 'rememberme',
        'id_submit'   => 'wp-submit',
    ));
    return $login_form;
}
add_shortcode('nctb_login', 'nctb_login_shortcode');

/**
 * Shortcode for registration form
 */
function nctb_register_shortcode() {
    if (is_user_logged_in()) {
        return '<p>You are already registered and logged in.</p>';
    }
    $output = '<h2>Register</h2>';
    $output .= '<form action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="post">
        <label for="nctb_register_username">Username</label>
        <input type="text" id="nctb_register_username" name="nctb_register_username" required>
        <label for="nctb_register_email">Email</label>
        <input type="email" id="nctb_register_email" name="nctb_register_email" required>
        <label for="nctb_register_password">Password</label>
        <input type="password" id="nctb_register_password" name="nctb_register_password" required>
        <label for="nctb_register_confirm">Confirm Password</label>
        <input type="password" id="nctb_register_confirm" name="nctb_register_confirm" required>
        <input type="hidden" name="nctb_register_nonce" value="' . wp_create_nonce('nctb-register') . '">
        <input type="submit" value="Register" class="button-primary" name="nctb_wp_register">
    </form>';
    // Process registration
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['nctb_wp_register'])) {
        $registered = nctb_process_registration();
        if (is_wp_error($registered)) {
            $output = '<p class="error">' . $registered->get_error_message() . '</p>' . $output;
        } elseif ($registered) {
            $output = '<p class="success">Registration completed. You can now <a href="' . esc_url(home_url('/login')) . '">log in</a>.</p>';
        } else {
            $output = '<p class="error">Unexpected error. Please try again.</p>';
        }
    }
    return $output;
}
add_shortcode('nctb_register', 'nctb_register_shortcode');

/**
 * Handle user registration
 */
function nctb_process_registration() {
    check_admin_referer('nctb-register', 'nctb_register_nonce');
    $username = sanitize_user($_POST['nctb_register_username']);
    $email    = sanitize_email($_POST['nctb_register_email']);
    $password = esc_attr($_POST['nctb_register_password']);
    $confirm  = esc_attr($_POST['nctb_register_confirm']);

    if ($password !== $confirm) {
        return new WP_Error('password_mismatch', __('Passwords do not match.'));
    }

    $user_id = wp_create_user($username, $password, $email);
    if (is_wp_error($user_id)) {
        return $user_id;
    }

    // Add default student role
    $user = new WP_User($user_id);
    $user->add_role('subscriber');

    return true;
}

/**
 * Redirect after login to dashboard
 */
add_filter('login_redirect', 'nctb_login_redirect', 10, 3);
function nctb_login_redirect( $redirect_to, $request, $user ) {
    if ( $user && ! is_wp_error( $user ) ) {
        return home_url('/dashboard');
    }
    return $redirect_to;
}

/**
 * Enforce Role-Based Access for new user role
 */
add_filter('user_has_cap', 'nctb_restrict_user_caps', 10, 3);
function nctb_restrict_user_caps($user_caps, $capabilities, $args){
    $user_id = isset($args[0])? $args[0] : get_current_user_id();
    $user = new WP_User($user_id);
    $roles = $user->roles;
    $role = array_shift($roles);
    // Example: prevent subscriber from with plugin access beyond allowed caps
    if($role === 'subscriber'){
        // keep default capabilities
    }
    return $user_caps;
}

/**
 * Enqueue ajaxurl for js
 */
function nctb_add_ajax_url() {
    wp_localize_script('nctb-script', 'ajax_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nctb_ajax_nonce' => wp_create_nonce('nctb_ajax_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'nctb_add_ajax_url');

/**
 * Create login and register pages on init if they don't exist
 */
function nctb_create_auth_pages() {
    $login_page = array(
        'post_title' => 'Login',
        'post_name' => 'login',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => '[nctb_login]',
        'post_author' => 1,
    );
    $login_page_id = page_exists_by_slug('login');
    if (!$login_page_id) {
        $login_page_id = wp_insert_post($login_page);
        // Set template
        update_post_meta($login_page_id, '_wp_page_template', 'page-login.php');
    }

    $register_page = array(
        'post_title' => 'Register',
        'post_name' => 'register',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => '[nctb_register]',
        'post_author' => 1,
    );
    $register_page_id = page_exists_by_slug('register');
    if (!$register_page_id) {
        $register_page_id = wp_insert_post($register_page);
        // Set template
        update_post_meta($register_page_id, '_wp_page_template', 'page-register.php');
    }
}
function page_exists_by_slug($slug) {
    $page = get_page_by_path($slug, OBJECT, 'page');
    return $page ? $page->ID : 0;
}
add_action('init', 'nctb_create_auth_pages');
?>