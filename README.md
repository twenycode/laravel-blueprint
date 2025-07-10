# Laravel Blueprint

A comprehensive architecture and utilities package for Laravel applications that provides a standardized structure, core components, and common patterns to accelerate development.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/twenycode/laravel-blueprint.svg?style=flat-square)](https://packagist.org/packages/twenycode/laravel-blueprint)
[![Total Downloads](https://img.shields.io/packagist/dt/twenycode/laravel-blueprint.svg?style=flat-square)](https://packagist.org/packages/twenycode/laravel-blueprint)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Introduction

Laravel Blueprint provides a solid foundation for building Laravel applications with a clean architecture, standardized patterns, and reusable components. It implements the repository pattern, service layer, and includes numerous utilities and base classes to streamline your development process.

## Features

- **Repository Pattern** - Comprehensive data access layer with built-in caching support
- **Service Layer** - Business logic abstraction with database transaction management
- **Resource Controllers** - Base controllers with CRUD operations, error handling, and flash messaging
- **Model Enhancements** - Feature-rich base model with common attributes and methods
- **Dual ID Support** - Choose between hashed integer IDs or native Laravel UUIDs
- **Caching System** - Automatic cache management with model observers
- **Helper Functions** - Extensive utility functions for dates, text, and numbers
- **Error Handling** - Standardized error handling traits across application components
- **Form Request Validation** - Permission-based request validation
- **Soft Delete Support** - Ready-to-use methods for handling soft deletes
- **Flash Messaging** - Integrated SweetAlert for beautiful flash messages and notifications
- **Custom Validation Rules** - Composite key uniqueness validation
- **API Support** - JSON response handling for API controllers

## Requirements

- PHP 8.2+
- Laravel 9.0+

## Installation

You can install this package via Composer:

```bash
composer require twenycode/laravel-blueprint
```

## Configuration

Publish the configuration files:

```bash
php artisan vendor:publish --provider="TwenyCode\LaravelBlueprint\TwenyLaravelBlueprintServiceProvider" --tag="tcb-config"
```

This will publish the following configuration files:
- `config/tweny-blueprint.php` - Main configuration settings
- `config/tweny-hashids.php` - HashIds configuration

## ID Management: Choose Your Approach

Laravel Blueprint supports two approaches for handling model IDs:

### Option 1: Hashed Integer IDs (Default)
Perfect for obfuscating sequential database IDs while maintaining integer performance.

### Option 2: Native Laravel UUIDs
Ideal for distributed systems, enhanced security, and when you need globally unique identifiers.

---

## Usage with Hashed Integer IDs

This is the traditional approach using HashIds to obfuscate integer primary keys.

### Models

```php
<?php

namespace App\Models;

use TwenyCode\LaravelBlueprint\Models\BaseModel;

class User extends BaseModel
{
    protected $fillable = [
        'name', 'email', 'password',
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    // Add custom relationships, methods, etc.
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

### Repositories

```php
<?php

namespace App\Repositories;

use App\Models\User;
use TwenyCode\LaravelBlueprint\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
        
        // Define default relationships to eager load
        $this->relationships = ['roles', 'permissions'];
    }
    
    // Add custom repository methods
    public function findByEmail(string $email)
    {
        return $this->handleError(function () use ($email) {
            return $this->model->where('email', $email)->first();
        }, 'find user by email');
    }
}
```

---

## Usage with Native UUIDs

For applications requiring UUIDs, use the specialized UUID components.

### Models

```php
<?php

namespace App\Models;

use TwenyCode\LaravelBlueprint\Models\BaseUuidModel;

class User extends BaseUuidModel
{
    protected $fillable = [
        'name', 'email', 'password',
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    // Add custom relationships, methods, etc.
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

### Repositories

```php
<?php

namespace App\Repositories;

use App\Models\User;
use TwenyCode\LaravelBlueprint\Repositories\BaseUuidRepository;

class UserRepository extends BaseUuidRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
        
        // Define default relationships to eager load
        $this->relationships = ['posts', 'profile'];
    }
    
    // Add custom repository methods
    public function findByEmail(string $email)
    {
        return $this->handleError(function () use ($email) {
            return $this->model
                ->with($this->relationships)
                ->where('email', $email)
                ->first();
        }, 'find user by email');
    }
}
```

### Services (Same for Both Approaches)

```php
<?php

namespace App\Services;

use App\Repositories\UserRepository;
use TwenyCode\LaravelBlueprint\Services\BaseService;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }
    
    // Business logic works the same regardless of ID type
    public function createUser(array $data)
    {
        return $this->transaction(function () use ($data) {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            $data['is_active'] = $data['is_active'] ?? true;
            
            return $this->repository->create($data);
        });
    }
}
```

### Controllers (Same for Both Approaches)

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;
use TwenyCode\LaravelBlueprint\Controllers\BaseResourceController;

class UserController extends BaseResourceController
{
    public function __construct(UserService $service)
    {
        $this->layer = $service;
        $this->controllerName = 'User';
        $this->baseViewName = 'users';
        $this->baseRouteName = 'users';
        $this->resourceVariable = 'user';
        $this->hasRelationShips = true;
    }
    
    public function store(UserStoreRequest $request)
    {
        return $this->handleError(function () use ($request) {
            $this->layer->createUser($request->validated());
            return $this->success('User created successfully');
        }, 'create user', $request->input());
    }
    
    public function update(UserUpdateRequest $request, $id)
    {
        return $this->handleError(function () use ($request, $id) {
            $this->layer->update($id, $request->validated());
            return $this->successRoute('users.index', 'User updated successfully');
        }, 'update user', $request->input());
    }
}
```

---

## Migrations

### For Hashed Integer IDs

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Auto-incrementing integer primary key
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
```

### For UUIDs

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID primary key
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
```

---

## Routes

### For Both ID Types

```php
// routes/web.php
use App\Http\Controllers\UserController;

Route::resource('users', UserController::class)->parameters([
    'users' => 'user:id' // Works for both hashed IDs and UUIDs
]);

// API Routes
// routes/api.php
use App\Http\Controllers\Api\UserController as ApiUserController;

Route::prefix('v1')->group(function () {
    Route::apiResource('users', ApiUserController::class)->parameters([
        'users' => 'user:id'
    ]);
});
```

---

## SweetAlert Integration

This package includes integration with [RealRashid/SweetAlert](https://github.com/realrashid/sweet-alert) for beautiful flash messages and notifications.

### Setup SweetAlert

1. Make sure SweetAlert is installed:

```bash
composer require realrashid/sweet-alert
```

2. Publish SweetAlert assets:

```bash
php artisan vendor:publish --provider="RealRashid\SweetAlert\SweetAlertServiceProvider" --tag=sweetalert-config
```

3. Include the SweetAlert view component in your layout:

```php
<!-- Before closing </body> tag -->
@include('sweetalert::alert')
```

4. If you encounter JavaScript errors about Swal not being defined, add SweetAlert's CDN in your layout:

```html
<!-- In your <head> section -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Before closing </body> tag (before the @include above) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
```

### Using Flash Messages

The Base Controller and Resource Controllers provide built-in methods for flash messaging:

```php
// Display a success message
$this->successMsg('Operation completed successfully');

// Display an error message
$this->errorMsg('Something went wrong');

// Redirect with a success message
return $this->success('Changes saved');

// Redirect to a route with a success message
return $this->successRoute('users.index', 'User created successfully');

// Redirect with an error message
return $this->error('Operation failed');

// Redirect to a route with an error message
return $this->errorRoute('users.index', 'User could not be created');
```

## Available Repository Methods

**Model & Utility Methods:**
- `model()` - Get model instance
- `decode($id)` - Decode hashed ID (integer IDs) / Pass-through (UUIDs)

**Basic CRUD Operations:**
- `getAll()` - Retrieve all records
- `create(array $data)` - Create new record
- `show($id)` - Show record by ID (alias for findById)
- `findById($id)` - Find record by ID
- `update($id, array $data)` - Update existing record
- `delete($id)` - Delete record

**Relationship Methods:**
- `getAllWithRelationships()` - Get all records with relationships
- `getActiveDataWithRelations()` - Get active records with relationships
- `getInactiveDataWithRelations()` - Get inactive records with relationships

**Status-Based Methods:**
- `getActiveData()` - Get all active records
- `pluckActiveData()` - Get active records as name-id pairs
- `updateActiveStatus($object, $status = null)` - Toggle active status

**Soft Delete Methods:**
- `trashed()` - Get soft-deleted records
- `findTrashedById($id)` - Find soft-deleted record by ID
- `restore($id)` - Restore soft-deleted record
- `forceDelete($id)` - Permanently delete record

**Query & Search Methods:**
- `searchByQuery(string $searchTerm)` - Search records by query
- `liveSearch(string $searchTerm)` - Live search for records
- `getInformationBy(string $filterTerm)` - Get filtered information
- `paginateWithRelationships($perPage = 25)` - Paginate with relationships

**Utility Methods:**
- `deleteWhere($column, $value)` - Delete records based on column value
- `orderBy($column, $value)` - Get records ordered by column

**Caching Methods (via RepositoryCacheTrait):**
- `setCacheDuration(int $minutes)` - Set cache duration
- `generateCacheKey(...$args)` - Generate cache key
- `forgetCache(array $keys)` - Forget specific cache keys
- `clearCacheKey()` - Clear all cache keys for model

## Available Service Methods

**Model & Utility Methods:**
- `model()` - Get model instance

**Basic CRUD Operations:**
- `getAll()` - Retrieve all records
- `create(array $data)` - Create new record
- `show($id)` - Show record by ID
- `findById($id)` - Find record by ID
- `update($id, array $data)` - Update existing record
- `delete($id)` - Delete record

**Relationship Methods:**
- `getAllWithRelationships()` - Get all records with relationships
- `getActiveDataWithRelations()` - Get active records with relationships
- `getInactiveDataWithRelations()` - Get inactive records with relationships

**Status Methods:**
- `updateActiveStatus($modelId, $status = null)` - Update active status

**Soft Delete Methods:**
- `trashed()` - Get soft-deleted records
- `restore($id)` - Restore soft-deleted record
- `forceDelete($id)` - Permanently delete record

**Query & Search Methods:**
- `searchByQuery(string $searchTerm)` - Search records by query
- `liveSearch(string $searchTerm)` - Live search for records
- `getInformationBy(string $filterTerm)` - Get filtered information

**Transaction Support:**
- `transaction(Closure $callback, int $attempts = 1)` - Execute within database transaction

## Available Controller Methods

### Web Resource Controllers

**Available Controller Methods:**
- `index()` - Display listing of resources
- `create()` - Show form for creating new resource
- `processStore($request)` - Store newly created resource
- `show($id)` - Display specified resource
- `edit($id)` - Show form for editing resource
- `processUpdate($request, $id)` - Update specified resource
- `destroy($id)` - Remove specified resource
- `trashed()` - Get soft-deleted records
- `restore($id)` - Restore soft-deleted resource
- `forceDelete($id)` - Permanently delete resource
- `updateActiveStatus($id)` - Update active status
- `authorizeAction($action, $object = null)` - Check permissions

### API Resource Controllers

**Available API Controller Methods:**
- `index()` - Get all resources (JSON)
- `processStore($request)` - Create new resource (JSON)
- `show($id)` - Get specific resource (JSON)
- `processUpdate($request, $id)` - Update resource (JSON)
- `destroy($id)` - Delete resource (JSON)
- `trashed()` - Get soft-deleted resources (JSON)
- `restore($id)` - Restore soft-deleted resource (JSON)
- `forceDelete($id)` - Permanently delete resource (JSON)
- `updateActiveStatus($id)` - Update active status (JSON)

## Available Model Features

### For Hashed Integer IDs
- **ID Hashing:** `encode()`, `decode($value)`, `getEncodedIdAttribute()`
- **Activity Logging:** Automatic logging of model changes
- **Date Mutators:** Automatic date formatting for `start_date`, `end_date`, `date`
- **Date Accessors:** Formatted date display methods
- **Status Methods:** `activate()`, `deactivate()`
- **Scopes:** `active()`, `inactive()`, `ordered()`, `boolean()`
- **Utility Methods:** `returnID($data)` for ID lookup by name

### For UUIDs
- **UUID Helpers:** `isValidUuid()`, `findById()`, `findByIdOrFail()`
- **Activity Logging:** Automatic logging of model changes (inherited)
- **Date Mutators:** Automatic date formatting (inherited)
- **Date Accessors:** Formatted date display methods (inherited)
- **Status Methods:** `activate()`, `deactivate()` (inherited)
- **Scopes:** `active()`, `inactive()`, `ordered()`, `boolean()`, `byId()` (inherited + UUID-specific)
- **Route Binding:** Optimized for UUID URLs

## Form Requests

Create form requests by extending the base form request:

```php
<?php

namespace App\Http\Requests;

use TwenyCode\LaravelBlueprint\Http\Requests\BaseFormRequest;

class UserStoreRequest extends BaseFormRequest
{
    public function authorize()
    {
        return $this->checkPermission('create-user');
    }
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
```

**Available Form Request Methods:**
- `checkPermission($permission)` - Check user permission with super admin bypass

## Custom Validation Rules

### Composite Key Uniqueness Rule

For validating unique combinations of fields:

```php
<?php

namespace App\Http\Requests;

use TwenyCode\LaravelBlueprint\Http\Requests\BaseFormRequest;
use TwenyCode\LaravelBlueprint\Rules\CompositeKeyUniquenessChecker;

class ProjectMemberRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                new CompositeKeyUniquenessChecker(
                    'project_members', 
                    [
                        'project_id' => $this->project_id,
                        'user_id' => $this->user_id,
                    ],
                    $this->route('id') // For updates
                )
            ],
        ];
    }
}
```

## Helper Functions

The package provides a wide range of helper functions:

### Date Helpers

```php
// Convert date formats
$formattedDate = dateTimeConversion('2023-01-01', 'd M Y');  // "01 Jan 2023"

