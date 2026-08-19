# NCTB AI Learning Hub — Public Marketing Frontend Design Plan

**Purpose:** Replace the current generic public landing experience with a professional, conversion-focused, mobile-first marketing website that accurately demonstrates the learning product already built.

**Platform:** WordPress  
**Theme:** `nctb-child-theme` — presentation only  
**Learning/business logic:** `nctb-learning-hub` plugin  
**Primary launch product:** SSC + HSC English  
**Future expansion:** ICT, Bangla, Mathematics, Science and other NCTB subjects  
**Audience:** Bangladeshi SSC/HSC students first; parents/guardians second  
**Primary conversion:** Start a free lesson / create a student account  
**Secondary conversion:** View SSC/HSC English / pricing  
**Design principle:** Academic, modern, trustworthy, calm, useful — not childish, not a coaching-centre poster, not a generic AI website.

---

# 1. Why the existing marketing plan should be upgraded

The current marketing plan has the correct basic pages:

- Home
- How It Works
- Subjects
- SSC English
- HSC English
- Pricing
- FAQ
- Contact
- Privacy / Terms

It also correctly separates the public marketing theme from the learning/business plugin.

However, the page strategy is still too generic. It describes a standard landing page with a hero, cards, pricing and FAQ, while the actual product is now significantly richer.

The public website should visibly demonstrate that the platform already has:

- NCTB Book → Unit → Lesson structure;
- reusable interactive lesson activities;
- deterministic practice;
- progressive hints;
- mistake tracking;
- concept mastery;
- spaced revision;
- a home-study dashboard;
- lesson-level payment/access control;
- contextual AI tutoring;
- writing coaching;
- listening activities;
- speaking practice.

The marketing website must therefore sell **the learning system**, not merely the phrase “AI-powered education”.

The key positioning should become:

> **Your NCTB lesson, explained clearly, practised step by step, and supported by a personal AI tutor at home.**

Alternative Bangla-supporting line:

> **NCTB বইয়ের প্রতিটি lesson — বুঝুন, practice করুন, ভুল ঠিক করুন, AI tutor-এর সাহায্যে আয়ত্ত করুন।**

Do not market the site as a replacement for school or teachers.

Market it as:

> **The student's digital home guide for the NCTB curriculum.**

---

# 2. Marketing objectives

The public site must accomplish five jobs.

## 2.1 Explain the product within 5 seconds

A visitor should immediately understand:

1. This is for NCTB students.
2. It follows their school book lesson by lesson.
3. It helps them learn and practise.
4. AI helps when they are confused.
5. SSC/HSC English is available first.

## 2.2 Establish trust

The site must feel academically credible.

Use:

- actual product UI;
- actual NCTB-aligned lesson hierarchy;
- actual implemented learning workflow;
- clear language about verified content;
- transparent AI boundaries;
- clear pricing;
- privacy-aware messaging.

Avoid:

- fabricated student counts;
- fabricated success rates;
- fake board-score claims;
- fake testimonials;
- “guaranteed A+” language;
- exaggerated claims such as “best AI tutor in Bangladesh” unless independently substantiated.

## 2.3 Make the value visible before signup

A student should see what learning feels like before creating an account.

The homepage should visually preview:

- lesson steps;
- practice feedback;
- progressive hints;
- AI tutor drawer;
- mistake notebook;
- revision due;
- writing feedback;
- dashboard progress.

## 2.4 Give one obvious next action

Primary CTA on most marketing pages:

**Start Free Lesson**

Secondary CTA:

**Explore SSC English** / **Explore HSC English**

Do not show five competing CTAs in the same viewport.

## 2.5 Prepare for future subjects

The public structure must already support:

- English;
- ICT;
- Bangla;
- Mathematics;
- Science;
- future class-specific subjects.

However, future subjects should be labelled **Coming later** or **Planned** until actually available.

---

# 3. Public information architecture

## Primary public navigation

Desktop:

```text
[Logo]  How It Works  Subjects  SSC English  HSC English  Pricing      Login   [Start Free]
```

If the menu becomes crowded:

```text
[Logo]  How It Works  Subjects  English ▼  Pricing                    Login   [Start Free]
```

English dropdown:

```text
SSC English
HSC English
Free Lesson
```

Mobile:

```text
[Logo]                                      [Menu]

Menu:
How It Works
Subjects
SSC English
HSC English
Pricing
FAQ
Login
[Start Free Lesson]
```

## Logged-in header behavior

Do not show the public conversion navigation as if the user is still a prospect.

Logged-in header:

```text
[Logo]  Learn  Practice  Progress                         [Dashboard]
```

or the existing student navigation.

Public and student navigation should remain clearly different.

---

# 4. Public page hierarchy

## Tier 1 — conversion pages

1. Home
2. SSC English
3. HSC English
4. Free Lesson
5. Pricing

## Tier 2 — explanation / trust

6. How It Works
7. Subjects
8. FAQ
9. About / Our Approach (optional but recommended)

## Tier 3 — support / legal

10. Contact / Support
11. Privacy Policy
12. Terms
13. Refund / Access Policy if required by commerce model

---

# 5. Visual brand direction

## 5.1 Brand personality

The visual identity should communicate:

- intelligent;
- calm;
- academic;
- modern;
- supportive;
- Bangladeshi;
- student-friendly;
- trustworthy.

It should **not** look:

- childish;
- neon;
- gaming-heavy;
- corporate SaaS-only;
- like a coaching-centre banner;
- like a government portal;
- like a generic “AI robot” site.

## 5.2 Recommended palette

Retain the existing green as the brand anchor but expand the system.

