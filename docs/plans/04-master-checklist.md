# NCTB AI LEARNING HUB — MASTER CHECKLIST & PROGRESS AUDIT

> **Comprehensive Status Checklist for Masterplan-1 & Frontend Design Plan-1**
> **Current Version:** `0.12.0` | **Environment:** WordPress 7.0.4 on Local Docker (`http://localhost:8080`)
> **Git Repository:** https://github.com/sahadatnisad/NCTB-website | **Branch:** `main`
> **Last Updated:** 2026-08-19

---

## 📑 TABLE OF CONTENTS
1. [Plan 1: Master Plan Checklist (18 Phases)](#1-plan-1-master-plan-checklist-18-phases)
2. [Plan 2: Frontend Design Plan Checklist (Public Marketing & Student App)](#2-plan-2-frontend-design-plan-checklist)
3. [Database & Migrations Audit (13 Tables)](#3-database--migrations-audit)
4. [REST API Endpoints Inventory (28 Endpoints)](#4-rest-api-endpoints-inventory)
5. [Live Platform Routes & Verification](#5-live-platform-routes--verification)
6. [Next Steps for Laptop / Claude Web](#6-next-steps-for-laptop--claude-web)

---

## 1. PLAN 1: MASTER PLAN CHECKLIST (18 PHASES)

### ✅ Completed & Fully Verified Phases (Phases 0–12)

- [x] **Phase 0: Safe WordPress Development Environment**
  - [x] Versioned migration runner `NCTB_Migrations`
  - [x] Environment-gated logger `NCTB_Logger`
  - [x] Plugin lifecycle activation/deactivation hooks
  - [x] Child theme skeleton (`nctb-child-theme`)
  - [x] Docker compose setup (`nctb-wordpress` + `nctb-mysql`)

- [x] **Phase 1: Visual Shell and Navigation**
  - [x] Header and footer layout with responsive navigation drawer
  - [x] Bilingual typography tokens (Hind Siliguri + Inter UTF-8)
  - [x] Contextual nav switching (Marketing menu vs Student app menu)
  - [x] UI design tokens, button states, card components

- [x] **Phase 2: Student Accounts and Onboarding**
  - [x] Custom role `nctb_student` with capability mapping
  - [x] `NCTB_Student_Profile` metadata service (grade, class, stream, curriculum version, study target)
  - [x] 4-step interactive onboarding wizard (`[nctb_onboarding]` / `page-onboarding.php`)
  - [x] Resumable onboarding state with localStorage & REST synchronization
  - [x] REST endpoints `/nctb/v1/student/*`

- [x] **Phase 3: Curriculum + Book + Unit + Lesson CMS**
  - [x] Custom post types: `nctb_book`, `nctb_unit`, `nctb_lesson`
  - [x] 6 Taxonomies: Class, Subject, Stream, Curriculum Version, Board Topic, Skill Domain
  - [x] Custom tables: `wp_nctb_concepts`, `wp_nctb_learning_outcomes`, `wp_nctb_lesson_concepts` (Migration `0.3.0`)
  - [x] Admin concepts manager screen & meta boxes
  - [x] Read-only REST endpoints `/nctb/v1/curriculum/*`
  - [x] Sample Book→Unit→Lesson seeder (`NCTB_Curriculum_Seeder`)

- [x] **Phase 4: One Gold-Standard Interactive Lesson**
  - [x] 14 standard reusable activity block types (`NCTB_Lesson_Activity_Types`)
  - [x] Custom table: `wp_nctb_lesson_activities` (Migration `0.4.0`)
  - [x] Admin activity editor meta box with reordering & block templates
  - [x] Full authentic NCTB sample lesson seeded (HSC English 1st Paper: Unit 1, Lesson 1 — Nelson Mandela)
  - [x] Mobile-first stepper and progress bar with URL hash & state resumption in `single-nctb_lesson.php`

- [x] **Phase 5: Practice and Question Engine**
  - [x] 4 Question types: `mcq`, `fill_in_blank`, `short_answer`, `error_correction`
  - [x] Custom tables: `wp_nctb_questions`, `wp_nctb_question_options`, `wp_nctb_question_concepts`, `wp_nctb_attempts` (Migration `0.5.0`)
  - [x] Central Marking Service (`NCTB_Marking_Service`) evaluating deterministically without AI
  - [x] Progressive Hint Service (`NCTB_Hint_Service`) with 3 scaffolded levels
  - [x] Admin practice questions manager under Lessons
  - [x] Interactive live practice quiz with attempt logging and score celebration

- [x] **Phase 6: Progress, Mastery, Mistakes, Spaced Revision**
  - [x] Custom tables: `wp_nctb_progress`, `wp_nctb_mastery`, `wp_nctb_mistakes`, `wp_nctb_review_schedule` (Migration `0.6.0`)
  - [x] Lesson progress tracking with step positions & completion timestamps
  - [x] Concept mastery calculation service (`NCTB_Mastery_Service`) with strict completion vs mastery separation
  - [x] Smart Mistake Notebook service (`NCTB_Mistakes_Service`) with auto-graduation (2 consecutive correct attempts)
  - [x] Spaced Repetition service (`NCTB_Spaced_Revision_Service`) with SM-2 interval ladder (1, 3, 7, 14, 30 days)
  - [x] Dedicated student screens: `/mistakes/`, `/revision/`, `/progress/`
  - [x] REST endpoints `/nctb/v1/progress/*`, `/nctb/v1/mistakes/*`, `/nctb/v1/revision/*`

- [x] **Phase 7: Functional Student Dashboard**
  - [x] Dashboard Aggregation Service (`NCTB_Dashboard_Service`)
  - [x] Rules-based home study guide layout (`/dashboard/`):
    - [x] Continue Learning hero card (deep-linking to student's exact activity step)
    - [x] Spaced Revision Due action card
    - [x] Needs Attention mistake alert card
    - [x] Top learning KPIs bar (Completed lessons, Mastered concepts, Mistakes to fix, Revision due)
    - [x] Enrolled textbooks progress bars with percentage completion
  - [x] REST endpoint `GET /nctb/v1/student/dashboard`

- [x] **Phase 8: Payments and Entitlements**
  - [x] Centralized Entitlement Service (`NCTB_Entitlements`)
  - [x] Multi-tier access evaluation (Free preview, Direct lesson pass, Unit pack, Book pack, Subscription, Admin grant)
  - [x] Custom tables: `wp_nctb_entitlements`, `wp_nctb_entitlement_audit` (Migration `0.8.0`)
  - [x] WooCommerce order listener (`woocommerce_order_status_completed`) granting passes server-side
  - [x] Access-denied paywall banner component
  - [x] Admin Entitlements manager screen under Lessons with manual grant & revocation
  - [x] Student My Purchases page (`/purchases/`)
  - [x] REST endpoints `/nctb/v1/entitlements/*`

- [x] **Phase 9: Contextual AI Tutor**
  - [x] Server-side provider adapter (`NCTB_AI_Adapter`) supporting Claude, OpenAI, and local mock fallback
  - [x] Prompt grounder (`NCTB_AI_Context_Builder`) assembling lesson text, outcomes, vocabulary, and mistake history
  - [x] Socratic guardrails (`NCTB_AI_Tutor`) — never giving away quiz answers; board-exam anti-hallucination
  - [x] Daily quota tracker (`NCTB_AI_Usage`) with `wp_nctb_ai_conversations` and `wp_nctb_ai_usage` tables (Migration `0.9.0`)
  - [x] Slide-out interactive AI Tutor drawer in `single-nctb_lesson.php` with 5 quick action chips (`explain`, `bangla`, `hint`, `example`, `why_wrong`)
  - [x] REST endpoints `/nctb/v1/tutor/*`

- [x] **Phase 10: Writing, Listening & Speaking Enhancements**
  - [x] 6-Stage iterative writing pipeline (`NCTB_Writing_Service`): Task → Brainstorm → Draft → AI Feedback → Revision → Final
  - [x] Multi-criteria rubric evaluation (Structure, Grammar, Vocabulary, Action Plan)
  - [x] Listening Audio Player service (`NCTB_Listening_Service`) with audio player & toggleable transcript
  - [x] Speaking Practice service (`NCTB_Speaking_Service`) with audio recording timer & formative pronunciation feedback with prominent non-official disclaimer
  - [x] Custom tables: `wp_nctb_writing_submissions`, `wp_nctb_speaking_submissions` (Migration `0.10.0`)
  - [x] Embedded interactive workbenches in `single-nctb_lesson.php`
  - [x] REST endpoints `/nctb/v1/skills/*`

- [x] **Phase 11: Authentic Board-Question Database**
  - [x] Board Question Service (`NCTB_Board_Service`) with 10 Education Boards metadata
  - [x] Custom tables: `wp_nctb_board_exams`, `wp_nctb_board_questions` (Migration `0.11.0`)
  - [x] Strict authenticity provenance: `is_authentic_board = 1`, `is_verified = 1`, official source reference
  - [x] Admin Board Questions manager screen (`edit.php?post_type=nctb_lesson&page=nctb-board-questions`)
  - [x] Student Board Questions Archive hub (`/board-questions/`) with live Level/Board/Year filter bar
  - [x] Expandable verified answer & marking scheme accordions
  - [x] Embedded authentic board questions practice widget in `single-nctb_lesson.php`
  - [x] REST endpoints `/nctb/v1/board/questions`, `/nctb/v1/board/lesson/{id}`

- [x] **Phase 12: Board Pattern Analytics**
  - [x] Board Pattern Analytics Service (`NCTB_Board_Analytics_Service`)
  - [x] Topic frequency recurrence engine & total marks evaluation
  - [x] Question type distribution analysis (`mcq`, `short_answer`, `flow_chart`, `summary`, etc.)
  - [x] Board breakdown & yearly volume trends (2018–2024)
  - [x] Student Board Pattern Analytics Hub (`/board-analytics/`) with level switcher, 4 metric KPIs, topic frequency progress bars, and 1-click deep links to board practice
  - [x] Prominent historical-only disclaimer guardrail (strictly historical analysis, never predictions)
  - [x] REST endpoint `GET /nctb/v1/board/analytics`

---

### 🔜 Remaining Phases to Build (Phases 13–18)

- [ ] **Phase 13: English MVP Content Library**
  - [ ] 20–30 high-quality SSC English lessons
  - [ ] 20–30 high-quality HSC English lessons
  - [ ] Question banks & micro-concept mappings
  - [ ] Vocabulary mastery records
  - [ ] Human review & publication workflow
- [ ] **Phase 14: Private Beta (Security, Performance & QA)**
  - [ ] Load testing, caching headers, object cache integration
  - [ ] Security penetration checks, nonce hardening, rate limiting
  - [ ] Beta student feedback instrumentation
- [ ] **Phase 15: Public English Launch**
  - [ ] Payment gateway live keys (bKash, Nagad, SSLCommerz)
  - [ ] Production domain, CDN & transactional email setup
  - [ ] SEO, OpenGraph metadata, sitemaps
- [ ] **Phase 16: Complete English Curriculum Coverage**
  - [ ] All remaining SSC & HSC English 1st & 2nd paper units
- [ ] **Phase 17: Add ICT Subject**
  - [ ] SSC & HSC ICT curriculum mapping, coding exercises & questions
- [ ] **Phase 18: Add Bangla & Other Core Subjects**
  - [ ] Bangla 1st/2nd, General Science, Math expansion

---

## 2. PLAN 2: FRONTEND DESIGN PLAN CHECKLIST

### 🌐 Public Marketing Site Pages (`themes/nctb-child-theme/`)

- [x] **Homepage (`/` / `front-page.php`):**
  - [x] Hero section with high-converting value proposition and primary CTAs
  - [x] 6-Pillar feature showcase cards
  - [x] Interactive Curriculum & Subjects preview
  - [x] Socratic AI Tutor spotlight section
  - [x] Student & Parent social proof testimonials
  - [x] Final CTA banner & trust badges

- [x] **How It Works (`/how-it-works/` / `page-how-it-works.php`):**
  - [x] 5-Step learning cycle visual walkthrough
  - [x] Socratic tutor scaffolding explanation
  - [x] Smart mistake notebook & spaced revision guide

- [x] **Subjects Overview (`/subjects/` / `page-subjects.php`):**
  - [x] SSC & HSC subject directory with curriculum syllabus breakdowns
  - [x] Direct links to SSC English and HSC English detail pages

- [x] **SSC English Hub (`/ssc-english/` / `page-ssc-english.php`):**
  - [x] SSC English 1st & 2nd Paper complete syllabus breakdown
  - [x] Paper structure, question distributions, board practice links

- [x] **HSC English Hub (`/hsc-english/` / `page-hsc-english.php`):**
  - [x] HSC English 1st & 2nd Paper complete syllabus breakdown
  - [x] Units breakdown, board examination pattern overview

- [x] **Pricing & Passes (`/pricing/` / `page-pricing.php`):**
  - [x] Transparent pricing matrix (Free Tier, Single Subject Pass, All-Access Subscription)
  - [x] Feature comparison table & bKash/Nagad payment methods

- [x] **Frequently Asked Questions (`/faq/` / `page-faq.php`):**
  - [x] Interactive accordion with FAQ categories (Curriculum, AI Tutor, Payments, Mobile)

- [x] **Contact & Support (`/contact/` / `page-contact.php`):**
  - [x] Support contact form, helpline details, Dhaka office address

- [x] **Privacy Policy (`/privacy-policy/` / `page-privacy-policy.php`):**
  - [x] Strict student privacy policy, zero data-selling guarantee

---

### 🎓 Student Learning App Screens

- [x] **Student Onboarding (`/onboarding/` / `page-onboarding.php`):**
  - [x] 4-step wizard: Grade/Level → Subject/Stream → Version → Target & Schedule
  - [x] LocalStorage state resumption + REST synchronization

- [x] **Student Dashboard (`/dashboard/` / `page-dashboard.php`):**
  - [x] Continue Learning hero card with exact activity step resume
  - [x] Spaced Revision Due action card
  - [x] Needs Attention mistake alert card
  - [x] 4 Top learning KPIs & enrolled textbooks progress

- [x] **Textbook & Unit Browsing (`/book/`, `single-nctb_book.php`, `single-nctb_unit.php`):**
  - [x] Textbook library grid with Class & Subject taxonomy filters
  - [x] Unit syllabus accordion with learning outcomes and lesson sequence

- [x] **Interactive Single Lesson (`single-nctb_lesson.php`):**
  - [x] Mobile-first activity stepper with URL hash navigation
  - [x] 14 Activity block renderers (Reading, Vocabulary, Grammar, Quiz, etc.)
  - [x] Interactive Practice Quiz with deterministic marking and 3-level hint ladder
  - [x] Writing Workbench with live word counter, draft saving, AI feedback & revision
  - [x] Listening Player with audio controls & transcript toggle
  - [x] Speaking Practice Recorder with timer & formative feedback
  - [x] Contextual AI Tutor slide-out drawer with 5 quick action chips
  - [x] Authentic Board Questions practice widget attached to lesson topic
  - [x] Gated Entitlement paywall card for non-entitled students

- [x] **My Mistake Notebook (`/mistakes/` / `page-mistakes.php`):**
  - [x] Active mistakes list with error context, correct answers & explanation
  - [x] Practice Mistake button & graduation streak tracker (🎓 2/2 graduation)

- [x] **Spaced Revision Schedule (`/revision/` / `page-revision.php`):**
  - [x] Due items list grouped by overdue, today, and upcoming
  - [x] SM-2 interval indicators & 1-click revision practice launcher

- [x] **Learning Progress & Mastery (`/progress/` / `page-progress.php`):**
  - [x] Overall syllabus completion percentage
  - [x] Micro-concept mastery matrix (Mastered, In Progress, Needs Attention)
  - [x] Total questions answered, accuracy rate, study streak

- [x] **My Purchases & Passes (`/purchases/` / `page-purchases.php`):**
  - [x] Active passes, book packs, subscriptions with validity & expiration dates

- [x] **Board Questions Bank (`/board-questions/` / `page-board-questions.php`):**
  - [x] Level/Board/Year multi-filter bar
  - [x] Question cards with MCQ options and verified answer accordions

- [x] **Board Pattern Analytics (`/board-analytics/` / `page-board-analytics.php`):**
  - [x] HSC/SSC level switcher & 4 metric KPI cards
  - [x] High-frequency topic ranking bars with direct practice deep links
  - [x] Question type and education board distribution breakdown

---

## 3. DATABASE & MIGRATIONS AUDIT (13 TABLES)

All 13 custom database tables are created via versioned idempotent migrations (`NCTB_Migrations`):

| # | Table Name | Migration | Purpose | Status |
|---|---|---|---|---|
| 1 | `wp_nctb_concepts` | `0.3.0` | Micro-concept definitions & taxonomy mapping | ✅ Active |
| 2 | `wp_nctb_learning_outcomes` | `0.3.0` | Lesson-level curriculum learning outcomes | ✅ Active |
| 3 | `wp_nctb_lesson_concepts` | `0.3.0` | Many-to-many link between lessons and concepts | ✅ Active |
| 4 | `wp_nctb_lesson_activities` | `0.4.0` | 14 activity blocks per lesson with sequence order | ✅ Active |
| 5 | `wp_nctb_questions` | `0.5.0` | Practice questions bank (MCQ, Fill blank, etc.) | ✅ Active |
| 6 | `wp_nctb_question_options` | `0.5.0` | MCQ options and distractors | ✅ Active |
| 7 | `wp_nctb_question_concepts` | `0.5.0` | Concept mapping for diagnostic scoring | ✅ Active |
| 8 | `wp_nctb_attempts` | `0.5.0` | Student attempt logs with scores and hints used | ✅ Active |
| 9 | `wp_nctb_progress` | `0.6.0` | Lesson completion, last step, and time spent | ✅ Active |
| 10 | `wp_nctb_mastery` | `0.6.0` | Concept mastery scores (0–100%) and streaks | ✅ Active |
| 11 | `wp_nctb_mistakes` | `0.6.0` | Active mistake notebook with graduation tracking | ✅ Active |
| 12 | `wp_nctb_review_schedule` | `0.6.0` | SM-2 spaced repetition review dates & intervals | ✅ Active |
| 13 | `wp_nctb_entitlements` | `0.8.0` | Student passes, purchases, and subscriptions | ✅ Active |
| 14 | `wp_nctb_entitlement_audit` | `0.8.0` | Audit trail of manual admin grants & revocations | ✅ Active |
| 15 | `wp_nctb_ai_conversations` | `0.9.0` | Grounded AI tutor dialog history | ✅ Active |
| 16 | `wp_nctb_ai_usage` | `0.9.0` | Daily token and request quota tracking | ✅ Active |
| 17 | `wp_nctb_writing_submissions` | `0.10.0` | Multi-stage writing drafts & rubric feedback | ✅ Active |
| 18 | `wp_nctb_speaking_submissions` | `0.10.0` | Audio recordings & formative practice feedback | ✅ Active |
| 19 | `wp_nctb_board_exams` | `0.11.0` | Board exam papers records (SSC/HSC, Board, Year) | ✅ Active |
| 20 | `wp_nctb_board_questions` | `0.11.0` | Verified authentic board exam questions & answers | ✅ Active |

---

## 4. REST API ENDPOINTS INVENTORY (28 ENDPOINTS)

### Onboarding & Profile (`/nctb/v1/student/*`)
- `POST /nctb/v1/student/onboarding` — Save onboarding preferences.
- `GET  /nctb/v1/student/profile` — Retrieve student profile data.
- `GET  /nctb/v1/student/dashboard` — Aggregated dashboard state.

### Curriculum (`/nctb/v1/curriculum/*`)
- `GET /nctb/v1/curriculum/books` — List all published textbooks.
- `GET /nctb/v1/curriculum/books/{id}` — Get single book with units.
- `GET /nctb/v1/curriculum/units/{id}` — Get single unit with lessons.
- `GET /nctb/v1/curriculum/lessons/{id}` — Get single lesson with activities.

### Practice & Question Engine (`/nctb/v1/practice/*`)
- `GET  /nctb/v1/practice/questions` — Retrieve questions for lesson.
- `POST /nctb/v1/practice/submit` — Submit answer & evaluate score.
- `POST /nctb/v1/practice/hint` — Request progressive hint level.

### Progress & Mastery (`/nctb/v1/progress/*`, `/nctb/v1/mistakes/*`, `/nctb/v1/revision/*`)
- `POST /nctb/v1/progress/step` — Update current step in lesson.
- `POST /nctb/v1/progress/complete` — Mark lesson as completed.
- `GET  /nctb/v1/progress/summary` — Full student mastery & progress report.
- `GET  /nctb/v1/mistakes/list` — Retrieve active mistake notebook items.
- `POST /nctb/v1/mistakes/resolve` — Practice and graduate a mistake.
- `GET  /nctb/v1/revision/due` — Retrieve items due for spaced review.
- `POST /nctb/v1/revision/complete` — Record spaced revision result.

### Payments & Entitlements (`/nctb/v1/entitlements/*`)
- `GET  /nctb/v1/entitlements/check` — Check access for a lesson/unit/book.
- `GET  /nctb/v1/entitlements/my` — List all active passes for current student.

### Contextual AI Tutor (`/nctb/v1/tutor/*`)
- `POST /nctb/v1/tutor/ask` — Ask Socratic AI Tutor grounded question.
- `GET  /nctb/v1/tutor/quota` — Check remaining daily interaction quota.

### Skills Workbench (`/nctb/v1/skills/*`)
- `POST /nctb/v1/skills/writing/draft` — Save writing draft.
- `POST /nctb/v1/skills/writing/feedback` — Request multi-criteria rubric evaluation.
- `POST /nctb/v1/skills/writing/final` — Submit final writing piece.
- `GET  /nctb/v1/skills/writing/submission` — Retrieve current draft & stage.
- `POST /nctb/v1/skills/speaking/submit` — Record audio attempt & get formative feedback.

### Board Questions & Analytics (`/nctb/v1/board/*`)
- `GET /nctb/v1/board/questions` — Filter authentic board questions.
- `GET /nctb/v1/board/lesson/{id}` — Retrieve board questions for lesson.
- `GET /nctb/v1/board/analytics` — Aggregate historical exam intelligence.

---

## 5. LIVE PLATFORM ROUTES & VERIFICATION

All 11 platform routes return **HTTP 200** on local environment:

| URL Route | Template | Purpose | Status |
|---|---|---|---|
| `http://localhost:8080/` | `front-page.php` | Public Marketing Homepage | ✅ 200 OK |
| `http://localhost:8080/book/` | `archive-nctb_book.php` | Curriculum Textbooks Library | ✅ 200 OK |
| `http://localhost:8080/?p=15` | `single-nctb_lesson.php` | Interactive Gold-Standard Lesson | ✅ 200 OK |
| `http://localhost:8080/board-questions/` | `page-board-questions.php` | Authentic Board Questions Bank | ✅ 200 OK |
| `http://localhost:8080/board-analytics/` | `page-board-analytics.php` | Board Pattern Analytics Hub | ✅ 200 OK |
| `http://localhost:8080/mistakes/` | `page-mistakes.php` | Smart Mistake Notebook | ✅ 200 OK |
| `http://localhost:8080/revision/` | `page-revision.php` | Spaced Revision Queue | ✅ 200 OK |
| `http://localhost:8080/progress/` | `page-progress.php` | Mastery & Progress Matrix | ✅ 200 OK |
| `http://localhost:8080/purchases/` | `page-purchases.php` | My Passes & Entitlements | ✅ 200 OK |
| `http://localhost:8080/dashboard/` | `page-dashboard.php` | Student Personalized Dashboard | ✅ 200 OK |
| `http://localhost:8080/onboarding/` | `page-onboarding.php` | Student Onboarding Wizard | ✅ 200 OK |

---

## 6. NEXT STEPS FOR LAPTOP / CLAUDE WEB

When you connect GitHub from your laptop / Claude Web:
1. Run `git pull origin main` to fetch all latest phases (0 through 12) and organized plans.
2. Refer to [`docs/plans/04-master-checklist.md`](./04-master-checklist.md) for the active build state.
3. The next phase to build is **Phase 13 (English MVP Content Library)**.
