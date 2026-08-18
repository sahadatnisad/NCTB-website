<?php
/**
 * Template Name: Subject
 * Template Post Type: subject
 */

get_header(); ?>

<main>
    <div class="container">
        <nav class="breadcrumb-nav" style="margin-bottom: 2rem;">
            <a href="<?php echo home_url('/dashboard'); ?>">Dashboard</a> &rarr; <span>Subjects</span>
        </nav>

        <div class="subject-header">
            <h1><?php single_term_title(); ?></h1>
            <p><?php echo term_description(); ?></p>
        </div>

        <div class="lessons-grid">
            <?php
            $args = array(
                'post_type' => 'lesson',
                'posts_per_page' => 12,
                'orderby' => 'date',
                'order' => 'DESC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'subject',
                        'field' => 'slug',
                        'terms' => single_term_slug(),
                    ),
                ),
            );
            $lessons_query = new WP_Query($args);
            if ($lessons_query->have_posts()) {
                while ($lessons_query->have_posts()) {
                    $lessons_query->have_posts();
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
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <span class="progress-text"><?php echo $percentage > 0 ? $percentage . '%' : 'Not started'; ?></span>
                            </div>
                        </a>
                    </div>
                <?php }
            } else {
                echo '<p>No lessons found in this subject.</p>';
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>