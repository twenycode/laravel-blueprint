<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Illuminate\Support\Str;

/**
 * Trait for UUID primary keys
 *
 * Provides automatic UUID generation for model primary keys
 * and related functionality for UUID-based models.
 */
trait UuidTrait
{
    /**
     * Boot the UUID trait for the model.
     */
    public static function bootUuidTrait()
    {
        static::creating(function ($model) {
            if ($model->useUuids && empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the primary key for the model.
     */
    public function getKeyName()
    {
        return 'id';
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType()
    {
        return $this->useUuids ? 'string' : 'int';
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing()
    {
        return !$this->useUuids;
    }

    /**
     * Generate a new UUID for the model.
     */
    public function generateUuid()
    {
        return (string) Str::uuid();
    }

    /**
     * Check if the model uses UUIDs
     */
    public function usesUuids()
    {
        return $this->useUuids ?? false;
    }

    /**
     * Scope to find by UUID
     */
    public function scopeByUuid($query, $uuid)
    {
        return $query->where($this->getKeyName(), $uuid);
    }

    /**
     * Find a model by UUID
     */
    public static function findByUuid($uuid)
    {
        return static::where((new static)->getKeyName(), $uuid)->first();
    }

    /**
     * Find a model by UUID or fail
     */
    public static function findByUuidOrFail($uuid)
    {
        return static::where((new static)->getKeyName(), $uuid)->firstOrFail();
    }

    /**
     * Check if a string is a valid UUID
     */
    public static function isValidUuid($uuid)
    {
        return Str::isUuid($uuid);
    }
}