// Calculate days between dates
$daysBetween = numberOfDays('2023-01-01', '2023-01-15');  // 14

// Calculate age in days
$age = calculateAge('1990-01-01');  // Age in days

// Get human-readable date difference
$diff = dateDifference('2023-01-01', '2023-02-01');  // "1 months"

// Calculate remaining days
$remaining = calculateRemainingDays('2023-12-31');  // Days until date

// Format time ago
$timeAgo = formatTimeAgo('2023-01-01 12:00:00');  // "3 months ago"

// Format date ranges
$range = formatDateDuration('2023-01-15', '2023-02-20');  // "15 Jan - 20 Feb, 2023"
```

### Number Helpers

```php
// Format file sizes
$fileSize = formatFileSize(1024 * 1024);  // "1.00 MB"

// Format currency
$amount = formatCurrencyDecimal(1234.56);  // "1,234.56"
$rounded = formatCurrency(1234.56);  // "1,235"
$money = formatMoney(1234.56);  // "$ 1,234.56"

// Calculate percentages
$value = calculatePercentNumber(15, 200);  // 30
```

### Text Helpers

```php
// Manipulate strings
$withoutUnderscores = removeUnderscore('hello_world');  // "hello world"
$withUnderscores = addUnderscore('hello world');  // "hello_world"
$snakeCase = snake('HelloWorld');  // "hello_world"
$headlined = headline('user_profile_settings');  // "User Profile Settings"

