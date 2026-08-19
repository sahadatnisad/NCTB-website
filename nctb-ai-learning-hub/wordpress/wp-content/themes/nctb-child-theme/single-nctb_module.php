<?php
/**
 * Single Course Module & Video Player Template (Phase 17).
 *
 * Renders structured lecture series with YouTube low-bandwidth facade,
 * interactive lecture playlist checklist sidebar, and progress persistence.
 *
 * @package NCTB\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$user_id   = get_current_user_id();
$module_id = get_the_ID();
$module    = NCTB_Module_Service::get_module( $module_id, $user_id );

$items           = $module['items'] ?? array();
$completed_items = $module['completed_items'] ?? array();
$first_item      = ! empty( $items[0] ) ? $items[0] : null;
?>

<div class="nctb-module-player-screen" id="nctb-module-app" data-module-id="<?php echo esc_attr( $module_id ); ?>">
	<!-- Module Top Header -->
	<header class="module-header-bar">
		<div class="module-header-inner">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'nctb_module' ) ); ?>" class="back-link">⬅ <?php esc_html_e( 'সকল কোর্স ও মডিউল', 'nctb-theme' ); ?></a>
			<div class="module-meta-row">
				<span class="meta-tag audience-<?php echo esc_attr( $module['audience'] ); ?>">
					<?php echo 'teacher' === $module['audience'] ? '🎓 শিক্ষক প্রশিক্ষণ' : '👨‍🎓 শিক্ষার্থী কোর্স'; ?>
				</span>
				<span class="meta-tag">⏳ <?php echo esc_html( $module['duration'] ); ?></span>
				<span class="meta-tag">📘 <?php echo esc_html( $module['subject'] ); ?></span>
			</div>
			<h1 class="module-main-title"><?php the_title(); ?></h1>
		</div>
	</header>

	<!-- Main Course Player Layout -->
	<div class="module-player-layout">
		<!-- Left: Active Video Stage -->
		<div class="player-stage-column">
			<div class="video-container" id="video-display-area">
				<?php if ( $first_item ) : ?>
					<div class="nctb-youtube-facade" data-video-id="<?php echo esc_attr( $first_item['youtube_id'] ); ?>" data-title="<?php echo esc_attr( $first_item['title'] ); ?>">
						<img src="https://img.youtube.com/vi/<?php echo esc_attr( $first_item['youtube_id'] ); ?>/hqdefault.jpg" alt="<?php echo esc_attr( $first_item['title'] ); ?>" loading="lazy">
						<button type="button" class="nctb-facade-play-btn" aria-label="Play video">
							<svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
						</button>
						<span class="nctb-facade-title"><?php echo esc_html( $first_item['title'] ); ?></span>
					</div>
				<?php else : ?>
					<div class="empty-video-placeholder">ভিডিও লেকচার শীঘ্রই যুক্ত হবে।</div>
				<?php endif; ?>
			</div>

			<div class="active-lecture-info">
				<div class="lecture-title-row">
					<h2 id="active-lecture-title"><?php echo esc_html( $first_item ? $first_item['title'] : get_the_title() ); ?></h2>
					<?php if ( is_user_logged_in() && $first_item ) : ?>
						<button type="button" class="nctb-btn nctb-btn-sm btn-mark-complete" id="btn-toggle-lecture" data-item-id="<?php echo esc_attr( $first_item['id'] ); ?>">
							<?php echo in_array( $first_item['id'], $completed_items, true ) ? '✓ সম্পন্ন হয়েছে' : '○ সম্পন্ন হিসেবে চিহ্নিত করুন'; ?>
						</button>
					<?php endif; ?>
				</div>
				<p id="active-lecture-desc" class="lecture-desc-text">
					<?php echo esc_html( $first_item ? $first_item['description'] : get_the_excerpt() ); ?>
				</p>
			</div>
		</div>

		<!-- Right: Playlist Checklist Sidebar -->
		<aside class="playlist-sidebar-column">
			<div class="playlist-card">
				<div class="playlist-header">
					<h3>📚 কোর্স লেকচার তালিকা</h3>
					<div class="progress-bar-wrap">
						<div class="progress-bar-fill" id="module-progress-fill" style="width: <?php echo esc_attr( $module['progress_percent'] ); ?>%;"></div>
					</div>
					<div class="progress-status-row">
						<span id="progress-percent-lbl"><?php echo esc_html( $module['progress_percent'] ); ?>% সম্পন্ন</span>
						<span><?php echo count( $completed_items ); ?> / <?php echo count( $items ); ?> লেকচার</span>
					</div>
				</div>

				<div class="playlist-items-list" id="playlist-container">
					<?php foreach ( $items as $idx => $it ) : ?>
						<?php
						$is_done   = in_array( $it['id'], $completed_items, true );
						$is_active = ( 0 === $idx );
						?>
						<div class="playlist-item <?php echo $is_active ? 'active' : ''; ?> <?php echo $is_done ? 'completed' : ''; ?>"
							 data-item-id="<?php echo esc_attr( $it['id'] ); ?>"
							 data-youtube-id="<?php echo esc_attr( $it['youtube_id'] ); ?>"
							 data-title="<?php echo esc_attr( $it['title'] ); ?>"
							 data-desc="<?php echo esc_attr( $it['description'] ?? '' ); ?>">
							<span class="item-checkbox <?php echo $is_done ? 'checked' : ''; ?>">
								<?php echo $is_done ? '✓' : ( $idx + 1 ); ?>
							</span>
							<div class="item-details">
								<h4><?php echo esc_html( $it['title'] ); ?></h4>
								<span class="item-duration">⏳ <?php echo esc_html( $it['duration'] ); ?></span>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</aside>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const moduleId = <?php echo (int) $module_id; ?>;
	const nonce = '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>';
	const toggleUrl = '<?php echo esc_url_raw( rest_url( "nctb/v1/modules/{$module_id}/toggle-item" ) ); ?>';

	const videoArea = document.getElementById('video-display-area');
	const activeTitle = document.getElementById('active-lecture-title');
	const activeDesc = document.getElementById('active-lecture-desc');
	const toggleBtn = document.getElementById('btn-toggle-lecture');
	const progressFill = document.getElementById('module-progress-fill');
	const progressLbl = document.getElementById('progress-percent-lbl');

	let activeItemId = '<?php echo esc_js( $first_item ? $first_item['id'] : '' ); ?>';

	function playLecture(itemEl) {
		document.querySelectorAll('.playlist-item').forEach(el => el.classList.remove('active'));
		itemEl.classList.add('active');

		const ytid = itemEl.getAttribute('data-youtube-id');
		const title = itemEl.getAttribute('data-title');
		const desc = itemEl.getAttribute('data-desc');
		activeItemId = itemEl.getAttribute('data-item-id');

		activeTitle.textContent = title;
		activeDesc.textContent = desc;

		videoArea.innerHTML = `
			<div class="nctb-youtube-facade" data-video-id="${ytid}" data-title="${title}">
				<img src="https://img.youtube.com/vi/${ytid}/hqdefault.jpg" alt="${title}" loading="lazy">
				<button type="button" class="nctb-facade-play-btn" aria-label="Play video">
					<svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
				</button>
				<span class="nctb-facade-title">${title}</span>
			</div>
		`;

		if (window.initYouTubeFacades) {
			window.initYouTubeFacades();
		}

		if (toggleBtn) {
			toggleBtn.setAttribute('data-item-id', activeItemId);
			const isDone = itemEl.classList.contains('completed');
			toggleBtn.textContent = isDone ? '✓ সম্পন্ন হয়েছে' : '○ সম্পন্ন হিসেবে চিহ্নিত করুন';
		}
	}

	document.querySelectorAll('.playlist-item').forEach(el => {
		el.addEventListener('click', function() {
			playLecture(this);
		});
	});

	if (toggleBtn) {
		toggleBtn.addEventListener('click', function() {
			const itemId = this.getAttribute('data-item-id');
			const activeItemEl = document.querySelector(`.playlist-item[data-item-id="${itemId}"]`);
			const isDone = activeItemEl.classList.contains('completed');
			const newStatus = !isDone;

			fetch(toggleUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
				body: JSON.stringify({ item_id: itemId, completed: newStatus })
			}).then(r => r.json()).then(res => {
				if (res.success) {
					activeItemEl.classList.toggle('completed', newStatus);
					const checkEl = activeItemEl.querySelector('.item-checkbox');
					if (checkEl) checkEl.classList.toggle('checked', newStatus);
					toggleBtn.textContent = newStatus ? '✓ সম্পন্ন হয়েছে' : '○ সম্পন্ন হিসেবে চিহ্নিত করুন';
					if (progressFill) progressFill.style.width = res.progress_percent + '%';
					if (progressLbl) progressLbl.textContent = res.progress_percent + '% সম্পন্ন';
				}
			});
		});
	}
});
</script>

<?php
get_footer();
