<?php

namespace App\Support\Analytics;

/**
 * Menentukan sumber kunjungan dari referrer & parameter UTM.
 */
class TrafficSource
{
    public const DIRECT = 'direct';
    public const SEARCH = 'search';
    public const AI = 'ai';
    public const SOCIAL = 'social';
    public const REFERRAL = 'referral';
    public const CAMPAIGN = 'campaign';
    public const INTERNAL = 'internal';

    /**
     * @return array{host: ?string, type: string}
     */
    public static function classify(?string $referrer, ?string $utmSource, string $siteHost): array
    {
        $host = self::host($referrer);

        // navigasi antar halaman situs sendiri bukan "sumber" kunjungan
        if ($host !== null && self::sameSite($host, $siteHost)) {
            return ['host' => null, 'type' => filled($utmSource) ? self::CAMPAIGN : self::INTERNAL];
        }

        if (filled($utmSource)) {
            return ['host' => $host, 'type' => self::CAMPAIGN];
        }

        if ($host === null) {
            return ['host' => null, 'type' => self::DIRECT];
        }

        foreach ([self::AI, self::SEARCH, self::SOCIAL] as $type) {
            foreach (config("analytics.sources.$type") as $needle) {
                if (self::matches($host, $needle)) {
                    return ['host' => $host, 'type' => $type];
                }
            }
        }

        return ['host' => $host, 'type' => self::REFERRAL];
    }

    public static function host(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $host = parse_url((string) $url, PHP_URL_HOST);

        if (!is_string($host) || $host === '') {
            return null;
        }

        $host = strtolower($host);

        // android-app://com.google.android.gm dll. tetap dicatat apa adanya
        return str_starts_with($host, 'www.') ? substr($host, 4) : $host;
    }

    private static function sameSite(string $host, string $siteHost): bool
    {
        $siteHost = strtolower(preg_replace('/^www\./', '', $siteHost));

        return $host === $siteHost;
    }

    private static function matches(string $host, string $needle): bool
    {
        // "google." cocok dengan google.com, google.co.id; "t.co" harus persis/subdomain
        if (str_ends_with($needle, '.')) {
            return str_starts_with($host, $needle) || str_contains($host, '.' . $needle);
        }

        return $host === $needle || str_ends_with($host, '.' . $needle);
    }
}
