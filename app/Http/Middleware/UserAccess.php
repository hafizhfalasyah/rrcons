<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * Contoh pemakaian: ->middleware('UserAccess:admin,writer')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // User tanpa role (mis. hasil registrasi publik lama) tidak boleh masuk panel admin
        if (!$user || !$user->hasAnyRole($roles)) {
            abort(403);
        }

        return $next($request);
    }
}
