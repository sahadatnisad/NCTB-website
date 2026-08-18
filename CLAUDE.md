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
3. **File Editing Best Practices:**
   - **Always read the target file with the `Read` tool before attempting an `Edit`.**
   - Provide exact, character-accurate `old_string` matches including whitespace and indentation.
   - If an `Edit` tool call fails, re-read the file lines or use the `Write` tool to output the complete updated file directly.
4. **Use bash execution tools actively.** You have full permissions to create, edit, lint, test, and commit.
5. **When completing a Phase:** Update `BUILD_STATE.md` and create `docs/BUILD_REPORT_PHASE_<N>.md`.

---

## 🛠 Useful Commands
- **Git status:** `git status`
- **Lint / PHP check:** `find . -name "*.php" -exec php -l {} +`
- **Commit changes:** `git add . && git commit -m "feat(phase-1): visual shell and navigation"`
