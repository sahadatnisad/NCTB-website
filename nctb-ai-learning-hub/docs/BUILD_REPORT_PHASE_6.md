# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 6 — Progress, mastery, mistakes, spaced revision
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash)
**Environment:** Local Docker (`docker-compose.yml`) — `nctb-wordpress` + `nctb-mysql`, site at http://localhost:8080
**WordPress version:** 7.0.4
**PHP version:** 8.3.33
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) 0.6.0
**Plugin version:** NCTB Learning Hub 0.6.0

## 1. What was built
Made the platform remember learning with intelligent retention algorithms:
- **Lesson Progress Tracking (`NCTB_Progress_Service`):** Tracks student step position (`last_activity_step`), completed activities array, and status (`in_progress`, `completed`).
- **Concept Mastery Engine (`NCTB_Mastery_Service`):** Central service that recalculates concept-level scores and levels (`novice`, `developing`, `proficient`, `mastered`) upon practice submissions.
- **Strict Separation of Completion vs. Mastery:** Viewing or completing all 14 activities in a lesson marks the lesson as `completed`, but does NOT grant automatic concept mastery — mastery is earned through retained practice accuracy.
- **Smart Mistake Notebook (`NCTB_Mistakes_Service`):** Automatically captures incorrect question attempts into `wp_nctb_mistakes`, logs error counts and streaks, schedules immediate review for the following day, and transitions mastered mistakes out of the active list when retried correctly.
- **Spaced Repetition & Revision Queue (`NCTB_Spaced_Revision_Service`):** Implements an interval schedule (1 day → 3 days → 7 days → 14 days → 30 days) that dynamically calculates due dates and queues items for daily student review.
- **Student Screens & Shortcodes:**
  - `[nctb_mistakes]` on `/mistakes/` — Interactive Mistake Notebook with error counts, wrong answers, explanations, and retry buttons.
  - `[nctb_revision_due]` on `/revision/` — Spaced Revision Queue showing items due today with interval and streak badges.
  - `[nctb_progress]` on `/progress/` — Learning Progress dashboard with completed lessons KPI, questions attempted, active mistakes count, revisions due, and a concept mastery breakdown table.
- **Progress REST API (`NCTB_Progress_REST`):** Endpoints under `nctb/v1/*` for saving step progress, retrieving summaries, fetching mistakes, resolving mistakes, and completing spaced reviews.

## 2. Files created/changed
**Plugin — new:**
- `includes/class-nctb-progress-service.php` — Lesson progress & step position data service.
- `includes/class-nctb-mastery-service.php` — Concept mastery calculation & levels service.
- `includes/class-nctb-mistakes-service.php` — Smart mistake notebook with error streaks & auto-graduation.
- `includes/class-nctb-spaced-revision-service.php` — Spaced repetition interval scheduling.
- `includes/class-nctb-progress-rest.php` — REST API controller for progress, mastery, mistakes, and revision.
- `includes/class-nctb-student-views.php` — Shortcodes renderer for student screens (`[nctb_mistakes]`, `[nctb_revision_due]`, `[nctb_progress]`).

**Plugin — changed:**
- `nctb-learning-hub.php` — Bumped version to `0.6.0`; required Phase 6 classes.
- `includes/class-nctb-migrations.php` — Added migration step `0.6.0` creating `wp_nctb_progress`, `wp_nctb_mastery`, `wp_nctb_mistakes`, and `wp_nctb_review_schedule`.
- `includes/class-nctb-pages.php` — Added auto-provisioning for `/mistakes/`, `/revision/`, and `/progress/`.
- `includes/class-nctb-plugin.php` — Initialized `NCTB_Student_Views` and registered `NCTB_Progress_REST`.
- `includes/class-nctb-practice-rest.php` — Connected `submit_answer` to automatically invoke `NCTB_Mistakes_Service` and `NCTB_Mastery_Service`.

**Theme — new:**
- `page-mistakes.php` — Template for `/mistakes/`.
- `page-revision.php` — Template for `/revision/`.
- `page-progress.php` — Template for `/progress/`.

**Theme — changed:**
- `header.php` — Added navigation links for Mistakes (`/mistakes`), Revision (`/revision`), and Progress (`/progress`).
- `js/lesson-interactive.js` — Added backend step progress synchronization, mistake resolution, and revision completion AJAX handlers.
- `css/curriculum.css` — Added responsive styles for KPI statistics cards, mistake cards, revision cards, and concept mastery badges.
- `functions.php` — Enqueued styles and interactive scripts on student study pages.
- `style.css` — Bumped theme version to `0.6.0`.

