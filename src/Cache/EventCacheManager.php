<?php

namespace TwenyCode\LaravelBlueprint\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Event Cache Manager
 *
 * Simple and clean cache management following pattern: {model_name}_{cache_key}
 */
class EventCacheManager
{
    private string $prefix = 'event';

    /**
     * Cache data
     */
    public function put(string $key, $data, ?int $seconds = null): bool
    {
        $seconds = $seconds ?? config('tweny-blueprint.cache.ttl', 3600);
        return Cache::put($this->key($key), $data, $seconds);
    }

    /**
     * Retrieve cached data
     */
    public function get(string $key, $default = null)
    {
        return Cache::get($this->key($key), $default);
    }

    /**
     * Cache with callback
     */
    public function remember(string $key, callable $callback, ?int $seconds = null)
    {
        $seconds = $seconds ?? config('tweny-blueprint.cache.ttl', 3600);
        return Cache::remember($this->key($key), $seconds, $callback);
    }

    /**
     * Delete cache
     */
    public function forget(string $key): bool
    {
        return Cache::forget($this->key($key));
    }

    /**
     * Check if cache exists
     */
    public function has(string $key): bool
    {
        return Cache::has($this->key($key));
    }

    /**
     * Clear all standard cache keys
     */
    public function clear(): void
    {
        $keys = config('tweny-blueprint.cache_keys', [
            'all', 'with_relationship', 'active_with_relationship',
            'inactive_with_relationship', 'trashed', 'paginated',
            'active', 'pluck_active'
        ]);

        array_map(fn($key) => $this->forget($key), $keys);

        Log::info("Cache cleared: {$this->prefix}");
    }

    /**
     * Clear specific event cache
     */
    public function clearEvent(int $eventId): void
    {
        $keys = [
            "{$eventId}",
            "details_{$eventId}",
            'all',
            'active',
            'with_relationship',
            'paginated'
        ];

        array_map(fn($key) => $this->forget($key), $keys);
    }

    /**
     * Set custom prefix
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Generate cache key
     */
    private function key(string $key): string
    {
        return "{$this->prefix}_{$key}";
    }
}