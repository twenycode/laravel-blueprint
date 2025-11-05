<?php

namespace TwenyCode\LaravelBlueprint\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ModelCacheObserver
{
    // Automatically called when model is created
    public function created(Model $model): void
    {
        $this->clearModelCache($model);
    }

    // Automatically called when model is updated
    public function updated(Model $model): void
    {
        $this->clearModelCache($model);
    }

    // Automatically called when model is soft deleted
    public function deleted(Model $model): void
    {
        $this->clearModelCache($model);
    }

    // Automatically called when model is restored from trash
    public function restored(Model $model): void
    {
        $this->clearModelCache($model);
    }

    // Automatically called when model is permanently deleted
    public function forceDeleted(Model $model): void
    {
        $this->clearModelCache($model);
    }

    // Clear all cache related to this model
    // Example: Role model clears all role:* cache keys
    protected function clearModelCache(Model $model): void
    {
        // Get model name and convert to snake_case for cache key
        // Example: Role -> role, UserProfile -> user_profile
        $modelName = class_basename($model);
        $cacheKeyPrefix = Str::snake($modelName);
        $driver = config('tweny-blueprint.cache.driver', 'redis');

        // Use tags for Redis/Memcached (clears all related keys at once)
        if (in_array($driver, ['redis', 'memcached'])) {
            try {
                Cache::tags([$cacheKeyPrefix])->flush();
                Log::info("Cache cleared for {$modelName} using tags");
            } catch (\Exception $e) {
                // Fallback if tags fail
                Log::warning("Failed to clear cache tags for {$modelName}: " . $e->getMessage());
                $this->clearCacheKeys($cacheKeyPrefix, $modelName);
            }
        } else {
            // For drivers that don't support tags (file, database)
            $this->clearCacheKeys($cacheKeyPrefix, $modelName);
        }
    }

    // Clear specific cache keys one by one
    // Format: role:all, role:all_with_relations, etc.
    protected function clearCacheKeys(string $cacheKeyPrefix, string $modelName): void
    {
        // Get cache keys from config: default + model-specific
        $keys = $this->getCacheKeysFromConfig($modelName);

        foreach ($keys as $key) {
            // Generate cache key: role:all_with_relations
            $cacheKey = $cacheKeyPrefix . ':' . $key;
            Cache::forget($cacheKey);

            // Also clear user-specific cache if user is logged in
            // Format: user_1:role:all_with_relations
            if (auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole(['employee'])) {
                $userKey = 'user_' . auth()->id() . ':' . $cacheKey;
                Cache::forget($userKey);
            }

            Log::info("Cache cleared for {$modelName}: {$cacheKey}");
        }
    }

    // Get all cache keys for this model from config
    protected function getCacheKeysFromConfig(string $modelName): array
    {
        // Get default keys
        $defaultKeys = config('tweny-blueprint.cache_keys.default', [
            'all',
            'all_with_relations',
            'active',
            'active_with_relations',
            'inactive',
            'inactive_with_relations',
            'trashed',
            'pluck_active'
        ]);

        // Get model-specific keys
        $modelKeys = config("tweny-blueprint.cache_keys.{$modelName}", []);

        // Merge and return unique keys
        return array_unique(array_merge($defaultKeys, $modelKeys));
    }
}