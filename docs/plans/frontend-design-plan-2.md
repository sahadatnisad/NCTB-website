# NCTB Learning Hub — Professional Frontend Redesign Prompt

## Purpose

Refactor the existing public marketing frontend into a polished, premium, academic education product.

This is **not** a feature build.

This is a **visual hierarchy, spacing, typography, composition, and conversion redesign** of the existing public frontend.

Preserve all current backend logic, routes, student systems, AI systems, practice systems, payments, and curriculum logic.

The goal is to make the public website feel like a professional education platform comparable in polish to strong modern learning products — calm, credible, intentional, and premium — instead of looking like a collection of feature cards.

---

# 1. Primary design problem to solve

The current frontend contains many good features, but too many of them are visually competing at the same level.

The redesigned public site must:

- reduce visual clutter;
- use stronger whitespace;
- create clearer section hierarchy;
- reduce card repetition;
- reduce unnecessary icons/emojis;
- reduce excessive borders;
- reduce decorative gradients;
- reduce overuse of colored badges;
- avoid making every section a grid;
- avoid making every feature look equally important;
- make the product itself the visual hero;
- make the primary CTA obvious;
- make the brand feel academic and trustworthy;
- make mobile layout feel intentionally designed, not merely stacked.

---

# 2. Product positioning

Use this as the central public message:

> **Your NCTB lesson. Learn it. Practise it. Master it.**

Support line:

> Follow the same SSC or HSC English lessons you study in school. Learn clearly, practise step by step, fix mistakes, revise later, and get contextual AI help when you need it.

Short positioning:

> **A digital home guide for NCTB students.**

Do not lead with:

- “AI-powered”
- “smart learning”
- “future of education”
- “revolutionary”
- “next-generation platform”

AI is a support feature, not the brand identity.

---

# 3. Visual direction

## Overall mood

Professional.
Academic.
Modern.
Quietly premium.
Student-friendly.
Bangladesh-relevant.
Not childish.
Not corporate SaaS.
Not a coaching-centre poster.
Not a government portal.
Not gaming UI.

Reference style direction:

- spacious modern education product;
- soft neutral backgrounds;
- strong typography;
- real product screenshots;
- selective use of green;
- minimal ornamentation;
- elegant cards;
- restrained iconography;
- clean rounded surfaces;
- strong content rhythm.

---

# 4. Simplify the brand system

Keep the current green, but use fewer colors.

## Primary palette

```css
:root {
  --brand: #0b6e4f;
  --brand-dark: #075b42;
  --brand-soft: #edf8f3;

  --text: #16211c;
  --text-soft: #5f6c66;
  --text-faint: #87918c;

  --surface: #ffffff;
  --surface-alt: #f7f9f8;
  --surface-warm: #fbfaf7;

  --border: #e5e9e7;

  --ai: #315ea8;
  --warning: #b7791f;
  --danger: #b84b4b;

  --shadow-sm: 0 4px 16px rgba(18, 33, 27, .06);
  --shadow-md: 0 14px 40px rgba(18, 33, 27, .10);
}
```

## Important

Do not use:

- green + blue + amber + rose in the same section;
- bright colorful icon tiles everywhere;
- colored gradient backgrounds for most cards;
- multiple accent colors in the header;
- glassmorphism.

Use green for brand and primary action.

Use blue only for AI-specific UI.

Use amber only for revision/attention states.

Use red only for mistakes/errors.

---

# 5. Remove public dark mode from the marketing header

For the public marketing site, remove or hide the dark-mode toggle.

Reason:

- it adds chrome;
- weakens visual identity;
- adds another control before the visitor understands the product;
- creates more QA/design states;
- is not important to conversion.

Dark mode can remain inside the logged-in student application later if desired.

Do not remove functionality from the student app unless explicitly requested.

---

# 6. Language control

Keep the English/Bangla language control, but make it subtle.

Replace a large toggle with:

```text
EN | বাংলা
```

or a small language button in the header.

Do not place language switching beside the primary CTA if it creates visual competition.

---

# 7. Typography

Use stronger editorial hierarchy.

Recommended:

```css
body {
  font-family: "Inter", "Noto Sans Bengali", "Hind Siliguri", system-ui, sans-serif;
  font-size: 17px;
  line-height: 1.65;
}

h1 {
  font-size: clamp(2.6rem, 5vw, 4.5rem);
  line-height: 1.02;
  letter-spacing: -0.035em;
  max-width: 12ch;
}

h2 {
  font-size: clamp(2rem, 3.5vw, 3.2rem);
  line-height: 1.08;
  letter-spacing: -0.025em;
}

h3 {
  font-size: 1.25rem;
  line-height: 1.25;
}
```

