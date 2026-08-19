# NCTB LEARNING HUB — MARKETING BUILD REPORT (PROFESSIONAL P1)

**Phase completed:** Professional Frontend Redesign (Phase P1 — Global Visual Reset + Homepage)
**Specification:** [`docs/plans/frontend-design-plan-2.md`](../plans/frontend-design-plan-2.md)
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash)
**Environment:** Local Docker (`http://localhost:8080`)
**Theme:** `nctb-child-theme` v0.12.0 | **WordPress:** 7.0.4 | **PHP:** 8.3.33

---

## 1. Before Problems Identified
- **Visual Clutter & Competition:** Too many equal cards, colorful badges, and icon tiles competing for visitor attention simultaneously.
- **Section Grid Repetition:** Every section previously relied on a 3-card or 6-card grid with identical card weights.
- **Header Chrome:** Public marketing header included a dark-mode toggle that distracted from the core product conversion flow.
- **Showcase Density:** A 6-tab showcase created high cognitive load and horizontal tab crowding on mobile devices.
- **Spanning Rhythm:** Sections had tight, compressed vertical padding (24–32px) rather than spacious editorial breathing room.

---

## 2. Files Reviewed & Changed

### Reviewed:
- `front-page.php`
- `header.php`
- `footer.php`
- `functions.php`
- `css/marketing.css`
- `css/theme-ui.css`
- `js/theme-ui.js`

### Changed:
1. `wp-content/themes/nctb-child-theme/css/marketing.css` — Replaced entire CSS with the professional design system:
   - Simplified brand tokens: `--brand (#0b6e4f)`, `--brand-dark (#075b42)`, `--brand-soft (#edf8f3)`, `--text (#16211c)`, `--surface-alt (#f7f9f8)`, `--surface-warm (#fbfaf7)`.
   - Restrained accents: Blue for AI only (`#315ea8`), Amber for revision only (`#b7791f`), Red for mistakes only (`#b84b4b`).
   - 8px-based spacing rhythm with 96–128px desktop section padding and 64–80px mobile section padding.
   - 3 button styles maximum (Primary Brand Green, Secondary Bordered Surface, and Text Link).
   - 3 card styles maximum (Product UI preview card, Information card with light border, and Elevated Pricing card).
2. `wp-content/themes/nctb-child-theme/front-page.php` — Redesigned into a cohesive 12-step narrative:
   - **Hero:** Left-aligned headline (*"Your NCTB lesson. Learn it. Practise it. Master it."*) + single primary CTA + live browser mockup with real Nelson Mandela lesson text and floating AI Tutor card.
   - **Proof Strip:** Subtle text checkmarks without heavy card boxes.
   - **3-Part Alternating Product Story:** Chapter A (Learn), Chapter B (Practise with 3-level progressive hints), Chapter C (Improve with mistake notebook & spaced revision).
   - **Learning Loop Timeline:** Clean horizontal sequence (`Learn → Practise → Tutor → Test → Fix → Revise → Master`).
   - **Two-Column Comparison:** Side-by-side comparison of typical passive courses vs. active NCTB guided mastery.
   - **AI Section:** Real AI Tutor drawer mockup with the 4 core actions.
   - **Dashboard Section:** Full-width clean study guide preview.
   - **SSC & HSC Hubs:** Two large editorial cards with syllabus highlights.
   - **Pricing:** 3 clean cards with popular plan elevation.
   - **FAQ:** Single-column accordion (`max-width: 820px`).
   - **Final CTA & Footer:** Deep green CTA band and clean 4-column editorial footer.
3. `wp-content/themes/nctb-child-theme/header.php` — Hidden public dark-mode toggle on marketing pages; refined subtle `EN | বাংলা` language switcher.
4. `wp-content/themes/nctb-child-theme/footer.php` — Conditional footer rendering preventing duplicate footer markup.

---

## 3. Design Decisions & Token Refinements

### Typography
- Standardized editorial scale: H1 `clamp(2.4rem, 4.8vw, 3.8rem)` / H2 `clamp(1.85rem, 3.2vw, 2.6rem)` / Body `17px` / Line-height `1.65`.
- Controlled body line-length (~55–72 characters on desktop) with left-aligned section headings.

### Spacing & Max-Width
- Container max-width: `1180px` with `24px` inline padding (`18px` on mobile).
- Ample vertical section breathing room (`104px` desktop / `68px` mobile).

### Color Discipline
- Green is strictly reserved for brand identity, progress, and primary actions.
- Blue is strictly localized to AI Tutor dialogs.
- Amber is strictly localized to Hint Ladders and Spaced Revision.
- Rose is strictly localized to the Mistake Notebook.

---

## 4. Quality Assurance & Testing

- **PHP Syntax:** All 75 PHP files linted clean with `php -l` (**0 errors**).
- **HTTP Status Check:** All 17 platform routes return **HTTP 200 OK**.
- **Mobile Responsiveness:** Clean single-column editorial stacking at 390px with 0 horizontal overflow.
- **Backend & Business Logic:** Unmodified (All 13 custom database tables, REST APIs, marking engine, and student workflows remain 100% intact).

---

## 5. Next Recommended Phases
- **P2:** SSC English + HSC English landing page redesign.
- **P3:** How It Works + Subjects page polish.
- **P4:** Pricing + FAQ + Contact page polish.

**STOP HERE. PHASE P1 COMPLETED.**
