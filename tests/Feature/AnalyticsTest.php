<?php

namespace Tests\Feature;

use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\PageView;
use App\Models\User;
use App\Support\Analytics\AnalyticsReport;
use App\Support\Analytics\LegacyVisitorImporter;
use App\Support\Analytics\TrafficSource;
use App\Support\Analytics\UserAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private const CHROME = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36';
    private const IPHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1';

    private User $admin;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::create(['name_id' => 'Bisnis', 'slug' => 'bisnis']);
        $this->article = Article::create([
            'user_id' => $this->admin->id, 'created_by' => $this->admin->id, 'category_id' => $category->id,
            'title_id' => 'Strategi Bisnis', 'slug' => 'strategi-bisnis', 'desc_id' => '<p>Isi</p>',
            'img' => 'back/blog/a.jpg', 'status' => Article::STATUS_PUBLISH, 'publish_date' => now()->subDay(),
        ]);
    }

    /**
     * Ambil token pelacak dari HTML halaman.
     */
    private function tokenFrom(string $uri, string $userAgent = self::CHROME): array
    {
        $html = $this->withHeaders(['User-Agent' => $userAgent])->get($uri)->assertOk()->getContent();
        preg_match('/var config = (\{.*?\});/s', $html, $match);
        $this->assertNotEmpty($match, 'tracker script missing on ' . $uri);

        return json_decode($match[1], true);
    }

    private function beacon(string $uri, array $body, string $userAgent = self::CHROME, string $ip = '203.0.113.10')
    {
        return $this->call('POST', $uri, [], [], [], [
            'HTTP_USER_AGENT' => $userAgent,
            'REMOTE_ADDR' => $ip,
            'CONTENT_TYPE' => 'text/plain',
        ], json_encode($body));
    }

    private function hit(string $uri, array $extra = [], string $userAgent = self::CHROME, string $ip = '203.0.113.10'): array
    {
        $config = $this->tokenFrom($uri, $userAgent);
        $this->beacon('/_v/hit', array_merge(['t' => $config['token']], $extra), $userAgent, $ip)->assertNoContent();

        return $config;
    }

    public function test_public_pages_embed_a_signed_tracker_token(): void
    {
        foreach (['/', '/blog', '/blog/strategi-bisnis', '/rrfuture', '/service', '/contact'] as $uri) {
            $config = $this->tokenFrom($uri);
            $this->assertStringContainsString('.', $config['token']);
            $this->assertSame(route('analytics.hit'), $config['hit']);
        }

        // halaman 404 tidak memuat pelacak
        $this->assertStringNotContainsString('analytics.hit', $this->get('/blog/tidak-ada')->getContent());
        $this->assertStringNotContainsString('/_v/hit', $this->get('/blog/tidak-ada')->getContent());
    }

    public function test_rendering_a_page_alone_records_nothing(): void
    {
        $this->withHeaders(['User-Agent' => self::CHROME])->get('/')->assertOk();

        $this->assertSame(0, PageView::count(), 'only browsers that run the script are counted');
    }

    public function test_a_browser_hit_is_recorded_with_context(): void
    {
        $this->hit('/blog/strategi-bisnis?lang=en', [
            'ref' => 'https://www.google.co.id/search?q=strategi',
            'lang' => 'id-ID',
            'sw' => 390,
        ], self::IPHONE);

        $view = PageView::sole();
        $this->assertSame('/blog/strategi-bisnis', $view->path);
        $this->assertSame('detail_blog', $view->route_name);
        $this->assertSame($this->article->id, $view->article_id);
        $this->assertSame('en', $view->locale);
        $this->assertSame('search', $view->source);
        $this->assertSame('google.co.id', $view->referrer_host);
        $this->assertSame('mobile', $view->device);
        $this->assertSame('Safari', $view->browser);
        $this->assertSame('iOS', $view->os);
        $this->assertSame(390, $view->screen_width);
        $this->assertSame(64, strlen($view->visitor_hash));
        $this->assertSame(PageView::TRACKER_JS, $view->tracker);
        $this->assertSame(1, $this->article->fresh()->views);

        // IP mentah tidak disimpan di mana pun
        $this->assertStringNotContainsString('203.0.113.10', json_encode($view->getAttributes()));
    }

    public function test_article_views_count_once_per_visitor_per_day(): void
    {
        $this->hit('/blog/strategi-bisnis');
        $this->hit('/blog/strategi-bisnis'); // refresh: page view baru, bukan pembaca baru
        $this->hit('/blog/strategi-bisnis', [], self::CHROME, '198.51.100.7'); // pengunjung lain

        $this->assertSame(3, PageView::count());
        $this->assertSame(2, $this->article->fresh()->views);

        $this->travel(1)->days();
        $this->hit('/blog/strategi-bisnis');
        $this->assertSame(3, $this->article->fresh()->views);
    }

    public function test_opening_an_article_page_no_longer_increments_views_directly(): void
    {
        $this->withHeaders(['User-Agent' => self::CHROME])->get('/blog/strategi-bisnis')->assertOk();
        $this->get('/blog/strategi-bisnis')->assertOk();

        $this->assertSame(0, $this->article->fresh()->views);
    }

    public function test_replayed_tampered_and_expired_tokens_are_ignored(): void
    {
        $config = $this->hit('/');
        $this->beacon('/_v/hit', ['t' => $config['token']])->assertNoContent();
        $this->assertSame(1, PageView::count(), 'replay must not create a second view');

        [$data, $signature] = explode('.', $this->tokenFrom('/')['token']);
        $payload = json_decode(base64_decode(strtr($data, '-_', '+/')), true);
        $payload['p'] = '/admin-fake';
        $forged = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=') . '.' . $signature;
        $this->beacon('/_v/hit', ['t' => $forged])->assertNoContent();

        $stale = $this->tokenFrom('/')['token'];
        $this->travel(3)->hours();
        $this->beacon('/_v/hit', ['t' => $stale])->assertNoContent();

        $this->beacon('/_v/hit', ['t' => 'garbage'])->assertNoContent();
        $this->call('POST', '/_v/hit', [], [], [], ['HTTP_USER_AGENT' => self::CHROME], 'not json')->assertNoContent();

        $this->assertSame(1, PageView::count());
    }

    public function test_bots_admins_and_excluded_ips_are_not_counted(): void
    {
        $token = fn () => $this->tokenFrom('/')['token'];

        foreach ([
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)',
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/140.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/120.0 Safari/537.36',
            'curl/8.4.0',
            '',
        ] as $bot) {
            $this->beacon('/_v/hit', ['t' => $token()], $bot)->assertNoContent();
        }

        $this->actingAs($this->admin);
        $this->beacon('/_v/hit', ['t' => $token()])->assertNoContent();
        auth()->logout();

        config(['analytics.exclude_ips' => ['203.0.113.10']]);
        $this->beacon('/_v/hit', ['t' => $token()])->assertNoContent();

        $this->assertSame(0, PageView::count());
    }

    public function test_heartbeat_tracks_reading_time_and_online_status(): void
    {
        $config = $this->hit('/blog/strategi-bisnis');
        $view = PageView::sole();

        $this->travel(70)->seconds();
        $this->assertSame(0, PageView::query()->active()->count(), 'no heartbeat for 70s → offline');

        $this->beacon('/_v/ping', ['v' => $config['view'], 'e' => 37])->assertNoContent();
        $this->assertSame(37, $view->fresh()->engaged_seconds);
        $this->assertSame(1, PageView::query()->active()->count());

        // waktu baca tidak pernah turun dan dibatasi
        $this->beacon('/_v/ping', ['v' => $config['view'], 'e' => 5]);
        $this->assertSame(37, $view->fresh()->engaged_seconds);
        $this->beacon('/_v/ping', ['v' => $config['view'], 'e' => 999999]);
        $this->assertSame(config('analytics.max_engaged_seconds'), $view->fresh()->engaged_seconds);

        // pengunjung lain tidak bisa memalsukan heartbeat milik orang lain
        $this->beacon('/_v/ping', ['v' => $config['view'], 'e' => 1], self::IPHONE, '198.51.100.9');
        $this->assertSame(config('analytics.max_engaged_seconds'), $view->fresh()->engaged_seconds);
    }

    public function test_realtime_endpoint_requires_login_and_reports_live_visitors(): void
    {
        $this->hit('/blog/strategi-bisnis');
        $this->hit('/', [], self::IPHONE, '198.51.100.7');

        $this->getJson('/dashboard/realtime')->assertUnauthorized();

        $this->actingAs($this->admin)->getJson('/dashboard/realtime')
            ->assertOk()
            ->assertJsonPath('active_visitors', 2)
            ->assertJsonPath('today.views', 2)
            ->assertJsonCount(30, 'per_minute')
            ->assertJsonCount(2, 'recent')
            ->assertJsonPath('recent.0.online', true)
            ->assertJsonFragment(['label' => 'Strategi Bisnis']);

        $this->travel(2)->minutes();
        $this->actingAs($this->admin)->getJson('/dashboard/realtime')->assertJsonPath('active_visitors', 0);
    }

    public function test_dashboard_shows_analytics_for_each_period(): void
    {
        $this->hit('/blog/strategi-bisnis', ['ref' => 'https://chatgpt.com/']);
        $this->hit('/', ['ref' => 'https://www.facebook.com/']);
        $this->hit('/contact', [], self::IPHONE, '198.51.100.7');

        foreach (['today', '7d', '30d', '90d', 'bogus'] as $range) {
            $this->actingAs($this->admin)->get('/dashboard?range=' . $range)->assertOk();
        }

        $report = new AnalyticsReport('7d');
        $summary = $report->summary();
        $this->assertSame(3, $summary['views']['value']);
        $this->assertSame(2, $summary['visitors']['value']);
        $this->assertSame(50.0, $summary['single_page_rate']['value']);
        $this->assertSame(['ai', 'direct', 'social'], $report->sources()->pluck('label')->sort()->values()->all());

        $this->actingAs($this->admin)->withSession(['app_locale' => 'id'])->get('/dashboard')
            ->assertSee('Analitik pengunjung')
            ->assertSee('Asisten AI (ChatGPT, Perplexity, …)')
            ->assertSee('Strategi Bisnis');
    }

    public function test_traffic_sources_and_user_agents_are_classified(): void
    {
        $site = 'rrconsindonesia.com';
        $cases = [
            ['https://www.google.com/', null, 'search'],
            ['https://www.bing.com/search?q=x', null, 'search'],
            ['https://chatgpt.com/', null, 'ai'],
            ['https://www.perplexity.ai/search', null, 'ai'],
            ['https://gemini.google.com/app', null, 'ai'],
            ['https://l.facebook.com/l.php', null, 'social'],
            ['https://t.co/abc', null, 'social'],
            ['https://example.org/post', null, 'referral'],
            ['https://www.rrconsindonesia.com/blog', null, 'internal'],
            ['', null, 'direct'],
            ['', 'newsletter', 'campaign'],
        ];

        foreach ($cases as [$referrer, $utm, $expected]) {
            $this->assertSame($expected, TrafficSource::classify($referrer, $utm, $site)['type'], $referrer . ' ' . $utm);
        }

        $this->assertFalse(UserAgent::isBot(self::CHROME));
        $this->assertTrue(UserAgent::isBot('Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'));
        $this->assertSame(['device' => 'mobile', 'browser' => 'Safari', 'os' => 'iOS'], UserAgent::parse(self::IPHONE));
        $this->assertSame('Edge', UserAgent::parse(self::CHROME . ' Edg/129.0')['browser']);
        $this->assertSame('tablet', UserAgent::parse('Mozilla/5.0 (Linux; Android 13; SM-X200) AppleWebKit/537.36 Chrome/120 Safari/537.36')['device']);
    }

    public function test_legacy_visitors_are_imported_without_bots(): void
    {
        DB::table('visitors')->insert([
            ['id' => 1, 'ip_address' => '1.1.1.1', 'user_agent' => self::CHROME, 'path' => '/blog/strategi-bisnis', 'visit_date' => '2025-08-02', 'created_at' => '2025-08-02 10:00:00', 'updated_at' => '2025-08-02 10:00:00'],
            ['id' => 2, 'ip_address' => '2.2.2.2', 'user_agent' => 'Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)', 'path' => '/', 'visit_date' => '2025-08-02', 'created_at' => '2025-08-02 11:00:00', 'updated_at' => '2025-08-02 11:00:00'],
            ['id' => 3, 'ip_address' => '127.0.0.1', 'user_agent' => self::CHROME, 'path' => '/', 'visit_date' => '2025-08-02', 'created_at' => '2025-08-02 12:00:00', 'updated_at' => '2025-08-02 12:00:00'],
            ['id' => 4, 'ip_address' => '3.3.3.3', 'user_agent' => self::IPHONE, 'path' => '/contact', 'visit_date' => '2025-08-03', 'created_at' => '2025-08-03 09:00:00', 'updated_at' => '2025-08-03 09:00:00'],
        ]);

        $report = LegacyVisitorImporter::import();
        $this->assertSame(['imported' => 2, 'skipped_bots' => 2], $report);
        $this->assertSame(['imported' => 0, 'skipped_bots' => 2], LegacyVisitorImporter::import(), 'import is idempotent');

        $legacy = PageView::where('legacy_visitor_id', 1)->sole();
        $this->assertSame('detail_blog', $legacy->route_name);
        $this->assertSame($this->article->id, $legacy->article_id);
        $this->assertSame(PageView::TRACKER_LEGACY, $legacy->tracker);
        $this->assertSame('2025-08-02 10:00:00', $legacy->created_at->format('Y-m-d H:i:s'));
        $this->assertSame('contact', PageView::where('legacy_visitor_id', 4)->value('route_name'));
        $this->assertSame(4, DB::table('visitors')->count(), 'legacy table stays intact');

        // koreksi waktu di visitors ikut tersinkron ke salinannya
        DB::table('visitors')->where('id', 1)->update(['created_at' => '2025-08-02 17:00:00']);
        $this->assertSame(1, LegacyVisitorImporter::syncTimestamps());
        $this->assertSame('2025-08-02 17:00:00', PageView::where('legacy_visitor_id', 1)->sole()->created_at->format('Y-m-d H:i:s'));
    }

    public function test_prune_command_respects_retention(): void
    {
        DB::table('page_views')->insert([
            ['visitor_hash' => str_repeat('a', 64), 'path' => '/', 'created_at' => now()->subDays(800), 'last_seen_at' => now()->subDays(800)],
            ['visitor_hash' => str_repeat('b', 64), 'path' => '/', 'created_at' => now()->subDays(10), 'last_seen_at' => now()->subDays(10)],
        ]);

        $this->artisan('rrcons:prune-page-views', ['--dry-run' => true])->assertExitCode(0);
        $this->assertSame(2, PageView::count());

        $this->artisan('rrcons:prune-page-views', ['--days' => 10])->assertExitCode(2);

        $this->artisan('rrcons:prune-page-views', ['--force' => true])->assertExitCode(0);
        $this->assertSame(1, PageView::count());
    }
}
