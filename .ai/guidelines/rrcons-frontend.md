## RRCONS — frontend

### Public site (`resources/views/front`)
- Six **standalone** Blade documents (`index`, `rrfuture`, `service`, `blog`, `detail-blog`, `contact`) — no shared layout. Navbar, mobile menu, language switcher, footer and WhatsApp widget are duplicated; apply shared changes to all six.
- Each page includes `front.partials.seo` (with `$seo` from `App\Support\Seo::<page>()`) and `front.partials.analytics` (pass `['article' => $article]` on the article page). AJAX partials: `front.partials.articles`, `front.partials.article_list`.
- Styling: Tailwind 3.4.17 utilities compiled with `npm run build:front-css` into `public/front/css/app.css` (committed, loaded via `asset_version()`). After changing classes in front views, `public/front/js`, or `app/Helpers`, run a clean `npm run build:front-css` and commit the CSS (the Docker `node` watcher only ever adds classes). Never use the Tailwind Play CDN.
- CDN libs: Alpine.js 3.17.4, AOS 2.3.4, Feather icons, Google Fonts Poppins. Shared script: `public/front/js/index.js` (mobile menu, WhatsApp box, carousel, etc.).
- Never write a literal `<?` (other than `<?php`) in a Blade template: Blade uses PHP's tokenizer, so with `short_open_tag=On` it becomes a PHP tag (see `resources/views/seo/sitemap.blade.php` for the split XML prolog).
- Output: `{{ }}` by default; `{!! highlightLastWord(...) !!}` is safe (helper escapes); article body only via `safe_desc`.
- **Loading screen** `resources/views/partials/page-loader.blade.php` (self-contained inline CSS/JS, no Tailwind/Bootstrap): no logo; white site background, thin blue-600 progress ring on a blue-100 track with the live percentage inside, status line + skip hint below (`lang/{id,en}/loader.php`). The ring is the skip button (hover/focus turns it into a blue "Skip" disc); when done the overlay opens as a circle growing from the ring (mask animation, 700 ms). Progress = DOM ready, then non-lazy `<img>` loaded/total, then `window.load` (12 s safety cap). Click/tap the ring or Esc skips; reappears on same-site link clicks and form submits after 150 ms (skipped for `target`≠`_self`, `download`, `#anchor`, modifier keys, `preventDefault()` handlers, or `data-no-loader` on the link/form); hidden on bfcache restore, in background tabs and without JS. Include it **right after `<body>`** in every standalone document (6 public pages, `back/layout/template`, `auth/login`) — `PageLoaderTest` enforces it. Error pages and LFM views are pinned (see AGENTS.md §12a) and have no loader.
- `public/front/css/tailwind.css` is a legacy unused 4 MB file; the Vite entries (`resources/js`, `resources/sass`) are not used by any view.

### Admin (`resources/views/back`)
- `@extends('back.layout.template')`, `@section('title')`, `@section('content')`, `@push('css')`/`@push('js')`.
- CDN: Bootstrap 5.2.3 + Icons, jQuery 3.7.1, DataTables 2.1.8, SweetAlert2 11, Chart.js 4.4.4 (dashboard), CKEditor 5 41.2.1 (articles), Feather icons.
- Build admin pages with the components in `resources/views/components/admin/`: `page-header` (+ `guide` tips for non-technical admins, location, preview link), `form-modal`, `translatable` (ID/EN tabs), `media-input`, `delete-modal`, `bilingual`, `updated-at`. Every page and form field gets a short plain-language note.
- Styling lives in `public/back/css/admin.css` (Poppins, `#2563eb`). Bootstrap is 5.2.3: do not use 5.3-only classes.
- All text via `__('English')` with Indonesian in `lang/id.json` (also strings inside components; sidebar labels are passed through `__()` too).
- Sidebar (`layout/sidebar.blade.php`) hides menus per role; keep it aligned with route middleware. Logout must stay in the always-visible `.sidebar-footer`.
