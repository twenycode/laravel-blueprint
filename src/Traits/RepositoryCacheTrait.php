<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

trait RepositoryCacheTrait
{
    // Models that need user-specific caching (e.g., ['Order', 'Invoice'])
    protected array $userSpecificModels = [];

    // Cache data with automatic expiration
    // Returns cached result or executes callback and caches it
    protected function remember(string $key, \Closure $callback, ?int $ttl = null)
    {
        // Skip caching if disabled
        if (!config('tweny-blueprint.cache.enabled', true)) {
            return $callback();
        }

        // Use config TTL or default to 1 hour
        $ttl = $ttl ?? config('tweny-blueprint.cache.ttl', 3600);
        return Cache::remember($key, $ttl, $callback);
    }

    // Generate cache key with format: role:all_with_relations or user_1:role:all_with_relations
    public function generateCacheKey(...$parts): string
    {
        // Build key parts: [user_prefix, model_prefix, ...custom_parts]
        $keyParts = array_filter([
            $this->getUserPrefix(),
            $this->cacheKeyPrefix,
            ...$parts
        ]);

        // Join with colons: role:all_with_relations
        return implode(':', $keyParts);
    }

    // Get user prefix for multi-tenant caching (returns user_1, user_2, etc.)
    protected function getUserPrefix(): ?string
    {
        if (!$this->shouldUseUserSpecificCache()) {
            return null;
        }

        return 'user_' . Auth::id();
    }

    // Determine if user-specific caching should be used
    // Only for logged-in non-admin users with specific models
    protected function shouldUseUserSpecificCache(): bool
    {
        return Auth::check()
            && in_array($this->modelName, $this->userSpecificModels)
            && !$this->isAdminUser();
    }

    // Check if current user is admin
    // Admins see all data, so no user-specific caching needed
    protected function isAdminUser(): bool
    {
        $user = Auth::user();
        $superAdminRole = config('tweny-blueprint.authorization.super_admin_role', 'superAdmin');

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($superAdminRole);
        }

        return $user->is_admin ?? false;
    }

    // Clear all cache for this model
    // Called automatically when model is created/updated/deleted
    public function clearCache(): void
    {
        if (!config('tweny-blueprint.cache.enabled', true)) {
            return;
        }

        $driver = config('tweny-blueprint.cache.driver', 'redis');

        // Use tags for Redis/Memcached (clears all related keys efficiently)
        if (in_array($driver, ['redis', 'memcached'])) {
            Cache::tags([$this->cacheKeyPrefix])->flush();

            // Also clear user-specific cache if applicable
            if ($this->shouldUseUserSpecificCache()) {
                $userTag = 'user_' . Auth::id();
                Cache::tags([$userTag, $this->cacheKeyPrefix])->flush();
            }
        } else {
            // Fallback for drivers that don't support tags (file, database)
            $this->clearCacheKeys();
        }
    }

    // Clear specific cache keys one by one
    // Used when cache driver doesn't support tags
    protected function clearCacheKeys(): void
    {
        // List of all cache keys used by BaseRepository
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
            Cache::forget($this->generateCacheKey($key));
        }
    }

    // Clear cache for specific user (useful when user data changes)
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

    // Clear cache for all users (useful for global data changes)
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