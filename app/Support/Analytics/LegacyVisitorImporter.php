<?php

namespace App\Support\Analytics;

use App\Models\PageView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyalin riwayat dari tabel `visitors` (pencatat lama, sisi server) ke `page_views`
 * agar grafik historis tetap ada. Tabel `visitors` tidak diubah.
 *
 * Catatan akurasi data lama: pencatat lama hanya menyimpan satu baris per IP per halaman
 * per hari dan tidak bisa membedakan browser sungguhan dari scraper. Bot yang dikenali
 * dari user agent & akses localhost disaring di sini.
 */
class LegacyVisitorImporter
{
    private const LOCAL_IPS = ['127.0.0.1', '::1'];

    /**
     * @return array{imported: int, skipped_bots: int}
     */
    public static function import(): array
    {
        $report = ['imported' => 0, 'skipped_bots' => 0];

        if (!Schema::hasTable('visitors') || !Schema::hasTable('page_views')) {
            return $report;
        }

        $articles = DB::table('articles')->pluck('id', 'slug');
        $staticRoutes = ['/' => 'home', '/blog' => 'blog', '/rrfuture' => 'rrfuture', '/service' => 'service', '/contact' => 'contact'];

        DB::table('visitors')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('page_views')->whereColumn('page_views.legacy_visitor_id', 'visitors.id');
            })
            ->orderBy('id')
            ->chunkById(1000, function ($visitors) use (&$report, $articles, $staticRoutes) {
                $rows = [];

                foreach ($visitors as $visitor) {
                    if (UserAgent::isBot($visitor->user_agent) || in_array($visitor->ip_address, self::LOCAL_IPS, true)) {
                        $report['skipped_bots']++;
                        continue;
                    }

                    $path = '/' . ltrim((string) $visitor->path, '/');
                    $route = $staticRoutes[$path] ?? null;
                    $articleId = null;

                    if ($route === null && str_starts_with($path, '/blog/')) {
                        $route = 'detail_blog';
                        $articleId = $articles[substr($path, 6)] ?? null;
                    }

                    $device = UserAgent::parse($visitor->user_agent);
                    $timestamp = $visitor->created_at ?? $visitor->visit_date . ' 00:00:00';

                    $rows[] = [
                        'view_id' => null,
                        // sama seperti pencatat baru: unik per hari, tanpa menyimpan IP
                        'visitor_hash' => hash_hmac('sha256', $visitor->visit_date . '|' . $visitor->ip_address . '|' . $visitor->user_agent, self::key()),
                        'path' => mb_substr($path, 0, 250),
                        'route_name' => $route,
                        'article_id' => $articleId,
                        'source' => 'unknown',
                        'device' => $device['device'],
                        'browser' => $device['browser'],
                        'os' => $device['os'],
                        'engaged_seconds' => 0,
                        'tracker' => PageView::TRACKER_LEGACY,
                        'legacy_visitor_id' => $visitor->id,
                        'created_at' => $timestamp,
                        'last_seen_at' => $timestamp,
                    ];
                }

                if ($rows) {
                    DB::table('page_views')->insertOrIgnore($rows);
                    $report['imported'] += count($rows);
                }
            });

        return $report;
    }

    /**
     * Samakan waktu baris legacy dengan tabel visitors (dipakai setelah koreksi timezone).
     */
    public static function syncTimestamps(): int
    {
        if (!Schema::hasTable('page_views') || !Schema::hasTable('visitors')) {
            return 0;
        }

        $updated = 0;

        DB::table('page_views')
            ->where('tracker', PageView::TRACKER_LEGACY)
            ->whereNotNull('legacy_visitor_id')
            ->orderBy('id')
            ->chunkById(1000, function ($views) use (&$updated) {
                $visitors = DB::table('visitors')->whereIn('id', $views->pluck('legacy_visitor_id'))->pluck('created_at', 'id');

                foreach ($views as $view) {
                    $createdAt = $visitors[$view->legacy_visitor_id] ?? null;

                    if ($createdAt && (string) $createdAt !== (string) $view->created_at) {
                        DB::table('page_views')->where('id', $view->id)->update([
                            'created_at' => $createdAt,
                            'last_seen_at' => $createdAt,
                        ]);
                        $updated++;
                    }
                }
            });

        return $updated;
    }

    private static function key(): string
    {
        return hash('sha256', 'rrcons-analytics|' . config('app.key'));
    }
}
