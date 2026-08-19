# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 10 — Writing, listening & speaking enhancements
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash)
**Environment:** Local Docker (`docker-compose.yml`) — `nctb-wordpress` + `nctb-mysql`, site at http://localhost:8080
**WordPress version:** 7.0.4
**PHP version:** 8.3.33
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) 0.10.0
**Plugin version:** NCTB Learning Hub 0.10.0

## 1. What was built
Expanded the curriculum platform from grammar and reading practice into complete, four-skills English language mastery:
- **Writing Process Engine (`NCTB_Writing_Service`):**
  - Multi-stage iterative writing pipeline: `Task` → `Brainstorm` → `Draft` → `AI Feedback` → `Revision` → `Final`.
  - Multi-criteria feedback breakdown:
    1. **Structure & Coherence** (0-10 score + paragraph guidance).
    2. **Grammar & Mechanics** (0-10 score + tense/agreement checks).
    3. **Vocabulary & Expression** (0-10 score + lesson-anchored suggestions).
    4. **Revision Action Plan** (specific actionable steps).
  - Drafts and submissions stored in `wp_nctb_writing_submissions` (**private by default**).
- **Listening Activity Player (`NCTB_Listening_Service`):**
  - Native audio player for historical audio excerpts and dialogue passages.
  - Audio duration and transcript viewer with toggleable transcript display.
- **Speaking Practice Recorder (`NCTB_Speaking_Service`):**
  - Interactive voice recorder widget with elapsed timer and submission handling.
  - Generates encouraging formative feedback on delivery, pace, and pronunciation.
  - **Prominent Disclaimer:** Clearly labeled as practice feedback, **NOT an official board examination score**.
- **Interactive UI Workbenches in Theme:** Embedded specialized workbenches for `writing`, `listening`, and `speaking` activity blocks in `single-nctb_lesson.php`.
- **Skills REST API (`NCTB_Skills_REST`):** Endpoints under `/nctb/v1/skills/*`.

## 2. Files created/changed
**Plugin — new:**
- `includes/class-nctb-writing-service.php` — Writing draft management, rubric evaluation, and stage transition service.
- `includes/class-nctb-listening-service.php` — Audio metadata and transcript service.
- `includes/class-nctb-speaking-service.php` — Speaking attempt logger and formative practice feedback service.
- `includes/class-nctb-skills-rest.php` — REST API controller (`/nctb/v1/skills/*`).

**Plugin — changed:**
- `nctb-learning-hub.php` — Bumped version to `0.10.0`; required Phase 10 classes.
- `includes/class-nctb-migrations.php` — Added migration step `0.10.0` creating `wp_nctb_writing_submissions` and `wp_nctb_speaking_submissions`.
- `includes/class-nctb-plugin.php` — Registered `NCTB_Skills_REST` routes.

**Theme — changed:**
- `single-nctb_lesson.php` — Added interactive Writing Workbench, Listening Player, and Speaking Practice widgets.
- `js/lesson-interactive.js` — Added client-side draft saving, AI feedback requesting, final submitting, transcript toggling, and speaking recording timer.
- `css/curriculum.css` — Added responsive styling for writing stage pills, textareas, feedback boxes, audio cards, and speaking badges.
- `style.css` — Bumped theme version to `0.10.0`.

## 3. Database/schema changes
Migration `0.10.0` creates (idempotent, dbDelta):
- `wp_nctb_writing_submissions` (id, user_id, lesson_id, activity_id, stage, draft_text, feedback_text, feedback_scores, status, created_at, updated_at)
- `wp_nctb_speaking_submissions` (id, user_id, lesson_id, activity_id, audio_url, duration_seconds, transcript_text, feedback_text, status, created_at)

## 4. Admin features added
- Database storage and audit records for student writing drafts and speaking submissions.

## 5. Student-facing features added
- **Writing Workbench:** Live word counter, Draft saving, multi-criteria AI rubric breakdown, and Revision / Final submission flow.
- **Listening Player:** Clean audio player with duration metadata and toggleable transcript.
- **Speaking Recorder:** Real-time recording timer, submission handling, and formative pronunciation feedback.

## 6. REST/AJAX endpoints added
Under namespace `nctb/v1/skills`:
- `POST /nctb/v1/skills/writing/draft` — Saves student draft.
- `POST /nctb/v1/skills/writing/feedback` — Evaluates draft and returns multi-criteria scores & advice.
- `POST /nctb/v1/skills/writing/final` — Marks writing activity completed.
- `GET  /nctb/v1/skills/writing/submission` — Retrieves current draft and stage.
- `POST /nctb/v1/skills/speaking/submit` — Records voice attempt and returns practice feedback.

## 7. Security controls added
- Student writing and speaking submissions are private by default (student user isolation).
- Input sanitization with `wp_kses_post`, `sanitize_textarea_field`, and `sanitize_key`.
- Prepared database statements with `$wpdb->prepare()`.

## 8. Tests performed (runtime, in Docker)
- `php -l` executed on all 74 PHP files across plugin and theme (0 syntax errors).
- Executed 17-assertion automated test suite inside Docker container:
  - Verified `NCTB_LH_VERSION` reports `0.10.0`.
  - Verified `wp_nctb_writing_submissions` and `wp_nctb_speaking_submissions` exist.
  - Tested writing draft persistence.
  - Tested multi-criteria feedback generation (Structure, Grammar, Vocabulary).
  - Tested final submission status update.
  - Tested listening audio track and transcript retrieval.
  - Tested speaking recording submission and non-official disclaimer.
  - Tested REST endpoints (`/writing/draft`, `/writing/feedback`, `/speaking/submit`).
- Front-End HTTP curl checks on all 9 routes (all returning 200).

## 9. Test results
All 17 automated tests passed (0 failures). All 9 platform routes return HTTP 200.

## 10. Screens/pages to manually review
- Interactive Lesson with Writing, Listening & Speaking activities: `http://localhost:8080/?p=15`

## 11. Known problems / technical debt
- None.

## 12. Setup or migration steps to perform
- Migrations run automatically on `admin_init`.

## 13. Rollback notes
- Drop tables `wp_nctb_writing_submissions`, `wp_nctb_speaking_submissions` and revert git commits to Phase 9 state (`v0.9.0`).

## 14. What is intentionally NOT built yet
- Phase 11: Authentic board-question database.
- Phase 12: Board pattern analytics.
- Phase 13: Full English curriculum content library.

**STOP HERE. NEXT PHASE NOT STARTED.**
