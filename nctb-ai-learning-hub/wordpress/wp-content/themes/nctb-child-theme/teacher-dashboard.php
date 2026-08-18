<?php
/**
 * Template Name: Teacher Dashboard
 * Template Post Type: page
 */

get_header(); ?>

<main>
    <div class="container">
        <div class="dashboard-header">
            <h1><?php esc_html_e('Teacher Dashboard', 'nctb-learning-hub'); ?></h1>
            <p><?php esc_html_e('Manage your curriculum and track student progress', 'nctb-learning-hub'); ?></p>
        </div>

        <div class="teacher-dashboard-grid">
            <!-- Sidebar Navigation -->
            <aside class="dashboard-sidebar">
                <nav class="dashboard-nav">
                    <ul>
                        <li><a href="#curriculum" class="active">My Curriculum</a></li>
                        <li><a href="#students">Students</a></li>
                        <li><a href="#analytics">Analytics</a></li>
                        <li><a href="/dashboard">Student View</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="teacher-dashboard-header">
                <h1><?php esc_html_e('Curriculum Management', 'nctb-learning-hub'); ?></h1>
                <p><?php esc_html_e('Create and manage your lessons and subjects', 'nctb-learning-hub'); ?></p>
            </div>
            <section class="dashboard-content">
                <div id="curriculum" class="dashboard-section">
                    <h2><?php esc_html_e('My Curriculum', 'nctb-learning-hub'); ?></h2>
                    <div class="curriculum-actions">
    <button class="button-primary" onclick="createNewLesson()">Create New Lesson</button>
    <button class="button-secondary" onclick="manageSubjects()">Manage Subjects</button>
    <button class="button-secondary" onclick="manageEnrollments()">Manage Enrollments</button>
    <button class="button-secondary" onclick="manageSubjects()">Manage Subjects</button>
    <button class="button-secondary" onclick="manageEnrollments()">Manage Enrollments</button>

                        <button class="button-primary" onclick="createNewLesson()">Create New Lesson</button>
    <button class="button-secondary" onclick="manageSubjects()">Manage Subjects</button>
    <button class="button-secondary" onclick="manageEnrollments()">Manage Enrollments</button>
                        <button class="button-secondary" onclick="manageSubjects()">Manage Subjects</button>
                    </div>

                    <div class="lessons-list">
                    <?php
                    // Add enrollment button to each lesson card
                    if (current_user_can('edit_posts')) {
                        $enrollment_button = '<button class="button-small" onclick="enrollAllStudents(this)" data-lesson-id="' . $lesson_id . '">Enroll All</button>';
                    } else {
                        $enrollment_button = '';
                    }
                    ?>
                    <div class="lesson-card">
                        <div class="lesson-info">
                            <h3><?php the_title(); ?></h3>
                            <p class="lesson-meta">Subject: <?php echo get_the_term_list($lesson_id, 'subject', '', ', '); ?></p>
                            <div class="stats">
                                <span>📚 Enrolled: <?php echo $enrollment_count ?: 0; ?></span>
                                <span>✅ Completed: <?php echo $completion_count ?: 0; ?></span>
                                <span>📊 Completion Rate: <?php echo $enrollment_count ? round(($completion_count / $enrollment_count) * 100, 1) : 0; ?>%</span>
                            </div>
                        </div>
                        <div class="lesson-actions">
                            <a href="<?php echo get_edit_post_link($lesson_id); ?>" class="button-small">Edit</a>
                            <a href="<?php echo get_permalink($lesson_id); ?>?preview=teacher" class="button-small">Preview</a>
                            <button class="button-small" onclick="deleteLesson(<?php echo $lesson_id; ?>, this)">Delete</button>
                            <?php echo $enrollment_button; ?>
                        </div>
                    </div>
                    <?php }
                    ?>
                </div>
                        <?php
                        $args = array(
                            'post_type' => 'lesson',
                            'posts_per_page' => 12,
                            'orderby' => 'date',
                            'order' => 'DESC',
                            'author' => get_current_user_id(),
                        );
                        $lessons_query = new WP_Query($args);
                        if ($lessons_query->have_posts()) {
                            while ($lessons_query->have_posts()) {
                                $lessons_query->the_post();
                                $lesson_id = get_the_ID();
                                $enrollment_count = get_post_meta($lesson_id, 'nctb_enrollment_count', true);
                                $completion_count = get_post_meta($lesson_id, 'nctb_completion_count', true);
                                ?>
                                <div class="lesson-card">
                                    <div class="lesson-info">
                                        <h3><?php the_title(); ?></h3>
                                        <p class="lesson-meta">Subject: <?php echo get_the_term_list($lesson_id, 'subject', '', ', '); ?></p>
                                        <div class="stats">
                                            <span>📚 Enrolled: <?php echo $enrollment_count ?: 0; ?></span>
                                            <span>✅ Completed: <?php echo $completion_count ?: 0; ?></span>
                                            <span>📊 Completion Rate: <?php echo $enrollment_count ? round(($completion_count / $enrollment_count) * 100, 1) : 0; ?>%</span>
                                        </div>
                                    </div>
                                    <div class="lesson-actions">
                                        <a href="<?php echo get_edit_post_link($lesson_id); ?>" class="button-small">Edit</a>
                                        <a href="<?php echo get_permalink($lesson_id); ?>?preview=teacher" class="button-small">Preview</a>
                                        <button class="button-small" onclick="deleteLesson(<?php echo $lesson_id; ?>, this)">Delete</button>
                                    </div>
                                </div>
                            <?php }
                        } else {
                            echo '<p>No lessons found.</p>';
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>

                <div id="students" class="dashboard-section">
                    <h2><?php esc_html_e('Students', 'nctb-learning-hub'); ?></h2>
                    <div class="students-grid">
                        <?php
                        $args = array(
                            'role' => 'subscriber',
                            'number' => 20,
                            'orderby' => 'user_registered',
                            'order' => 'DESC',
                        );
                        $students_query = get_users($args);
                        foreach ($students_query as $student) {
                            $enrolled_count = count_user_posts($student->ID, 'lesson');
                            $completed_count = count_user_posts($student->ID, 'lesson', array('meta_key' => 'nctb_lesson_progress', 'meta_value' => 100));
                            ?>
                            <div class="student-card">
                                <div class="student-info">
                                    <h3><?php echo $student->display_name; ?></h3>
                                    <p><?php echo $student->user_email; ?></p>
                                    <div class="stats">
                                        <span>📚 Lessons Enrolled: <?php echo $enrolled_count; ?></span>
                                        <span>✅ Completed: <?php echo $completed_count; ?></span>
                                        <span>📊 Progress: <?php echo $enrolled_count ? round(($completed_count / $enrolled_count) * 100, 1) : 0; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div id="analytics" class="dashboard-section">
                    <h2>Analytics</h2>
                    <div class="analytics-grid">
                        <div class="analytics-card">
                            <h3>Total Lessons</h3>
                            <div class="big-number"><?php echo wp_count_posts('lesson')->publish; ?></div>
                        </div>
                        <div class="analytics-card">
                            <h3>Total Students</h3>
                            <div class="big-number"><?php echo count_users()['total']; ?></div>
                        </div>
                        <div class="analytics-card">
                            <h3>Active Students</h3>
                            <div class="big-number"><?php
                            $args = array(
                                'role' => 'subscriber',
                                'meta_query' => array(
                                    array(
                                        'key' => 'nctb_last_activity',
                                        'value' => strtotime('-30 days'),
                                        'compare' => '>=',
                                        'type' => 'NUMERIC'
                                    )
                                )
                            );
                            $active_students = get_users($args);
                            echo count($active_students);
                            ?></div>
                        </div>
                        <div class="analytics-card">
                            <h3>Avg. Completion Rate</h3>
                            <div class="big-number" id="avg-completion-rate">Loading...</div>
                        </div>
                        <div class="analytics-card">
                            <h3>Lessons This Month</h3>
                            <div class="big-number"><?php
                            $args = array(
                                'post_type' => 'lesson',
                                'posts_per_page' => -1,
                                'date_query' => array(
                                    array(
                                        'month' => date('n'),
                                        'year' => date('Y')
                                    )
                                )
                            );
                            $monthly_lessons = new WP_Query($args);
                            echo $monthly_lessons->found_posts;
                            wp_reset_postdata();
                            ?></div>
                        </div>
                        <!-- Analytics Charts -->
                        <div class="analytics-chart" style="width: 100%; max-width: 800px; margin: 2rem auto;">
                            <canvas id="lesson-completion-chart" height="80"></canvas>
                        </div>
                        <div class="analytics-chart" style="width: 100%; max-width: 400px; margin: 2rem auto;">
                            <canvas id="student-progress-chart" height="80"></canvas>
                        </div>
                        <div class="analytics-chart" style="width: 100%; max-width: 800px; margin: 2rem auto;">
                            <canvas id="enrollment-trends-chart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<?php get_footer(); ?>