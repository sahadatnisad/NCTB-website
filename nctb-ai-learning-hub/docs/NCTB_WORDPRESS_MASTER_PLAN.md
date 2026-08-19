# NCTB AI Learning Hub — WordPress Master Build Plan

## Purpose of this file

This is the single build plan for the WordPress version of the NCTB AI Learning Hub. Use it as the permanent project blueprint when working with an AI coding agent.

Build **one phase at a time**. Do not ask an AI agent to build the entire platform in one attempt. After each phase, generate a build report, test the site, and review the report before starting the next phase.

---

# 1. Product in one sentence

Build a **lesson-by-lesson digital companion to the NCTB curriculum** where students can learn the school lesson, practise it, receive contextual AI help, revise mistakes, complete assessments, and prepare for board examinations at home.

The first product is:

**SSC + HSC English — an interactive NCTB lesson book with a personal AI English tutor.**

Later the same system will support ICT, Bangla, Mathematics, Science and other NCTB subjects.

---

# 2. Core product rules

These rules must not change during development.

1. The **NCTB curriculum and official book structure control what is taught**.
2. AI does **not** decide the curriculum.
3. The website is not primarily a PDF library, video library, chatbot, question bank, or coaching site.
4. The main experience is:

   **Book → Unit/Chapter → Lesson → Learn → Practice → Tutor → Test → Mistakes → Revision → Mastery**

5. English lessons may develop six skills together:
   - Grammar
   - Vocabulary
   - Reading
   - Writing
   - Listening
   - Speaking
6. AI is embedded inside the lesson. A giant general chatbot is secondary.
7. Students should receive hints before full answers when appropriate.
8. Lesson **completion** and lesson **mastery** are different values.
9. Previous board questions must be stored as verified source material and linked to concepts/lessons.
10. AI-generated questions must never be labelled as authentic board questions.
11. Database-based lessons and practice should work without calling the AI API for every interaction.
12. AI-generated educational content must follow: **Draft → Review → Approved → Published**.
13. The site must be mobile-first, fast on slower connections, and easy to use on Android phones.
14. The data architecture must support future subjects from day one even though English launches first.
15. Because students may be minors, privacy, moderation, authorization and secure data handling are mandatory.

---

# 3. WordPress architecture

## 3.1 WordPress role

WordPress will provide:

- user accounts;
- editorial/admin interface;
- media library;
- site pages;
- authentication foundation;
- REST API foundation;
- commerce integration;
- content publishing workflow.

## 3.2 Custom development approach

Create one main custom plugin:

`nctb-learning-hub`

The plugin should contain nearly all learning/business logic:

- curriculum hierarchy;
- lesson engine;
- activity blocks;
- question/practice engine;
- attempts;
- progress;
- mastery;
- mistakes;
- spaced revision;
- AI tutor integration;
- board-question system;
- student dashboard data;
- entitlements/access rules;
- analytics.

Use a lightweight child theme or block theme for presentation only. **Do not place core learning logic in the theme.**

## 3.3 Hybrid WordPress data model

Use WordPress-native content where editorial convenience is useful, and custom database tables where high-volume structured data is required.

### WordPress-native editorial content

Suggested custom post types:

- `nctb_book`
- `nctb_unit`
- `nctb_lesson`

Use taxonomies/metadata for:

- education level;
- class;
- subject;
- paper;
- curriculum version;
- academic session;
- tags/topics.

Use WordPress Media Library for:

- images;
- lesson audio;
- diagrams;
- approved downloadable resources.

### Custom plugin tables

Use dedicated, versioned custom tables for high-volume or relational learning data. Prefix them with the WordPress database prefix.

Minimum logical tables:

- `nctb_concepts`
- `nctb_learning_outcomes`
- `nctb_lesson_concepts`
- `nctb_lesson_activities` if activities are not fully stored in Gutenberg blocks
- `nctb_questions`
- `nctb_question_options`
- `nctb_question_concepts`
- `nctb_attempts`
- `nctb_progress`
- `nctb_mastery`
- `nctb_mistakes`
- `nctb_review_schedule`
- `nctb_vocabulary`
- `nctb_vocabulary_mastery`
- `nctb_board_questions`
- `nctb_board_exams`
- `nctb_entitlements`
- `nctb_ai_usage`
- `nctb_ai_conversations` or a privacy-minimized equivalent

