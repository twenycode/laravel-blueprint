<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic caching behavior for repositories.
    | Requires Redis or Memcached for tag-based cache invalidation.
    |
    */

    'cache' => [
        // Enable automatic caching in repositories
        'enabled' => env('BLUEPRINT_CACHE_ENABLED', true),

        // Cache duration in seconds (default: 1 hour)
        'ttl' => env('BLUEPRINT_CACHE_TTL', 3600),

        // Cache driver (must support tags: redis, memcached)
        'driver' => env('CACHE_DRIVER', 'redis'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Observers
    |--------------------------------------------------------------------------
    |
    | Automatically clear cache when models are created, updated, or deleted.
    | Register your models here to enable automatic cache invalidation.
    |
    */

    'observers' => [
        // Enable automatic cache clearing
        'enabled' => env('BLUEPRINT_OBSERVERS_ENABLED', true),

        // Models to observe for cache invalidation
        'models' => [
            // App\Models\User::class,
            // App\Models\Post::class,
            // App\Models\Product::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Configure authorization behavior in base controllers.
    |
    */

    'authorization' => [
        // Enable authorization checks in controllers
        'enabled' => env('BLUEPRINT_AUTHORIZATION_ENABLED', true),

        // Super admin role (bypasses all authorization checks)
        'super_admin_role' => env('BLUEPRINT_SUPER_ADMIN_ROLE', 'superAdmin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for repositories.
    |
    */

    'pagination' => [
        // Default items per page
        'per_page' => env('BLUEPRINT_PER_PAGE', 15),

        // Maximum items per page
        'max_per_page' => 100,
    ],
];