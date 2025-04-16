<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

/**
 * Trait for ID hashing and obfuscation
 *
 * Provides methods to encode and decode model IDs for public-facing URLs
 * to prevent sequential ID guessing and enhance security.
 */
trait HashingIds
{

    // decode object IDs
    public function getRouteKey()
    {
        return $this->encode();
    }

    /**
     * Get Hashids instance with app-specific salt
     *
     * @return Hashids
     */
    protected function getHasher(): Hashids
    {
        $salt = Config::get('tweny-hashids.connections.main.salt', Config::get('app.key', 'laravel-blueprint'));
        $minLength = Config::get('tweny-hashids.connections.main.length',24);
        $alphabet = Config::get('tweny-hashids.connections.main.alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

        return new Hashids($salt, $minLength, $alphabet);
    }

    /**
     * Encode an ID
     *
     * @param int $id The ID to encode
     * @return string The encoded ID
     */
    public function encode(): string
    {
        return $this->getHasher()->encode($this->getKey());
    }

    /**
     * Decode an encoded ID
     *
     * @param string $value The encoded ID
     * @return int|null The decoded ID or null if invalid
     */
    public function decode($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        $decoded = $this->getHasher()->decode($value);

        return !empty($decoded) ? $decoded[0] : null;
    }

    /**
     * Get the encoded ID (accessor for encoded_id attribute)
     *
     * @return string
     */
    public function getEncodedIdAttribute(): string
    {
        return $this->encode($this->id);
    }
}