# NCTB LEARNING HUB — FRONTEND DESIGN & PRESENTATION BUILD REPORT

**Project:** NCTB AI Learning Hub — Frontend Design & Public Marketing System
**Specification Reference:** [`docs/plans/frontend-design-plan-1.md`](../plans/frontend-design-plan-1.md)
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash) & Claude
**WordPress Version:** 7.0.4 | **PHP:** 8.3.33 | **Theme Version:** 0.12.0
**Repository:** https://github.com/sahadatnisad/NCTB-website | **Branch:** `main`

---

## 1. EXECUTIVE SUMMARY

The frontend of the **NCTB AI Learning Hub** has been engineered from the ground up as an academic, conversion-optimized, mobile-first presentation layer. It provides two distinct, context-aware user experiences:
1. **Public Marketing Website:** A high-converting suite of 9 marketing pages that visually demonstrate the platform's unique learning method before requiring student signup.
2. **Student Learning Application:** An interactive, distraction-free study environment featuring an activity stepper, deterministic practice quizzes, progressive 3-level hints, Socratic AI tutoring, writing rubrics, mistake notebooks, and authentic board-question archives.

---

## 2. DESIGN SYSTEM & VISUAL BRAND IDENTITY

### 🎨 Color Palette
- **Primary Brand Green:** `#0b6e4f` (Anchor, primary CTAs, active steppers, progress bars)
- **Deep Brand Green:** `#075b42` (Hover states, header accents)
- **Soft Brand Mint:** `#dff3eb` / `#f3faf7` (Pills, badges, light card accents)
- **AI Tutor Blue:** `#1e40af` / `#3b82f6` (Socratic AI chat bubbles, tutor chips)
- **Revision / Attention Amber:** `#c87a16` / `#fffbeb` (Spaced revision alerts, hint ladders)
- **Error / Mistake Rose:** `#f43f5e` / `#fff1f2` (Mistake notebook cards, error diagnosis)
- **Neutral Inks & Surfaces:** `#0f172a` (Body headers), `#334155` (Body text), `#ffffff` (Card surface), `#f8fafc` (Background soft)

### ✍️ Bilingual Typography
Standardized sans-serif typography stack optimized for high legibility across English and Bengali:
```css
font-family: 'Noto Sans Bengali', 'Hind Siliguri', Inter, system-ui, -apple-system, sans-serif;
```
- **Scale:** H1 Desktop (48–56px) / H1 Mobile (32–38px) | H2 (28–36px) | H3 (20–24px) | Body (16–18px) | Small (13–14px).

### 🌓 Theme Controls & Language Switcher
- **English ⇄ বাংলা Switcher (`theme-ui.js`):** Client-side instant language swap utilizing `data-en` and `data-bn` attributes, persisting in `localStorage`.
- **Light / Dark Mode Engine:** Full CSS custom property overrides supporting system preferences and manual toggles (`☀️ / 🌙`).

---

## 3. PUBLIC MARKETING SITE ARCHITECTURE (9 PAGES)

All marketing pages are provisioned in WordPress and assigned dedicated child-theme templates:

| Page | URL Slug | Template File | Key Sections & Capabilities |
|---|---|---|---|
| **Homepage** | `/` | `front-page.php` | • Interactive Hero with right-side live lesson mockup<br>• Academic Trust Strip (4 pillars)<br>• "Not another video course" Comparative Matrix<br>• 7-Step Learning Loop Grid<br>• Interactive 6-Tab Product Showcase<br>• 6 English Skills Grid<br>• Curriculum Hubs Preview<br>• Transparent Pricing Matrix<br>• FAQ Accordion & High-converting CTA |
| **How It Works** | `/how-it-works/` | `page-how-it-works.php` | Detailed walkthrough of the 7-step learning loop (Learn → Practise → Help → Test → Mistakes → Revise → Master). |
| **Subjects Directory** | `/subjects/` | `page-subjects.php` | Complete subject catalog covering SSC & HSC English (live) and upcoming ICT, Bangla, and Science. |
| **SSC English Hub** | `/ssc-english/` | `page-ssc-english.php` | SSC English 1st & 2nd Paper syllabus breakdown, board readiness indicators, and direct sample lesson links. |
| **HSC English Hub** | `/hsc-english/` | `page-hsc-english.php` | HSC English 1st & 2nd Paper syllabus breakdown, passage comprehension, theme writing, and board patterns. |
| **Pricing & Passes** | `/pricing/` | `page-pricing.php` | 3-Tier transparent pricing matrix (Free Starter ৳0, Single Lesson Pass ৳19, All-Access Monthly Pass ৳299). |
| **FAQ Hub** | `/faq/` | `page-faq.php` | Interactive accordion answering questions on NCTB alignment, mobile data, AI guardrails, and board exams. |
| **Support & Contact** | `/contact/` | `page-contact.php` | Support channels, helpline contact details, office location, and student feedback form. |
| **Privacy & Terms** | `/privacy-policy/` | `page-privacy-policy.php` | Strict student privacy guarantees, zero data selling policy, and transparent terms of service. |

---