```css
:root {
  --brand-700: #075b42;
  --brand-600: #0b6e4f;
  --brand-500: #14805f;
  --brand-100: #dff3eb;
  --brand-050: #f3faf7;

  --ink-950: #12211b;
  --ink-800: #24362f;
  --ink-600: #53645d;
  --ink-400: #87948e;

  --surface: #ffffff;
  --surface-soft: #f7f9f8;
  --surface-warm: #fbfaf5;

  --border: #dfe6e2;

  --accent-blue: #2f6fed;
  --accent-amber: #c87a16;
  --accent-red: #b94a48;

  --success: #18865f;
}
```

Use green for:

- primary CTA;
- brand identifiers;
- progress/mastered states;
- active navigation.

Use blue selectively for:

- AI tutor;
- informational actions;
- interactive product-preview highlights.

Use amber for:

- revision due;
- attention;
- learning-in-progress.

Do not use all accents in every section.

## 5.3 Background rhythm

Alternate:

```text
white
very pale green
white
warm neutral
white
deep green CTA band
```

This gives the page visual rhythm without excessive decoration.

## 5.4 Typography

Use a clean sans-serif stack that supports both English and Bangla well.

Preferred approach:

```css
font-family:
  "Noto Sans Bengali",
  "Hind Siliguri",
  Inter,
  system-ui,
  -apple-system,
  "Segoe UI",
  sans-serif;
```

If external font loading meaningfully hurts speed, use an optimized subset or system-first stack.

Typography should feel academic, not playful.

Recommended scale:

```text
Hero H1 desktop: 56–64px / 1.05
Hero H1 mobile:  38–44px / 1.08

Section H2:       36–44px
Mobile H2:        29–34px

Card H3:          20–24px
Body:             16–18px
Small/meta:       13–15px
```

Keep body line length around 55–72 characters on desktop.

## 5.5 Radius and shadows

Use restrained radii:

```text
Buttons: 10–12px
Cards:   16–20px
Large product preview: 22–28px
```

Use subtle shadows only for:

- floating dashboard previews;
- AI tutor drawer;
- pricing highlight;
- hero product mockup.

Most cards can use borders rather than shadows.

---

# 6. Homepage — complete professional structure

The homepage should not be a long collection of equal cards.

It needs a narrative:

```text
Problem
→ Product promise
→ Show product
→ Explain method
→ Prove capabilities
→ Show who it is for
→ Explain pricing
→ Reduce doubts
→ CTA
```

---

# 7. Homepage Section 1 — Header

## Goal

Immediate clarity and trust.

## Design

Sticky after scroll, not necessarily sticky at page load if mobile space is constrained.

Desktop:

```text
┌───────────────────────────────────────────────────────────────────────┐
│ NCTB Learning Hub   How It Works  Subjects  English  Pricing          │
│                                                Login   [Start Free]    │
└───────────────────────────────────────────────────────────────────────┘
```

Use a slim announcement bar only if there is genuinely useful information.

Example:

```text
SSC & HSC English is now being developed lesson by lesson.  Explore →
```

Do not permanently consume vertical space with promotional clutter.

---

# 8. Homepage Section 2 — Hero

## Objective

Answer:

**What is this? Who is it for? Why should I care?**

## Recommended hero copy

Eyebrow:

**NCTB-aligned • Lesson by lesson • AI-supported**

H1:

> **Your NCTB lesson.  
> Learn it. Practise it. Master it.**

Supporting text:

> Follow the same SSC or HSC English lessons you study in school. Get clear explanations, guided practice, mistake review, revision, writing and speaking support, and contextual AI help when you need it.

Primary CTA:

**Start a Free Lesson**

Secondary CTA:

**See How It Works**

Microcopy below CTA:

```text
No card required for the free lesson.
```

Only use this if true.

## Hero visual

Do **not** use a generic illustration of a robot teaching a child.

Use a real product composition.

Recommended right-side mockup:

```text
Browser / phone frame
┌───────────────────────────────┐
│ English for Today             │
│ Unit 1 · Lesson 1             │
│                               │
│ Nelson Mandela...             │
│ ███████░░ 72%                 │
│                               │
│ ✓ Warm-up                     │
│ ✓ Reading                     │
│ ● Vocabulary                  │
│ ○ Practice                    │
│ ○ Lesson Test                 │
│                               │
│ [Continue Lesson]             │
└───────────────────────────────┘

Small floating card:
┌─────────────────────────┐
│ AI Tutor                │
│ “Why is this wrong?”    │
│ [Explain simply]        │
└─────────────────────────┘
```

This immediately differentiates the product from a video course.

## Mobile

Hero text first, CTA second, product preview third.

Do not force a two-column layout on mobile.

---

# 9. Homepage Section 3 — Credibility strip

Do not use fake logos or fake statistics.

Use factual product principles:

```text
NCTB lesson structure
Step-by-step practice
English + Bangla support
Progress & revision
Contextual AI tutor
```

Design as a quiet horizontal strip.

Example:

```text
✓ NCTB-aligned    ✓ Mobile-first    ✓ Learn + practise    ✓ AI inside lessons
```

---

# 10. Homepage Section 4 — “Not another video course”

This section should establish the product difference.

Headline:

> **A digital home guide, not just another course library.**

Copy:

> Open the lesson you are studying at school. Understand the content, practise the difficult parts, get hints when you are stuck, revisit mistakes, and continue until the concept becomes stable.

Visual comparison:

```text
Typical online course                 NCTB Learning Hub

Watch a video                         Follow your NCTB lesson
Move to next chapter                  Practise before mastery
See right/wrong                       Understand why
Forget old mistakes                   Mistakes return for revision
Generic chatbot                       Tutor knows your current lesson
```

Keep this comparison respectful and factual.

---

# 11. Homepage Section 5 — Product loop

This should be one of the most visually important sections.

Headline:

> **One lesson. One complete learning loop.**

Flow:

```text
1. Learn
   Understand the lesson clearly

        ↓

2. Practice
   Guided → independent questions

        ↓

3. Get Help
   Hints and contextual AI tutor

        ↓

4. Test
   Check lesson understanding

        ↓

5. Fix Mistakes
   Your mistakes are remembered

        ↓

6. Revise
   Difficult concepts return later

        ↓

7. Master
   Progress and mastery are tracked separately
```

