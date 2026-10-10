<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     */
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        $locale = session(
            'app_locale',
            config('app.locale', 'id'),
        );

        if (!in_array($locale, ['id', 'en'], true)) {
            $locale = config('app.locale', 'id');
        }

        App::setLocale($locale);
        // nama bulan/hari & diffForHumans() mengikuti bahasa aktif
        Carbon::setLocale($locale);

        return $next($request);
    }
}