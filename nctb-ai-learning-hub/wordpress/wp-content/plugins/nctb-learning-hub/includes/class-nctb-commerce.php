<?php
/**
 * WooCommerce Integration & Commerce Mapping Helper (Phase 8).
 *
 * Connects WooCommerce product purchases to NCTB learning entitlements.
 * Listens for completed orders and invokes NCTB_Entitlements::grant_entitlement()
 * with complete server-side verification.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Commerce
 */
class NCTB_Commerce {

	const META_ENTITLEMENT_TYPE = '_nctb_entitlement_type';
	const META_ITEM_TYPE        = '_nctb_item_type';
	const META_ITEM_ID          = '_nctb_item_id';
	const META_DURATION_DAYS    = '_nctb_duration_days';

	/**
	 * Wire commerce hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'handle_order_completed' ) );
	}

	/**
	 * Handle completed WooCommerce order to grant entitlements.
	 *
	 * @param int $order_id WooCommerce Order ID.
	 * @return void
	 */
	public static function handle_order_completed( $order_id ) {
		if ( ! function_exists( 'wc_get_order' ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$user_id = $order->get_user_id();
		if ( ! $user_id ) {
			// If guest checkout, try matching billing email
			$billing_email = $order->get_billing_email();
			$user          = get_user_by( 'email', $billing_email );
			$user_id       = $user ? $user->ID : 0;
		}

		if ( ! $user_id ) {
			NCTB_Logger::warning( 'Could not grant entitlement: no user found for order', array( 'order_id' => $order_id ) );
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$product_id = $item->get_product_id();
			$ent_type   = get_post_meta( $product_id, self::META_ENTITLEMENT_TYPE, true );
			$item_type  = get_post_meta( $product_id, self::META_ITEM_TYPE, true ) ?: 'lesson';
			$item_id    = (int) get_post_meta( $product_id, self::META_ITEM_ID, true );
			$duration   = (int) get_post_meta( $product_id, self::META_DURATION_DAYS, true );

			if ( ! $ent_type ) {
				continue;
			}

			$expires_at = null;
			if ( $duration > 0 ) {
				$expires_at = gmdate( 'Y-m-d H:i:s', strtotime( "+{$duration} days", current_time( 'timestamp', true ) ) );
			}

			$notes = sprintf( 'Purchased via WooCommerce Order #%d', $order_id );

			NCTB_Entitlements::grant_entitlement(
				$user_id,
				$ent_type,
				$item_type,
				$item_id,
				'woocommerce',
				(string) $order_id,
				0,
				$expires_at,
				$notes
			);

			NCTB_Logger::info(
				'Granted entitlement from WooCommerce order',
				array(
					'user_id'    => $user_id,
					'order_id'   => $order_id,
					'product_id' => $product_id,
					'ent_type'   => $ent_type,
				)
			);
		}
	}

	/**
	 * Render Paywall Banner when a lesson is locked.
	 *
	 * @param int $lesson_id Target lesson ID.
	 * @return string HTML paywall banner.
	 */
	public static function render_paywall_card( $lesson_id ) {
		$lesson_id   = absint( $lesson_id );
		$lesson      = get_post( $lesson_id );
		$login_url   = wp_login_url( get_permalink( $lesson_id ) );
		$is_logged_in = is_user_logged_in();

		ob_start();
		?>
		<div class="nctb-paywall-card">
			<div class="paywall-badge">🔒 <?php esc_html_e( 'প্রিমিয়াম কনটেন্ট (Premium Lesson)', 'nctb-learning-hub' ); ?></div>
			<h2><?php esc_html_e( 'এই পাঠটিতে অ্যাক্সেস করতে পাস প্রয়োজন', 'nctb-learning-hub' ); ?></h2>
			<p class="paywall-desc"><?php esc_html_e( 'পাঠের ১৪টি ইন্টারঅ্যাক্টিভ অ্যাক্টিভিটি, আনলিমিটেড কুইজ প্র্যাকটিস এবং এআই টিউটর সহায়তা আনলক করুন।', 'nctb-learning-hub' ); ?></p>

			<div class="paywall-options-grid">
				<div class="paywall-opt-box">
					<div class="opt-tag">একক পাঠ (Single Lesson)</div>
					<div class="opt-price">৳ ২০ <span class="opt-term">/ আজীবন</span></div>
					<p class="opt-sub">শুধুমাত্র এই লেসনের সম্পূর্ণ অ্যাক্সেস</p>
					<?php if ( $is_logged_in ) : ?>
						<button type="button" class="nctb-btn nctb-btn-primary btn-purchase-pass" data-type="direct_lesson" data-item-id="<?php echo esc_attr( $lesson_id ); ?>">
							💳 <?php esc_html_e( 'এখনই কিনুন (৳২০)', 'nctb-learning-hub' ); ?>
						</button>
					<?php else : ?>
						<a href="<?php echo esc_url( $login_url ); ?>" class="nctb-btn nctb-btn-primary">
							🔑 <?php esc_html_e( 'লগইন করে কিনুন', 'nctb-learning-hub' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<div class="paywall-opt-box featured">
					<div class="opt-tag highlight">সব পাঠ্যবই (All-Access Pass)</div>
					<div class="opt-price">৳ ২৯৯ <span class="opt-term">/ মাস</span></div>
					<p class="opt-sub">সব বই, ইউনিট, প্র্যাকটিস ইঞ্জিন ও এআই টিউটর আনলিমিটেড</p>
					<?php if ( $is_logged_in ) : ?>
						<button type="button" class="nctb-btn nctb-btn-success btn-purchase-pass" data-type="subscription" data-item-id="0">
							⚡ <?php esc_html_e( 'অল-অ্যাক্সেস পাস নিন (৳২৯৯)', 'nctb-learning-hub' ); ?>
						</button>
					<?php else : ?>
						<a href="<?php echo esc_url( $login_url ); ?>" class="nctb-btn nctb-btn-success">
							🔑 <?php esc_html_e( 'লগইন করে পাস নিন', 'nctb-learning-hub' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
