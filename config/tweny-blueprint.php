<?php

return [
    // Cache settings
    'cache' => [
        'enabled' => env('BLUEPRINT_CACHE_ENABLED', true),
        'ttl' => env('BLUEPRINT_CACHE_TTL', 3600), // 1 hour
        'driver' => env('CACHE_DRIVER', 'redis'),
    ],

    // Cache keys that will be automatically cleared on CRUD operations
    // Add your custom keys here per model
    'cache_keys' => [
        // Default keys used by all models
        'default' => [
            'all',
            'all_with_relations',
            'active',
            'active_with_relations',
            'inactive',
            'inactive_with_relations',
            'trashed',
            'pluck_active',
        ],

        // Custom keys per model
        // Format: ModelName => [keys]
//        'Product' => [
//            'featured',
//            'trending',
//            'new_arrivals',
//            'on_sale',
//            'low_stock',
//        ],
//        'Post' => [
//            'published',
//            'featured',
//            'popular',
//            'recent',
//        ],
//        'User' => [
//            'admins',
//            'verified',
//            'active_users',
//        ],
//        'Order' => [
//            'pending',
//            'completed',
//            'cancelled',
//        ],
    ],

    // Auto-clear cache when models change
    'observers' => [
        'enabled' => env('BLUEPRINT_OBSERVERS_ENABLED', true),
        'models' => [
            // Add your models here
            // App\Models\User::class,
            // App\Models\Post::class,
        ],
    ],

    // Authorization settings
    'authorization' => [
        'enabled' => env('BLUEPRINT_AUTHORIZATION_ENABLED', true),
        'super_admin_role' => env('BLUEPRINT_SUPER_ADMIN_ROLE', 'superAdmin'),
    ],

    // Pagination settings
    'pagination' => [
        'per_page' => env('BLUEPRINT_PER_PAGE', 15),
        'max_per_page' => 100,
    ],

    // HashIDs for ID obfuscation
    'hashids' => [
        'default' => 'main',
        'connections' => [
            'main' => [
                'salt' => env('HASHIDS_SALT', env('APP_KEY')),
                'length' => 6,
                'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
            ],
        ],
    ],
];