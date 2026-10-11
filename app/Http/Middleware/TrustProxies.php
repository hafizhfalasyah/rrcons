<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Proxy tepercaya bisa diatur lewat TRUSTED_PROXIES (lihat config/analytics.php).
     */
    protected function proxies()
    {
        $configured = config('analytics.trusted_proxies');

        if (blank($configured)) {
            return $this->proxies;
        }

        return $configured === '*' ? '*' : array_map('trim', explode(',', $configured));
    }
}
