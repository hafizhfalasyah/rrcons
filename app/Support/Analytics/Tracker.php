<?php

namespace App\Support\Analytics;

use App\Models\Blog\Article;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Pencatat kunjungan:
 * 1. Halaman publik menyisipkan token bertanda tangan (HMAC) berisi path, route & artikel.
 * 2. Browser mengirim token itu (JavaScript) -> satu baris page_views.
 * 3. Browser mengirim heartbeat selama halaman terbuka -> data real-time & waktu baca.
 */
class Tracker
{
    public static function enabled(): bool
    {
        return (bool) config('analytics.enabled');
    }

    /*
    |--------------------------------------------------------------------------
    | Token halaman
    |--------------------------------------------------------------------------
    */

    public static function pageToken(Request $request, ?Article $article = null): string
    {
        return self::issue($request, $article)['token'];
    }

    /**
     * @return array{token: string, view: string}
     */
    public static function issue(Request $request, ?Article $article = null): array
    {
        $payload = [
            'v' => (string) Str::uuid(),
            'p' => '/' . ltrim($request->path(), '/'),
            'r' => optional($request->route())->getName(),
            'a' => $article?->id,
            'l' => app()->getLocale(),
            't' => now()->getTimestamp(),
        ];

        $data = self::base64UrlEncode(json_encode($payload));

        return ['token' => $data . '.' . self::signature($data), 'view' => $payload['v']];
    }

    /**
     * @return array|null payload bila tanda tangan valid & belum kedaluwarsa
     */
    public static function verifyToken(?string $token): ?array
    {
        if (!is_string($token) || substr_count($token, '.') !== 1 || strlen($token) > 1024) {
            return null;
        }

        [$data, $signature] = explode('.', $token);

        if (!hash_equals(self::signature($data), $signature)) {
            return null;
        }

        $payload = json_decode(self::base64UrlDecode($data), true);

        if (!is_array($payload) || !isset($payload['v'], $payload['p'], $payload['t'])) {
            return null;
        }

        $age = now()->getTimestamp() - (int) $payload['t'];

        if ($age < -60 || $age > config('analytics.token_ttl')) {
            return null;
        }

        return $payload;
    }

    /*
    |--------------------------------------------------------------------------
    | Pencatatan
    |--------------------------------------------------------------------------
    */

    /**
     * Identitas pengunjung anonim yang berganti setiap hari (tanpa cookie, tanpa IP mentah).
     */
    public static function visitorHash(Request $request): string
    {
        return hash_hmac('sha256', now()->toDateString() . '|' . $request->ip() . '|' . $request->userAgent(), self::key());
    }

    public static function shouldIgnore(Request $request): bool
    {
        return !self::enabled()
            || UserAgent::isBot($request->userAgent())
            || (config('analytics.exclude_authenticated') && $request->user())
            || in_array($request->ip(), config('analytics.exclude_ips'), true);
    }

