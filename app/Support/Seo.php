<?php

namespace App\Support;

use App\Models\Blog\Article;
use App\Models\Contact\UsContact;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Data SEO satu halaman publik: meta tag, Open Graph, Twitter, hreflang dan JSON-LD.
 * Dirender oleh resources/views/front/partials/seo.blade.php.
 */
class Seo
{
    public string $title;
    public string $description;
    public ?string $keywords = null;
    public ?string $image = null;
    public ?string $imageAlt = null;
    public string $type = 'website';
    public string $schemaType = 'WebPage';
    public bool $index = true;

    /** Query string yang membentuk halaman berbeda (dipertahankan di canonical) */
    public array $canonicalQuery = [];

    /** article:published_time, dll. */
    public array $article = [];

    /** [['name' => ..., 'url' => ...], ...] */
    public array $breadcrumbs = [];

    /** Node JSON-LD tambahan */
    public array $schema = [];

    public static function make(string $title, string $description): self
    {
        $seo = new self();
        $seo->title = $title;
        $seo->description = self::clean($description, 160);

        return $seo;
    }

    /*
    |--------------------------------------------------------------------------
    | Halaman
    |--------------------------------------------------------------------------
    */

    public static function home(Collection $services): self
    {
        $seo = self::make(__('index.seo.title'), __('index.seo.description'));
        $seo->keywords = __('index.seo.keywords');
        $seo->schema[] = self::itemList(__('index.seo.services_heading'), $services->map(fn ($service) => [
            '@type' => 'Service',
            'name' => $service->name,
            'description' => self::clean($service->desc, 300),
            'provider' => ['@id' => self::organizationId()],
            'areaServed' => self::areaServed(),
        ])->values()->all());

        return $seo;
    }

    public static function blogIndex($articles, ?string $categorySlug = null, ?string $query = null): self
    {
        $seo = self::make(__('blog.seo.index.title'), __('blog.seo.index.description'));
        $seo->keywords = __('blog.seo.index.keywords');
        $seo->schemaType = 'CollectionPage';
        $seo->canonicalQuery = array_filter(['category' => $categorySlug, 'page' => request('page') > 1 ? (int) request('page') : null]);
        // hasil pencarian tidak perlu diindeks
        $seo->index = blank($query);
        $seo->breadcrumbs = [
            ['name' => __('blog.seo.breadcrumb.home'), 'url' => self::localizedUrl(url('/'))],
            ['name' => __('blog.seo.breadcrumb.blog'), 'url' => self::localizedUrl(route('blog'))],
        ];
        $seo->schema[] = [
            '@type' => 'Blog',
            '@id' => route('blog') . '#blog',
            'name' => __('blog.seo.index.title'),
            'description' => __('blog.seo.index.description'),
            'url' => route('blog'),
            'inLanguage' => self::hreflang(),
            'publisher' => ['@id' => self::organizationId()],
            'blogPost' => collect($articles->items())->map(fn (Article $article) => self::blogPostingSummary($article))->all(),
        ];

        return $seo;
    }

