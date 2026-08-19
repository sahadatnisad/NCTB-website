# NCTB AI Learning Hub — Master Blueprints

> A complete blueprint set for building, launching, marketing, and operating the NCTB AI Learning Hub as a **lifetime project** on WordPress. Written to be handed to an AI coding agent (Antigravity) and to your own team.
>
> **Prepared:** 19 August 2026 · Based on review of the existing repo `sahadatnisad/NCTB-website` (phases 0–13 complete).

---

## Read in this order

1. **`01_BUILD_BLUEPRINT.md`** — The full-stack, phase-by-phase build plan (backend + frontend + database + acceptance criteria). This is the master file for Antigravity. Continues from your existing phase 13. Covers: teacher role, one-portal architecture, modules/video-course system, notes system, AI-as-paid, subject expansion (ICT → Maths → Sciences → class 6–8), and teacher content.

2. **`02_DESIGN_SYSTEM.md`** — The world-class UI/UX & brand system: color palette, bilingual typography, spacing, components, motion, accessibility, and performance-as-design. **Every screen must follow this.**

3. **`03_CONTENT_OPERATIONS.md`** — How content actually gets made and imported: NCTB-aligned authoring in Markdown/YAML, the WP-CLI importer, notes & diagrams, AI for students *and* teachers, student/teacher profiles, video courses, and free/paid management. This is your day-to-day once the structure exists.

4. **`04_MARKETING_PLAN.md`** — Positioning, audiences, the growth flywheel, channels (SEO/YouTube/Facebook/teacher partnerships), launch sequence, metrics, and the funding narrative.

5. **`05_PUBLISHING_REQUIREMENTS_RISKS_COSTS.md`** — Publishing procedure (Docker → staging → production), requirements checklist (technical/payments/legal/content/people), a full risk register, and cost structure (categories to get real quotes for).

---

## The strategy in five sentences

1. You already built an excellent, working learning engine — one subject (English), one audience (students). 
2. The plan turns that into a platform by adding **a teacher role on the same database**, a **modules/video system**, a **notes system**, and **AI as a paid feature** — the structure — *before* pouring in more subjects.
3. Once the structure is solid, **adding a subject or course is a content job, not an engineering job** — exactly the "build structure, add modules gradually" model you wanted.
4. Launch small (English, students), **prove the loop and the economics**, then expand one subject at a time gated on real usage.
5. **Traction is the goal** — real retained paying users + an active teacher base — and that evidence, not the size of an empty structure, is what makes the project fundable later.

## Non-negotiables carried through every document
- **AI is paid**, server-side, grounded, quota-capped — never called for anything deterministic.
- **One WordPress install, one database, two roles** (student + teacher).
- **Content is data, not code** — new subjects never require engine changes.
- **Mobile-first, low-bandwidth, Bangla-first** — your students are on cheap phones.
- **Verify before you trust** — the existing build's "done" statuses, the legal position on NCTB content, and every cost/price figure all need real verification. Nothing in these docs invents a number or a source.

## What to do next (this month)
1. Give `01`–`03` to Antigravity and have it start at **Build Phase 14** (verify/secure/harden) — no new features until the current build is trusted.
2. Confirm the **NCTB content-rights** position (legal) — this shapes everything content-side.
3. Stand up the site yourself and **click through every claimed feature** — treat phases 0–13 as unverified until you've seen them work.
4. Start the **curriculum map** for English + ICT (your launch content backlog).
5. Get **real quotes** for hosting, payment gateways, and AI usage; run a tiny beta to measure AI-cost-per-user and willingness to pay.
