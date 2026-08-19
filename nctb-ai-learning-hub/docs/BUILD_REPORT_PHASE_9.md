# NCTB LEARNING HUB — BUILD REPORT

**Phase completed:** Phase 9 — Contextual AI tutor
**Date:** 2026-08-19
**Built by:** Antigravity (Gemini 3.7 Flash)
**Environment:** Local Docker (`docker-compose.yml`) — `nctb-wordpress` + `nctb-mysql`, site at http://localhost:8080
**WordPress version:** 7.0.4
**PHP version:** 8.3.33
**Theme:** NCTB Learning Hub Theme (`nctb-child-theme`) 0.9.0
**Plugin version:** NCTB Learning Hub 0.9.0

## 1. What was built
Built a lesson-anchored, contextual AI tutor engine with strict pedagogical guardrails and server-side model execution:
- **Server-Side AI Provider Adapter (`NCTB_AI_Adapter`):** Model-agnostic adapter supporting Anthropic Claude, OpenAI, and intelligent curriculum-grounded fallback for local environments without external keys. **Credentials are never exposed to the browser.**
- **Context Builder & Prompt Grounder (`NCTB_AI_Context_Builder`):** Gathers student level (SSC/HSC), preferred language (Bangla/English), active lesson title, unit, book, learning outcomes, key vocabulary, current activity step text, and mistake attempt context into a compact, grounded prompt.
- **Socratic Scaffolding Guardrails:** Strict prompt rules that prohibit giving away direct quiz answers, guide the student with clues and analogies, and forbid fabricating authentic board exam questions.
- **AI Quota & Usage Tracker (`NCTB_AI_Usage`):** Daily request and token accounting per student (50 requests/day for standard student, 200/day for subscribed) stored in `wp_nctb_ai_usage` and `wp_nctb_ai_conversations`.
- **Action Types Supported:**
  - `explain` — Explains the current concept in simple terms.
  - `bangla` — Explains in natural Bengali with English key terms highlighted in bold.
  - `hint` — Socratic clue without spoiling the answer.
  - `example` — Provides a realistic sentence example for the concept.
  - `why_wrong` — Analyzes the student's recent incorrect attempt and explains the thinking error.
  - `free_chat` — Open questions grounded in current lesson text.
- **Slide-Out AI Tutor Drawer UI:** Built responsive drawer in `single-nctb_lesson.php` with quick action chips, message bubbles, typing indicators, daily quota counter (`⚡ 50 left`), and instant question form.
- **REST Controller (`NCTB_AI_REST`):** Endpoints under `/nctb/v1/tutor/*` (`POST /ask`, `GET /history`, `GET /quota`).

## 2. Files created/changed
**Plugin — new:**
- `includes/class-nctb-ai-adapter.php` — Server-side AI provider adapter with Anthropic/OpenAI/Mock backends.
- `includes/class-nctb-ai-context-builder.php` — Lesson, concept, and mistake context assembly with Socratic guardrails.
- `includes/class-nctb-ai-usage.php` — Daily quota enforcement and conversation logger.
- `includes/class-nctb-ai-tutor.php` — Central AI tutor orchestrator.
- `includes/class-nctb-ai-rest.php` — REST API controller for AI interactions (`/nctb/v1/tutor/*`).

**Plugin — changed:**
- `nctb-learning-hub.php` — Bumped version to `0.9.0`; required Phase 9 classes.
- `includes/class-nctb-migrations.php` — Added migration step `0.9.0` creating `wp_nctb_ai_conversations` and `wp_nctb_ai_usage`.
- `includes/class-nctb-plugin.php` — Registered `NCTB_AI_REST` routes.

**Theme — changed:**
- `single-nctb_lesson.php` — Added interactive AI Tutor trigger bar and slide-out drawer markup.
- `js/lesson-interactive.js` — Added client-side drawer toggle, quick chip actions, fetch requests, stream rendering, and quota updates.
- `css/curriculum.css` — Added responsive drawer, chat bubbles, action chips, and overlay styling.
- `style.css` — Bumped theme version to `0.9.0`.

## 3. Database/schema changes
Migration `0.9.0` creates (idempotent, dbDelta):
- `wp_nctb_ai_conversations` (id, user_id, lesson_id, action_type, user_prompt, ai_response, provider, tokens_used, created_at)
- `wp_nctb_ai_usage` (id, user_id, usage_date, request_count, prompt_tokens, completion_tokens, last_request_at)

## 4. Admin features added
- Server-side configuration via `NCTB_AI_PROVIDER` and `NCTB_AI_API_KEY` in `secrets.php` / `wp-config.php`.

## 5. Student-facing features added
- **AI Tutor Drawer:** Slide-out drawer accessible on every interactive lesson step.
- **5 Quick Action Chips:** One-tap `💡 Explain Step`, `🇧🇩 বাংলায় ব্যাখ্যা`, `🔍 Give a Hint`, `📝 Sentence Example`, `❓ Why was I wrong?`.
- **Live Markdown Chat:** Real-time chat bubbles with simple markdown formatting (bold, italics, lists, line breaks).
- **Daily Quota Counter:** Visual indicator showing remaining interactions today.

