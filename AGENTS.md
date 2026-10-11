# RRCONS — Agent Handoff Guide

> Canonical, tool-agnostic project guide for AI coding agents (Claude Code, Codex, Cursor, Copilot, Gemini, …) and humans.
> `CLAUDE.md` imports this file. Concise topic rules live in `.ai/guidelines/` (Laravel Boost format).
> The running work log for handoffs is `.ai/handoff.md`.
>
> Last full audit: **2026-10-11** — Laravel 13.35.0 on PHP 8.5 (upgraded 1:1 from Laravel 10.48.22, see §12a), 104 tests / 877 assertions passing (also inside Docker).

---

## 0. Handoff protocol (do this first)

1. Read this file fully, then `.ai/handoff.md` (what the previous agent was doing / what is open).
2. Check repository state before touching anything:
   ```bash
   git status -sb && git branch -vv && git log --oneline -10
   ```
   Know which branch tracks which remote (see §4) — the two remotes have **unrelated histories**.
3. Run the test suite to confirm a green baseline: `php artisan test` (see §10).
4. Work in small, verifiable steps. Keep tests green.
5. **Before you stop, run out of context, or hand off:** update `.ai/handoff.md` (done / in progress / next steps / gotchas found). Uncommitted work must be described there.

---

## 1. What this project is

Company-profile website + CMS for **RR Consultant Indonesia** (legal name *Rita Russo Consultant*, brand **RRCONS**), based in Malang, Jawa Timur.

- **Public site (bilingual Indonesian / English):** Home (`/`), RR Future Tech (`/rrfuture`), RR Vision (`/service`), Blog (`/blog`, `/blog/{slug}`), Contact (`/contact`), plus `sitemap.xml`, `robots.txt`, `llms.txt`, `feed` (RSS).
- **Admin panel** (Bootstrap): dashboard with first-party visitor analytics, CMS editors for every public section, blog (articles, categories, configs), users, file manager.
- **Production** runs on shared hosting. Docker (`compose.yaml`) is **local development only**.

---

## 2. Tech stack (actual, verified)

| Layer | Details |
|---|---|
| Backend | PHP **`^8.5`** (composer; developed on 8.5.7), **Laravel 13.35.0** (Laravel 10 directory structure kept: `app/Http/Kernel.php`, `RouteServiceProvider`, `config/app.php` providers), Eloquent, Blade, Carbon 3 |
| Packages | `laravel/ui` 4.6 (auth scaffolding, login only), `laravel/sanctum` 4 (unused API `/api/user`), `laravel/tinker` 3, `yajra/laravel-datatables-oracle` 13 (admin article table — core package only, the v13 meta package would add Livewire), `unisharp/laravel-filemanager` 2.15 (CKEditor uploads; **v2.9.1 views pinned**, see §12a), `intervention/image` 3.11 + `intervention/image-laravel` 1.5 (GD driver, upload optimization). `doctrine/dbal` was removed (Laravel 11+ has native schema tools) |
| Dev packages | PHPUnit 12, Pint, Sail, Collision 8, Ignition 2.12, Faker, Mockery |
| DB | MySQL/MariaDB in production & Docker (MariaDB 10.6.27); **SQLite in-memory for tests** |
| Public frontend | Standalone Blade pages + **Tailwind 3.4.17 compiled to `public/front/css/app.css`** (committed). CDN: Alpine 3.17.4, AOS 2.3.4, Feather icons, Google Fonts (Poppins). Shared JS: `public/front/js/index.js` |
| Admin frontend | CDN: Bootstrap 5.2.3, Bootstrap Icons, jQuery 3.7.1, DataTables 2.1.8, SweetAlert2 11, Chart.js 4.4.4, CKEditor 5 (41.2.1 classic), Feather icons |
| Vite | `vite.config.js` (`resources/sass/app.scss`, `resources/js/app.js`) exists but **no view uses `@vite`** — not part of the runtime |
| Local toolchain seen | PHP CLI 8.5.7 (runs the app directly, no Docker needed), Composer 2.8, Node 22, npm 10. Docker image: PHP 8.5.7 + Apache |

---

## 3. Commands

