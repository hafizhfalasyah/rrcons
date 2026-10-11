<?php

namespace App\Support\Analytics;

use App\Models\Blog\Article;
use App\Models\PageView;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Agregasi statistik kunjungan untuk dashboard admin.
 */
class AnalyticsReport
{
    public const RANGES = ['today' => 1, '7d' => 7, '30d' => 30, '90d' => 90];

    public Carbon $from;
    public Carbon $to;

    public function __construct(public string $range = '7d')
    {
        $this->range = array_key_exists($range, self::RANGES) ? $range : '7d';
        $days = self::RANGES[$this->range];

        $this->to = now();
        $this->from = now()->subDays($days - 1)->startOfDay();
    }

    /*
    |--------------------------------------------------------------------------
    | Ringkasan
    |--------------------------------------------------------------------------
    */

    public function summary(): array
    {
        $current = $this->kpis($this->from, $this->to);

        // periode sebelumnya dengan panjang sama untuk perbandingan
        $length = $this->from->diffInSeconds($this->to);
        $previous = $this->kpis($this->from->copy()->subSeconds($length + 1), $this->from->copy()->subSecond());

        return collect($current)->map(fn ($value, $key) => [
            'value' => $value,
            'previous' => $previous[$key],
            'change' => $this->change($value, $previous[$key]),
        ])->all();
    }

    private function kpis(Carbon $from, Carbon $to): array
    {
        $views = $this->query($from, $to)->count();

        // hash pengunjung berganti tiap hari -> pengunjung unik dijumlah per hari
        $visitors = (int) DB::query()->fromSub(
            $this->query($from, $to)->selectRaw('substr(created_at, 1, 10) as day, visitor_hash')->groupBy('day', 'visitor_hash'),
            'daily'
        )->count();

        $engaged = $this->query($from, $to)
            ->where('tracker', PageView::TRACKER_JS)
            ->where('engaged_seconds', '>', 0)
            ->avg('engaged_seconds');

        $perVisitor = DB::query()->fromSub(
            $this->query($from, $to)
                ->where('tracker', PageView::TRACKER_JS)
                ->selectRaw('count(*) as pages')
                ->groupBy(DB::raw('substr(created_at, 1, 10)'), 'visitor_hash'),
            'visits'
        );
        $visits = (clone $perVisitor)->count();
        $singlePage = (clone $perVisitor)->where('pages', 1)->count();

        return [
            'views' => $views,
            'visitors' => $visitors,
            'avg_engaged_seconds' => (int) round((float) $engaged),
            'single_page_rate' => $visits ? round($singlePage / $visits * 100, 1) : 0.0,
            'pages_per_visitor' => $visitors ? round($views / $visitors, 2) : 0.0,
        ];
    }

