<?php

namespace App\Providers;

use App\Auth\Sha1UserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Auth::provider('sha1', function ($app, array $config) {
            return new Sha1UserProvider(
                $app->make(\Illuminate\Contracts\Hashing\Hasher::class),
                $config['model']
            );
        });
    }
}