## 3. Database/schema changes
Migration `0.6.0` creates (idempotent, dbDelta):
- `wp_nctb_progress` (id, user_id, lesson_id, unit_id, book_id, status, last_activity_step, completed_activities, completed_at, updated_at, created_at)
- `wp_nctb_mastery` (id, user_id, concept_id, mastery_score, mastery_level, total_attempts, correct_attempts, last_attempt_at, updated_at)
- `wp_nctb_mistakes` (id, user_id, question_id, lesson_id, last_attempt_id, wrong_answer, status, error_count, correct_streak, last_error_at, resolved_at)
- `wp_nctb_review_schedule` (id, user_id, item_type, item_id, lesson_id, interval_days, ease_factor, repetition_count, due_date, status, last_reviewed_at, created_at)

## 4. Admin features added
- Database tables for student learning memory registered with indexing.
- Concepts linked to practice questions now reflect live aggregated mastery scores.

## 5. Student-facing features added
- **My Mistake Notebook (`/mistakes/`):**
  - Lists all active mistakes with error counts, wrong submissions, explanations, and retry action.
  - Interactive "Mark as Mastered" button with smooth card dismiss animation.
  - Empty state congratulatory banner when all mistakes are resolved.
- **Spaced Revision Queue (`/revision/`):**
  - Displays questions and concepts due for review today based on scientific spaced repetition.
  - Shows repetition streak count and interval days.
  - "Mark Reviewed Today" button advances interval to the next spaced stage.
- **Learning Progress & Mastery Dashboard (`/progress/`):**
  - KPI cards: Completed Lessons, Total Questions Attempted, Active Mistakes, Revisions Due.
  - Concept Mastery List with color-coded badges (`Novice` <40%, `Developing` 40-69%, `Proficient` 70-89%, `Mastered` >=90%).
  - Progress bar and accuracy percentages per concept.

## 6. REST/AJAX endpoints added
Under namespace `nctb/v1`:
- `POST /nctb/v1/progress/step` — Saves current lesson step position and updates completion.
- `GET /nctb/v1/progress/summary` — Retrieves student's overall progress KPI metrics and concept mastery list.
- `GET /nctb/v1/mistakes` — Fetches active mistakes with strict student isolation.
- `POST /nctb/v1/mistakes/resolve` — Resolves a mistake item.
- `GET /nctb/v1/revision/due` — Fetches items due for spaced revision.
- `POST /nctb/v1/revision/complete` — Completes a revision item and advances interval.

## 7. Security controls added
- All queries parameterized with `$wpdb->prepare()`.
- Strict per-student data isolation enforced across all progress, mastery, mistake, and revision queries.
- Input sanitization on all step progress and resolution parameters.

## 8. Tests performed (runtime, in Docker)
- `php -l` executed on all 52 PHP files across plugin and theme (0 syntax errors).
- Executed 21-assertion automated test suite inside Docker container:
  - Verified `NCTB_LH_VERSION` reports `0.6.0`.
  - Verified all 4 tables exist (`wp_nctb_progress`, `wp_nctb_mastery`, `wp_nctb_mistakes`, `wp_nctb_review_schedule`).
  - Tested lesson step progress tracking (step 3 `in_progress`, step 14 `completed`).
  - Verified that lesson completion does NOT grant automatic concept mastery (strict separation check).
  - Tested mistake logging upon incorrect answer and automatic spaced review scheduling.
  - Tested mistake graduation upon correct retry (status transitions to `mastered`).
  - Tested concept mastery recalculation and level assignment.
  - Tested spaced repetition interval progression (1 day → 3 days).
  - Tested REST endpoints (`/progress/step`, `/progress/summary`, `/mistakes`, `/revision/due`).
- Front-End HTTP curl checks:
  - Homepage `/` → 200
  - Books archive `/book/` → 200
  - Lesson page `/?p=15` → 200
  - Mistakes page `/mistakes/` → 200
  - Revision page `/revision/` → 200
  - Progress page `/progress/` → 200
  - Dashboard `/dashboard/` → 200
  - Onboarding `/onboarding/` → 200

## 9. Test results
All 21 automated tests passed (0 failures). All 8 main front-end routes return HTTP 200.

## 10. Screens/pages to manually review
- Mistakes screen: http://localhost:8080/mistakes/
- Revision queue: http://localhost:8080/revision/
- Progress & Mastery: http://localhost:8080/progress/
- Single lesson practice quiz: http://localhost:8080/?p=15 (Activity #13) to test submitting wrong/right answers and watching them appear/clear in the mistake notebook.

## 11. Known problems / technical debt
- None.

## 12. Setup or migration steps to perform
- Migrations and page provisioning run automatically on `admin_init`.

## 13. Rollback notes
- Drop tables `wp_nctb_progress`, `wp_nctb_mastery`, `wp_nctb_mistakes`, `wp_nctb_review_schedule`, delete pages with slugs `mistakes`, `revision`, `progress`, and revert git commits to Phase 5 state (`v0.5.0`).

## 14. What is intentionally NOT built yet
- Phase 7: Functional student home study guide dashboard (assembling Phase 6 metrics into rules-based study recommendations).
- Phase 8: Payments and entitlements.
- Phase 9: Contextual AI tutor engine.

**STOP HERE. NEXT PHASE NOT STARTED.**
