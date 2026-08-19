# BUILD STATE — NCTB AI Learning Hub

> **This is the memory of the project.** Any AI, on any device, reads this file
> **first** to know where the build stands, and updates it **last** before
> committing. Keep it accurate and short. The full plan is in [README.md](./README.md)
> and detailed checklists in [docs/plans/master-checklist.md](./docs/plans/master-checklist.md).

- **Repo:** https://github.com/sahadatnisad/NCTB-website
- **Branch:** main
- **Last updated:** 2026-08-19
- **Updated by:** Antigravity (Gemini 3.7 Flash)

---

## 👉 CURRENT PHASE TO BUILD

**PHASE 18 — Notes & explanations content type**

> Read the "PHASE 18" section in [docs/plans/01_BUILD_BLUEPRINT.md](./docs/plans/01_BUILD_BLUEPRINT.md) for full requirements before building.
> Goal: Implement `nctb_note` CPT with rich markdown/LaTeX math formulas, graphical diagram support, and printable/offline revision sheet views.

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
| 7 | Functional student dashboard | ✅ Done |
| 8 | Payments and entitlements | ✅ Done |
| 9 | Contextual AI tutor | ✅ Done |
| 10 | Writing, listening & speaking | ✅ Done |
| 11 | Board-question database | ✅ Done |
| 12 | Board pattern analytics | ✅ Done |
| 13 | English MVP content library | ✅ Done |
| 14 | Private beta: security, performance & QA | ✅ Done |
| 15 | Production launch readiness | ✅ Done |
| 16 | Teacher role & unified portal | ✅ Done |
| 17 | Modules & video-course system | ✅ Done |
| 18 | Notes & explanations content type | 🔜 **NEXT** |
| 19 | AI as a paid product (students + teachers) | ⬜ Not started |
| 20 | Add ICT (content-only proof) | ⬜ Not started |
| 21 | Maths engine extension | ⬜ Not started |
| 22 | Science subjects (Physics, Chemistry, Biology) | ⬜ Not started |
| 23 | Extend to Class 6–8 (JSC) | ⬜ Not started |
| 24 | Teacher content depth & resources | ⬜ Not started |

Legend: ✅ done · 🔜 next · 🚧 in progress · ⬜ not started · ⚠️ blocked

---

## Completed phases log

### ✅ Phase 0 — Safe WordPress development environment
- **Done:** 2026-08-18 by Claude (Claude Code) & Antigravity.
- **What was built:** `nctb-learning-hub` plugin skeleton, versioned migration runner `NCTB_Migrations`, logger, activation/deactivation lifecycle, docs, git structure.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_0.md`](./docs/build-history/BUILD_REPORT_PHASE_0.md)

### ✅ Phase 1 — Visual shell and navigation
- **Done:** 2026-08-19.
- **What was built:** Standardized WordPress core layout with Docker compose integration (`http://localhost:8080`), responsive child theme (`nctb-child-theme`) with header, footer, mobile navigation, homepage template, and verified live plugin activation. All PHP files linted with 0 errors.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_1.md`](./docs/build-history/BUILD_REPORT_PHASE_1.md)

