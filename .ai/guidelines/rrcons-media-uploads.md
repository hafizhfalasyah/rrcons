## RRCONS — uploads & media

- Use the `App\Http\Controllers\Concerns\ManagesPublicFiles` trait: `storePublicFile($file, 'back/<area>/<section>')`, `replacePublicFile($oldPath, $file, $dir)`, `deletePublicFile($path, $legacyDir)`.
- Files live on the `public` disk (`storage/app/public`), named `uniqid().ext`. The DB stores the **relative path** (e.g. `back/home/hero/64f1c2.jpg`); views render `asset('storage/' . $path)`. Requires `php artisan storage:link`.
- Directories in use: `back/blog`, `back/home/{poster,hero,about,profile,business,sosmed,gallery}`, `back/rrfuture/hero`, `back/service/hero`, `back/contact/hero`.
- Images (jpg/jpeg/png/webp) are optimized after upload by `App\Support\ImageOptimizer` (max side 2560 px, quality 85, kept only if ≥5% smaller, failures never block the upload). Toggle with `OPTIMIZE_UPLOADED_IMAGES`. Batch for old files: `php artisan rrcons:optimize-images --dry-run` (backs up originals, `--restore=<dir>`).
- Validation conventions: images `image|mimes:...|max:2048`; sosmed video `mimetypes:video/mp4,video/mpeg,video/x-matroska,video/quicktime|max:51200`.
- Rich-text images go through UniSharp Laravel File Manager at `/laravel-filemanager` (routes registered once inside the admin/writer group, `use_package_routes=false`, disk `public`).
- Static public assets: `public/front/image`, `public/front/images`; reference with `asset()` or `asset_version()` (adds `?v=<mtime>`).
