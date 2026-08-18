# NCTB Learning Hub — Coding Standards

These standards apply to every phase. They restate the project rules from
`NCTB_WORDPRESS_MASTER_PLAN.md` in an actionable form.

## Architecture

- **All learning/business logic lives in the `nctb-learning-hub` plugin.** The
  theme (`nctb-child-theme`) is presentation only. Never hard-code curriculum
  content into templates.
- Keep modules loosely coupled. Register new services through
  `NCTB_Plugin::run()` / the `nctb_lh_loaded` action, not the main plugin file.
- Centralize cross-cutting concerns behind a single service each:
  entitlements, AI calls, mastery calculation, and question marking.
- Do not add a third-party dependency or plugin without documenting why a small
  amount of maintainable custom code will not do.

## WordPress conventions

- Follow the WordPress Coding Standards (WPCS). Run `composer lint` before
  committing (config: `wp-content/plugins/nctb-learning-hub/phpcs.xml.dist`).
- Use hooks/actions/filters. **Never modify WordPress core files.**
- Use capabilities and roles for authorization; use nonces on state-changing
  requests.
- **Every REST endpoint must declare a correct `permission_callback`.**
- Sanitize all untrusted input; escape all output; use `$wpdb->prepare()` or
  safe WP APIs for queries.
- Version every custom-table schema change through `NCTB_Migrations` — never
  alter production tables by hand.

## Security & privacy

- Never place API keys/secrets in browser JavaScript or committed source.
  Secrets go in `wp-config.php`, environment variables, or the git-ignored
  `config/secrets.php` (see `docs/SECRETS.md`).
- AI is called **server-side only**, through one provider adapter.
- Collect only necessary student data. Keep student writing/speaking private by
  default. Never expose one student's data to another. Do not log raw secrets
  or unnecessary AI conversation content.

## Internationalization

- Wrap user-facing strings in translation functions with the correct text
  domain (`nctb-learning-hub` for the plugin, `nctb-theme` for the theme).
- Support Bangla via UTF-8; mark Bangla content with `lang="bn"`.

## Performance (mobile-first)

- Assume Android phones on mobile data. Load assets only on pages that use
  them. Lazy-load media, paginate large datasets, avoid large autoloaded
  options, and never call the AI for routine MCQ marking or stored explanations.

## Definition of done (per feature)

Goal, user story, data needs, endpoints/actions, UI behavior, validation,
permissions, error states, mobile behavior, tests, and a passing lint run.
