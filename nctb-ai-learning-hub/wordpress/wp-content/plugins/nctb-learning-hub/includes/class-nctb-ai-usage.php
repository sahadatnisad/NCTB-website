<?php
/**
 * AI Usage Quota & Conversation Logger (Phase 9).
 *
 * Tracks per-student daily AI request quotas, token consumption, and maintains
 * a privacy-minimized interaction log without collecting unnecessary PII.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_AI_Usage
 */
class NCTB_AI_Usage {

	const FREE_DAILY_LIMIT       = 50;
	const SUBSCRIBED_DAILY_LIMIT = 200;

	/**
	 * Check if a student is within their daily AI quota.
	 *
	 * @param int $user_id Student User ID.
	 * @return array<string,mixed> Quota status array.
	 */
	public static function check_daily_quota( $user_id ) {
		global $wpdb;
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return array(
				'allowed'   => true,
				'used'      => 0,
				'limit'     => self::FREE_DAILY_LIMIT,
				'remaining' => self::FREE_DAILY_LIMIT,
			);
		}

		// Check subscription status
		$has_sub = false;
		if ( class_exists( 'NCTB_Entitlements' ) ) {
			$ents = NCTB_Entitlements::get_user_entitlements( $user_id );
			foreach ( $ents as $e ) {
				if ( 'subscription' === $e->entitlement_type || 'all_access' === $e->item_type ) {
					$has_sub = true;
					break;
				}
			}
		}

		$limit = $has_sub ? self::SUBSCRIBED_DAILY_LIMIT : self::FREE_DAILY_LIMIT;
		$table = NCTB_Migrations::table( 'ai_usage' );
		$today = current_time( 'Y-m-d' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$usage_row = $wpdb->get_row(
			$wpdb->prepare( "SELECT request_count FROM {$table} WHERE user_id = %d AND usage_date = %s", $user_id, $today )
		);

		$used = $usage_row ? (int) $usage_row->request_count : 0;
		$rem  = max( 0, $limit - $used );

		return array(
			'allowed'   => ( $used < $limit ),
			'used'      => $used,
			'limit'     => $limit,
			'remaining' => $rem,
		);
	}

	/**
	 * Record an AI interaction and update usage counters.
	 *
	 * @param int    $user_id     Student User ID.
	 * @param int    $lesson_id   Lesson Post ID.
	 * @param string $action_type Action type (explain, bangla, hint, example, why_wrong, free_chat).
	 * @param string $prompt      User prompt.
	 * @param string $response    AI response.
	 * @param string $provider    AI provider identifier.
	 * @param int    $tokens      Tokens consumed.
	 * @return void
	 */
	public static function record_interaction( $user_id, $lesson_id, $action_type, $prompt, $response, $provider = 'mock', $tokens = 100 ) {
		global $wpdb;
		$user_id     = absint( $user_id );
		$lesson_id   = absint( $lesson_id );
		$tokens      = max( 1, absint( $tokens ) );
		$action_type = sanitize_key( $action_type ) ?: 'explain';
		$provider    = sanitize_key( $provider ) ?: 'mock';

		$conv_table  = NCTB_Migrations::table( 'ai_conversations' );
		$usage_table = NCTB_Migrations::table( 'ai_usage' );
		$now         = current_time( 'mysql', true );
		$today       = current_time( 'Y-m-d' );

		// 1. Log conversation
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->insert(
			$conv_table,
			array(
				'user_id'     => $user_id,
				'lesson_id'   => $lesson_id,
				'action_type' => $action_type,
				'user_prompt' => sanitize_textarea_field( $prompt ),
				'ai_response' => wp_kses_post( $response ),
				'provider'    => $provider,
				'tokens_used' => $tokens,
				'created_at'  => $now,
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s' )
		);

		// 2. Upsert daily usage
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$existing_usage = $wpdb->get_row(
			$wpdb->prepare( "SELECT id, request_count, prompt_tokens, completion_tokens FROM {$usage_table} WHERE user_id = %d AND usage_date = %s", $user_id, $today )
		);

		if ( $existing_usage ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update(
				$usage_table,
				array(
					'request_count'     => (int) $existing_usage->request_count + 1,
					'prompt_tokens'     => (int) $existing_usage->prompt_tokens + round( $tokens * 0.4 ),
					'completion_tokens' => (int) $existing_usage->completion_tokens + round( $tokens * 0.6 ),
					'last_request_at'   => $now,
				),
				array( 'id' => $existing_usage->id ),
				array( '%d', '%d', '%d', '%s' ),
				array( '%d' )
			);
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$usage_table,
				array(
					'user_id'           => $user_id,
					'usage_date'        => $today,
					'request_count'     => 1,
					'prompt_tokens'     => round( $tokens * 0.4 ),
					'completion_tokens' => round( $tokens * 0.6 ),
					'last_request_at'   => $now,
				),
				array( '%d', '%s', '%d', '%d', '%d', '%s' )
			);
		}
	}

	/**
	 * Get recent conversation history for a student and lesson.
	 *
	 * @param int $user_id   Student User ID.
	 * @param int $lesson_id Lesson Post ID.
	 * @param int $limit     Max items.
	 * @return array<int,object>
	 */
	public static function get_recent_history( $user_id, $lesson_id, $limit = 20 ) {
		global $wpdb;
		$user_id   = absint( $user_id );
		$lesson_id = absint( $lesson_id );
		$limit     = absint( $limit );

		if ( ! $user_id ) {
			return array();
		}

		$table = NCTB_Migrations::table( 'ai_conversations' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, action_type, user_prompt, ai_response, created_at 
				FROM {$table} 
				WHERE user_id = %d AND (lesson_id = %d OR lesson_id = 0) 
				ORDER BY id ASC 
				LIMIT %d",
				$user_id,
				$lesson_id,
				$limit
			)
		);
	}
}