```bash
# install
composer install
npm ci                                   # only needed to rebuild public Tailwind CSS
cp .env.example .env && php artisan key:generate
php artisan storage:link                 # uploads are served from public/storage

# run
php artisan serve                        # no Vite/dev server needed

# tests (SQLite :memory:, forced in phpunit.xml)
php artisan test
php artisan test --filter=ArticleSchedulingTest

# public CSS — REQUIRED after changing Tailwind classes in resources/views/front/**,
# public/front/js/** or app/Helpers/** ; commit the regenerated file
npm run build:front-css                  # -> public/front/css/app.css (minified)

# useful inspection
php artisan route:list --except-vendor
php artisan migrate:status
```

After pulling dependency changes run `php artisan optimize:clear` (or at least `php artisan view:clear`): Blade decides staleness by file mtime and Composer keeps old mtimes, so stale compiled vendor views can survive an upgrade.

### Custom artisan commands (`app/Console/Commands`)

| Command | Purpose | Safety |
|---|---|---|
| `rrcons:optimize-images [--dry-run] [--restore=<dir>] [--force]` | Re-encode already-uploaded images under `storage/app/public/back` | Backs originals up to `storage/app/image-backups/<Ymd-His>`; restorable |
| `rrcons:prune-page-views [--days=N] [--dry-run] [--force]` | Delete `page_views` older than retention (default `ANALYTICS_RETENTION_DAYS`=730, min 30) | Not scheduled; manual only |
| `rrcons:fix-legacy-timezone --switched-at="YYYY-MM-DD HH:MM" [--dry-run] [--restore] [--force]` | Shift timestamps written while the app was UTC into Asia/Jakarta | Every changed value backed up in `legacy_timezone_backups`; idempotent |

The scheduler (`app/Console/Kernel.php`) is **empty** — nothing requires cron. Scheduled blog posts are a query-time filter, not a job.

### Docker (local only)

```bash
cp .env.docker.example .env.docker      # fill passwords + RRCONS_APP_KEY
docker compose --env-file .env.docker up -d --build
# app http://localhost:8082 (PHP 8.5.7 + Apache) · MariaDB 10.6.27 on 127.0.0.1:3307 · Mailpit UI 8025
# node service = Tailwind watcher (npm ci if needed, then build:front-css --watch=always --poll); Vite is not used
# the watcher never drops removed classes -> run a clean `npm run build:front-css` before committing app.css
docker compose --env-file .env.docker exec app php artisan test   # safe: tests are forced onto SQLite :memory:
```
Native mode (no PHP container): `docker compose --env-file .env.docker up -d db mailpit` + `php artisan serve` with `.env` `DB_HOST=127.0.0.1`, `DB_PORT=3307` (see `.env.example`). `public/storage` is a **relative** symlink (`../storage/app/public`) that works in both modes. Load a production dump into Docker with `scripts/import-db.ps1 -File dump.sql -Migrate`. Upload limits: 64M/72M (`docker/php/custom.ini` and `public/.htaccess`). See `README-Docker.md`.

---

## 4. Git & release workflow (IMPORTANT)

Two GitHub remotes with **unrelated histories** (different root commits, different hashes) but **identical file content except `README.md`**:

| Remote | URL | Root commit | Local branches tracking it |
|---|---|---|---|
| `origin` | https://github.com/hafizhfalasyah/rrcons.git | `b1b3269 Deploy v1.0` | `main` → `origin/main`, `staging` → `origin/staging` |
| `solveit` | https://github.com/solveit-id/rrcons.git | `223420b Initial commit` | `solveit-staging` → `solveit/staging` |

- `README.md` differs on purpose: `origin` has a custom project README; `solveit` still has the Laravel default README. Do not "sync" README between them unless asked.
- `backup/legacy-local-main` is a local-only safety branch — never delete it.
- Never merge one remote's history into the other (they share no ancestor). Port changes with `git cherry-pick`.

