# NCTB Learning Hub — Backup & Restore

Because students may be minors and their progress data is valuable, every
environment must be backup-able and restorable. Two things must be captured:
the **database** and the **`wp-content` files** (uploads, plugins, theme).

## What to back up

- **Database** — all tables, including the future `wp_nctb_*` learning tables.
- **`wp-content/uploads`** — lesson audio, images, approved resources.
- **`wp-content/plugins/nctb-learning-hub`** and
  **`wp-content/themes/nctb-child-theme`** — already in Git, but include in
  file backups for completeness.

WordPress core does **not** need backing up (reinstallable from source).

## Database backup (WP-CLI — preferred)

```bash
# From the WordPress root.
wp db export "backups/db-$(date +%Y%m%d-%H%M%S).sql"
```

## Database backup (mysqldump — fallback)

```bash
mysqldump -u DB_USER -p DB_NAME > "backups/db-$(date +%Y%m%d-%H%M%S).sql"
```

## File backup

```bash
tar -czf "backups/uploads-$(date +%Y%m%d-%H%M%S).tar.gz" wp-content/uploads
```

## Restore

```bash
# Database.
wp db import backups/db-YYYYMMDD-HHMMSS.sql
# Or: mysql -u DB_USER -p DB_NAME < backups/db-YYYYMMDD-HHMMSS.sql

# Files.
tar -xzf backups/uploads-YYYYMMDD-HHMMSS.tar.gz
```

## Rules

- Take a database backup **before every migration / schema change**.
- Store backups outside the web root (or in git-ignored `backups/`, which this
  repo already ignores) and off-site for production.
- Test a restore at least once before go-live (Phase 14).
- Never restore production data into development without sanitizing student PII.
