<?php

namespace TwenyCode\LaravelBlueprint;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
        // Merge config with unified configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/tweny-blueprint.php', 'tweny-blueprint'
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
        Paginator::useBootstrap();

        // Publish unified config
        $this->publishes([
            __DIR__ . '/../config/tweny-blueprint.php' => config_path('tweny-blueprint.php'),
        ], 'tcb-config');

        // Register model observers
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
        // Get models to observe from config
        $models = config('tweny-blueprint.observers.models', []);

        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                forward_static_call([$modelClass, 'observe'], ModelCacheObserver::class);
            }
        }
    }
}