Desktop: horizontal or serpentine timeline.

Mobile: vertical stepper.

Use small authentic UI thumbnails inside selected steps.

---

# 12. Homepage Section 6 — Interactive product showcase

Instead of six static feature cards, build a tabbed product showcase.

Tabs:

```text
Lesson
Practice
AI Tutor
Mistakes
Progress
Writing
```

## Lesson tab

Show:

- activity stepper;
- NCTB book/unit context;
- reading/vocabulary/grammar blocks;
- progress.

## Practice tab

Show actual behavior:

```text
Question
Neither Rahim nor his friends ___ present.

[is] [are] [was] [has]

Need help?
[Hint 1]

Incorrect?
Try again before seeing the full answer.
```

## AI Tutor tab

Show contextual quick actions:

```text
Explain this
বাংলায় বুঝিয়ে দিন
Give me a hint
Why was I wrong?
Give another example
```

## Mistakes tab

Show:

```text
My Mistakes
Articles       7
Tense          5
Vocabulary     4

[Practice My Mistakes]
```

## Progress tab

Show:

```text
Completed lessons
Questions attempted
Active mistakes
Revision due
Concept mastery
```

## Writing tab

Show:

```text
Task → Brainstorm → Draft → Feedback → Revision → Final
```

This section should demonstrate depth without forcing visitors to read long paragraphs.

---

# 13. Homepage Section 7 — English skills

Headline:

> **English is more than grammar.**

Use six skill cards, but make them outcome-driven.

### Grammar

Understand the rule, notice it in context, practise it, and correct misconceptions.

### Vocabulary

Learn useful words in context and revisit them through spaced review.

### Reading

Work with lesson passages, main ideas, detail, inference and vocabulary-in-context.

### Writing

Plan, draft, receive formative feedback, revise, and submit a stronger final version.

### Listening

Listen to lesson-related audio, answer questions, and use transcripts when appropriate.

### Speaking

Practise short spoken responses and receive formative practice feedback.

At launch, only publicly claim these skills to the extent the corresponding content is actually available.

---

# 14. Homepage Section 8 — AI positioning

Do not title this section simply:

**Powered by AI**

That is generic.

Use:

> **AI that knows what lesson you are studying.**

Explain:

```text
Generic AI:
“Ask me anything.”

NCTB Learning Hub:
Current class
+ current book
+ current unit
+ current lesson
+ learning outcomes
+ approved lesson content
+ your recent mistakes
= contextual help
```

Key message:

> The AI supports the lesson; it does not decide the curriculum.

Show three quick-action examples:

```text
“Explain this in Bangla.”
“Give me a hint, not the answer.”
“Why was my last answer wrong?”
```

Add trust note:

> AI-generated explanations and practice support are separate from verified NCTB/board content.

---

# 15. Homepage Section 9 — Dashboard / home guide

Headline:

> **Open the dashboard and know what to study next.**

Use a large polished dashboard preview based on the real student dashboard.

Show:

```text
Continue Learning
Unit 3 · Lesson 2
58%

Revision Due
8 items

Needs Attention
Articles
Present Perfect

This Week
4 lessons
83 questions
78% accuracy
```

The marketing value proposition:

> The student does not have to decide from scratch every day. The platform remembers progress, mistakes and revision due.

---

# 16. Homepage Section 10 — SSC / HSC choice

Headline:

> **Start with your class.**

Two large cards:

```text
SSC English
Classes 9–10

NCTB lesson structure
Grammar & vocabulary
Reading and writing
Practice and revision
Contextual AI tutor

[Explore SSC English]
```

```text
HSC English
Classes 11–12

NCTB lesson structure
English skills
Practice and mastery
Writing support
Contextual AI tutor

[Explore HSC English]
```

Future subjects section below:

```text
More NCTB subjects are planned using the same learning system.

ICT • Bangla • Mathematics • Science
```

Use **Coming later** where appropriate.

---

# 17. Homepage Section 11 — Free lesson conversion block

This is more important than testimonials at the current stage.

Headline:

> **Try the learning experience before you pay.**

Subtext:

> Open a sample lesson, see how the explanation, practice, hints, tutor and progress system work, then decide whether it fits you.

CTA:

**Start Free Lesson**

Secondary:

**Create Free Account**

If the free lesson can be previewed without registration, let the visitor preview first and request registration only when progress needs saving.

---

# 18. Homepage Section 12 — Pricing preview

Use simple options.

Do not overcomplicate pricing before the product has sufficient content.

Suggested structure:

```text
Free
Try the platform
• Selected sample lesson
• Limited practice
• Basic progress
• Limited AI demo
[Start Free]

Single Lesson
Pay only for what you need
• Full lesson
• Practice
• Assessment
• Progress/revision
• Stated AI allowance
[Browse Lessons]

Monthly
For regular learners
• Included course content
• Larger practice access
• Larger AI allowance
• Progress and revision
[View Plans]
```

If pack/course purchase is not yet customer-ready, do not show it as active.

Use actual prices only when finalized.

No artificial “90% discount today” urgency.

---

# 19. Homepage Section 13 — Parent confidence

Parents may pay even when students are the main users.

Create a concise trust section.

Headline:

> **Designed to support study at home.**

Points:

- follows the student's curriculum structure;
- encourages practice rather than passive watching;
- keeps lesson completion separate from mastery;
- remembers mistakes and schedules revision;
- AI is moderated and lesson-grounded;
- student writing and speaking submissions are private by default.

Do not build a parent dashboard marketing promise until that feature exists.

---

# 20. Homepage Section 14 — FAQ

Use 6–8 high-impact questions, not 20.

Recommended:

