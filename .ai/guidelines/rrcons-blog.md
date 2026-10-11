## RRCONS — blog

- Models: `App\Models\Blog\{Article, Category, Comment, Config}`. Article relations are capitalized: `Category()`, `User()` (last editor, `user_id`), `Creator()` (original author, `created_by`), plus `comments()`.
- Publication: admin choice `publication` ∈ `private|publish|schedule` → `Article::publicationAttributes()` sets `status` (`'0'`/`'1'`) and `publish_date` (datetime). Scheduled posts need no cron: visibility is computed by the `published()` scope (`status = '1'` and `publish_date` null or ≤ now).
- **Every public article query must use `Article::published()`** (pages, AJAX lists, search, sitemap, feed, llms.txt, comment posting).
- Slugs: `Article::uniqueSlug($title_id, $ignoreId)` / `Category::uniqueSlug(...)`; unique DB index; article slugs `articles`, `categories`, `configs`, `comments` are reserved. The slug follows the Indonesian title; previous slugs are kept in `article_slug_redirects` (`ArticleSlugRedirect`, written by `Article::booted()`) and `/blog/{old-slug}` returns a 301 to the current URL. Never delete those rows by hand — they keep shared links and search results working.
- Content HTML from CKEditor 5 is sanitized on save with `App\Support\HtmlSanitizer::clean()` and rendered with `{!! $article->safe_desc !!}`.
- SEO per article: `meta_title_{id,en}` (≤70), `meta_description_{id,en}` (≤320); `seoTitle()`/`seoDescription()` fall back to title / cleaned excerpt.
- Requests: `ArticleRequest` (image required) / `UpdateArticleRequest` (image optional), `publish_date` required only for `schedule` and must be in the future.
- Admin list uses server-side DataTables (yajra) in `Back\Blog\ArticleController@index` (`request()->ajax()`); delete returns JSON for SweetAlert.
- Categories with articles cannot be deleted (FK without cascade). `views` is incremented by the analytics tracker, once per visitor/article/day.
- Comments are moderated: new ones are `pending` (`Comment::STATUS_*`), only `Comment::approved()` is rendered/counted publicly; admin + writer use `/blog/comments` (`Back\Blog\CommentController`) to approve, mark spam, move back to pending or delete. Use `$comment->moderate($status, $user)` so `moderated_by/at` are recorded.