Create tables with versioned migrations/upgrade logic. Never modify production tables manually without a migration path.

## 3.4 Commerce

Use WooCommerce for products, checkout, orders and payment integration.

Support four access models:

1. Free lesson/sample access
2. Single lesson purchase
3. Unit/topic/course pack purchase
4. Monthly subscription

Do not check access with a simple `paid=true` flag. Build an **entitlement layer** that decides whether a student can access a lesson, pack, course or AI allowance.

The entitlement layer should be able to understand:

- direct lesson purchase;
- pack ownership;
- full-course ownership;
- active subscription;
- free access;
- admin-granted access;
- expiry where applicable.

Use a currently compatible WooCommerce subscription solution when recurring billing is added. Verify compatibility at implementation time.

## 3.5 AI integration

AI calls must happen **server-side through the custom plugin**, never directly from browser JavaScript.

Build an AI provider adapter so the model/provider can be changed without rewriting the learning system.

The AI receives only relevant context:

- student class/level;
- current subject/book/unit/lesson;
- current learning outcome/concept;
- approved lesson content;
- recent attempts relevant to the concept;
- selected Bangla/English support level;
- verified board-question context when needed.

Do not send the entire database to the model.

---

# 4. Main website areas

## Public pages

- Home
- How It Works
- Subjects
- SSC English landing page
- HSC English landing page
- Pricing
- Free Lesson
- Login
- Registration
- FAQ
- Privacy Policy
- Terms
- Contact/Support

## Logged-in student pages

- Dashboard
- My Subjects
- My Book
- Unit/Chapter page
- Lesson page
- Practice
- My Mistakes
- Revision Due
- Board Questions
- AI Tutor
- Vocabulary
- Progress
- Purchases / Membership
- Profile / Preferences

## WordPress admin areas

- Curriculum
- Books
- Units
- Lessons
- Lesson Activities
- Concepts
- Learning Outcomes
- Questions
- Vocabulary
- Board Questions
- AI Review Queue
- Students
- Progress Analytics
- Orders
- Subscriptions
- AI Usage
- Settings

---

# 5. Universal lesson structure

Do not force every lesson to have exactly the same blocks, but support these reusable activity types:

1. Warm-up / prior knowledge
2. Context / situation
3. NCTB-aligned reading/content
4. Vocabulary
5. Notice the language
6. Grammar/language explanation
7. Examples
8. Guided practice
9. Independent practice
10. Reading practice
11. Listening task
12. Speaking task
13. Writing task
14. NCTB exercise practice
15. Board-question practice
16. Contextual AI help
17. Lesson quiz/test
18. Feedback
19. Mistake review
20. Spaced revision

For grammar, a preferred learning path is:

**Context → Notice → Understand → Guided Practice → Independent Recall → Use → Exam Transfer → Feedback → Retry → Mastery → Spaced Review**

---

# 6. Student assistance model

For a wrong answer use progressive help.

### Assistance Level 1 — Hint

Give a small clue without revealing the answer.

### Assistance Level 2 — Explanation

Explain the relevant rule/concept and let the student try again.

### Assistance Level 3 — Step-by-step teaching

Show the reasoning, correct answer and a similar example.

Track:

- number of attempts;
- hints used;
- time spent where useful;
- concept tested;
- final result.

---

# 7. Mastery and revision model

Keep progress and mastery separate.

Example:

- Lesson progress: 100%
- Lesson mastery: 68%

Initial mastery bands may be:

- 0–49: reteach
- 50–69: guided practice
- 70–84: independent practice
- 85–94: mastered
- 95–100: strong mastery

Treat these as internal learning indicators, not scientific test scores.

Initial spaced-review schedule can be:

- same lesson/day
- 1 day
- 3 days
- 7 days
- 14 days
- 30 days

Wrong answers shorten the next review interval. Repeated successful recall extends it.

---

# 8. Board-question system

Each verified board question should store at minimum:

- exam level;
- board;
- year;
- subject;
- paper;
- question number;
- marks where applicable;
- question type;
- topic;
- concept;
- sub-concept/rule;
- question text;
- options where applicable;
- verified answer;
- explanation;
- source reference;
- verification status.

Students should eventually be able to filter by:

- board;
- year;
- topic;
- unit/lesson;
- concept;
- question type;
- weak areas.

Pattern analytics should be historical analysis only, not exam prediction.

---

