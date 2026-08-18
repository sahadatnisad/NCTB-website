# BUILD STATE — NCTB AI Learning Hub

> **This is the memory of the project.** Any AI, on any device, reads this file
> **first** to know where the build stands, and updates it **last** before
> committing. Keep it accurate and short. The full plan is in [README.md](./README.md).

- **Repo:** https://github.com/sahadatnisad/NCTB-website
- **Branch:** main
- **Last updated:** 2026-08-19
- **Updated by:** Antigravity AI Pair Programmer

---

## 👉 CURRENT PHASE TO BUILD

**PHASE 3 — Curriculum + Book + Unit + Lesson CMS**

> Read the "PHASE 3" section in [README.md](./README.md#phase-3--curriculum--book--unit--lesson-cms) for full requirements before building.

**Do ONLY this phase, then stop for human review.**

---

## Progress at a glance

| Phase | Title | Status |
|---|---|---|
| 0 | Safe WordPress development environment | ✅ Done |
| 1 | Visual shell and navigation | ✅ Done |
| 2 | Student accounts and onboarding | ✅ Done |
| 3 | Curriculum + Book + Unit + Lesson CMS | 🔜 **NEXT** |
| 4 | One gold-standard interactive lesson | ⬜ Not started |
| 5 | Practice and question engine | ⬜ Not started |
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
