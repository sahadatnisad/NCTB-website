<?php
/**
 * Archive Template for Video Modules & Courses (Phase 17).
 *
 * Displays courses directory with filter tabs for Students, Teachers, and Subjects.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$user_id  = get_current_user_id();
$audience = isset( $_GET['audience'] ) ? sanitize_key( $_GET['audience'] ) : '';

$args = array(
	'post_type'      => NCTB_Module_CPT::POST_TYPE,
	'post_status'    => 'publish',
	'posts_per_page' => 20,
);

if ( ! empty( $audience ) ) {
	$args['meta_query'] = array(
		array(
			'key'     => NCTB_Module_CPT::META_AUDIENCE,
			'value'   => array( $audience, 'both' ),
			'compare' => 'IN',
		),
	);
}

$modules_query = new WP_Query( $args );
?>

<div class="nctb-mkt">
	<section class="mkt-hero">
		<div class="mkt-wrap">
			<span class="mkt-eyebrow"><?php esc_html_e( 'Video Courses & Masterclasses', 'nctb-theme' ); ?></span>
			<h1><?php esc_html_e( 'ভিডিও কোর্স ও মাস্টারক্লাস হাব', 'nctb-theme' ); ?></h1>
			<p class="mkt-lead"><?php esc_html_e( 'শিক্ষার্থীদের ব্যাকরণ ও ব্যবহারিক দক্ষতা এবং শিক্ষকদের জন্য ক্লাসরুম পাঠদান নির্দেশিকা।', 'nctb-theme' ); ?></p>

			<div class="module-filter-pills">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_module' ) ); ?>" class="filter-pill <?php echo empty( $audience ) ? 'active' : ''; ?>">সকল কোর্স</a>
				<a href="<?php echo esc_url( add_query_arg( 'audience', 'student', get_post_type_archive_link( 'nctb_module' ) ) ); ?>" class="filter-pill <?php echo 'student' === $audience ? 'active' : ''; ?>">👨‍🎓 শিক্ষার্থীদের কোর্স</a>
				<a href="<?php echo esc_url( add_query_arg( 'audience', 'teacher', get_post_type_archive_link( 'nctb_module' ) ) ); ?>" class="filter-pill <?php echo 'teacher' === $audience ? 'active' : ''; ?>">🎓 শিক্ষক প্রশিক্ষণ</a>
			</div>
		</div>
	</section>

	<section class="mkt-section">
		<div class="mkt-wrap">
			<?php if ( $modules_query->have_posts() ) : ?>
				<div class="modules-cards-grid">
					<?php
					while ( $modules_query->have_posts() ) :
						$modules_query->the_post();
						$mod_id = get_the_ID();
						$mod    = NCTB_Module_Service::get_module( $mod_id, $user_id );
						?>
						<article class="module-card">
							<div class="module-card-badge-row">
								<span class="badge-audience <?php echo esc_attr( $mod['audience'] ); ?>">
									<?php echo 'teacher' === $mod['audience'] ? '🎓 শিক্ষক' : '👨‍🎓 শিক্ষার্থী'; ?>
								</span>
								<span class="badge-duration">⏳ <?php echo esc_html( $mod['duration'] ); ?></span>
							</div>

							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="module-card-desc"><?php echo esc_html( $mod['excerpt'] ?: wp_trim_words( get_the_content(), 18 ) ); ?></p>

							<div class="module-card-footer">
								<span class="instructor-tag">👤 <?php echo esc_html( $mod['instructor'] ?: 'NCTB Hub' ); ?></span>
								<a href="<?php the_permalink(); ?>" class="nctb-btn nctb-btn-sm nctb-btn-primary">ভিডিও দেখুন ➔</a>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else : ?>
				<div class="nctb-empty-state">
					<p>কোনো কোর্স পাওয়া যায়নি।</p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</div>

<?php
get_footer();
