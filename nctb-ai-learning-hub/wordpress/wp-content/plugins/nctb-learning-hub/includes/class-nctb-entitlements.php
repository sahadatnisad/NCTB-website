<?php
/**
 * Centralized Entitlement & Access Control Service (Phase 8).
 *
 * Single source of truth for all curriculum and lesson access decisions.
 * Evaluates free status, direct purchases, unit/book packs, all-access
 * subscriptions, admin manual grants, and role overrides with strict audit logging.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Entitlements
 */
class NCTB_Entitlements {

	const TYPE_DIRECT_LESSON = 'direct_lesson';
	const TYPE_PACK_UNIT     = 'pack_unit';
	const TYPE_PACK_BOOK     = 'pack_book';
	const TYPE_FULL_SUBJECT  = 'full_subject';
	const TYPE_SUBSCRIPTION  = 'subscription';
	const TYPE_ADMIN_GRANT   = 'admin_grant';

	const STATUS_ACTIVE  = 'active';
	const STATUS_EXPIRED = 'expired';
	const STATUS_REVOKED = 'revoked';

	/**
	 * Meta key for free lesson flag.
	 */
	const META_IS_FREE = '_nctb_is_free';

	/**
	 * Check if a student is entitled to access a given lesson.
	 *
	 * @param int $user_id   Student User ID (0 for unauthenticated guest).
	 * @param int $lesson_id Lesson Post ID.
	 * @return array<string,mixed> Access decision array: ['granted' => bool, 'reason' => string, 'expires_at' => string|null]
	 */
	public static function can_access_lesson( $user_id, $lesson_id ) {
		$user_id   = absint( $user_id );
		$lesson_id = absint( $lesson_id );

		if ( ! $lesson_id ) {
			return array(
				'granted'    => false,
				'reason'     => 'invalid_lesson',
				'expires_at' => null,
			);
		}

		// 1. Free lesson check (accessible to everyone including guests)
		if ( self::is_lesson_free( $lesson_id ) ) {
			return array(
				'granted'    => true,
				'reason'     => 'free_lesson',
				'expires_at' => null,
			);
		}

		// Guests cannot access paid content
		if ( ! $user_id ) {
			return array(
				'granted'    => false,
				'reason'     => 'auth_required',
				'expires_at' => null,
			);
		}

		// 2. Administrator & Teacher/Editor role override
		if ( user_can( $user_id, 'manage_options' ) || user_can( $user_id, 'edit_posts' ) ) {
			return array(
				'granted'    => true,
				'reason'     => 'admin_role',
				'expires_at' => null,
			);
		}

		global $wpdb;
		$ent_table = NCTB_Migrations::table( 'entitlements' );
		$now       = current_time( 'mysql', true );

		// 3. All-Access active subscription check
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sub = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$ent_table} 
				WHERE user_id = %d AND item_type = 'all_access' AND status = %s 
				AND (expires_at IS NULL OR expires_at > %s) 
				LIMIT 1",
				$user_id,
				self::STATUS_ACTIVE,
				$now
			)
		);
		if ( $sub ) {
			return array(
				'granted'    => true,
				'reason'     => 'subscription',
				'expires_at' => $sub->expires_at,
			);
		}

		// 4. Parent Book Pack Check
		$unit_id = class_exists( 'NCTB_Curriculum_CPT' ) ? NCTB_Curriculum_CPT::get_lesson_unit( $lesson_id ) : 0;
		$book_id = ( class_exists( 'NCTB_Curriculum_CPT' ) && $unit_id ) ? NCTB_Curriculum_CPT::get_unit_book( $unit_id ) : 0;

		if ( $book_id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$book_ent = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$ent_table} 
					WHERE user_id = %d AND item_type = 'book' AND item_id = %d AND status = %s 
					AND (expires_at IS NULL OR expires_at > %s) 
					LIMIT 1",
					$user_id,
					$book_id,
					self::STATUS_ACTIVE,
					$now
				)
			);
			if ( $book_ent ) {
				return array(
					'granted'    => true,
					'reason'     => 'pack_book',
					'expires_at' => $book_ent->expires_at,
				);
			}
		}

		// 5. Parent Unit Pack Check
		if ( $unit_id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$unit_ent = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$ent_table} 
					WHERE user_id = %d AND item_type = 'unit' AND item_id = %d AND status = %s 
					AND (expires_at IS NULL OR expires_at > %s) 
					LIMIT 1",
					$user_id,
					$unit_id,
					self::STATUS_ACTIVE,
					$now
				)
			);
			if ( $unit_ent ) {
				return array(
					'granted'    => true,
					'reason'     => 'pack_unit',
					'expires_at' => $unit_ent->expires_at,
				);
			}
		}

		// 6. Direct Lesson Entitlement Check
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$lesson_ent = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$ent_table} 
				WHERE user_id = %d AND item_type = 'lesson' AND item_id = %d AND status = %s 
				AND (expires_at IS NULL OR expires_at > %s) 
				LIMIT 1",
				$user_id,
				$lesson_id,
				self::STATUS_ACTIVE,
				$now
			)
		);
		if ( $lesson_ent ) {
			return array(
				'granted'    => true,
				'reason'     => $lesson_ent->entitlement_type,
				'expires_at' => $lesson_ent->expires_at,
			);
		}

		// Locked
		return array(
			'granted'    => false,
			'reason'     => 'locked',
			'expires_at' => null,
		);
	}

	/**
	 * Check if a lesson is free.
	 *
	 * @param int $lesson_id Lesson Post ID.
	 * @return bool
	 */
	public static function is_lesson_free( $lesson_id ) {
		$lesson_id = absint( $lesson_id );
		if ( ! $lesson_id ) {
			return false;
		}

		// First lesson of prototype is free by default for demo
		$is_free_meta = get_post_meta( $lesson_id, self::META_IS_FREE, true );
		if ( '' !== $is_free_meta ) {
			return (bool) $is_free_meta;
		}

		// Default: first lesson in any book/unit is free preview
		$lesson = get_post( $lesson_id );
		if ( $lesson && 0 === (int) $lesson->menu_order ) {
			return true;
		}

		return false;
	}

	/**
	 * Grant an entitlement to a student.
	 *
	 * @param int         $user_id          Student User ID.
	 * @param string      $entitlement_type 'direct_lesson', 'pack_unit', 'pack_book', 'subscription', 'admin_grant'.
	 * @param string      $item_type        'lesson', 'unit', 'book', 'subject', 'all_access'.
	 * @param int         $item_id          Target post ID or 0 for all_access.
	 * @param string      $source_type      'woocommerce', 'manual', 'free_promotion'.
	 * @param string      $source_id        Order ID or reference.
	 * @param int         $granted_by       User ID performing the grant (0 for system).
	 * @param string|null $expires_at       Expiration MySQL datetime string or null for lifetime.
	 * @param string      $notes            Audit trail explanation note.
	 * @return int|WP_Error Inserted entitlement ID.
	 */
	public static function grant_entitlement( $user_id, $entitlement_type, $item_type, $item_id, $source_type = 'manual', $source_id = '', $granted_by = 0, $expires_at = null, $notes = '' ) {
		global $wpdb;
		$user_id   = absint( $user_id );
		$item_id   = absint( $item_id );
		$item_type = sanitize_key( $item_type ) ?: 'lesson';
		$ent_type  = sanitize_key( $entitlement_type ) ?: self::TYPE_DIRECT_LESSON;

		if ( ! $user_id ) {
			return new WP_Error( 'nctb_invalid_user', __( 'Valid student ID is required.', 'nctb-learning-hub' ) );
		}

		$ent_table   = NCTB_Migrations::table( 'entitlements' );
		$audit_table = NCTB_Migrations::table( 'entitlement_audit' );
		$now         = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->insert(
			$ent_table,
			array(
				'user_id'          => $user_id,
				'entitlement_type' => $ent_type,
				'item_type'        => $item_type,
				'item_id'          => $item_id,
				'source_type'      => sanitize_key( $source_type ),
				'source_id'        => sanitize_text_field( $source_id ),
				'status'           => self::STATUS_ACTIVE,
				'granted_by'       => absint( $granted_by ),
				'granted_at'       => $now,
				'expires_at'       => $expires_at,
				'created_at'       => $now,
				'updated_at'       => $now,
			),
			array( '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
		);

		$ent_id = $wpdb->insert_id;

		// Audit Log
		if ( $ent_id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert(
				$audit_table,
				array(
					'entitlement_id' => $ent_id,
					'user_id'        => $user_id,
					'action'         => 'grant',
					'performed_by'   => absint( $granted_by ),
					'notes'          => sanitize_textarea_field( $notes ?: sprintf( 'Granted %s for %s #%d', $ent_type, $item_type, $item_id ) ),
					'created_at'     => $now,
				),
				array( '%d', '%d', '%s', '%d', '%s', '%s' )
			);
		}

		return $ent_id;
	}

	/**
	 * Revoke an entitlement.
	 *
	 * @param int    $entitlement_id Entitlement ID.
	 * @param int    $performed_by   User ID revoking access.
	 * @param string $notes          Reason for revocation.
	 * @return bool
	 */
	public static function revoke_entitlement( $entitlement_id, $performed_by = 0, $notes = '' ) {
		global $wpdb;
		$ent_id = absint( $entitlement_id );
		if ( ! $ent_id ) {
			return false;
		}

		$ent_table   = NCTB_Migrations::table( 'entitlements' );
		$audit_table = NCTB_Migrations::table( 'entitlement_audit' );
		$now         = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$ent = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$ent_table} WHERE id = %d", $ent_id ) );
		if ( ! $ent ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->update(
			$ent_table,
			array(
				'status'     => self::STATUS_REVOKED,
				'updated_at' => $now,
			),
			array( 'id' => $ent_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		// Audit Log
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->insert(
			$audit_table,
			array(
				'entitlement_id' => $ent_id,
				'user_id'        => (int) $ent->user_id,
				'action'         => 'revoke',
				'performed_by'   => absint( $performed_by ),
				'notes'          => sanitize_textarea_field( $notes ?: 'Revoked by administrator' ),
				'created_at'     => $now,
			),
			array( '%d', '%d', '%s', '%d', '%s', '%s' )
		);

		return true;
	}

	/**
	 * Get active entitlements for a student.
	 *
	 * @param int $user_id Student User ID.
	 * @return array<int,object>
	 */
	public static function get_user_entitlements( $user_id ) {
		global $wpdb;
		$user_id   = absint( $user_id );
		$ent_table = NCTB_Migrations::table( 'entitlements' );
		$now       = current_time( 'mysql', true );

		if ( ! $user_id ) {
			return array();
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$ent_table} 
				WHERE user_id = %d AND status = %s 
				AND (expires_at IS NULL OR expires_at > %s) 
				ORDER BY granted_at DESC",
				$user_id,
				self::STATUS_ACTIVE,
				$now
			)
		);

		if ( ! empty( $rows ) ) {
			foreach ( $rows as &$r ) {
				if ( 'all_access' === $r->item_type ) {
					$r->item_title = __( 'All-Access Pass (সব পাঠ্যবই ও লেসন)', 'nctb-learning-hub' );
				} else {
					$r->item_title = get_the_title( $r->item_id ) ?: ucfirst( $r->item_type ) . ' #' . $r->item_id;
				}
			}
		}

		return $rows;
	}
}
