## RRCONS — analytics & SEO

### Visitor analytics (first-party, cookieless)
- `front.partials.analytics` embeds an HMAC-signed page token from `App\Support\Analytics\Tracker::issue()`; browser JS posts it to `POST /_v/hit` (sendBeacon, `text/plain` JSON) and heartbeats to `POST /_v/ping` every 15 s. Both are CSRF-exempt, `throttle:120,1`, and always return 204.
- Rows go to `page_views` (`App\Models\PageView`): daily `visitor_hash` (no raw IP), path, route, article, locale, source (search/ai/social/referral/direct), UTM, device/browser/OS, country (only from a trusted CDN header), `engaged_seconds`, `tracker` (`js`|`legacy`).
- Ignored: bots (`config('analytics.bot_pattern')`, also checked client-side), logged-in users, `ANALYTICS_EXCLUDE_IPS`. Behind Cloudflare/proxy set `TRUSTED_PROXIES=*` and optionally `ANALYTICS_COUNTRY_HEADER=CF-IPCountry`.
- Dashboard: `App\Support\Analytics\AnalyticsReport` (ranges `today|7d|30d|90d`), realtime JSON at `/dashboard/realtime`. Legacy `visitors` table is history only (imported once by `LegacyVisitorImporter`). Retention prune: `php artisan rrcons:prune-page-views --dry-run`.

### SEO / GEO
- `App\Support\Seo` builds title/description, canonical (keeps meaningful query like `category`, `page`), hreflang alternates via `?lang=`, Open Graph/Twitter, and a JSON-LD graph (Organization, WebSite, WebPage/CollectionPage, Blog/BlogPosting, Service ItemList, BreadcrumbList). New public pages must pass `'seo' => Seo::...` and include `front.partials.seo`.
- Organization & geo data: `config/seo.php` + `SEO_*` env (leave unknown values empty — empty values are omitted).
- `GET /sitemap.xml`, `/robots.txt` (blocks admin paths, welcomes AI crawlers), `/llms.txt` (Markdown summary for AI engines), `/feed` (RSS). When adding admin paths, extend `config('seo.disallow')`.
- Blog search results (`?q=`) are `noindex`; admin and login pages are not indexed.
