<?php
/**
 * Template Name: Student Dashboard
 * Template Post Type: page
 */

get_header(); ?>

<main>
    <div class="container">
        <div class="dashboard-header">
            <h1><?php esc_html_e('Student Dashboard', 'nctb-learning-hub'); ?></h1>
            <p><?php esc_html_e('Track your learning progress and explore courses', 'nctb-learning-hub'); ?></p>
        </div>

        <div class="dashboard-grid">
            <!-- Sidebar Navigation -->
            <aside class="dashboard-sidebar">
                <nav class="dashboard-nav">
                    <ul>
                        <li><a href="#lessons" class="active">My Lessons</a></li>
                        <li><a href="#subjects">Subjects</a></li>
                        <li><a href="#progress">Progress</a></li>
                        <li><a href="#achievements">Achievements</a></li>
                        <?php
                        if (current_user_can('edit_posts')) {
                            echo '<li><a href="/teacher">Teacher View</a></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <section class="dashboard-content">
                <div id="lessons" class="dashboard-section">
                    <h2><?php esc_html_e('My Lessons', 'nctb-learning-hub'); ?></h2>
                    <div class="lessons-grid">
                        <?php
                        $args = array(
                            'post_type' => 'lesson',
                            'posts_per_page' => 6,
                            'orderby' => 'date',
                            'order' => 'DESC',
                        );
                        $lessons_query = new WP_Query($args);
                        if ($lessons_query->have_posts()) {
                            while ($lessons_query->have_posts()) {
                                $lessons_query->the_post();
                                $lesson_id = get_the_ID();
                                $progress = get_user_meta(get_current_user_id(), 'nctb_lesson_progress_' . $lesson_id, true);
                                $percentage = $progress ? ($progress / 100) * 100 : 0;
                                ?>
                                <div class="lesson-card">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) {
                                            the_post_thumbnail('thumbnail', array('class' => 'lesson-thumb'));
                                        } ?>
                                        <div class="lesson-info">
                                            <h3><?php the_title(); ?></h3>
                                            <p class="lesson-meta"><?php esc_html_e('Subject:', 'nctb-learning-hub'); ?> <?php echo get_the_term_list($lesson_id, 'subject', '', ', '); ?></p>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                            </div>
                                            <span class="progress-text"><?php echo $percentage > 0 ? $percentage . '%' : 'Not started'; ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php }
                        } else {
                            echo '<p>No lessons found.</p>';
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>

                <div id="subjects" class="dashboard-section">
                    <h2><?php esc_html_e('Browse Subjects', 'nctb-learning-hub'); ?></h2>
                    <div class="subjects-grid">
                        <?php
                        $terms = get_terms(array(
                            'taxonomy' => 'subject',
                            'hide_empty' => false,
                            'number' => 12,
                        ));
                        foreach ($terms as $term) {
                            $term_link = get_term_link($term);
                            $term_count = $term->count;
                            ?>
                            <div class="subject-card">
                                <a href="<?php echo esc_url($term_link); ?>">
                                    <h3><?php echo $term->name; ?></h3>
                                    <p><?php echo $term_count; ?> lessons available</p>
                                </a>

                    </div>
                </div>

                <div id="progress" class="dashboard-section">
                    <h2><?php esc_html_e('Learning Progress', 'nctb-learning-hub'); ?></h2>
                    <div class="progress-summary">
                        <div class="progress-item">
                            <span>Total Lessons Enrolled:</span>
                            <span><?php echo count_user_posts(get_current_user_id(), 'lesson'); ?></span>
                        </div>
                        <div class="progress-item">
                            <span>Lessons Completed:</span>
                            <span><?php
                            $completed = count_user_posts(get_current_user_id(), 'lesson', array('meta_key' => 'nctb_lesson_progress', 'meta_value' => 100));
                            echo $completed;
                            ?></span>
                        </div>
                        <div class="progress-item">
                            <span>Overall Progress:</span>
                            <span><?php
                            $total = count_user_posts(get_current_user_id(), 'lesson');
                            if ($total > 0) {
                                $completed = count_user_posts(get_current_user_id(), 'lesson', array('meta_key' => 'nctb_lesson_progress', 'meta_value' => 100));
                                $overall = ($completed / $total) * 100;
                                echo round($overall, 1) . '%';
                            } else {
                                echo '0%';
                            }
                            ?></span>
                        </div>
                    </div>
                </div>
            </section>

<!-- Achievements Section -->
<div id="achievements" class="dashboard-section">
    <h2><?php esc_html_e('Achievements', 'nctb-learning-hub'); ?></h2>
    <div class="achievements-summary">
        <div class="achievement-item">
            <span><?php esc_html_e('Total Points:', 'nctb-learning-hub'); ?></span>
            <span id="user-points">Loading...</span>
        </div>
        <div class="achievement-item">
            <span><?php esc_html_e('Badges Earned:', 'nctb-learning-hub'); ?></span>
            <span id="user-badges-count">Loading...</span>
        </div>
    </div>
    <div class="achievements-details">
        <div id="user-badges"><?php esc_html_e('Loading badges...', 'nctb-learning-hub'); ?></div>
        <div id="user-leaderboard" style="margin-top: 20px;">
            <h3><?php esc_html_e('Leaderboard', 'nctb-learning-hub'); ?></h3>
            <div id="leaderboard-content"><?php esc_html_e('Loading leaderboard...', 'nctb-learning-hub'); ?></div>
        </div>
    </div>
</div>
        </div>
    </div>
</main>

<?php
// Set AJAX URL and nonce for front-end
$nctb_ajax_url = admin_url('admin-ajax.php');
$nctb_ajax_nonce = wp_create_nonce('nctb_ajax_nonce');
?>

<script>
jQuery(document).ready(function($) {
    // Fetch user points
    $.ajax({
        url: nctb_ajax_url,
        method: 'POST',
        data: {
            action: 'nctb_get_user_points',
            nonce: nctb_ajax_nonce
        },
        success: function(response) {
            if (response.success) {
                $('#user-points').text(response.data.points + ' points');
            } else {
                $('#user-points').text('Error');
            }
        },
        error: function() {
            $('#user-points').text('Error');
        }
    });

    // Fetch user badges
    $.ajax({
        url: nctb_ajax_url,
        method: 'POST',
        data: {
            action: 'nctb_get_user_badges',
            nonce: nctb_ajax_nonce
        },
        success: function(response) {
            if (response.success) {
                var badges = response.data;
                if (badges.length === 0) {
                    $('#user-badges').html('<p>No badges earned yet.</p>');
                    $('#user-badges-count').text('0');
                } else {
                    var count = badges.length;
                    $('#user-badges-count').text(count + ' badges');
                    var badgesHTML = '';
                    $.each(badges, function(index, badge) {
                        badgesHTML += '<div class="nctb-badge" title="' + badge.description + '">';
                        badgesHTML += '<span class="nctb-badge-icon">' + badge.icon + '</span>';
                        badgesHTML += '<span class="nctb-badge-name">' + badge.name + '</span>';
                        badgesHTML += '</div>';
                    });
                    $('#user-badges').html(badgesHTML);
                }
            } else {
                $('#user-badges').html('<p>Error loading badges.</p>');
            }
        },
        error: function() {
            $('#user-badges').html('<p>Error loading badges.</p>');
        }
    });

    // Fetch leaderboard
    $.ajax({
        url: nctb_ajax_url,
        method: 'POST',
        data: {
            action: 'nctb_get_leaderboard',
            nonce: nctb_ajax_nonce
        },
        success: function(response) {
            if (response.success) {
                var leaderboard = response.data;
                if (leaderboard.length === 0) {
                    $('#leaderboard-content').html('<p>No data available.</p>');
                } else {
                    var leaderboardHTML = '';
                    $.each(leaderboard, function(index, entry) {
                        var rank = index + 1;
                        leaderboardHTML += '<li>';
                        leaderboardHTML += '<span class="nctb-leaderboard-rank">#' + rank + ' ' + entry.display_name + '</span>';
                        leaderboardHTML += '<span class="nctb-leaderboard-points">' + entry.points + ' points</span>';
                        leaderboardHTML += '</li>';
                    });
                    $('#leaderboard-content').html('<ul>' + leaderboardHTML + '</ul>');
                }
            } else {
                $('#leaderboard-content').html('<p>Error loading leaderboard.</p>');
            }
        },
        error: function() {
            $('#leaderboard-content').html('<p>Error loading leaderboard.</p>');
        }
    });
});
</script>

<?php get_footer(); ?>