<?php

namespace TwenyCode\LaravelBlueprint\Repositories;

/**
 * Base UUID Repository
 *
 * Extends base repository for UUID-based models.
 */
class BaseUuidRepository extends BaseRepository
{
    /**
     * Decode a UUID (no decoding needed)
     */
    public function decodeId($id)
    {
        return $id;
    }

    /**
     * Encode a UUID (no encoding needed)
     */
    public function encodeId($id)
    {
        return $id;
    }
}