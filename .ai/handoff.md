# Handoff log

Shared work log for every agent (Claude Code, Codex, Cursor, …) and human working on RRCONS.
**Newest entry first.** Read it at the start of a session; prepend an entry before you stop.
Template and rules: `.claude/skills/rrcons-handoff/SKILL.md` (plain Markdown — usable by any agent).

---

## 2026-10-11 — Claude Code — full project audit + agent handoff docs
- **Branch / HEAD:** `solveit-staging` (based on `solveit/main` 01fccf6); same commits cherry-picked onto `staging` for `origin` (based on `origin/main` 05624f3).
- **Goal:** audit the whole project (flows, logic, relationships, conditions) and create handoff docs compatible with Claude Code, Codex and other agents.
- **Done:**
  - `AGENTS.md` — canonical guide (stack, commands, dual-remote git workflow, architecture, role matrix, data model, flows, frontend, testing, deployment, known issues, rules).
  - `CLAUDE.md` — imports `AGENTS.md` + Claude Code specifics.
  - `.claude/settings.json` (permissions), `.claude/skills/{rrcons-handoff,rrcons-verify,rrcons-bilingual-content,rrcons-release}`, `.claude/agents/rrcons-reviewer.md`.
  - `.ai/README.md`, `.ai/guidelines/*.md` (Laravel Boost custom-guideline format), this log.
  - `.gitignore`: `.claude/settings.local.json`, `CLAUDE.local.md`.
- **In progress (uncommitted):** none.
- **Next steps / open backlog (none started — ask the user before picking one up):**
  1. Fix `ConfigSeeder` import (`App\Models\Blog\Config`) or remove the seeder.
  2. Decide on comment moderation / admin comment list.
  3. Optional: keep old article URLs working when the Indonesian title (slug) changes (redirect table or stop regenerating slug).
  4. Align hero image `mimes` (add `webp` to RR Future / Service / Contact heroes).
  5. Remove dead code (`Front\ArticleController`, `Front\CategoryController`), unused `public/front/css/tailwind.css`, unused Vite entries — only on request.
  6. Laravel Boost install requires framework ≥ 10.49 (see `.ai/README.md`); Laravel 10 itself is out of security support.
- **Verification:** `php artisan test` → 77 passed (759 assertions), 1 vendor deprecation notice (PHP 8.5). Documentation-only change; no runtime code touched.
- **Gotchas / decisions:**
  - `origin` and `solveit` have unrelated histories; only `README.md` differs between them (intentional).
  - Local `.env` has `APP_ENV=production`; never run destructive DB commands.
  - Laravel Boost was **not** installed (needs a framework bump; dependency changes need user approval).
