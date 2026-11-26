<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Support\ChatGuestIdentity;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Use project's server.php only (framework's file is missing on some installs)
        $this->app->extend(ServeCommand::class, function () {
            return $this->app->make(\App\Console\Commands\ServeCommand::class);
        });
    }

    public function boot(): void
    {
        $this->ensureLogDirectoryExists();
        $this->ensureFrameworkDirectoriesExist();
        $this->ensurePublicStorageExistsWhenUsingPublicRoot();

        // In production, use the request root (scheme + host + port + base path) so forms, redirects,
        // and asset() stay on the same origin (e.g. www vs non-www). getHost() alone drops non-default ports.
        if (app()->environment('production') && request()->hasHeader('Host')) {
            URL::forceRootUrl(request()->root());
            if (request()->secure()) {
                URL::forceScheme('https');
            }
        }

        // Non-production: align generated URLs with the actual request (localhost vs 127.0.0.1, port).
        // If APP_URL does not match how you open the site, asset()/url() can point at a different origin
        // and static images may fail in the Network tab with (failed) net::…
        // Use root(), not getSchemeAndHttpHost(): when Host omits the port, Symfony may assume :80 and
        // forceRootUrl would strip :8000 from artisan serve — asset() then loads from http://127.0.0.1/...
        if (! app()->environment('production')) {
            // php artisan serve uses the CLI SAPI, so runningInConsole() is always true there.
            // Only adjust URLs when handling an actual HTTP request (REQUEST_METHOD set), not plain artisan commands.
            $this->app->booted(function () {
                if (PHP_SAPI === 'cli' && empty($_SERVER['REQUEST_METHOD'] ?? null)) {
                    return;
                }
                $request = request();
                if ($request && $request->hasHeader('Host')) {
                    URL::forceRootUrl($request->root());
                }
            });
        }

        // Password: min 8 chars, at least one number, uppercase, lowercase, and symbol. No data-leak check.
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        Paginator::defaultView('pagination.default');
        RateLimiter::for('chat-messages', function (Request $request) {
            $user = $request->user();

            if (! $user) {
                $guestKey = (string) $request->cookie(ChatGuestIdentity::COOKIE_NAME, 'anon');

                return Limit::perMinute(5)->by('guest:' . $guestKey . '|' . $request->ip());
            }

            if ($user->isAdmin()) {
                return Limit::perMinute(60)->by('admin:' . $user->id);
            }

            return Limit::perMinute(20)->by('customer:' . $user->id . '|' . $request->ip());
        });

        View::composer('layouts.partials.navbar', function ($view) {
            $cartService = app(CartService::class);
            $cartCount = (int) $cartService->count();

            // Skip the product/image queries entirely when the cart is empty (most page views).
            $items = $cartCount > 0 ? $cartService->itemsWithProducts(false) : collect();

            $view->with('cartCount', $cartCount);
            $view->with('cartPreviewItems', $items->take(3));
            $view->with('cartTotal', (float) $items->sum('subtotal'));

            $navCategories = Cache::remember('navbar_categories', now()->addMinutes(30), function () {
                return Category::where('status', true)->orderBy('name')->get();
            });
            $view->with('navCategories', $navCategories);

            $adMessages = [];
            $hasAdvertisementBar = false;
            if (config('ads.enabled')) {
                $adMessages = Cache::remember('navbar_ad_messages', now()->addMinutes(5), function () {
                    $messages = [];
                    $adsPath = config('ads.path');
                    // Prefer runtime-managed JSON file, but gracefully fallback to config/env messages.
                    if ($adsPath && file_exists($adsPath)) {
                        $json = @file_get_contents($adsPath);
                        if ($json !== false) {
                            $data = json_decode($json, true);
                            if (is_array($data) && isset($data['messages']) && is_array($data['messages'])) {
                                $messages = array_values(array_filter(array_map('trim', $data['messages'])));
                            }
                        }
                    }
                    if (empty($messages)) {
                        $messages = array_values(array_filter(array_map('trim', (array) config('ads.messages', []))));
                    }

                    return $messages;
                });

                $hasAdvertisementBar = !empty($adMessages);
            }
            $view->with('adMessages', $adMessages);
            $view->with('hasAdvertisementBar', $hasAdvertisementBar);
        });

        View::composer('layouts.partials.footer', function ($view) {
            $footerCategories = Cache::remember('footer_categories', now()->addMinutes(30), function () {
                return Category::where('status', true)->orderBy('name')->take(6)->get();
            });
            $view->with('footerCategories', $footerCategories);
        });

        $this->registerSecurityEventLogging();
    }

    private function registerSecurityEventLogging(): void
    {
        Event::listen(Lockout::class, function (Lockout $event) {
            Log::warning('Login lockout', [
                'email' => $event->request->input('email'),
                'ip' => $event->request->ip(),
                'user_agent' => $event->request->userAgent(),
            ]);
        });

        Event::listen(Failed::class, function (Failed $event) {
            if ($event->user) {
                Log::info('Login failed for existing user', [
                    'user_id' => $event->user->id,
                    'ip' => request()->ip(),
                ]);
            } else {
                Log::info('Login failed (unknown user)', [
                    'email' => $event->credentials['email'] ?? null,
                    'ip' => request()->ip(),
                ]);
            }
        });

        Event::listen(Login::class, function (Login $event) {
            try {
                \App\Models\User::whereKey($event->user->getAuthIdentifier())->update([
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip(),
                ]);
            } catch (\Throwable $e) {
                // Never block login if the column is missing (migration not yet run).
                Log::warning('Could not record last login', ['message' => $e->getMessage()]);
            }

            Log::info('User logged in', [
                'user_id' => $event->user->id,
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(PasswordReset::class, function (PasswordReset $event) {
            Log::info('Password reset completed', [
                'user_id' => $event->user->id,
                'ip' => request()->ip(),
            ]);
        });
    }

    private function ensureLogDirectoryExists(): void
    {
        $logDir = storage_path('logs');
        if (! is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . DIRECTORY_SEPARATOR . 'laravel.log';
        if (! file_exists($logFile) && is_writable($logDir)) {
            @touch($logFile);
        }
    }

    /**
     * Ensure runtime framework directories exist in case deploy/upload missed them.
     */
    private function ensureFrameworkDirectoriesExist(): void
    {
        $paths = [
            storage_path('framework'),
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/testing'),
            storage_path('framework/views'),
        ];

        foreach ($paths as $path) {
            $parent = File::dirname($path);
            if (File::isDirectory($path) || ! is_writable($parent)) {
                continue;
            }

            @File::makeDirectory($path, 0755, true);
        }
    }

    /**
     * When using public disk root as public/storage (no symlink), ensure the directory exists.
     */
    private function ensurePublicStorageExistsWhenUsingPublicRoot(): void
    {
        if (config('filesystems.public_root_mode') !== 'public') {
            return;
        }
        $dir = public_path('storage');
        if (! File::isDirectory($dir) && is_writable(File::dirname($dir))) {
            @File::makeDirectory($dir, 0755, true);
        }
    }
}
