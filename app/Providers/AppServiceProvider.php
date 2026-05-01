<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tell Spatie to cache permissions locally
        app(\Spatie\Permission\PermissionRegistrar::class)->cacheExpirationTime = now()->addHours(24);

        // Override how auth resolves the user: cache the user + roles in file cache
        // so we don't hit Supabase on every single page load
        \Illuminate\Support\Facades\Auth::provider('eloquent-cached', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends \Illuminate\Auth\EloquentUserProvider {
                public function retrieveById($identifier)
                {
                    return \Illuminate\Support\Facades\Cache::remember(
                        'auth_user_' . $identifier,
                        300, // 5 minutes
                        function () use ($identifier) {
                            $user = parent::retrieveById($identifier);
                            if ($user) {
                                $user->load('roles', 'permissions');
                            }
                            return $user;
                        }
                    );
                }
            };
        });
    }
}
