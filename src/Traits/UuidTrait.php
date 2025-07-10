<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Illuminate\Support\Str;

/**
 * UUID Trait for Models
 *
 * This trait can be used on any Eloquent model to automatically
 * generate UUIDs for the primary key. It works independently
 * of the BaseModel class.
 */
trait UuidTrait
{
    /**
     * Boot the UUID trait
     */
    protected static function bootUuidTrait()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Initialize the UUID trait
     */
    public function initializeUuidTrait()
    {
        // Set the key type to string for UUIDs
        $this->keyType = 'string';

        // Disable auto-incrementing
        $this->incrementing = false;
    }

    /**
     * Generate a new UUID
     */
    public function generateUuid(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Check if a string is a valid UUID
     */
    public static function isValidUuid(string $uuid): bool
    {
        return Str::isUuid($uuid);
    }

    /**
     * Find a model by UUID
     */
    public static function findByUuid(string $uuid)
    {
        return static::where((new static)->getKeyName(), $uuid)->first();
    }

    /**
     * Find a model by UUID or fail
     */
    public static function findByUuidOrFail(string $uuid)
    {
        return static::where((new static)->getKeyName(), $uuid)->firstOrFail();
    }

    /**
     * Scope to find by UUID
     */
    public function scopeByUuid($query, string $uuid)
    {
        return $query->where($this->getKeyName(), $uuid);
    }

    /**
     * Get the route key for the model (returns UUID)
     */
    public function getRouteKey()
    {
        return $this->getAttribute($this->getRouteKeyName());
    }

    /**
     * Override route model binding to work with UUIDs
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }




}