Rules:

- Do not center every section heading.
- Use left alignment for most sections.
- Use centered headings only for selected moments such as pricing or final CTA.
- Keep paragraph width controlled.
- Avoid small body copy.
- Avoid uppercase everywhere.
- Use eyebrow labels sparingly.

---

# 8. Spacing system

Use a consistent 8px-based spacing system.

```text
8
16
24
32
48
64
80
96
128
```

Desktop section padding:

```text
96–128px vertical
```

Mobile section padding:

```text
64–80px vertical
```

Do not place sections back-to-back with only 24–32px separation.

The current design should feel more editorial and less compressed.

---

# 9. Container width

Use:

```css
max-width: 1180px;
margin-inline: auto;
padding-inline: 24px;
```

Mobile:

```css
padding-inline: 18px;
```

Do not allow text and cards to stretch edge to edge on large monitors.

---

# 10. Header redesign

## Desktop

Keep it simple.

```text
[Logo]   How It Works   Subjects   SSC English   HSC English   Pricing          Login   [Start Free]
```

Remove unnecessary icons.

Header height:

```text
72–80px
```

Use white background with very light border.

Sticky after scroll.

Primary CTA:

```text
Start Free
```

Secondary login is text-only or ghost button.

## Mobile

```text
[Logo]                              [Menu]
```

Inside menu:

```text
How It Works
Subjects
SSC English
HSC English
Pricing
FAQ
Login
[Start Free Lesson]
```

---

# 11. Homepage redesign

The homepage should have fewer visually loud sections.

Recommended order:

1. Header
2. Hero
3. Product proof strip
4. Product experience preview
5. Learning loop
6. Why this is different
7. Contextual AI
8. Home study dashboard
9. SSC / HSC English
10. Pricing
11. FAQ
12. Final CTA
13. Footer

Do not keep all 15+ previous homepage sections if the result feels too long.

---

# 12. Hero redesign

## Left

Eyebrow:

```text
NCTB-aligned • SSC & HSC English
```

Headline:

> **Your NCTB lesson.  
> Learn it. Practise it. Master it.**

Description:

> Follow the same lessons you study in school, with clear explanations, guided practice, mistake review, revision, and contextual AI support.

Buttons:

```text
[Start Free Lesson]   [See How It Works]
```

Small trust line:

```text
No card required for the free lesson.
```

Only show if true.

## Right

Use a real product UI composition.

Main frame:

- lesson title;
- unit;
- progress;
- lesson activity list;
- continue button.

Floating small AI card:

```text
AI Tutor
Why was my answer wrong?

[Explain simply]
```

Do not use:

- robot illustration;
- cartoon student;
- abstract 3D shapes;
- giant gradient sphere.

The actual product should be the hero visual.

---

# 13. Product proof strip

Immediately after hero:

```text
NCTB-aligned
Lesson-by-lesson
Practice with hints
Mistake review
Spaced revision
Contextual AI tutor
```

Use text + minimal check icons.

No large cards.

No shadows.

---

# 14. Replace the 6-tab showcase with a calmer product story

The six-tab showcase is useful, but it can feel busy.

Refactor it into 3 major product chapters:

## A. Learn

Show:

- lesson structure;
- reading;
- vocabulary;
- grammar;
- activities.

## B. Practice

Show:

- question;
- progressive hint;
- retry;
- explanation;
- mastery.

## C. Improve

Show:

- mistakes;
- revision due;
- dashboard;
- writing feedback;
- AI tutor.

Desktop:

alternating split sections.

```text
Text            Product UI
Product UI      Text
Text            Product UI
```

Mobile:

text followed by UI preview.

This is more premium than six equal tabs.

---

# 15. Learning loop section

Keep the learning loop, but simplify the visual.

Do not use seven heavy cards.

Use a horizontal timeline on desktop:

```text
Learn → Practice → Tutor → Test → Fix → Revise → Master
```

Below the timeline, explain only 3–4 key points.

Mobile:

vertical line with steps.

---

# 16. “Why it is different” section

Use one strong two-column comparison.

Left title:

> **Built around the lesson you are already studying.**

Right:

```text
Typical course
Watch
Move on
Forget
Generic AI

NCTB Learning Hub
Learn your exact lesson
Practise it
Review mistakes
Revise later
Contextual AI support
```

