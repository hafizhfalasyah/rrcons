<?php

/*
|--------------------------------------------------------------------------
| SEO & GEO
|--------------------------------------------------------------------------
|
| Identitas organisasi untuk meta tag, Open Graph, structured data (JSON-LD),
| sitemap, robots.txt, RSS dan llms.txt. Nilai kosong (null / '') tidak akan
| ditampilkan, jadi jangan isi dengan data yang belum pasti.
|
*/

return [

    // Nama brand yang tampil di judul halaman & og:site_name
    'site_name' => env('SEO_SITE_NAME', 'RRCONS'),

    'organization' => [
        'name' => env('SEO_ORG_NAME', 'RR Consultant Indonesia'),
        'legal_name' => env('SEO_ORG_LEGAL_NAME', 'Rita Russo Consultant'),
        'alternate_names' => ['RRCONS', 'RR Consultant', 'Rita Russo Consultant'],
        'logo' => env('SEO_ORG_LOGO', 'front/image/logo.png'),
        'phone' => env('SEO_ORG_PHONE', '+62-812-5262-4284'),
        'email' => env('SEO_ORG_EMAIL', 'ritarussoconsultant@gmail.com'),
        'founding_date' => env('SEO_ORG_FOUNDING_DATE'),
        // Unit bisnis yang dibahas di website (dipakai di JSON-LD & llms.txt)
        'departments' => [
            ['name' => 'RR Future Tech', 'path' => '/rrfuture'],
            ['name' => 'RR Vision', 'path' => '/service'],
        ],
        // Profil resmi di platform lain (dipisah koma), mis. https://www.instagram.com/xxx
        'same_as' => array_values(array_filter(explode(',', (string) env('SEO_ORG_SAME_AS', '')))),
    ],

    /*
    | GEO (lokasi). Alamat jalan diambil dari menu Contact > Contact Us (entri "Alamat").
    | Koordinat hanya ditampilkan bila diisi.
    */
    'geo' => [
        'country' => env('SEO_GEO_COUNTRY', 'ID'),
        'region' => env('SEO_GEO_REGION', 'ID-JI'),        // kode ISO 3166-2 (ID-JI = Jawa Timur)
        'region_name' => env('SEO_GEO_REGION_NAME', 'Jawa Timur'),
        'locality' => env('SEO_GEO_LOCALITY', 'Malang'),   // kota (sesuai alamat di menu Contact)
        'postal_code' => env('SEO_GEO_POSTAL_CODE'),
        'latitude' => env('SEO_GEO_LATITUDE'),
        'longitude' => env('SEO_GEO_LONGITUDE'),
        'area_served' => array_values(array_filter(explode(',', (string) env('SEO_GEO_AREA_SERVED', 'Indonesia')))),
    ],

    // Gambar default untuk Open Graph / Twitter bila halaman tidak punya gambar sendiri
    'default_image' => env('SEO_DEFAULT_IMAGE', 'front/image/logo.png'),

    'twitter_site' => env('SEO_TWITTER_SITE'), // mis. @rrcons

    // Verifikasi Search Console / Bing Webmaster (isi kode verifikasinya saja)
    'verification' => [
        'google' => env('SEO_GOOGLE_VERIFICATION'),
        'bing' => env('SEO_BING_VERIFICATION'),
    ],

    // Bahasa yang didukung: kode aplikasi => kode og:locale / hreflang
    'locales' => [
        'id' => ['hreflang' => 'id-ID', 'og' => 'id_ID'],
        'en' => ['hreflang' => 'en', 'og' => 'en_US'],
    ],

    // Crawler AI yang diizinkan secara eksplisit (Generative Engine Optimization)
    'ai_crawlers' => [
        'GPTBot', 'OAI-SearchBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-SearchBot', 'Claude-User',
        'PerplexityBot', 'Perplexity-User', 'Google-Extended', 'Applebot-Extended', 'Bingbot', 'CCBot',
    ],

    // Path yang tidak boleh diindeks
    'disallow' => [
        '/dashboard', '/users', '/login', '/logout', '/home/', '/rrfuture/', '/service/', '/contact/',
        '/blog/articles', '/blog/categories', '/blog/configs', '/laravel-filemanager', '/set-language/',
        '/get-articles', '/search-articles',
    ],

];
