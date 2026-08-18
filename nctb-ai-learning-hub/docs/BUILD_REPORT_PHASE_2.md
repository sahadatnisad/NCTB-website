# BUILD REPORT — PHASE 2: Student accounts and onboarding

> **Phase Status:** ✅ COMPLETE  
> **Date:** 2026-08-19  
> **Author:** Antigravity AI Pair Programmer  
> **Repository:** https://github.com/sahadatnisad/NCTB-website

---

## 🎯 Phase Goals & Scope
Phase 2 builds the core identity and onboarding foundation for students using the NCTB AI Learning Hub.

- **Role & Capabilities:** Defined dedicated `nctb_student` role with scoped capabilities.
- **Profile Data Architecture:** Implemented user metadata schema capturing education level, class session, explanation language preference, chosen subjects, and target exam sessions.
- **Resumable Multi-Step Onboarding Flow:** Built a 4-step interactive, mobile-first onboarding wizard in Bangla & English that automatically saves intermediate steps.
- **Security & Cross-Student Isolation:** Enforced strict REST API authentication, permission callbacks, nonce validation, and user ID scoping (`get_current_user_id()`), blocking any user from accessing or modifying another student's profile.

---

## 🧱 What Was Built

### 1. Plugin Core (`nctb-learning-hub`)
- **`includes/class-nctb-roles.php`:**
  - Registers the custom `nctb_student` WordPress role.
  - Grants safe capabilities: `read`, `view_nctb_content`, `edit_nctb_profile`, `submit_nctb_practice`.
  - Configures administrator overrides for testing and oversight.
- **`includes/class-nctb-student-profile.php`:**
  - Standardized user meta getter, validator, step-saver, and completion checker.
  - Supported education levels: Class 6, Class 7, Class 8, SSC (Class 9-10), HSC (Class 11-12).
  - Supported explanation languages: Bangla (`bn`), Bilingual / Easy English + Bangla (`bilingual`), English Only (`en`).
  - Supported subject selection: English 1st Paper, English 2nd Paper (Grammar & Writing), ICT, Bangla 1st & 2nd Papers.
- **`includes/class-nctb-onboarding-rest.php`:**
  - `GET /wp-json/nctb/v1/meta/options` — Public catalog of valid levels, languages, and subjects.
  - `GET /wp-json/nctb/v1/student/profile` — Authenticated student profile endpoint (blocks unauthorized users with HTTP 401).
  - `POST /wp-json/nctb/v1/student/onboarding/step` — Step-by-step validator & state persistence with WP-Nonce security.
  - `POST /wp-json/nctb/v1/student/onboarding/complete` — Onboarding completion marker and dashboard redirect provider.
- **`public/class-nctb-public.php`:**
  - Added shortcodes: `[nctb_onboarding]` and `[nctb_student_dashboard]`.
  - Configured conditional asset loading (`onboarding.js` & `onboarding.css`).
  - Configured localized script payload `nctbData` containing REST root, nonce, and profile state.
  - Implemented automatic redirection: incomplete onboarding redirects to `/onboarding`, completed students redirect to `/dashboard`.

### 2. Presentation Theme (`nctb-child-theme`)
- **`page-onboarding.php` & `page-dashboard.php`:** Dedicated WordPress page templates.
- **`header.php` & `style.css`:** Responsive navigation bar with contextual Login / Setup / Dashboard / Logout states.
- **`js/onboarding.js`:** Reactive AJAX/REST wizard supporting smooth step transitions, step validation, alerts, and instant completion transitions.
- **`css/onboarding.css`:** Mobile-first card designs, radio/checkbox option grids, stepper indicators, and Bangla typography.
- **`.htaccess`:** Configured standard WordPress URL rewrite rules.

---

## 🧪 Verification & Automated Test Results

A full automated verification test suite was executed inside the live WordPress runtime:

| Test Case | Description | Result |
|---|---|---|
| **Test 1** | `nctb_student` role registration with custom capabilities | ✅ PASS |
| **Test 2** | Student user creation and role assignment | ✅ PASS |
| **Test 3.1** | Step 1 invalid level validation rejection (`WP_Error` 422) | ✅ PASS |
| **Test 3.2** | Step 1 valid level save & advance to Step 2 | ✅ PASS |
| **Test 4.1** | Step 2 empty subjects validation rejection (`WP_Error` 422) | ✅ PASS |
| **Test 4.2** | Step 2 subject selection save & advance to Step 3 | ✅ PASS |
| **Test 5** | Step 3 explanation language preference save | ✅ PASS |
| **Test 6** | Step 4 target session save & complete onboarding flag | ✅ PASS |
| **Test 7** | Interrupted onboarding resumability verification | ✅ PASS |
| **Test 8** | REST API `/nctb/v1/meta/options` catalog response | ✅ PASS |
| **Test 9** | REST API security: unauthenticated profile access blocked (401) | ✅ PASS |

---

## 🚀 Live Endpoints
- **Onboarding Page:** `http://localhost:8080/onboarding/`
- **Dashboard Page:** `http://localhost:8080/dashboard/`
- **REST API:** `http://localhost:8080/wp-json/nctb/v1/meta/options`