No large feature grid.

---

# 17. AI section redesign

Headline:

> **AI that knows what lesson you are studying.**

Visual:

show actual AI tutor drawer.

Use 4 quick actions only:

```text
Explain this
বাংলায় বুঝিয়ে দিন
Give me a hint
Why was I wrong?
```

Support copy:

> The tutor receives lesson context, your level, and relevant mistakes. It helps with the current lesson instead of acting like a generic chatbot.

Trust note:

> Verified curriculum and board content remain separate from generated explanations.

Use blue only inside the AI preview.

---

# 18. Dashboard section

This should look like a premium product screenshot.

Use one large dashboard mockup, not many small cards scattered around.

Show:

- Continue Learning;
- Revision Due;
- Needs Attention;
- Learning KPIs;
- Book progress.

Headline:

> **Know what to study next.**

Copy:

> The platform remembers your lesson progress, mistakes and revision schedule so you can continue from where you left off.

---

# 19. SSC / HSC section

Use two elegant large cards.

Do not use six small subject cards.

### SSC English

```text
SSC English
Classes 9–10

Lesson-by-lesson learning
Grammar and vocabulary
Reading and writing
Practice and revision
AI tutor support

[Explore SSC English]
```

### HSC English

```text
HSC English
Classes 11–12

NCTB lesson structure
Reading and writing
Grammar and language practice
Revision and mastery
AI tutor support

[Explore HSC English]
```

Future subjects can appear as a simple text line below:

```text
Coming later: ICT · Bangla · Mathematics · Science
```

---

# 20. Pricing redesign

Three clean cards maximum.

No excessive colored headers.

```text
Free
Single Lesson
Monthly
```

Highlight only one recommended plan.

Show:

- price;
- short description;
- 4–6 essential benefits;
- one CTA.

Do not use 15-row comparison tables on the homepage.

Detailed comparison can live on `/pricing/`.

---

# 21. FAQ redesign

Use a single-column accordion.

Width:

```text
max-width: 820px
```

Do not place FAQ into a multi-column card grid.

---

# 22. Footer redesign

Use a clean editorial footer.

Deep green background or very dark neutral.

Columns:

```text
Learn
Platform
Support
Legal
Account
```

Keep it spacious.

Do not overcrowd with icons.

---

# 23. Remove visual noise

Claude must explicitly inspect and reduce:

- emoji headings;
- colorful icon boxes;
- badges;
- repeated card borders;
- repeated shadows;
- repeated gradients;
- excessive pills;
- excessive rounded containers;
- excessive section dividers;
- hover animations on every card;
- animated decorative backgrounds;
- redundant labels;
- duplicate CTAs.

One section should usually have one visual focus.

---

# 24. Button system

Primary:

```css
background: var(--brand);
color: white;
height: 48–52px;
padding: 0 22px;
border-radius: 10px;
font-weight: 650;
```

Secondary:

```css
background: white;
border: 1px solid var(--border);
color: var(--text);
```

Text link:

```text
See how it works →
```

Do not use more than 3 button styles.

---

# 25. Card system

Only 3 card types:

## Product card

Larger.
May have shadow.
Used for real UI preview.

## Information card

Border only.
No strong shadow.

## Pricing card

Border + optional slight elevation for recommended plan.

Avoid a separate card style for every feature.

---

# 26. Product screenshots

Use actual screenshots or faithful CSS mockups.

Priority screenshots:

1. lesson;
2. practice question with hint;
3. AI tutor drawer;
4. student dashboard;
5. mistake notebook;
6. writing workbench.

Screenshots should be:

- cropped intentionally;
- high resolution;
- optimized WebP/AVIF;
- no private student data;
- no browser clutter unless intentionally framed.

---

# 27. Homepage visual rhythm

Recommended:

```text
White hero
↓
Soft mint proof strip
↓
White Learn section
↓
Soft neutral Practice section
↓
White AI section
↓
Soft mint Dashboard section
↓
White SSC/HSC
↓
Warm neutral Pricing
↓
White FAQ
↓
Deep green Final CTA
↓
Dark/deep footer
```

This creates intentional pacing.

---

# 28. Mobile-specific redesign

The mobile homepage must not feel like “desktop cards stacked vertically”.

Rules:

- use one-column editorial layout;
- reduce card count;
- large type;
- full-width primary CTA;
- product screenshots full-width;
- hide unnecessary secondary decorations;
- keep sticky header minimal;
- collapse comparison into simple rows;
- use swipe/scroll only when truly necessary;
- avoid tabs with 6 tiny labels;
- avoid horizontal overflow;
- do not shrink product screenshots until unreadable.

