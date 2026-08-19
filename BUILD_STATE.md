# BUILD STATE — NCTB AI Learning Hub

> **This is the memory of the project.** Any AI, on any device, reads this file
> **first** to know where the build stands, and updates it **last** before
> committing. Keep it accurate and short. The full plan is in [README.md](./README.md).

- **Repo:** https://github.com/sahadatnisad/NCTB-website
- **Branch:** main
- **Last updated:** 2026-08-19
- **Updated by:** Antigravity (Gemini 3.7 Flash)

---

## 👉 CURRENT PHASE TO BUILD

**PHASE 7 — Functional student dashboard**

> Read the "PHASE 7" section in [README.md](./README.md#phase-7--functional-student-dashboard) for full requirements before building.
> Make the site behave like a home study guide. Assemble Phase 6 learning metrics into rules-based study guidance: Continue Learning, Today's Practice, Revision Due, Needs Attention (active mistakes), recent progress, and My Book progress.

**Do ONLY this phase, then stop for human review.**

---

## Progress at a glance

| Phase | Title | Status |
|---|---|---|
| 0 | Safe WordPress development environment | ✅ Done |
| 1 | Visual shell and navigation | ✅ Done |
| 2 | Student accounts and onboarding | ✅ Done |
| 3 | Curriculum + Book + Unit + Lesson CMS | ✅ Done |
| 4 | One gold-standard interactive lesson | ✅ Done |
| 5 | Practice and question engine | ✅ Done |
| 6 | Progress, mastery, mistakes, spaced revision | ✅ Done |
| 7 | Functional student dashboard | 🔜 **NEXT** |
| 8 | Payments and entitlements | ⬜ Not started |
| 9 | Contextual AI tutor | ⬜ Not started |
| 10 | Writing, listening & speaking | ⬜ Not started |
| 11 | Board-question database | ⬜ Not started |
| 12 | Board pattern analytics | ⬜ Not started |
| 13 | English MVP content library | ⬜ Not started |
| 14 | Private beta: security, performance & QA | ⬜ Not started |
| 15 | Public English launch | ⬜ Not started |
| 16 | Complete English | ⬜ Not started |
| 17 | Add ICT | ⬜ Not started |
| 18 | Add Bangla & other subjects | ⬜ Not started |

Legend: ✅ done · 🔜 next · 🚧 in progress · ⬜ not started · ⚠️ blocked

---

## Completed phases log

### ✅ Phase 0 — Safe WordPress development environment
- **Done:** 2026-08-18 by Claude (Claude Code) & Antigravity.
- **What was built:** `nctb-learning-hub` plugin skeleton, versioned migration runner `NCTB_Migrations`, logger, activation/deactivation lifecycle, docs, git structure.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_0.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_0.md)

### ✅ Phase 1 — Visual shell and navigation
- **Done:** 2026-08-19.
- **What was built:** Standardized WordPress core layout with Docker compose integration (`http://localhost:8080`), responsive child theme (`nctb-child-theme`) with header, footer, mobile navigation, homepage template, and verified live plugin activation. All PHP files linted with 0 errors.

### ✅ Phase 2 — Student accounts and onboarding
- **Done:** 2026-08-19 by Antigravity.
- **What was built:** `nctb_student` role and capabilities, `NCTB_Student_Profile` metadata manager, `NCTB_Onboarding_REST` API controller (`nctb/v1/student/*`), mobile-first multi-step onboarding wizard (`[nctb_onboarding]` / `page-onboarding.php`), `[nctb_student_dashboard]` shortcode / `page-dashboard.php`, client-side state persistence with resumability, nonces and cross-student isolation. Verified with 9 automated tests passing.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_2.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_2.md)

### ✅ Phase 3 — Curriculum + Book + Unit + Lesson CMS
- **Done:** 2026-08-19 by Claude (Claude Code).
- **What was built:** `nctb_book` / `nctb_unit` / `nctb_lesson` CPTs + 6 taxonomies; editor meta boxes for relationships, learning outcomes, and concept links; `menu_order` sequencing; **Concepts** admin screen; 3 custom tables via migration `0.3.0` (`nctb_concepts`, `nctb_learning_outcomes`, `nctb_lesson_concepts`) with `NCTB_Curriculum_Data` service; read-only REST `nctb/v1/curriculum/*`; sample Book→Unit→Lesson seeder; theme browse templates + `curriculum.css`.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_3.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_3.md)

### ✅ Phase 4 — One gold-standard interactive lesson
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** 14 standard reusable activity blocks; admin lesson activity editor meta box with reordering; custom table `wp_nctb_lesson_activities` via migration `0.4.0`; REST endpoints; complete authentic NCTB sample lesson seeded; mobile-first stepper and progress bar with state resumption from URL hash and localStorage in generic `single-nctb_lesson.php`.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_4.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_4.md)

### ✅ Phase 5 — Practice and question engine
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** 4 question types (`mcq`, `fill_in_blank`, `short_answer`, `error_correction`); Central Marking Service (`NCTB_Marking_Service`) evaluating submissions deterministically without AI; Progressive Hint Service (`NCTB_Hint_Service`) with 3-level scaffolded hints; custom tables `wp_nctb_questions`, `wp_nctb_question_options`, `wp_nctb_question_concepts`, and `wp_nctb_attempts` via migration `0.5.0`; Admin Practice Questions manager; REST API endpoints; 5 sample practice questions seeded; interactive live practice quiz component.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_5.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_5.md)

### ✅ Phase 6 — Progress, mastery, mistakes, spaced revision
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Custom tables `wp_nctb_progress`, `wp_nctb_mastery`, `wp_nctb_mistakes`, and `wp_nctb_review_schedule` via migration `0.6.0`; lesson progress tracking with step positions and completion timestamps; concept mastery calculation service (`NCTB_Mastery_Service`) with strict completion vs. mastery separation; Smart Mistake Notebook service (`NCTB_Mistakes_Service`) with error tracking and auto-graduation; Spaced Repetition service (`NCTB_Spaced_Revision_Service`) with SM-2 interval ladder; student screens (`/mistakes/`, `/revision/`, `/progress/`) with responsive cards and KPIs; REST API endpoints under `nctb/v1/*`.
- **Verified (runtime, Docker):** 21 automated unit/integration tests passing (0 failures); all 52 PHP files linted clean; all 8 main routes returning HTTP 200.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_6.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_6.md)