# 9. AI Tutor behavior

The AI tutor must:

- identify and respect student level;
- use the current lesson as the primary context;
- give a hint before a full answer when educationally appropriate;
- use simple language;
- support English-only, English + Bangla, or stronger Bangla scaffolding;
- reduce unnecessary Bangla dependence as appropriate;
- diagnose the misconception, not only mark correct/incorrect;
- ask the student to try again;
- avoid completing assessed work automatically;
- use examples appropriate to Bangladeshi school learners;
- clearly distinguish approved curriculum content from generated examples;
- never invent a board question and present it as authentic;
- never claim an AI score is an official board/NCTB assessment;
- be age-appropriate and moderated;
- admit uncertainty when required.

Useful contextual actions:

- Explain this
- Explain in Bangla
- Make it easier
- Give another example
- Give me a hint
- Why was I wrong?
- Test me on this
- Show the rule
- Show a similar question
- Show a verified board question

---

# 10. Payment model

## Free

- registration;
- selected sample lessons;
- limited practice;
- basic progress;
- limited AI demo.

## Single lesson purchase

Unlock one lesson and its included practice/assessment under the stated access terms.

## Pack/course purchase

Unlock a unit, topic pack or full class English course.

## Monthly subscription

Unlock subscribed course content and larger AI allowances.

AI usage should have separate quotas/limits because it has variable operating cost.

Do not call AI for routine MCQ marking, basic explanations already stored in the database, or ordinary lesson navigation.

---

# 11. Build plan — first to last

This is the only sequence to follow unless a later project review explicitly changes it.

## PHASE 0 — Set up a safe WordPress development environment

### Goal

Create a clean, recoverable development base.

### Build

- staging/local WordPress installation;
- Git repository;
- database backup method;
- environment separation for development/staging/production;
- child theme or block theme;
- empty `nctb-learning-hub` custom plugin;
- coding standards and project documentation folder;
- WordPress debug logging in development only;
- secret/API-key handling plan.

### Done when

- WordPress loads normally;
- plugin activates/deactivates cleanly;
- no PHP/JS console errors;
- Git tracks project code;
- database/files can be backed up and restored.

### STOP

Produce a build report and review it before Phase 1.

---

## PHASE 1 — Create the visual shell and navigation

### Goal

Make the website feel like a student learning hub before complex logic is added.

### Build

Public shell:

- Home
- Subjects
- Pricing placeholder
- Login/Register

Student shell:

- Dashboard placeholder
- My Subjects
- Learn/My Book
- Practice
- Tutor
- Profile

Requirements:

- mobile-first;
- English + Bangla typography support;
- clean academic design, not childish;
- responsive navigation;
- fast page loads;
- accessible buttons/forms.

### Done when

- layouts work on common mobile and desktop widths;
- logged-out and logged-in navigation differ correctly;
- no learning logic is hard-coded in templates.

### STOP

Produce a build report and review it before Phase 2.

---

## PHASE 2 — Student accounts and onboarding

### Goal

Know who the student is and what they study.

### Build

Student profile fields:

- SSC/HSC or class level;
- class/session;
- preferred explanation language;
- chosen subjects;
- optional target exam/session;
- onboarding complete flag.

Onboarding flow:

1. account creation;
2. select level/class;
3. select English;
4. choose explanation preference;
5. redirect to dashboard.

### Done when

- student data saves securely;
- one student cannot read/edit another student's private profile;
- onboarding can be resumed if interrupted.

### STOP

Produce a build report and review it before Phase 3.

---

## PHASE 3 — Curriculum + Book + Unit + Lesson CMS

### Goal

Create the academic backbone.

### Build

WordPress admin can create/manage:

- education level/class;
- subject;
- book;
- unit/chapter;
- lesson;
- lesson order;
- learning outcomes;
- concepts;
- curriculum version/source reference.

Student-facing hierarchy:

**Class → Subject → Book → Unit → Lesson**

### Important

Do not enter the full English course yet. Enter only enough data for one prototype lesson.

### Done when

- admin can create/edit/reorder curriculum entities without coding;
- students can browse one sample book/unit/lesson tree;
- curriculum data is not hard-coded in the theme.

### STOP

Produce a build report and review it before Phase 4.

---

## PHASE 4 — Build ONE gold-standard interactive lesson

### Goal

Prove the complete lesson experience before scaling content.

