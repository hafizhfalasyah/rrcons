<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

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
}
