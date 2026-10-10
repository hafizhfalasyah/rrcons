<?php

namespace Tests\Feature;

use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Contact\ContactMenu;
use App\Models\Contact\UsContact;
use App\Models\RRfuture\ProductRRfuture;
use App\Models\RRfuture\RRfutureMenu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['name' => 'Rita Russo', 'role' => User::ROLE_ADMIN]);
        $this->category = Category::create(['name_id' => 'Bisnis', 'name_en' => 'Business', 'slug' => 'bisnis']);
    }

    private function article(string $slug, string $status = Article::STATUS_PUBLISH, $publishDate = null, array $extra = []): Article
    {
        return Article::create(array_merge([
            'user_id' => $this->admin->id,
            'created_by' => $this->admin->id,
            'category_id' => $this->category->id,
            'title_id' => 'Judul ' . $slug,
            'title_en' => 'Title ' . $slug,
            'slug' => $slug,
            'desc_id' => '<p>Isi artikel ' . $slug . ' yang informatif.</p>',
            'desc_en' => '<p>Informative article ' . $slug . '.</p>',
            'img' => 'back/blog/' . $slug . '.jpg',
            'status' => $status,
            'publish_date' => $publishDate ?? now()->subDay(),
        ], $extra));
    }

    private function jsonLd(string $html): array
    {
        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $match);
        $this->assertNotEmpty($match, 'JSON-LD block missing');

        $data = json_decode($match[1], true);
        $this->assertIsArray($data, 'JSON-LD is not valid JSON');

        return $data;
    }

    private function types(array $jsonLd): array
    {
        return collect($jsonLd['@graph'])->pluck('@type')->flatten()->all();
    }

    public function test_public_pages_have_complete_meta_tags_and_structured_data(): void
    {
        foreach (['/' => 'WebPage', '/rrfuture' => 'WebPage', '/service' => 'WebPage', '/blog' => 'CollectionPage', '/contact' => 'ContactPage'] as $uri => $pageType) {
            $html = $this->get($uri . '?lang=en')->assertOk()->getContent();

            $this->assertStringContainsString('<link rel="canonical" href="' . url($uri === '/' ? '' : $uri) . '?lang=en">', $html, $uri);
            $this->assertStringContainsString('hreflang="id-ID"', $html, $uri);
            $this->assertStringContainsString('hreflang="x-default"', $html, $uri);
            $this->assertStringContainsString('<meta property="og:locale" content="en_US">', $html, $uri);
            $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image">', $html, $uri);
            $this->assertMatchesRegularExpression('#<meta property="og:image" content="https?://#', $html, 'og:image must be absolute');
            $this->assertSame(1, substr_count($html, '<title>'), $uri . ' must have exactly one <title>');

            $types = $this->types($this->jsonLd($html));
            $this->assertContains('Organization', $types, $uri);
            $this->assertContains('WebSite', $types, $uri);
            $this->assertContains($pageType, $types, $uri);
        }
    }

    public function test_language_query_parameter_switches_and_remembers_the_language(): void
    {
        $this->get('/blog?lang=id')->assertOk()->assertSee('<html', false)->assertSee('lang="id"', false);
        // pilihan diingat di session untuk halaman berikutnya
        $this->get('/blog')->assertSee('<link rel="canonical" href="' . url('/blog') . '?lang=id">', false);

        $this->get('/blog?lang=en')->assertSee('lang="en"', false);
        $this->get('/blog?lang=xx')->assertOk();
    }

    public function test_article_page_has_blog_posting_schema_and_article_meta(): void
    {
        $this->article('strategi', Article::STATUS_PUBLISH, null, [
            'meta_title_en' => 'Business Strategy Guide',
            'meta_description_en' => 'A practical guide to planning business strategy.',
        ]);

        $html = $this->get('/blog/strategi?lang=en')->assertOk()->getContent();

        $this->assertStringContainsString('<title>Business Strategy Guide | RRCONS</title>', $html);
        $this->assertStringContainsString('<meta name="description" content="A practical guide to planning business strategy.">', $html);
        $this->assertStringContainsString('<meta property="og:type" content="article">', $html);
        $this->assertStringContainsString('<meta property="article:published_time"', $html);
        $this->assertStringContainsString('<meta property="article:author" content="Rita Russo">', $html);

        $graph = collect($this->jsonLd($html)['@graph']);
        $posting = $graph->firstWhere('@type', 'BlogPosting');
        $this->assertSame('Title strategi', $posting['headline']);
        $this->assertSame('Rita Russo', $posting['author']['name']);
        $this->assertSame('en', $posting['inLanguage']);
        $this->assertStringStartsWith('http', $posting['image'][0]);
        $this->assertNotNull($graph->firstWhere('@type', 'BreadcrumbList'));

        // tanpa meta khusus: judul & cuplikan isi bahasa aktif, tidak memakai meta bahasa lain
        $html = $this->get('/blog/strategi?lang=id')->getContent();
        $this->assertStringContainsString('<title>Judul strategi | RRCONS</title>', $html);
        $this->assertStringContainsString('Isi artikel strategi yang informatif.', $html);
    }

    public function test_sitemap_lists_pages_and_only_published_articles_in_both_languages(): void
    {
        $this->article('terbit');
        $this->article('rahasia', Article::STATUS_PRIVATE);
        $this->article('nanti', Article::STATUS_PUBLISH, now()->addWeek());

        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'sitemap must be valid XML');

        $content = $response->getContent();
        $this->assertStringContainsString(url('/blog/terbit') . '?lang=id', $content);
        $this->assertStringContainsString(url('/blog/terbit') . '?lang=en', $content);
        $this->assertStringContainsString('hreflang="x-default"', $content);
        $this->assertStringContainsString('<image:loc>' . url('storage/back/blog/terbit.jpg') . '</image:loc>', $content);
        $this->assertStringContainsString(url('/rrfuture') . '?lang=en', $content);
        $this->assertStringContainsString('category=bisnis', $content);
        $this->assertStringNotContainsString('/blog/rahasia', $content);
        $this->assertStringNotContainsString('/blog/nanti', $content);
    }

    public function test_robots_txt_blocks_admin_and_welcomes_ai_crawlers(): void
    {
        $robots = $this->get('/robots.txt')->assertOk()->getContent();

        $this->assertStringContainsString("User-agent: *\nAllow: /", $robots);
        $this->assertStringContainsString('Disallow: /dashboard', $robots);
        $this->assertStringContainsString('Disallow: /blog/articles', $robots);
        $this->assertStringContainsString('User-agent: GPTBot', $robots);
        $this->assertStringContainsString('User-agent: ClaudeBot', $robots);
        $this->assertStringContainsString('User-agent: PerplexityBot', $robots);
        $this->assertStringContainsString('Sitemap: ' . url('/sitemap.xml'), $robots);
        $this->assertStringNotContainsString("Disallow: /blog\n", $robots);
    }

    public function test_llms_txt_summarises_the_business_for_ai_engines(): void
    {
        $menu = ContactMenu::create(['title_id' => 'Kontak']);
        UsContact::create(['contact_menu_id' => $menu->id, 'title' => 'Alamat', 'desc_id' => 'Jl. Contoh No. 1']);
        $rr = RRfutureMenu::create(['title_id' => 'Produk']);
        ProductRRfuture::create(['rrfuture_menu_id' => $rr->id, 'name_id' => 'Aplikasi Kasir', 'name_en' => 'POS App', 'desc_id' => 'Kasir', 'desc_en' => 'Point of sale']);
        $this->article('panduan');
        $this->article('draf', Article::STATUS_PRIVATE);

        $llms = $this->get('/llms.txt?lang=en')->assertOk()->getContent();

        $this->assertStringStartsWith('# RR Consultant Indonesia (RRCONS)', $llms);
        $this->assertStringContainsString('Jl. Contoh No. 1', $llms);
        $this->assertStringContainsString('POS App: Point of sale', $llms);
        $this->assertStringContainsString('[Title panduan](' . url('/blog/panduan') . '?lang=en)', $llms);
        $this->assertStringNotContainsString('draf', $llms);

        $this->assertStringContainsString('Aplikasi Kasir', $this->get('/llms.txt?lang=id')->getContent());
    }

    public function test_rss_feed_contains_published_articles(): void
    {
        $this->article('kabar');
        $this->article('rahasia', Article::STATUS_PRIVATE);

        $response = $this->get('/feed?lang=en')->assertOk();
        $xml = simplexml_load_string($response->getContent());

        $this->assertNotFalse($xml, 'feed must be valid XML');
        $this->assertSame('Title kabar', (string) $xml->channel->item[0]->title);
        $this->assertCount(1, $xml->channel->item);
        $this->assertStringContainsString('<link rel="alternate" type="application/rss+xml"', $this->get('/blog')->getContent());
    }

    public function test_search_results_are_not_indexed_but_blog_is(): void
    {
        $this->article('cari');

        $this->get('/blog?q=cari&lang=id')->assertOk()->assertSee('noindex, follow', false)->assertSee('Judul cari');
        $this->get('/blog')->assertSee('index, follow', false)->assertDontSee('noindex', false);
    }

    public function test_admin_and_login_pages_are_not_indexed(): void
    {
        $this->get('/login')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->actingAs($this->admin)->get('/dashboard')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_admin_saves_article_seo_fields(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post(route('blog.articles.store'), [
            'title_id' => 'Artikel SEO',
            'category_id' => $this->category->id,
            'desc_id' => '<p>Isi</p>',
            'img' => UploadedFile::fake()->image('a.jpg'),
            'publication' => Article::CHOICE_PUBLISH,
            'meta_title_id' => 'Judul SEO khusus',
            'meta_description_id' => 'Deskripsi SEO khusus untuk Google.',
            'meta_title_en' => str_repeat('x', 71),
        ])->assertSessionHasErrors('meta_title_en');

        $this->actingAs($this->admin)->post(route('blog.articles.store'), [
            'title_id' => 'Artikel SEO',
            'category_id' => $this->category->id,
            'desc_id' => '<p>Isi</p>',
            'img' => UploadedFile::fake()->image('a.jpg'),
            'publication' => Article::CHOICE_PUBLISH,
            'meta_title_id' => 'Judul SEO khusus',
            'meta_description_id' => 'Deskripsi SEO khusus untuk Google.',
        ])->assertRedirect();

        $this->assertDatabaseHas('articles', ['slug' => 'artikel-seo', 'meta_title_id' => 'Judul SEO khusus']);
        $this->get('/blog/artikel-seo?lang=id')->assertSee('<title>Judul SEO khusus | RRCONS</title>', false);

        $this->actingAs($this->admin)->get(route('blog.articles.edit', Article::first()->id))
            ->assertOk()->assertSee('name="meta_description_id"', false)->assertSee('Judul SEO khusus');
    }
}
