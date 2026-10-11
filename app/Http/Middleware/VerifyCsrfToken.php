<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Dikirim navigator.sendBeacon (tidak bisa membawa header CSRF). Aman karena setiap
        // kunjungan wajib membawa token halaman bertanda tangan HMAC & hanya mencatat statistik.
        '_v/hit',
        '_v/ping',
    ];

    /**
     * Laravel 13 meloloskan request tanpa token CSRF bila browser mengirim header
     * Sec-Fetch-Site: same-origin. Dimatikan agar perilakunya tetap sama seperti
     * sebelumnya: setiap POST/PUT/DELETE wajib membawa token CSRF yang valid
     * (form yang dikirim setelah sesi kedaluwarsa tetap menampilkan halaman 419).
     */
    protected function hasValidOrigin($request)
    {
        return false;
    }
}
