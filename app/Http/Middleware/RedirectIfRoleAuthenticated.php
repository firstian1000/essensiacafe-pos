<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfRoleAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = $guards ?: ['admin', 'cashier'];

        if (in_array('admin', $guards, true) && Auth::guard('admin')->check()) {
            Auth::shouldUse('admin');

            return redirect()->route('dashboard');
        }

        if (in_array('cashier', $guards, true) && Auth::guard('cashier')->check()) {
            Auth::shouldUse('cashier');

            return redirect()->route('cashier.index');
        }

        return $next($request);
    }
}
