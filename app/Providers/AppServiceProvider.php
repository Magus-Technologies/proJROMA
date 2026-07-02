<?php

namespace App\Providers;

use BladeUI\Icons\Factory as IconFactory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Forzar URL base con subfolder — soluciona TODOS los route() y url() ──
        URL::forceRootUrl(config('app.url'));

        // ── Set de íconos custom (resources/svg): logos de marcas que heroicons no trae ──
        $this->callAfterResolving(IconFactory::class, function (IconFactory $factory) {
            $factory->add('custom', [
                'path'   => resource_path('svg'),
                'prefix' => 'custom',
            ]);
        });

        // ── Rate limiter para login ──────────────────────────────────────────────
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->input('user') . '|' . $request->ip());
        });
    }
}
