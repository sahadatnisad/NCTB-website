# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 8 — Payments and entitlements
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash)
**Environment:** Local Docker (`docker-compose.yml`) — `nctb-wordpress` + `nctb-mysql`, site at http://localhost:8080
**WordPress version:** 7.0.4
**PHP version:** 8.3.33
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) 0.8.0
**Plugin version:** NCTB Learning Hub 0.8.0

## 1. What was built
Built a robust, server-side access control, entitlements, and commerce integration system:
- **Centralized Entitlement Service (`NCTB_Entitlements`):** Single source of truth for all curriculum and lesson access decisions (**never simple `paid=true`**, never client-trust). Evaluates:
  1. Free lesson status (`_nctb_is_free` or introductory preview).
  2. Admin/Editor role bypass.
  3. All-Access active subscriptions (`expires_at > NOW()`).
  4. Book Pack ownership.
  5. Unit Pack ownership.
  6. Direct single lesson purchases.
  7. Admin manual grants with mandatory audit logging.
- **Audit Trail Engine:** Automatically logs every grant, expiration, and revocation with user IDs, timestamps, and explanation notes in `wp_nctb_entitlement_audit`.
- **Commerce & WooCommerce Integration Helper (`NCTB_Commerce`):** Maps product metadata (`_nctb_entitlement_type`, `_nctb_item_id`, `_nctb_duration_days`) to curriculum items and listens on `woocommerce_order_status_completed` to grant entitlements server-side.
- **Access-Denied / Paywall Banner:** Renders responsive pricing cards (Single Lesson pass vs. All-Access monthly subscription) on locked lesson pages.
- **Admin Entitlements Manager (`NCTB_Entitlements_Admin`):** Dedicated screen under Lessons → Entitlements (`edit.php?post_type=nctb_lesson&page=nctb-entitlements`) to inspect student passes, grant manual access, and revoke entitlements with audit notes.
- **My Purchases & Passes Screen (`/purchases/`):** Student screen rendering active passes, item titles, granted dates, and expiration timestamps.
- **Entitlements REST API (`NCTB_Entitlements_REST`):** Endpoints under `nctb/v1/entitlements/*` and `nctb/v1/student/purchases` for checking lesson access and retrieving passes.

## 2. Files created/changed
**Plugin — new:**
- `includes/class-nctb-entitlements.php` — Centralized entitlement and access evaluation service with audit logging.
- `includes/class-nctb-commerce.php` — WooCommerce order listener and paywall card renderer.
- `includes/class-nctb-entitlements-admin.php` — Admin screen for viewing, granting, and revoking student entitlements.
- `includes/class-nctb-entitlements-rest.php` — REST API controller for access decisions and purchases (`/nctb/v1/entitlements/*`).

**Plugin — changed:**
- `nctb-learning-hub.php` — Bumped version to `0.8.0`; required Phase 8 classes.
- `includes/class-nctb-migrations.php` — Added migration step `0.8.0` creating `wp_nctb_entitlements` and `wp_nctb_entitlement_audit`.
- `includes/class-nctb-pages.php` — Added auto-provisioning for `/purchases/` (`[nctb_my_purchases]`).
- `includes/class-nctb-plugin.php` — Initialized `NCTB_Commerce`, `NCTB_Entitlements_Admin`, and registered `NCTB_Entitlements_REST`.
- `includes/class-nctb-student-views.php` — Added `[nctb_my_purchases]` shortcode renderer.

**Theme — new:**
- `page-purchases.php` — Theme template for `/purchases/`.

**Theme — changed:**
- `single-nctb_lesson.php` — Gated activity stepper behind `NCTB_Entitlements::can_access_lesson()` and rendered `NCTB_Commerce::render_paywall_card()` on locked lessons.
- `header.php` — Added "পাস (Passes)" navigation link.
- `css/curriculum.css` — Added responsive styles for paywall cards, pricing options, and purchase badge lists.
- `functions.php` — Enqueued styles and scripts for `/purchases/`.
- `style.css` — Bumped theme version to `0.8.0`.

