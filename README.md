# NCTB AI Learning Hub

> A lesson-by-lesson digital companion to the Bangladesh **NCTB curriculum**, where students learn the school lesson, practise it, get contextual AI help, review mistakes, take assessments, and prepare for board exams from home.
>
> **First product:** SSC + HSC **English** — an interactive NCTB lesson book with a personal AI English tutor. The same engine later powers ICT, Bangla, Mathematics, Science and other NCTB subjects.

**Platform:** WordPress (custom plugin `nctb-learning-hub` + presentation theme) · **Hosting target:** Hostinger (PHP/MySQL) · **Commerce:** WooCommerce · **AI:** server-side provider adapter.

---

## ⭐ This repo is built by AI, one phase at a time

This project is designed so that **any AI coding assistant** (Claude Code, Gemini/Antigravity, Cursor, Windsurf, Codex, etc.) can continue the build from **any device, at any time**, without losing track of what is done.

Three files run the system:

| File | Role | Who updates it |
|------|------|----------------|
| **[README.md](./README.md)** (this file) | The complete plan: rules, architecture, and every phase in detail. | Rarely — only if the plan itself changes. |
| **[BUILD_STATE.md](./BUILD_STATE.md)** | The **live progress tracker**. Which phase is done, what's next, what's left. **The AI reads this first and updates it last.** | The AI, after every phase. |
| **[AGENTS.md](./AGENTS.md)** | The step-by-step protocol every AI must follow (also mirrored in [CLAUDE.md](./CLAUDE.md) for Claude Code). | Rarely. |

### The golden workflow (every AI session)

```
1. git pull                         → get the latest work from other devices/AIs
2. Read BUILD_STATE.md              → find the CURRENT phase to build
3. Read the matching phase in README → get the detailed requirements
4. Build ONLY that one phase         → do not build future phases
5. Test it (see each phase's DoD)    → don't claim a test passed unless you ran it
6. Write the Build Report            → append to docs, update BUILD_STATE.md
7. git add / commit / push           → sync to GitHub, then STOP for human review
```

> **One phase at a time. Always stop after a phase for human review before starting the next.** This is a hard rule from the original plan and it stays.

### How to work from any device

1. On the new device: `git clone https://github.com/sahadatnisad/NCTB-website.git`
2. Open the folder in your AI tool of choice.
3. Tell the AI: **“Read AGENTS.md and BUILD_STATE.md, then continue the build.”**
4. When it finishes a phase it runs `bash scripts/sync.sh "message"` (or plain git) to push.
5. Any other device just runs `git pull` to receive that work.

Because progress lives in `BUILD_STATE.md` (committed to GitHub), the “memory” of the project travels with the repo — not with any single AI or computer.

---

## Table of contents

