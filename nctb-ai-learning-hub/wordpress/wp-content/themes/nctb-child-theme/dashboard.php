<?php
/**
 * Template Name: Dashboard
 * Template Post Type: page
 */

get_header(); ?>

<main>
    <div class="container">
        <div class="dashboard-header">
            <h1><?php esc_html_e('Welcome to Your Learning Hub', 'nctb-learning-hub'); ?></h1>
            <p><?php esc_html_e('Track your progress and explore curriculum', 'nctb-learning-hub'); ?></p>
        </div>

        <div class="dashboard-grid">
            <!-- Sidebar Navigation -->
            <aside class="dashboard-sidebar">
                <nav class="dashboard-nav">
                    <ul>
                        <li><a href="#lessons" class="active">My Lessons</a></li>
                        <li><a href="#subjects">Subjects</a></li>
                        <li><a href="#progress">Progress</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <section class="dashboard-content">
                <div id="lessons" class="dashboard-section">
                    <h2><?php esc_html_e('My Lessons', 'nctb-learning-hub'); ?></h2>
                    <div class="lessons-grid">
    <?php
    // Add enrollment button to each lesson card
    $enrollment_button = '';
    if (current_user_can('read')) {
        $enrollment_status = get_user_meta(get_current_user_id(), 'nctb_enrolled_lessons', true);
        $enrolled_array = is_array($enrollment_status) ? $enrollment_status : array();
        $is_enrolled = in_array($lesson_id, $enrolled_array);
        $button_text = $is_enrolled ? esc_html__('Unenroll', 'nctb-learning-hub') : esc_html__('Enroll', 'nctb-learning-hub');
        $button_class = $is_enrolled ? 'enrolled' : '';
        $enrollment_button = '<button class="button-small enrollment-toggle ' . $button_class . '" data-lesson-id="' . $lesson_id . '" data-action="' . ($is_enrolled ? 'unenroll' : 'enroll') . '">' . $button_text . '</button>';
    }
    ?>
    <div class="lesson-card" data-lesson-id="<?php echo $lesson_id; ?>">
        <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()) {
                the_post_thumbnail('thumbnail', array('class' => 'lesson-thumb'));
            } ?>
            <div class="lesson-info">
                <h3><?php the_title(); ?></h3>
                <p class="lesson-meta">Subject: <?php echo get_the_term_list($lesson_id, 'subject', '', ', '); ?></p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <span class="progress-text"><?php echo $percentage > 0 ? $percentage . '%' : 'Not started'; ?></span>
            </div>
        </a>
        <div class="lesson-actions">
            <?php echo $enrollment_button; ?>
        </div>
    </div>
    <?php }
    ?>
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
                                            <p class="lesson-meta">Subject: <?php echo get_the_term_list($lesson_id, 'subject', '', ', '); ?></p>
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
                        <?php } ?>
                    </div>
                </div>

                <div id="progress" class="dashboard-section">
                    <h2>Learning Progress</h2>
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
        </div>
    </div>
</main>

<?php get_footer(); ?>