<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        $vite = $this->viteCspSources();
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com{$vite['script']}",
            "style-src 'self' 'unsafe-inline' https:{$vite['style']}",
            "img-src 'self' data: https: blob: https://www.google-analytics.com",
            "font-src 'self' https: data:{$vite['font']}",
            "connect-src 'self' https://www.google-analytics.com https://region1.google-analytics.com{$vite['connect']}",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * In development, allow Vite dev server (e.g. http://127.0.0.1:5173) for scripts, styles, and HMR.
     */
    private function viteCspSources(): array
    {
        $sources = [
            'script' => '',
            'style' => '',
            'font' => '',
            'connect' => '',
        ];

        if (app()->environment('local') && config('app.debug')) {
            // Use 127.0.0.1 only (CSP rejects IPv6 form http://[::1]:5173). Vite should be run with host 127.0.0.1.
            $http = ' http://127.0.0.1:5173 http://localhost:5173';
            $ws = ' ws://127.0.0.1:5173 ws://localhost:5173';
            $sources['script'] = $http;
            $sources['style'] = $http;
            $sources['font'] = $http;
            $sources['connect'] = $http . $ws;
        }

        return $sources;
    }
}
