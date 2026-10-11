# Handoff log

Shared work log for every agent (Claude Code, Codex, Cursor, …) and human working on RRCONS.
**Newest entry first.** Read it at the start of a session; prepend an entry before you stop.
Template and rules: `.claude/skills/rrcons-handoff/SKILL.md` (plain Markdown — usable by any agent).

---

## 2026-10-11 — Claude Code — Laravel 10.48 → 13.35 and PHP 8.2 → 8.5 upgrade (1:1)
- **Branch / HEAD:** `solveit-staging` → PR to `solveit-id/rrcons` main; same commits cherry-picked to `staging` → PR to `hafizhfalasyah/rrcons` main.
- **Goal:** upgrade framework + PHP only, with zero change in flow, logic, UI, responsiveness, relationships, performance, data or migrations.
- **Backup (before any change):** `D:\PROYEK\PROYEK SOLVE IT\WEB\rrcons-backups\pre-laravel13-20261011-130219\` (full project tar incl. vendor/.env/storage, git bundle, README); git tag `backup/laravel10-final` on both remotes. The production DB could not be dumped from here (remote access denied) — back it up from the hosting panel before deploying.
- **Done:**
  - `composer.json`: php ^8.5, laravel/framework ^13, sanctum ^4, tinker ^3, ui ^4.6, LFM ^2.15, `yajra/laravel-datatables-oracle` ^13 (core only), intervention ^3.11 / image-laravel ^1.5, doctrine/dbal removed; dev: phpunit ^12, collision ^8.6, ignition ^2.12.
  - Code: migration `add_performance_indexes` → `Schema::getIndexes()`; `AnalyticsReport` Carbon 3 `diffInSeconds` cast; `VerifyCsrfToken` extends `PreventRequestForgery` and keeps token-only CSRF; `config/hashing.php` `rehash_on_login=false`.
  - Pinned Laravel 10 visuals: `resources/views/errors/minimal.blade.php`; LFM v2.9.1 views in `resources/views/vendor/laravel-filemanager`.
  - Dockerfile → `php:8.5.7-apache-trixie`.
  - Tests: `UpgradeCompatibilityTest`, `CsrfProtectionTest`. Docs: AGENTS.md (§2, §11, new §12a), CLAUDE.md, `.ai/*`, `.claude/*`, README-Docker.md.
- **In progress (uncommitted):** none.
- **Verification:** `php artisan test` 84 passed (773 assertions). Before/after snapshot diff of 151 responses (all public + admin pages in id/en, role views, validation errors, write flows, beacons, sitemap/robots/llms/feed): identical except lowercase `charset`, DataTables JSON key order (SQLite column order) and SQLite DDL text. Resulting DB rows identical. All 43 migrations on MariaDB 10.6.27: identical `mariadb-dump --no-data`; Laravel 13 on a Laravel 10-migrated DB → "Nothing to migrate". Route table identical (103) except `QUERY` verb on LFM `Route::any`. LFM endpoints identical (index differs only by 5 unused JS lang keys). ImageOptimizer output byte-identical.
- **Next steps:** before deploying to shared hosting: switch hosting to PHP 8.5, back up DB, deploy, run `php artisan optimize:clear` then the AGENTS.md §11 checklist.
- **Found (pre-existing, not fixed — out of 1:1 scope):** `/sitemap.xml` and `/feed` 500 when PHP `short_open_tag=On` (Docker images); see AGENTS.md §12 item 12. Docker PHP 8.5.7 image builds and runs; full suite in Docker: 82/84 (only those 2 SeoTest cases, same as Laravel 10 on the PHP 8.2 image).
- **Gotchas / decisions:** Blade compiled-view cache survives vendor upgrades (mtime) → always `optimize:clear` after `composer update`. Laravel 10 app structure intentionally kept (officially supported).

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
  6. (Superseded) Laravel was upgraded to 13.35 — see the entry above.
- **Verification:** `php artisan test` → 77 passed (759 assertions), 1 vendor deprecation notice (PHP 8.5). Documentation-only change; no runtime code touched.
- **Gotchas / decisions:**
  - `origin` and `solveit` have unrelated histories; only `README.md` differs between them (intentional).
  - Local `.env` has `APP_ENV=production`; never run destructive DB commands.
  - Laravel Boost was **not** installed (needs a framework bump; dependency changes need user approval).