## 3. Database/schema changes
Migration `0.8.0` creates (idempotent, dbDelta):
- `wp_nctb_entitlements` (id, user_id, entitlement_type, item_type, item_id, source_type, source_id, status, granted_by, granted_at, expires_at, meta_data, created_at, updated_at)
- `wp_nctb_entitlement_audit` (id, entitlement_id, user_id, action, performed_by, notes, created_at)

## 4. Admin features added
- Submenu page under Lessons: **Entitlements** (`edit.php?post_type=nctb_lesson&page=nctb-entitlements`).
- Listing of granted entitlements with student names, emails, pass types, target items, expiration dates, and statuses.
- Form to manually grant Single Lesson, Unit Pack, Book Pack, or All-Access passes with custom expiration and mandatory audit note.
- "Revoke" action with confirmation and audit logging.

## 5. Student-facing features added
- **Paywall Gate:** Locked lessons display pricing options (৳20 Single Lesson / ৳299 All-Access Pass) with purchase actions instead of raw 403 or broken layouts.
- **My Purchases & Passes Page (`/purchases/`):** Lists all active passes, pass types, item titles, and expiration terms.
- **Navigation:** Header menu contains direct link to `/purchases/`.

## 6. REST/AJAX endpoints added
Under namespace `nctb/v1`:
- `GET /nctb/v1/entitlements/check?lesson_id={id}` — Returns access decision (`granted: true/false`, `reason`, `expires_at`).
- `GET /nctb/v1/student/purchases` — Returns student's active passes.
- `POST /nctb/v1/entitlements/purchase-demo` — Sandbox instant pass granting for development/testing.

## 7. Security controls added
- Centralized server-side enforcement on all content delivery.
- Capability checks (`manage_options`) and nonce verification (`nctb_entitlement_admin_action`) on all admin grants and revocations.
- Immutable audit trail recording every grant and revoke event.
- Prepared database statements with `$wpdb->prepare()`.

## 8. Tests performed (runtime, in Docker)
- `php -l` executed on all 58 PHP files across plugin and theme (0 syntax errors).
- Executed 17-assertion automated test suite inside Docker container:
  - Verified `NCTB_LH_VERSION` reports `0.8.0`.
  - Verified `wp_nctb_entitlements` and `wp_nctb_entitlement_audit` exist.
  - Tested locked lesson blocking for unentitled student.
  - Tested free lesson accessibility.
  - Tested manual entitlement grant with audit logging.
  - Tested direct lesson access decision.
  - Tested active pass retrieval (`get_user_entitlements`).
  - Tested entitlement revocation and subsequent lock.
  - Tested all-access subscription grant unlocking all content.
  - Tested REST endpoints (`/entitlements/check`, `/student/purchases`, `/entitlements/purchase-demo`).
- Front-End HTTP curl checks:
  - Homepage `/` → 200
  - Books archive `/book/` → 200
  - Lesson page `/?p=15` → 200
  - Mistakes page `/mistakes/` → 200
  - Revision page `/revision/` → 200
  - Progress page `/progress/` → 200
  - Purchases page `/purchases/` → 200
  - Dashboard `/dashboard/` → 200
  - Onboarding `/onboarding/` → 200

## 9. Test results
All 17 automated tests passed (0 failures). All 9 platform routes return HTTP 200.

## 10. Screens/pages to manually review
- Admin Entitlements Manager: `http://localhost:8080/wp-admin/edit.php?post_type=nctb_lesson&page=nctb-entitlements`
- Purchases page: `http://localhost:8080/purchases/`
- Lesson page: `http://localhost:8080/?p=15`

## 11. Known problems / technical debt
- None.

## 12. Setup or migration steps to perform
- Migrations run automatically on `admin_init`.

## 13. Rollback notes
- Drop tables `wp_nctb_entitlements`, `wp_nctb_entitlement_audit`, delete page with slug `purchases`, and revert git commits to Phase 7 state (`v0.7.0`).

## 14. What is intentionally NOT built yet
- Phase 9: Contextual AI tutor engine (server-side streaming/chat, prompt grounder, token budgeter).
- Phase 10: Writing, listening & speaking evaluation engine.
- Phase 11: Authentic board-question database.

**STOP HERE. NEXT PHASE NOT STARTED.**