### Build one NCTB-aligned prototype lesson with:

- learning objective;
- warm-up;
- main content/reading;
- vocabulary;
- grammar/language focus where relevant;
- examples;
- guided practice;
- independent practice;
- one writing task;
- one listening activity if content is available;
- one speaking activity if practical;
- lesson summary;
- basic lesson quiz placeholder;
- contextual Tutor button placeholder.

### Lesson editor

The admin should be able to arrange reusable activity blocks. Start with the smallest set needed for the prototype, then add more block types later.

### Done when

- lesson can be authored from WordPress admin;
- lesson renders correctly on mobile/desktop;
- progress position can be identified;
- no lesson-specific PHP template is required.

### STOP

Test with real users if possible. Produce a build report and review it before Phase 5.

---

## PHASE 5 — Practice and question engine

### Goal

Turn lessons into active practice.

### Build first question types

- MCQ;
- single fill-in-the-blank;
- short text answer;
- error correction where practical.

Question records must support:

- lesson;
- concept;
- difficulty;
- correct answer;
- explanation;
- hint levels;
- source type;
- verification status.

Student interaction:

- answer;
- mark;
- hint;
- retry;
- explanation;
- next question.

### Done when

- question logic works without AI;
- student attempts are stored;
- progressive hints work;
- authenticated authorization is enforced;
- admin can create/edit questions.

### STOP

Produce a build report and review it before Phase 6.

---

## PHASE 6 — Progress, mastery, mistakes and spaced revision

### Goal

Make the platform remember learning.

### Build

- lesson progress;
- concept mastery;
- mistake notebook;
- review schedule;
- revision queue;
- simple mastery recalculation after attempts;
- separate `completed` and `mastered` states.

Student pages:

- My Mistakes;
- Revision Due;
- basic Progress page.

### Done when

- wrong answers appear in My Mistakes;
- mastered mistakes can leave the active mistake list;
- revision items become due automatically;
- lesson completion does not automatically mean mastery.

### STOP

Produce a build report and review it before Phase 7.

---

## PHASE 7 — Functional student dashboard

### Goal

Make the site behave like a home guide.

### Dashboard should show

- Continue Learning;
- Today's Practice;
- Revision Due;
- Needs Attention;
- recent progress;
- My Book progress.

Use rules first, not sophisticated AI recommendations.

### Done when

A returning student can open the dashboard and immediately know what to study next.

### STOP

Produce a build report and review it before Phase 8.

---

## PHASE 8 — Payments and entitlements

### Goal

Allow free, per-lesson and subscription access safely.

### Build

- WooCommerce integration;
- lesson product mapping;
- free lesson entitlement;
- direct lesson entitlement;
- pack/course entitlement structure;
- subscription-compatible entitlement structure;
- access-denied/paywall UX;
- My Purchases/Membership page;
- admin grant/revoke access capability with auditability.

### Done when

- a paid lesson unlocks only for entitled students;
- free lessons work without purchase;
- cancelled/expired access behaves according to product rules;
- access checks are centralized in the entitlement service.

### STOP

Run payment tests in sandbox/test mode. Produce a build report and review it before Phase 9.

---

## PHASE 9 — Contextual AI Tutor

### Goal

Add AI only after the lesson/practice system already works.

### Build

- server-side AI provider adapter;
- secure API key handling;
- AI usage limits;
- lesson-aware Tutor drawer/modal;
- actions such as Explain This / Bangla / Hint / Another Example / Why Was I Wrong?;
- context builder using approved lesson content;
- basic moderation/safety checks;
- AI usage logging without collecting unnecessary private data.

### Important

AI must supplement approved content, not replace it.

### Done when

- AI never needs API credentials in browser code;
- Tutor knows current lesson/concept;
- AI can explain a student's mistake using relevant attempt context;
- AI quota/usage can be tracked;
- board questions cannot be fabricated as authentic.

### STOP

Produce a build report with sample tutor interactions and review it before Phase 10.

---

## PHASE 10 — Writing, listening and speaking enhancements

### Goal

Move from grammar practice to complete English development.

### Build incrementally

Writing:

- task → brainstorm → draft → feedback → revision → final;
- save drafts and feedback separately.

Listening:

- WordPress-hosted/external approved audio;
- transcript stored privately/admin side when appropriate;
- listening question activities.

Speaking:

- start with simple browser recording/upload flow only if technically practical;
- add AI speaking feedback later;
- avoid misleading official-looking scores.

### Done when

At least one real lesson can include reading, vocabulary, grammar, writing, listening and optional speaking without breaking the lesson flow.

### STOP

Produce a build report and review it before Phase 11.

---

## PHASE 11 — Board-question database

### Goal

Connect learning to authentic exam practice.

### Build

- board exam records;
- verified board questions;
- year/board/topic/concept metadata;
- source reference;
- verified answer/explanation;
- filter/search page;
- attach relevant board questions to lessons.

### Done when

- student can open a lesson and practise verified related board questions;
- filters by board/year/topic work;
- AI-generated items are clearly separated from authentic board questions.

### STOP

Produce a build report and review it before Phase 12.

---

## PHASE 12 — Board pattern analytics

### Goal

Turn the board-question database into historical exam intelligence.

### Build

- frequency by topic/concept;
- frequency by year;
- frequency by board;
- common question types;
- practice high-frequency historical patterns.

### Important

Always describe this as historical analysis, not prediction.

### STOP

Produce a build report and review it before Phase 13.

---

## PHASE 13 — Build the English MVP content library

### Goal

Scale the proven system, not redesign it.

### Build

- 20–30 high-quality SSC English lessons;
- 20–30 high-quality HSC English lessons;
- question banks;
- vocabulary;
- verified board-question links;
- selected audio/writing/speaking activities;
- human review workflow.

### Content workflow

**Official NCTB material → Curriculum mapping → Learning outcomes → Lesson decomposition → Micro-concepts → Draft → Activities → Questions → Board questions → AI assistance → Human review → Publish**

### STOP

Do not build hundreds of lessons before testing the MVP with students.

Produce a build report and review it before Phase 14.

---

## PHASE 14 — Private beta, security, performance and quality review

### Goal

Make the site safe and reliable before public launch.

### Test

- mobile usability;
- low-bandwidth behavior;
- broken links;
- lesson progress persistence;
- payment/entitlement edge cases;
- authorization;
- nonce/capability checks;
- REST permission callbacks;
- SQL injection resistance/prepared queries;
- XSS escaping/sanitization;
- private student data protection;
- AI prompt abuse/prompt injection resistance;
- AI usage limits;
- backups/restore;
- performance/caching;
- accessibility basics;
- error logging;
- analytics/quality flags.

### Beta

Invite a small group of SSC/HSC students and collect:

- where they get confused;
- which screens they abandon;
- lesson completion;
- practice accuracy;
- tutor usage;
- payment friction;
- mobile problems.

Fix major issues before launch.

### STOP

Produce a beta report and review it before Phase 15.

---

## PHASE 15 — Public English launch

### Goal

Launch the first real product.

### Launch scope

- SSC English;
- HSC English;
- lesson-by-lesson learning;
- practice;
- progress/mastery;
- mistakes/revision;
- contextual AI tutor;
- per-lesson purchasing;
- monthly plan if ready;
- verified board questions;
- essential analytics/admin controls.

Monitor quality, AI cost, payment behavior and student outcomes closely.

---

## PHASE 16 — Complete English

After launch, expand all required SSC/HSC English lessons and skills using the same engine.

Do not redesign the core unless usage data proves that it is necessary.

---

## PHASE 17 — Add ICT

Reuse:

- users;
- payments;
- curriculum;
- lessons;
- activities;
- questions;
- practice;
- mastery;
- revision;
- AI;
- analytics.

Create only ICT-specific activity/question types that are genuinely needed.

---

## PHASE 18 — Add Bangla and other NCTB subjects

Repeat the curriculum-first content workflow.

The platform becomes the broader NCTB learning hub only after the English learning engine has been proven.

---

# 12. Required build report after EVERY phase

The coding AI must stop after the current phase and output this report.

```text
NCTB LEARNING HUB — BUILD REPORT

Phase completed:
Date:
Environment:
WordPress version:
PHP version:
Theme:
Plugin version:

1. What was built
- ...

2. Files created/changed
- ...

3. Database/schema changes
- ...

4. Admin features added
- ...

5. Student-facing features added
- ...

6. REST/AJAX endpoints added
- ...

7. Security controls added
- ...

8. Tests performed
- ...

9. Test results
- ...

10. Screens/pages to manually review
- ...

11. Known problems / technical debt
- ...

12. Setup or migration steps I must perform
- ...

13. Rollback notes
- ...

14. What is intentionally NOT built yet
- ...

STOP HERE. DO NOT START THE NEXT PHASE.
```