### ✅ Phase 2 — Student accounts and onboarding
- **Done:** 2026-08-19 by Antigravity.
- **What was built:** `nctb_student` role and capabilities, `NCTB_Student_Profile` metadata manager, `NCTB_Onboarding_REST` API controller (`nctb/v1/student/*`), mobile-first multi-step onboarding wizard (`[nctb_onboarding]` / `page-onboarding.php`), `[nctb_student_dashboard]` shortcode / `page-dashboard.php`, client-side state persistence with resumability, nonces and cross-student isolation. Verified with 9 automated tests passing.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_2.md`](./docs/build-history/BUILD_REPORT_PHASE_2.md)

### ✅ Phase 3 — Curriculum + Book + Unit + Lesson CMS
- **Done:** 2026-08-19 by Claude (Claude Code).
- **What was built:** `nctb_book` / `nctb_unit` / `nctb_lesson` CPTs + 6 taxonomies; editor meta boxes for relationships, learning outcomes, and concept links; `menu_order` sequencing; **Concepts** admin screen; 3 custom tables via migration `0.3.0` (`nctb_concepts`, `nctb_learning_outcomes`, `nctb_lesson_concepts`) with `NCTB_Curriculum_Data` service; read-only REST `nctb/v1/curriculum/*`; sample Book→Unit→Lesson seeder; theme browse templates + `curriculum.css`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_3.md`](./docs/build-history/BUILD_REPORT_PHASE_3.md)

### ✅ Phase 4 — One gold-standard interactive lesson
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** 14 standard reusable activity blocks; admin lesson activity editor meta box with reordering; custom table `wp_nctb_lesson_activities` via migration `0.4.0`; REST endpoints; complete authentic NCTB sample lesson seeded; mobile-first stepper and progress bar with state resumption from URL hash and localStorage in generic `single-nctb_lesson.php`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_4.md`](./docs/build-history/BUILD_REPORT_PHASE_4.md)

### ✅ Phase 5 — Practice and question engine
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** 4 question types (`mcq`, `fill_in_blank`, `short_answer`, `error_correction`); Central Marking Service (`NCTB_Marking_Service`) evaluating submissions deterministically without AI; Progressive Hint Service (`NCTB_Hint_Service`) with 3-level scaffolded hints; custom tables `wp_nctb_questions`, `wp_nctb_question_options`, `wp_nctb_question_concepts`, and `wp_nctb_attempts` via migration `0.5.0`; Admin Practice Questions manager; REST API endpoints; 5 sample practice questions seeded; interactive live practice quiz component.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_5.md`](./docs/build-history/BUILD_REPORT_PHASE_5.md)

### ✅ Phase 6 — Progress, mastery, mistakes, spaced revision
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Custom tables `wp_nctb_progress`, `wp_nctb_mastery`, `wp_nctb_mistakes`, and `wp_nctb_review_schedule` via migration `0.6.0`; lesson progress tracking with step positions and completion timestamps; concept mastery calculation service (`NCTB_Mastery_Service`) with strict completion vs. mastery separation; Smart Mistake Notebook service (`NCTB_Mistakes_Service`) with error tracking and auto-graduation; Spaced Repetition service (`NCTB_Spaced_Revision_Service`) with SM-2 interval ladder; student screens (`/mistakes/`, `/revision/`, `/progress/`) with responsive cards and KPIs; REST API endpoints under `nctb/v1/*`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_6.md`](./docs/build-history/BUILD_REPORT_PHASE_6.md)

### ✅ Phase 7 — Functional student dashboard
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Centralized Dashboard Aggregation Service (`NCTB_Dashboard_Service`); rules-based home study guide layout with Continue Learning hero card (deep-linking to active activity step), Spaced Revision Due action widget, Needs Attention mistake alert widget, quick learning KPIs bar, and Enrolled Books curriculum progress bars with completion percentages; REST endpoint `GET /nctb/v1/student/dashboard`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_7.md`](./docs/build-history/BUILD_REPORT_PHASE_7.md)

### ✅ Phase 8 — Payments and entitlements
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Centralized Entitlement Service (`NCTB_Entitlements`) evaluating free status, direct purchases, unit/book packs, subscriptions, and admin grants; custom tables `wp_nctb_entitlements` and `wp_nctb_entitlement_audit` via migration `0.8.0`; WooCommerce integration listener (`NCTB_Commerce`); Access-denied paywall banner component; Admin Entitlements manager screen under Lessons (`edit.php?post_type=nctb_lesson&page=nctb-entitlements`); My Purchases page (`/purchases/`); REST API endpoints under `nctb/v1/entitlements/*`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_8.md`](./docs/build-history/BUILD_REPORT_PHASE_8.md)

### ✅ Phase 9 — Contextual AI tutor
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Server-side AI provider adapter (`NCTB_AI_Adapter`) for Anthropic/OpenAI/Mock; Context builder (`NCTB_AI_Context_Builder`) assembling grounded prompts with curriculum data, vocabulary, outcomes, and student mistake context; Socratic scaffolding guardrails (never giving away quiz answers, anti-hallucination for board exams); daily quota tracker (`NCTB_AI_Usage`) with `wp_nctb_ai_conversations` and `wp_nctb_ai_usage` tables via migration `0.9.0`; slide-out AI Tutor Drawer in `single-nctb_lesson.php` with 5 quick action chips (`explain`, `bangla`, `hint`, `example`, `why_wrong`); REST endpoints under `nctb/v1/tutor/*`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_9.md`](./docs/build-history/BUILD_REPORT_PHASE_9.md)

### ✅ Phase 10 — Writing, listening & speaking enhancements
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** 6-stage iterative writing pipeline service (`NCTB_Writing_Service`) with multi-criteria rubric feedback breakdown (Structure, Grammar, Vocabulary, Revision plan); Listening player service (`NCTB_Listening_Service`) with audio duration and transcript toggle; Speaking practice service (`NCTB_Speaking_Service`) with recording timer, submission logging, and formative pronunciation feedback with non-official disclaimer; custom tables `wp_nctb_writing_submissions` and `wp_nctb_speaking_submissions` via migration `0.10.0`; embedded interactive workbenches in `single-nctb_lesson.php`; Skills REST endpoints under `nctb/v1/skills/*`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_10.md`](./docs/build-history/BUILD_REPORT_PHASE_10.md)

### ✅ Phase 11 — Board-question database
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Authentic Board Question Service (`NCTB_Board_Service`) with 10 Education Boards metadata (Level, Board, Year, Subject, Question No, Marks, Type, Topic, Verified Answer, Marking Scheme, Official Source Reference); custom tables `wp_nctb_board_exams` and `wp_nctb_board_questions` via migration `0.11.0`; Admin Board Questions manager (`NCTB_Board_Admin`) under Lessons; Student Board Questions archive hub (`[nctb_board_questions]` / `page-board-questions.php` / `/board-questions/`) with live level/board/year filter bar and verified answer accordions; lesson-level board exam practice widget embedded in `single-nctb_lesson.php`; strict separation of authentic exam items from AI practice; REST endpoints under `nctb/v1/board/*`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_11.md`](./docs/build-history/BUILD_REPORT_PHASE_11.md)

### ✅ Phase 12 — Board pattern analytics
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Board Pattern Analytics Service (`NCTB_Board_Analytics_Service`) aggregating historical topic frequency, question type distributions, board coverage, and yearly volume trends; Student Board Pattern Analytics Hub (`[nctb_board_analytics]` / `page-board-analytics.php` / `/board-analytics/`) with level switcher (HSC/SSC), 4 metric KPIs, topic frequency progress bars with 1-click deep links to board practice; strictly framed and labeled as **Historical Statistical Analysis Only, never prediction**; REST endpoint `GET /nctb/v1/board/analytics`.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_12.md`](./docs/build-history/BUILD_REPORT_PHASE_12.md)

### ✅ Phase 13 — English MVP content library
- **Done:** 2026-08-19.
- **What was built:** HSC & SSC English curriculum mapping and lesson seeding templates (`NCTB_Content_Library_Service`).
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_13.md`](./docs/build-history/BUILD_REPORT_PHASE_13.md)

### ✅ Phase 14 — Private beta, security, performance & QA review
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Integrated the 6 Master Blueprints from Claude (`00_INDEX.md` through `05_PUBLISHING_REQUIREMENTS_RISKS_COSTS.md`) into `docs/plans/`; hardened all 6 REST controllers (`class-nctb-ai-rest.php`, `class-nctb-progress-rest.php`, `class-nctb-practice-rest.php`, `class-nctb-dashboard-rest.php`, `class-nctb-entitlements-rest.php`, `class-nctb-skills-rest.php`) to eliminate insecure developer fallbacks and isolate student data; added native Google Gemini provider support to `NCTB_AI_Adapter`; implemented low-bandwidth YouTube Facade component in `theme-ui.js` and `theme-ui.css` for 3G network savings; audited 56 `$wpdb` SQL database queries.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_14.md`](./docs/build-history/BUILD_REPORT_PHASE_14.md)

### ✅ Phase 15 — Production launch readiness
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Enhanced WooCommerce commerce listener (`NCTB_Commerce`) with Bangladeshi mobile financial services transaction parsing (bKash, Nagad, Rocket, SSLCommerz); Educational SEO & Schema.org Structured Data service (`NCTB_SEO`) outputting `Course`, `LearningResource`, and `BreadcrumbList` JSON-LD microdata; Bilingual Transactional Notification & Email service (`NCTB_Notifications`); Terms of Service (`page-terms.php`) and Privacy Policy compliance updates; Automated cross-platform MySQL backup and restore operations (`scripts/backup_db.*`, `scripts/restore_db.*`); Production secrets configuration template (`config/secrets.example.php`).
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_15.md`](./docs/build-history/BUILD_REPORT_PHASE_15.md)

### ✅ Phase 16 — Teacher role & unified portal ("Shikkhok Hub")
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Implemented `nctb_teacher` role and capabilities (`NCTB_Roles`); added custom table `wp_nctb_teacher_profiles` via migration `0.16.0` (`NCTB_Migrations`); built Teacher Profile Service (`NCTB_Teacher_Profile`) with institutional metadata; created Teacher REST API controller (`NCTB_Teacher_REST`); built 3-step educator onboarding wizard (`[nctb_teacher_onboarding]` / `page-teacher-onboarding.php`) and dedicated Teacher Dashboard (`[nctb_teacher_dashboard]` / `page-teacher-dashboard.php`) with pedagogical quick tools; configured role-aware navigation in `header.php` and complete CSS styling.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_16.md`](./docs/build-history/BUILD_REPORT_PHASE_16.md)

### ✅ Phase 17 — Modules & video-course system
- **Done:** 2026-08-19 by Antigravity (Gemini 3.7 Flash).
- **What was built:** Registered `nctb_module` CPT & `module_category` taxonomy (`NCTB_Module_CPT`); added custom table `wp_nctb_module_progress` via migration `0.17.0` (`NCTB_Migrations`); built Module Service (`NCTB_Module_Service`) with lecture completion calculation and sample course seeders; created Module REST API controller (`NCTB_Module_REST`); built single course player template (`single-nctb_module.php`) with low-bandwidth YouTube facade and playlist checklist sidebar, course archive (`archive-nctb_module.php`), and complete CSS styling.
- **Report:** [`docs/build-history/BUILD_REPORT_PHASE_17.md`](./docs/build-history/BUILD_REPORT_PHASE_17.md)