// Work with plurals
$plural = pluralize('category');  // "categories"
$pluralVar = pluralizeVariableName('userProfile');  // "userProfiles"

// Handle pluralization
$suffix = plural(1);  // ""
$suffix = plural(2);  // "s"

// Trim text
$trimmed = trimWords('This is a long text that needs trimming', 5);  // "This is a long text..."
$trimmedHtml = trimHtmlWords('<p>This is a <strong>long</strong> text</p>', 3);  // "<p>This is a...</p>"
```

## Caching System

### Repository Caching

The package includes automatic caching for repository methods:

```php
// Repository methods are automatically cached
$users = $userRepository->getAll();  // Cached for 24 hours by default

// Customize cache duration
$users = $userRepository->setCacheDuration(60)->getAll();  // Cache for 1 hour

// Clear cache manually
$userRepository->clearCacheKey();

// Forget specific cache keys
$userRepository->forgetCache(['all', 'active']);
```

### Event Caching

For event-based caching, use the EventCacheTrait:

```php
<?php

namespace App\Repositories;

use TwenyCode\LaravelBlueprint\Repositories\BaseRepository;
use TwenyCode\LaravelBlueprint\Traits\EventCacheTrait;

class EventRepository extends BaseRepository
{
    use EventCacheTrait;
    
