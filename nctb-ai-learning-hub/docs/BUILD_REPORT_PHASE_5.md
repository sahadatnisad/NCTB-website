# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 5 — Practice and question engine
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash)
**Environment:** Local Docker (`docker-compose.yml`) — `nctb-wordpress` + `nctb-mysql`, site at http://localhost:8080
**WordPress version:** 7.0.4
**PHP version:** 8.3.33
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) 0.5.0
**Plugin version:** NCTB Learning Hub 0.5.0

## 1. What was built
Turned lessons into active practice with deterministic logic — **without AI**:
- **4 Supported Question Types:** Multiple Choice Questions (`mcq`), Single fill-in-the-blank (`fill_in_blank`), Short text answer (`short_answer`), and Error correction (`error_correction`).
- **Central Marking Service (`NCTB_Marking_Service`):** Single source of truth for evaluation without AI. Handles option matching, text normalization (trim, punctuation, Unicode/Bengali tolerance), pipe-separated multiple accepted variants (`27 | twenty seven | 27 years`), and educational scoring with hint penalty adjustments.
- **Progressive Hint Engine (`NCTB_Hint_Service`):** Three-tier hint hierarchy (Level 1 subtle orientation clue → Level 2 contextual clue → Level 3 targeted hint) that scaffolds student thinking without immediately giving away the answer.
- **Admin Practice Questions Manager (`NCTB_Question_Admin`):** Dedicated admin screen under Lessons → Practice Questions (`edit.php?post_type=nctb_lesson&page=nctb-questions`) to create, edit, filter, and delete questions with dynamic MCQ option builders, progressive hints, explanations, and concept tags.
- **Practice REST API (`NCTB_Practice_REST`):** Endpoints under `nctb/v1/practice/*` for fetching lesson questions (with options stripped of `is_correct` to prevent cheating), submitting answers for instant marking, requesting progressive hints, and querying student attempt history with per-student isolation.
- **Gold-Standard Sample Practice Questions:** Seeded 5 authentic practice questions for the prototype lesson (Nelson Mandela).
- **Interactive Practice Quiz Theme Component:** Replaced the static quiz placeholder with a live, responsive stepper quiz engine featuring instant marking, hint reveals, error retries, and a mastery completion summary screen.

## 2. Files created/changed
**Plugin — new:**
- `includes/class-nctb-question-types.php` — Registry of question types, difficulty levels, verification statuses, and input sanitization helpers.
- `includes/class-nctb-practice-data.php` — Database data service for questions, MCQ options, question-concept links, and student attempts.
- `includes/class-nctb-marking-service.php` — Central marking service for evaluating submissions without AI.
- `includes/class-nctb-hint-service.php` — Progressive hint disclosure engine.
- `includes/class-nctb-practice-rest.php` — REST API controller for practice and questions (`/nctb/v1/practice/*`).
- `includes/class-nctb-question-admin.php` — Admin practice question manager screen.

**Plugin — changed:**
- `nctb-learning-hub.php` — Bumped version to `0.5.0`; required the 6 new Phase 5 classes.
- `includes/class-nctb-migrations.php` — Added migration step `0.5.0` creating `wp_nctb_questions`, `wp_nctb_question_options`, `wp_nctb_question_concepts`, and `wp_nctb_attempts` via `dbDelta`.
- `includes/class-nctb-plugin.php` — Initialized `NCTB_Question_Admin` in wp-admin and registered `NCTB_Practice_REST`.
- `includes/class-nctb-curriculum-seeder.php` — Added `maybe_seed_questions` to seed 5 gold-standard practice questions.

**Theme — changed:**
- `single-nctb_lesson.php` — Rendered live Interactive Practice Quiz Engine inside the quiz activity block with question stepper, option selectors, hint containers, feedback banners, and summary cards.
- `js/lesson-interactive.js` — Added client-side practice engine logic for submitting answers, requesting progressive hints from REST API, error retries, score calculation, and retaking quizzes.
- `css/curriculum.css` — Added responsive styles for question cards, MCQ option pills, text inputs, progressive hints, feedback banners, and the completion summary card.
- `style.css` — Bumped theme version to `0.5.0`.

## 3. Database/schema changes
Migration `0.5.0` creates (idempotent, dbDelta):
- `wp_nctb_questions` (id, lesson_id, question_type, prompt, content, difficulty, correct_answer, explanation, hint_1, hint_2, hint_3, source_type, verification_status, meta_data, sort_order, is_active, created_at, updated_at)
- `wp_nctb_question_options` (id, question_id, option_key, option_text, is_correct, feedback, sort_order)
- `wp_nctb_question_concepts` (id, question_id, concept_id — unique question+concept link)
- `wp_nctb_attempts` (id, user_id, question_id, lesson_id, given_answer, is_correct, score, hints_used, attempt_number, feedback_given, created_at)

