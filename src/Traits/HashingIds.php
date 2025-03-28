<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Illuminate\Support\Facades\Config;
use Hashids\Hashids;

/**
 * Trait for hashing IDs in models
 *
 * This trait provides methods to encode and decode model IDs for public-facing URLs
 */
trait HashingIds
{
    /**
     * Get a Hashids instance
     *
     * @return \Hashids\Hashids
     */
    protected function getHasher(): Hashids
    {
        $salt = Config::get('app.key', 'your-app-key');
        $minLength = Config::get('core.hashids.min_length', 10);
        $alphabet = Config::get('core.hashids.alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

        return new Hashids($salt, $minLength, $alphabet);
    }

    /**
     * Encode an ID
     *
     * @param int $id The ID to encode
     * @return string The encoded ID
     */
    public function encode($id): string
    {
        return $this->getHasher()->encode($id);
    }

    /**
     * Decode an encoded ID
     *
     * @param string $encodedId The encoded ID to decode
     * @return int|null The decoded ID or null if invalid
     */
    public function decode($encodedId)
    {
        $decoded = $this->getHasher()->decode($encodedId);

        // Hashids returns an array, we want the first value or null
        return !empty($decoded) ? $decoded[0] : null;
    }

    /**
     * Get the encoded ID (to be used in routes, etc.)
     *
     * @return string
     */
    public function getHashedIdAttribute(): string
    {
        return $this->encode($this->id);
    }

    /**
     * Get the route key for the model
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'hashed_id';
    }

    /**
     * Retrieve the model for a bound value
     *
     * @param mixed $value The value to find the model by
     * @param string|null $field The field to search in
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // If field is specified and it's not the hashed_id, use parent implementation
        if ($field && $field !== 'hashed_id') {
            return parent::resolveRouteBinding($value, $field);
        }

        // Otherwise, decrypt the ID and find by primary key
        $id = $this->decode($value);
        return $id ? $this->findOrFail($id) : null;
    }
}