**Release flow used for every change set (PRs #1–#6 on both repos):**
1. Commit on the working branch (e.g. `solveit-staging`, based on latest `solveit/main`).
2. `git push solveit solveit-staging:staging` → open PR `staging → main` on `solveit-id/rrcons` → merge (merge commit).
3. `git checkout staging && git merge --ff-only origin/main` → `git cherry-pick <commits>` → `git push origin staging` → PR `staging → main` on `hafizhfalasyah/rrcons` → merge.
4. Fast-forward local `main` to `origin/main`; return to the working branch.

**Commit message convention** (lowercase, imperative, no trailing period, no co-author trailers):
```
(add) ...      new feature/file
(update) ...   change to existing behaviour/UI
(fix) ...      bug fix
(perf) ...     performance
(test) ...     tests
(chore) ...    tooling/config
```
Example: `(update) remove channel pending tabs and align delete confirmation modal`.
Do **not** add `Co-Authored-By` lines. Commit/push only when the user asks.

---

## 5. Directory map

```
app/
  Console/Commands/          rrcons:* commands (see §3)
  Helpers/helpers.php        isMenuActive(), highlightLastWord() (escapes!), asset_version()
  Http/
    Controllers/
      Auth/LoginController   email+password login only, redirect /dashboard
      Back/                  admin: Dashboard, User, Home/HomeMenu, RRfuture/RRfutureMenu,
                             Service/ServiceMenu, Contact/ContactMenu, Blog/{Article,Category,Config}
      Front/                 HomeController (ALL public pages), SeoController, AnalyticsController
                             (Front/ArticleController + Front/CategoryController are DEAD code)
      Concerns/              ManagesPublicFiles (uploads), ValidatesTranslations (bilingual rules)
    Middleware/UserAccess    role gate: ->middleware('UserAccess:admin,writer')
    Middleware/SetLocale     ?lang= / session('app_locale') -> App + Carbon locale
    Requests/                ArticleRequest, UpdateArticleRequest, UserRequest, UserUpdateRequest
  Models/                    User, PageView, Visitor(legacy)
    Blog/ Home/ RRfuture/ Service/ Contact/   content models (see §7)
    Concerns/                HasTranslations, GeneratesUniqueSlug
  Support/                   Seo, HtmlSanitizer, ImageOptimizer, LegacyTimezoneFix
    Analytics/               Tracker, AnalyticsReport, TrafficSource, UserAgent, LegacyVisitorImporter
config/analytics.php seo.php lfm.php filesystems.php(optimize_uploaded_images)
database/migrations          2014–2025 originals + 2026_10_* hardening migrations
lang/{id,en}/*.php           public-site strings (grouped keys, e.g. blog.sections.blog.detail...)
lang/id.json                 admin-panel strings (English source key -> Indonesian)
resources/views/front/       6 standalone public pages + partials/{seo,analytics,articles,article_list}
resources/views/back/        admin; layout/template.blade.php + layout/sidebar.blade.php
resources/views/seo/         sitemap.blade.php, feed.blade.php
public/front/                css/app.css (compiled, committed), js/index.js, image/, images/
public/vendor/laravel-filemanager/   published LFM assets (committed)
tests/Feature, tests/Unit    PHPUnit (see §10)
docker/, Dockerfile, compose.yaml    local dev only
```

---

## 6. Architecture & request flow

### Middleware
`web` group = Laravel defaults + `SetLocale`. Aliases: `UserAccess` (role list), `auth`, `guest`, `throttle`.
`VerifyCsrfToken::$except = ['_v/hit', '_v/ping']` (sendBeacon analytics; protected by HMAC token instead).
`TrustProxies` reads `TRUSTED_PROXIES` (via `config('analytics.trusted_proxies')`).

### Routes (`routes/web.php`, 103 routes incl. vendor)
- Public (`Front\HomeController`): `/`, `/rrfuture`, `/service`, `/blog`, `/blog/{slug}` (`detail_blog`), `POST /blog/{slug}/comment` (`throttle:5,1`), `/get-articles`, `/search-articles` (AJAX JSON with rendered HTML), `/contact`.
- SEO (`Front\SeoController`): `/sitemap.xml`, `/robots.txt`, `/llms.txt`, `/feed`.
- Analytics beacons (`Front\AnalyticsController`, `throttle:120,1`): `POST /_v/hit`, `POST /_v/ping` — always `204`.
- Language: `GET /set-language/{id|en}` → session `app_locale` → redirect back.
- Auth: `Auth::routes()` with register/reset/confirm/verify **disabled**. Accounts are created by an admin.
- Admin: everything under `middleware(['auth','UserAccess:admin,writer,uploader'])`, then narrower groups. Admin edit forms use `POST /{section}/{id}/update` and `DELETE /{section}/{id}/delete` (not resource routes), except blog/users.
- Route **names must stay unique** (a test enforces it) so `php artisan route:cache` works.

### Role matrix (enforced in routes + controllers; mirrored in `back/layout/sidebar.blade.php`)

| Area | admin | writer | uploader |
|---|---|---|---|
| `/dashboard`, `/dashboard/realtime` | ✓ | ✓ | ✓ |
| `/users` | all users, create/delete, change roles | own profile only | own profile only |
| `/home/poster`, `/home/gallery` | ✓ | ✗ | ✓ |
| other `/home/*`, `/rrfuture/*`, `/service/*`, `/contact/*` | ✓ | ✗ | ✗ |
| `/blog/articles`, `/blog/categories`, `/blog/comments` (moderation) | ✓ | ✓ | ✗ |
| `/blog/configs` | ✓ | ✗ | ✗ |
| `/laravel-filemanager/*` | ✓ | ✓ | ✗ |

Users with `role = NULL` get 403 everywhere in the panel. Admins cannot change their own role or delete themselves. When changing access, update **routes, sidebar, and `tests/Feature/AccessControlTest.php`** together.

---

## 7. Domain model & database

### "Menu + sections" CMS pattern
Each public page has a `*_menus` table whose rows hold the **section heading** (`title`, `sub_title`); each section table has a FK to one menu row and the section content. Views read the heading through the relation, e.g. `$hero->homeMenu->title`.

| Page | Menu model/table | Section models/tables (FK) |
|---|---|---|
| Home | `HomeMenu` / `home_menus` | `HeroHome` (bg_img), `AboutHome` (img, text), `ServiceHome` (name, desc), `AdvantageHome` (title, desc), `ProfileHome` (img, name, position), `BusinessHome` (img, title, desc, form_link), `SosmedHome` (video, title, sosmed_link), `GalleryHome` (img, title, desc, month, year) — FK `home_menu_id` (cascade) |
| Home popup | — | `PosterHome` / `poster_homes` (img, name) — **no menu FK** |
| RR Future Tech | `RRfutureMenu` / `rrfuture_menus` | `HeroRRfuture` (bg_img), `ProductRRfuture` (name, desc) — FK `rrfuture_menu_id` |
| RR Vision | `ServiceMenu` / `service_menus` | `HeroService` (bg_img), `OurService` (name, desc) — FK `service_menu_id` |
| Contact | `ContactMenu` / `contact_menus` | `HeroContact` (bg_img), `UsContact` (title, desc) — FK `contact_menu_id` |

- CMS rows are **edit-only** in the admin (no create/delete) except **posters** and **gallery** (full CRUD). Blog and users have full CRUD.
- **There are no seeders for CMS content.** A fresh database renders empty sections (the home page is explicitly null-safe; verify other pages when testing on an empty DB). Real content comes from the production database dump. `DatabaseSeeder` only calls `ConfigSeeder`, which is **broken** (`use App\Models\Config` — the model is `App\Models\Blog\Config`).
- `UsContact.title` is a fixed key (e.g. `Alamat`) used by the contact page (`$us['Alamat']`) and by `Seo::streetAddress()`; it is not editable and not translated.
- New gallery items take `home_menu_id` from an existing gallery row, else a menu titled like "galeri/gallery", else hard-coded `8`.

### Blog
- `Category` (name_id/en, slug **unique**) `hasMany` `Article`.
- `Article`: `user_id` = **last editor**, `created_by` = **original author** (relations `User()`, `Creator()`), `category_id` (FK, no cascade — category delete is blocked while it has articles), `title_id/en`, `slug` (**unique**), `desc_id/en` (longText HTML), `meta_title_id/en` (≤70), `meta_description_id/en` (≤320), `img`, `views`, `status` (`'0'` private / `'1'` publish), `publish_date` (datetime, nullable). Index `(status, publish_date)`.
- `Comment` (article_id cascade, name, email, content, `status` `pending|approved|spam` default `pending`, `moderated_by`, `moderated_at`). Visitors post via `POST /blog/{slug}/comment` (`throttle:5,1`, status cannot be set from the form); only `Comment::approved()` is shown/counted on the article page. Admin + writer moderate at `/blog/comments` (`Back\Blog\CommentController`: filter tabs, search, approve / back to pending / spam / delete; sidebar badge = pending count). Comments that existed before moderation were migrated as `approved`.
- `ArticleSlugRedirect` (article_id cascade, slug unique): previous slugs of an article.
- `Config` (name, value): legacy blog settings, admin-editable, **not used by public views**.
- Relation methods on Article are capitalized (`Category()`, `User()`, `Creator()`); `Category` also has `Articles()`. PHP method names are case-insensitive, so `with('category')` works, but the loaded-relation key is case-sensitive: eager-load with the same casing you later access (e.g. `with('Category')` + `$article->Category`) or the relation is lazy-loaded again.

### Other tables
- `users`: name, email (unique), password, `role` enum(`admin`,`writer`,`uploader`) nullable, `two_factor_code` (unused), timestamps.
- `page_views` (new analytics, one row per JS-verified page view): `view_id` uuid unique, `visitor_hash` (daily HMAC, no raw IP), path, route_name, article_id (nullOnDelete), locale, referrer_host, source, utm_*, device, browser, os, screen_width, browser_language, country, engaged_seconds, `tracker` (`js` | `legacy`), `legacy_visitor_id`, created_at, last_seen_at.
- `visitors`: **legacy** visit log, no longer written; imported once into `page_views` (tracker=`legacy`, bots skipped).
- `legacy_timezone_backups`: created on demand by the timezone fix.
- Laravel defaults: `password_reset_tokens`, `password_resets`, `failed_jobs`, `personal_access_tokens`.

### Notable migrations (2026_10_*) — all written to be idempotent / safe on partially migrated DBs
`change_publish_date_to_datetime` · `normalize_uploaded_file_paths` (filename → `back/<section>/<file>`) · `add_unique_slug_to_articles_and_categories` (dedupes with `-{id}`) · `add_translations_to_cms_content` (`x` → `x_id` + `x_en`) · `add_seo_fields_to_articles` · `fix_legacy_utc_timestamps` (auto only when the datetime migration ran in the same `migrate` call) · `create_page_views` · `import_legacy_visitors_into_page_views` · `add_performance_indexes`.
Migrations must keep working on **both MySQL and SQLite** (tests): one `renameColumn` per `Schema::table` call, guard with `Schema::hasColumn`.

---

## 8. Key behaviours & flows

### Bilingual content (id = primary, en = optional)
- Physical columns `<field>_id` / `<field>_en`. Models list logical fields in `protected $translatable` and use `HasTranslations`: `$model->title` returns the active-locale value, **falling back to `_id`** when English is empty. Always **write** the physical columns.
- Admin validation: `ValidatesTranslations::validateWithTranslations($request, ['title' => 'required|...'], $plainRules)` → `_id` gets the rules, `_en` becomes `nullable` + same rules; empty `_en` is stored as `null`.
- Locale resolution (`SetLocale`): `?lang=id|en` (also stored in session) → `session('app_locale')` → `config('app.locale')` (= `APP_LOCALE`, config default `en`). Carbon locale follows.
- Public UI strings: `lang/{id,en}/<page>.php` (keys must match in both — test enforced). Admin UI strings: `__('English text')` with Indonesian in `lang/id.json` (every `__('...')` literal in `resources/views/back`, `app/Http/Controllers`, `app/Http/Requests` must exist there — test enforced).

### Blog publication
Admin form choice `publication` ∈ `private | publish | schedule` → `Article::publicationAttributes()` maps to `status` + `publish_date`:
- private → status `'0'` (keeps old publish_date) · publish → status `'1'`, publish_date = now (already-published articles keep their date) · schedule → status `'1'`, future `publish_date` (validated `after:now`).
- Public visibility = `Article::published()` scope: status `'1'` AND (`publish_date` NULL or ≤ now). Use this scope for **every** public query (pages, sitemap, feed, llms, comments).
- Slug = `Article::uniqueSlug($title_id)`, regenerated when the Indonesian title changes; `Article::booted()` stores every previous slug in `article_slug_redirects` and `HomeController@show` answers old slugs with a **301 to the current URL** (query string kept; only for published articles). A previous slug of one article can never be reused by another (`Article::slugIsTaken`); returning to an old title reuses it and drops the redirect. Reserved slugs: `articles`, `categories`, `configs`, `comments`.
- Article HTML is sanitized on save (`HtmlSanitizer::clean`) and again on render (`$article->safe_desc`). The sanitizer is an allowlist (iframes only from YouTube/Vimeo/Google Maps).
- `views` is incremented by the analytics tracker (once per visitor per article per day, bots excluded), not by page loads.

### Uploads & media (`Concerns\ManagesPublicFiles`)
- Stored on disk `public` (`storage/app/public`) as `uniqid().ext` under `back/blog`, `back/home/{poster,hero,about,profile,business,sosmed,gallery}`, `back/{rrfuture,service,contact}/hero`. DB stores the **relative path**; views use `asset('storage/' . $path)`.
- `replacePublicFile()` deletes the old file; `deletePublicFile($path, $legacyDir)` handles legacy filename-only values.
- Images (jpg/jpeg/png/webp) are auto-optimized after upload by `ImageOptimizer` (max 2560px, quality 85, only kept if ≥5% smaller; never fails the upload). Toggle `OPTIMIZE_UPLOADED_IMAGES`.
- Limits: images `max:2048` KB; sosmed video `max:51200` KB (mp4/mpeg/mkv/mov).
- CKEditor uploads go to UniSharp LFM (`/laravel-filemanager/upload?type=Images`, disk `public`, private + shared folders enabled, `use_package_routes=false` — routes registered once inside the role-protected group).

### Visitor analytics (first-party, cookieless)
1. Every public page includes `front.partials.analytics`, which embeds an HMAC-signed token (`Tracker::issue`: view uuid, path, route, article id, locale, timestamp; TTL 7200 s).
2. Browser JS (skips bots/webdriver) sends `POST /_v/hit` (sendBeacon, `text/plain` JSON) → `Tracker::recordHit` → `page_views` row (`insertOrIgnore` on view uuid = replay-safe).
3. Heartbeat `POST /_v/ping` every 15 s → `last_seen_at`, `engaged_seconds` (cap 1800 s). "Online" = seen within 60 s.
4. Ignored: bots (`config('analytics.bot_pattern')`), logged-in users (`ANALYTICS_EXCLUDE_AUTHENTICATED`), `ANALYTICS_EXCLUDE_IPS`.
5. Dashboard (`AnalyticsReport`): ranges `today|7d|30d|90d`, KPIs vs previous period, daily trend, top pages/articles, sources (search/ai/social/referral/direct), referrers, device/browser/OS/locale/country breakdowns; `/dashboard/realtime` JSON is polled.

### SEO / GEO
- `App\Support\Seo` builds per-page meta, Open Graph/Twitter, canonical, hreflang alternates (`?lang=`), and JSON-LD graph (Organization, WebSite, Blog/BlogPosting, Service lists, BreadcrumbList). Rendered by `front.partials.seo` — each page controller passes `'seo' => Seo::<page>(...)`.
- Organization/geo data: `config/seo.php` + `SEO_*` env vars (leave empty if unknown; empty values are not printed).
- `robots.txt` disallows admin paths and explicitly allows AI crawlers; `llms.txt` is a Markdown site summary; sitemap includes published articles (with images) and categories that have published articles.
- Blog search results (`?q=`) are `noindex`.

### Admin UI patterns
- Pages `@extends('back.layout.template')`, `@section('title')`, `@section('content')`, `@push('css'|'js')`.
- Section index pages list rows in a table with Bootstrap modals (`updatemodal.blade.php`, `createmodal`, `deletemodal`) posting to the routes above; flash `session('success')` and `$errors`.
- Article list is server-side DataTables (`request()->ajax()` branch in `Back\Blog\ArticleController@index`); delete is AJAX + SweetAlert returning JSON.

---

## 9. Public frontend rules

- The 6 public pages (`front/index|rrfuture|service|blog|detail-blog|contact.blade.php`) are **standalone HTML documents** — no shared layout. Navbar, mobile menu, footer, language switcher and WhatsApp widget are duplicated; a change to one usually must be applied to **all six**.
- Styles: Tailwind utility classes only, compiled by `npm run build:front-css` (config `tailwind.config.js`, source `resources/css/front.css`). The compiled `public/front/css/app.css` is committed and loaded with `asset_version()` (cache-busting by mtime). **Never re-introduce the Tailwind Play CDN** (test enforced).
- `public/front/css/tailwind.css` (~4 MB) is a legacy, unreferenced file.
- Section titles use `{!! highlightLastWord($text) !!}` — the helper escapes its input; any other raw output must be escaped/sanitized.
- Home: poster popup (auto-slide stops when closed), category filter + AJAX articles (`/get-articles`), gallery filter by month/year.

---

## 10. Testing

- `phpunit.xml` forces `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` (as `<env force>` and `<server>`), and `tests/CreatesApplication.php` aborts on any other connection → tests never touch a real DB, even inside the Docker `app` container. Requires PHP extensions `pdo_sqlite` and `gd`.
- Suites: `AccessControlTest` (role matrix, registration disabled), `ArticleSchedulingTest`, `ContentManagementTest`, `MultiLanguageTest` (both locales, lang key parity, `lang/id.json` coverage), `SeoTest`, `AnalyticsTest`, `PerformanceTest` (compiled CSS, unique route names, single menu query, image optimization), `LegacyTimezoneFixTest`, `UpgradeCompatibilityTest` + `CsrfProtectionTest` (Laravel 13 pins, §12a), `RegressionTest` (XML prolog/short tags, hero webp, idempotent seeder), `ArticleSlugRedirectTest`, `CommentModerationTest`, `Unit/HtmlSanitizerTest`.
- Tests use `RefreshDatabase` + `User::factory()->create(['role' => ...])`. Only `UserFactory` exists — create other rows directly with models.
- Add/adjust tests with every behaviour change; follow the existing descriptive `test_snake_case_sentence` naming.

---

## 11. Environment & deployment

Important `.env` keys (see `.env.example`): `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE`, `DB_*`, `SEO_*`, `ANALYTICS_ENABLED`, `ANALYTICS_EXCLUDE_AUTHENTICATED`, `ANALYTICS_EXCLUDE_IPS`, `TRUSTED_PROXIES` (`*` behind Cloudflare/proxy), `ANALYTICS_COUNTRY_HEADER` (e.g. `CF-IPCountry`, only behind a CDN), `ANALYTICS_RETENTION_DAYS`, `OPTIMIZE_UPLOADED_IMAGES`.

⚠️ The local `.env` in the maintainer's checkout has `APP_ENV=production`; treat its database as **possibly real**. Never run `migrate:fresh`, `migrate:reset`, `db:wipe`, `db:seed` or destructive SQL against it. Never print or commit `.env` / `.env.docker` values.

**Full step-by-step guide: `docs/DEPLOY-SHARED-HOSTING.md`** (build the upload package locally with `scripts/build-release.ps1`: git archive of HEAD + `composer install --no-dev`, no `.env`/uploads/tests/docker). Short version (server must run **PHP 8.5** with `gd`, `pdo_mysql`, `mbstring`, `fileinfo`, `intl` …):
```bash
# back up the database from the hosting panel first
composer install --no-dev --optimize-autoloader
php artisan optimize:clear           # REQUIRED after the Laravel 13 upgrade: drops stale compiled views/config/routes
php artisan migrate --force          # the upgrade adds no migrations -> "Nothing to migrate"
php artisan storage:link             # once
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
No Node on the server is needed (compiled CSS is committed). No queue worker, no cron.
Rollback: git tag `backup/laravel10-final` (both remotes) = last Laravel 10 / PHP 8.2 code.

---

## 12. Known issues & tech debt (verified 2026-10-11)

1. ~~`ConfigSeeder` broken~~ — fixed 2026-10-11 (correct model, idempotent `firstOrCreate`).
2. No CMS seeders/factories: fresh installs have empty public sections; need a production dump.
3. Dead code: `Front\ArticleController`, `Front\CategoryController` (their views `front.article.*`, `front.category.*` don't exist; routes commented out). `Visitor` model is legacy (read-only history).
4. ~~Blog comments unmoderated~~ — moderation added 2026-10-11 (`/blog/comments`).
5. ~~Title change breaks the old article URL~~ — fixed 2026-10-11 (301 redirects from previous slugs).
6. ~~Hero `webp` rejected on RR Future/Service/Contact~~ — fixed 2026-10-11 (all heroes accept `jpeg,png,jpg,gif,webp`).
7. ~~Upload limit 20M < 50 MB video validation~~ — fixed 2026-10-11 (`public/.htaccess` + Docker `custom.ini`: 64M/72M; hosting may still cap it — check *Select PHP Version → Options*).
8. Gallery fallback `home_menu_id = 8` is hard-coded.
9. `public/front/css/tailwind.css` (4 MB) unused; Vite pipeline (`resources/js`, `resources/sass`) unused; `/api/user` Sanctum route unused; `users.two_factor_code` unused.
10. `composer.json` requires PHP `^8.5`: the hosting must run PHP 8.5 before this code is deployed (Composer's platform check stops the app otherwise). Laravel Boost 2.x is now installable (Laravel 13) but not installed (see `.ai/README.md`).
11. No `pint.json`; code style isn't uniformly Pint-formatted — do **not** run Pint over the whole repo (huge unrelated diff). Format only the files you touch, if at all.
12. ~~`/sitemap.xml` & `/feed` 500 with `short_open_tag=On`~~ — fixed 2026-10-11: Blade tokenizes templates with PHP's tokenizer, so a literal `<?` in a template becomes a PHP open tag; the XML prolog is now built from split strings. `RegressionTest` forbids `<?` (except `<?php`) in any template.
13. `phpunit.xml` uses `<server>` entries + `tests/CreatesApplication.php` refuses any connection other than SQLite `:memory:` — needed because process env vars (docker compose) override `<env>` and `RefreshDatabase` would wipe the real database.

## 12a. Laravel 10 → 13 upgrade (2026-10-11) — what was pinned to stay 1:1

The upgrade changed only framework/PHP versions. Behaviour was verified identical with before/after snapshots of every public/admin page (HTML, JSON, XML, headers, redirects, validation errors), resulting DB rows, MariaDB 10.6 schema of all 43 migrations (identical DDL), route table, file-manager endpoints and image-optimizer output (identical bytes). Things deliberately pinned — **do not remove them unless the user wants the new Laravel behaviour**:

| Item | Why |
|---|---|
| `resources/views/errors/minimal.blade.php` (copy of Laravel 10's) | Laravel 11+ redesigned the default 403/404/419/429/500/503 pages |
| `resources/views/vendor/laravel-filemanager/*` (LFM v2.9.1 views; inline CSS/JS read from `public/vendor/laravel-filemanager`) | LFM 2.15 (the only version supporting Laravel 13) ships a new UI |
| `App\Http\Middleware\VerifyCsrfToken::hasValidOrigin()` returns `false` (extends `PreventRequestForgery`) | Laravel 13 accepts token-less POSTs with `Sec-Fetch-Site: same-origin`; the app still requires a CSRF token |
| `config/hashing.php` `'rehash_on_login' => false` | Laravel 11+ may rewrite password hashes on login |
| `AnalyticsReport::summary()` casts `diffInSeconds(..., true)` to int | Carbon 3 returns signed floats |
| Migration `2026_10_16_000000_add_performance_indexes` uses `Schema::getIndexes()` | Doctrine DBAL removed in Laravel 11 (same index names, same result) |
| `yajra/laravel-datatables-oracle` instead of the `yajra/laravel-datatables` meta package | v13 meta package pulls Livewire + PhpSpreadsheet and adds routes |

Accepted, non-visible differences: response header `charset=utf-8` is now lowercase; LFM's `Route::any` upload route also lists the new `QUERY` verb; the LFM JS language object contains 5 extra unused keys; DataTables JSON key order follows the SQLite test schema. Session serialization stays `php`, cache/session prefixes and cookie name are unchanged (app config files define them), so active sessions survive the deploy. Guard tests: `tests/Feature/UpgradeCompatibilityTest.php`, `tests/Feature/CsrfProtectionTest.php`.

---

## 13. Rules for agents

**Do**
- Match surrounding style: PSR-4 with the Laravel 10-style structure the app keeps on Laravel 13, Indonesian code comments (existing convention), English identifiers.
- Use `Article::published()` for all public article queries.
- Keep bilingual pairs (`_id` required, `_en` optional) in migrations, models (`$translatable`, `$fillable`), controller validation, admin modals (both language inputs), public views, and `lang/{id,en}` files.
- Add every new admin `__('…')` string to `lang/id.json`; keep `lang/id/*.php` ↔ `lang/en/*.php` keys identical.
- Use `ManagesPublicFiles` for uploads; store relative paths; render with `asset('storage/…')`.
- Sanitize any rich HTML with `HtmlSanitizer::clean()`; escape everything else (`{{ }}`).
- Rebuild and commit `public/front/css/app.css` after Tailwind class changes.
- Keep route names unique; keep role checks consistent across routes, sidebar, controllers and tests.
- Run `php artisan test` before declaring work done; report failures honestly.
- Update `.ai/handoff.md` before ending a session.

**Don't**
- Don't run destructive DB commands, read/print secrets, or commit `.env*` files.
- Don't use the Tailwind Play CDN, `@vite` on public pages, or add new CDN libraries without need.
- Don't edit an already-run migration; add a new, idempotent one (MySQL + SQLite compatible).
- Don't merge histories between `origin` and `solveit`; cherry-pick instead. Don't sync README across them.
- Don't add `Co-Authored-By` trailers; follow the `(type) summary` commit format.
- Don't mass-reformat files or upgrade dependencies/framework without an explicit request.