- [Core product rules (never change these)](#core-product-rules-never-change-these)
- [Architecture overview](#architecture-overview)
- [Data model](#data-model)
- [Website areas](#website-areas)
- [Universal lesson structure](#universal-lesson-structure)
- [Student assistance & mastery model](#student-assistance--mastery-model)
- [AI tutor behavior](#ai-tutor-behavior)
- [Payment model](#payment-model)
- [**The phases (0 → 18)**](#the-phases)
- [Build report template](#build-report-template)
- [Coding rules](#coding-rules)
- [First-time setup on Hostinger / a new device](#first-time-setup)

---

## Core product rules (never change these)

1. The **NCTB curriculum and official book structure control what is taught.** AI does **not** decide the curriculum.
2. The main experience is a loop, not a library:
   **Book → Unit/Chapter → Lesson → Learn → Practice → Tutor → Test → Mistakes → Revision → Mastery → Board Practice.**
3. English lessons may develop six skills together: grammar, vocabulary, reading, writing, listening, speaking.
4. AI is **embedded inside the lesson**. A giant general chatbot is secondary.
5. Give **hints before full answers** when educationally appropriate.
6. Lesson **completion** and lesson **mastery** are different values — never merge them.
7. Previous board questions are stored as **verified source material** and linked to concepts/lessons.
8. **AI-generated questions must never be labelled as authentic board questions.**
9. Database-driven lessons/practice must work **without** calling the AI for every interaction.
10. AI-generated educational content follows: **Draft → Review → Approved → Published** (never auto-published).
11. The site is **mobile-first**, fast on slow connections, easy on Android phones.
12. The data architecture must support **future subjects from day one**, even though English launches first.
13. Students may be **minors** → privacy, moderation, authorization and secure data handling are mandatory.

---

## Architecture overview

- **WordPress** provides: user accounts, admin/editorial UI, media library, pages, authentication, REST API, commerce integration, publishing workflow.
- **One custom plugin `nctb-learning-hub`** holds nearly all learning/business logic (curriculum, lessons, activities, questions, attempts, progress, mastery, mistakes, revision, AI tutor, board questions, entitlements, analytics).
- **A lightweight presentation theme** (`nctb-child-theme`) handles look & feel only. **No learning logic in the theme.**
- **WooCommerce** is the commerce foundation. Access is decided by a **centralized entitlement service**, never a simple `paid=true` flag.
- **AI is called server-side only**, through **one provider adapter** so the model/provider can be swapped without rewriting the learning system. The AI receives only relevant approved context — never the whole database.

Centralize these behind a single service each: **entitlements, AI calls, mastery calculation, question marking.**

### Repository layout

```
NCTB Website/                         ← git root (this repo → github.com/sahadatnisad/NCTB-website)
├── README.md                         ← the complete plan (this file)
├── BUILD_STATE.md                    ← live progress tracker (AI updates this)
├── AGENTS.md                         ← universal AI build protocol
├── CLAUDE.md                         ← Claude Code entry (points to AGENTS.md)
├── scripts/sync.sh                   ← one-command GitHub sync
├── NCTB_WORDPRESS_MASTER_PLAN.md     ← original plan (archived reference; README supersedes it)
└── nctb-ai-learning-hub/             ← the WordPress site
    ├── wp-content/
    │   ├── plugins/nctb-learning-hub/   ← ALL learning/business logic
    │   └── themes/nctb-child-theme/     ← presentation only (mobile-first, EN/BN)
    └── docs/                            ← coding standards, env, secrets, backups, build reports
```

---

## Data model

Use **WordPress-native content** where editorial convenience helps, and **custom versioned tables** for high-volume relational data.

**WordPress-native (custom post types):** `nctb_book`, `nctb_unit`, `nctb_lesson`.
**Taxonomies/meta:** education level, class, subject, paper, curriculum version, academic session, tags/topics.
**Media Library:** images, lesson audio, diagrams, approved downloadable resources.

**Custom plugin tables** (prefixed with the WP DB prefix, created via **versioned migrations** in `NCTB_Migrations` — never by hand):

`nctb_concepts`, `nctb_learning_outcomes`, `nctb_lesson_concepts`, `nctb_lesson_activities` (if not fully in blocks), `nctb_questions`, `nctb_question_options`, `nctb_question_concepts`, `nctb_attempts`, `nctb_progress`, `nctb_mastery`, `nctb_mistakes`, `nctb_review_schedule`, `nctb_vocabulary`, `nctb_vocabulary_mastery`, `nctb_board_questions`, `nctb_board_exams`, `nctb_entitlements`, `nctb_ai_usage`, `nctb_ai_conversations` (privacy-minimized).

> Each phase below creates only the tables it needs. Do **not** create a table before its phase.

---

## Website areas

**Public:** Home · How It Works · Subjects · SSC English landing · HSC English landing · Pricing · Free Lesson · Login · Register · FAQ · Privacy · Terms · Contact/Support.

**Logged-in student:** Dashboard · My Subjects · My Book · Unit/Chapter · Lesson · Practice · My Mistakes · Revision Due · Board Questions · AI Tutor · Vocabulary · Progress · Purchases/Membership · Profile/Preferences.

**Admin:** Curriculum · Books · Units · Lessons · Lesson Activities · Concepts · Learning Outcomes · Questions · Vocabulary · Board Questions · AI Review Queue · Students · Progress Analytics · Orders · Subscriptions · AI Usage · Settings.

---

## Universal lesson structure

Support these reusable activity types (not every lesson uses all):
warm-up · context/situation · NCTB-aligned reading · vocabulary · notice the language · grammar explanation · examples · guided practice · independent practice · reading practice · listening task · speaking task · writing task · NCTB exercise practice · board-question practice · contextual AI help · lesson quiz · feedback · mistake review · spaced revision.

Preferred grammar path: **Context → Notice → Understand → Guided Practice → Independent Recall → Use → Exam Transfer → Feedback → Retry → Mastery → Spaced Review.**

---

## Student assistance & mastery model

**Progressive help for a wrong answer:** (1) **Hint** — a small clue; (2) **Explanation** — the rule + try again; (3) **Step-by-step** — reasoning, correct answer, similar example.
Track: attempts, hints used, time (where useful), concept tested, final result.

**Progress ≠ Mastery.** Example: progress 100%, mastery 68%.

Mastery bands (internal indicators, not scientific scores): 0–49 reteach · 50–69 guided · 70–84 independent · 85–94 mastered · 95–100 strong.

Spaced review schedule: same day · 1 day · 3 days · 7 days · 14 days · 30 days. Wrong answers shorten the next interval; repeated correct recall extends it.

---

## AI tutor behavior

The tutor must: respect student level; use the current lesson as primary context; hint before full answers; use simple language; support English-only / English+Bangla / stronger Bangla scaffolding and reduce Bangla dependence over time; diagnose the misconception (not just mark right/wrong); ask the student to try again; **not** complete assessed work automatically; use Bangladeshi-appropriate examples; clearly separate approved content from generated examples; **never** invent a board question as authentic; **never** claim an AI score is an official NCTB/board assessment; stay age-appropriate and moderated; admit uncertainty.

Contextual actions: Explain this · Explain in Bangla · Make it easier · Give another example · Give me a hint · Why was I wrong? · Test me on this · Show the rule · Show a similar question · Show a verified board question.

---

## Payment model

- **Free:** registration, sample lessons, limited practice, basic progress, limited AI demo.
- **Single lesson:** unlock one lesson + its practice/assessment.
- **Pack/course:** unlock a unit, topic pack, or full class English course.
- **Monthly subscription:** subscribed content + larger AI allowance.

AI usage has **separate quotas** because it has variable cost. Never call AI for routine MCQ marking, stored explanations, or ordinary navigation.

---

## The phases

Build in this order. **Each phase ends with a Build Report, a `BUILD_STATE.md` update, a commit/push, and a STOP for human review.** Do not build features that belong to a later phase.

Legend for each phase: **Goal · Prerequisites · Build · Data/Schema · Endpoints · Security · Definition of Done (DoD) · On completion.**

---

### PHASE 0 — Safe WordPress development environment  ✅ *(complete — pending review)*

- **Goal:** A clean, recoverable development base.
- **Build:** local/staging WordPress; Git repo; DB backup method; dev/staging/prod separation; presentation theme; empty `nctb-learning-hub` plugin; coding standards + docs folder; debug logging in dev only; secret/API-key handling plan.
- **DoD:** WordPress loads; plugin activates/deactivates cleanly; no PHP/JS errors; Git tracks code; backup/restore works.
- **Status:** Plugin skeleton, versioned migration runner, logger, theme, and docs are built. See [`nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_0.md`](./nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_0.md). ⚠️ Known issue to resolve before Phase 1: the WordPress core folder layout is non-standard (no real `wp-includes/` directory) — restore standard WP structure so the site boots.

---

### PHASE 1 — Visual shell and navigation

- **Goal:** Make the site feel like a student learning hub before complex logic exists.
- **Prerequisites:** Phase 0.
- **Build:**
  - Public shell pages: Home, Subjects, Pricing (placeholder), Login/Register.
  - Student shell pages: Dashboard (placeholder), My Subjects, Learn/My Book, Practice, Tutor, Profile.
  - Responsive, mobile-first navigation that **differs for logged-out vs logged-in** users.
  - English + Bangla typography; clean academic (not childish) design; accessible buttons/forms; fast loads.
- **Data/Schema:** none.
- **Endpoints:** none (or trivial menu logic).
- **Security:** escape all output; no secrets; capability check for the logged-in menu.
- **DoD:** layouts work on common mobile & desktop widths; logged-out vs logged-in nav differ correctly; **no learning logic hard-coded in templates**.
- **On completion:** Build Report → update BUILD_STATE → commit/push → STOP.

---

### PHASE 2 — Student accounts and onboarding

- **Goal:** Know who the student is and what they study.
- **Prerequisites:** Phase 1.
- **Build:**
  - Student profile fields: SSC/HSC or class level; class/session; preferred explanation language; chosen subjects; optional target exam/session; `onboarding_complete` flag.
  - Onboarding flow: account creation → select level/class → select English → choose explanation preference → redirect to dashboard. **Resumable** if interrupted.
- **Data/Schema:** store profile in user meta (no custom table needed yet).
- **Endpoints:** REST/AJAX to save onboarding steps — each with a **permission callback + nonce**.
- **Security:** a student **cannot read/edit another student's** profile; sanitize inputs; escape outputs.
- **DoD:** student data saves securely; cross-student access blocked; onboarding resumes after interruption.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 3 — Curriculum + Book + Unit + Lesson CMS

- **Goal:** The academic backbone.
- **Prerequisites:** Phase 2.
- **Build:**
  - Admin can create/manage: education level/class, subject, book, unit/chapter, lesson, lesson order, learning outcomes, concepts, curriculum version/source reference — **without coding**.
  - CPTs `nctb_book`, `nctb_unit`, `nctb_lesson` + taxonomies (level, class, subject, paper, curriculum version, session, topics).
  - Student-facing browse hierarchy: **Class → Subject → Book → Unit → Lesson.**
- **Data/Schema:** CPTs + taxonomies; tables `nctb_concepts`, `nctb_learning_outcomes`, `nctb_lesson_concepts` via migration.
- **Endpoints:** read endpoints for the browse tree (public/permission-appropriate).
- **Security:** capabilities for editors; escape output; prepared queries.
- **Important:** enter only **one prototype lesson's** worth of data — not the whole course.
- **DoD:** admin can create/edit/reorder curriculum without code; students browse one sample tree; **no curriculum hard-coded in the theme**.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 4 — One gold-standard interactive lesson

- **Goal:** Prove the complete lesson experience before scaling content.
- **Prerequisites:** Phase 3.
- **Build one NCTB-aligned prototype lesson** with: objective, warm-up, main reading/content, vocabulary, grammar focus (if relevant), examples, guided practice, independent practice, one writing task, one listening activity (if audio available), one speaking activity (if practical), summary, a **quiz placeholder**, and a **contextual Tutor button placeholder**.
  - **Lesson editor:** admin arranges **reusable activity blocks** (start with the smallest set; add more block types later).
- **Data/Schema:** `nctb_lesson_activities` if activities aren't fully stored in Gutenberg blocks.
- **Endpoints:** save/read lesson activity order.
- **Security:** capability checks; sanitize block content; escape on render.
- **DoD:** lesson authored from admin; renders correctly on mobile/desktop; progress position identifiable; **no lesson-specific PHP template required**.
- **On completion:** Test with real users if possible → Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 5 — Practice and question engine

- **Goal:** Turn lessons into active practice — **without AI**.
- **Prerequisites:** Phase 4.
- **Build:** question types MCQ, single fill-in-the-blank, short text answer, error correction (where practical).
  - Question records support: lesson, concept, difficulty, correct answer, explanation, hint levels, source type, verification status.
  - Student interaction: answer → mark → hint → retry → explanation → next question.
- **Data/Schema:** `nctb_questions`, `nctb_question_options`, `nctb_question_concepts`, `nctb_attempts`.
- **Endpoints:** submit answer, request hint, fetch next — **each with permission callback + nonce**; marking runs through **one central marking service**.
- **Security:** authenticated authorization enforced; prepared queries; sanitize/escape; students can't read others' attempts.
- **DoD:** question logic works with **no AI**; attempts stored; progressive hints work; admin can create/edit questions.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 6 — Progress, mastery, mistakes, spaced revision

- **Goal:** Make the platform remember learning.
- **Prerequisites:** Phase 5.
- **Build:** lesson progress; concept mastery (via **one mastery service**); mistake notebook; review schedule; revision queue; mastery recalculation after attempts; **separate `completed` and `mastered` states**.
  - Student pages: My Mistakes, Revision Due, basic Progress.
- **Data/Schema:** `nctb_progress`, `nctb_mastery`, `nctb_mistakes`, `nctb_review_schedule`.
- **Endpoints:** fetch mistakes/revision-due; mark reviewed — permission-guarded.
- **Security:** per-student data isolation; prepared queries.
- **DoD:** wrong answers appear in My Mistakes; mastered mistakes leave the active list; revision items become due automatically; completion ≠ mastery.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 7 — Functional student dashboard

- **Goal:** Make the site behave like a home study guide.
- **Prerequisites:** Phase 6.
- **Build (rules-based, not sophisticated AI):** Continue Learning · Today's Practice · Revision Due · Needs Attention · recent progress · My Book progress.
- **Data/Schema:** none new (reads Phase 6 data).
- **Endpoints:** dashboard data aggregation — permission-guarded, cached where safe.
- **Security:** per-student isolation.
- **DoD:** a returning student opens the dashboard and immediately knows what to study next.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 8 — Payments and entitlements

- **Goal:** Free, per-lesson and subscription access — safely.
- **Prerequisites:** Phase 7.
- **Build:** WooCommerce integration; lesson↔product mapping; free/direct/pack-course/subscription entitlement structures; access-denied/paywall UX; My Purchases/Membership page; admin grant/revoke with **audit trail**.
  - All access decided by a **centralized entitlement service** (never `paid=true`). Understands: direct lesson purchase, pack ownership, full-course ownership, active subscription, free access, admin grant, expiry.
- **Data/Schema:** `nctb_entitlements`.
- **Endpoints:** entitlement checks server-side; webhooks from WooCommerce.
- **Security:** verify order/subscription server-side; audit admin grants; no client-trust.
- **DoD:** a paid lesson unlocks only for entitled students; free lessons work without purchase; cancelled/expired access behaves per product rules; access checks are centralized.
- **On completion:** Run payment tests in **sandbox/test mode** → Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 9 — Contextual AI tutor

- **Goal:** Add AI **only after** lesson/practice already work.
- **Prerequisites:** Phase 8 (and 4–6).
- **Build:** server-side **AI provider adapter**; secure API-key handling; AI usage limits/quotas; lesson-aware Tutor drawer/modal; actions (Explain This / Bangla / Hint / Another Example / Why Was I Wrong?); **context builder** using approved lesson content + relevant recent attempts; moderation/safety checks; AI usage logging **without** unnecessary private data.
- **Data/Schema:** `nctb_ai_usage`, `nctb_ai_conversations` (privacy-minimized).
- **Endpoints:** tutor request endpoint — permission callback + nonce + rate/quota check. **API keys never in browser code.**
- **Security:** server-side keys only; prompt-injection resistance; moderation; quota enforcement; minimal logging.
- **DoD:** AI never needs credentials in the browser; tutor knows current lesson/concept; AI can explain a student's mistake from attempt context; usage tracked; board questions cannot be fabricated as authentic.
- **On completion:** Build Report **with sample tutor interactions** → BUILD_STATE → commit/push → STOP.

---

### PHASE 10 — Writing, listening & speaking enhancements

- **Goal:** Move from grammar practice to full English development.
- **Prerequisites:** Phase 9.
- **Build incrementally:**
  - **Writing:** task → brainstorm → draft → feedback → revision → final; save drafts and feedback separately.
  - **Listening:** WordPress-hosted/approved external audio; transcript stored admin-side when appropriate; listening question activities.
  - **Speaking:** simple browser recording/upload only if practical; AI speaking feedback later; **avoid misleading official-looking scores.**
- **Data/Schema:** writing drafts + feedback storage (private by default); listening assets in Media Library.
- **Security:** student writing/speaking is **private by default**; sanitize uploads.
- **DoD:** at least one real lesson includes reading, vocabulary, grammar, writing, listening and optional speaking without breaking lesson flow.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 11 — Board-question database

- **Goal:** Connect learning to authentic exam practice.
- **Prerequisites:** Phase 5 (+ curriculum).
- **Build:** board exam records; verified board questions with year/board/topic/concept metadata + source reference + verified answer/explanation; filter/search page; attach relevant board questions to lessons.
  - Each board question stores at minimum: exam level, board, year, subject, paper, question number, marks, question type, topic, concept, sub-concept/rule, question text, options (where applicable), verified answer, explanation, source reference, verification status.
- **Data/Schema:** `nctb_board_exams`, `nctb_board_questions`.
- **DoD:** a student opens a lesson and practises verified related board questions; filters by board/year/topic work; **AI-generated items are clearly separated from authentic board questions**.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 12 — Board pattern analytics

- **Goal:** Turn the board-question DB into historical exam intelligence.
- **Prerequisites:** Phase 11.
- **Build:** frequency by topic/concept, by year, by board; common question types; practice high-frequency historical patterns.
- **Important:** always describe as **historical analysis, not prediction**.
- **DoD:** analytics render correctly and are labelled historical-only.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 13 — English MVP content library

- **Goal:** **Scale the proven system, not redesign it.**
- **Prerequisites:** Phases 4–12.
- **Build:** 20–30 SSC English lessons; 20–30 HSC English lessons; question banks; vocabulary; verified board-question links; selected audio/writing/speaking activities; a **human review workflow**.
  - Content workflow: **Official NCTB material → Curriculum mapping → Learning outcomes → Lesson decomposition → Micro-concepts → Draft → Activities → Questions → Board questions → AI assistance → Human review → Publish.**
- **Important:** do **not** build hundreds of lessons before testing the MVP with students.
- **On completion:** Build Report → BUILD_STATE → commit/push → STOP.

---

### PHASE 14 — Private beta: security, performance & QA

- **Goal:** Make the site safe and reliable before public launch.
- **Prerequisites:** Phase 13.
- **Test:** mobile usability; low-bandwidth behavior; broken links; progress persistence; payment/entitlement edge cases; authorization; nonce/capability checks; REST permission callbacks; SQL-injection resistance (prepared queries); XSS escaping/sanitization; private student-data protection; AI prompt-injection resistance; AI usage limits; backup/restore; performance/caching; accessibility basics; error logging; analytics/quality flags.
- **Beta:** invite a small SSC/HSC group; collect confusion points, abandonment screens, completion, accuracy, tutor usage, payment friction, mobile problems. Fix major issues.
- **On completion:** **Beta report** → BUILD_STATE → commit/push → STOP.

---

### PHASE 15 — Public English launch

- **Goal:** Launch the first real product.
- **Scope:** SSC English; HSC English; lesson-by-lesson learning; practice; progress/mastery; mistakes/revision; contextual AI tutor; per-lesson purchasing; monthly plan (if ready); verified board questions; essential analytics/admin controls.
- Monitor quality, AI cost, payment behavior, student outcomes closely.
- **On completion:** Build Report → BUILD_STATE → commit/push.

---

### PHASE 16 — Complete English
Expand all required SSC/HSC English lessons/skills on the same engine. Don't redesign the core unless usage data proves it necessary.

### PHASE 17 — Add ICT
Reuse users, payments, curriculum, lessons, activities, questions, practice, mastery, revision, AI, analytics. Add only genuinely-needed ICT-specific activity/question types.

### PHASE 18 — Add Bangla & other NCTB subjects
Repeat the curriculum-first content workflow. The platform becomes the broad NCTB learning hub only after the English engine is proven.

---

## Build report template

Every phase ends with this report, saved to `nctb-ai-learning-hub/docs/BUILD_REPORT_PHASE_<N>.md` **and** summarized in `BUILD_STATE.md`.

```text
NCTB LEARNING HUB — BUILD REPORT

Phase completed:
Date:
Environment / WordPress version / PHP version / Theme / Plugin version:

1. What was built
2. Files created/changed
3. Database/schema changes
4. Admin features added
5. Student-facing features added
6. REST/AJAX endpoints added
7. Security controls added
8. Tests performed
9. Test results
10. Screens/pages to manually review
11. Known problems / technical debt
12. Setup or migration steps I must perform
13. Rollback notes
14. What is intentionally NOT built yet

STOP HERE. NEXT PHASE NOT STARTED.
```

---

## Coding rules

**WordPress:** follow WP coding standards; use hooks/actions/filters; never modify core; keep business logic in the plugin and visuals in the theme; use capabilities/roles; use nonces; every REST endpoint has a permission callback; sanitize input; escape output; use `$wpdb->prepare()` or safe APIs; version all schema migrations; never put secrets in JS or committed source; translation-ready strings; UTF-8 Bangla support.

**Architecture:** don't hard-code curriculum in PHP templates; don't duplicate entities/tables; justify any third-party dependency; prefer stable WP APIs; keep modules loosely coupled; centralize entitlements, AI calls, mastery, and marking each behind one service.

**Educational:** curriculum alignment is editorially controlled; AI content never auto-published; board-question authenticity verified; AI feedback is supportive, not an official grade; hints precede answers; progress ≠ mastery; students can retry mistakes.

**Performance:** mobile-first; don't load AI/chat libraries on pages that don't use them; lazy-load media; compress responsibly; paginate large datasets; cache expensive reads; no huge autoloaded options.

**Privacy/safety:** collect only needed data; keep student writing/speaking private by default; never expose one student's data to another; don't log raw secrets; minimize AI-conversation logging; provide deletion/export where required; moderate AI responses.

---

## First-time setup

**On Hostinger (production/staging):**
1. Create a WordPress site (Hostinger shared hosting is PHP/MySQL — ideal for WordPress).
2. Deploy the `nctb-ai-learning-hub/` WordPress files (via Git deploy, SFTP, or Hostinger's Git integration).
3. Copy `wp-config-sample.php` → `wp-config.php`; set DB credentials and `WP_ENVIRONMENT_TYPE`. Keep `wp-config.php` out of Git (already ignored).
4. Activate the **NCTB Learning Hub Theme** and **NCTB Learning Hub** plugin.
5. Follow `nctb-ai-learning-hub/docs/ENVIRONMENT.md`, `SECRETS.md`, and `BACKUP_RESTORE.md`.

**On a new dev device (to continue building with any AI):**
```bash
git clone https://github.com/sahadatnisad/NCTB-website.git
cd "NCTB-website"     # or the cloned folder name
# open in your AI tool, then say: "Read AGENTS.md and BUILD_STATE.md, then continue."
```

---

*This README is the canonical plan and supersedes `NCTB_WORDPRESS_MASTER_PLAN.md` (kept for reference). If the plan itself must change, edit this file and note it in `BUILD_STATE.md`.*
