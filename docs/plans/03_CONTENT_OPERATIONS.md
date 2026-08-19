# NCTB AI Learning Hub — Content Operations Guide

> **Purpose.** The practical playbook for the part that will actually make or break the platform: **producing content and getting it into the website repeatably.** Covers converting NCTB books into structured lessons, making notes, offering AI to teachers/students, organising teacher & student profiles, delivering video courses, and managing free vs. paid — everything content-side.
>
> **Core truth:** once the structure (from `01_BUILD_BLUEPRINT.md`) exists, *this document is the real day-to-day work.* Your bottleneck is content, not code.

---

## 1. Legal reality check first (read before scanning any book)

NCTB textbooks are published by a government body. Whether — and how — you may copy, adapt, or redistribute their content is a **rights question you must confirm before building a business on it.** I cannot give you a definitive legal answer and I won't guess.

- **Verify** the copyright/licensing status of NCTB textbook content for commercial reuse. NCTB provides free PDFs of textbooks publicly; *free to read* is not the same as *free to rebuild into a paid product*.
- **Safer default:** treat NCTB books as the **curriculum spine and reference** — i.e., you align to the same syllabus, topics, and learning outcomes — but author **your own original lessons, notes, questions, and explanations** rather than reproducing textbook text/images verbatim. This is both safer legally and *better pedagogically* (your value is the teaching, not a re-typed book).
- **Board questions:** past board exam questions are widely reproduced, but confirm the status and always mark provenance (you already store official source references — keep doing that).
- **When in doubt, consult a Bangladeshi IP/education lawyer.** This is a one-time cost that protects a lifetime project.

Everything below assumes you are authoring **original content aligned to NCTB**, not copying the books.

---

## 2. The content model (what a "course" actually is in your system)

Your engine already defines the hierarchy. Content producers must think in these units:

```
Class (taxonomy: 6,7,8,9,10,SSC,HSC…)
  └─ Subject (English, ICT, Physics…)
       └─ Book (nctb_book)            e.g. "HSC English 1st Paper"
            └─ Unit (nctb_unit)        e.g. "Unit 1: People or Events…"
                 └─ Lesson (nctb_lesson)  e.g. "Lesson 1: Nelson Mandela"
                      └─ Activities (14 block types) — the interactive body
                      └─ Questions (linked practice)
                      └─ Concepts / Learning outcomes (micro-skills)
Notes (nctb_note)      — standalone free explanations, cross-linked to lessons
Modules (video courses)— YouTube-based free/paid courses (student or teacher)
Board questions        — authentic past questions (separate, verified)
```

**Golden rule for producers:** every lesson maps to **learning outcomes** and **concepts**. That mapping is what powers mastery tracking, the mistake notebook, and the AI tutor's grounding. A lesson without outcome/concept tags is a dead end — the smart features can't work on it.

---

## 3. Converting NCTB books into website content — the pipeline

You asked specifically: *"how to convert NCTB books to website like a python file or md file."* Here is a concrete, repeatable pipeline. It has two layers: a **human authoring format** (Markdown/YAML) and an **importer** (a script that loads it into WordPress).

### 3.1 Why Markdown/YAML, not typing into wp-admin
- Authors write fast in plain text; content lives in files you can version in git, review, and re-import.
- The same file can be validated, spell-checked, and bulk-imported.
- It decouples *writing* from *the CMS*, so a non-technical content team can produce while devs handle import.

### 3.2 The authoring format (one file per lesson)

Use a Markdown file with a **YAML front-matter header** (metadata) + a **structured body** (activities). Example:

```markdown
---
type: lesson
class: HSC
subject: English 1st Paper
book: HSC English 1st Paper
unit: "Unit 1: People or Events that Inspire You"
lesson_title: "Lesson 1: The Unforgettable History"
slug: hsc-eng1-u1-l1
access: paid            # free | paid
learning_outcomes:
  - Identify main idea and supporting details in a nonfiction text
  - Use context clues to infer vocabulary
concepts:
  - reading-main-idea
  - vocabulary-context-clues
board_topics:
  - HSC-Eng1-Unit1
estimated_minutes: 25
---

## activity: reading
title: Read the passage
body: |
  <original or licensed passage text here>

## activity: vocabulary
title: Key words
items:
  - word: emancipation
    meaning_bn: মুক্তি / স্বাধীনতা
    example: The speech called for the emancipation of all people.

## activity: comprehension_quiz
questions:
  - type: mcq
    prompt: What is the main idea of the passage?
    options: [A…, B…, C…, D…]
    answer: B
    concept: reading-main-idea
    explanation: |
      The passage repeatedly returns to…

## activity: writing
title: Short response
prompt: In 3–4 sentences, explain…
rubric: [structure, grammar, vocabulary]
```

