<?php
/**
 * Lightweight debug logger.
 *
 * Writes only when WP_DEBUG and NCTB_LH_DEBUG are both enabled, so production
 * stays silent. Never logs secrets — callers must not pass API keys or raw
 * student PII into these methods.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Logger
 */
class NCTB_Logger {

	/**
	 * Whether logging is active for this environment.
	 *
	 * @return bool
	 */
	protected static function enabled() {
		return defined( 'WP_DEBUG' ) && WP_DEBUG
			&& defined( 'NCTB_LH_DEBUG' ) && NCTB_LH_DEBUG;
	}

	/**
	 * Write an informational line to the PHP error log.
	 *
	 * @param string $message Human-readable message. No secrets.
	 * @param array  $context Optional context; scalar values only.
	 * @return void
	 */
	public static function info( $message, array $context = array() ) {
		self::write( 'INFO', $message, $context );
	}

	/**
	 * Write an error line to the PHP error log.
	 *
	 * @param string $message Human-readable message. No secrets.
	 * @param array  $context Optional context; scalar values only.
	 * @return void
	 */
	public static function error( $message, array $context = array() ) {
		self::write( 'ERROR', $message, $context );
	}

	/**
	 * Internal writer.
	 *
	 * @param string $level   Log level label.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return void
	 */
	protected static function write( $level, $message, array $context ) {
		if ( ! self::enabled() ) {
			return;
		}

		$suffix = '';
		if ( ! empty( $context ) ) {
			$suffix = ' ' . wp_json_encode( $context );
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( sprintf( '[NCTB %s] %s%s', $level, $message, $suffix ) );
	}
}