Bring this report back for review before giving the AI coding agent the next phase.

---

# 13. Project coding rules

The AI coding agent must follow these rules at every phase.

## WordPress rules

- Follow WordPress coding conventions.
- Use hooks/actions/filters instead of editing WordPress core.
- Never modify WordPress core files.
- Keep business logic in the custom plugin.
- Keep visual/theme logic separate from business logic.
- Use capabilities and roles for authorization.
- Use nonces where appropriate.
- Every REST endpoint must have a correct permission callback.
- Sanitize input.
- Escape output.
- Use `$wpdb->prepare()` or safe WordPress APIs for database queries.
- Version custom database schemas and migrations.
- Never put API keys/secrets in browser JavaScript or committed source code.
- Make all UI strings translation-ready where practical.
- Support Bangla text correctly with UTF-8.

## Architecture rules

- Do not hard-code curriculum content into PHP templates.
- Do not create duplicate entities/tables when an existing abstraction already fits.
- Do not add a third-party dependency without explaining why it is necessary.
- Do not add a plugin merely to avoid a small amount of maintainable custom code.
- Prefer stable WordPress APIs.
- Keep modules loosely coupled.
- Centralize entitlement/access logic.
- Centralize AI calls behind one provider/service layer.
- Centralize mastery calculations behind one service.
- Centralize question marking behind one service.

## Educational rules

- NCTB/curriculum alignment is editorially controlled.
- AI-generated content is never auto-published.
- Board-question authenticity must be verified.
- AI feedback is supportive, not an official exam grade.
- Hints should precede full answers when appropriate.
- Progress is not mastery.
- Students should be able to retry mistakes.

## Performance rules

- Mobile-first.
- Avoid loading AI/chat libraries on pages that do not use them.
- Lazy-load media.
- Compress images/audio responsibly.
- Paginate large question/history datasets.
- Cache expensive read operations where safe.
- Do not store huge unnecessary blobs in options/autoloaded data.

## Privacy/safety rules

- Collect only needed student data.
- Keep student writing/speaking data private by default.
- Do not expose one student's progress to another.
- Do not log raw secrets.
- Minimize sensitive AI conversation logging.
- Provide deletion/export pathways where required.
- Moderate student-facing AI responses.

---

# 14. MASTER PROMPT FOR AN AI CODING AGENT

Copy the prompt below into your coding AI. Replace `[CURRENT PHASE]` with the phase you want it to build. Attach this Markdown file or place it in the project repository and tell the agent to read it first.

---

## MASTER BUILD PROMPT

You are the senior WordPress architect and developer for an education product named **NCTB AI Learning Hub**.

Your task is to build **ONLY [CURRENT PHASE]** from the project plan in `NCTB_WORDPRESS_MASTER_PLAN.md`.

### Product mission

The product is a lesson-by-lesson digital companion to the Bangladesh NCTB curriculum. Students open the same book/unit/lesson they study in school, learn it more clearly, practise it, ask for contextual help, complete assessments, review mistakes and prepare for board examinations.

The first release is SSC + HSC English. The architecture must support ICT, Bangla, Mathematics, Science and other NCTB subjects later without rebuilding the platform.

### Product loop

Curriculum → Book → Unit/Chapter → Lesson → Learn → Practice → Tutor → Test → Mistakes → Revision → Mastery → Board Practice.

### English learning dimensions

The lesson engine must eventually support:

- grammar;
- vocabulary;
- reading;
- writing;
- listening;
- speaking;
- exam preparation.

Do not treat these as completely separate products. A single lesson may integrate several of them.

### WordPress architecture

Use WordPress as the platform and CMS.

Create/maintain one core custom plugin named:

`nctb-learning-hub`

Keep learning/business logic in that plugin, not in the theme.

Use a lightweight theme/child theme/block theme for presentation.

Use WordPress-native editorial entities where appropriate and custom versioned database tables for high-volume learning data such as attempts, mastery, mistakes, revision, questions, board questions, entitlements and AI usage.

Use WooCommerce as the commerce foundation. Build a centralized entitlement service so access can come from free access, a lesson purchase, pack/course purchase, subscription or admin grant.

### AI architecture

