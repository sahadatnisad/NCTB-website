<?php
/**
 * Production Environment Configuration & Secrets Template.
 *
 * Copy this file to `config/secrets.php` (or define these constants in `wp-config.php`).
 * NEVER commit `secrets.php` or real API keys to git repository.
 *
 * @package NCTB\LearningHub
 */

// -----------------------------------------------------------------------------
// 1. AI Provider Configuration
// -----------------------------------------------------------------------------
// Options: 'gemini', 'anthropic', 'openai', 'mock'
define( 'NCTB_AI_PROVIDER', 'gemini' );

// Primary Server-Side API Key
define( 'NCTB_AI_API_KEY', 'your-ai-api-key-here' );

// Daily conversation quota cap per student (abuse safeguard)
define( 'NCTB_AI_DAILY_QUOTA', 30 );

// -----------------------------------------------------------------------------
// 2. Payment Gateway Configuration (WooCommerce / Direct MFS)
// -----------------------------------------------------------------------------
// bKash Merchant Settings
define( 'NCTB_BKASH_APP_KEY', 'your-bkash-app-key' );
define( 'NCTB_BKASH_APP_SECRET', 'your-bkash-app-secret' );
define( 'NCTB_BKASH_USERNAME', 'your-bkash-username' );
define( 'NCTB_BKASH_PASSWORD', 'your-bkash-password' );
define( 'NCTB_BKASH_IS_SANDBOX', false );

// Nagad Merchant Settings
define( 'NCTB_NAGAD_MERCHANT_ID', 'your-nagad-merchant-id' );
define( 'NCTB_NAGAD_PUBLIC_KEY', 'your-nagad-public-key' );
define( 'NCTB_NAGAD_PRIVATE_KEY', 'your-nagad-private-key' );

// SSLCommerz Gateway Settings
define( 'NCTB_SSLC_STORE_ID', 'your-sslcommerz-store-id' );
define( 'NCTB_SSLC_STORE_PASSWD', 'your-sslcommerz-store-passwd' );
define( 'NCTB_SSLC_IS_SANDBOX', false );

// -----------------------------------------------------------------------------
// 3. Security & Logging
// -----------------------------------------------------------------------------
define( 'NCTB_ENV', 'production' ); // 'development', 'staging', 'production'
define( 'NCTB_LOG_LEVEL', 'warning' ); // 'debug', 'info', 'warning', 'error'
