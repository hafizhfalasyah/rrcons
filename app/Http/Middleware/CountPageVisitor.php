<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visitor;
use Carbon\Carbon;

class CountPageVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $today = Carbon::today();
        $ip = $request->ip();
        $path = '/' . ltrim($request->path(), '/'); // pastikan path diawali "/"

        $exists = Visitor::whereDate('visit_date', $today)
            ->where('ip_address', $ip)
            ->where('path', $path)
            ->exists();

        if (!$exists) {
            Visitor::create([
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'visit_date' => $today,
                'path' => $path,
            ]);
        }

        return $next($request);
    }
}