    public function getUpcomingEvents()
    {
        return $this->rememberEventCache('upcoming', function() {
            return $this->model->where('start_date', '>', now())->get();
        });
    }
}
```

### Model Cache Observers

Models are automatically observed for cache clearing:

```php
// In config/tweny-blueprint.php
'observable_models' => [
    \App\Models\User::class,
    \App\Models\Product::class,
],

// Cache is automatically cleared when models are created, updated, or deleted
```

## Configuration

### Cache Configuration

Configure caching behavior in `config/tweny-blueprint.php`:

```php
// Enable/disable model cache observers
'enable_cache_observers' => true,

// Common cache keys
'cache_keys' => [
    'all', 'with_relationship', 'active_with_relationship', 
    'inactive_with_relationship', 'trashed', 'paginated', 'active', 'pluck_active'
],

// Default cache duration in minutes
'cache_duration' => 1440, // 24 hours
```

### Hash IDs Configuration

Configure ID hashing in `config/tweny-hashids.php`:

```php
'connections' => [
    'main' => [
        'salt' => env('HASHIDS_SALT', config('app.key')),
        'length' => 6,
        'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
    ],
],
```

### Model Configuration

Configure model behavior in `config/tweny-blueprint.php`:

```php
// Observable models (automatically clear cache on changes)
'observable_models' => [
    \App\Models\User::class,
    \App\Models\Product::class,
],

