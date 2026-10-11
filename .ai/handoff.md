# Handoff log

Shared work log for every agent (Claude Code, Codex, Cursor, …) and human working on RRCONS.
**Newest entry first.** Read it at the start of a session; prepend an entry before you stop.
Template and rules: `.claude/skills/rrcons-handoff/SKILL.md` (plain Markdown — usable by any agent).

---

## 2026-10-12 — Claude Code — clearer admin panel: bilingual tabbed forms, guides, sidebar & dashboard
- **Branch / HEAD:** `solveit-staging` → PR to `solveit-id/rrcons` main; cherry-picked to `staging` → PR to `hafizhfalasyah/rrcons` main.
- **Goal:** make the admin understandable for non-technical staff and stop the "save EN → switch language → save ID" routine.
- **Done:**
  - Layout: `public/back/css/admin.css` (RRCONS blue + Poppins), new header (view website, panel language, profile with role, red Logout; "My Profile" removed), sidebar grouped Overview / Website content (accordion per public page, plain labels) / Blog / Account, logout pinned in `.sidebar-footer` (old bug: `height: 100vh` + `top: 56px` hid the bottom), collapsible on desktop, off-canvas on mobile.
  - Components `resources/views/components/admin/*` (page-header + guide, alerts, form-modal, translatable ID/EN tabs, media-input with preview, delete-modal, bilingual, updated-at) and partials `back/partials/{section-headings,hero-banner,text-items}`. Every CMS page rewritten with them; old `*modal.blade.php` files removed. Lists show both languages and flag missing English.
  - Fixes on the way: failed form now reopens with its own old input only (`_modal`), per-row "last updated" (was a global value), gallery month name overflow, duplicate `#logout-form`, `sub_title` HTML `required` vs nullable validation.
  - Dashboard: welcome + quick actions, role-based content summary (published articles, pending comments, categories, gallery, posters, users), visitor analytics in tabs with explanations, latest/popular articles. Controllers add `withCount` / counts only.
  - ~230 strings in `lang/id.json`; `AdminInterfaceTest` (5) + updated assertions in `MultiLanguageTest`, `AnalyticsTest`. Docs: AGENTS.md admin UI patterns, frontend/bilingual guidelines, bilingual skill.
- **Verification:** 109 tests pass. Visual check with headless Edge on the Docker copy (desktop 1440×900/640, mobile 390): dashboard, section headings, about modal (ID/EN tabs), gallery, services, users, profile dropdown, short screen + mobile sidebar with logout visible; no JS errors. Temporary check account removed afterwards.
- **Gotchas:** Bootstrap is 5.2.3 — no `*-subtle` / `text-bg-*`. Strings inside components and sidebar labels must exist in `lang/id.json` (test enforces literals only — arrays of labels passed to components are not scanned, check by hand).

## 2026-10-11 — Claude Code — storage cleanup, README sync, no-terminal deploy kit
- **Branch / HEAD:** `solveit-staging` → PR to `solveit-id/rrcons` main; cherry-picked (except the README commit, already identical there) to `staging` → PR to `hafizhfalasyah/rrcons` main.
- **Done:**
  - Checked then deleted local `storage/app/public/photos` and `storage/app/public/public` (= `public/storage/photos|public` via the symlink). `public/` was empty (only the old path-normalisation migration mentioned it); `photos/` is the LFM image folder (config `lfm.php`), its contents (`photos/1/image.png`, empty `13/`, `shares/`) were not referenced in any DB text column of the production copy, code, views or JS. LFM recreates the folders when opened. Fixed `UpgradeCompatibilityTest` which recreated them in real storage (now `Storage::fake`).
  - `README.md` on solveit is now byte-identical to hafizhfalasyah's; docs/skills updated: both trees must stay identical.
  - `scripts/build-release.ps1`: `-UseDocker`, `-WithWebInstaller` (stub `scripts/stubs/web-installer.php`); `docs` no longer packaged. `scripts/import-db.ps1`: `-Native` (local MySQL/XAMPP, `-EnvFile`, strips mariadb-dump sandbox header). `docs/DEPLOY-SHARED-HOSTING.md` rewritten (local without/with Docker; hosting via Terminal, Cron Job, web installer, phpMyAdmin import; troubleshooting).
- **Verification:** 104 tests pass. Native rehearsal on XAMPP MariaDB 10.4 with the production copy: 11 migrations OK, site 200. Build `-UseDocker -WithWebInstaller` OK. Hosting simulation (PHP 8.5 Apache, production mode, pre-migration production data, no storage link): installer 404 on wrong key, all checks ✔, run ✔ (link + 11 migrations + caches), self-deleted, pages 200, no log errors; also verified for a `public_html` layout. Cron command and Method D dump command executed as documented.
- **Next steps (user):** follow `docs/DEPLOY-SHARED-HOSTING.md`. Keep `release/*-installer.txt` private.
- **Gotchas:** web installer keys are secrets — never print/commit them. `storage:link` prints ERROR but exits 0 when the link exists (harmless).