AI must be called server-side only through the custom plugin. Never expose provider API keys in JavaScript.

All AI calls must go through one provider/service abstraction.

AI must receive only the relevant approved lesson context and relevant learner context. Do not send the entire database.

AI supplements approved curriculum/teaching content. It does not control the curriculum.

Never auto-publish AI-generated educational content.

Never label an AI-generated question as an authentic board question.

### Teaching behavior

When appropriate use progressive assistance:

1. small hint;
2. explanation;
3. step-by-step answer + similar example.

Keep lesson completion separate from mastery.

Store attempts so the system can identify concept-level weaknesses.

### Security requirements

All code must follow secure WordPress practices.

- Do not modify WordPress core.
- Use capabilities/roles correctly.
- Use nonces where required.
- Add permission callbacks to REST endpoints.
- Sanitize all untrusted input.
- Escape output.
- Use prepared queries or safe WordPress APIs.
- Do not expose private student records.
- Do not commit API keys, passwords or payment secrets.
- Use versioned database migrations for custom tables.

### Performance/mobile requirements

The majority of students may use Android phones and mobile data.

- mobile-first responsive UI;
- low unnecessary JavaScript;
- optimized media;
- lazy loading;
- no unnecessary AI calls;
- paginate large datasets;
- avoid large autoloaded option payloads;
- maintain fast student-facing screens.

### Coding process

Before changing code:

1. Read `NCTB_WORDPRESS_MASTER_PLAN.md` completely.
2. Inspect the existing repository and current phase status.
3. Do not overwrite working architecture without a documented reason.
4. Identify dependencies from previous completed phases.
5. State briefly what files/tables/endpoints you intend to add or modify.

Then implement ONLY `[CURRENT PHASE]`.

For each feature in this phase, define internally:

- feature goal;
- user story;
- data requirements;
- endpoint/action requirements;
- UI behavior;
- validation rules;
- permission rules;
- error states;
- mobile behavior;
- tests;
- definition of done.

### Do not overbuild

Do not implement features belonging to later phases just because they are mentioned in the master plan.

You may create clean extension points/interfaces for later phases, but do not build later functionality now.

If a future dependency is needed, create the smallest safe placeholder/interface and document it.

### Testing

Run all tests available to you for this phase.

At minimum inspect:

- PHP errors/warnings;
- JavaScript console errors where testable;
- authorization failures/success;
- invalid input;
- valid input;
- mobile/responsive behavior where testable;
- database migration success;
- plugin activation/deactivation safety;
- regression against already-completed phases.

Do not claim a test passed unless you actually performed it.

### Required final response

When the current phase is complete, STOP coding and produce exactly this build report:

NCTB LEARNING HUB — BUILD REPORT

Phase completed:
Date:
Environment:
WordPress version:
PHP version:
Theme:
Plugin version:

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

End the response with:

**STOP HERE. NEXT PHASE NOT STARTED.**

Do not start another phase until I explicitly provide approval after reviewing the report.

---

# 15. Prompt for Phase 0 — use this first

After placing this file in the project/repository, give your coding AI this shorter first instruction:

```text
Read NCTB_WORDPRESS_MASTER_PLAN.md completely.

Build ONLY PHASE 0 — Set up a safe WordPress development environment.

Do not build curriculum, lessons, practice, payments or AI yet.

Create the project/plugin skeleton and documentation foundation exactly as required by the plan. Preserve WordPress core. Use secure WordPress conventions. Make sure the custom plugin activates/deactivates without fatal errors.

When finished, test the phase and output the required NCTB LEARNING HUB — BUILD REPORT.

STOP after the report. Do not begin Phase 1.
```

---

# 16. What you should send back for review after each build

When you return for project review, provide:

1. the AI coding agent's Build Report;
2. screenshots of the important new screens;
3. any error messages;
4. the plugin ZIP or changed project files if available;
5. any database migration notes;
6. what you manually tested;
7. what did not work as expected.

Then the next phase can be reviewed and adjusted before implementation.

---

# 17. Final implementation philosophy

Build the smallest working system first.

Do not start with hundreds of lessons.

Do not start with complex AI personalization.

Do not start with deep board analytics.

Start with:

**one WordPress plugin → one curriculum tree → one excellent lesson → one practice engine → one student history → one payment flow → one contextual AI tutor.**

Once that is reliable, scale content and subjects.