- **`## activity: <type>`** maps to your 14 built-in activity block types (reading, vocabulary, grammar, quiz, writing, listening, speaking, etc.).
- The YAML header carries everything the engine needs for mastery/AI grounding.
- Questions embedded here populate the practice/marking tables.

### 3.3 The importer (the "python file" idea — but do it in the plugin)

You imagined a Python file. You *can* prototype parsing in Python, but the cleanest path in WordPress is a **WP-CLI import command inside your plugin** (PHP), because it can call WordPress functions directly to create posts, terms, and table rows safely. Recommended:

- Add a WP-CLI command, e.g. `wp nctb import-lesson path/to/lesson.md`.
- It parses YAML front-matter + activity sections, then:
  1. Finds/creates the Class/Subject/Book/Unit (idempotent — don't duplicate).
  2. Creates/updates the `nctb_lesson` post.
  3. Writes activities to `wp_nctb_lesson_activities`.
  4. Creates questions/options in the question tables.
  5. Links concepts/outcomes.
  6. Sets `access` (free/paid) meta.
- **Idempotent by `slug`:** re-importing updates, never duplicates.
- A folder import: `wp nctb import-dir content/hsc-english/` loops all files.

> Prototype the parser in Python if you like, but the **authoritative importer should live in the plugin as WP-CLI** so it uses WordPress's own APIs and stays consistent with your migrations and validation. Have the agent build this as a small phase-scoped tool when you start bulk content (a good task for Phase 16–17 era).

### 3.4 Validation before import (quality gate)
Build a validator (part of the CLI) that rejects a lesson file if: missing outcomes/concepts, a quiz question has no answer, a concept isn't in the concept registry, or `access` is missing. **No lesson enters the site without passing validation.** This is how you keep quality high at scale.

### 3.5 Content production workflow (team process)
1. **Curriculum map** (once per subject): list every Book→Unit→Lesson from the NCTB syllabus, with outcomes. This is your production backlog.
2. **Author** the `.md` file (subject expert).
3. **Peer review** (second expert checks accuracy + curriculum fit).
4. **Validate** (CLI).
5. **Import to staging**, click through, fix.
6. **Publish** to production.
7. Track status in a simple sheet (Planned → Drafting → Review → Live).

---

## 4. Creating notes & graphical explanations (the free-tier magnet)

Notes are your SEO engine and free-value hook.

- **Format:** author as Markdown → import as `nctb_note` CPT (or write directly in the WP editor for one-offs). Fields: title, Class, Subject, Topic, `access` (mostly free), optional linked lesson.
- **What makes a great note:** one concept, explained simply in Bangla-first language, with a clear diagram or worked example, and a cross-link to the practice lesson. Short, scannable, mobile-first.
- **Graphical explanations / diagrams:** 
  - Produce diagrams in a consistent style (see design system). Tools: draw in Figma/Illustrator, or generate schematic diagrams from code (e.g. Mermaid for flow/process, or SVG) for reproducibility. Export **SVG** (crisp + tiny) where possible, PNG/WebP otherwise. Always add alt text.
  - For maths/science, keep diagrams labeled and printable; reuse a template so all subjects look like one system.
- **SEO:** each note = a page targeting a real student search ("HSC Physics Newton's second law explanation Bangla"). Good titles, meta descriptions, schema.org, internal links to lessons. This is how you get free organic traffic that converts to paid.

---

## 5. Offering AI to students and teachers

AI is a **paid** entitlement (per your decision) and must stay grounded and cost-controlled.

### 5.1 For students
- **AI tutor (exists):** grounded in the current lesson's text, outcomes, vocabulary, and the student's own mistakes. Socratic — scaffolds, never hands over quiz answers. Quick actions: explain, in Bangla, hint, example, why-was-I-wrong.
- **Guardrails:** never fabricate board answers; label writing/speaking feedback as *formative, non-official*; daily quota as abuse cap even for paying users.
- **Cost control:** never call AI for deterministic marking or stored explanations. Cache common explanations. Prefer short, grounded prompts over open-ended.

### 5.2 For teachers (new, high-value)
Ground these in curriculum too, and keep them practical:
- **Lesson-plan generator:** input class + subject + topic + duration → a structured lesson plan (objectives, warm-up, activities, assessment, homework), editable.
- **"Explain for a weak student":** rephrase a concept at an easier level, with an analogy and a check-question.
- **Question / class-test maker:** generate practice items aligned to outcomes (clearly marked AI-generated, *not* authentic board questions).
- **Marking assistant:** help a teacher grade short written answers against a rubric (assistive, teacher stays in control).
- **Presentation helper:** outline slides for a topic + speaker notes (feeds your "easy presentation creation" teaching goal — see §7).

### 5.3 Transparency & trust
- Always show a small "AI-generated, verify before classroom use" note on teacher outputs and "formative, not official" on student feedback. This protects users and you, and it's honest.

---

## 6. Organising profiles

### 6.1 Student profile
- Onboarding captures: class/level, subjects, stream (for HSC), curriculum version, study target/schedule (you built this).
- Profile stores learning state: enrolled books, progress, mastery, mistakes, revision schedule, purchases, AI quota. Private by default.
- Let students edit class/subjects later (people change streams).

### 6.2 Teacher profile
- Onboarding captures: name, school, district, subjects taught, classes taught, short bio, optional avatar.
- **Verification (optional, builds trust):** a `verification_status` field; you can later verify teachers (e.g. via school info) to unlock a "verified teacher" badge and possibly teacher-only content. Start `unverified`, add process later.
- Teacher state: enrolled teacher modules, progress, downloaded resources, AI usage, purchases.
- **Privacy:** teachers and students never see each other's private data. If you later add a community/Q&A, only public posts are shared — never profile internals.

### 6.3 One account, role-aware
- Same login system; role decides the dashboard and content visibility. Keep it simple in v1: a user is a student *or* a teacher (chosen at signup). Support switching later if demand appears.

---

## 7. Video courses (free & paid) for students and teachers

You'll upload to YouTube and surface here. This is cheap, scalable, and mobile-friendly.

### 7.1 Hosting approach
- **Upload to YouTube** as **Unlisted** (for paid, so they're not publicly findable) or **Public** (for free/marketing reach). Embed via your Module system.
- **Important honesty:** *unlisted is not true DRM* — a determined user can share the link. For a low-budget lifetime project this trade-off is usually acceptable (piracy of educational video is hard to fully stop even with expensive systems). If you later need stronger protection, evaluate a paid video platform — but don't spend on that now.
- Use the **YouTube facade** (load the player only on click) to save your users' data.

### 7.2 Structuring a video course (Module)
- A **Module** = a course: title, audience (student/teacher), subject/class, `access` (free/paid), cover image, ordered **items**.
- **Items** = individual videos (+ optional notes/PDF/quiz). Each item can itself be free or paid (e.g. first 2 videos free as a teaser, rest paid).
- Progress ticks per item; "resume where you left off."
- Cross-link: a video course item can point to a related interactive lesson or practice.

### 7.3 Free vs. paid strategy for video
- **Free:** intro/overview videos, some "how to teach" teacher content (builds goodwill + reach), sample lessons.
- **Paid:** full course sequences, exam-focused series, in-depth teacher upskilling.
- Teaser pattern: first item(s) free, rest paywalled — a proven conversion pattern. Keep the paywall honest and benefit-led (design system §5).

### 7.4 Teacher-specific video content (your differentiator)
- "How to teach topic X in the classroom," "Easy presentation creation," "Using AI in your teaching," classroom problem-solving. Pair videos with **downloadable resources** (slide templates, worksheets) attached to the module item.

---

## 8. Managing free vs. paid across everything

One consistent rule, enforced by the entitlement system (not per-feature hacks):

| Content type | Typical free | Typical paid |
|---|---|---|
| Notes / explanations | Mostly free (SEO magnet) | Occasionally premium deep-dives |
| Board question DB | Free to browse | — |
| Sample interactive lessons | A few free per subject | Full lesson library |
| AI tutor & AI teacher tools | — | **Paid** (per your decision; optional tiny free trial) |
| Video courses | Intro/teaser + some teacher content | Full course sequences |
| Progress analytics, mistake notebook, spaced revision | Basic | Full |

- Each content item carries an `access` flag. Selling happens via WooCommerce products mapped to entitlements (single-subject pass, all-access subscription, AI add-on, individual course).
- **Never** paywall something the user already reasonably expected free, and never dark-pattern. Trust is the whole game in education.

---

## 9. Content quality standards (non-negotiable for a "lifetime project")
- **Accurate & curriculum-aligned** — reviewed by a second expert.
- **Bangla-first, clear, encouraging** — written for a tired student on a phone.
- **Every lesson tagged** with outcomes + concepts (or the smart features die).
- **Consistent diagrams & formatting** (design system).
- **Original, not copied** from NCTB books (see §1).
- **Versioned** — content files in git; changes reviewed.

---

## 10. Suggested content repo layout (separate from code)

Keep authored content in a clear structure (in the same repo under `/content/` or a separate content repo):

```
content/
  hsc/
    english-1st-paper/
      unit-01/
        lesson-01.md
        lesson-02.md
      notes/
        main-idea.md
    ict/
      ...
  ssc/
    ...
  modules/            # video course definitions (yaml/md)
    student/
    teacher/
  board-questions/    # structured past-question imports
```

The plugin's WP-CLI importer reads this tree. This gives you a clean, reviewable, ever-growing content library that anyone on the team can contribute to — and that turns "add a subject" into "add a folder and import."

---

### Bottom line
Build the importer once, define the authoring format once, set the quality gate once — then **growth is just adding folders of well-made content.** That is exactly the "build the structure, add modules gradually" model you described, made concrete.