    private function change($current, $previous): ?float
    {
        if (!$previous) {
            return null;
        }

        return round(($current - $previous) / $previous * 100, 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Tren & daftar teratas
    |--------------------------------------------------------------------------
    */

    public function daily(): array
    {
        $rows = $this->query()
            ->selectRaw('substr(created_at, 1, 10) as day, count(*) as views, count(distinct visitor_hash) as visitors')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $labels = $views = $visitors = [];

        foreach (CarbonPeriod::create($this->from->copy()->startOfDay(), $this->to->copy()->startOfDay()) as $day) {
            $key = $day->toDateString();
            $labels[] = $day->translatedFormat($this->range === '90d' ? 'd M' : 'd M Y');
            $views[] = (int) ($rows[$key]->views ?? 0);
            $visitors[] = (int) ($rows[$key]->visitors ?? 0);
        }

        return compact('labels', 'views', 'visitors');
    }

    public function topPages(int $limit = 10): Collection
    {
        $rows = $this->query()
            ->selectRaw('path, max(route_name) as route_name, max(article_id) as article_id, count(*) as views, count(distinct visitor_hash) as visitors, avg(case when engaged_seconds > 0 then engaged_seconds end) as engaged')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();

        $titles = Article::whereIn('id', $rows->pluck('article_id')->filter())->get()->keyBy('id');

        return $rows->map(fn ($row) => (object) [
            'path' => $row->path,
            'label' => self::pageLabel($row->route_name, $titles[$row->article_id] ?? null, $row->path),
            'views' => (int) $row->views,
            'visitors' => (int) $row->visitors,
            'engaged' => $row->engaged ? (int) round($row->engaged) : null,
        ]);
    }

    public function topArticles(int $limit = 5): Collection
    {
        $rows = $this->query()
            ->whereNotNull('article_id')
            ->selectRaw('article_id, count(*) as views, count(distinct visitor_hash) as visitors, avg(case when engaged_seconds > 0 then engaged_seconds end) as engaged')
            ->groupBy('article_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();

        $articles = Article::whereIn('id', $rows->pluck('article_id'))->get()->keyBy('id');

        return $rows->filter(fn ($row) => isset($articles[$row->article_id]))->map(fn ($row) => (object) [
            'article' => $articles[$row->article_id],
            'views' => (int) $row->views,
            'visitors' => (int) $row->visitors,
            'engaged' => $row->engaged ? (int) round($row->engaged) : null,
        ])->values();
    }

    /**
     * Sumber kunjungan (hanya data pelacak baru; data lama tidak menyimpan referrer).
     */
    public function sources(): Collection
    {
        return $this->breakdown('source', fn ($query) => $query->where('tracker', PageView::TRACKER_JS)->where('source', '!=', TrafficSource::INTERNAL));
    }

    public function referrers(int $limit = 8): Collection
    {
        return $this->breakdown('referrer_host', fn ($query) => $query->whereNotNull('referrer_host'), $limit);
    }

    public function breakdown(string $column, ?callable $scope = null, int $limit = 6): Collection
    {
        $query = $this->query()->whereNotNull($column);

        if ($scope) {
            $scope($query);
        }

        $rows = $query->selectRaw("$column as label, count(*) as views")->groupBy($column)->orderByDesc('views')->get();
        $total = max(1, $rows->sum('views'));

        $top = $rows->take($limit)->map(fn ($row) => (object) [
            'label' => $row->label,
            'views' => (int) $row->views,
            'percent' => round($row->views / $total * 100, 1),
        ]);

        $others = $rows->slice($limit)->sum('views');

        if ($others > 0) {
            $top->push((object) ['label' => '__other', 'views' => $others, 'percent' => round($others / $total * 100, 1)]);
        }

        return $top->values();
    }

    /**
     * Porsi data dari pencatat lama (sebelum pelacak terverifikasi browser aktif).
     */
    public function legacyShare(): float
    {
        $total = $this->query()->count();

        return $total ? round($this->query()->where('tracker', PageView::TRACKER_LEGACY)->count() / $total * 100, 1) : 0.0;
    }

    /*
    |--------------------------------------------------------------------------
    | Real-time
    |--------------------------------------------------------------------------
    */

    public static function realtime(): array
    {
        $active = PageView::query()->active();

        $pages = (clone $active)
            ->selectRaw('path, max(route_name) as route_name, max(article_id) as article_id, count(distinct visitor_hash) as visitors')
            ->groupBy('path')
            ->orderByDesc('visitors')
            ->limit(8)
            ->get();
        $titles = Article::whereIn('id', $pages->pluck('article_id')->filter())->get()->keyBy('id');

        $since = now()->subMinutes(29)->startOfMinute();
        $perMinute = PageView::query()
            ->where('created_at', '>=', $since)
            ->selectRaw('substr(created_at, 1, 16) as minute, count(*) as views')
            ->groupBy('minute')
            ->pluck('views', 'minute');

        $minutes = [];
        for ($i = 0; $i < 30; $i++) {
            $minute = $since->copy()->addMinutes($i);
            $minutes[] = ['label' => $minute->format('H:i'), 'views' => (int) ($perMinute[$minute->format('Y-m-d H:i')] ?? 0)];
        }

        $recent = PageView::query()->with('article')
            ->where('tracker', PageView::TRACKER_JS)
            ->latest('created_at')
            ->limit(12)
            ->get();

        $today = PageView::query()->where('created_at', '>=', now()->startOfDay());

        return [
            'generated_at' => now()->toIso8601String(),
            'active_visitors' => (clone $active)->distinct()->count('visitor_hash'),
            'active_pages' => $pages->map(fn ($row) => [
                'path' => $row->path,
                'label' => self::pageLabel($row->route_name, $titles[$row->article_id] ?? null, $row->path),
                'visitors' => (int) $row->visitors,
            ])->all(),
            'devices' => (clone $active)->selectRaw('device, count(distinct visitor_hash) as visitors')->groupBy('device')->pluck('visitors', 'device'),
            'per_minute' => $minutes,
            'today' => [
                'views' => (clone $today)->count(),
                'visitors' => (clone $today)->distinct()->count('visitor_hash'),
            ],
            'recent' => $recent->map(fn (PageView $view) => [
                'time' => $view->created_at->format('H:i:s'),
                'ago' => $view->created_at->diffForHumans(),
                'label' => self::pageLabel($view->route_name, $view->article, $view->path),
                'path' => $view->path,
                'device' => $view->device,
                'browser' => $view->browser,
                'source' => $view->source,
                'referrer' => $view->referrer_host,
                'country' => $view->country,
                'online' => $view->last_seen_at && $view->last_seen_at->gte(now()->subSeconds(config('analytics.active_window'))),
            ])->all(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    private function query(?Carbon $from = null, ?Carbon $to = null): Builder
    {
        return DB::table('page_views')
            ->where('created_at', '>=', ($from ?? $this->from)->format('Y-m-d H:i:s'))
            ->where('created_at', '<=', ($to ?? $this->to)->format('Y-m-d H:i:s'));
    }

    public static function pageLabel(?string $route, ?Article $article, string $path): string
    {
        if ($article) {
            return $article->title;
        }

        return match ($route) {
            'home' => __('Home'),
            'blog' => __('Blog'),
            'rrfuture' => __('RR Future'),
            'service' => __('Service'),
            'contact' => __('Contact'),
            default => $path,
        };
    }
}
