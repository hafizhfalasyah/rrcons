<?php

/*
|--------------------------------------------------------------------------
| Visitor analytics
|--------------------------------------------------------------------------
|
| Kunjungan dicatat oleh browser (JavaScript) yang mengirim token bertanda
| tangan dari halaman, sehingga bot, scraper tanpa JavaScript dan request
| palsu tidak ikut terhitung. Tanpa cookie: identitas pengunjung adalah hash
| harian (IP + user agent + tanggal + APP_KEY) dan IP mentah tidak disimpan.
|
*/

return [

    'enabled' => env('ANALYTICS_ENABLED', true),

    // Kunjungan user yang sedang login (admin/penulis) tidak dihitung
    'exclude_authenticated' => env('ANALYTICS_EXCLUDE_AUTHENTICATED', true),

    // IP yang tidak dihitung, pisahkan dengan koma (mis. IP kantor, atau 127.0.0.1 di komputer lokal).
    // Default kosong: di balik reverse proxy semua request bisa tampak berasal dari 127.0.0.1.
    'exclude_ips' => array_values(array_filter(array_map('trim', explode(',', (string) env('ANALYTICS_EXCLUDE_IPS', ''))))),

    // Proxy/CDN tepercaya agar IP pengunjung terbaca benar, mis. "*" di balik Cloudflare (dipisah koma)
    'trusted_proxies' => env('TRUSTED_PROXIES'),

    // Header negara dari CDN, mis. CF-IPCountry (Cloudflare). Kosongkan bila tidak memakai CDN,
    // karena tanpa CDN header ini bisa dipalsukan pengunjung.
    'country_header' => env('ANALYTICS_COUNTRY_HEADER'),

    // Umur maksimal token halaman (detik) sebelum kunjungan ditolak
    'token_ttl' => 7200,

    // Interval heartbeat dari browser (detik) & batas "sedang online" (detik)
    'heartbeat_interval' => 15,
    'active_window' => 60,

    // Batas atas waktu baca per halaman (detik) agar tab yang terbuka berjam-jam tidak merusak rata-rata
    'max_engaged_seconds' => 1800,

    // Data mentah page view yang lebih tua dari ini dihapus oleh `php artisan rrcons:prune-page-views`
    'retention_days' => env('ANALYTICS_RETENTION_DAYS', 730),

    /*
    | Pola user agent bot / scraper (case-insensitive). Dipakai di browser & server.
    */
    'bot_pattern' => 'bot|crawl|spider|slurp|archiver|facebookexternalhit|meta-externalagent|embedly|quora link preview|'
        . 'whatsapp|telegrambot|discordbot|skypeuripreview|headless|phantomjs|selenium|puppeteer|playwright|lighthouse|'
        . 'pagespeed|gtmetrix|pingdom|uptime|monitor|python|curl|wget|httpclient|okhttp|go-http|java\/|libwww|scrapy|'
        . 'axios|node-fetch|guzzle|dataprovider|360spider|petalbot|bytespider|ahrefs|semrush|mj12|dotbot|yandex|baidu|'
        . 'gptbot|chatgpt|oai-searchbot|claudebot|claude-web|perplexity|ccbot|google-extended|applebot|amazonbot|^user-agent:',

    /*
    | Klasifikasi sumber kunjungan berdasarkan domain referrer
    */
    'sources' => [
        'search' => ['google.', 'bing.com', 'yahoo.', 'duckduckgo.com', 'yandex.', 'baidu.com', 'ecosia.org', 'search.brave.com'],
        'ai' => ['chatgpt.com', 'chat.openai.com', 'perplexity.ai', 'claude.ai', 'gemini.google.com', 'copilot.microsoft.com', 'you.com', 'phind.com'],
        'social' => ['facebook.com', 'fb.com', 'instagram.com', 't.co', 'twitter.com', 'x.com', 'linkedin.com', 'lnkd.in',
            'youtube.com', 'tiktok.com', 'whatsapp.com', 'wa.me', 'telegram.org', 't.me', 'threads.net', 'pinterest.'],
    ],

];
