# NCTB AI Learning Hub — Design System & Brand Blueprint

> **Purpose.** A concrete, buildable design system so the product looks like a **first-class, globally-credible education brand** — calm, trustworthy, focused, fast. Every screen the agent builds must use these tokens. This is not decoration; consistency *is* what makes a product feel world-class.
>
> **Design north star:** *Clarity over cleverness. Calm over loud. Fast over fancy.* A student on a cheap phone at night, tired, should feel the product is on their side — quiet, legible, encouraging. Think the restraint of Khan Academy + Duolingo's warmth + the typographic calm of a well-made book.

---

## 1. Brand foundation

**Personality:** trustworthy, modern, warm, academic-but-not-stiff, distinctly Bangladeshi without cliché. Not childish, not corporate-cold.

**Brand promise in one line:** *"Learn it, practise it, master it — with a tutor in your pocket."*

**Logo direction (brief for a designer, not built here):** a simple, geometric mark suggesting growth/light/a book or spark; works in one color; legible at 24px (favicon) and on a dark navbar. Avoid gradients-heavy logos (they render poorly small and cost data). Provide SVG, mono, and favicon variants.

---

## 2. Color system

> Proposed palette. The **accent (primary)** is the one color to change if you want a different brand identity — everything else is a neutral/support system that stays. All pairings below are chosen to meet **WCAG AA** contrast for text; the agent must verify contrast on final backgrounds.

### 2.1 Core palette (light mode)

| Token | Hex | Use |
|---|---|---|
| `--c-primary` | `#1E6F5C` (deep teal-green) | Primary brand, key CTAs, active states. Evokes growth/learning, distinct from the usual edu-blue. |
| `--c-primary-hover` | `#175A4A` | Hover/pressed primary. |
| `--c-primary-soft` | `#E4F1EC` | Primary-tinted backgrounds, chips, highlights. |
| `--c-accent` | `#F2A65A` (warm amber) | Secondary accent — encouragement, streaks, highlights, "achievement" moments. Use sparingly. |
| `--c-ink` | `#14201C` | Primary text (near-black, slightly warm). |
| `--c-ink-2` | `#41514B` | Secondary text. |
| `--c-ink-3` | `#6B7A74` | Muted text, captions. |
| `--c-line` | `#E2E8E5` | Borders, dividers. |
| `--c-surface` | `#FFFFFF` | Cards, sheets. |
| `--c-bg` | `#F7FAF9` | App background (soft, not stark white — easier on eyes, feels premium). |
| `--c-success` | `#2E7D5B` | Correct answers, completion. |
| `--c-warning` | `#C77A24` | Needs attention. |
| `--c-danger` | `#C0453B` | Errors, mistakes (used gently, never alarming). |
| `--c-info` | `#2C6E9B` | Neutral info, links. |

### 2.2 Dark mode (required — many students study at night)

| Token | Hex |
|---|---|
| `--c-bg` | `#0F1613` |
| `--c-surface` | `#17211D` |
| `--c-ink` | `#EAF1EE` |
| `--c-ink-2` | `#B8C6C0` |
| `--c-line` | `#26332E` |
| `--c-primary` | `#3F9B82` (lightened for contrast on dark) |
| `--c-primary-soft` | `#12332A` |

Implement via CSS custom properties toggled by a `data-theme` attribute + a user preference respecting `prefers-color-scheme`.

### 2.3 Color usage rules
- **One primary action per screen.** The teal CTA is the eye's anchor; don't compete it with other strong colors.
- **Amber = earned moments only** (streaks, mastery, celebration). Overuse kills its meaning.
- **Semantic colors are never decorative.** Red only means error/mistake; green only means correct/done.
- **Never rely on color alone** — pair with icon/text (accessibility + colorblind users).

---

## 3. Typography

> Bilingual (Bangla + Latin) is non-negotiable. Bangla needs generous line-height and a font designed for screen. All fonts below are free (Google Fonts) — **verify current licensing yourself before shipping**, but these are standard, widely-used families.

### 3.1 Font families

| Role | Font | Notes |
|---|---|---|
| Bangla UI + body | **Hind Siliguri** (already in repo) or **Anek Bangla** | Hind Siliguri is proven for screen Bangla. Anek Bangla is a modern variable option — evaluate both. |
| Bangla headings (optional premium feel) | **Tiro Bangla** (serif) or keep Anek Bangla bold | A serif Bangla for hero headings can feel editorial/premium; test readability. |
| Latin UI + body | **Inter** (already in repo) | Neutral, superb screen legibility. Keep. |
| Latin display/headings | **Plus Jakarta Sans** or **Sora** | Slightly more character than Inter for hero headings; optional. |
| Monospace (code/ICT) | **JetBrains Mono** or system mono | For ICT code snippets. |

**Pairing recommendation:** Body = Hind Siliguri (bn) + Inter (en). Headings = same, bumped weight — or introduce Plus Jakarta Sans for Latin headings only. Keep total font files minimal for bandwidth (subset + `font-display: swap`).

### 3.2 Type scale (fluid, mobile-first)

| Token | Size (mobile → desktop) | Weight | Use |
|---|---|---|---|
| `--t-display` | 28 → 44px | 700 | Hero headline |
| `--t-h1` | 24 → 32px | 700 | Page title |
| `--t-h2` | 20 → 26px | 600 | Section |
| `--t-h3` | 18 → 20px | 600 | Card title |
| `--t-body` | 16 → 17px | 400 | Body (never below 16px on mobile) |
| `--t-small` | 14px | 400 | Captions, meta |
| `--t-micro` | 12px | 500 | Labels, tags |