// User-specific cache models (for per-user data)
'user_cache_models' => [
    'Contract', 'Task', 'Project'
],
```

### Authorization Configuration

Configure authorization settings in `config/tweny-blueprint.php`:

```php
// Whether to check authorization by default in controllers
'check_authorization' => true,

// Super admin role name
'super_admin_role' => 'superAdmin',
```

## Choosing Between ID Approaches

### Use Hashed Integer IDs When:
- You want to maintain integer primary key performance
- Sequential ID exposure is a concern but not critical
- Working with existing integer-based systems
- Need smaller URL parameters

### Use Native UUIDs When:
- Building distributed systems or microservices
- Enhanced security is paramount
- Need offline ID generation capability
- Working with multi-tenant applications
- Building public APIs where ID exposure matters

### Performance Considerations

**Hashed Integer IDs:**
- ✅ Fast integer operations
- ✅ Smaller storage footprint
- ✅ Better MySQL performance
- ❌ Requires encoding/decoding

**Native UUIDs:**
- ✅ No encoding overhead
- ✅ Globally unique
- ✅ Laravel native support
- ❌ Larger storage (36 chars)
- ❌ Slightly slower indexing

## Error Handling

All components use standardized error handling:

```php
// In repositories, services, and controllers
protected function handleError(callable $function, string $context, mixed $request = null, string $msg = 'Something went wrong')
{
    try {
        return $function();
    } catch (Exception $e) {
        // Automatic logging and graceful error handling
        Log::error("Failed to {$context}: " . $e->getMessage());
        
        // Return appropriate response based on context
        if (method_exists($this, 'error')) {
            return $this->error($msg);
        }
        
        throw $e;
    }
}
```

## Testing

The package is designed to be easily testable:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_user()
    {
        $repository = new UserRepository(new User());
        
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];
        
        $user = $repository->create($userData);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }
    
    public function test_can_find_active_users()
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);
        
        $repository = new UserRepository(new User());
        $activeUsers = $repository->getActiveData();
        
        $this->assertCount(3, $activeUsers);
    }
}
```

## Extending The Package

### Custom Repositories

Add specialized queries and methods to your repository classes:

```php
public function findActiveByEmail($email)
{
    return $this->handleError(function () use ($email) {
        return $this->model
            ->where('email', $email)
            ->where('is_active', true)
            ->first();
    }, 'find active user by email');
}

public function getPopularUsers($limit = 10)
{
    return $this->handleError(function () use ($limit) {
        return $this->model
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }, 'get popular users');
}
```

### Custom Services

Add complex business logic in your service classes:

```php
public function registerUser(array $data)
{
    return $this->transaction(function () use ($data) {
        // Create user
        $user = $this->repository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        
        // Assign role
        $user->assignRole('user');
        
        // Send welcome email
        Mail::to($user->email)->send(new WelcomeEmail($user));
        
        return $user;
    });
}

public function bulkUpdateStatus(array $userIds, bool $status)
{
    return $this->transaction(function () use ($userIds, $status) {
        foreach ($userIds as $userId) {
            $this->repository->update($userId, ['is_active' => $status]);
        }
        return count($userIds);
    });
}
```

## Performance Considerations

### Caching Strategy

- Repository methods are cached by default for 24 hours
- User-specific caching for employee role data
- Automatic cache invalidation on model changes
- Configurable cache duration per operation

### Database Optimization

- Eager loading relationships by default
- Pagination support for large datasets
- Selective querying with active/inactive scopes
- Efficient soft delete handling

### Memory Management

- Lazy loading of relationships when not needed
- Configurable pagination limits
- Efficient query building with repository pattern

## Migration Between ID Types

### From Integer IDs to UUIDs

If you're migrating from integer IDs to UUIDs, here's a general approach:

```php
// Step 1: Create a migration to add UUID column
Schema::table('users', function (Blueprint $table) {
    $table->uuid('uuid')->nullable()->after('id');
    $table->index('uuid');
});

// Step 2: Populate UUIDs for existing records
User::whereNull('uuid')->chunk(100, function ($users) {
    foreach ($users as $user) {
        $user->update(['uuid' => \Str::uuid()]);
    }
});

// Step 3: Update your models and repositories
// Change from: class User extends BaseModel
// To: class User extends BaseUuidModel

// Change from: class UserRepository extends BaseRepository  
// To: class UserRepository extends BaseUuidRepository

// Step 4: Later, after updating all references:
// 1. Drop old ID foreign keys
// 2. Rename uuid to id
// 3. Make id primary key
// 4. Update foreign key references
```

### From UUIDs to Integer IDs

