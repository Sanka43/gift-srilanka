<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust Hosts: allow APP_URL host and both www / non-www (e.g. giftsrilanka.com & www.giftsrilanka.com)
        $middleware->trustHosts(at: function () {
            $host = parse_url(config('app.url'), PHP_URL_HOST);
            if (! $host) {
                return [];
            }
            $hosts = [$host];
            if (str_starts_with($host, 'www.')) {
                $hosts[] = substr($host, 4);
            } else {
                $hosts[] = 'www.' . $host;
            }
            return array_values(array_unique($hosts));
        }, subdomains: false);

        // Laravel 11: middleware aliases here (equivalent to $routeMiddleware in Kernel.php)
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // Exempt external payment gateway webhooks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'payment/payhere/notify',
        ]);

        $middleware->web(append: [\App\Http\Middleware\EnsureUserNotBlocked::class]);
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $e, $request) {
            $tokenMismatch = $e instanceof \Illuminate\Session\TokenMismatchException
                || ($e->getPrevious() instanceof \Illuminate\Session\TokenMismatchException);
            if ($tokenMismatch) {
                return redirect()
                    ->route('login')
                    ->with('status', 'Your session expired. Please log in again.');
            }

            if (! config('app.debug') && app()->environment('production')) {
                if ($e instanceof \Illuminate\Validation\ValidationException
                    || $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    return null;
                }
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'An error occurred. Please try again later.'], 500);
                }
                return response()->view('errors.500', [], 500);
            }
        });
    })->create();
