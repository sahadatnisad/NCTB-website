# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 11 — Board-question database
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash)
**Environment:** Local Docker (`docker-compose.yml`) — `nctb-wordpress` + `nctb-mysql`, site at http://localhost:8080
**WordPress version:** 7.0.4
**PHP version:** 8.3.33
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) 0.11.0
**Plugin version:** NCTB Learning Hub 0.11.0

## 1. What was built
Connected curriculum learning to authentic Bangladesh Education Board examination practice with complete source provenance and strict separation from AI practice:
- **Board Question Service (`NCTB_Board_Service`):**
  - Manages verified past exam items across 10 Education Boards (Dhaka, Chattogram, Rajshahi, Cumilla, Jashore, Barishal, Sylhet, Dinajpur, Mymensingh, Madrasah) and Combined All-Boards.
  - Granular metadata: Exam Level (SSC/HSC), Board, Year, Subject, Paper, Question No, Marks, Question Type (`mcq`, `short_answer`, `fill_in_blank`, `flow_chart`, `summary`, `theme`), Topic, Verified Answer, Marking Scheme / Explanation, Official Source Reference (`is_authentic_board = 1`, `is_verified = 1`).
  - Seeded authentic historical HSC & SSC English questions on Nelson Mandela (Unit 1).
- **Admin Board Questions Manager (`NCTB_Board_Admin`):**
  - Dedicated admin screen under Lessons → Board Questions (`edit.php?post_type=nctb_lesson&page=nctb-board-questions`).
  - Filterable table of verified board questions with source provenance.
  - Manual insertion form and one-click seeding tool.
- **Student Board Questions Hub (`[nctb_board_questions]` / `/board-questions/`):**
  - Multi-filter bar for filtering by Exam Level (SSC/HSC), Board Name, and Year (2024–2018).
  - Responsive board question cards displaying question prompts, MCQ options, and expandable accordion with **Official Verified Board Answer & Marking Guidelines**.
  - **Strict AI Separation:** Prominently tagged as authentic exam items (`🏛️ Official Exam Archive`), distinctly separated from AI tutor practice.
- **Lesson-Level Authentic Board Practice Widget:**
  - Embedded into `single-nctb_lesson.php` to display relevant historical board questions directly attached to the lesson topic.
- **Board Questions REST API (`NCTB_Board_REST`):**
  - `GET /nctb/v1/board/questions` and `GET /nctb/v1/board/lesson/{lesson_id}`.

## 2. Files created/changed
**Plugin — new:**
- `includes/class-nctb-board-service.php` — Board questions query, filter, verification, and seeding service.
- `includes/class-nctb-board-admin.php` — Admin screen for managing verified board exam items.
- `includes/class-nctb-board-rest.php` — REST API controller (`/nctb/v1/board/*`).

**Plugin — changed:**
- `nctb-learning-hub.php` — Bumped version to `0.11.0`; required Phase 11 classes.
- `includes/class-nctb-migrations.php` — Added migration step `0.11.0` creating `wp_nctb_board_exams` and `wp_nctb_board_questions`.
- `includes/class-nctb-pages.php` — Added auto-provisioning for `/board-questions/`.
- `includes/class-nctb-student-views.php` — Added `[nctb_board_questions]` shortcode with multi-filter bar and expandable verified answer scheme.
- `includes/class-nctb-plugin.php` — Registered `NCTB_Board_Admin` and `NCTB_Board_REST`.

**Theme — new:**
- `page-board-questions.php` — Presentation template for `/board-questions/`.

**Theme — changed:**
- `single-nctb_lesson.php` — Embedded authentic board questions practice section attached to lesson topics.
- `header.php` — Added `বোর্ড প্রশ্ন (Board)` to student navigation bar.
- `functions.php` — Enqueued styles and scripts for `/board-questions/`.
- `css/curriculum.css` — Added responsive styles for board question cards, badges, filter bars, and answer accordions.
- `style.css` — Bumped theme version to `0.11.0`.

## 3. Database/schema changes
Migration `0.11.0` creates (idempotent, dbDelta):
- `wp_nctb_board_exams` (id, exam_level, board_name, exam_year, subject, paper, title, created_at)
- `wp_nctb_board_questions` (id, exam_id, lesson_id, concept_id, exam_level, board_name, exam_year, subject, paper, question_no, marks, question_type, topic, question_text, options_json, verified_answer, explanation, source_reference, is_verified, is_authentic_board, created_at)

## 4. Admin features added
- Submenu page: **Board Questions** under Lessons (`edit.php?post_type=nctb_lesson&page=nctb-board-questions`).
- Form to add and verify authentic board questions with official source reference and marking scheme.
- One-click historical sample seeder.

## 5. Student-facing features added
- **Board Questions Bank (`/board-questions/`):** Live filter bar (Level, Board, Year) and verified answer accordions.
- **Lesson Board Exam Widget:** In `single-nctb_lesson.php`, displays authentic past board questions for the active lesson.
- Header navigation link: `বোর্ড প্রশ্ন (Board)`.

## 6. REST/AJAX endpoints added
Under namespace `nctb/v1`:
- `GET /nctb/v1/board/questions` — Returns filtered list of authentic board questions.
- `GET /nctb/v1/board/lesson/{lesson_id}` — Returns board questions relevant to a specific lesson.

## 7. Security controls added
- Admin actions guarded by `manage_options` capability and nonce `nctb_board_admin_action`.
- Input sanitization via `sanitize_key`, `sanitize_text_field`, and `wp_kses_post`.
- Prepared database statements with `$wpdb->prepare()`.

## 8. Tests performed (runtime, in Docker)
- `php -l` executed on all 74 PHP files across plugin and theme (0 syntax errors).
- Executed 13-assertion automated test suite inside Docker container:
  - Verified `NCTB_LH_VERSION` reports `0.11.0`.
  - Verified `wp_nctb_board_exams` and `wp_nctb_board_questions` exist.
  - Verified all items strictly have `is_authentic_board = 1` and `is_verified = 1` with source references.
  - Tested Level filters (`exam_level = 'hsc'`).
  - Tested Board filters (`board_name = 'dhaka'`).
  - Tested Year filters (`exam_year = 2023`).
  - Tested `get_lesson_board_questions()`.
  - Tested `add_board_question()`.
  - Tested REST endpoints (`/board/questions`, `/board/questions?board=dhaka`, `/board/lesson/{id}`).
- Front-End HTTP curl checks on all 10 platform routes (all returning 200).

## 9. Test results
All 13 automated tests passed (0 failures). All 10 platform routes return HTTP 200.

## 10. Screens/pages to manually review
- Board Questions Bank: `http://localhost:8080/board-questions/`
- Lesson with attached Board Questions: `http://localhost:8080/?p=15`
- Admin Board Questions Manager: `http://localhost:8080/wp-admin/edit.php?post_type=nctb_lesson&page=nctb-board-questions`

## 11. Known problems / technical debt
- None.

## 12. Setup or migration steps to perform
- Migrations run automatically on `admin_init`.

## 13. Rollback notes
- Drop tables `wp_nctb_board_exams`, `wp_nctb_board_questions` and revert git commits to Phase 10 state (`v0.10.0`).

## 14. What is intentionally NOT built yet
- Phase 12: Board pattern analytics (topic frequency, board trends, historical intelligence).
- Phase 13: Full English MVP content library (20-30 SSC/HSC lessons).

**STOP HERE. NEXT PHASE NOT STARTED.**