---

# 29. Public navigation vs student app

Keep public frontend visually distinct from the learning app, but related.

Public:

- polished;
- spacious;
- explanatory;
- conversion-focused.

Student app:

- functional;
- denser;
- task-focused;
- progress-oriented.

Do not force the marketing page's large section spacing into dashboard/lesson screens.

---

# 30. Do not redesign functionality yet

This phase must not modify:

- question marking;
- AI behavior;
- mastery calculations;
- revision algorithm;
- entitlement logic;
- WooCommerce integration;
- database schema;
- lesson activity data;
- board analytics logic;
- student privacy logic.

This is a frontend design refactor.

---

# 31. Exact implementation phase

## PROFESSIONAL FRONTEND PHASE P1 — Global visual reset + homepage

### Goal

Make the public homepage visually professional and coherent.

### Required work

1. Audit current homepage.
2. Take screenshots:
   - 390px;
   - 768px;
   - 1366px.
3. Identify visual problems.
4. Refactor design tokens.
5. Refactor typography.
6. Refactor header.
7. Refactor hero.
8. Simplify product proof.
9. Replace cluttered 6-tab showcase with 3-part product story.
10. Simplify learning loop.
11. Redesign AI section.
12. Redesign dashboard preview.
13. Redesign SSC/HSC cards.
14. Redesign pricing preview.
15. Redesign FAQ.
16. Redesign footer.
17. Verify logged-in pages did not change unexpectedly.
18. Test all CTAs.

### Do not redesign other marketing pages yet.

---

# 32. Acceptance criteria

The phase is complete only when:

- homepage looks intentional at 390px and 1366px;
- hero clearly communicates product within 5 seconds;
- one dominant primary CTA exists;
- no section looks like a generic Bootstrap feature grid;
- product screenshots are the main visual evidence;
- no fake claims are added;
- no broken routes;
- no horizontal overflow;
- no console errors;
- no PHP warnings;
- public header is clean;
- student navigation still works;
- typography is consistent;
- spacing is consistent;
- no more than 3 button styles;
- no more than 3 card styles;
- marketing dark-mode control removed or hidden;
- language control is subtle;
- existing learning functionality is untouched;
- PHP lint passes;
- existing tests pass;
- new screenshots are reviewed before STOP.

---

# 33. Claude prompt

Copy this prompt exactly.

