## RRCONS — core project rules

- Laravel **13.35** (PHP ^8.5, Laravel 10-style app structure kept) company-profile + CMS for RR Consultant Indonesia (RRCONS). Public site is bilingual (Indonesian primary, English optional); admin panel is Bootstrap 5. Production = shared hosting; Docker = local dev only.
- Full guide: `AGENTS.md`. Work log for handoffs: `.ai/handoff.md` — read it first, update it before stopping.
- All public pages are served by `App\Http\Controllers\Front\HomeController`; SEO endpoints by `Front\SeoController`; analytics beacons by `Front\AnalyticsController`. `Front\ArticleController` and `Front\CategoryController` are dead code.
- Admin controllers live in `App\Http\Controllers\Back\{Home,RRfuture,Service,Contact,Blog}`; admin routes use `POST /{area}/{section}/{id}/update` and `DELETE /{area}/{section}/{id}/delete`.
- Role access via `->middleware('UserAccess:admin,writer')` (roles: `admin`, `writer`, `uploader`; `null` role = 403). Keep `routes/web.php`, `resources/views/back/layout/sidebar.blade.php` and `tests/Feature/AccessControlTest.php` consistent.
- Route names must be unique (route cache).
- Code comments are written in Indonesian; identifiers in English. Match surrounding style; do not mass-format with Pint.
- Never run `migrate:fresh|reset|refresh`, `db:wipe` or `db:seed` against the configured database; never read/print/commit `.env*`.
- Migrations: add new idempotent files only (guard with `Schema::hasColumn`/`hasTable`), compatible with MySQL and SQLite; one `renameColumn` per `Schema::table`.
- Git: remotes `origin` (hafizhfalasyah/rrcons) and `solveit` (solveit-id/rrcons) have unrelated histories — port with cherry-pick. Commit format `(add|update|fix|perf|test|chore) lowercase summary`, no `Co-Authored-By` trailer.
- Run `php artisan test` (SQLite in-memory) before declaring work done.
- Laravel 10→13 upgrade pins (keep them): Laravel 10 error layout `resources/views/errors/minimal.blade.php`, LFM v2.9.1 views in `resources/views/vendor/laravel-filemanager`, CSRF token always required (`VerifyCsrfToken::hasValidOrigin()` = false), `hashing.rehash_on_login = false`. See AGENTS.md §12a.