## 4. STUDENT APP SCREENS & SHORTCODES (8 CORE VIEWS)

| Screen | URL Slug | Shortcode / View | Key Features |
|---|---|---|---|
| **Student Onboarding** | `/onboarding/` | `[nctb_onboarding]` | 4-step mobile wizard (Class/Level → Subject/Stream → Version → Target Schedule) with localStorage persistence. |
| **Student Dashboard** | `/dashboard/` | `[nctb_student_dashboard]` | Personalized study guide: Continue Learning hero card, Spaced Revision due card, Mistakes alert card, 4 learning KPIs, and Textbook progress bars. |
| **Textbook Library** | `/book/` | `archive-nctb_book.php` | Grid of official NCTB textbooks with class/subject taxonomy filters and unit syllabus accordions. |
| **Interactive Lesson** | `/?p=15` | `single-nctb_lesson.php` | 14 activity blocks, URL hash step navigation, practice quizzes, progressive hints, AI tutor drawer, and board practice widget. |
| **My Mistake Notebook** | `/mistakes/` | `[nctb_mistakes]` | Active error log with diagnostic explanations, concept tags, and 2/2 graduation streak tracking. |
| **Spaced Revision** | `/revision/` | `[nctb_revision_due]` | SM-2 spaced repetition queue (Overdue, Today, Upcoming) with 1-click revision practice launcher. |
| **Progress & Mastery** | `/progress/` | `[nctb_progress]` | Micro-concept mastery matrix (Mastered, In Progress, Needs Attention) and syllabus completion bars. |
| **My Purchases** | `/purchases/` | `[nctb_my_purchases]` | Active access passes, single lesson passes, subscriptions, and expiration dates. |
| **Board Archive** | `/board-questions/` | `[nctb_board_questions]` | 10 Bangladesh Education Boards filter bar (2018–2024) with verified answer scheme accordions. |
| **Board Analytics** | `/board-analytics/` | `[nctb_board_analytics]` | Historical topic frequency rankings, question type distributions, and board coverage graphs. |

---

## 5. INTERACTIVE COMPONENTS & JAVASCRIPT MODULES

1. **Live Browser Mockup (`front-page.php`):** Renders a simulated browser window of the HSC English *Nelson Mandela* lesson showing 72% progress, active stepper badges, and a floating Socratic AI Tutor chip.
2. **Interactive 6-Tab Product Showcase (`theme-ui.js`):** Client-side tabbed interface allowing instant previewing of the Lesson Stepper, Practice Quizzes, AI Tutor Drawer, Mistake Notebook, Writing Workbench, and Board Analytics.
3. **Contextual AI Tutor Drawer (`single-nctb_lesson.php` / `lesson-interactive.js`):** Slide-out drawer with 5 grounded quick-action chips (`Explain Step`, `বাংলায় ব্যাখ্যা`, `Give Hint`, `Example`, `Why Wrong?`).
4. **Interactive Practice Engine:** Deterministic instant marking for MCQ, fill-in-the-blanks, short answers, and error correction with a 3-level progressive hint ladder.
5. **6-Stage Writing Workbench:** Iterative workflow (Task → Brainstorm → Draft → AI Rubric Feedback → Revision → Final Polish) with word counting and multi-criteria rubrics.

---

## 6. VERIFICATION & QUALITY ASSURANCE

### 🧪 PHP Syntax & Linter
- All 75 PHP files across plugin and theme passed `php -l` with **0 syntax errors**.

### 🌐 HTTP Status Audit (All 17 Routes)
- `http://localhost:8080/` → **200 OK**
- `http://localhost:8080/how-it-works/` → **200 OK**
- `http://localhost:8080/subjects/` → **200 OK**
- `http://localhost:8080/ssc-english/` → **200 OK**
- `http://localhost:8080/hsc-english/` → **200 OK**
- `http://localhost:8080/pricing/` → **200 OK**
- `http://localhost:8080/faq/` → **200 OK**
- `http://localhost:8080/contact/` → **200 OK**
- `http://localhost:8080/privacy-policy/` → **200 OK**
- `http://localhost:8080/book/` → **200 OK**
- `http://localhost:8080/?p=15` → **200 OK**
- `http://localhost:8080/board-questions/` → **200 OK**
- `http://localhost:8080/board-analytics/` → **200 OK**
- `http://localhost:8080/mistakes/` → **200 OK**
- `http://localhost:8080/revision/` → **200 OK**
- `http://localhost:8080/progress/` → **200 OK**
- `http://localhost:8080/purchases/` → **200 OK**
- `http://localhost:8080/dashboard/` → **200 OK**
- `http://localhost:8080/onboarding/` → **200 OK**

---

## 7. GIT SYNCHRONIZATION

- **Repository:** https://github.com/sahadatnisad/NCTB-website
- **Branch:** `main`
- **Latest Commit:** `19176dd`
- **Artifacts:** All theme files (`style.css`, `css/marketing.css`, `css/theme-ui.css`, `css/curriculum.css`, `js/theme-ui.js`, `js/lesson-interactive.js`, and all 16 theme templates) are versioned and pushed.
