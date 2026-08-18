# AGENTS.md — Build protocol for any AI

**Read this before doing anything.** It works for Claude Code, Gemini/Antigravity,
Cursor, Windsurf, Codex, or any AI coding assistant. It defines how to continue
building the **NCTB AI Learning Hub** without losing progress across devices and AIs.

The full plan is in **[README.md](./README.md)**. The live progress is in
**[BUILD_STATE.md](./BUILD_STATE.md)**.

---

## The protocol (follow exactly)

1. **Sync first.** Run `git pull` to get the latest work from other devices/AIs.
2. **Find your job.** Open **[BUILD_STATE.md](./BUILD_STATE.md)** → read the
   **"CURRENT PHASE TO BUILD"** section. That single phase is your task.
3. **Read the requirements.** Open the matching phase section in
   **[README.md](./README.md)** and read it fully, plus any "carry-over / blocker"
   notes in BUILD_STATE.
4. **Plan briefly.** State which files, tables, and endpoints you will add or
   change for this phase only.
5. **Build ONLY that phase.** Do **not** implement features from later phases.
   You may add small placeholders/interfaces for future phases, clearly marked.
6. **Test it.** Do the checks listed in that phase's *Definition of Done*.
   **Never claim a test passed unless you actually ran it.** If you cannot run
   something (no PHP/DB in your environment), say so explicitly.
7. **Write the Build Report.** Save it to
   `nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_<N>.md` using the template in
   README.
8. **Update the memory.** Edit **[BUILD_STATE.md](./BUILD_STATE.md)**: move the
   finished phase to the completed log (date, who, what was built, report link,
   remaining items) and set the next phase as `🔜 NEXT`.
9. **Sync last.** Run `bash scripts/sync.sh "Phase <N> complete: <summary>"`
   (or `git add -A && git commit && git push`).
10. **STOP.** Do not start the next phase. Wait for the human to review and approve.

---

## Hard rules (from the plan — do not break)

- **One phase at a time.** Always stop after a phase for human review.
- Keep **all learning/business logic in the `nctb-learning-hub` plugin**, and
  **presentation in the theme**. Never hard-code curriculum into templates.
- **Never modify WordPress core files.** Use hooks/actions/filters.
- Every REST endpoint needs a **permission callback**; use **nonces** on
  state-changing requests; **sanitize input, escape output**; use
  `$wpdb->prepare()` / safe APIs.
- **Never put API keys/secrets in browser JS or in committed code.** AI runs
  **server-side only**, through one provider adapter.
- **Version every DB schema change** through `NCTB_Migrations`. Back up the DB
  before migrating (see `nctb-ai-learning-hub/docs/BACKUP_RESTORE.md`).
- **Mobile-first**, low-bandwidth friendly. Don't call AI for routine MCQ
  marking or stored explanations.
- **Progress ≠ mastery.** Hints precede full answers. AI content is never
  auto-published. AI-generated questions are never labelled authentic board
  questions.
- Student data is private by default; never expose one student's data to another.

---

## Environment notes

- **Hosting target:** Hostinger (PHP/MySQL) — standard WordPress stack.
- **Git:** repo root is this folder; remote `origin` →
  `github.com/sahadatnisad/NCTB-website`, branch `main`.
- `wp-config.php`, `config/secrets.php`, `*.sql`, uploads, and `node_modules/`
  are git-ignored — never commit them.
- If your session has **no PHP/WP-CLI/Composer**, you can still author code and
  do static checks; clearly record in the Build Report what was **not** run and
  must be verified locally.

---

## If you are unsure

- If a requirement is ambiguous, prefer the **smallest, safest** implementation
  that satisfies the phase's Definition of Done, and note the assumption in the
  Build Report.
- If the current phase seems already done, verify against its DoD, then (if truly
  complete) advance BUILD_STATE to the next phase rather than rebuilding.
- Do not redesign completed, working architecture without a documented reason.
