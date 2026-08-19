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

**PHASE 5 — Practice and question engine**

> Read the "PHASE 5" section in [README.md](./README.md#phase-5--practice-and-question-engine) for full requirements before building.
> Turn lessons into active practice — **without AI**. Build question types (MCQ, single fill-in-the-blank, short text answer, error correction), records supporting difficulty, explanations, progressive hint levels, source type, attempts table, central marking service, nonces & permission callbacks.

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
| 5 | Practice and question engine | 🔜 **NEXT** |
| 6 | Progress, mastery, mistakes, spaced revision | ⬜ Not started |
| 7 | Functional student dashboard | ⬜ Not started |
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
- **What was built:** `nctb_book` / `nctb_unit` / `nctb_lesson` CPTs + 6 taxonomies (class, subject, paper, curriculum version, session, topic); editor meta boxes for unit→book / lesson→unit relationships, learning outcomes, and concept links; `menu_order` sequencing with admin columns; **Concepts** admin screen; 3 custom tables via migration `0.3.0` (`nctb_concepts`, `nctb_learning_outcomes`, `nctb_lesson_concepts`) with the `NCTB_Curriculum_Data` service; read-only REST `nctb/v1/curriculum/*`; one-time sample Book→Unit→Lesson seeder; theme browse templates (archive + single book/unit/lesson) + `curriculum.css`; header "Learn" link. Plugin bumped to 0.3.0.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_3.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_3.md)

### ✅ Phase 4 — One gold-standard interactive lesson
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** 14 standard reusable activity blocks (objective, warm-up, reading passage, vocabulary, grammar focus, examples, guided practice with interactive hints/reveals, independent practice with self-check checklist, writing task with live word counter and model response, listening with audio player & transcript, speaking with 2-minute timer, lesson summary, quiz placeholder for Phase 5, contextual AI tutor callout for Phase 9); admin lesson activity editor meta box with reordering and type selector; custom table `wp_nctb_lesson_activities` via migration `0.4.0`; REST endpoints (`/curriculum/lesson/{id}/activities`, reorder route, enriched lesson details); complete authentic NCTB sample lesson seeded; mobile-first stepper and progress bar with state resumption from URL hash and localStorage in generic `single-nctb_lesson.php` (no lesson-specific PHP templates).
- **Verified (runtime, Docker):** 23 automated unit/integration tests passing (0 failures); all PHP files linted clean; HTTP 200 on lesson, books, units, dashboard, onboarding, and homepage; activity ordering, sanitization, and REST routes verified.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_4.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_4.md)