    public static function article(Article $article): self
    {
        $seo = self::make(
            $article->seoTitle(),
            $article->seoDescription()
        );
        $seo->type = 'article';
        $seo->schemaType = 'WebPage';
        $seo->keywords = optional($article->Category)->name;
        $seo->image = self::absoluteAsset('storage/' . $article->img);
        $seo->imageAlt = $article->title;
        $seo->article = array_filter([
            'published_time' => optional($article->publish_date)->toIso8601String(),
            'modified_time' => optional($article->updated_at)->toIso8601String(),
            'section' => optional($article->Category)->name,
            'author' => optional($article->Creator)->name,
        ]);
        $seo->breadcrumbs = [
            ['name' => __('blog.seo.breadcrumb.home'), 'url' => self::localizedUrl(url('/'))],
            ['name' => __('blog.seo.breadcrumb.blog'), 'url' => self::localizedUrl(route('blog'))],
            ['name' => $article->title, 'url' => self::localizedUrl(route('detail_blog', $article->slug))],
        ];

        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $article->desc)));

        $seo->schema[] = array_filter([
            '@type' => 'BlogPosting',
            '@id' => route('detail_blog', $article->slug) . '#article',
            'headline' => Str::limit($article->title, 110, ''),
            'description' => $seo->description,
            'image' => [$seo->image],
            'datePublished' => optional($article->publish_date)->toIso8601String(),
            'dateModified' => optional($article->updated_at)->toIso8601String(),
            'inLanguage' => self::hreflang(),
            'articleSection' => optional($article->Category)->name,
            'wordCount' => str_word_count($text),
            'author' => $article->Creator
                ? ['@type' => 'Person', 'name' => $article->Creator->name]
                : ['@id' => self::organizationId()],
            'publisher' => ['@id' => self::organizationId()],
            'mainEntityOfPage' => ['@id' => self::localizedUrl(route('detail_blog', $article->slug)) . '#webpage'],
            'isPartOf' => ['@id' => route('blog') . '#blog'],
        ]);

        return $seo;
    }

    public static function rrfuture(Collection $products): self
    {
        $seo = self::make(__('rrfuture.seo.title'), __('rrfuture.seo.description'));
        $seo->keywords = __('rrfuture.seo.keywords');
        $seo->breadcrumbs = [
            ['name' => __('blog.seo.breadcrumb.home'), 'url' => self::localizedUrl(url('/'))],
            ['name' => 'RR Future Tech', 'url' => self::localizedUrl(route('rrfuture'))],
        ];
        $seo->schema[] = self::itemList('RR Future Tech', $products->map(fn ($product) => [
            '@type' => 'Service',
            'name' => $product->name,
            'description' => self::clean($product->desc, 300),
            'serviceType' => 'Software development',
            'provider' => ['@id' => self::organizationId()],
            'areaServed' => self::areaServed(),
        ])->values()->all());

        return $seo;
    }

    public static function service(Collection $services): self
    {
        $seo = self::make(__('service.seo.title'), __('service.seo.description'));
        $seo->keywords = __('service.seo.keywords');
        $seo->breadcrumbs = [
            ['name' => __('blog.seo.breadcrumb.home'), 'url' => self::localizedUrl(url('/'))],
            ['name' => 'RR Vision', 'url' => self::localizedUrl(route('service'))],
        ];
        $seo->schema[] = self::itemList('RR Vision', $services->map(fn ($service) => [
            '@type' => 'Service',
            'name' => $service->name,
            'description' => self::clean($service->desc, 300),
            'provider' => ['@id' => self::organizationId()],
            'areaServed' => self::areaServed(),
        ])->values()->all());

        return $seo;
    }

    public static function contact(): self
    {
        $seo = self::make(__('contact.seo.title'), __('contact.seo.description'));
        $seo->keywords = __('contact.seo.keywords');
        $seo->schemaType = 'ContactPage';
        $seo->breadcrumbs = [
            ['name' => __('blog.seo.breadcrumb.home'), 'url' => self::localizedUrl(url('/'))],
            ['name' => __('contact.seo.breadcrumb'), 'url' => self::localizedUrl(route('contact'))],
        ];

        return $seo;
    }

    /*
    |--------------------------------------------------------------------------
    | URL & bahasa
    |--------------------------------------------------------------------------
    */

    public static function hreflang(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return config("seo.locales.$locale.hreflang", $locale);
    }

    public static function ogLocale(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return config("seo.locales.$locale.og", $locale);
    }

    /**
     * URL versi bahasa tertentu, mis. https://rrconsindonesia.com/blog?lang=en
     */
    public static function localizedUrl(string $url, ?string $locale = null, array $query = []): string
    {
        $query = array_filter($query, fn ($value) => $value !== null && $value !== '');
        $query['lang'] = $locale ?? app()->getLocale();

        return strtok($url, '?') . '?' . http_build_query($query);
    }

    public function canonical(): string
    {
        return self::localizedUrl(url()->current(), null, $this->canonicalQuery);
    }

    /**
     * hreflang => URL, termasuk x-default (tanpa parameter bahasa).
     */
    public function alternates(): array
    {
        $alternates = [];

        foreach (array_keys(config('seo.locales')) as $locale) {
            $alternates[self::hreflang($locale)] = self::localizedUrl(url()->current(), $locale, $this->canonicalQuery);
        }

        $query = http_build_query(array_filter($this->canonicalQuery));
        $alternates['x-default'] = url()->current() . ($query ? '?' . $query : '');

        return $alternates;
    }

    public function fullTitle(): string
    {
        $site = config('seo.site_name');

        return str_contains($this->title, $site) ? $this->title : $this->title . ' | ' . $site;
    }

    public function imageUrl(): string
    {
        return $this->image ?: self::absoluteAsset(config('seo.default_image'));
    }

    /*
    |--------------------------------------------------------------------------
    | JSON-LD
    |--------------------------------------------------------------------------
    */

    public function graph(): array
    {
        $pageUrl = $this->canonical();

        $webPage = array_filter([
            '@type' => $this->schemaType,
            '@id' => $pageUrl . '#webpage',
            'url' => $pageUrl,
            'name' => $this->fullTitle(),
            'description' => $this->description,
            'inLanguage' => self::hreflang(),
            'isPartOf' => ['@id' => url('/') . '#website'],
            'about' => ['@id' => self::organizationId()],
            'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $this->imageUrl()],
            'breadcrumb' => $this->breadcrumbs ? ['@id' => $pageUrl . '#breadcrumb'] : null,
        ]);

        $graph = [self::organization(), self::website(), $webPage];

        if ($this->breadcrumbs) {
            $graph[] = [
                '@type' => 'BreadcrumbList',
                '@id' => $pageUrl . '#breadcrumb',
                'itemListElement' => collect($this->breadcrumbs)->values()->map(fn ($crumb, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'],
                ])->all(),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => array_merge($graph, $this->schema),
        ];
    }

    public function jsonLd(): string
    {
        // JSON_HEX_TAG mencegah "</script>" dari konten menutup tag script
        return json_encode($this->graph(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    }

    public static function organizationId(): string
    {
        return url('/') . '#organization';
    }

    public static function organization(): array
    {
        $org = config('seo.organization');
        $geo = config('seo.geo');
        $address = self::streetAddress();

        return array_filter([
            '@type' => ['Organization', 'ProfessionalService'],
            '@id' => self::organizationId(),
            'name' => $org['name'],
            'legalName' => $org['legal_name'],
            'alternateName' => $org['alternate_names'],
            'url' => url('/'),
            'logo' => ['@type' => 'ImageObject', 'url' => self::absoluteAsset($org['logo'])],
            'image' => self::absoluteAsset($org['logo']),
            'description' => __('index.seo.description'),
            'email' => $org['email'],
            'telephone' => $org['phone'],
            'foundingDate' => $org['founding_date'],
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressLocality' => $geo['locality'],
                'addressRegion' => $geo['region_name'] ?? null,
                'postalCode' => $geo['postal_code'],
                'addressCountry' => $geo['country'],
            ]),
            'geo' => $geo['latitude'] && $geo['longitude'] ? [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $geo['latitude'],
                'longitude' => (float) $geo['longitude'],
            ] : null,
            'areaServed' => self::areaServed(),
            'contactPoint' => [[
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'telephone' => $org['phone'],
                'email' => $org['email'],
                'availableLanguage' => ['Indonesian', 'English'],
                'areaServed' => $geo['country'],
            ]],
            'department' => collect($org['departments'])->map(fn ($department) => [
                '@type' => 'Organization',
                'name' => $department['name'],
                'url' => url($department['path']),
                'parentOrganization' => ['@id' => self::organizationId()],
            ])->all(),
            'knowsLanguage' => ['id', 'en'],
            'sameAs' => $org['same_as'] ?: null,
        ]);
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => config('seo.organization.name'),
            'alternateName' => config('seo.site_name'),
            'inLanguage' => array_values(array_map(fn ($locale) => $locale['hreflang'], config('seo.locales'))),
            'publisher' => ['@id' => self::organizationId()],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => ['@type' => 'EntryPoint', 'urlTemplate' => route('blog') . '?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function blogPostingSummary(Article $article): array
    {
        return array_filter([
            '@type' => 'BlogPosting',
            '@id' => route('detail_blog', $article->slug) . '#article',
            'headline' => Str::limit($article->title, 110, ''),
            'url' => self::localizedUrl(route('detail_blog', $article->slug)),
            'image' => self::absoluteAsset('storage/' . $article->img),
            'datePublished' => optional($article->publish_date)->toIso8601String(),
            'dateModified' => optional($article->updated_at)->toIso8601String(),
        ]);
    }

    private static function itemList(string $name, array $items): array
    {
        return [
            '@type' => 'ItemList',
            'name' => $name,
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => array_filter($item),
            ])->all(),
        ];
    }

    private static function areaServed(): array
    {
        return collect(config('seo.geo.area_served'))
            ->map(fn ($area) => ['@type' => 'Country', 'name' => trim($area)])
            ->all();
    }

    /**
     * Alamat diambil dari CMS (Contact > Contact Us, entri "Alamat").
     */
    public static function streetAddress(): ?string
    {
        // di-cache per request
        $attributes = request()->attributes;

        if (!$attributes->has('seo.street_address')) {
            try {
                $address = optional(UsContact::where('title', 'Alamat')->first())->desc_id;
            } catch (\Throwable $e) {
                $address = null;
            }

            // alamat di CMS bisa berisi baris baru; satukan menjadi satu baris
            $address = trim(preg_replace('/\s*\n\s*/', ' ', (string) $address), " ,\t");
            $address = preg_replace('/\s{2,}/', ' ', $address);

            $attributes->set('seo.street_address', $address !== '' ? $address : null);
        }

        return $attributes->get('seo.street_address');
    }

    /**
     * URL absolut untuk file publik. asset() di proyek ini relatif (asset_url = "/"),
     * padahal Open Graph & structured data wajib memakai URL absolut.
     */
    public static function absoluteAsset(string $path): string
    {
        return url($path);
    }

    public static function clean(?string $text, int $limit): string
    {
        $text = html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim(preg_replace('/\s+/', ' ', $text)), $limit);
    }
}