```text
You are now performing a PROFESSIONAL FRONTEND REDESIGN of the existing
WordPress NCTB Learning Hub.

This is NOT a feature build.
This is NOT a backend build.
This is NOT a rewrite.

Your job is to improve the public marketing visual quality.

FIRST:

1. git pull
2. Read AGENTS.md
3. Read BUILD_STATE.md
4. Read README.md
5. Read docs/CODING_STANDARDS.md
6. Read docs/PUBLIC_MARKETING_FRONTEND_DESIGN_PLAN.md if present
7. Read PROFESSIONAL_FRONTEND_REDESIGN_PROMPT.md
8. Inspect:
   - front-page.php
   - header.php
   - footer.php
   - functions.php
   - css/marketing.css
   - js/theme-ui.js
   - current marketing template parts
9. Open the existing homepage in a browser.
10. Take screenshots at:
    - 390px
    - 768px
    - 1366px
11. Write down the visual problems before changing code.
12. Check git status.

IMPORTANT:

The existing site already has strong functionality.
Do not add new learning features.
Do not change backend logic.

The current visual problem is excess visual competition:
too many cards, badges, icons, colors and sections with equal importance.

TARGET DESIGN:

Professional academic education platform.
Calm.
Spacious.
Premium.
Modern.
Not childish.
Not a coaching-centre poster.
Not a generic AI SaaS page.
Not a government portal.

POSITIONING:

"Your NCTB lesson. Learn it. Practise it. Master it."

Support:

"Follow the same SSC or HSC English lessons you study in school. Learn clearly,
practise step by step, fix mistakes, revise later, and get contextual AI help
when you need it."

PRIMARY CTA:
Start Free Lesson

SECONDARY:
See How It Works

DESIGN RULES:

- Keep #0b6e4f as primary brand green.
- Reduce the number of accent colors.
- Green = primary brand/action.
- Blue = AI only.
- Amber = revision only.
- Red = mistakes only.
- White + soft neutral backgrounds dominate.
- Strong typography.
- Large whitespace.
- No excessive gradients.
- No glassmorphism.
- No glowing effects.
- No decorative robot illustration.
- Use actual product screenshots/UI previews.
- Use only 3 button styles.
- Use only 3 card styles.
- Reduce icon/emoji usage.
- Remove or hide dark-mode control from PUBLIC marketing header.
- Keep EN | বাংলা language control subtle.
- Do not center every section.
- Do not make every section a 3-card grid.
- Avoid six equally weighted feature tabs on mobile.
- Use alternating editorial split layouts.
- Preserve accessibility.
- Preserve mobile speed.
- Preserve existing URLs.

HOMEPAGE STRUCTURE:

1. Clean header
2. Hero
3. Lightweight proof strip
4. Learn product story
5. Practice product story
6. Improve product story
7. Simplified learning loop
8. Contextual AI section
9. Dashboard / home-study-guide section
10. SSC / HSC English cards
11. Pricing preview
12. FAQ
13. Final CTA
14. Footer

HERO:

Left:
"NCTB-aligned • SSC & HSC English"

H1:
"Your NCTB lesson.
Learn it. Practise it. Master it."

Description:
"Follow the same lessons you study in school, with clear explanations, guided
practice, mistake review, revision, and contextual AI support."

Buttons:
[Start Free Lesson]
[See How It Works]

Right:
Use the REAL lesson UI as the main visual.
Include a small floating AI Tutor card.
Do not use generic AI art.

PRODUCT STORY:

Replace or greatly simplify the current 6-tab showcase.

Create 3 strong alternating sections:

A. LEARN
Show lesson stepper / NCTB lesson hierarchy.

B. PRACTICE
Show question + progressive hint + retry + explanation.

C. IMPROVE
Show mistakes + revision + dashboard + writing / tutor support.

Each section should have:
- one headline
- one short explanation
- maximum 3 supporting bullets
- one large product UI preview

LEARNING LOOP:

Use:
Learn → Practice → Tutor → Test → Fix → Revise → Master

Do not render seven heavy cards.

AI SECTION:

Headline:
"AI that knows what lesson you are studying."

Show real tutor UI and only:
- Explain this
- বাংলায় বুঝিয়ে দিন
- Give me a hint
- Why was I wrong?

DASHBOARD:

Headline:
"Know what to study next."

Show one large real dashboard preview.
Do not split it into many floating mini-cards.

SSC/HSC:

Two large clean cards only.

PRICING:

Three simple cards:
Free
Single Lesson
Monthly

Do not invent prices.
Use current configured prices if already final.
Otherwise show neutral labels without fake amounts.

FAQ:

Single-column accordion, max width ~820px.

FOOTER:

Simple and editorial.
No icon clutter.

MOBILE:

This is critical.

At 390px:
- no horizontal overflow;
- hero CTA full-width or near-full-width;
- product screenshots readable;
- sections do not become endless card stacks;
- hide decorative elements that do not help;
- typography remains large;
- menu remains simple.

ARCHITECTURE RULE:

Theme = presentation only.
Plugin = learning/business logic.

Do not modify:
- AI backend
- marking
- mastery
- revision
- entitlement
- WooCommerce
- database
- lesson content structure

TEST:

After implementation:

1. PHP lint
2. existing automated tests
3. browser console
4. HTTP checks
5. screenshot review at 390 / 768 / 1366
6. check homepage while logged out
7. check dashboard/lesson while logged in
8. check menu state
9. check all homepage CTAs
10. check no horizontal overflow

BUILD REPORT:

Create:
docs/MARKETING_BUILD_REPORT_PROFESSIONAL_P1.md

Include:
- before problems
- files reviewed
- files changed
- typography decisions
- spacing decisions
- color decisions
- components removed/simplified
- screenshots reviewed
- mobile QA
- desktop QA
- regressions
- known issues
- commit hash

Then commit and push.

STOP.

Do not start SSC/HSC inner-page redesign yet.
Do not start another phase.
```

---

# 34. What I recommend after P1

After Claude completes this phase, do not immediately continue.

Bring back:

- the build report;
- 390px screenshot;
- 1366px screenshot;
- homepage URL screenshot;
- any visual issues Claude identified.

Then review visual direction.

Only after the homepage is visually correct should you proceed to:

```text
P2 — SSC English + HSC English landing page redesign
P3 — How It Works + Subjects
P4 — Pricing + FAQ + Contact
P5 — Student app visual polish
```

The homepage should establish the visual language for everything else.
