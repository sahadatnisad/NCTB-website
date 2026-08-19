# AUDIT — Phases 0 → 3

**Date:** 2026-08-19 · **Auditor:** Claude (Claude Code)
**Method:** static (lint of all 35 PHP files, security-pattern grep, structure review) + **runtime** against the live Docker stack (`localhost:8080`, WP 7.0.4, PHP 8.3.28) with `WP_DEBUG` on.
**Scope:** committed state at `ec38d9b` (Phase 3). *Note: Phase 4 was being edited concurrently on the working tree during this audit — see Finding #1.*

## Verdict
**Phases 0–3 are solidly built and correctly connected.** Core learning flow works end-to-end: activate → migrate → CPTs → seed → browse Book › Unit › Lesson with outcomes & concepts, over both HTML and REST. No blocking defects in the shipped code. The issues below are mostly **leftover artifacts from Phase 1/2 iterations** and **process/coordination risk**, not broken Phase 3 logic.

## What passed (evidence)
| Check | Result |
|---|---|
| `php -l` on all 35 plugin+theme files | ✅ 0 syntax errors |
| Runtime PHP warnings/notices (home, onboarding, dashboard, book/unit/lesson, REST) | ✅ none (empty debug.log) |
| Every REST route has a `permission_callback` | ✅ 7/7 routes |
| REST 404 guards (unknown id / wrong post-type) | ✅ 404 |
| Custom-table queries use `$wpdb->prepare()` / safe constant SQL | ✅ |
| Meta-box saves: nonce + `edit_post` cap + autosave guard | ✅ |
| Concepts admin: `check_admin_referer` + `edit_posts` cap, output escaped | ✅ |
| Template output escaping (`esc_html/attr/url`) | ✅ |
| Schema version option = current | ✅ `0.3.0` |
| `nctb_student` role + caps | ✅ read, view_nctb_content, edit_nctb_profile, submit_nctb_practice |
| CPTs + 3 custom tables present; sample tree seeded | ✅ |
| Front-end pages + all REST endpoints | ✅ HTTP 200 |
| Unauthenticated admin Concepts page | ✅ 302 → login |

## Findings

### 🔴 #1 — HIGH (process): concurrent, uncoordinated build
While auditing, Phase 4 files were being written to the **same working tree** (uncommitted: `class-nctb-lesson-activity-types.php`, `admin/css`, `admin/js`, and edits to migrations, curriculum-data/meta/rest, main plugin). This happened **before Phase 3 was human-reviewed**, which violates the "one phase, then STOP for review" rule in `AGENTS.md`/`BUILD_STATE.md`.
- **Risk:** merge conflicts, lost work, half-applied Phase 4 leaving the tree inconsistent.
- **Fix:** run one agent at a time. Commit/stash the in-flight Phase 4 work, review Phase 3, then resume. Have every agent `git pull` + read `BUILD_STATE.md` before editing, and push immediately after a phase.

