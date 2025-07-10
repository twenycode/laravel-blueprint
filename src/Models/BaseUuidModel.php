<?php

namespace TwenyCode\LaravelBlueprint\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BaseUuidModel extends BaseModel
{
    use HasUuids;

    /**
     * Get the columns that should receive a unique identifier.
     * By default, Laravel will use the 'id' column.
     *
     * @return array
     */
    public function uniqueIds()
    {
        return ['id'];
    }


    /**
     * UUID Helper Methods
     * -----------------------
     */

    // Check if a string is a valid UUID format
    public static function isValidUuid(string $id): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $id) === 1;
    }

    // Find a model by ID
    public static function findById(string $id)
    {
        return static::where((new static)->getKeyName(), $id)->first();
    }

    // Find a model by ID or fail
    public static function findByIdOrFail(string $id)
    {
        return static::where((new static)->getKeyName(), $id)->firstOrFail();
    }

    // Scope to find by ID (additional helper)
    public function scopeById($query, string $id)
    {
        return $query->where($this->getKeyName(), $id);
    }

    /**
     * Override route model binding to work with UUIDs
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    /**
     * Get the route key for the model (returns ID)
     */
    public function getRouteKey()
    {
        return $this->getAttribute($this->getRouteKeyName());
    }

    /**
     * Get the route key name for the model (defaults to 'id')
     */
    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }


}