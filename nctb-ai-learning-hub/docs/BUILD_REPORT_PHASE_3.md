# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 3 — Curriculum + Book + Unit + Lesson CMS
**Date:** 2026-08-19
**Built by:** Claude (Claude Code)
**Environment:** Local Docker (`docker-compose.yml`) — `nctb-wordpress` + `nctb-mysql`, site at http://localhost:8080
**WordPress version:** 7.0.4
**PHP version:** 8.3.28
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) 0.3.0
**Plugin version:** NCTB Learning Hub 0.3.0

## 1. What was built
The academic backbone: editors can create, edit and reorder the full
**Class → Subject → Book → Unit → Lesson** hierarchy from wp-admin without code,
and students can browse a sample tree on the front-end. Concepts and per-lesson
learning outcomes are stored in versioned custom tables.

## 2. Files created/changed
**Plugin — new:**
- `includes/class-nctb-curriculum-data.php` — data service for the 3 custom tables (prepared queries).
- `includes/class-nctb-curriculum-cpt.php` — CPTs, taxonomies, admin columns, menu_order sequencing, relationship query helpers.
- `includes/class-nctb-curriculum-meta.php` — editor meta boxes (unit→book, lesson→unit, learning outcomes, concept links) with nonce + capability + sanitization.
- `includes/class-nctb-curriculum-admin.php` — Concepts management submenu (add/list/delete).
- `includes/class-nctb-curriculum-rest.php` — read-only `nctb/v1/curriculum/*` endpoints.
- `includes/class-nctb-curriculum-seeder.php` — one-time sample tree seeder.

**Plugin — changed:**
- `nctb-learning-hub.php` — version 0.2.0 → 0.3.0; require the new classes.
- `includes/class-nctb-migrations.php` — added migration step `0.3.0` (creates the 3 tables via dbDelta).
- `includes/class-nctb-plugin.php` — load curriculum modules; register curriculum REST controller.
- `includes/class-nctb-activator.php` — register roles + CPTs, seed sample tree, then flush rewrite rules.

**Theme — new:**
- `archive-nctb_book.php`, `single-nctb_book.php`, `single-nctb_unit.php`, `single-nctb_lesson.php` — browse/detail templates (presentation only).
- `css/curriculum.css` — mobile-first browse styles.

**Theme — changed:**
- `functions.php` — conditionally enqueue `curriculum.css` on curriculum screens only.
- `header.php` — added a "পাঠ্যবই (Learn)" nav link to the books archive.

## 3. Database/schema changes
Migration `0.3.0` creates (idempotent, dbDelta):
- `wp_nctb_concepts` (id, name, slug, subject, description, created_at)
- `wp_nctb_learning_outcomes` (id, lesson_id, outcome_text, sort_order, created_at)
- `wp_nctb_lesson_concepts` (id, lesson_id, concept_id — unique lesson+concept)

Relationships between posts use post meta: `_nctb_book_id` (on units), `_nctb_unit_id` (on lessons); sequencing uses native `menu_order`.

## 4. Admin features added
- Books / Units / Lessons post types with editors, ordering (Page Attributes → Order) and admin list columns showing parent + order.
- Taxonomies: Class/Level, Subject, Paper, Curriculum Version, Session (books), Topic (lessons).
- Meta boxes to attach a unit to a book, a lesson to a unit, enter learning outcomes (one per line), and link concepts.
- **Concepts** admin screen (under Lessons) to add/list/delete reusable concepts.

## 5. Student-facing features added
- `/book/` archive listing books; single Book page (units + nested lessons); single Unit page (ordered lessons); single Lesson page (learning outcomes, content, concept chips, Book›Unit breadcrumb).
- "Learn" link in the site header.

## 6. REST/AJAX endpoints added
All under namespace `nctb/v1`, read-only, published content only:
- `GET /curriculum/books`
- `GET /curriculum/book/{id}` (nested units + lessons)
- `GET /curriculum/lesson/{id}` (content, unit, book, outcomes, concepts)

## 7. Security controls added
- Meta-box saves guarded by nonce (`nctb_curriculum_meta_save`), `current_user_can( 'edit_post' )`, autosave short-circuit, and full sanitization.
- Concepts admin actions via `admin-post.php` with `check_admin_referer` + `edit_posts` capability; output escaped.
- All custom-table queries use `$wpdb->prepare()`.
- REST endpoints return 404 for missing/unpublished/mismatched-type IDs.

## 8. Tests performed (runtime, in Docker)
- `php -l` on all new/changed plugin + theme PHP.
- Reactivated plugin → verified version reports 0.3.0.
- Verified the 3 custom tables exist (`$wpdb`).
- Verified CPTs registered (`wp post-type list`).
- Verified sample tree seeded (Book/Unit/Lesson) with correct `menu_order`.
- Verified row counts: 2 concepts, 3 learning outcomes, 2 lesson–concept links.
- REST: `/curriculum/books` and `/curriculum/lesson/15` return correct nested data.
- REST 404 guards: unknown id and wrong-type id both → 404.
- Front-end HTTP 200 for book archive, single unit, single lesson; asserted markers (Learning Outcomes, concept chips, breadcrumb) and conditional `curriculum.css` load.
- Regression: homepage 200; unauthenticated Concepts admin page → 302 to login.

## 9. Test results
All of the above passed. No PHP syntax errors; no new PHP warnings/notices attributable to Phase 3 code.

## 10. Screens/pages to manually review
- wp-admin: Books, Units, Lessons lists + editors; Lessons → Concepts screen.
- Front-end: `/book/`, the sample Book, Unit, and Lesson pages on a phone width.

## 11. Known problems / technical debt
- Stale log line observed for a **removed** Phase-1 file `themes/nctb-child-theme/student-dashboard.php` (no longer exists, not referenced, not part of Phase 3). If a page still has its template set to that deleted file, reassign the page template. Worth a cleanup pass.
- `wp db query` fails in this environment due to a MySQL 8 client TLS/self-signed-cert quirk; PHP/`$wpdb` connections are unaffected (all verification used `$wpdb`).
- Concept editing (update) is intentionally minimal (add/delete only) — sufficient for Phase 3.
- Lesson↔unit and unit↔book links are single-parent by design.

## 12. Setup or migration steps to perform
- On a fresh install the activator handles everything (tables, CPTs, sample seed, rewrite flush).
- On an in-place file upgrade from 0.2.0: tables are created on next `admin_init` by the migration runner; **visit Settings → Permalinks once (or reactivate the plugin)** to flush rewrite rules so CPT URLs resolve.

## 13. Rollback notes
- Deactivating the plugin removes CPT/endpoints but preserves data.
- To fully revert Phase 3: `git revert`/remove the six new plugin files + four theme templates + `curriculum.css`, restore version to 0.2.0, and drop `wp_nctb_concepts`, `wp_nctb_learning_outcomes`, `wp_nctb_lesson_concepts`. The sample posts (Book/Unit/Lesson) can be trashed from admin.
- No WordPress core files were modified.

## 14. What is intentionally NOT built yet
- Lesson activity blocks / gold-standard lesson authoring (Phase 4).
- Practice/question engine and attempts (Phase 5).
- Progress, mastery, mistakes, revision (Phase 6).
- Dashboard "continue learning" wiring to real lessons (Phase 7).
- Only ONE sample lesson's data is entered, per the plan — no full course content.

**STOP HERE. NEXT PHASE NOT STARTED.**
