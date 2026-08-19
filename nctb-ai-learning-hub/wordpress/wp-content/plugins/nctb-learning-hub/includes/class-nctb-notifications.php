<?php
/**
 * Transactional Notifications & Email Service (Phase 15).
 *
 * Sends responsive, bilingual HTML transactional emails for onboarding welcome,
 * purchase receipts (bKash/Nagad/SSLCommerz), and spaced revision study reminders.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Notifications
 */
class NCTB_Notifications {

	/**
	 * Send welcome email on student onboarding completion.
	 *
	 * @param int $user_id User ID.
	 * @return bool Whether the email was sent successfully.
	 */
	public static function send_welcome_email( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user || empty( $user->user_email ) ) {
			return false;
		}

		$to      = $user->user_email;
		$name    = ! empty( $user->display_name ) ? $user->display_name : 'শিক্ষার্থী';
		$subject = sprintf( 'স্বাগতম %s! আপনার NCTB AI লার্নিং যাত্রা শুরু হলো 🚀', get_bloginfo( 'name' ) );

		$dashboard_url = home_url( '/dashboard/' );
		$browse_url    = home_url( '/books/' );

		$body = self::render_email_layout(
			'স্বাগতম ' . esc_html( $name ) . '!',
			'আপনার NCTB AI Learning Hub অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে। এখন থেকে আপনি আপনার ক্লাসের সব বইয়ের অধ্যায় পড়তে পারবেন, ইন্টারঅ্যাক্টিভ কুইজ প্র্যাকটিস করতে পারবেন এবং এআই টিউটরের সাহায্য নিতে পারবেন।',
			array(
				array( 'label' => '📚 পড়াশোনা শুরু করুন', 'url' => $dashboard_url ),
				array( 'label' => '📖 পাঠ্যবইসমূহ দেখুন', 'url' => $browse_url ),
			)
		);

