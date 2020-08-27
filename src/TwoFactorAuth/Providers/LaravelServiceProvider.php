<?php

namespace Freedom\TwoFactorAuth\Providers;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Freedom\TwoFactorAuth\Guards\Session as SessionGuard;
use Illuminate\Support\Facades\Auth;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/two-factor-auth.php' => config_path('two-factor-auth.php'),
        ], 'config');
        $this->mergeConfigFrom(__DIR__.'/../config/two-factor-auth.php', 'two-factor-auth');

        if (! class_exists('CreateActivityLogTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../migrations/create_oauth_auth_code.php.stub' => database_path("/migrations/{$timestamp}_create_oauth_auth_code.php"),
            ], 'migrations');
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Auth::extend('two-factor-session', function ($app, $name, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\Guard...
            return new SessionGuard('two-factor-session',Auth::createUserProvider($config['provider']), app()->make('session.store'), request());
        });
    }
}
