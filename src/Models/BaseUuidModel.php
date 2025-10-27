<?php

namespace TwenyCode\LaravelBlueprint\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BaseUuidModel extends BaseModel
{
    use HasUuids;

    // ====================================
    // UUID Configuration
    // ====================================

    // Disable auto-incrementing IDs
    public $incrementing = false;

    // Set primary key type to string
    protected $keyType = 'string';
}