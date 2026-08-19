# NCTB AI Learning Hub — Full-Stack Build Blueprint

> **Purpose of this file.** This is the master build plan handed to an AI coding agent (Antigravity, Claude Code, or any agent) to build the platform **gradually, one phase at a time**. It continues from the existing repository (`sahadatnisad/NCTB-website`), which has completed phases 0–13. It defines backend, frontend, database, and acceptance criteria for every remaining phase.
>
> **Read together with:** `02_DESIGN_SYSTEM.md` (all UI must follow it), `03_CONTENT_OPERATIONS.md` (how content is produced and imported), and the repo's own `AGENTS.md` / `BUILD_STATE.md` protocol.
>
> **Environment:** WordPress (in Docker on Ubuntu) + custom plugin `nctb-learning-hub` + child theme `nctb-child-theme`. Hosting target: standard PHP/MySQL (Hostinger or similar). All prior architecture rules in `AGENTS.md` still apply.

---

## 0. Rules the agent must never break (inherited + new)

**Inherited from `AGENTS.md` (keep all of these):**
- One phase at a time. Build only the current phase, then STOP for human review.
- All business logic in the **plugin**; all presentation in the **theme**. Never hard-code curriculum into templates. Never modify WordPress core.
- Every REST endpoint has a permission callback; nonces on state-changing requests; sanitize input, escape output; `$wpdb->prepare()` for all SQL.
- **No API keys or secrets in browser JS or committed code.** AI runs server-side only, through one provider adapter.
- Version every schema change through `NCTB_Migrations`. Back up DB before migrating.
- Mobile-first, low-bandwidth. Don't call AI for anything a deterministic function can do.
- Progress ≠ mastery. Student data is private by default; never leak one user's data to another.

**New rules for this blueprint:**
- **AI features are a PAID entitlement.** No AI call may run for a user who lacks an active AI entitlement (except an optional tiny free trial quota, if a phase explicitly enables it). Enforce this server-side in the entitlement layer, not the UI.
- **One database, two roles.** Students and teachers share the same WordPress install and DB. Never build a separate teacher app or teacher database.
- **Every screen must conform to `02_DESIGN_SYSTEM.md`.** Use the design tokens (colors, spacing, type scale, components). Do not invent ad-hoc colors or fonts.
- **Content is data, not code.** New subjects/modules are added as content (see `03_CONTENT_OPERATIONS.md`), never by writing new engine code. If a phase would require per-subject code, stop and flag it.
- **Free vs. paid is a property of content, enforced by entitlements.** A module, lesson, or video is marked `access = free | paid`. The gate is one code path, reused everywhere.

---

## 1. Target architecture (the whole system on one page)

```
                 ┌─────────────────────────────────────────────┐
                 │            WordPress (single install)         │
                 │                                               │
  Public/SEO ───▶│  THEME (nctb-child-theme)  — presentation     │
                 │   • Marketing pages, design system UI         │
                 │   • Student app screens                       │
                 │   • Teacher app screens                       │
                 │   • Renders data from REST + shortcodes       │
                 │                                               │
                 │  PLUGIN (nctb-learning-hub) — ALL logic       │
                 │   • Roles: nctb_student, nctb_teacher         │
                 │   • Curriculum CMS (Book→Unit→Lesson)         │
                 │   • Modules (video course container)          │
                 │   • Notes/explanations content type           │
                 │   • Practice + marking + hints                │
                 │   • Progress / mastery / mistakes / revision  │
                 │   • Entitlements (free/paid + AI-paid)         │
                 │   • AI adapter (server-side, paid-gated)      │
                 │   • Board question DB + analytics             │
                 │   • Teacher profile + student profile         │
                 │   • REST API (/nctb/v1/*)                     │
                 │                                               │
                 │  DATA: MySQL custom tables (via migrations)   │
                 │  COMMERCE: WooCommerce → entitlements         │
                 └─────────────────────────────────────────────┘
                          │                         │
                   AI provider (server)      Payment gateways
                   Claude / OpenAI           bKash / Nagad / SSLCommerz
                          │
                   YouTube (embedded video, unlisted/public)
```

**Key idea:** the plugin is a *learning engine* + *marketplace of access*. Subjects, classes, notes, and video courses are **content poured into it**. The engine never changes when you add Physics or a new teacher course.

---

## 2. Data model additions (beyond the 20 tables already built)

