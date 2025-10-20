<?php

namespace Odie\EphemeralUsers;

use Illuminate\Support\ServiceProvider;
use Odie\EphemeralUsers\Logging\EphemeralLogger;

class EphemeralUserServiceProvider extends ServiceProvider
{
    /**
     * Register package services
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/ephemeral-users.php',
            'ephemeral-users'
        );

        // Register the ephemeral logger as a singleton
        $this->app->singleton('ephemeral.logger', function ($app) {
            return new EphemeralLogger;
        });
    }

    /**
     * Bootstrap package services
     */
    public function boot(): void
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ephemeral-users.php' => config_path('ephemeral-users.php'),
            ], 'ephemeral-users-config');
        }
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return ['ephemeral.logger'];
    }
}
