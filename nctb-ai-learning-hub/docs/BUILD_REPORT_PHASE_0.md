# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 0 — Set up a safe WordPress development environment
**Date:** 2026-08-18
**Environment:** Local development (Linux). No PHP/WP-CLI/Composer runtime available in this session — code authored and statically checked, not executed here.
**WordPress version:** 7.0.4 (core present in repo)
**PHP version:** Target >= 8.0 (not installed in this session)
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) v0.1.0
**Plugin version:** NCTB Learning Hub v0.1.0

## 1. What was built
- Activatable `nctb-learning-hub` plugin skeleton holding all future learning/business logic, with a singleton loader and clean extension points (`nctb_lh_loaded`).
- Versioned schema/migration runner foundation (`NCTB_Migrations`) — no tables yet, by design.
- Environment-gated debug logger (`NCTB_Logger`), active only when `WP_DEBUG` + `NCTB_LH_DEBUG` are set.
- Safe activation/deactivation/uninstall lifecycle (no data destruction on deactivate).
- Presentation-only, mobile-first theme (`nctb-child-theme`) with English/Bangla (UTF-8) typography; replaced the previously broken placeholder directories.
- Documentation folder: coding standards, environment separation, secrets handling, backup/restore, README, and phase-status tracker.
- Secret-handling pattern (`config/secrets.sample.php`) plus `.gitignore` rules at repo and plugin level.

## 2. Files created/changed
- Removed broken placeholders: `wp-content/themes/nctb-child-theme/composer.json/` and `style.css/` (were empty directories).
- Plugin: `nctb-learning-hub.php`, `uninstall.php`, `composer.json`, `phpcs.xml.dist`, `readme.txt`, `.gitignore`, `includes/{class-nctb-plugin,class-nctb-activator,class-nctb-deactivator,class-nctb-migrations,class-nctb-logger}.php`, `admin/class-nctb-admin.php`, `public/class-nctb-public.php`, `config/secrets.sample.php`, and `index.php` guards in every folder.
- Theme: `style.css`, `functions.php`, `index.php`, `header.php`, `footer.php`, `composer.json`.
- Docs: `docs/{README,CODING_STANDARDS,ENVIRONMENT,SECRETS,BACKUP_RESTORE,PHASE_STATUS,BUILD_REPORT_PHASE_0}.md`.
- Repo root: `.gitignore`.

## 3. Database/schema changes
- None. `NCTB_Migrations` establishes the versioned upgrade path and stores schema version in the `nctb_lh_db_version` option (non-autoloaded). No custom tables created (correct for Phase 0).

## 4. Admin features added
- None functional. `NCTB_Admin` is a loaded-only placeholder with a documented extension point for Phase 3+.

## 5. Student-facing features added
- None functional. `NCTB_Public` is a placeholder; the theme renders a minimal valid shell. Real UI arrives in Phase 1.

## 6. REST/AJAX endpoints added
- None (correct for Phase 0).

## 7. Security controls added
- `ABSPATH`/`WP_UNINSTALL_PLUGIN` guards on every PHP file.
- Secrets excluded from version control (`config/secrets.php`, `wp-config.php` git-ignored); server-side-only secret pattern documented.
- Logger will not emit in production and is documented never to receive secrets.
- Non-destructive deactivate; uninstall removes only plugin options.
- WPCS ruleset (`phpcs.xml.dist`) enforcing WordPress standards, text domain, and PHP 8.0+ compatibility.

## 8. Tests performed
- Static structural check of all authored PHP: `<?php` tag present, brace and parenthesis balance — no mismatches.
- File-tree verification against the plan's Phase 0 deliverable list.
- Git ignore verification: `wp-config.php`, `config/secrets.php`, and `wp-content/uploads/` confirmed ignored.
- Confirmed broken theme placeholder directories were removed and replaced with real files.

## 9. Test results
- All static checks passed. No brace/paren/tag mismatches.
- Ignore rules behave as intended.
- NOT tested (no runtime this session): live plugin activation/deactivation, WordPress boot, PHP lint via phpcs, browser/JS console.

## 10. Screens/pages to manually review
- WordPress admin → Plugins: activate/deactivate **NCTB Learning Hub** (expect no fatal errors).
- WordPress admin → Appearance → Themes: activate **NCTB Learning Hub Theme**.
- Front page renders with the minimal header/footer and no PHP notices (with `WP_DEBUG` on).

## 11. Known problems / technical debt
- **WordPress core layout looks non-standard in this repo:** `wp-includes` files (e.g. `formatting.php`, `load.php`, `pluggable.php`, `version.php`) sit at the repo root and there is no `wp-includes/` directory, yet `wp-settings.php` requires `ABSPATH . WPINC . '/...'`. The site likely will not boot until the standard WordPress folder structure is restored. This is environment/infrastructure, independent of the Phase 0 code delivered.
- `nctb-child-theme` is implemented as a self-contained standalone theme (no parent theme is present in this install), so despite the folder name it is not a true WordPress child theme. Functionally equivalent for the plan's "presentation-only" intent.
- No PHP runtime in this session, so activation and phpcs were not executed — must be verified locally.

## 12. Setup or migration steps I must perform
1. Restore a standard WordPress layout (ensure a real `wp-includes/` directory exists) so WordPress boots — see Known Problems.
2. Copy `wp-config-sample.php` → `wp-config.php`; set DB credentials, `WP_ENVIRONMENT_TYPE`, and the dev debug constants from `docs/ENVIRONMENT.md`.
3. Activate the theme and the plugin from wp-admin; confirm no fatal errors.
4. (Optional, for linting) `cd wp-content/plugins/nctb-learning-hub && composer install && composer lint`.
5. Take an initial database/file backup per `docs/BACKUP_RESTORE.md`.

## 13. Rollback notes
- All new code is isolated under `wp-content/plugins/nctb-learning-hub`, `wp-content/themes/nctb-child-theme`, `docs/`, and root `.gitignore`. Deleting these folders fully reverts Phase 0.
- No database tables were created; the only DB footprint is two options (`nctb_lh_db_version`, `nctb_lh_installed_at`) removed automatically on plugin uninstall.
- No WordPress core files were modified.

## 14. What is intentionally NOT built yet
- Curriculum CPTs/taxonomies, lessons, activities (Phase 3–4).
- Practice/question engine, attempts (Phase 5).
- Progress, mastery, mistakes, spaced revision (Phase 6).
- Student dashboard (Phase 7).
- WooCommerce, entitlement service, paywall (Phase 8).
- AI provider adapter and tutor (Phase 9).
- Writing/listening/speaking, board questions, analytics (Phase 10–12).
- Any real curriculum content (Phase 13+).

**STOP HERE. NEXT PHASE NOT STARTED.**