The existing 20 tables (curriculum, activities, questions, attempts, progress, mastery, mistakes, revision, entitlements, AI usage, writing/speaking, board exams/questions) stay as-is. Add the following, each via a **new versioned migration**:

**Teachers & unified profiles**
- `wp_nctb_teacher_profiles` — teacher_id (user), display_name, school, district, subjects_taught (json), classes_taught (json), bio, verification_status (`unverified|pending|verified`), avatar_attachment_id, created_at.
- Reuse the existing student profile mechanism; do **not** duplicate. A user has one WP account; role determines which profile record applies.

**Modules & video courses** (the new content container for free/paid video learning)
- `wp_nctb_modules` — id, title, slug, audience (`student|teacher`), subject_term_id (nullable), class_term_id (nullable), access (`free|paid`), price_ref (nullable, links WooCommerce product), summary, cover_attachment_id, status (`draft|published`), menu_order, created_at.
- `wp_nctb_module_items` — id, module_id, type (`video|note|pdf|lesson_ref|quiz_ref`), title, youtube_id (nullable), note_id (nullable), lesson_id (nullable), duration_seconds (nullable), menu_order, access (`free|paid`).
- `wp_nctb_module_progress` — id, user_id, module_id, item_id, completed (bool), completed_at.

**Notes / explanations** (free content type — the "notes, graphical explanations" tier)
- Prefer a **Custom Post Type `nctb_note`** (title, body, featured image, taxonomies: Class, Subject, Topic) rather than a custom table — notes are editorial content and benefit from the WP editor, revisions, and SEO. Add `access` meta (`free|paid`) and optional `lesson_id` link.

**AI entitlement**
- No new table needed if you extend the existing entitlement types with an `ai_access` product/tier. Add an `ai` entitlement kind evaluated by `NCTB_Entitlements`. Keep the daily quota table as a safety cap.

> The agent must design each migration to be **idempotent** and reversible-safe, following `NCTB_Migrations` conventions already in the repo.

---

## 3. Roles, capabilities & the single portal

**Roles**
- `nctb_student` — exists. Keep.
- `nctb_teacher` — **new**. Capabilities: manage own teacher profile, access teacher modules (free + entitled paid), use teacher AI tools (if entitled), download classroom resources. **No** capability to edit curriculum or other users.
- `nctb_content_editor` (optional, admin-side) — for your content team to author lessons/notes/modules without full admin.

**One portal, role-aware routing**
- Same login. After login, route by role: students → student dashboard; teachers → teacher dashboard.
- A user could theoretically be both (a teacher who also studies) — support a role switch later, but v1 assumes one primary role chosen at onboarding.
- Shared components (navbar, footer, design system) are identical; the *content and dashboard widgets* differ by role.

---

## 4. The phased build sequence

> Numbering continues from the repo. Each phase lists: **Goal · Backend · Frontend · Data · Definition of Done (DoD)**. The agent builds one phase, tests it, writes a build report, updates `BUILD_STATE.md`, then stops. Phases are ordered so the product is *launchable after Phase 15* and everything after is expansion.

### PHASE 14 — Verify, secure, harden (do this first, no new features)
- **Goal:** Trust the existing build; make it safe to take money.
- **Backend:** Audit every REST endpoint for permission callbacks + nonces; confirm `$wpdb->prepare()` everywhere; add rate limiting on AI and auth endpoints; confirm student-data isolation; sanitize/escape audit; confirm AI keys are server-only.
- **Frontend:** Low-bandwidth pass — measure and shrink page weight; lazy-load images/video; confirm every interactive screen works on a low-end Android over throttled 3G.
- **Data:** Verify all migrations apply cleanly on a fresh DB; backup/restore tested.
- **DoD:** A written security checklist all green (with evidence, not claims); the app clicked through end-to-end by a human with a real student account; performance budget met (define a target, e.g. usable first paint on 3G — set the exact number during this phase).

### PHASE 15 — Production launch readiness (English only, students only)
- **Goal:** Go live with the proven slice.
- **Backend:** Connect live payment gateways (bKash, Nagad, SSLCommerz) to WooCommerce → entitlements; transactional email (order receipts, password reset); error logging/monitoring.
- **Frontend:** Final polish of marketing + student app per design system; SEO (titles, meta, OpenGraph, sitemap, schema.org for educational content); cookie/consent + privacy pages.
- **Data:** Production DB, automated backups, staging vs. production separation.
- **DoD:** A real student can sign up, buy with bKash, access paid lessons, and use paid AI — verified with a real (small) transaction. Site indexed by Google. Backups running.

