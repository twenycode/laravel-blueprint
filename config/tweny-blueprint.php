<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Core Package Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for the TwenyCode LaravelBlueprint package.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Blade Directive Configuration
    |--------------------------------------------------------------------------
    */

    // Super admin role name used in blade directives
    'super_admin_role' => 'superAdmin',

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    // Enable model cache observers
    'enable_cache_observers' => true,

    // Common cache keys used for repositories
    'cache_keys' => [
        'all','with_relationship', 'active_with_relationship', 'inactive_with_relationship',
        'trashed', 'paginated','active','pluck_active'
    ],

    // Default cache duration in minutes
    'cache_duration' => 1440, // 24 hours

    /*
    |--------------------------------------------------------------------------
    | Model Classes
    |--------------------------------------------------------------------------
    |
    | Define model class mappings for relationships.
    | These can be overriden in the application config.
    */

    'models' => [
//        'country' => \App\Models\Country::class,
//        'address' => \App\Models\Address::class,
//        'employee' => \App\Models\Employee::class,
//        'department' => \App\Models\Department::class,
//        'individual' => \App\Models\Individual::class,
//        'organization' => \App\Models\Organization::class,
//        'title' => \App\Models\Title::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Observable Models
    |--------------------------------------------------------------------------
    |
    | List of models that should be observed by the ModelCacheObserver.
    | Cache will be automatically cleared for these models when they change.
    */

    'observable_models' => [
        // Add model classes to observe here
        // Example: \App\Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | User-Specific Cache Models
    |--------------------------------------------------------------------------
    |
    | Models that should use user-specific caching.
    | Useful for models that have user-specific views or access controls.
    */

    'user_cache_models' => [
//        'Contract', 'Ticket', 'Task', 'Project', 'LeaveRequest'
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization Configuration
    |--------------------------------------------------------------------------
    */

    // Whether to check authorization by default in controllers
    'check_authorization' => true,
];