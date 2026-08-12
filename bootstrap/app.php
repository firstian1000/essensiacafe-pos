<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\EnsureUserRole;
use App\Http\Middleware\RedirectIfRoleAuthenticated;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserRole::class,
            'guest.role' => RedirectIfRoleAuthenticated::class,
        ]);

        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'midtrans/callback',
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('cashier', 'cashier/*', 'kasir/*')) {
                return route('cashier.login.form');
            }

            $sharedOperationalPaths = [
                'cashier',
                'cashier/*',
                'kasir/*',
                'categories',
                'categories/*',
                'menus',
                'menus/*',
                'tables',
                'tables/*',
                'orders',
                'orders/*',
                'payments',
                'payments/*',
                'settings',
            ];

            foreach ($sharedOperationalPaths as $path) {
                if ($request->is($path)) {
                    return route('login');
                }
            }

            return route('login');
        });
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

if (getenv('VERCEL') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || isset($_SERVER['HTTP_X_VERCEL_ID'])) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