### 🟠 #2 — MEDIUM (Phase 1/2 debt): orphan pages with missing templates
Four pages exist but point at templates that are **not on disk**, so they render via fallback (near-empty):
| Page (slug) | Assigned template | On disk? |
|---|---|---|
| `login` | `page-login.php` | ❌ missing |
| `register` | `page-register.php` | ❌ missing |
| `teacher` | `teacher-dashboard.php` | ❌ missing |
| `dashboard-3` | `student-dashboard.php` | ❌ missing |
- The real auth flow uses WordPress core `wp-login.php` (the header "Login" link → `wp_login_url()`), so **these custom `/login` and `/register` pages are unused, empty shells** — Phase 1's "Login/Register" is only partially realized.
- `dashboard-3` → `student-dashboard.php` is the source of the **parse-error log line** seen earlier; the file was later removed but the page still references it.
- **Fix:** delete the four orphan pages (they're DB content, safe to trash), or build real templates/content for `/login` + `/register` if custom auth pages are wanted.

### 🟠 #3 — MEDIUM (cleanliness): duplicate dashboard pages
`dashboard` (id 12, correct → `page-dashboard.php`) coexists with `dashboard-3` (id 7, orphan). The `-3` suffix implies earlier duplicates were created during iteration. Keep one, trash the rest.

### 🟡 #4 — LOW (integration): dashboard not wired to Phase 3
The student dashboard body still shows *"লেসন শীঘ্রই উন্মুক্ত হচ্ছে (Phase 3)"* and does **not** link subjects to the now-live `/book/` browse. Update it to link each enrolled subject into the curriculum.

### 🟡 #5 — LOW (integration): subject slug vs taxonomy mismatch
Student profiles store subjects as slugs (`english_1st`), but the `nctb_subject` taxonomy uses free-text term names (`English 1st Paper`). There is no programmatic link, so a student's chosen subject cannot yet filter books. Standardize subject identifiers across profile + taxonomy before the Phase 7 dashboard relies on it.

### 🟡 #6 — LOW (dev hygiene): WP_DEBUG was off in dev
The dev `wp-config.php` had no `WP_DEBUG`/`WP_DEBUG_LOG` (enabled during this audit). Per `docs/ENVIRONMENT.md`, development should keep both on so regressions surface early.

## Non-issues (verified, no action)
- `wp db query` fails with a MySQL-8 client TLS/self-signed-cert quirk — environment only; PHP/`$wpdb` connections are unaffected (all checks used `$wpdb`).
- wp-config `DB_HOST` default `'mysql'` is only a fallback; compose sets `WORDPRESS_DB_HOST=db` correctly.

## Recommended order of action
1. Resolve the concurrency (Finding #1) — commit/stash Phase 4, single-track from here.
2. Trash the 4 orphan pages + duplicate dashboard (Findings #2, #3).
3. Wire dashboard → `/book/` and align subject identifiers (Findings #4, #5) — natural to fold into Phase 7.

---

## Fixes applied (2026-08-19, Claude)

| Finding | Status | What was done |
|---|---|---|
| #2 Orphan pages (login, register, teacher, dashboard-3) | ✅ Fixed | Root cause: they were **manual local DB cruft**, not code. Deleted (ids 9,10,8,7). Real auth continues via WP-core `wp-login.php`. |
| #3 Duplicate dashboard | ✅ Fixed | `dashboard-3` removed; single canonical `dashboard` page remains. |
| (new) Pages not reproducible from repo | ✅ Fixed | Added `includes/class-nctb-pages.php` — idempotent provisioner that recreates the `onboarding` + `dashboard` pages (correct template + shortcode) on activation and on in-place upgrade. Verified: idempotent (no dupes) and fresh-install safe (delete → recreated with `page-onboarding.php`/`[nctb_onboarding]`). |
| #4 Dashboard stale "Phase 3" text / no browse link | ✅ Fixed | Dashboard subject cards now link to the live `/book/` browse; removed the "coming soon (Phase 3)" text. |
| #6 Dev `WP_DEBUG` off | ✅ Fixed (local) | Enabled `WP_DEBUG`/`WP_DEBUG_LOG` in the dev `wp-config.php` (git-ignored). |
| #5 Subject slug ↔ taxonomy mismatch | ⏳ Deferred | Design alignment (profile slug `english_1st` vs term `English 1st Paper`); best folded into the Phase 7 dashboard. Left as-is to avoid touching the concurrently-edited seeder. |

**Verification:** all changed PHP lints clean; whole plugin loads (v0.5.0); all pages HTTP 200; zero PHP warnings/notices (empty debug.log); orphan pages gone; onboarding+dashboard present.

**Commit note:** at fix time the working tree also contained Antigravity's in-progress **Phase 5** work in the *same* shared files (`nctb-learning-hub.php`, `class-nctb-plugin.php`). To avoid committing unfinished Phase 5, these fixes were left uncommitted on disk to be captured by the next clean full-tree commit. **Action item: serialize agents — one phase at a time (per `AGENTS.md`).**
