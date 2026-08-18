# BUILD STATE — NCTB AI Learning Hub

> **This is the memory of the project.** Any AI, on any device, reads this file
> **first** to know where the build stands, and updates it **last** before
> committing. Keep it accurate and short. The full plan is in [README.md](./README.md).

- **Repo:** https://github.com/sahadatnisad/NCTB-website
- **Branch:** main
- **Last updated:** 2026-08-18
- **Updated by:** Claude (Claude Code)

---

## 👉 CURRENT PHASE TO BUILD

**PHASE 1 — Visual shell and navigation**

> Read the "PHASE 1" section in [README.md](./README.md#phase-1--visual-shell-and-navigation) for full requirements before building.
> ⚠️ **Blocker to resolve first:** the WordPress core layout in `nctb-ai-learning-hub/` is non-standard (no real `wp-includes/` directory), so the site may not boot. Restore a standard WordPress install before/at the start of Phase 1. See Phase 0 known problems.

**Do ONLY this phase, then stop for human review.**

---

## Progress at a glance

| Phase | Title | Status |
|-------|-------|--------|
| 0 | Safe WordPress development environment | ✅ Done (pending review) |
| 1 | Visual shell and navigation | 🔜 **NEXT** |
| 2 | Student accounts and onboarding | ⬜ Not started |
| 3 | Curriculum + Book + Unit + Lesson CMS | ⬜ Not started |
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
- **Done:** 2026-08-18 by Claude (Claude Code).
- **What was built:** `nctb-learning-hub` plugin skeleton (loader, versioned migration runner `NCTB_Migrations`, logger, activation/deactivation/uninstall lifecycle, admin/public placeholders, secrets pattern); presentation-only `nctb-child-theme` (mobile-first, EN/BN); docs (coding standards, environment, secrets, backup/restore, phase status); root + plugin `.gitignore`.
- **Report:** [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_0.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_0.md)
- **Remaining / carry-over:**
  - ⚠️ WordPress core layout is non-standard (no `wp-includes/` dir) → site may not boot. **Fix before Phase 1.**
  - Not verified at runtime (no PHP/WP-CLI in the build session): live plugin activation, WordPress boot, phpcs lint. Verify locally.
  - Theme is a standalone theme (no parent present) despite the `nctb-child-theme` name.

---

## How to update this file (for the AI)

When you finish a phase:
1. Move that phase to **Completed phases log** with: date, who, what was built, report link, and any remaining/carry-over items.
2. Set the **next** phase as `🔜 NEXT` under "CURRENT PHASE TO BUILD" and in the table.
3. Update **Last updated** / **Updated by** at the top.
4. Save the full Build Report to `nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_<N>.md`.
5. Commit & push: `bash scripts/sync.sh "Phase <N> complete: <short summary>"`.
6. **STOP** — wait for human review before the next phase.
