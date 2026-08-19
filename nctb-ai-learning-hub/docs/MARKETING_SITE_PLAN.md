# Public Marketing Website — Build Plan

The logged-in learning app is well developed; the **public-facing website** a first-time
visitor lands on is not. This plan builds it. Pure frontend (theme templates + CSS) — no
backend changes, so it does not collide with the phase work in the plugin.

**Design language:** extend the existing clean academic look — green accent (`#0b6e4f`),
system + Bangla-capable fonts, mobile-first, bilingual (Bangla + English) touches.

**Build rule:** marketing copy lives in theme templates (it is presentation, not curriculum,
so this does not break the "no curriculum in templates" rule).

---

## Steps

### M1 — Foundation (design system + homepage) ← building now
- `css/marketing.css` — hero, sections, cards, buttons, pricing, FAQ, footer components.
- `front-page.php` — full homepage: hero, trust strip, how-it-works, subjects, features,
  pricing preview, FAQ, final CTA, footer.
- Enqueue `marketing.css` on the front page and marketing pages.
- **Done when:** homepage renders a real landing page (not the default blog list) and is
  responsive on mobile + desktop.

### M2 — How It Works page
- `page-how-it-works.php` — the Learn → Practice → Tutor → Test → Mistakes → Revision → Mastery
  loop explained with steps/icons.

### M3 — Subjects + SSC/HSC English landing pages
- `page-subjects.php` — subject overview cards.
- `page-ssc-english.php`, `page-hsc-english.php` — conversion-focused landing pages
  (what's covered, sample lesson CTA, pricing CTA).

### M4 — Pricing page
- `page-pricing.php` — Free / Per-lesson / Monthly tiers with feature comparison + FAQ.

### M5 — FAQ + Contact + Support
- `page-faq.php` — accordion FAQ.
- `page-contact.php` — contact info + simple form (mailto or plugin form later).

### M6 — Legal + nav/footer wiring + QA
- `page-privacy.php`, `page-terms.php` (stubs, editable in admin).
- Register a primary nav menu with the public links; site-wide footer.
- Idempotent provisioning of the marketing pages (so a fresh clone has them).
- Responsive/accessibility QA; verify HTTP 200 + no PHP errors.

---

## Status
- [x] M1 — Foundation + homepage
- [ ] M2 — How It Works
- [ ] M3 — Subjects + SSC/HSC landing
- [ ] M4 — Pricing
- [ ] M5 — FAQ + Contact
- [ ] M6 — Legal + nav/footer + QA
