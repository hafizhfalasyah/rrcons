---
name: rrcons-bilingual-content
description: Step-by-step recipe for adding or changing a translatable (Indonesian/English) CMS field or section in RRCONS - migration with _id/_en columns, model $translatable/$fillable, ValidatesTranslations rules, admin Bootstrap modal inputs for both languages, public Blade output, lang files and tests.
---

# Bilingual CMS field / section recipe

Conventions: `<field>_id` = Indonesian (required, primary), `<field>_en` = English (optional, falls back to `_id` on the public site). Logical name `<field>` is what views read.

## 1. Migration (new file, never edit an old one)
```php
// database/migrations/YYYY_MM_DD_HHMMSS_add_<field>_to_<table>.php
public function up(): void
{
    if (Schema::hasColumn('<table>', '<field>_id')) {
        return; // idempotent
    }
    Schema::table('<table>', function (Blueprint $table) {
        $table->string('<field>_id')->nullable();
        $table->string('<field>_en')->nullable()->after('<field>_id');
    });
}
```
- Renaming an existing column to `_id`: one `renameColumn` per `Schema::table` call (SQLite), guard with `Schema::hasColumn` (see `2026_10_13_000000_add_translations_to_cms_content.php`).
- Must run on MySQL (production) and SQLite (tests).

## 2. Model
```php
use App\Models\Concerns\HasTranslations;

class XxxHome extends Model
{
    use HasTranslations;

    protected $translatable = ['title', '<field>'];
    protected $fillable = [/* ... */ '<field>_id', '<field>_en'];
}
```
`$model-><field>` now returns the active-locale value with Indonesian fallback. Always write the physical columns.

## 3. Admin controller
```php
use App\Http\Controllers\Concerns\ValidatesTranslations;

$data = $this->validateWithTranslations($request, [
    '<field>' => 'required|string|max:255',     // rules for _id; _en becomes nullable + same rules
], [
    'img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // non-translated fields
]);
```
Files: use `ManagesPublicFiles` (`storePublicFile` / `replacePublicFile($old, $file, 'back/<area>/<section>')`).
Flash messages: `->with('success', __('… updated successfully.'))` and add the English key with its Indonesian translation to `lang/id.json`.

## 4. Routes & access
Add inside the right `Route::prefix(...)->name(...)->middleware('UserAccess:...')` group in `routes/web.php`; unique route name; mirror access in `resources/views/back/layout/sidebar.blade.php`; extend `tests/Feature/AccessControlTest.php` matrix.

## 5. Admin views (`resources/views/back/<area>_menu/<section>_section/`)
- Everything lives in `index.blade.php`: `<x-admin.page-header>` (description, `location`, `preview-url`, `guide` tips), the list (use `<x-admin.bilingual>` for ID/EN text, `<x-admin.updated-at>`), then one `<x-admin.form-modal :id="'edit-x-' . $item->id">` per row.
- Inside the modal: `<x-admin.translatable :key="..." :item="$item" :fields="[['name' => '<field>', 'label' => __('...'), 'required' => true, 'max' => 255, 'hint' => __('...')]]" />` renders the Indonesian/English tabs and `<field>_id` / `<field>_en` inputs; `<x-admin.media-input>` for pictures/videos. Examples: `home_menu/about_section/index.blade.php`, `home_menu/gallery_section/form.blade.php`.
- Wrap all UI text in `__('English text')` and add to `lang/id.json`.

## 6. Public view
- Read the logical attribute: `{{ $row-><field> }}`. For headings with the colored last word: `{!! highlightLastWord($row->homeMenu->title ?? '') !!}` (helper escapes).
- Static UI strings go in `lang/id/<page>.php` and `lang/en/<page>.php` with identical keys.
- Be null-safe: CMS tables may be empty on fresh databases.
- If you add Tailwind classes, run `npm run build:front-css`.

## 7. Tests
Extend `tests/Feature/MultiLanguageTest.php` / `ContentManagementTest.php`: admin saves both languages; `/?lang=en` shows English; empty English falls back to Indonesian. Run `php artisan test`.
