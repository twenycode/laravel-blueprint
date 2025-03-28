<?php

namespace TwenyCode\LaravelCore\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Trait for repository caching functionality
 */
trait RepositoryCacheTrait
{
    /** @var int Default cache duration in minutes */
    protected int $cacheDuration = 1440; // 24 hours (1 day)

    /** @var array Models that use user-specific caching */
    protected array $includeInUserCache = [ ];

    /**
     * Set the cache duration
     *
     * @param int $minutes Duration in minutes
     * @return self
     */
    public function setCacheDuration(int $minutes): self
    {
        $this->cacheDuration = $minutes;
        return $this;
    }

    /**
     * Remember a value in cache
     *
     * @param string $key Cache key
     * @param \Closure $callback Function to generate value if not cached
     * @param int|null $duration Cache duration in minutes (null for default)
     * @return mixed
     */
    protected function remember(string $key, \Closure $callback, ?int $duration = null)
    {
        $duration = $duration ?? $this->cacheDuration;

        return Cache::remember(
            $key,
            now()->addMinutes($duration),
            $callback
        );
    }

    /**
     * Generate a cache key with support for user-specific data
     *
     * @param mixed ...$args Additional arguments to include in key
     * @return string Generated cache key
     */
    public function generateCacheKey(...$args): string
    {
        $key = $this->cacheKeyPrefix . '_' . implode('_', $args);

        // Add user-specific prefix for certain models if user is logged in and not an admin
        if ($this->shouldUseUserSpecificCache()) {
            return auth()->user()->id . '_' . $key;
        }

        return $key;
    }

    /**
     * Determine if user-specific caching should be used
     *
     * @return bool Whether to use user-specific caching
     */
    protected function shouldUseUserSpecificCache(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        if (!auth()->user()->hasRole(['employee'])) {
            return false;
        }

        return in_array($this->modelName, $this->includeInUserCache);
    }

    /**
     * Forget specific cache keys
     *
     * @param array $keys Keys to forget
     * @return void
     */
    public function forgetCache(array $keys = []): void
    {
        foreach ($keys as $key) {
            // Clear user-specific cache if applicable
            if ($this->shouldUseUserSpecificCache()) {
                Cache::forget(auth()->user()->id . '_' . $this->cacheKeyPrefix . '_' . $key);
            }

            // Always clear global cache
            Cache::forget($this->cacheKeyPrefix . '_' . $key);
        }
    }

    /**
     * Clear all cache keys for this model
     *
     * @return void
     */
    public function clearCacheKey(): void
    {
        $this->forgetCache(config('cache.keys', [
            'all', 'active', 'inactive', 'trashed', 'with_relations'
        ]));

        // Log cache clearing for debugging
        Log::info("Cache cleared for {$this->modelName}");
    }
}