1. Is this an official NCTB website?
2. Does the platform follow NCTB books?
3. Is this only for English?
4. How does the AI tutor work?
5. Will the AI give answers during tests?
6. Can I buy only one lesson?
7. What happens to lessons I purchase?
8. Does it work well on mobile phones?

Important answer to question 1:

> No. This is an independent learning platform designed to align lessons with the NCTB curriculum and books. It should not imply official NCTB ownership or endorsement unless such authorization exists.

---

# 21. Homepage Section 15 — Final CTA

Use a simple dark/deep-green full-width band.

Headline:

> **Start with the lesson you are studying now.**

Subtext:

> Learn clearly. Practise actively. Fix mistakes. Revise later.

Primary:

**Start Free Lesson**

Secondary:

**Explore English**

Avoid multiple extra links.

---

# 22. Footer

Recommended columns:

```text
Learn
- SSC English
- HSC English
- Subjects
- Free Lesson

Platform
- How It Works
- Pricing
- FAQ

Support
- Contact
- Help

Legal
- Privacy
- Terms
- Refund / Access Policy

Account
- Login
- Register
```

Bottom line:

```text
Independent NCTB-aligned learning platform.
Not an official NCTB website unless explicitly authorized.
```

Only include the disclaimer wording if legally/brand-wise appropriate; it is strongly recommended to avoid confusion.

---

# 23. SSC English landing page

## Goal

Convert an SSC student or parent who already knows the class.

## Structure

1. Hero
2. What the student learns
3. NCTB book/unit/lesson structure
4. Sample lesson preview
5. Grammar + vocabulary + reading + writing + listening + speaking
6. Practice and progressive hints
7. Mistake/revision system
8. AI tutor
9. Board-question section when implemented
10. Pricing/access
11. FAQ
12. CTA

Hero:

> **SSC English, lesson by lesson.**

Subtext:

> Study the same NCTB lesson structure, understand difficult language, practise step by step, and receive help when you get stuck.

CTA:

**Try SSC Lesson**

Do not create SEO filler paragraphs merely to make the page long.

---

# 24. HSC English landing page

Similar structure but more mature presentation.

Hero:

> **HSC English with structured practice and a lesson-aware AI tutor.**

Emphasize:

- reading comprehension;
- grammar/language use;
- writing process;
- vocabulary;
- exam-oriented practice where verified;
- revision;
- AI explanation.

Use less playful illustrations than the SSC page.

---

# 25. Subjects page

The current marketing plan says “subject overview cards”; improve this to a scalable subject catalog.

Sections:

```text
Available now
English

In development / Planned
ICT
Bangla
Mathematics
Science
...
```

Each card should show:

```text
Subject
Available classes
Status
What the learning engine supports
CTA
```

Do not make planned subjects appear purchasable.

---

# 26. How It Works page

This page should explain the product more deeply than the homepage.

Main visual:

```text
NCTB Book
  ↓
Unit
  ↓
Lesson
  ↓
Learn
  ↓
Practice
  ↓
Tutor
  ↓
Lesson Test
  ↓
Mistakes
  ↓
Revision
  ↓
Mastery
```

Then explain four underlying systems:

1. Lesson book
2. Practice engine
3. Learner memory
4. Contextual AI tutor

End with a real sample lesson CTA.

---

# 27. Pricing page

Use clarity over feature overload.

## Pricing principles

- show BDT clearly;
- disclose whether lesson access expires;
- disclose AI limits;
- distinguish subscription from permanent/defined lesson access;
- show refund/access policy link;
- avoid dark patterns;
- do not hide important limits below a collapsed accordion.

Recommended comparison rows:

```text
Sample lessons
Full lesson content
Practice
Lesson assessment
Mistake notebook
Spaced revision
AI tutor allowance
Writing feedback
Speaking practice
Course access
```

If a feature is not active, label it rather than silently implying availability.

---

# 28. Free Lesson page

This page should be treated as a product experience, not a marketing article.

Recommended flow:

```text
Short intro
↓
Open actual lesson
↓
Allow several activities
↓
Allow several practice questions
↓
Demonstrate one contextual AI action
↓
Show end-of-preview summary
↓
Create account to save progress / continue
```

This is likely the strongest conversion mechanism for the site.

---

# 29. Login / registration

Public marketing should not use the default WordPress login appearance as the primary experience.

Create branded account pages while preserving WordPress authentication securely.

Registration copy should be minimal:

```text
Create your student account

Continue your lessons
Save your progress
Review your mistakes
Use your tutor

[Create Account]

Already have an account? Log in
```

Do not ask for unnecessary information before onboarding.

---

# 30. Contact and support

Use:

- support email;
- contact form with anti-spam protection;
- FAQ search/link;
- clear response expectations only if operationally guaranteed.

Do not expose personal phone numbers publicly unless intentionally part of support operations.

---

# 31. Marketing components to build once and reuse

The theme should have reusable presentation components.

Recommended:

```text
marketing-section
marketing-section-header
marketing-eyebrow
marketing-hero
marketing-button
marketing-button-secondary
marketing-product-frame
marketing-browser-frame
marketing-phone-frame
marketing-feature-tab
marketing-step
marketing-check-list
marketing-pricing-card
marketing-faq
marketing-callout
marketing-subject-card
marketing-skill-card
marketing-stat-card
marketing-badge
marketing-trust-note
```

Use PHP template parts where useful:

```text
template-parts/marketing/
  hero.php
  product-preview.php
  learning-loop.php
  ai-tutor-preview.php
  dashboard-preview.php
  pricing-preview.php
  faq.php
  final-cta.php
```

Do not duplicate 200-line HTML sections across multiple page templates.

---

# 32. Product previews: use real UI, not decorative illustrations

One of the most important improvements is to showcase actual platform behavior.

Recommended method:

### Option A — CSS recreation

Create lightweight static product-preview components that visually mirror:

- dashboard;
- lesson stepper;
- practice question;
- AI tutor;
- mistake notebook.

