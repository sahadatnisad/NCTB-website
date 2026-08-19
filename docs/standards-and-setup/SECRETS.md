# NCTB Learning Hub — Secret & API-Key Handling

Secrets (AI provider keys, payment gateway keys, SMTP credentials, database
passwords) must never appear in browser JavaScript or in committed source.

## Order of preference

1. **`wp-config.php` constants** — best for server-managed deployments.
   ```php
   define( 'NCTB_AI_API_KEY', 'sk-...' );
   define( 'NCTB_AI_PROVIDER', 'anthropic' );
   ```
2. **Environment variables** — read with `getenv()` where the host supports it.
3. **`wp-content/plugins/nctb-learning-hub/config/secrets.php`** — local
   development fallback only. This file is **git-ignored**. Copy the sample:
   ```bash
   cp config/secrets.sample.php config/secrets.php
   ```

## Rules

- AI and payment calls happen **server-side only**. The browser never sees a key.
- Never `echo`, `var_dump`, or log a secret. `NCTB_Logger` must not receive keys.
- Rotate keys if they are ever exposed; do not reuse across environments.
- Dev/staging use sandbox/test keys; production uses live keys.
- `.gitignore` already excludes `config/secrets.php` and `wp-config.php`. Verify
  with `git status` before every commit.

## What is safe to commit

- `config/secrets.sample.php` (placeholder values only).
- Provider/model identifiers that are not secret (e.g. `NCTB_AI_PROVIDER`).
