<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * Repository caching functionality with user-specific support
 */
trait RepositoryCacheTrait
{
    /**
     * Models that require user-specific caching
     * Override this in your repository if needed
     */
    protected array $userSpecificModels = [];

    /**
     * Remember a value in cache
     */
    protected function remember(string $key, \Closure $callback, ?int $ttl = null)
    {
        if (!config('tweny-blueprint.cache.enabled', true)) {
            return $callback();
        }

        $ttl = $ttl ?? config('tweny-blueprint.cache.ttl', 3600);

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Generate a cache key with optional user-specific prefix
     */
    public function generateCacheKey(...$parts): string
    {
        $keyParts = array_filter([
            $this->getUserPrefix(),
            $this->cacheKeyPrefix,
            ...$parts
        ]);

        return implode(':', $keyParts);
    }

    /**
     * Get user prefix for cache key if applicable
     */
    protected function getUserPrefix(): ?string
    {
        if (!$this->shouldUseUserSpecificCache()) {
            return null;
        }

        return 'user_' . Auth::id();
    }

    /**
     * Determine if user-specific caching should be used
     */
    protected function shouldUseUserSpecificCache(): bool
    {
        // Not logged in
        if (!Auth::check()) {
            return false;
        }

        // Model doesn't require user-specific caching
        if (!in_array($this->modelName, $this->userSpecificModels)) {
            return false;
        }

        // Optional: Skip for admin users (they see all data)
        if ($this->isAdminUser()) {
            return false;
        }

        return true;
    }

    /**
     * Check if current user is an admin
     * Override this method to customize admin detection
     */
    protected function isAdminUser(): bool
    {
        $user = Auth::user();

        // Check for super admin role from config
        $superAdminRole = config('tweny-blueprint.authorization.super_admin_role', 'superAdmin');

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($superAdminRole);
        }

        // Fallback: check for is_admin attribute
        if (isset($user->is_admin)) {
            return $user->is_admin;
        }

        return false;
    }

    /**
     * Clear all cache for this repository
     */
    public function clearCache(): void
    {
        if (!config('tweny-blueprint.cache.enabled', true)) {
            return;
        }

        $driver = config('tweny-blueprint.cache.driver', 'redis');

        if (in_array($driver, ['redis', 'memcached'])) {
            // Clear tag-based cache
            Cache::tags([$this->cacheKeyPrefix])->flush();

            // If user-specific, also clear user's cache
            if ($this->shouldUseUserSpecificCache()) {
                $userTag = 'user_' . Auth::id();
                Cache::tags([$userTag, $this->cacheKeyPrefix])->flush();
            }
        } else {
            // Clear specific keys for non-tag-supporting drivers
            $this->clearCacheKeys();
        }
    }

    /**
     * Clear specific cache keys (for non-tag-supporting drivers)
     */
    protected function clearCacheKeys(): void
    {
        $keys = [
            'all',
            'all_with_relations',
            'active',
            'active_with_relations',
            'inactive',
            'inactive_with_relations',
            'trashed',
            'pluck_active'
        ];
        foreach ($keys as $key) {
            $cacheKey = $this->generateCacheKey($key);
            Cache::forget($cacheKey);
        }
    }

    /**
     * Clear cache for a specific user
     * Useful when user data changes
     */
    public function clearUserCache(int $userId): void
    {
        if (!config('tweny-blueprint.cache.enabled', true)) {
            return;
        }

        $driver = config('tweny-blueprint.cache.driver', 'redis');

        if (in_array($driver, ['redis', 'memcached'])) {
            $userTag = 'user_' . $userId;
            Cache::tags([$userTag, $this->cacheKeyPrefix])->flush();
        }
    }

    /**
     * Clear cache for all users
     * Useful for global data changes
     */
    public function clearAllUsersCache(): void
    {
        if (!config('tweny-blueprint.cache.enabled', true)) {
            return;
        }

        $driver = config('tweny-blueprint.cache.driver', 'redis');

        if (in_array($driver, ['redis', 'memcached'])) {
            Cache::tags([$this->cacheKeyPrefix])->flush();
        }
    }
}