		return self::send_html_mail( $to, $subject, $body );
	}

	/**
	 * Send purchase confirmation receipt.
	 *
	 * @param int                  $user_id       User ID.
	 * @param int                  $order_id      WooCommerce Order ID.
	 * @param array<int,array>     $granted_items Granted entitlement summary.
	 * @param float|string         $total_amount  Order total.
	 * @return bool
	 */
	public static function send_purchase_receipt( $user_id, $order_id, array $granted_items, $total_amount ) {
		$user = get_userdata( $user_id );
		if ( ! $user || empty( $user->user_email ) ) {
			return false;
		}

		$to      = $user->user_email;
		$subject = sprintf( 'পেমেন্ট নিশ্চিতকরণ ও অ্যাক্সেস রসিদ — অর্ডার #%d', $order_id );

		$items_html = '<ul style="margin:16px 0;padding-left:20px;line-height:1.8;">';
		foreach ( $granted_items as $item ) {
			$exp_text = ! empty( $item['expires_at'] ) ? ' (মেয়াদ: ' . esc_html( $item['expires_at'] ) . ')' : ' (আজীবন অ্যাক্সেস)';
			$items_html .= '<li><strong>' . esc_html( $item['product_name'] ) . '</strong>' . $exp_text . '</li>';
		}
		$items_html .= '</ul>';

		$content = '<p>আপনার অর্ডার #<strong>' . (int) $order_id . '</strong> সফলভাবে সম্পন্ন হয়েছে। মোট পরিশোধিত অর্থ: <strong>৳ ' . esc_html( (string) $total_amount ) . '</strong>।</p>' .
			'<p><strong>আনলককৃত সুবিধা ও কনটেন্ট:</strong></p>' . $items_html .
			'<p>আপনার আনলককৃত পাঠ ও সুবিধাসমূহ এখনই আপনার স্টুডেন্ট ড্যাশবোর্ডে উপলব্ধ।</p>';

		$body = self::render_email_layout(
			'পেমেন্ট সফল হয়েছে! 🎉',
			$content,
			array(
				array( 'label' => '🚀 ড্যাশবোর্ডে যান', 'url' => home_url( '/dashboard/' ) ),
				array( 'label' => '💳 আমার ক্রয়সমূহ', 'url' => home_url( '/purchases/' ) ),
			)
		);

		return self::send_html_mail( $to, $subject, $body );
	}

	/**
	 * Send spaced repetition due review reminder.
	 *
	 * @param int $user_id User ID.
	 * @param int $due_count Total reviews due.
	 * @return bool
	 */
	public static function send_revision_reminder( $user_id, $due_count ) {
		$user = get_userdata( $user_id );
		if ( ! $user || empty( $user->user_email ) || $due_count <= 0 ) {
			return false;
		}

		$to      = $user->user_email;
		$name    = ! empty( $user->display_name ) ? $user->display_name : 'শিক্ষার্থী';
		$subject = sprintf( '⏰ আজকের রিভিশন বাকি আছে (%d টি টপিক) — %s', $due_count, get_bloginfo( 'name' ) );

		$content = '<p>প্রিয় ' . esc_html( $name ) . ',</p>' .
			'<p>স্পেসড রিপিটেশন পদ্ধতি অনুযায়ী আপনার স্মৃতিতে পড়াগুলো দীর্ঘস্থায়ী রাখতে আজ <strong>' . (int) $due_count . ' টি টপিক</strong> রিভিশন দেওয়া প্রয়োজন।</p>' .
			'<p>মাত্র ৫-১০ মিনিট সময় নিয়ে আজকের রিভিশন পর্বটি শেষ করে নিন।</p>';

		$body = self::render_email_layout(
			'আজকের রিভিশন স্মরণিকা 🧠',
			$content,
			array(
				array( 'label' => '⚡ রিভিশন শুরু করুন', 'url' => home_url( '/revision/' ) ),
			)
		);

		return self::send_html_mail( $to, $subject, $body );
	}

	/**
	 * Send an HTML email with proper headers.
	 *
	 * @param string $to Recipient.
	 * @param string $subject Subject.
	 * @param string $html_body HTML Message.
	 * @return bool
	 */
	protected static function send_html_mail( $to, $subject, $html_body ) {
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_bloginfo( 'name' ) . ' <no-reply@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
		);

		return wp_mail( $to, $subject, $html_body, $headers );
	}

	/**
	 * Render responsive HTML email template layout.
	 *
	 * @param string               $heading Heading title.
	 * @param string               $content HTML Content.
	 * @param array<int,array>     $buttons Array of ['label' => string, 'url' => string].
	 * @return string HTML email.
	 */
	protected static function render_email_layout( $heading, $content, array $buttons = array() ) {
		$site_name = get_bloginfo( 'name' );
		$site_url  = home_url( '/' );

		$buttons_html = '';
		if ( ! empty( $buttons ) ) {
			$buttons_html .= '<div style="margin:28px 0 10px;text-align:center;">';
			foreach ( $buttons as $b ) {
				$buttons_html .= '<a href="' . esc_url( $b['url'] ) . '" style="display:inline-block;padding:12px 24px;margin:4px 8px;background:#1E6F5C;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;font-size:15px;">' . esc_html( $b['label'] ) . '</a>';
			}
			$buttons_html .= '</div>';
		}

		return '<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . esc_html( $heading ) . '</title>
</head>
<body style="margin:0;padding:24px 0;background-color:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;color:#14201c;">
<div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
  <div style="background:#1E6F5C;padding:24px;text-align:center;">
    <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:700;">' . esc_html( $site_name ) . '</h1>
    <p style="color:#e4f1ec;margin:4px 0 0;font-size:14px;">ডিজিটাল পাঠ্যবই ও এআই লার্নিং হাব</p>
  </div>
  <div style="padding:32px 24px;line-height:1.7;font-size:16px;">
    <h2 style="color:#14201c;margin-top:0;font-size:20px;">' . esc_html( $heading ) . '</h2>
    ' . $content . '
    ' . $buttons_html . '
  </div>
  <div style="background:#f8fafc;padding:16px 24px;text-align:center;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b;">
    <p style="margin:0;">© ' . gmdate( 'Y' ) . ' ' . esc_html( $site_name ) . ' · সর্বস্বত্ব সংরক্ষিত</p>
    <p style="margin:4px 0 0;"><a href="' . esc_url( $site_url ) . '" style="color:#1E6F5C;text-decoration:none;">ওয়েবসাইট ভিজিট করুন</a> · <a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '" style="color:#1E6F5C;text-decoration:none;">গোপনীয়তা নীতি</a></p>
  </div>
</div>
</body>
</html>';
	}
}