## 6. REST/AJAX endpoints added
Under namespace `nctb/v1`:
- `POST /nctb/v1/tutor/ask` — Processes student query with context grounding and returns AI reply.
- `GET /nctb/v1/tutor/history?lesson_id={id}` — Returns conversation history for current lesson.
- `GET /nctb/v1/tutor/quota` — Returns remaining daily token quota.

## 7. Security controls added
- API keys reside strictly server-side; zero credentials in browser JavaScript.
- Socratic scaffolding guardrails in system prompts.
- Input sanitization (`sanitize_textarea_field`, `sanitize_key`, `wp_kses_post`).
- Student isolation on conversation history and usage logs.
- Daily quota rate limiting to prevent abuse and API cost spikes.

## 8. Tests performed (runtime, in Docker)
- `php -l` executed on all 71 PHP files across plugin and theme (0 syntax errors).
- Executed 19-assertion automated test suite inside Docker container:
  - Verified `NCTB_LH_VERSION` reports `0.9.0`.
  - Verified `wp_nctb_ai_conversations` and `wp_nctb_ai_usage` exist.
  - Tested grounded system prompt assembly with lesson titles, student level, and guardrails.
  - Tested Socratic `explain`, `bangla`, `hint`, `example`, and `why_wrong` action handlers.
  - Tested daily quota decrement and token accounting.
  - Tested quota exhaustion lock with student-friendly message.
  - Tested REST endpoints (`/tutor/ask`, `/tutor/history`, `/tutor/quota`).
- Front-End HTTP curl checks on all 9 routes (all returning 200).

## 9. Sample Tutor Interactions
- **Action: Explain Step (`explain`)**
  > **AI:** 💡 **সহজ ব্যাখ্যা (Explanation):** এই পাঠ্যাংশে নেলসন ম্যান্ডেলার দক্ষিণ আফ্রিকায় বর্ণবাদবিরোধী ঐতিহাসিক সংগ্রাম তুলে ধরা হয়েছে। তিনি ১৯৯৩ সালে নোবেল শান্তি পুরস্কার লাভ করেন এবং ১৯৯৪ সালে দেশটির প্রথম কৃষ্ণাঙ্গ প্রেসিডেন্ট নির্বাচিত হন। *Key takeaway:* He guided South Africa from apartheid to a multi-racial democracy.
- **Action: Bangla Translation & Terms (`bangla`)**
  > **AI:** 🇧🇩 **বাংলা অর্থ ও তাৎপর্য:**
  > - **Apartheid (বর্ণবাদ):** জাতিগত বৈষম্য নীতি।
  > - **Emancipation (মুক্তি):** রাজনৈতিক বা সামাজিক বন্ধন থেকে মুক্তি।
  > - **Shackle (শৃঙ্খল):** দাসত্ব বা সীমাবদ্ধতার প্রতীক।
- **Action: Socratic Hint (`hint`)**
  > **AI:** 🔍 **চিন্তার সূত্র (Guided Clue):** সরাসরি উত্তর না দিয়ে একটি ক্লু দিচ্ছি: পাঠ্যাংশের দ্বিতীয় প্যারাগ্রাফে নজর দিন যেখানে তিনি রোবেন দ্বীপে বন্দিত্বের সময়কাল এবং ১৯৯৩ সালের নোবেল শান্তি পুরস্কারের কথা উল্লেখ করেছেন। সংখ্যাটি ২৫ থেকে ৩০-এর মধ্যে।
- **Action: Why Was I Wrong (`why_wrong`)**
  > **AI:** ❓ **কেন এটি ভুল ছিল (Error Analysis):** আপনি সম্ভবত প্রেসিডেন্ট নির্বাচিত হওয়ার সাল (১৯৯৪) এবং নোবেল শান্তি পুরস্কার লাভের সাল (১৯৯৩)-এর মধ্যে বিভ্রান্ত হয়েছিলেন। মনে রাখবেন: তিনি নোবেল পান ১৯৯৩ সালে এবং প্রেসিডেন্ট হন পরের বছর ১৯৯৪ সালে।

## 10. Screens/pages to manually review
- Interactive Lesson with AI Tutor Drawer: `http://localhost:8080/?p=15`

## 11. Known problems / technical debt
- None.

## 12. Setup or migration steps to perform
- Migrations run automatically on `admin_init`.

## 13. Rollback notes
- Drop tables `wp_nctb_ai_conversations`, `wp_nctb_ai_usage` and revert git commits to Phase 8 state (`v0.8.0`).

## 14. What is intentionally NOT built yet
- Phase 10: Writing, listening & speaking evaluation engine.
- Phase 11: Authentic board-question database.
- Phase 12: Board pattern analytics.

**STOP HERE. NEXT PHASE NOT STARTED.**
