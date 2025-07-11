<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
            $user = $request->user();

            $roles = array_map('strtolower', $roles);

            $userRoles = $user?->roles->pluck('name')->map(fn ($r) => strtolower($r)) ?? collect();

            if (! $user || $userRoles->intersect($roles)->isEmpty()) {
                abort(403, 'Unauthorized.');
            }

            return $next($request);
    }
}
