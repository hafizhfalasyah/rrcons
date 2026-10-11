<?php

namespace App\Support\Analytics;

/**
 * Parser user agent ringan (tanpa dependency) untuk jenis perangkat, browser, OS & deteksi bot.
 */
class UserAgent
{
    public static function isBot(?string $userAgent): bool
    {
        $userAgent = trim((string) $userAgent);

        if ($userAgent === '' || strlen($userAgent) < 20) {
            return true;
        }

        return (bool) preg_match('/' . config('analytics.bot_pattern') . '/i', $userAgent);
    }

    /**
     * @return array{device: string, browser: string, os: string}
     */
    public static function parse(?string $userAgent): array
    {
        $ua = (string) $userAgent;

        return [
            'device' => self::device($ua),
            'browser' => self::browser($ua),
            'os' => self::os($ua),
        ];
    }

    private static function device(string $ua): string
    {
        if (preg_match('/iPad|Tablet|PlayBook|Silk|(Android(?!.*Mobile))/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/Mobi|iPhone|iPod|Android.*Mobile|Windows Phone|Opera Mini|IEMobile/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    private static function browser(string $ua): string
    {
        // urutan penting: banyak browser memakai token "Chrome"/"Safari"
        $map = [
            'Edge' => '/Edg(e|A|iOS)?\//',
            'Opera' => '/OPR\/|Opera/',
            'Samsung Internet' => '/SamsungBrowser\//',
            'UC Browser' => '/UCBrowser\//',
            'Yandex' => '/YaBrowser\//',
            'Firefox' => '/Firefox\/|FxiOS\//',
            'Chrome' => '/Chrome\/|CriOS\//',
            'Safari' => '/Safari\//',
        ];

        foreach ($map as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }

        return 'Other';
    }

    private static function os(string $ua): string
    {
        $map = [
            'iOS' => '/iPhone|iPad|iPod/',
            'Android' => '/Android/',
            'Windows' => '/Windows/',
            'macOS' => '/Mac OS X|Macintosh/',
            'ChromeOS' => '/CrOS/',
            'Linux' => '/Linux/',
        ];

        foreach ($map as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }

        return 'Other';
    }
}
