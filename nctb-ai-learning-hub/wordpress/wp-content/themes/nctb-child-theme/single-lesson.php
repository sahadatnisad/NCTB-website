<?php
/**
 * Template Name: Single Lesson
 * Template Post Type: lesson
 */

get_header(); ?>

<main>
    <div class="container">
        <nav class="breadcrumb-nav" style="margin-bottom: 2rem;">
            <a href="<?php echo home_url('/dashboard'); ?>">Dashboard</a> &rarr; <span><?php the_title(); ?></span>
        </nav>

        <div class="lesson-detail">
            <div class="lesson-header">
                <h1><?php the_title(); ?></h1>
                <div class="lesson-meta">
                    <span>Subject: <?php echo get_the_term_list(get_the_ID(), 'subject', '', ', '); ?></span>
                    <span>Date: <?php echo get_the_date('F j, Y'); ?></span>
                </div>
            </div>

            <div class="lesson-content">
                <?php if (has_post_thumbnail()) {
                    the_post_thumbnail('full', array('class' => 'lesson-featured-image'));
                } ?>
                <div class="lesson-body">
                    <?php the_content(); ?>
                </div>
            </div>

            <div class="lesson-progress-section">
                <h2>Track Your Progress</h2>
                <div class="progress-container">
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar-bg"></div>
                        <div class="progress-bar-fill" id="progress-fill"></div>
                    </div>
                    <div class="progress-percentage" id="progress-percentage">0%</div>
                </div>
                <div class="progress-buttons">
                    <button class="progress-btn" onclick="updateProgress(25)">25%</button>
                    <button class="progress-btn" onclick="updateProgress(50)">50%</button>
                    <button class="progress-btn" onclick="updateProgress(75)">75%</button>
                    <button class="progress-btn" onclick="updateProgress(100)">100%</button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>