    /**
     * Catat satu tampilan halaman. Mengembalikan true bila tercatat.
     */
    public static function recordHit(Request $request, array $body): bool
    {
        $payload = self::verifyToken($body['t'] ?? null);

        if ($payload === null || self::shouldIgnore($request)) {
            return false;
        }

        $utmSource = self::limit($body['us'] ?? null, 100);
        $source = TrafficSource::classify(self::limit($body['ref'] ?? null, 2048), $utmSource, $request->getHost());
        $device = UserAgent::parse($request->userAgent());
        $visitorHash = self::visitorHash($request);
        $articleId = isset($payload['a']) ? (int) $payload['a'] : null;
        $now = now();

        // artikel bisa saja sudah dihapus sejak halaman dibuka
        if ($articleId && !DB::table('articles')->where('id', $articleId)->exists()) {
            $articleId = null;
        }

        $inserted = DB::table('page_views')->insertOrIgnore([
            'view_id' => $payload['v'],
            'visitor_hash' => $visitorHash,
            'path' => Str::limit((string) $payload['p'], 250, ''),
            'route_name' => self::limit($payload['r'] ?? null, 64),
            'article_id' => $articleId ?: null,
            'locale' => self::limit($payload['l'] ?? null, 5),
            'referrer_host' => self::limit($source['host'], 250),
            'source' => $source['type'],
            'utm_source' => $utmSource,
            'utm_medium' => self::limit($body['um'] ?? null, 100),
            'utm_campaign' => self::limit($body['uc'] ?? null, 100),
            'device' => $device['device'],
            'browser' => $device['browser'],
            'os' => $device['os'],
            'screen_width' => self::screenWidth($body['sw'] ?? null),
            'browser_language' => self::limit($body['lang'] ?? null, 16),
            'country' => self::country($request),
            'engaged_seconds' => 0,
            'tracker' => PageView::TRACKER_JS,
            'created_at' => $now,
            'last_seen_at' => $now,
        ]);

        // token yang sama (replay / double submit) tidak dihitung dua kali
        if (!$inserted) {
            return false;
        }

        if ($articleId) {
            self::countArticleView($articleId, $visitorHash, $payload['v']);
        }

        return true;
    }

    /**
     * Heartbeat: perbarui waktu terakhir terlihat & waktu baca halaman.
     */
    public static function recordPing(Request $request, array $body): bool
    {
        $viewId = $body['v'] ?? null;

        if (!is_string($viewId) || !Str::isUuid($viewId) || self::shouldIgnore($request)) {
            return false;
        }

        $view = DB::table('page_views')
            ->where('view_id', $viewId)
            // hanya pemilik kunjungan yang boleh memperbaruinya
            ->where('visitor_hash', self::visitorHash($request))
            ->where('created_at', '>=', now()->subSeconds(config('analytics.token_ttl')))
            ->first(['id', 'engaged_seconds']);

        if (!$view) {
            return false;
        }

        $engaged = max(0, min((int) ($body['e'] ?? 0), (int) config('analytics.max_engaged_seconds')));

        DB::table('page_views')->where('id', $view->id)->update([
            'last_seen_at' => now(),
            'engaged_seconds' => max((int) $view->engaged_seconds, $engaged),
        ]);

        return true;
    }

    /**
     * Kolom articles.views: satu kali per pengunjung per artikel per hari.
     */
    private static function countArticleView(int $articleId, string $visitorHash, string $viewId): void
    {
        $alreadyCounted = DB::table('page_views')
            ->where('article_id', $articleId)
            ->where('visitor_hash', $visitorHash)
            ->where('view_id', '!=', $viewId)
            ->where('created_at', '>=', now()->startOfDay())
            ->exists();

        if (!$alreadyCounted) {
            DB::table('articles')->where('id', $articleId)->increment('views');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    private static function key(): string
    {
        return hash('sha256', 'rrcons-analytics|' . config('app.key'));
    }

    private static function signature(string $data): string
    {
        return self::base64UrlEncode(hash_hmac('sha256', $data, self::key(), true));
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): string
    {
        return (string) base64_decode(strtr($value, '-_', '+/'), true);
    }

    private static function limit($value, int $length): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim(strip_tags((string) $value));

        return $value === '' ? null : Str::limit($value, $length, '');
    }

    private static function screenWidth($value): ?int
    {
        $width = filter_var($value, FILTER_VALIDATE_INT);

        return $width !== false && $width > 0 && $width <= 10000 ? $width : null;
    }

    private static function country(Request $request): ?string
    {
        $header = config('analytics.country_header');
        $country = $header ? strtoupper((string) $request->header($header)) : '';

        // XX = tidak diketahui, T1 = Tor (Cloudflare)
        return preg_match('/^[A-Z]{2}$/', $country) && !in_array($country, ['XX', 'T1'], true) ? $country : null;
    }
}