Advantages:

- very fast;
- responsive;
- no image-loading penalty;
- easy to maintain visual consistency.

### Option B — optimized screenshots

Use screenshots of the actual product when the UI is polished.

Requirements:

- WebP/AVIF;
- responsive sizes;
- lazy loading below fold;
- descriptive alt text;
- no private student information.

### Option C — hybrid

Use CSS frames with actual product screenshots inside.

This is probably the best option.

---

# 33. Copywriting rules

## Use

- “lesson by lesson”
- “NCTB-aligned”
- “practice”
- “understand”
- “mistakes”
- “revision”
- “mastery”
- “home guide”
- “contextual AI tutor”
- “English + Bangla support”
- “verified board questions” only when verified

## Avoid

- “revolutionary”
- “world-class AI”
- “100% success”
- “guaranteed A+”
- “replace tuition”
- “official NCTB” unless authorized
- “AI knows everything”
- “unlimited AI” unless operationally true
- “board prediction” framed as certainty

---

# 34. Bilingual strategy

Do not duplicate every line in two languages on the same screen; this creates visual noise.

Recommended approach:

## Marketing copy

Primary copy in clear English with selective Bangla support.

Examples:

```text
Understand the lesson
সহজভাবে বুঝুন
```

or:

```text
Explain in Bangla
বাংলায় বুঝিয়ে দিন
```

## Navigation

Use one language consistently.

## Product previews

Use the real bilingual UI.

## Future

Add a global English/Bangla marketing-language switch if there is enough translated content to support it consistently.

---

# 35. Mobile-first requirements

Assume:

- 360px-wide Android devices;
- mobile data;
- intermittent connection;
- one-handed use.

Requirements:

- no horizontal scroll;
- 44px minimum interactive target;
- CTA full-width on narrow phones where appropriate;
- hero visual below text;
- product tabs horizontally scrollable or stacked;
- pricing cards stacked;
- no autoplay video;
- no giant background video;
- no heavy scroll animation;
- no 5MB hero image;
- lazy-load below-fold images;
- minimize JavaScript;
- use CSS for decorative effects.

---

# 36. Desktop design requirements

Target content max-width:

```text
1120–1200px
```

Use intentional asymmetry in the hero and product-preview sections.

Avoid filling the entire viewport width with cards.

Recommended grid rhythm:

```text
Hero:           5 / 7
Comparison:     5 / 7
Product tabs:   4 / 8
SSC/HSC:        6 / 6
Pricing:        4 / 4 / 4
```

---

# 37. Accessibility

Minimum requirements:

- semantic heading hierarchy;
- one H1 per page;
- keyboard-accessible menus and accordions;
- visible focus styles;
- buttons must be real `<button>` elements where actions occur;
- links must be `<a>`;
- sufficient color contrast;
- icons never carry meaning alone;
- reduced-motion support;
- alt text on meaningful screenshots;
- decorative images use empty alt;
- FAQ accordions expose `aria-expanded`;
- mobile menu traps/focus behavior handled correctly;
- Bengali text marked appropriately where useful.

---

# 38. Performance budget

Suggested target for public homepage:

```text
Initial page HTML:          lean
Critical CSS:               minimal
JavaScript:                 < 100 KB gzipped if practical
Hero media:                 < 250–350 KB optimized
Below-fold images:          lazy-loaded
Third-party scripts:        minimal
```

Goals:

- good Core Web Vitals;
- fast first content on mobile data;
- no AI calls on marketing page load;
- no WooCommerce-heavy assets on pages that do not need commerce interactions where avoidable.

Conditionally enqueue:

```text
marketing.css
marketing.js
```

only on marketing pages.

---

# 39. SEO structure

Each page must have a distinct intent.

Suggested titles:

```text
Home:
NCTB AI Learning Hub — Lesson-by-Lesson Learning for Bangladesh Students

SSC:
SSC English Learning & Practice — NCTB Lesson-by-Lesson

HSC:
HSC English Learning & Practice — NCTB Lesson-by-Lesson

How It Works:
How NCTB Learning Hub Works — Learn, Practice, Revise

Pricing:
Pricing — NCTB Learning Hub
```

Do not keyword-stuff.

Use structured FAQ schema only if FAQ content is actually visible to users and implementation follows current search-engine guidelines.

---

# 40. Trust and legal clarity

Because the product references NCTB, include appropriate independent-platform wording.

Do not:

- use NCTB logos without permission;
- imply government ownership;
- imply board endorsement;
- publish copyrighted textbook content beyond what is legally permitted.

Public copy can say:

> NCTB-aligned

rather than:

> Official NCTB AI Tutor

unless official authorization exists.

---

# 41. No fake social proof

Until genuine testimonials exist, do not fabricate them.

Instead use product proof:

```text
Lesson structure preview
Real practice preview
Real AI tutor preview
Real dashboard preview
Real revision workflow
```

Later, verified student/parent testimonials may be introduced with permission.

---

# 42. Animation / interaction

Use motion for orientation, not decoration.

Allowed:

- hero mockup subtle rise-in;
- tab transitions;
- accordion expand;
- progress bar animate once;
- button hover/focus;
- floating tutor card with very subtle motion.

Avoid:

- parallax;
- constant bouncing icons;
- particles;
- glowing gradients;
- excessive scroll-triggered animation;
- autoplay carousels.

Respect:

```css
@media (prefers-reduced-motion: reduce) { ... }
```

---

# 43. Homepage wireframe

