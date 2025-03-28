<?php

namespace TwenyCode\LaravelBlueprint\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Observer to automatically clear cache when models are modified
 */
class ModelCacheObserver
{
    /**
     * Handle the Model "created" event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function created(Model $model): void
    {
        $this->clearModelCache($model);
    }

    /**
     * Handle the Model "updated" event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function updated(Model $model): void
    {
        $this->clearModelCache($model);
    }

    /**
     * Handle the Model "deleted" event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function deleted(Model $model): void
    {
        $this->clearModelCache($model);
    }

    /**
     * Handle the Model "restored" event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function restored(Model $model): void
    {
        $this->clearModelCache($model);
    }

    /**
     * Handle the Model "force deleted" event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function forceDeleted(Model $model): void
    {
        $this->clearModelCache($model);
    }

    /**
     * Clear cache for the model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected function clearModelCache(Model $model): void
    {
        $modelName = class_basename($model);
        $cacheKeyPrefix = Str::snake($modelName);

        // Get common cache keys from config or use defaults
        $keys = config('tcb_core.cache_keys', [
            'all', 'active', 'inactive', 'trashed', 'with_relations'
        ]);

        foreach ($keys as $key) {
            // Clear global cache
            $cacheKey = $cacheKeyPrefix . '_' . $key;
            Cache::forget($cacheKey);

            // Clear user-specific cache if user is logged in as employee
            if (auth()->check() && auth()->user()->hasRole(['employee'])) {
                $userKey = auth()->user()->username . '_' . $cacheKey;
                Cache::forget($userKey);
                Log::info("Cache cleared for {$modelName}: {$userKey}");
            }

            // Log cache clearing for debugging
            Log::info("Cache cleared for {$modelName}: {$cacheKey}");
        }
    }
}