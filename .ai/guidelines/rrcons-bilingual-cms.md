## RRCONS — bilingual CMS content

- Translatable fields are stored as `<field>_id` (Indonesian, required) and `<field>_en` (English, optional). Models declare `protected $translatable = [...]` and `use App\Models\Concerns\HasTranslations;` — reading `$model->field` returns the active locale with fallback to `_id`. Always write the physical `_id`/`_en` columns.
- Validate with `App\Http\Controllers\Concerns\ValidatesTranslations::validateWithTranslations($request, $translatedRules, $plainRules)`; English gets `nullable` automatically and is stored as `null` when empty.
- Locale: `?lang=id|en` → session `app_locale` → `config('app.locale')`. `GET /set-language/{lang}` switches and redirects back. `SetLocale` middleware also sets Carbon's locale.
- CMS pattern: each page has a `*_menus` table (section headings `title`/`sub_title`) and section tables with `*_menu_id` FK (`HomeMenu` → `HeroHome`, `AboutHome`, `ServiceHome`, `AdvantageHome`, `ProfileHome`, `BusinessHome`, `SosmedHome`, `GalleryHome`; `RRfutureMenu` → `HeroRRfuture`, `ProductRRfuture`; `ServiceMenu` → `HeroService`, `OurService`; `ContactMenu` → `HeroContact`, `UsContact`). `PosterHome` has no menu.
- CMS rows are edit-only; only posters and gallery items have create/delete. There are no CMS seeders — real content comes from the production DB.
- Not translated on purpose: `ProfileHome.name`, `UsContact.title` (fixed key such as `Alamat`, used by the contact page and `Seo::streetAddress()`), URLs, media paths.
- Strings: public pages use grouped keys in `lang/{id,en}/<page>.php` (identical key sets, test-enforced); admin uses `__('English text')` translated in `lang/id.json` (every literal key must exist there, test-enforced).
- Admin modals show two inputs per field: `Title (Indonesia)` / `Title (English)` (see `resources/views/back/home_menu/advantage_section/updatemodal.blade.php`).
