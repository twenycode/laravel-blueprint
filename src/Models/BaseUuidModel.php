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


}