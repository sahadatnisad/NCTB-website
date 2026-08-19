# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 4 — One gold-standard interactive lesson
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash)
**Environment:** Local Docker (`docker-compose.yml`) — `nctb-wordpress` + `nctb-mysql`, site at http://localhost:8080
**WordPress version:** 7.0.4
**PHP version:** 8.3.33
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) 0.4.0
**Plugin version:** NCTB Learning Hub 0.4.0

## 1. What was built
Proved the complete lesson experience before scaling content:
- **14 Reusable Activity Block Types:** objective, warm-up, reading passage, vocabulary, grammar focus, worked examples, guided practice (with hints & reveals), independent practice (with self-check checklist), writing task (with live word counter and model answer), listening activity (with HTML5 audio player and transcript toggle), speaking activity (with 2-minute timer and talking points), lesson summary, quiz placeholder (bridge to Phase 5), and contextual AI tutor placeholder (bridge to Phase 9).
- **Admin Lesson Activity Editor:** Drag-and-drop / reorderable, collapsible activity builder meta box in wp-admin (`nctb_lesson` edit screen) with type selector, rich inputs, JSON metadata handling, and secure nonce + capability checking.
- **REST Endpoints:** `GET /nctb/v1/curriculum/lesson/{id}/activities`, `POST /nctb/v1/curriculum/lesson/{id}/activities/order`, and enhanced `GET /nctb/v1/curriculum/lesson/{id}` including full activity block arrays.
- **Gold-Standard Prototype Lesson Seeding:** Complete authentic NCTB English For Today SSC lesson (*"Nelson Mandela, from Prisoner to President"*) seeded with all 14 activity blocks.
- **Responsive Interactive Theme Experience:** Mobile-first stepper and progress bar in `single-nctb_lesson.php`, identifiable progress position (restores from URL hash and `localStorage`), step-by-step navigation, full lesson toggle, and **zero lesson-specific PHP templates**.

## 2. Files created/changed
**Plugin — new:**
- `includes/class-nctb-lesson-activity-types.php` — Registry of the 14 activity block types, labels, icons, schemas, and sanitization logic.
- `admin/css/nctb-admin-activities.css` — Admin styling for the reorderable activity builder.
- `admin/js/nctb-admin-activities.js` — Admin script for dynamically adding, reordering, deleting, and collapsing activity blocks.

**Plugin — changed:**
- `nctb-learning-hub.php` — Bumped version to `0.4.0`; required `class-nctb-lesson-activity-types.php`.
- `includes/class-nctb-migrations.php` — Added migration step `0.4.0` creating `wp_nctb_lesson_activities` via `dbDelta`.
- `includes/class-nctb-curriculum-data.php` — Added activity data service methods (`get_lesson_activities`, `create_activity`, `update_activity`, `delete_activity`, `reorder_activities`, `set_lesson_activities`).
- `includes/class-nctb-curriculum-meta.php` — Added Interactive Lesson Activities meta box (`render_lesson_activities`) and secure activity saving in `save_lesson`.
- `includes/class-nctb-curriculum-rest.php` — Added `/curriculum/lesson/{id}/activities` and reorder endpoints; included formatted activities in `get_lesson`.
- `includes/class-nctb-curriculum-seeder.php` — Added `maybe_seed_activities` with full 14 gold-standard activity blocks for Nelson Mandela sample lesson.
- `admin/class-nctb-admin.php` — Enqueued admin activity builder scripts and styles on `nctb_lesson` edit screens.

**Theme — new:**
- `js/lesson-interactive.js` — Vanilla, mobile-first JavaScript for step switching, progress bar updates, localStorage state persistence, hint reveals, audio transcript toggles, live word counter, speaking timer, and keyboard arrow navigation.

**Theme — changed:**
- `single-nctb_lesson.php` — Generic, data-driven interactive lesson template with breadcrumbs, activity stepper, progress status bar, step pills, modular card views, guided practice reveals, writing draft zone, and contextual tutor callout.
- `css/curriculum.css` — Added responsive, mobile-first styles for the activity stepper, progress bar, step pills, vocabulary grid, grammar boxes, example callouts, audio player, speaking timer, and placeholders.
- `functions.php` — Enqueued `lesson-interactive.js` on `single-nctb_lesson` screens.
- `style.css` — Bumped theme version to `0.4.0`.

