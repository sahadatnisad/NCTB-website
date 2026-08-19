# MASTER ROADMAP & BUILD CHECKLIST — NCTB AI LEARNING HUB

> **Repository:** `https://github.com/sahadatnisad/NCTB-website`  
> **Branch:** `main`  
> **Architecture Principle:** "One Portal, One Database, Two Roles" (Students + Teachers / Shikkhok Hub)  
> **Design System:** `docs/plans/02_DESIGN_SYSTEM.md` (Hind Siliguri + Inter, Emerald Green `#1E6F5C`, Bengali UTF-8)

---

## 🚀 Quick Setup: How to Run & Test on Your Local PC

When you clone or pull the repository onto your local computer, follow these exact steps:

### 1. Requirements
- **Git** (installed via Git for Windows or system package manager)
- **Docker Desktop** (or local PHP 8.1+ with MySQL 8.0 & Apache/Nginx)

### 2. Start the Local Server with Docker
In the project root directory (`I:\Website` or your clone directory), run:
```bash
# Pull the latest code
git pull origin main

# Start WordPress and MySQL containers
docker compose up -d
```
- **Website URL:** `http://localhost:8080`
- **WordPress Admin:** `http://localhost:8080/wp-admin` (User: `admin`, Password: `password`)
- **Database:** `localhost:3306` (DB: `wordpress`, User: `wordpress`, Password: `wordpress`)

### 3. Verify Database Migrations
Log in to WordPress admin once (`http://localhost:8080/wp-admin`). The `NCTB_Migrations` runner will automatically execute and apply all schema migrations up to `0.18.0` idempotently!

---

## 📊 Master Phase-by-Phase Progress & Checklist

