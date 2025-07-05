<?php

namespace TwenyCode\LaravelBlueprint\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Simple Event Cache Manager
 * Follows the pattern: {model_name}_{cache_key}
 */
class EventCacheManager
{
    private string $cacheKeyPrefix = 'event';
    private int $cacheDuration = 1440; // 24 hours in minutes

    /**
     * Cache data with a key
     */
    public function put(string $key, $data, int $minutes = null): bool
    {
        $cacheKey = $this->generateCacheKey($key);
        $minutes = $minutes ?? $this->cacheDuration;

        return Cache::put($cacheKey, $data, now()->addMinutes($minutes));
    }

    /**
     * Get cached data
     */
    public function get(string $key, $default = null)
    {
        $cacheKey = $this->generateCacheKey($key);
        return Cache::get($cacheKey, $default);
    }

    /**
     * Cache data using callback if not exists
     */
    public function remember(string $key, callable $callback, int $minutes = null)
    {
        $cacheKey = $this->generateCacheKey($key);
        $minutes = $minutes ?? $this->cacheDuration;

        return Cache::remember($cacheKey, now()->addMinutes($minutes), $callback);
    }

    /**
     * Delete cached data
     */
    public function forget(string $key): bool
    {
        $cacheKey = $this->generateCacheKey($key);
        return Cache::forget($cacheKey);
    }

    /**
     * Check if key exists in cache
     */
    public function has(string $key): bool
    {
        $cacheKey = $this->generateCacheKey($key);
        return Cache::has($cacheKey);
    }

    /**
     * Clear all event cache using standard cache keys
     */
    public function clear(): void
    {
        // Use the same cache keys from your tweny-blueprint config
        $keys = config('tweny-blueprint.cache_keys', [
            'all', 'with_relationship', 'active_with_relationship',
            'inactive_with_relationship', 'trashed', 'paginated',
            'active', 'pluck_active'
        ]);

        foreach ($keys as $key) {
            $this->forget($key);
        }

        Log::info('Event cache cleared');
    }

    /**
     * Clear cache for specific event
     */
    public function clearEvent(int $eventId): void
    {
        $keys = [
            "event_{$eventId}",
            "event_details_{$eventId}",
            'all', 'active', 'with_relationship'
        ];

        foreach ($keys as $key) {
            $this->forget($key);
        }

        Log::info("Cache cleared for event: {$eventId}");
    }

    /**
     * Set cache duration
     */
    public function setCacheDuration(int $minutes): self
    {
        $this->cacheDuration = $minutes;
        return $this;
    }

    /**
     * Generate cache key following your pattern: {model_name}_{cache_key}
     */
    public function generateCacheKey(string $key): string
    {
        return $this->cacheKeyPrefix . '_' . $key;
    }

    /**
     * Forget specific cache keys
     */
    public function forgetKeys(array $keys): void
    {
        foreach ($keys as $key) {
            $this->forget($key);
        }
    }

    /**
     * Clear cache key (matches your RepositoryCacheTrait method)
     */
    public function clearCacheKey(): void
    {
        $this->clear();
        Log::info("Cache cleared for {$this->cacheKeyPrefix}");
    }
}