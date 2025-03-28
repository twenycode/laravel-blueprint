<?php

namespace TwenyCode\LaravelBlueprint;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use TwenyCode\LaravelBlueprint\Observers\ModelCacheObserver;

/**
 * Core Service Provider
 * Registers all components of the core package
 */
class CoreServiceProvider extends ServiceProvider
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
            __DIR__ . '/../config/tcb_core.php', 'tcb_core'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/tcb_hashids.php', 'tcb_hashids'
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
            __DIR__ . '/../config/tcb_core.php' => config_path('tcb_core.php'),
            __DIR__ . '/../config/tcb_hashids.php' => config_path('tcb_hashids.php'),
        ], 'tcb-config');

        // Register model observers - note the updated config key
        if (config('tcb_core.enable_cache_observers', true)) {
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
        $models = config('tcb_core.observable_models', []);

        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                forward_static_call([$modelClass, 'observe'], ModelCacheObserver::class);
            }
        }
    }
}