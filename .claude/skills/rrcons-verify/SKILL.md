---
name: rrcons-verify
description: Verification checklist for RRCONS changes. Use before reporting a task as done or before committing - runs the PHPUnit suite and checks Tailwind build, translations, route names, migrations and role-access consistency according to what changed.
---

# RRCONS verification

Run only the checks relevant to what changed, then report results honestly (paste failing output).

## 1. Always
```bash
git status -sb && git diff --stat
php artisan test
```
Tests run on SQLite `:memory:` (forced by `phpunit.xml`), so they never touch the real database.

## 2. Conditional checks

| If you changed… | Then |
|---|---|
| Tailwind classes in `resources/views/front/**`, `public/front/js/**`, `app/Helpers/**` | Clean `npm run build:front-css` (the Docker watcher keeps removed classes) and include `public/front/css/app.css` in the commit (`PerformanceTest` checks key classes) |
| Admin text `__('…')` in `resources/views/back`, `app/Http/Controllers`, `app/Http/Requests` | Add the key to `lang/id.json` (`MultiLanguageTest`) |
| `lang/id/*.php` or `lang/en/*.php` | Same keys in both locales (except `validation.php`) |
| `routes/web.php` | Route names unique (`php artisan route:list` + `PerformanceTest`); role middleware matches `back/layout/sidebar.blade.php` and `AccessControlTest` |
| A migration | Works on SQLite and MySQL; one `renameColumn` per `Schema::table`; guarded with `Schema::hasColumn`; never edit a migration that already ran in production |
| Public article queries | Uses `Article::published()` |
| Rich HTML input/output | Saved through `HtmlSanitizer::clean()`, rendered via `safe_desc` or escaped |
| Uploads | Uses `ManagesPublicFiles`, stores relative path, old file removed on replace |
| Public page markup (navbar/footer/etc.) | Same change applied to all 6 standalone pages if shared |

## 3. Optional manual check
`php artisan serve` and open `/`, `/?lang=en`, `/blog`, `/dashboard` (needs a user with a role). Do not run destructive DB commands to create data; the local `.env` may point to a real database.

## 4. Report
List: tests (pass/fail counts), which conditional checks applied and their result, anything not verified.
