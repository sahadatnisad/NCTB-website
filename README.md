# 🇧🇩 NCTB AI Learning Hub

> A lesson-by-lesson digital companion to the Bangladesh **NCTB curriculum**, where students learn the school lesson, practise it, receive contextual AI help, review mistakes, take assessments, and prepare for board exams from home.
>
> **First Product:** SSC + HSC **English** — an interactive NCTB lesson book with a personal AI English tutor. Later scalable to ICT, Bangla, Mathematics, Science, and other NCTB subjects.

[![WordPress](https://img.shields.io/badge/WordPress-7.0.4-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.3%20%7C%208.0%2B-777bb4.svg)](https://php.net)
[![Build Status](https://img.shields.io/badge/Phase%200--24-Completed%20%E2%9C%85-success.svg)](./docs/plans/MASTER_ROADMAP_CHECKLIST.md)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## 📂 Documentation & Master Plans

All detailed architectural blueprints, design systems, and versioned build reports live in the **[`docs/`](./docs)** directory:

- 🎯 **[Master Roadmap Checklist](./docs/plans/MASTER_ROADMAP_CHECKLIST.md)** — Complete status matrix and local setup guide for Phases 0–24+.
- 📘 **[Build Blueprint (Technical Specification)](./docs/plans/01_BUILD_BLUEPRINT.md)** — Core platform architecture, curriculum CMS, question engines, AI tutoring, and Shikkhok Hub.
- 🎨 **[Design System](./docs/plans/02_DESIGN_SYSTEM.md)** — Color palettes, typography, UI components, and layout tokens.
- 📝 **[Content Operations](./docs/plans/03_CONTENT_OPERATIONS.md)** — NCTB subject schemas, lesson authoring workflows, and board questions.
- 📜 **[Build Version History Reports (Phases 0–24)](./docs/build-history/)** — Detailed build reports for every completed phase.
- 🛠️ **[Standards & Setup Guides](./docs/standards-and-setup/)** — Coding standards, environment configuration, secrets handling, and backups.

---

## ⚡ Quick Start (Local Development)

The project runs in local Docker containers (`nctb-wordpress` and `nctb-mysql`):

```bash
# 1. Clone repository
git clone https://github.com/sahadatnisad/NCTB-website.git
cd "NCTB Website"

# 2. Start Docker containers
docker-compose up -d

# 3. Open site in your browser
# URL: http://localhost:8080
```

---

## 🤖 Multi-Agent Build Protocol

This repository is built autonomously by AI agents across multiple sessions and devices. The project memory travels with the git repository:

1. **[`BUILD_STATE.md`](./BUILD_STATE.md)** — The single source of live project memory. The AI reads this **first** and updates it **last**.
2. **[`AGENTS.md`](./AGENTS.md)** / **[`CLAUDE.md`](./CLAUDE.md)** — The strict 10-step build protocol every AI must follow.
3. Build **one phase at a time** → Run automated tests in Docker → Generate Build Report → Update `BUILD_STATE.md` → Sync via `bash scripts/sync.sh` → Stop for human review.

---

## 🏗️ Architecture & Tech Stack

- **Platform:** WordPress 7.0+ (`wp-content/plugins/nctb-learning-hub` + `wp-content/themes/nctb-child-theme`)
- **Database:** MySQL with 13 custom tables managed via versioned migrations (`NCTB_Migrations`)
- **Commerce:** WooCommerce order completed listener with multi-tier access passes (`NCTB_Entitlements`)
- **AI Tutor:** Server-side adapter supporting Anthropic Claude, OpenAI, and grounded fallback (`NCTB_AI_Adapter`)
- **Exam Intelligence:** Authentic Bangladesh Education Board questions archive & statistical pattern analytics (`NCTB_Board_Service`, `NCTB_Board_Analytics_Service`)

---

## 📄 License

GPL-2.0-or-later. Designed for NCTB students and educators in Bangladesh.