## 3. Database/schema changes
Migration `0.4.0` creates (idempotent, dbDelta):
- `wp_nctb_lesson_activities`
  - `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
  - `lesson_id` BIGINT UNSIGNED NOT NULL (KEY lesson_id)
  - `activity_type` VARCHAR(64) NOT NULL
  - `title` VARCHAR(255) NOT NULL DEFAULT ''
  - `content` LONGTEXT NOT NULL
  - `meta_data` LONGTEXT NULL (JSON string)
  - `sort_order` INT NOT NULL DEFAULT 0 (KEY sort_order)
  - `is_active` TINYINT(1) NOT NULL DEFAULT 1
  - `created_at` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00'
  - `updated_at` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00'
  - PRIMARY KEY (id)

## 4. Admin features added
- Lesson Activities builder meta box on Lesson edit screen:
  - Add any of the 14 standard activity types.
  - Move Up (▲) / Move Down (▼) ordering controls.
  - Collapsible cards with live title preview and delete action.
  - Type-specific rich content & JSON metadata editing.
  - Expand / Collapse all toggle.
- Secure saving with nonce verification (`nctb_curriculum_meta_save`), `edit_post` capability check, XSS sanitization (`wp_kses_post`, `sanitize_text_field`), and structured JSON validation.

## 5. Student-facing features added
- Interactive activity stepper with step counter, progress bar, and clickable step pills (`1 🎯`, `2 💡`, `3 📖`, `4 🔤`, etc.).
- Identifiable progress position: saves current activity index to `localStorage` and URL anchor (`#activity-3`), resuming seamlessly on page reload.
- Full lesson view toggle: switch between focused step-by-step mode and linear complete-lesson view.
- 14 gold-standard block presentations:
  - Warm-up card with reflective quote and discussion prompts.
  - Reading passage with paragraph numbering (`[1]`, `[2]`, `[3]`, `[4]`).
  - Vocabulary builder grid with English definitions, Bangla meanings, and example sentences.
  - Grammar focus boxes with narrative past tense formulas and relative clauses.
  - Worked examples with sentence analysis.
  - Guided practice with interactive "💡 Show Hint" and "✅ Reveal Model Answer" toggles.
  - Independent practice with student self-check checkboxes.
  - Writing task with prompt, writing guidelines, live word counter, and model response reveal.
  - Listening activity with audio player, duration badge, collapsible transcript, and comprehension question.
  - Speaking activity with prompt, talking points, and interactive 2-minute practice timer.
  - Lesson summary recap card.
  - **Quiz Placeholder Card:** "⚡ Lesson Assessment Quiz (Coming in Phase 5 Practice Engine)".
  - **Contextual AI Tutor Button & Callout:** "🤖 Ask AI Tutor about this lesson (Coming in Phase 9)".

## 6. REST/AJAX endpoints added
Under namespace `nctb/v1`:
- `GET /curriculum/lesson/{id}/activities` (public read-only for published lessons; returns array of 14 formatted activities with labels, icons, rendered content, and metadata).
- `POST /curriculum/lesson/{id}/activities/order` (guarded by `edit_post` permission check + nonce; accepts ordered array of activity IDs to update sort order).
- `GET /curriculum/lesson/{id}` now returns the full `activities` list in the response payload.

## 7. Security controls added
- Activity inputs sanitized via `NCTB_Lesson_Activity_Types::sanitize_activity`: HTML filtered with `wp_kses_post`, titles with `sanitize_text_field`, type keys with `sanitize_key`, and metadata arrays validated.
- Reorder REST route guarded with `check_edit_lesson_permission` verifying `current_user_can('edit_post', $lesson_id)`.
- Meta box saves guarded by `nctb_curriculum_meta_save` nonce, `current_user_can('edit_post')`, and autosave short-circuiting.
- All database queries executed with `$wpdb->prepare()`.

## 8. Tests performed (runtime, in Docker)
- `php -l` executed on all PHP files in plugin and child theme (0 syntax errors).
- Executed 23-assertion automated test suite inside Docker container:
  - `NCTB_LH_VERSION` reports `0.4.0`.
  - Verified `wp_nctb_lesson_activities` table exists in MySQL.
  - Verified 14 activity types registered and validated.
  - Tested XSS sanitization of title, HTML content, and metadata.
  - Verified sample lesson ID 15 has 14 seeded activity blocks in correct order.
  - Tested REST `GET /curriculum/lesson/15` returns 200 with 14 activities.
  - Tested REST `GET /curriculum/lesson/15/activities` returns 200 with 14 activities.
  - Tested REST 404 on nonexistent lesson ID.
  - Tested REST 401/403 unauthorized rejection on `POST /curriculum/lesson/15/activities/order`.
- Front-End HTTP curl checks:
  - `http://localhost:8080/?p=15` returns HTTP 200 (following canonical 301).
  - Verified HTML contains stepper panel, progress status bar, step pills, vocabulary grid cards, quiz placeholder, tutor callout bar, and `lesson-interactive.js`.
- Regression HTTP curl checks:
  - Homepage `/` → 200
  - Books archive `/book/` → 200
  - Onboarding `/onboarding/` → 200
  - Dashboard `/dashboard/` → 200
- Resolved carry-over debt: updated stale page template meta reference from deleted `student-dashboard.php` to `page-dashboard.php`.

## 9. Test results
All 23 automated tests passed. All front-end and REST endpoints return HTTP 200. Zero PHP errors, warnings, or notices.

## 10. Screens/pages to manually review
- wp-admin: Edit Lesson ("Lesson 1 — Nelson Mandela...") → review "Interactive Lesson Activities (Phase 4)" meta box (add/reorder/collapse/edit blocks).
- Front-end: http://localhost:8080/?p=15 (or `/lesson/lesson-1-nelson-mandela-from-prisoner-to-president-sample/`) on desktop and mobile viewports.

## 11. Known problems / technical debt
- None. Carry-over template debt from Phase 1 was resolved during this phase.

## 12. Setup or migration steps to perform
- On existing installations: migrations run automatically on `admin_init` (or plugin activation). Visit Settings → Permalinks once if rewrite rules need flushing.
- On fresh installations: the activator handles table creation, CPT registration, sample lesson & 14-activity block seeding, and rewrite flush.

## 13. Rollback notes
- To roll back Phase 4: drop table `wp_nctb_lesson_activities`, revert git commits to Phase 3 state (`v0.3.0`), and delete option `nctb_lh_sample_activities_seeded`.
- No WordPress core files were modified.

## 14. What is intentionally NOT built yet
- Phase 5: Practice and question engine (interactive questions, attempts, progressive hints, marking service).
- Phase 6: Progress, mastery, mistakes, and spaced revision engine.
- Phase 9: Conversational AI tutor server adapter and chat interface.
- Only ONE gold-standard prototype lesson's content was entered per the plan — no full course content.

**STOP HERE. NEXT PHASE NOT STARTED.**
