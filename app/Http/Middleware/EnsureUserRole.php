<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $requestedArea = $request->query('area');

        if (($requestedArea === 'cashier' || $request->is('cashier', 'cashier/*', 'kasir/*')) && Auth::guard('cashier')->check()) {
            Auth::shouldUse('cashier');
        } elseif ($requestedArea === 'admin' && Auth::guard('admin')->check()) {
            Auth::shouldUse('admin');
        } elseif (Auth::guard('cashier')->check() && ! Auth::guard('admin')->check()) {
            Auth::shouldUse('cashier');
        } elseif (Auth::guard('admin')->check()) {
            Auth::shouldUse('admin');
        } elseif (Auth::guard('cashier')->check()) {
            Auth::shouldUse('cashier');
        }

        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
