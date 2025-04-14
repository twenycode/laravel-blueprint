<?php

namespace TwenyCode\LaravelBlueprint;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use TwenyCode\LaravelBlueprint\Observers\ModelCacheObserver;

/**
 * Core Service Provider
 * Registers all components of the core package
 */
class TwenyLaravelBlueprintServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configs with prefixed keys
        $this->mergeConfigFrom(
            __DIR__ . '/../config/tweny-blueprint.php', 'tweny-blueprint'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/tweny-hashids.php', 'tweny-hashids'
        );

        // Register helpers
        $this->registerHelpers();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configs
        $this->publishes([
            __DIR__ . '/../config/tweny-blueprint.php' => config_path('tweny-blueprint.php'),
            __DIR__ . '/../config/tweny-hashids.php' => config_path('tweny-hashids.php'),
        ], 'tcb-config');

        // Publish SweetAlert assets when the package is published
        $this->publishes([
            __DIR__.'/../vendor/realrashid/sweet-alert/public/sweetalert' => public_path('vendor/sweetalert'),
        ], 'laravel-blueprint-sweetalert-assets');

        // Register model observers - note the updated config key
        if (config('tweny-blueprint.enable_cache_observers', true)) {
            $this->registerModelObservers();
        }
    }

    /**
     * Register helper functions
     *
     * @return void
     */
    protected function registerHelpers()
    {
        // Load the global helper file if it exists
        if (file_exists($helpers = __DIR__ . '/../helpers/helpers.php')) {
            require $helpers;
        }
    }

    /**
     * Register model observers
     *
     * @return void
     */
    protected function registerModelObservers()
    {
        // Get models to observe from config - note the updated config key
        $models = config('tweny-blueprint.observable_models', []);

        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                forward_static_call([$modelClass, 'observe'], ModelCacheObserver::class);
            }
        }
    }
}