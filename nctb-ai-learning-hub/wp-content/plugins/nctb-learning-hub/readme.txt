=== NCTB Learning Hub ===
Contributors: nctbaihub
Tags: education, lms, nctb, curriculum, ai-tutor
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Core learning and business logic for the NCTB AI Learning Hub: a lesson-by-lesson
digital companion to the Bangladesh NCTB curriculum.

== Description ==

This plugin holds nearly all learning/business logic for the platform:
curriculum hierarchy, lesson engine, practice/question engine, attempts,
progress, mastery, mistakes, spaced revision, entitlements, and the contextual
AI tutor. Presentation lives in the accompanying theme, not here.

Built phase-by-phase per NCTB_WORDPRESS_MASTER_PLAN.md. This is the Phase 0
foundation: an activatable skeleton with a versioned migration runner and
extension points. No curriculum, practice, payments or AI features are shipped
yet.

== Changelog ==

= 0.1.0 =
* Phase 0: plugin skeleton, activation/deactivation lifecycle, versioned
  migration runner foundation, debug logger, admin/public placeholders,
  secrets handling pattern.
