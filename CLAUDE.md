# CLAUDE.md — NCTB AI Learning Hub

> **Project Memory & Master Instruction for Claude Code**

## 📌 Project Overview
- **Project Name:** NCTB AI Learning Hub
- **Repository:** https://github.com/sahadatnisad/NCTB-website
- **Master Plan:** [README.md](./README.md) & [NCTB_WORDPRESS_MASTER_PLAN.md](./NCTB_WORDPRESS_MASTER_PLAN.md)
- **Current Build State:** [BUILD_STATE.md](./BUILD_STATE.md)

---

## 🧭 Instructions for Claude Code CLI
1. **Current Active Phase:** **Phase 1 — Visual shell and navigation** (Next: Phase 2)
2. **Environment & Command Rules:**
   - **PHP & MySQL are inside Docker:** Never run `php` on the host machine. Always use:
     ```bash
     docker exec nctb-wordpress php ...
     ```
   - **Always quote paths with spaces:** Always use `"..."` when `cd`-ing into directories with spaces.
   - **Do not download external binaries in loops:** The Docker container has PHP 8.3 and Apache pre-installed.
3. **File Editing Best Practices:**
   - Always read the target file with the `Read` tool before attempting an `Edit`.
   - If an `Edit` tool call fails, use the `Write` tool to output the complete updated file directly.
4. **When completing a Phase:** Update `BUILD_STATE.md` and create `docs/BUILD_REPORT_PHASE_<N>.md`.

---

## 🛠 Useful Commands
- **Git status:** `git status`
- **Lint inside container:** `docker exec nctb-wordpress bash -c 'find /var/www/html/wp-content/plugins -name "*.php" -exec php -l {} +'`
- **Check container logs:** `docker logs nctb-wordpress --tail 20`
- **Commit changes:** `git add . && git commit -m "feat(phase-1): visual shell and navigation"`