```text
┌───────────────────────────────────────────────────────┐
│ HEADER                                                │
├───────────────────────────────────────────────────────┤
│ HERO                                                  │
│ NCTB lesson. Learn. Practice. Master.    PRODUCT UI   │
│ [Start Free] [How it Works]              MOCKUP       │
├───────────────────────────────────────────────────────┤
│ TRUST / PRODUCT PRINCIPLES                            │
├───────────────────────────────────────────────────────┤
│ DIGITAL HOME GUIDE / DIFFERENCE                       │
│ Text                          Comparison              │
├───────────────────────────────────────────────────────┤
│ LEARNING LOOP                                         │
│ Learn → Practice → Tutor → Test → Mistakes → Review   │
├───────────────────────────────────────────────────────┤
│ INTERACTIVE PRODUCT SHOWCASE                          │
│ Lesson | Practice | Tutor | Mistakes | Progress       │
│                     Large Preview                     │
├───────────────────────────────────────────────────────┤
│ SIX ENGLISH SKILLS                                    │
├───────────────────────────────────────────────────────┤
│ AI THAT KNOWS YOUR LESSON                             │
│ Context diagram                  Tutor preview        │
├───────────────────────────────────────────────────────┤
│ HOME STUDY DASHBOARD PREVIEW                          │
├───────────────────────────────────────────────────────┤
│ SSC ENGLISH                   HSC ENGLISH             │
├───────────────────────────────────────────────────────┤
│ TRY A FREE LESSON                                    │
├───────────────────────────────────────────────────────┤
│ PRICING PREVIEW                                       │
├───────────────────────────────────────────────────────┤
│ PARENT / TRUST                                        │
├───────────────────────────────────────────────────────┤
│ FAQ                                                   │
├───────────────────────────────────────────────────────┤
│ FINAL CTA                                             │
├───────────────────────────────────────────────────────┤
│ FOOTER                                                │
└───────────────────────────────────────────────────────┘
```

---

# 44. Homepage density

Desktop homepage target:

```text
12–15 meaningful sections maximum
```

But sections should vary in size.

Do not make every section:

```text
heading
paragraph
three identical cards
```

Alternate:

- split layouts;
- product UI;
- timeline;
- comparison;
- tabs;
- full-width CTA;
- two-column course choice.

---

# 45. WordPress implementation approach

The existing architecture rule remains:

> Theme = presentation  
> Plugin = learning/business logic

Public marketing should primarily live in the theme.

Recommended files:

```text
wp-content/themes/nctb-child-theme/
├── front-page.php
├── page-how-it-works.php
├── page-subjects.php
├── page-ssc-english.php
├── page-hsc-english.php
├── page-pricing.php
├── page-free-lesson.php
├── page-faq.php
├── page-contact.php
├── page-privacy.php
├── page-terms.php
│
├── template-parts/
│   └── marketing/
│       ├── hero.php
│       ├── trust-strip.php
│       ├── difference.php
│       ├── learning-loop.php
│       ├── product-showcase.php
│       ├── skills.php
│       ├── ai-tutor-preview.php
│       ├── dashboard-preview.php
│       ├── subject-choice.php
│       ├── free-lesson-cta.php
│       ├── pricing-preview.php
│       ├── trust.php
│       ├── faq.php
│       └── final-cta.php
│
├── css/
│   └── marketing.css
│
└── js/
    └── marketing.js
```

Only create `marketing.js` if interaction actually requires it.

Most layout/visual behavior should be CSS.

---

# 46. Marketing content editability

The original plan allows marketing copy in theme templates because it is presentation rather than curriculum.

That is acceptable for initial development.

However, long term, avoid requiring a code deployment merely to change:

- hero headline;
- pricing copy;
- FAQ;
- announcement;
- course availability;
- footer/support text.

Recommended staged approach:

### V1

Theme copy for speed.

### V1.1+

Use:

- WordPress page editor;
- theme options only where justified;
- custom fields only if genuinely necessary.

Do not install a large page builder simply to make marketing copy editable.

---

# 47. Marketing page state logic

Public header CTA should adapt to authentication.

## Logged out

```text
Login
Start Free
```

## Logged in, onboarding incomplete

```text
Continue Setup
```

## Logged in, onboarding complete

```text
Dashboard
```

Do not show a logged-in student “Register”.

---

# 48. Conversion funnel

Primary funnel:

```text
Google/social/direct
        ↓
Homepage / SSC / HSC landing
        ↓
Free Lesson
        ↓
Experience lesson
        ↓
Create account
        ↓
Onboarding
        ↓
Dashboard
        ↓
Continue free content
        ↓
Locked lesson / pricing
        ↓
Purchase
```

Secondary:

```text
Homepage
→ Pricing
→ Register
→ Dashboard
```

The free lesson funnel should be treated as primary because it demonstrates the product.

---

# 49. Analytics events for marketing

Add privacy-conscious event tracking later.

Useful events:

```text
marketing_cta_click
free_lesson_open
free_lesson_started
free_lesson_completed
register_started
register_completed
pricing_view
plan_selected
checkout_started
purchase_completed
faq_opened
ssc_landing_view
hsc_landing_view
```

Do not track keystrokes or sensitive learning content for marketing analytics.

---

# 50. Current marketing M1 — recommended improvement

The current plan marks M1 homepage foundation as complete.

Do not discard it blindly.

Claude should first audit:

```text
front-page.php
css/marketing.css
header.php
footer.php
functions.php
```

Then refactor the existing M1 work into this design system.

Preserve:

- working responsive behavior;
- working URLs;
- current logged-in/logged-out menu behavior;
- accessibility already present;
- current green brand identity where useful.

Replace/improve:

- generic feature-card repetition;
- generic “AI” messaging;
- weak product proof;
- hero visual if decorative/generic;
- shallow course positioning;
- inconsistent spacing;
- excessive borders/shadows;
- weak CTA hierarchy.

---

# 51. Build order for the redesigned marketing frontend

Build one marketing phase at a time.

## MARKETING DESIGN PHASE A — Audit + design tokens

### Goal

Establish the professional visual system without breaking current pages.

### Work

- audit current `front-page.php`;
- audit `marketing.css`;
- audit header/footer;
- define CSS tokens;
- normalize spacing;
- normalize buttons;
- normalize cards;
- normalize headings;
- improve focus states;
- confirm mobile widths;
- take before/after screenshots.