```php
// Step 1: Add integer ID column
Schema::table('users', function (Blueprint $table) {
    $table->bigIncrements('new_id')->after('id');
});

// Step 2: Update your models and repositories
// Change from: class User extends BaseUuidModel
// To: class User extends BaseModel

// Change from: class UserRepository extends BaseUuidRepository
// To: class UserRepository extends BaseRepository

// Step 3: Later, after updating all references:
// 1. Drop UUID foreign keys
// 2. Rename new_id to id
// 3. Make id primary key
// 4. Update foreign key references
```

## Troubleshooting

### Common Issues

1. **Cache not clearing**: Ensure model observers are registered in config
2. **Hash IDs not working**: Check HASHIDS_SALT environment variable
3. **UUID validation failing**: Ensure proper UUID format validation
4. **Permission errors**: Verify user roles and permissions setup
5. **SweetAlert not displaying**: Check JavaScript console for errors

### Debug Mode

Enable debug logging by adding to your `.env`:

```env
LOG_LEVEL=debug
```

### Performance Monitoring

Monitor cache hit rates and query performance:

```php
// Log cache operations
Log::info('Cache hit for key: ' . $cacheKey);

// Monitor query counts
DB::enableQueryLog();
// ... your operations
$queries = DB::getQueryLog();
Log::info('Query count: ' . count($queries));
```

### UUID-Specific Debugging

```php
// Validate UUID format
if (!\Str::isUuid($id)) {
    Log::warning("Invalid UUID format: {$id}");
}

// Check model configuration
if (!in_array('id', $model->uniqueIds())) {
    Log::error("Model not configured for UUID primary key");
}
```

## Best Practices

### Code Organization

1. **Consistent Approach**: Choose either hashed IDs or UUIDs for your entire application
2. **Repository Pattern**: Keep database logic in repositories
3. **Service Layer**: Business logic belongs in services
4. **Error Handling**: Use the built-in error handling traits
5. **Caching**: Leverage automatic caching for better performance

### Security Considerations

1. **ID Exposure**: UUIDs provide better security than hashed integer IDs
2. **Permission Checks**: Always use the authorization features
3. **Input Validation**: Validate UUIDs in form requests
4. **Rate Limiting**: Consider rate limiting for API endpoints

### Performance Tips

1. **Eager Loading**: Define relationships in repository constructors
2. **Caching**: Use appropriate cache durations for your data
3. **Indexing**: Ensure proper database indexing for UUIDs
4. **Pagination**: Use pagination for large datasets

## API Documentation

### Response Formats

The package provides consistent API response formats:

```json
// Success Response
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "user": {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}

// Error Response  
{
    "success": false,
    "message": "User not found"
}
```

### Status Codes

- `200` - Success
- `201` - Created
- `204` - No Content (for deletions)
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `vendor/bin/phpunit`
4. Follow PSR-12 coding standards
5. Add tests for new features

### Reporting Issues

When reporting issues, please include:

1. Laravel version
2. PHP version
3. Package version
4. Steps to reproduce
5. Expected vs actual behavior
6. Error messages/logs

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md). createUser(array $data)
{
return $this->transaction(function () use ($data) {
// Hash password
if (isset($data['password'])) {
$data['password'] = Hash::make($data['password']);
}

            // Set default active status
            $data['is_active'] = $data['is_active'] ?? true;
            
            return $this->repository->create($data);
        });
    }
    
    /**
     * Update user profile
     */
    public function updateUserProfile(string $id, array $profileData)
    {
        return $this->transaction(function () use ($id, $profileData) {
            $user = $this->repository->findById($id);
            
            // Update user basic info
            if (isset($profileData['name'])) {
                $user->update(['name' => $profileData['name']]);
            }
            
            // Update or create profile
            if (isset($profileData['bio']) || isset($profileData['avatar'])) {
                $user->profile()->updateOrCreate(
                    ['user_id' => $id],
                    [
                        'bio' => $profileData['bio'] ?? null,
                        'avatar' => $profileData['avatar'] ?? null,
                    ]
                );
            }
            
            return $user->fresh(['profile']);
        });
    }
    
    /**
     * Activate multiple users
     */
    public function activateUsers(array $ids)
    {
        return $this->transaction(function () use ($ids) {
            $count = 0;
            foreach ($ids as $id) {
                try {
                    $this->repository->update($id, ['is_active' => true]);
                    $count++;
                } catch (\Exception $e) {
                    \Log::warning("Failed to activate user {$id}: " . $e->getMessage());
                }
            }
            return $count;
        });
    }
}
```

## 4. Controller Setup

Create a controller using the regular `BaseResourceController`:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;
use TwenyCode\LaravelBlueprint\Controllers\BaseResourceController;

class UserController extends BaseResourceController
{
    public function __construct(UserService $service)
    {
        $this->layer = $service;
        $this->controllerName = 'User';
        $this->baseViewName = 'users';
        $this->baseRouteName = 'users';
        $this->resourceVariable = 'user';
        $this->hasRelationShips = true;
    }
    
    public function store(UserStoreRequest $request)
    {
        return $this->handleError(function () use ($request) {
            $this->layer->createUser($request->validated());
            return $this->success('User created successfully');
        }, 'create user', $request->input());
    }
    
    public function update(UserUpdateRequest $request, $id)
    {
        return $this->handleError(function () use ($request, $id) {
            $this->layer->update($id, $request->validated());
            return $this->successRoute('users.index', 'User updated successfully');
        }, 'update user', $request->input());
    }
}
```

