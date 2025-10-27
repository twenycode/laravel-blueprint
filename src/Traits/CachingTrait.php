<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use TwenyCode\LaravelBlueprint\Cache\EventCacheManager;

/**
 * Simple Event Cache Trait
 * Works with your existing repository pattern
 */
trait CachingTrait
{
    private ?EventCacheManager $eventCacheManager = null;

    /**
     * Get event cache manager
     */
    protected function getEventCacheManager(): EventCacheManager
    {
        if (!$this->eventCacheManager) {
            $this->eventCacheManager = app(EventCacheManager::class);
        }
        return $this->eventCacheManager;
    }

    /**
     * Get all events (cached) - matches your pattern
     */
    public function getAllEventsCached()
    {
        return $this->getEventCacheManager()->remember('all', function() {
            return $this->model->all();
        });
    }

    /**
     * Get active events (cached)
     */
    public function getActiveEventsCached()
    {
        return $this->getEventCacheManager()->remember('active', function() {
            return $this->model->where('status', 'active')->get();
        });
    }

    /**
     * Get events with relationship (cached) - matches your cache key
     */
    public function getEventsWithRelationshipCached(array $relations = [])
    {
        return $this->getEventCacheManager()->remember('with_relationship', function() use ($relations) {
            return $this->model->with($relations)->get();
        });
    }

    /**
     * Get active events with relationship (cached)
     */
    public function getActiveEventsWithRelationshipCached(array $relations = [])
    {
        return $this->getEventCacheManager()->remember('active_with_relationship', function() use ($relations) {
            return $this->model->where('status', 'active')->with($relations)->get();
        });
    }

    /**
     * Get paginated events (cached)
     */
    public function getPaginatedEventsCached(int $perPage = 15)
    {
        return $this->getEventCacheManager()->remember('paginated', function() use ($perPage) {
            return $this->model->paginate($perPage);
        });
    }

    /**
     * Get single event (cached)
     */
    public function getEventCached(int $id)
    {
        return $this->getEventCacheManager()->remember("event_{$id}", function() use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * Get events by category (cached)
     */
    public function getEventsByCategoryCached(int $categoryId)
    {
        return $this->getEventCacheManager()->remember("category_{$categoryId}", function() use ($categoryId) {
            return $this->model->where('category_id', $categoryId)->get();
        });
    }

    /**
     * Custom cache method - use your own key and callback
     */
    public function rememberEventCache(string $key, callable $callback, int $minutes = null)
    {
        return $this->getEventCacheManager()->remember($key, $callback, $minutes);
    }

    /**
     * Clear all event cache - matches your clearCacheKey method
     */
    public function clearEventCache(): void
    {
        $this->getEventCacheManager()->clearCacheKey();
    }

    /**
     * Clear cache for specific event
     */
    public function clearEventCacheById(int $eventId): void
    {
        $this->getEventCacheManager()->clearEvent($eventId);
    }

    /**
     * Forget specific cache keys - matches your forgetCache method
     */
    public function forgetEventCache(array $keys): void
    {
        $this->getEventCacheManager()->forgetKeys($keys);
    }

    /**
     * Set cache duration for next operation
     */
    public function setCacheDuration(int $minutes): self
    {
        $this->getEventCacheManager()->setCacheDuration($minutes);
        return $this;
    }
}