### PHASE 16 — Teacher role & unified portal
- **Goal:** Teachers and students on one portal/database.
- **Backend:** `nctb_teacher` role + capabilities; `wp_nctb_teacher_profiles` migration; teacher onboarding REST; role-aware routing.
- **Frontend:** Teacher onboarding wizard (subjects, classes, school, district); teacher dashboard shell (empty widgets ok); role switch in nav.
- **Data:** Teacher profile table.
- **DoD:** A teacher can register, complete onboarding, and land on a teacher dashboard distinct from the student one; students unaffected; no data leaks across roles.

### PHASE 17 — Modules & video-course system (free + paid, both audiences)
- **Goal:** The reusable container for YouTube-based free/paid courses, for students AND teachers.
- **Backend:** `wp_nctb_modules`, `wp_nctb_module_items`, `wp_nctb_module_progress` migrations; module CRUD in admin; entitlement gating (`access = free|paid`) reusing `NCTB_Entitlements`; REST for module list/detail/progress.
- **Frontend:** Module catalog (filter by audience/subject/class/free-paid); module player page (YouTube embed + item list + progress ticks + paywall card for locked items); "My Courses" on both dashboards.
- **Data:** As above.
- **DoD:** Admin can create a free module and a paid module; a student sees free content, is paywalled on paid, and can buy access; progress ticks persist; same works for a teacher module.

### PHASE 18 — Notes & explanations content type (free tier magnet)
- **Goal:** Fast, SEO-friendly free notes / graphical explanations.
- **Backend:** `nctb_note` CPT with Class/Subject/Topic taxonomies, `access` meta, optional lesson link; REST + sitemap inclusion.
- **Frontend:** Notes library (browse/filter/search), single-note reading view (design-system typography, image/diagram support, print-friendly), "related lesson / practice" cross-links.
- **DoD:** A note can be authored, published, found via search/SEO, and cross-linked to a lesson; free notes are genuinely useful without login.

### PHASE 19 — AI as a paid product (students + teachers)
- **Goal:** Make AI a clean paid entitlement; add teacher AI tools.
- **Backend:** Add `ai` entitlement kind; hard server-side gate on all AI endpoints; extend adapter with **teacher tools**: lesson-plan generator, "explain this concept for a weak student," class-test/marking assistant — all grounded, all guardrailed, all quota-tracked.
- **Frontend:** Student AI tutor already exists — put it behind the AI paywall with a clear upgrade prompt. Teacher AI panel on teacher dashboard.
- **DoD:** No AI runs without entitlement (verified); a paying student uses the tutor; a paying teacher generates a lesson plan; quotas enforced; costs observable in logs.

### PHASE 20 — Second subject: ICT (content-only proof of the engine)
- **Goal:** Prove that adding a subject is a **content** operation.
- **Backend:** None expected. If any code is needed, STOP and flag — that means the engine isn't generic enough.
- **Frontend:** None beyond what modules/lessons already render.
- **Data/Content:** ICT books/units/lessons/questions/notes/board questions imported per `03_CONTENT_OPERATIONS.md`.
- **DoD:** Full ICT subject live using only content import, zero engine changes.

### PHASE 21 — Maths engine extension (the one hard subject)
- **Goal:** Support mathematics properly.
- **Backend:** Math rendering (KaTeX/MathJax — pick after evaluating licensing/perf); a math-aware answer input + marking (start with structured/numeric answers and formula equivalence for simple cases; step-by-step checking is a later stretch goal, not v1).
- **Frontend:** Math input widget; rendered equations in lessons/notes/questions.
- **DoD:** A student can read a maths lesson with rendered equations, attempt a numeric/expression question, and be marked deterministically for the supported cases; unsupported cases fall back gracefully (e.g. self-check or teacher review).

### PHASE 22 — Science subjects as notes + video + questions (Physics, Chemistry, Biology)
- **Goal:** Broaden coverage cheaply, content-first.
- **Backend:** None (reuse notes + modules + questions). Diagrams handled as images.
- **DoD:** Physics/Chemistry/Biology available as notes + video courses + question banks using only content import.