## 5. API Controller Setup

For API endpoints, use the regular `BaseApiResourceController`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;
use TwenyCode\LaravelBlueprint\Controllers\BaseApiResourceController;

class UserController extends BaseApiResourceController
{
    public function __construct(UserService $service)
    {
        $this->layer = $service;
        $this->controllerName = 'User';
        $this->baseRouteName = 'api.users';
        $this->resourceVariable = 'user';
        $this->hasRelationShips = true;
    }
    
    public function store(UserStoreRequest $request)
    {
        return $this->handleError(function () use ($request) {
            $user = $this->layer->createUser($request->validated());
            return $this->successResponse(['user' => $user], 'User created successfully', 201);
        }, 'create user', $request->input());
    }
    
    public function update(UserUpdateRequest $request, $id)
    {
        return $this->handleError(function () use ($request, $id) {
            $user = $this->layer->update($id, $request->validated());
            return $this->successResponse(['user' => $user], 'User updated successfully');
        }, 'update user', $request->input());
    }
}}, 'activate user');
    }
    
    /**
     * Deactivate user
     */
    public function deactivate($uuid)
    {
        return $this->handleError(function () use ($uuid) {
            $this->layer->updateActiveStatus($uuid, false);
            return $this->success('User deactivated successfully');
        }, 'deactivate user');
    }
}
```

## 5. API Controller Setup

For API endpoints, create a controller that extends `BaseApiResourceController`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;
use TwenyCode\LaravelBlueprint\Controllers\BaseApiResourceController;

class UserController extends BaseApiResourceController
{
    public function __construct(UserService $service)
    {
        $this->layer = $service;
        $this->controllerName = 'User';
        $this->baseRouteName = 'api.users';
        $this->resourceVariable = 'user';
        $this->hasRelationShips = true;
    }
    
    public function store(UserStoreRequest $request)
    {
        return $this->handleError(function () use ($request) {
            $user = $this->layer->createUser($request->validated());
            return $this->successResponse(['user' => $user], 'User created successfully', 201);
        }, 'create user', $request->input());
    }
    
    public function update(UserUpdateRequest $request, $uuid)
    {
        return $this->handleError(function () use ($request, $uuid) {
            $user = $this->layer->update($uuid, $request->validated());
            return $this->successResponse(['user' => $user], 'User updated successfully');
        }, 'update user', $request->input());
    }
    
    /**
     * Batch activate users
     */
    public function batchActivate(Request $request)
    {
        $request->validate([
            'uuids' => 'required|array',
            'uuids.*' => 'required|string|uuid'
        ]);
        
        return $this->handleError(function () use ($request) {
            $count = $this->layer->activateUsers($request->uuids);
            return $this->successResponse(
                ['activated_count' => $count], 
                "{$count} users activated successfully"
            );
        }, 'batch activate users');
    }
    
    /**
     * Batch deactivate users
     */
    public function batchDeactivate(Request $request)
    {
        $request->validate([
            'uuids' => 'required|array',
            'uuids.*' => 'required|string|uuid'
        ]);
        
        return $this->handleError(function () use ($request) {
            $count = $this->layer->deactivateUsers($request->uuids);
            return $this->successResponse(
                ['deactivated_count' => $count], 
                "{$count} users deactivated successfully"
            );
        }, 'batch deactivate users');
    }
}
```

## 6. Form Request Setup

Create form requests that work with UUIDs:

```php
<?php

namespace App\Http\Requests;

use TwenyCode\LaravelBlueprint\Http\Requests\BaseFormRequest;

class UserStoreRequest extends BaseFormRequest
{
    public function authorize()
    {
        return $this->checkPermission('create-user');
    }
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ];
    }
}
```