### Done when

- existing marketing homepage still works;
- visual system is consistent;
- no plugin logic changed;
- no regression in student pages.

### STOP

Report.

---

## MARKETING DESIGN PHASE B — Homepage conversion redesign

### Goal

Build the homepage described in this document.

### Required sections

1. header;
2. hero;
3. credibility strip;
4. digital-home-guide difference;
5. learning loop;
6. product showcase;
7. English skills;
8. contextual AI;
9. dashboard preview;
10. SSC/HSC cards;
11. free lesson CTA;
12. pricing preview;
13. parent/trust;
14. FAQ;
15. final CTA;
16. footer.

### Done when

- clear above-the-fold value;
- real product previews visible;
- one dominant primary CTA;
- fully responsive;
- keyboard accessible;
- no fake claims;
- no PHP/JS errors.

### STOP

Report.

---

## MARKETING DESIGN PHASE C — How It Works + Subjects

Build:

- `page-how-it-works.php`;
- `page-subjects.php`.

Use the shared components from Phase B.

### Done when

- no duplicated giant blocks;
- future subjects clearly distinguished from available subjects.

### STOP

Report.

---

## MARKETING DESIGN PHASE D — SSC/HSC conversion pages

Build:

- SSC English;
- HSC English.

Each should include:

- lesson structure;
- real product preview;
- skills;
- tutor;
- practice/revision;
- pricing CTA;
- free sample CTA;
- FAQ.

### STOP

Report.

---

## MARKETING DESIGN PHASE E — Free Lesson funnel

### Goal

Turn the prototype lesson into the product demo.

Connect public marketing CTA to the actual free lesson/access logic.

Do not duplicate the lesson in the marketing theme.

### STOP

Report.

---

## MARKETING DESIGN PHASE F — Pricing

Build professional pricing:

- free;
- single lesson;
- monthly;
- only additional active options.

Integrate existing entitlement logic rather than duplicating access rules in the theme.

### STOP

Report.

---

## MARKETING DESIGN PHASE G — FAQ / Support / Legal

Build:

- FAQ;
- contact/support;
- privacy;
- terms;
- refund/access policy if required.

Use editable WordPress content where appropriate.

### STOP

Report.

---

## MARKETING DESIGN PHASE H — Conversion + accessibility + performance QA

Test:

- 360px;
- 390px;
- 768px;
- 1024px;
- 1366px;
- 1440px.

Verify:

- keyboard navigation;
- focus;
- menu;
- accordions;
- contrast;
- reduced motion;
- no layout shift;
- images optimized;
- no horizontal overflow;
- public vs logged-in navigation;
- free lesson CTA;
- login/register;
- pricing CTA;
- WooCommerce links where applicable;
- no PHP errors;
- no JS console errors.

Run Lighthouse or equivalent.

Do not chase a score while breaking usability; use metrics as diagnostics.

---

# 52. Definition of done for public marketing V1

Marketing frontend is ready when:

- homepage clearly communicates NCTB lesson-by-lesson learning within the first viewport;
- SSC/HSC English is visibly the first active product;
- actual product UI is demonstrated;
- visitor can understand the learning loop without registering;
- visitor can start a free lesson easily;
- logged-out and logged-in navigation states are correct;
- pricing matches actual entitlement behavior;
- no unsupported/fake claims;
- mobile experience is strong;
- Bangla text renders correctly;
- accessibility basics pass;
- marketing assets are conditionally loaded;
- no learning/business rules are duplicated into the theme;
- no secrets or API keys enter frontend code;
- no regressions to dashboard, lesson, practice, AI tutor, payments, writing/listening/speaking;
- all new public pages return HTTP 200;
- PHP lint passes;
- existing automated tests still pass;
- Build Report is written before another marketing phase starts.

---

# 53. Claude implementation rules

Before changing code, Claude must:

```text
1. git pull
2. read AGENTS.md
3. read BUILD_STATE.md
4. read this file
5. read MARKETING_SITE_PLAN.md
6. inspect the existing theme files
7. inspect current marketing homepage in browser
8. take screenshots at mobile and desktop widths
9. identify what can be preserved
10. implement ONLY the requested marketing phase
```

Do not:

- rewrite learning plugin architecture;
- change database schema for visual work;
- duplicate entitlement logic;
- expose AI keys;
- hard-code curriculum data;
- fabricate testimonials;
- fabricate platform statistics;
- invent prices;
- install a heavy page builder;
- add a frontend framework solely for marketing pages;
- modify WordPress core;
- begin the next phase automatically.

---

# 54. Build Report format for every marketing phase

```markdown
# MARKETING BUILD REPORT — PHASE X

## Goal

## Existing files reviewed

## What was changed

## Files created

## Files modified

## Visual design decisions

## Responsive behavior

## Accessibility checks

## Performance checks

## Functional links tested

## Regression tests

## Screenshots reviewed

### Mobile

### Tablet

### Desktop

## PHP / JS errors

## Known issues

## What was intentionally not changed

## Git commit

## Recommendation for next phase

STOP — awaiting human review.
```

---

# 55. Full implementation prompt for Claude

Copy the prompt below when you are ready to redesign the public marketing frontend.

