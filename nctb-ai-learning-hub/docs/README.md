# NCTB AI Learning Hub — Developer Documentation

Lesson-by-lesson digital companion to the Bangladesh NCTB curriculum. First
product: **SSC + HSC English** with a contextual AI tutor. The architecture is
built to support ICT, Bangla, Mathematics and Science later without a rebuild.

The authoritative blueprint is `../NCTB_WORDPRESS_MASTER_PLAN.md`. Build **one
phase at a time** and produce a Build Report after each phase before starting
the next.

## Repository layout

```
nctb-ai-learning-hub/          WordPress root (core files)
├── wp-content/
│   ├── plugins/
│   │   └── nctb-learning-hub/  ← all learning/business logic
│   │       ├── nctb-learning-hub.php   main plugin file
│   │       ├── uninstall.php
│   │       ├── includes/       plugin core (loader, migrations, logger, lifecycle)
│   │       ├── admin/          admin placeholder (grows in Phase 3+)
│   │       ├── public/         front-end placeholder (grows in Phase 1+)
│   │       ├── config/         secrets.sample.php (secrets.php is git-ignored)
│   │       └── languages/      translations
│   └── themes/
│       └── nctb-child-theme/   ← presentation only (mobile-first, EN/BN)
└── docs/                       this folder
```

## Documentation index

- `CODING_STANDARDS.md` — architecture, WordPress, security and performance rules.
- `ENVIRONMENT.md` — dev/staging/production separation and debug logging.
- `SECRETS.md` — API-key and credential handling.
- `BACKUP_RESTORE.md` — database/file backup and restore procedure.
- `PHASE_STATUS.md` — which phases are done, in progress, or not started.

## Local setup (summary)

1. Install WordPress (core is already present in this repo).
2. Copy `wp-config-sample.php` to `wp-config.php`, set DB credentials, add the
   development debug constants from `ENVIRONMENT.md`.
3. Activate the **NCTB Learning Hub Theme** and the **NCTB Learning Hub** plugin.
4. (Later phases) `cd wp-content/plugins/nctb-learning-hub && composer install`
   for lint tooling, then `composer lint`.

## Golden rules

- Learning logic lives in the plugin, presentation in the theme.
- Never commit `wp-config.php` or `config/secrets.php`.
- Back up the database before any migration.
- Do not build features from later phases early.