```php
<?php

namespace App\Http\Requests;

use TwenyCode\LaravelBlueprint\Http\Requests\BaseFormRequest;

class UserUpdateRequest extends BaseFormRequest
{
    public function authorize()
    {
        return $this->checkPermission('update-user');
    }
    
    public function rules()
    {
        // Get the UUID from the route
        $uuid = $this->route('user');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|unique:users,email,{$uuid}",
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
```

## 7. Routes Setup

Define your routes to work with UUIDs:

```php
// routes/web.php
use App\Http\Controllers\UserController;

Route::resource('users', UserController::class)->parameters([
    'users' => 'user:id' // Use UUID for route model binding
]);

// Additional routes for status management
Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

// API Routes
// routes/api.php
use App\Http\Controllers\Api\UserController as ApiUserController;

Route::prefix('v1')->group(function () {
    Route::apiResource('users', ApiUserController::class)->parameters([
        'users' => 'user:id'
    ]);
    
    // Batch operations
    Route::post('users/batch/activate', [ApiUserController::class, 'batchActivate']);
    Route::post('users/batch/deactivate', [ApiUserController::class, 'batchDeactivate']);
});
```

## 8. Migration Example

Create migrations for UUID-based tables:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
```

## 9. Testing Examples

Here are some test examples for UUID-based models:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected UserService $userService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService(new UserRepository(new User()));
    }
    
    /** @test */
    public function it_can_create_user_with_uuid()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];
        
        $user = $this->userService->createUser($userData);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue(\Str::isUuid($user->id));
        $this->assertEquals('John Doe', $user->name);
        $this->assertTrue($user->is_active);
    }
    
    /** @test */
    public function it_can_find_user_by_uuid()
    {
        $user = User::factory()->create();
        
        $foundUser = $this->userService->findById($user->id);
        
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($user->email, $foundUser->email);
    }
    
    /** @test */
    public function it_can_update_user_by_uuid()
    {
        $user = User::factory()->create(['name' => 'Original Name']);
        
        $updatedUser = $this->userService->update($user->id, [
            'name' => 'Updated Name'
        ]);
        
        $this->assertEquals('Updated Name', $updatedUser->name);
    }
    
    /** @test */
    public function it_can_batch_activate_users()
    {
        $users = User::factory()->count(3)->create(['is_active' => false]);
        $uuids = $users->pluck('id')->toArray();
        
        $count = $this->userService->activateUsers($uuids);
        
        $this->assertEquals(3, $count);
        
        foreach ($users as $user) {
            $this->assertTrue($user->fresh()->is_active);
        }
    }
    
    /** @test */
    public function it_validates_uuid_format()
    {
        $validUuid = \Str::uuid()->toString();
        $invalidUuid = 'not-a-uuid';
        
        $this->assertTrue($this->userService->isValidUuid($validUuid));
        $this->assertFalse($this->userService->isValidUuid($invalidUuid));
    }
}
```

## 10. Configuration

Add your UUID models to the cache observer configuration:

```php
// config/tweny-blueprint.php

'observable_models' => [
    \App\Models\User::class,
    \App\Models\Post::class,
    \App\Models\Category::class,
    // Add other UUID-based models here
],
```

## 11. Benefits of UUID Approach

### Advantages:
- **Security**: UUIDs don't reveal sequence information
- **Scalability**: Globally unique across distributed systems
- **No Collision**: Extremely low probability of duplicates
- **Laravel Native**: Uses Laravel's built-in HasUuids trait
- **Performance**: Direct UUID operations without encoding/decoding

### When to Use:
- Applications requiring high security
- Distributed systems or microservices
- APIs that expose IDs publicly
- Systems requiring offline ID generation
- Multi-tenant applications

### Performance Considerations:
- UUIDs are 36 characters vs integer IDs
- Slightly larger database storage
- Index performance may be slightly slower
- Memory usage is higher per record

## 12. Migration from Integer IDs

If you're migrating from integer IDs to UUIDs, here's a general approach:

```php
// Create a migration to add UUID column
Schema::table('users', function (Blueprint $table) {
    $table->uuid('uuid')->nullable()->after('id');
    $table->index('uuid');
});

// Populate UUIDs for existing records
User::whereNull('uuid')->chunk(100, function ($users) {
    foreach ($users as $user) {
        $user->update(['uuid' => \Str::uuid()]);
    }
});

// Later, after updating all references:
// 1. Drop old ID foreign keys
// 2. Rename uuid to id
// 3. Make id primary key
// 4. Update foreign key references
```

This UUID-based approach gives you all the functionality of the original blueprint package while leveraging Laravel's native UUID support for better performance and maintainability.