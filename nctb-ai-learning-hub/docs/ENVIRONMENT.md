# NCTB Learning Hub — Environments

Three separate environments. Code is identical across them; only configuration
and data differ. Never develop against production data.

| Environment | Purpose                         | `WP_ENVIRONMENT_TYPE` | Debug | Data |
|-------------|---------------------------------|-----------------------|-------|------|
| development | Local coding & testing          | `development`         | ON    | Disposable seed data |
| staging     | Pre-release verification / beta | `staging`             | ON (log only) | Sanitized copy of prod |
| production  | Live students                   | `production`          | OFF   | Real data (backed up) |

## How the code detects the environment

The plugin reads `wp_get_environment_type()`. Set it per environment in
`wp-config.php`:

```php
define( 'WP_ENVIRONMENT_TYPE', 'development' );
```

## Debug logging (development/staging only)

Add to `wp-config.php` on **non-production** environments only:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );   // writes wp-content/debug.log
define( 'WP_DEBUG_DISPLAY', false ); // never render errors to the browser
define( 'SCRIPT_DEBUG', true );
define( 'NCTB_LH_DEBUG', true );  // enables the plugin's NCTB_Logger output
```

Production `wp-config.php` must set `WP_DEBUG` to `false` and must **not** define
`NCTB_LH_DEBUG`.

## Config that must differ per environment

- Database credentials and table prefix.
- `WP_HOME` / `WP_SITEURL`.
- AI provider keys (staging/prod may use different keys or quotas).
- Payment gateway keys (sandbox in dev/staging, live in production).

Keep these out of version control — see `docs/SECRETS.md`.
