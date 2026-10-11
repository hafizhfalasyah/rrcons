## RRCONS — testing

- Run `php artisan test`. `phpunit.xml` forces SQLite `:memory:` (needs `pdo_sqlite` and `gd`), array cache/session, so tests never touch the real database. PHP 8.5 vendor `Deprecated:` notices are noise.
- Baseline (2026-10-11): 77 tests, 759 assertions, all passing.
- Suites: `AccessControlTest`, `ArticleSchedulingTest`, `ContentManagementTest`, `MultiLanguageTest`, `SeoTest`, `AnalyticsTest`, `PerformanceTest`, `LegacyTimezoneFixTest`, `Unit/HtmlSanitizerTest`.
- Guard-rail tests to keep green: role matrix; `lang/id.json` covers all admin `__()` literals; `lang/id` ↔ `lang/en` key parity; compiled CSS contains used classes and no Play CDN; unique route names; home menus loaded once; sitemap/feed only contain published articles.
- Style: PHPUnit classes in `tests/Feature`, `use RefreshDatabase;`, method names `test_descriptive_snake_case()`. Users via `User::factory()->create(['role' => User::ROLE_ADMIN])`; other models are created directly (no other factories).
- Every behaviour change needs a test in the matching suite.