**Line-height:** Bangla body ≥ 1.7; Latin body ≥ 1.6; headings ~1.25. Bangla runs tall — give it room.
**Measure:** 60–75 characters per line max for reading content (notes, lessons).

---

## 4. Spacing, radius, elevation

- **Spacing scale (8px base):** 4, 8, 12, 16, 24, 32, 48, 64. Use tokens `--s-1`…`--s-8`. Consistent rhythm is 80% of a "polished" feel.
- **Radius:** `--r-sm 8px` (inputs, chips), `--r-md 14px` (cards), `--r-lg 22px` (sheets, hero), `--r-full` (pills, avatars). Soft, friendly, not sharp.
- **Elevation:** use soft, low-spread shadows on a light bg; avoid heavy drop shadows. Two levels only: `--shadow-1` (cards), `--shadow-2` (menus/modals). In dark mode, use border + subtle glow instead of shadow.
- **Container widths:** content max 720px (reading), app max 1120px, marketing max 1200px.

---

## 5. Core components (spec so the agent builds them once, reuses everywhere)

Build these as reusable partials/CSS classes. Every screen composes from them.

- **Buttons:** Primary (teal, filled), Secondary (outline), Ghost (text), Danger (only for destructive). Min tap target 44×44px. Loading + disabled states. Icon+label option.
- **Cards:** surface bg, `--r-md`, `--shadow-1`, 16–24px padding. Variants: lesson card, module card, note card, KPI stat card, paywall card.
- **Navbar:** sticky, minimal; role-aware menu; theme toggle; profile avatar menu. Mobile = bottom tab bar for app (Home, Learn, Practice, Progress, Profile) — thumb-reachable.
- **Progress indicators:** thin bar for lesson stepper; ring for mastery %; subtle, not gamified-garish.
- **Chips/tags:** subject, class, free/paid, difficulty. Pill shape, `--c-primary-soft` bg for neutral, semantic colors where meaningful.
- **Paywall card:** calm, honest, benefit-led ("Unlock the AI tutor and full lessons"). Never dark-pattern. Clear price, clear what you get, one CTA.
- **AI tutor drawer:** slide-out sheet, message bubbles, quick-action chips, quota indicator, "formative / not official" disclaimer footer.
- **Empty states:** friendly illustration + one line + one action. Never a blank screen.
- **Forms/inputs:** large, labeled, inline validation, Bangla-friendly. Errors in words + color + icon.
- **Video item (module):** YouTube facade (thumbnail + play; load iframe on click to save data), title, duration, completion tick, lock icon if paid.
- **Toasts/celebration:** gentle success toast; a small confetti/streak moment on mastery (amber) — tasteful, quick, skippable.

---

## 6. Iconography & imagery
- **Icons:** one consistent set (e.g. Lucide/Feather-style line icons) — stroke 1.75px, rounded caps, matches the soft radius language. Never mix icon styles.
- **Illustration:** light, optimistic, inclusive of Bangladeshi students/teachers; flat or soft. Keep file sizes tiny (SVG where possible). Avoid stocky corporate 3D.
- **Diagrams (science/maths):** clean, labeled, high-contrast, printable; consistent style across subjects. See `03_CONTENT_OPERATIONS.md` for the production pipeline.
- **Photography (marketing):** authentic, warm, local; avoid generic Western stock.

---

## 7. Motion
- **Principle:** motion clarifies, never entertains. Fast (150–250ms), ease-out, purposeful.
- Page/section transitions subtle; stepper advances with a gentle slide; correct-answer feedback quick and affirming.
- **Respect `prefers-reduced-motion`** — disable non-essential animation.
- No autoplay video, no distracting loops (also saves data/battery).

---

## 8. Accessibility (WCAG 2.1 AA, required)
- Text contrast ≥ 4.5:1 (body), ≥ 3:1 (large). Verify every token pairing.
- Full keyboard operability; visible focus rings (don't remove outlines).
- Semantic HTML + ARIA only where needed; correct `lang` attributes for bn/en.
- Alt text on all meaningful images; captions/transcripts for audio (you already have transcript toggles).
- Tap targets ≥ 44px; forms labeled; errors announced.
- Test with a screen reader and at 200% zoom.

---

## 9. Performance-as-design (this audience makes it a design constraint)
- Total CSS lean and tokenized; critical CSS inlined; defer the rest.
- System-font fallback while webfonts load (`font-display: swap`); subset Bangla/Latin to needed glyphs.
- Images: responsive `srcset`, modern formats (WebP/AVIF), lazy-load, explicit dimensions (no layout shift).
- Video: facade-load only. Never embed 10 live iframes on one page.
- Target: usable on a low-end Android over 3G. Set exact Lighthouse/weight budgets in Build Phase 14 and enforce them.

---

## 10. Implementation notes for the agent
- Define **all tokens as CSS custom properties** in one `:root` (+ `[data-theme="dark"]`) file in the child theme. Every component references tokens — no hard-coded hex/px in components.
- Build a tiny **living style guide page** (`/style-guide/`, admin-only or hidden) showing every token and component — so future phases stay consistent and you can review the system visually.
- Do **not** pull a heavy UI framework. A tokenized CSS layer + small components is lighter and more distinctive than a generic framework theme.
- When a new screen is needed, **compose from existing components first**; only add a new component if truly novel, and add it to the style guide.

> **Bottom line:** world-class here comes from *restraint, consistency, speed, and legibility* — not from more colors or effects. A calm teal-and-warm-neutral system, beautiful bilingual type, generous spacing, and fast pages will read as premium to students, teachers, parents, and any future funder.