### PHASE 23 — Extend down to JSC / class 6–8
- **Goal:** Younger grades in launched subjects (parent-buyer segment).
- **Backend:** Confirm Class taxonomy covers 6–8; adjust onboarding options; possibly parent-friendly messaging. Content-driven otherwise.
- **DoD:** A class-6 student/parent can onboard and access class-6 content.

### PHASE 24 — Teacher content depth & light community
- **Goal:** Real teacher value: "how to teach," easy presentation creation, AI in education, classroom resources; optional teacher Q&A.
- **Backend:** Downloadable resource attachments on teacher modules; optional lightweight Q&A (a CPT or a vetted plugin) — evaluate build-vs-plugin.
- **DoD:** Teachers complete a "how to teach topic X" module, download a slide template, and (if built) ask/answer a question.

### PHASE 25+ — Ongoing expansion
- More subjects, deeper teacher courses, richer analytics, engagement (revision reminders — email first; SMS only if cost justifies). Each new subject = content, gated on the previous showing real usage.

---

## 5. Backend engineering standards (for the agent)

- **PHP/WordPress:** follow WordPress coding standards (repo has `phpcs.xml.dist`); keep services single-responsibility (the repo already does this well — `NCTB_*_Service` classes).
- **REST:** namespaced `/nctb/v1/*`; permission callbacks always; consistent JSON error shapes; nonce on writes; pagination on list endpoints.
- **Entitlements are the single gate.** Free/paid, subject passes, subscriptions, and **AI access** all evaluate through `NCTB_Entitlements`. No feature checks access on its own.
- **AI adapter:** one server-side adapter, provider-swappable (Claude/OpenAI/mock). Every prompt grounded in curriculum context. Guardrails: never reveal quiz answers; never fabricate board answers; label formative feedback as non-official. Log tokens for cost tracking. Respect per-user daily quota even for paid users (abuse cap).
- **Caching:** object cache + page cache friendly; cache expensive reads (board analytics, catalogs); bust on content update. Assume low-bandwidth users throughout.
- **Testing:** each phase ships automated checks where feasible **and** a human-verifiable DoD. Never claim a test passed without running it (repo rule).

---

## 6. Frontend engineering standards (for the agent)

- **Follow `02_DESIGN_SYSTEM.md` exactly** — tokens, components, spacing, type. No ad-hoc styles.
- **Progressive enhancement:** core content readable without JS; interactivity layered on. Critical for weak devices/networks.
- **Keep JS lean:** prefer vanilla JS / small modules over heavy frameworks. Only introduce a framework on a screen that truly needs complex state (most don't). Never ship a big bundle to a reading page.
- **Accessibility:** WCAG 2.1 AA target — keyboard nav, focus states, color contrast, alt text, semantic HTML, proper Bangla language attributes (`lang="bn"`).
- **Bilingual by construction:** UI strings translatable via WP i18n; Bangla-first default; correct Bangla fonts and line-height (Bangla needs more vertical space than Latin).
- **Performance budget:** set and enforce a page-weight budget per template in Phase 14; images responsive + lazy; video via lightweight YouTube facade (load the real iframe only on click) to save data.

---

## 7. Handoff protocol for the agent (each phase)

1. `git pull`. Read `BUILD_STATE.md` → current phase. Read this file's phase spec + `02_DESIGN_SYSTEM.md` + (if content) `03_CONTENT_OPERATIONS.md`.
2. State the files, tables, endpoints, and screens you will add/change — **for this phase only**.
3. Build only that phase. Placeholders for future phases allowed if clearly marked.
4. Test against the DoD. Run the checks; never claim untested passes. If you can't run something, say so.
5. Write `docs/build-history/BUILD_REPORT_PHASE_<N>.md`.
6. Update `BUILD_STATE.md` (move phase to done, set next as NEXT).
7. Commit + push. **STOP** for human review.

---

## 8. Definition of "launchable" and "fundable"

- **Launchable (after Phase 15):** a real student can sign up, pay via bKash, learn SSC/HSC English interactively, and use paid AI — safely and on a cheap phone.
- **Fundable (target, not guaranteed):** you have real, retained, paying users across ≥2 subjects and an active teacher cohort, with usage/retention/revenue data to show. Funders pay for *traction and evidence*, not for the size of an empty structure. Build the structure, then earn the evidence.