| Phase | Description & Goal | Audience / Category | Status | Report Link |
|---|---|---|---|---|
| **0** | Development environment, migration engine (`NCTB_Migrations`), activation lifecycles | Foundation | ✅ Completed | [`BUILD_REPORT_PHASE_0.md`](../build-history/BUILD_REPORT_PHASE_0.md) |
| **1** | Visual shell, responsive child theme (`nctb-child-theme`), navigation header/footer | Presentation | ✅ Completed | [`BUILD_REPORT_PHASE_1.md`](../build-history/BUILD_REPORT_PHASE_1.md) |
| **2** | Student role (`nctb_student`), onboarding wizard (`/onboarding`), student profile service | Students | ✅ Completed | [`BUILD_REPORT_PHASE_2.md`](../build-history/BUILD_REPORT_PHASE_2.md) |
| **3** | Curriculum CMS (`nctb_book`, `nctb_unit`, `nctb_lesson`), concept map (`0.3.0`) | Curriculum | ✅ Completed | [`BUILD_REPORT_PHASE_3.md`](../build-history/BUILD_REPORT_PHASE_3.md) |
| **4** | 14 Interactive Lesson activity blocks (`wp_nctb_lesson_activities` `0.4.0`), stepper UI | Learning Engine | ✅ Completed | [`BUILD_REPORT_PHASE_4.md`](../build-history/BUILD_REPORT_PHASE_4.md) |
| **5** | Practice & question engine (`mcq`, `fill_blank`, `short_answer`), progressive hint service (`0.5.0`) | Assessment | ✅ Completed | [`BUILD_REPORT_PHASE_5.md`](../build-history/BUILD_REPORT_PHASE_5.md) |
| **6** | Progress, mastery calculation, Smart Mistake Notebook, SM-2 Spaced Repetition (`0.6.0`) | Retention | ✅ Completed | [`BUILD_REPORT_PHASE_6.md`](../build-history/BUILD_REPORT_PHASE_6.md) |
| **7** | Student Study Guide Dashboard (`/dashboard`), continue learning cards, due revision widgets | Students | ✅ Completed | [`BUILD_REPORT_PHASE_7.md`](../build-history/BUILD_REPORT_PHASE_7.md) |
| **8** | WooCommerce integration, centralized entitlement service (`NCTB_Entitlements` `0.8.0`), paywalls | Monetization | ✅ Completed | [`BUILD_REPORT_PHASE_8.md`](../build-history/BUILD_REPORT_PHASE_8.md) |
| **9** | Contextual AI Tutor Drawer, curriculum grounding, Socratic guardrails, quota tracker (`0.9.0`) | AI Engine | ✅ Completed | [`BUILD_REPORT_PHASE_9.md`](../build-history/BUILD_REPORT_PHASE_9.md) |
| **10** | 6-stage Writing rubric evaluator, Audio listening player, Speaking recorder (`0.10.0`) | Skills Engine | ✅ Completed | [`BUILD_REPORT_PHASE_10.md`](../build-history/BUILD_REPORT_PHASE_10.md) |
| **11** | Authentic Board Question Bank (10 Education Boards, verified marking schemes `0.11.0`) | Exam Prep | ✅ Completed | [`BUILD_REPORT_PHASE_11.md`](../build-history/BUILD_REPORT_PHASE_11.md) |
| **12** | Historical Board Pattern Analytics (Topic frequency, Question distributions, No predictions) | Analytics | ✅ Completed | [`BUILD_REPORT_PHASE_12.md`](../build-history/BUILD_REPORT_PHASE_12.md) |
| **13** | English MVP content library mapping (HSC & SSC unit-lesson structures) | Content | ✅ Completed | [`BUILD_REPORT_PHASE_13.md`](../build-history/BUILD_REPORT_PHASE_13.md) |
| **14** | Security audit, student data isolation, Gemini AI adapter, low-bandwidth 3G YouTube facade | Security & QA | ✅ Completed | [`BUILD_REPORT_PHASE_14.md`](../build-history/BUILD_REPORT_PHASE_14.md) |
| **15** | Production readiness: bKash/Nagad/SSLCommerz commerce hooks, Schema.org SEO, transactional emails, DB backups | Launch Readiness | ✅ Completed | [`BUILD_REPORT_PHASE_15.md`](../build-history/BUILD_REPORT_PHASE_15.md) |
| **16** | Teacher role (`nctb_teacher`), `wp_nctb_teacher_profiles` (`0.16.0`), teacher onboarding & dashboard | Teachers | ✅ Completed | [`BUILD_REPORT_PHASE_16.md`](../build-history/BUILD_REPORT_PHASE_16.md) |
| **17** | Modules & Video Courses (`nctb_module`), progress checklist table (`0.17.0`), course player & archive | Courses / Video | ✅ Completed | [`BUILD_REPORT_PHASE_17.md`](../build-history/BUILD_REPORT_PHASE_17.md) |
| **18** | Revision Notes & Formula Sheets (`nctb_note`), KaTeX LaTeX math support, printable PDF handouts | Notes / Formulas | ✅ Completed | [`BUILD_REPORT_PHASE_18.md`](../build-history/BUILD_REPORT_PHASE_18.md) |
| **19** | AI as a paid product: Teacher AI Lesson Planner & Quiz Maker tools, tiered quota packages | AI Commercialization | ✅ Completed | [`BUILD_REPORT_PHASE_19.md`](../build-history/BUILD_REPORT_PHASE_19.md) |
| **20** | Add ICT: Content-only proof of engine (HTML, C programming, logic gates, practicals) | Subject Expansion | ✅ Completed | [`BUILD_REPORT_PHASE_20.md`](../build-history/BUILD_REPORT_PHASE_20.md) |
| **21** | Maths Engine: KaTeX math input, deterministic formula matching, step-by-step guidance | Subject Expansion | ✅ Completed | [`BUILD_REPORT_PHASE_21.md`](../build-history/BUILD_REPORT_PHASE_21.md) |
| **22** | Science Subjects: Physics, Chemistry, Biology notes, video masterclasses, and question banks | Subject Expansion | ✅ Completed | [`BUILD_REPORT_PHASE_22.md`](../build-history/BUILD_REPORT_PHASE_22.md) |
| **23** | Extend to Class 6–8 (JSC / JDC) with parent-friendly onboarding and curriculum | Class Expansion | ✅ Completed | [`BUILD_REPORT_PHASE_23.md`](../build-history/BUILD_REPORT_PHASE_23.md) |
| **24** | Teacher Content Depth: Downloadable classroom slide templates, pedagogy Q&A | Community | ✅ Completed | [`BUILD_REPORT_PHASE_24.md`](../build-history/BUILD_REPORT_PHASE_24.md) |
| **25+**| Ongoing subject scaling (Bangla, Accounting, Economics), SMS revision alerts | Long-term Scale | 🔜 **NEXT** | `Pending` |

---

## 🎯 Immediate Next Phase: Phase 19 (AI as a Paid Product)

### Objectives for Phase 19:
1. **Teacher AI Pedagogical Tools (`NCTB_AI_Tutor` & `NCTB_Teacher_Views`)**:
   - 📝 **45-Minute Lesson Plan Generator**: Grounded in specific NCTB learning outcomes, dividing class time into Intro (10m), Direct Instruction (15m), Group Activity (10m), and Formative Assessment (10m).
   - ⚡ **Classroom Quiz & Test Generator**: Creates print-ready and digital quizzes with multiple difficulty levels and answer keys.
   - 🔍 **Student Misconception Diagnostic Tool**: Recommends pedagogical interventions for concepts where students fail in board exams.
2. **Strict Server-Side AI Entitlement Gating**:
   - Enforce that AI tools only run for users with active AI Passes (`ai_access`), with a small free trial allocation (3 questions).
   - Daily quota cap enforcement for abuse prevention.
