<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Contact\UsContact;
use App\Models\Home\AboutHome;
use App\Models\Home\BusinessHome;
use App\Models\Home\GalleryHome;
use App\Models\Home\HomeMenu;
use App\Models\Home\ServiceHome;
use App\Models\RRfuture\ProductRRfuture;
use App\Models\RRfuture\RRfutureMenu;
use App\Models\Service\OurService;
use App\Models\Service\ServiceMenu;
use App\Support\Seo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Endpoint untuk mesin pencari & mesin AI: sitemap.xml, robots.txt, llms.txt dan RSS.
 */
class SeoController extends Controller
{
    private const CACHE = 'public, max-age=3600';

    public function sitemap()
    {
        $entries = collect([
            ['url' => url('/'), 'lastmod' => $this->latest([HomeMenu::class, AboutHome::class, ServiceHome::class, BusinessHome::class, GalleryHome::class]), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => route('rrfuture'), 'lastmod' => $this->latest([RRfutureMenu::class, ProductRRfuture::class]), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => route('service'), 'lastmod' => $this->latest([ServiceMenu::class, OurService::class]), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => route('blog'), 'lastmod' => $this->latestArticleDate(), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('contact'), 'lastmod' => $this->latest([UsContact::class]), 'priority' => '0.6', 'changefreq' => 'yearly'],
        ]);

        // kategori yang punya artikel terbit
        Category::whereHas('articles', fn ($query) => $query->published())->get()->each(function ($category) use ($entries) {
            $entries->push([
                'url' => route('blog'),
                'query' => ['category' => $category->slug],
                'lastmod' => $this->latestArticleDate($category->id),
                'priority' => '0.5',
                'changefreq' => 'weekly',
            ]);
        });

        Article::published()->latest('publish_date')->get()->each(function (Article $article) use ($entries) {
            $entries->push([
                'url' => route('detail_blog', $article->slug),
                'lastmod' => collect([$article->updated_at, $article->publish_date])->filter()->max(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'image' => ['loc' => Seo::absoluteAsset('storage/' . $article->img), 'title' => $article->title_id],
            ]);
        });

        return response()
            ->view('seo.sitemap', ['entries' => $entries, 'locales' => array_keys(config('seo.locales'))])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', self::CACHE);
    }

    public function robots()
    {
        $disallow = config('seo.disallow');
        $lines = ['# ' . config('seo.organization.name') . ' — ' . url('/'), ''];

        $group = function (string $agent) use (&$lines, $disallow) {
            $lines[] = 'User-agent: ' . $agent;
            $lines[] = 'Allow: /';
            foreach ($disallow as $path) {
                $lines[] = 'Disallow: ' . $path;
            }
            $lines[] = '';
        };

        $group('*');

        // Generative Engine Optimization: crawler AI diizinkan membaca konten publik
        $lines[] = '# AI search & assistant crawlers are welcome to read public content.';
        foreach (config('seo.ai_crawlers') as $agent) {
            $group($agent);
        }

        $lines[] = 'Sitemap: ' . route('sitemap');

        return response(implode("\n", $lines) . "\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', self::CACHE);
    }

    /**
     * llms.txt (https://llmstxt.org) — ringkasan faktual situs dalam Markdown agar mudah
     * dipahami & dikutip oleh mesin AI (ChatGPT, Claude, Perplexity, Gemini, dll).
     */
    public function llms()
    {
        $org = config('seo.organization');
        $locale = app()->getLocale();
        $link = fn (string $url) => Seo::localizedUrl($url);

        $lines = [
            '# ' . $org['name'] . ' (' . config('seo.site_name') . ')',
            '',
            '> ' . __('index.seo.description'),
            '',
            '- ' . __('seo.llms.legal_name') . ': ' . $org['legal_name'],
            '- ' . __('seo.llms.website') . ': ' . url('/'),
            '- ' . __('seo.llms.languages') . ': Bahasa Indonesia, English (' . __('seo.llms.language_hint') . ')',
            '- ' . __('seo.llms.area_served') . ': ' . implode(', ', config('seo.geo.area_served')),
        ];

        if ($address = Seo::streetAddress()) {
            $lines[] = '- ' . __('seo.llms.address') . ': ' . $address;
        }

        $lines[] = '- ' . __('seo.llms.contact') . ': ' . $org['phone'] . ' · ' . $org['email'];
        $lines[] = '';

        $lines[] = '## ' . __('seo.llms.pages');
        $lines[] = '- [' . __('blog.seo.breadcrumb.home') . '](' . $link(url('/')) . '): ' . __('index.seo.og_description');
        $lines[] = '- [RR Future Tech](' . $link(route('rrfuture')) . '): ' . __('rrfuture.seo.description');
        $lines[] = '- [RR Vision](' . $link(route('service')) . '): ' . __('service.seo.description');
        $lines[] = '- [Blog](' . $link(route('blog')) . '): ' . __('blog.seo.index.description');
        $lines[] = '- [' . __('contact.seo.breadcrumb') . '](' . $link(route('contact')) . '): ' . __('contact.seo.description');
        $lines[] = '';

        $this->appendList($lines, __('seo.llms.services'), ServiceHome::all());
        $this->appendList($lines, 'RR Future Tech', ProductRRfuture::all());
        $this->appendList($lines, 'RR Vision', OurService::all());

        $articles = Article::published()->with('Category')->latest('publish_date')->take(20)->get();
        if ($articles->isNotEmpty()) {
            $lines[] = '## ' . __('seo.llms.articles');
            foreach ($articles as $article) {
                $lines[] = '- [' . $article->title . '](' . $link(route('detail_blog', $article->slug)) . ')'
                    . ' (' . optional($article->publish_date)->toDateString() . ')'
                    . ': ' . Seo::clean($article->seoDescription(), 200);
            }
            $lines[] = '';
        }

        $lines[] = '## ' . __('seo.llms.optional');
        $lines[] = '- [Sitemap](' . route('sitemap') . ')';
        $lines[] = '- [RSS](' . route('feed', ['lang' => $locale]) . ')';
        $lines[] = '- [' . __('seo.llms.other_language') . '](' . route('llms', ['lang' => $locale === 'id' ? 'en' : 'id']) . ')';

        return response(implode("\n", $lines) . "\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', self::CACHE);
    }

    public function feed()
    {
        $articles = Article::published()->with(['Category', 'Creator'])->latest('publish_date')->take(30)->get();

        return response()
            ->view('seo.feed', [
                'articles' => $articles,
                'updated' => $articles->first()?->publish_date ?? now(),
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->header('Cache-Control', self::CACHE);
    }

    private function appendList(array &$lines, string $heading, Collection $items): void
    {
        if ($items->isEmpty()) {
            return;
        }

        $lines[] = '## ' . $heading;
        foreach ($items as $item) {
            $description = Seo::clean($item->desc, 240);
            $lines[] = '- ' . $item->name . ($description !== '' ? ': ' . $description : '');
        }
        $lines[] = '';
    }

    private function latest(array $models): ?Carbon
    {
        return collect($models)
            ->map(fn ($model) => $model::max('updated_at'))
            ->filter()
            ->map(fn ($date) => Carbon::parse($date))
            ->max();
    }

    private function latestArticleDate(?int $categoryId = null): ?Carbon
    {
        $date = Article::published()
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->max('updated_at');

        return $date ? Carbon::parse($date) : null;
    }
}
