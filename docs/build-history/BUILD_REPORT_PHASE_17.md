# BUILD REPORT — PHASE 17: Modules & Video-Course System

> **Project:** NCTB AI Learning Hub  
> **Repository:** `sahadatnisad/NCTB-website`  
> **Phase:** 17 — Modules & Video-Course System  
> **Date:** 2026-08-19  
> **Completed by:** Antigravity (Gemini 3.7 Flash)  
> **Reference Plan:** `docs/plans/01_BUILD_BLUEPRINT.md` (Phase 17)

---

## 1. Executive Summary

Phase 17 delivers the **Modules & Video-Course System** (`nctb_module`), supporting video masterclasses, practical coding walkthroughs, and teacher pedagogical training modules.

In this phase, we:
1. **Registered the `nctb_module` CPT & `module_category` Taxonomy (`NCTB_Module_CPT`)**: Supported structured course metadata including target audience (`student`, `teacher`, `both`), class/level (`class_6` to `class_12`), estimated duration, instructor/channel, and video playlist items JSON.
2. **Added Schema Migration `0.17.0` (`NCTB_Migrations`)**: Created the custom `wp_nctb_module_progress` table to store per-user lecture completion checklists (`completed_items`), percentage progress, and completion states.
3. **Built the Module Service (`NCTB_Module_Service`)**: Implemented lecture retrieval, per-user progress calculation, item completion toggle logic, and seeded 2 authentic starting courses (HSC English Grammar Masterclass for students and Classroom Pedagogy Guide for teachers).
4. **Built the Module REST API Controller (`NCTB_Module_REST`)**: Created endpoints for module listing (`GET /nctb/v1/modules`), module detail (`GET /nctb/v1/modules/{id}`), and item completion toggling (`POST /nctb/v1/modules/{id}/toggle-item`).
5. **Built Course Player & Directory Views**:
   - `single-nctb_module.php`: Interactive course player with low-bandwidth YouTube facade, active lecture descriptions, and sticky playlist checklist sidebar.
   - `archive-nctb_module.php`: Courses directory with audience filter pills (`All`, `Students`, `Teachers`).
   - `theme-ui.css`: Responsive styling with progress bar indicators and checkbox states.

---

## 2. Changes Made by Component

### A. Plugin Architecture (`nctb-learning-hub`)
- **`includes/class-nctb-migrations.php`**:
  - Added Migration `0.17.0` creating `wp_nctb_module_progress`.
- **`includes/class-nctb-module-cpt.php`**:
  - Registered `nctb_module` CPT and `module_category` taxonomy with custom editor meta boxes.
- **`includes/class-nctb-module-service.php`**:
  - Implemented progress calculation, item completion toggling, and sample course seeders.
- **`includes/class-nctb-module-rest.php`**:
  - Registered `/nctb/v1/modules`, `/modules/{id}`, and `/modules/{id}/toggle-item`.
- **`nctb-learning-hub.php` & `class-nctb-plugin.php`**:
  - Wired module CPT and REST routes; bumped version to `0.17.0`.

### B. Theme Presentation (`nctb-child-theme`)
- **`single-nctb_module.php`**: Single course player template with YouTube facade and interactive playlist sidebar.
- **`archive-nctb_module.php`**: Modules directory archive with audience filter pills.
- **`css/theme-ui.css`**: Added CSS styles for course cards, progress bar fills, and sidebar playlists.

---

## 3. Definition of Done (DoD) Verification

| Requirement | Status | Evidence |
|---|---|---|
| **Course CPT & Taxonomy** | ✅ Passed | `nctb_module` and `module_category` registered in `class-nctb-module-cpt.php`. |
| **Progress Schema Table** | ✅ Passed | `wp_nctb_module_progress` created via Migration `0.17.0`. |
| **REST API Controller** | ✅ Passed | 3 REST endpoints under `nctb/v1/modules/*` enforcing user isolation. |
| **Course Player Template** | ✅ Passed | `single-nctb_module.php` integrates YouTube facade with real-time checklist persistence. |
| **Course Directory Archive** | ✅ Passed | `archive-nctb_module.php` provides tabbed filtering for Student and Teacher modules. |

---

## 4. Next Steps

- **Phase 18:** **Notes & explanations content type** (`nctb_note` CPT for graphical explanations, math formulas, English summaries, and printable classroom revision notes).
