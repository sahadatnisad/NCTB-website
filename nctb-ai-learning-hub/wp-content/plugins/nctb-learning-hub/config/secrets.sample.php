<?php
/**
 * SAMPLE secrets file — copy to secrets.php and fill in real values.
 *
 * DO NOT commit secrets.php. It is git-ignored. This sample documents the
 * expected constants without exposing any real key. Prefer setting these in
 * wp-config.php or environment variables in real deployments; this file is a
 * fallback for local development only.
 *
 * Secrets must NEVER be printed to the browser or logged. AI calls happen
 * server-side only (see the plan's AI architecture rules).
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// AI provider API key (server-side use only). Leave undefined until Phase 9.
if ( ! defined( 'NCTB_AI_API_KEY' ) ) {
	define( 'NCTB_AI_API_KEY', '' );
}

// AI provider identifier (e.g. 'anthropic'), so the adapter can switch models.
if ( ! defined( 'NCTB_AI_PROVIDER' ) ) {
	define( 'NCTB_AI_PROVIDER', 'anthropic' );
}
