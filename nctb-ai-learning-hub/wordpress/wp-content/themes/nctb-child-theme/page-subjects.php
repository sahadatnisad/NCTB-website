<?php
/**
 * Template Name: Subjects Page
 * Template Post Type: page
 */

get_header(); ?>

<main>
    <div class="container">
        <section class="hero">
            <h1>Subjects</h1>
            <p>Explore our curriculum offerings</p>
        </section>
        <section class="card-grid">
            <div class="card">
                <h2>English</h2>
                <p>Comprehensive English language learning</p>
                <a href="<?php echo home_url('/lessons/english'); ?>" class="button">View Lessons</a>
            </div>
            <div class="card">
                <h2>Mathematics</h2>
                <p>Problem-solving and practice exercises</p>
                <a href="<?php echo home_url('/lessons/mathematics'); ?>" class="button">View Lessons</a>
            </div>
            <div class="card">
                <h2>Science</h2>
                <p>Interactive science concepts</p>
                <a href="<?php echo home_url('/lessons/science'); ?>" class="button">View Lessons</a>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();
?>