## 4. Admin features added
- Submenu page under Lessons: **Practice Questions** (`edit.php?post_type=nctb_lesson&page=nctb-questions`).
- Filter questions by Lesson.
- List view with Type, Prompt preview, Lesson, Difficulty badge, Hint counts, and Edit/Delete actions.
- Add / Edit question form with:
  - Target Lesson selector.
  - Question Type selector (MCQ, Fill in blank, Short answer, Error correction).
  - Rich Prompt and Context textareas.
  - Difficulty selector (Easy, Medium, Hard).
  - Target Correct Answer input (supports multiple valid variants via `|`).
  - Dynamic 4-option MCQ table with radio selector for the correct choice and individual feedback fields.
  - Progressive Hint inputs (Hint 1, Hint 2, Hint 3).
  - Full Explanation textarea.
  - Multi-concept link checkboxes.
- Secure saving with nonce verification (`nctb_question_admin_action`), `edit_posts` capability check, and XSS sanitization.

## 5. Student-facing features added
- **Live Practice Quiz Component** embedded directly on the single lesson page:
  - Dynamic Question Stepper (e.g. "Question 1 of 5").
  - Interactive Question Cards with Type badge and Difficulty badge.
  - MCQ selectable radio pill buttons with highlight states.
  - Text input fields for fill-in-the-blank, short answer, and error correction.
  - **Progressive Hint Button:** Fetches and reveals scaffolded Level 1/2 hints on demand.
  - **Instant Marking & Feedback:** Instant evaluation banner (Green success / Red try-again) with score badges and full explanations.
  - **Error Retry Workflow:** Allows students to correct mistakes and retry.
  - **Quiz Completion Summary:** Displays total score earned, percentage accuracy, feedback message, and a Retake Quiz button.

## 6. REST/AJAX endpoints added
Under namespace `nctb/v1`:
- `GET /nctb/v1/practice/lesson/{id}/questions` (public read-only for published lessons; strips `is_correct` from options on client payload to prevent inspecting answers).
- `POST /nctb/v1/practice/submit` (evaluates answer via central marking service, stores attempt in `wp_nctb_attempts`, returns score, feedback, and explanation).
- `POST /nctb/v1/practice/hint` (returns progressive hint level data).
- `GET /nctb/v1/practice/attempts` (returns authenticated student's practice history with cross-student isolation).

## 7. Security controls added
- `is_correct` flag is strictly stripped on question retrieval endpoints.
- Submissions and hint requests require valid parameters and run through sanitizers.
- All database queries use `$wpdb->prepare()`.
- Question administration is guarded by `edit_posts` capability checks and nonce verification.
- Cross-student attempt isolation enforced (students cannot view or tamper with other students' attempts).

## 8. Tests performed (runtime, in Docker)
- `php -l` executed on all PHP files in plugin and theme (0 syntax errors).
- Executed 32-assertion automated test suite inside Docker container:
  - Verified `NCTB_LH_VERSION` reports `0.5.0`.
  - Verified all 4 tables exist in MySQL (`wp_nctb_questions`, `wp_nctb_question_options`, `wp_nctb_question_concepts`, `wp_nctb_attempts`).
  - Tested 4 question types registry and validation.
  - Tested prompt and answer XSS sanitization.
  - Tested Central Marking Service on MCQ, Fill in Blank (multiple variants + punctuation tolerance), Short Answer (case-insensitivity), and Error Correction.
  - Tested Progressive Hint Service (Level 1, Level 2, and availability flags).
  - Tested REST `GET /practice/lesson/{id}/questions` returns 200 with answers securely hidden.
  - Tested REST `POST /practice/submit` marks answer and persists attempt to `wp_nctb_attempts`.
  - Tested REST `POST /practice/hint` returns Level 1 hint.
- Front-End HTTP curl checks:
  - `http://localhost:8080/?p=15` returns HTTP 200.
  - Verified HTML contains `nctb-practice-engine`, question cards, MCQ option lists, submit buttons, hint buttons, and the summary card.
- Regression HTTP curl checks:
  - Homepage `/` → 200
  - Books archive `/book/` → 200
  - Onboarding `/onboarding/` → 200
  - Dashboard `/dashboard/` → 200

## 9. Test results
All 32 automated tests passed (0 failures). All front-end and REST endpoints return HTTP 200.

## 10. Screens/pages to manually review
- wp-admin: Lessons → Questions (`edit.php?post_type=nctb_lesson&page=nctb-questions`) to review the practice question manager (Add/Edit/Delete/Filter).
- Front-end: http://localhost:8080/?p=15 (Activity #13 — Practice Quiz) to test answering questions, requesting hints, retrying errors, and completing the quiz.

## 11. Known problems / technical debt
- None.

## 12. Setup or migration steps to perform
- Migrations run automatically on `admin_init`.
- Sample practice questions are automatically seeded for the prototype lesson.

## 13. Rollback notes
- Drop tables `wp_nctb_questions`, `wp_nctb_question_options`, `wp_nctb_question_concepts`, `wp_nctb_attempts`, delete option `nctb_lh_sample_questions_seeded`, and revert git commits to Phase 4 state (`v0.4.0`).

## 14. What is intentionally NOT built yet
- Phase 6: Progress, mastery, mistakes, and spaced revision engine.
- Phase 7: Functional student dashboard with real metrics.
- Phase 8: Payments and entitlements.
- Phase 9: Contextual AI tutor engine.

**STOP HERE. NEXT PHASE NOT STARTED.**
