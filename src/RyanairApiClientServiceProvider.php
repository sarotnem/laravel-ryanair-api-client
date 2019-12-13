<?php

namespace Sarotnem\RyanairApiClient;

use Illuminate\Support\ServiceProvider;

/**
 * A Laravel service provider that injects all of the Civil Service API endpoints
 * into the service container.
 */
class RyanairApiClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ryanair.php', 'ryanair');
        $this->publishes([
            __DIR__ . '/../config/ryanair.php' => config_path('ryanair.php'),
        ]);
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Sarotnem\RyanairApiClient\RyanairApiClient', function () {
            return new RyanairApiClient();
        });
        $this->app->alias('Sarotnem\RyanairApiClient\RyanairApiClient', 'ryanair-api-client');
    }
}
