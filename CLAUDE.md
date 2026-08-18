# CLAUDE.md — NCTB AI Learning Hub

> **Project Memory & Master Instruction for Claude Code**

## 📌 Project Overview
- **Project Name:** NCTB AI Learning Hub
- **Repository:** https://github.com/sahadatnisad/NCTB-website
- **Master Plan:** [README.md](./README.md) & [NCTB_WORDPRESS_MASTER_PLAN.md](./NCTB_WORDPRESS_MASTER_PLAN.md)
- **Current Build State:** [BUILD_STATE.md](./BUILD_STATE.md)

---

## 🧭 Instructions for Claude Code CLI
1. **Always read `BUILD_STATE.md` first** to see which Phase is active.
2. **Current Active Phase:** **Phase 1 — Visual shell and navigation**
   - Blocker to check: Standardize `nctb-ai-learning-hub/` WordPress core layout if needed.
   - Build visual shell, header, navigation, and mobile-first responsive layout in `nctb-child-theme` and `nctb-learning-hub` plugin.
3. **Use the file editing and bash execution tools actively.** You have full capability to create, edit, lint, and commit files.
4. **When completing a Phase:** Update `BUILD_STATE.md` and create `docs/BUILD_REPORT_PHASE_<N>.md`.

---

## 🛠 Useful Commands
- **Git status:** `git status`
- **Lint / PHP check:** `find . -name "*.php" -exec php -l {} +`
- **Commit changes:** `git add . && git commit -m "feat(phase-1): visual shell and navigation"`