```text
You are improving the PUBLIC MARKETING FRONTEND of the existing WordPress
NCTB AI Learning Hub.

This is an existing project. Do not rebuild it from scratch.

FIRST:

1. git pull.
2. Read AGENTS.md.
3. Read BUILD_STATE.md.
4. Read README.md / the authoritative project build plan.
5. Read docs/CODING_STANDARDS.md.
6. Read docs/MARKETING_SITE_PLAN.md.
7. Read PUBLIC_MARKETING_FRONTEND_DESIGN_PLAN.md.
8. Inspect the current WordPress child theme, especially:
   - front-page.php
   - header.php
   - footer.php
   - functions.php
   - css/marketing.css
   - any marketing page templates
   - template parts
9. Open the actual homepage in the local browser.
10. Inspect it at mobile and desktop widths before editing.
11. Capture or record the current visual state.
12. Check git status and make sure you are not overwriting another agent's
    uncommitted work.

PROJECT RULES:

- WordPress is the platform.
- nctb-learning-hub plugin contains learning/business logic.
- nctb-child-theme is presentation only.
- Do not modify WordPress core.
- Do not move learning logic into the theme.
- Do not duplicate entitlement/payment logic in templates.
- Do not call AI directly from browser code.
- Do not expose secrets.
- Do not hard-code curriculum data into marketing templates.
- Marketing copy may live in theme templates for V1.
- Use WordPress Coding Standards.
- Escape all output.
- Use translation functions for user-facing strings.
- Maintain Bangla UTF-8 support.
- Mobile-first.
- Assume Android devices and mobile data.
- Avoid unnecessary JavaScript and heavy third-party dependencies.
- Do not install a page builder unless explicitly approved.
- Do not fabricate testimonials, student counts, success rates, board results
  or prices.
- Do not imply this is an official NCTB/government website unless the project
  has explicit authorization.
- Build ONLY the requested marketing phase.
- Test it.
- Write the marketing Build Report.
- Commit/push.
- STOP for human review.

PRODUCT POSITIONING:

This is a lesson-by-lesson digital companion to the Bangladesh NCTB curriculum.

First product:
SSC + HSC English.

Core experience:
Book → Unit → Lesson → Learn → Practice → Tutor → Test → Mistakes →
Revision → Mastery.

The public website should feel like a professional modern academic product,
not a coaching-centre poster, not a children's game, not a government portal,
and not a generic AI SaaS landing page.

PRIMARY VALUE PROPOSITION:

"Your NCTB lesson. Learn it. Practise it. Master it."

Support message:

"Follow the same SSC or HSC English lessons you study in school. Get clear
explanations, guided practice, mistake review, revision, writing and speaking
support, and contextual AI help when you need it."

PRIMARY CTA:
Start Free Lesson

SECONDARY CTA:
See How It Works

DESIGN DIRECTION:

- Preserve the existing green brand anchor, refine it into a complete system.
- Professional academic typography.
- Generous whitespace.
- Strong hierarchy.
- Actual product UI previews instead of robot illustrations.
- Restrained shadows.
- 16–20px card radii.
- Strong mobile layouts.
- One primary CTA per section.
- Use green primarily for brand/primary CTA.
- Use blue selectively for AI.
- Use amber selectively for revision/attention.
- Avoid excessive gradients, neon, glassmorphism, particles and animations.
- Support prefers-reduced-motion.
- Use clear focus states and semantic HTML.

HOMEPAGE CONTENT ORDER:

1. Header
2. Hero with actual product preview
3. Credibility/product-principle strip
4. "Digital home guide, not another course library" comparison
5. Learn → Practice → Tutor → Test → Mistakes → Revision → Mastery loop
6. Interactive product showcase:
   Lesson / Practice / AI Tutor / Mistakes / Progress / Writing
7. Six English skills
8. Contextual AI section
9. Student dashboard/home-study-guide preview
10. SSC English + HSC English cards
11. Free lesson conversion block
12. Pricing preview
13. Parent/trust section
14. FAQ
15. Final CTA
16. Footer

IMPORTANT PRODUCT PROOF:

Use the REAL capabilities already present in the product:

- interactive lesson activity system;
- progressive hints;
- deterministic practice marking;
- mistake notebook;
- concept mastery;
- spaced revision;
- student home-study dashboard;
- entitlements;
- contextual AI tutor;
- writing process;
- listening;
- speaking.

Do not claim board analytics/features as active unless the current repository
shows they are implemented and customer-ready.

PRODUCT PREVIEW RULE:

Prefer lightweight CSS/static UI recreations or optimized screenshots of the
real application. Do not create generic AI illustrations.

MOBILE:

Test at minimum:
360px, 390px, 768px.

DESKTOP:
1024px, 1366px, 1440px.

No horizontal scrolling.
No autoplay video.
No giant hero media.
Lazy-load below-fold screenshots.
Do not load marketing assets globally if not needed.

ACCESSIBILITY:

- semantic headings;
- keyboard navigation;
- visible focus;
- correct button/link semantics;
- sufficient contrast;
- accordion ARIA;
- meaningful alt text;
- reduced motion.

NOW BUILD ONLY:

[INSERT MARKETING PHASE HERE, e.g. "MARKETING DESIGN PHASE A — Audit + design tokens"]

When complete:

1. Run PHP lint.
2. Run existing tests.
3. Check browser console.
4. Verify homepage and student pages for regressions.
5. Review mobile + desktop screenshots.
6. Write MARKETING_BUILD_REPORT_PHASE_[X].md using the required format.
7. Update BUILD_STATE.md only if the project's tracking protocol requires it.
8. Commit and push.
9. STOP.

Do not begin the next marketing phase.
```

---

# 56. Recommended immediate next action

Do **not** ask Claude to implement all pages now.

Start with:

> **MARKETING DESIGN PHASE A — Audit + design tokens**

Reason:

The existing M1 homepage already exists. The safest professional redesign starts by visually auditing it, preserving functioning structure, and establishing a consistent design system.

After Phase A:

1. bring the Build Report;
2. inspect screenshots;
3. adjust design direction if necessary;
4. then authorize Homepage Phase B.

This keeps the same disciplined one-phase-at-a-time process as the core learning platform.

---

# 57. Final design principle

The public website should visually prove this sentence:

> **This is the student's NCTB lesson turned into an interactive learning experience, with practice, memory, revision and a tutor built around it.**

If a section does not help a visitor understand or trust that promise, it probably does not belong on the homepage.
