# NCTB AI Learning Hub — Publishing, Requirements, Risks & Costs

> **Purpose.** How to actually take the site live on WordPress, what you need to have in place, the risks to watch, and how to think about money.
>
> **Honesty note on money:** I have **not** invented prices. Bangladesh hosting, domain, gateway fees, and AI API costs change constantly and depend on your provider and scale. Every figure below is a **category to get a real quote for**, not a number to trust. I'll tell you *what* to budget for and *how* to estimate it — you fill in verified numbers.

---

## 1. Publishing procedure (dev → live)

You're developing in **WordPress + Docker on Ubuntu**. Going live means moving from that local Docker setup to a real host.

### 1.1 Environments
Run **three**:
- **Local (Docker on your Ubuntu):** where Antigravity builds. Already set up.
- **Staging (on the real host, hidden):** a copy of production for final testing before anything goes public. Non-negotiable for a paid product.
- **Production (live site):** what users see.

Never test on production. Never let an AI agent build directly on production.

### 1.2 What ships to the host
Per `01_BUILD_BLUEPRINT.md` §5.2: ship **your code only** — the `nctb-learning-hub` plugin and `nctb-child-theme`. WordPress core, WooCommerce, and third-party plugins are installed on the host, not carried in your repo. (Fixing the "whole WordPress core committed to git" issue makes this clean.)