## 2026-10-11 — Claude Code — article slug redirects + blog comment moderation
- **Branch / HEAD:** `solveit-staging` → PR to `solveit-id/rrcons` main; cherry-picked to `staging` → PR to `hafizhfalasyah/rrcons` main.
- **Goal:** stop old article URLs from breaking when the title changes; add moderation for blog comments.
- **Done:**
  - Slugs: migration `2026_10_17_000000_create_article_slug_redirects_table` (+ renames any article slug `comments`), model `ArticleSlugRedirect`, `Article::booted()` records previous slugs, `Article::slugIsTaken()` keeps old slugs reserved for their article, `HomeController@show` → 301 to the current slug (query kept, published only). Admin: flash message on URL change, "Article URL" + old URLs on the article detail page, updated help text. `GeneratesUniqueSlug::slugIsTaken()` hook.
  - Comments: migration `2026_10_17_000100_add_moderation_to_comments_table` (status/moderated_by/moderated_at, existing rows → approved), `Comment` statuses/scopes/`moderate()`, public page shows approved only, new pending message (id/en), `Back\Blog\CommentController` + `/blog/comments` routes (admin, writer) + view + sidebar badge, `/blog/comments` disallowed in robots, `comments` reserved slug.
  - Tests: `ArticleSlugRedirectTest` (7), `CommentModerationTest` (9), access matrix row. Docs: AGENTS.md §6/§7/§8/§12, `.ai/guidelines/rrcons-blog.md`, deploy guide checklist, reviewer agent.
- **In progress (uncommitted):** none.
- **Verification:** 104 tests / 877 assertions pass (natively and inside Docker). Docker production copy: both migrations OK, 74 existing comments → approved and still shown; live HTTP flow: submit (hidden) → admin list + badge → approve (shown, 74→75) → delete (74); real article renamed → old URL 301 (with `?lang`) → renamed back → original URL 200, temporary URL 301. Test data cleaned up.
- **Next steps (user):** redeploy per `docs/DEPLOY-SHARED-HOSTING.md` (production will run 11 pending migrations). After deploy, moderate new comments in Blog → Comments.
- **Gotchas:** don't delete `article_slug_redirects` rows; keep `comments` in `Article::reservedSlugs()` (route `/blog/comments` shadows `/blog/{slug}`).

## 2026-10-11 — Claude Code — Docker refresh, remaining bug fixes, shared-hosting deploy kit
- **Branch / HEAD:** `solveit-staging` → PR to `solveit-id/rrcons` main; cherry-picked to `staging` → PR to `hafizhfalasyah/rrcons` main.
- **Goal:** bring local Docker in line with the real project, fix remaining bugs, prepare redeploy to shared hosting (not VPS).
- **Done:**
  - Docker: app rebuilt on PHP 8.5.7; `db` published on `127.0.0.1:3307` (native `php artisan serve` can use it); `node` → Node 22 running the Tailwind watcher (Vite was unused); Composer cache dir fixed; upload limits 64M/72M; entrypoint makes a relative `public/storage` link; dead `docker/node/entrypoint.sh` removed; `.env.docker.example`, `.env.example`, `README-Docker.md` rewritten.
  - Bugs: `/sitemap.xml` + `/feed` 500 with `short_open_tag=On` (Blade tokenizer; XML prolog now split), `package-lock.json` out of sync (`npm ci` failed on Linux — regenerated; compiled CSS byte-identical on Windows and Linux), `public/.htaccess` upload limit 20M < 50 MB video rule, hero `webp` rejected on RR Future/Service/Contact, broken + non-idempotent `ConfigSeeder`, tests could hit MariaDB when run inside the `app` container.
  - Deploy kit: `docs/DEPLOY-SHARED-HOSTING.md`, `scripts/build-release.ps1` (release ZIP with production vendor), `scripts/import-db.ps1` (load a prod dump into Docker + migrate). `/release` git-ignored.
  - Tests: `RegressionTest` (4). Docs: AGENTS.md (§3, §11, §12), CLAUDE.md, `.ai/guidelines/*`.
- **Rehearsal on a production copy (Docker DB):** 9 pending `2026_10_*` migrations ran fine on Laravel 13/PHP 8.5 (timezone fix ~37 s, 11,482 legacy visits → 7,894 page views); 66 public URLs + 68 admin pages + 33 assets + CRUD/upload/CSRF checks OK; no new log errors.
- **Incident (resolved):** running `php artisan test` inside the `app` container wiped the local Docker DB because compose env overrode phpunit `<env>`. Restored from `rrcons-backups/docker-db-20261011-143301` and re-migrated (same counts as before). Root cause fixed (`<server>` env + hard guard in `tests/CreatesApplication.php`). Production was never touched.
- **In progress (uncommitted):** none.
- **Verification:** 88 tests / 795 assertions pass natively and inside Docker; snapshot diff vs Laravel 10 baseline unchanged (only the 4 known non-visible diffs); release ZIP built and boot-tested.
- **Next steps (user):** ensure hosting offers PHP 8.5, back up prod DB + `storage/app/public`, follow `docs/DEPLOY-SHARED-HOSTING.md`. Local `.env` for native mode needs `DB_HOST=127.0.0.1`, `DB_PORT=3307` and the Docker DB credentials.
- **Follow-up fix:** Tailwind v3 `--watch` exits when stdin closes (container) → compose uses `--watch=always --poll`; the watcher never drops removed classes, so run a clean `npm run build:front-css` before committing `app.css`.
- **Gotchas:** PowerShell 5.1 strips inner double quotes when calling native programs (scripts avoid them). Never write a literal `<?` in Blade templates.

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