### 1.3 Step-by-step first publish
1. **Get hosting + domain** (see requirements §2). Confirm PHP 8.x + MySQL versions match your Docker.
2. **Install WordPress** fresh on the host (or via host's installer).
3. **Install WooCommerce** + required plugins.
4. **Deploy your plugin + theme** (git pull on host, or CI, or SFTP for a first pass).
5. **Run migrations** (`NCTB_Migrations`) to create the 20+ custom tables.
6. **Import content** (WP-CLI importer from `03_CONTENT_OPERATIONS.md`).
7. **Configure secrets** server-side (AI keys, gateway keys) — in `wp-config.php`/`secrets.php`, **never** in git.
8. **Set up SSL/HTTPS** (Let's Encrypt — usually free with the host).
9. **Configure caching + CDN.**
10. **Connect payment gateways** in live mode; do a real small test transaction.
11. **Set up backups** (automated daily DB + files) and test a restore.
12. **SEO basics:** sitemap, robots, OpenGraph, analytics.
13. **Test everything on staging first**, then repeat on production.
14. **Go live**; monitor logs and error rates closely for the first days.

### 1.4 Ongoing deploy workflow
- Build on local → test → push to git → deploy to **staging** → verify → deploy to **production** during low-traffic hours → back up first, always.
- Keep the "one phase, then human review" discipline even for deploys.

---

## 2. Requirements checklist (what you must have)

### 2.1 Technical
- **Domain name** (a `.com` and consider a local `.com.bd`; verify `.com.bd` registration requirements — they can need documents).
- **Hosting** with PHP 8.x, MySQL/MariaDB, adequate RAM/CPU, and — critically — **enough headroom for caching and concurrent users**. Cheap shared hosting may choke under real load; confirm limits. A managed WordPress host or a small VPS may be needed as you grow.
- **SSL certificate** (usually free via Let's Encrypt).
- **CDN** (e.g. a free-tier CDN) for static assets + low-bandwidth users.
- **Transactional email** service (order receipts, password resets) — a proper provider, not raw PHP mail (which lands in spam). Verify deliverability to Bangladeshi inboxes.
- **AI provider account** (Claude/OpenAI) with billing + usage limits set.
- **Backups** (host-level + your own off-host copy).
- **Monitoring** (uptime + error logging).

### 2.2 Payments
- **Merchant accounts / integrations** for **bKash, Nagad, and/or SSLCommerz** (SSLCommerz aggregates cards + mobile wallets — often the simplest single integration; verify current offerings and fees yourself).
- Each gateway has **onboarding requirements** (business documents, bank account, possibly trade license). Start this early — approval can take time.
- Confirm each gateway's **WooCommerce plugin** exists and is maintained (verify current status directly).

### 2.3 Legal / compliance (verify with a local professional)
- **Business registration / trade license** — likely needed for merchant accounts and to operate commercially. Confirm what's required in Bangladesh.
- **NCTB content rights** — the big one; see `03_CONTENT_OPERATIONS.md` §1. Confirm you can build a paid product aligned to (ideally not copying) NCTB material.
- **Privacy policy & terms** — real ones, especially since you handle minors' data. Confirm obligations for collecting data from students under 18.
- **Payment/consumer regulations** — refund policy, transaction records.
- **Tax** — VAT/registration obligations for digital services; confirm locally.
- I am not a lawyer and this is not legal advice — treat these as items to verify with a Bangladeshi professional. A little legal cost now protects a lifetime project.

### 2.4 Content
- A **content backlog** (curriculum map) for launch subjects.
- The **importer + validator** built.
- Launch content authored, reviewed, and imported (English first; ICT next).

### 2.5 People
- Content authors (NCTB experts) — your real bottleneck.
- Someone to run the AI builder + do deploys/ops.
- Someone for support + community/marketing (can be you early on).

---

## 3. Risk register

| # | Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|---|
| 1 | **NCTB content rights** — you can't legally paywall copied textbook content | Unknown until verified | High | Author *original* content aligned to NCTB, not copies; get legal opinion; mark board-question provenance. |
| 2 | **AI cost > revenue** — API bills exceed what users pay | Medium | High | AI is paid-only; hard quotas; never call AI for deterministic tasks; cache; monitor cost/user from launch. |
| 3 | **Content can't scale** — too few experts, slow production | High | High | Build importer + validator; hire/partner content authors; expand one subject at a time gated on usage. |
| 4 | **Build quality unverified** — AI-built phases claimed done but untested | Medium | High | Phase 14 verification pass; human clicks every feature; security audit before taking money. |
| 5 | **Low willingness to pay** — students/parents won't pay enough | Unknown | High | Test price early (small pilots); strong free tier for reach; keep costs low so low ARPU still works. |
| 6 | **Payment gateway friction** — onboarding delays, failed transactions | Medium | Medium | Start gateway onboarding early; use SSLCommerz as a simpler aggregator; test real transactions. |
| 7 | **Performance on weak devices/networks** | Medium | High | Low-bandwidth design system; caching/CDN; YouTube facades; performance budget enforced. |
| 8 | **Security breach / student data leak** | Low-Med | Very High | Phase 14 hardening; least-privilege; encrypt secrets; backups; privacy by default. |
| 9 | **Founder overload / scope creep** — building everything at once | High | High | Strict phase discipline; a written "NOT building this year" list; one subject/audience at a time. |
| 10 | **Video piracy** (unlisted links shared) | Medium | Low-Med | Accept as tolerable at this budget; strong platform value > raw video; consider protection only later. |
| 11 | **Dependence on one AI provider** | Low | Medium | Adapter is provider-swappable (already built); keep mock/fallback. |
| 12 | **Funding never arrives** | Medium | Medium | Don't depend on it; design to be self-sustaining on subscription revenue; funding as upside, not lifeline. |
| 13 | **Key-person risk** (project lives in your head / one repo) | Medium | High | Document everything (these blueprints help); back up repo + DB; bring in a second trusted person over time. |

---

## 4. Cost structure (categories to get real quotes for — no invented numbers)

Think in **one-time setup**, **recurring fixed**, and **variable (scales with users)**.

### 4.1 One-time / setup
- Domain registration (annual, but first-time setup).
- Logo/brand design (can be modest or DIY early).
- Legal review (content rights, terms/privacy, business setup) — worth paying for once.
- Business registration / trade license fees.
- Initial content production (the big one — expert time to author launch subjects).

### 4.2 Recurring fixed (monthly/annual)
- Hosting (shared → VPS → managed as you grow).
- Domain renewal.
- Transactional email service.
- CDN (may be free tier initially).
- Backup storage.
- Monitoring/uptime tools (often free tier).
- Ongoing content production (salaries/fees for authors — likely your largest ongoing cost).

### 4.3 Variable (scales with usage) — watch these
- **AI API tokens** — scales with paid AI users. *Model this before loosening quotas.* Roughly: (avg tokens per interaction) × (interactions per user) × (paid users) × (provider price per token). Get the provider's current price and measure real usage in beta; do **not** trust any estimate, including a guessed one.
- **Payment gateway fees** — a % per transaction; get each provider's current rate.
- **Bandwidth/scaling** — as traffic grows, hosting tier rises.
- **SMS** (if you add reminders) — per-message cost in Bangladesh; email is far cheaper, prefer it.

### 4.4 How to build your real budget
1. List each item above.
2. Get a **real quote** for each (host, gateway, AI provider, legal).
3. Measure **AI cost per active user** and **conversion/price** in a small paid beta.
4. Only then project. A budget built on measured betas beats one built on guesses every time.

### 4.5 The one financial rule that keeps you alive
**Revenue per paying user must comfortably exceed variable cost per user (mainly AI + gateway fees).** If it doesn't, growth makes you *lose* money faster. Because AI is paid-only in your model, you're structurally in good shape — just keep measuring it.

---

## 5. Pre-launch go/no-go checklist

Don't take real money until all of these are true:
- [ ] Phase 14 security + verification pass complete (human-verified, not AI-claimed).
- [ ] Real test transaction succeeds via a live gateway.
- [ ] Backups running and a restore tested.
- [ ] Privacy policy + terms live; minors'-data obligations confirmed.
- [ ] NCTB content-rights position confirmed (or content is original/aligned).
- [ ] Performance acceptable on a real low-end Android over 3G.
- [ ] Enough quality free + launch paid content imported.
- [ ] AI cost-per-user roughly understood from beta.
- [ ] Support channel ready (email/Facebook) to answer users fast.

---

### Bottom line
Publishing is not just "upload the site" — it's hosting + payments + legal + backups + content + verification, done on staging first. Get real quotes, verify the legal position, measure AI cost in a beta, and never take money before the go/no-go list is green. Do that, and you have a durable, low-risk foundation for a lifetime project.
