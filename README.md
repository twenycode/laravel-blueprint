# Laravel Blueprint

A comprehensive architecture and utilities package for Laravel applications that provides a standardized structure, core components, and common patterns to accelerate development.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/twenycode/laravel-blueprint.svg?style=flat-square)](https://packagist.org/packages/twenycode/laravel-blueprint)
[![Total Downloads](https://img.shields.io/packagist/dt/twenycode/laravel-blueprint.svg?style=flat-square)](https://packagist.org/packages/twenycode/laravel-blueprint)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [ID Management Approaches](#id-management-approaches)
- [Quick Start Guide](#quick-start-guide)
- [Core Components](#core-components)
- [Helper Functions](#helper-functions)
- [Caching System](#caching-system)
- [SweetAlert Integration](#sweetalert-integration)
- [Testing](#testing)
- [Migration Guide](#migration-guide)
- [Best Practices](#best-practices)
- [Contributing](#contributing)
- [License](#license)

## Introduction

Laravel Blueprint provides a solid foundation for building Laravel applications with a clean architecture, standardized patterns, and reusable components. It implements the repository pattern, service layer, and includes numerous utilities and base classes to streamline your development process.

## Features

### 🏗️ **Architecture Components**
- **Repository Pattern** - Comprehensive data access layer with built-in caching support
- **Service Layer** - Business logic abstraction with database transaction management
- **Resource Controllers** - Base controllers with CRUD operations, error handling, and flash messaging
- **Form Request Validation** - Permission-based request validation with custom rules

### 🔧 **Model & Database**
- **Enhanced Models** - Feature-rich base models with common attributes and methods
- **Dual ID Support** - Choose between hashed integer IDs or native Laravel UUIDs
- **Soft Delete Support** - Ready-to-use methods for handling soft deletes
- **Migration Helpers** - Standardized migration patterns for both ID types

### 🚀 **Performance & Caching**
- **Intelligent Caching** - Automatic cache management with model observers
- **User-Specific Caching** - Context-aware caching for multi-tenant applications
- **Cache Invalidation** - Automatic cache clearing on model changes

### 🎨 **UI & UX**
- **Flash Messaging** - Integrated SweetAlert for beautiful notifications
- **Error Handling** - Standardized error handling across all components
- **API Support** - JSON response handling for API controllers

### 🛠️ **Utilities & Helpers**
- **Helper Functions** - Extensive utility functions for dates, text, and numbers
- **Custom Validation Rules** - Composite key uniqueness validation
- **Text Processing** - Advanced text manipulation and formatting functions

## Requirements

- **PHP:** 8.2 or higher
- **Laravel:** 9.0, 10.0, or 11.0
- **Dependencies:** Hashids, SweetAlert

## Installation

Install the package via Composer:

```bash
composer require twenycode/laravel-blueprint
```

## Configuration

Publish the configuration files:

```bash
php artisan vendor:publish --provider="TwenyCode\LaravelBlueprint\TwenyLaravelBlueprintServiceProvider" --tag="tcb-config"
```

This publishes:
- `config/tweny-blueprint.php` - Main configuration settings

## ID Management Approaches

Laravel Blueprint supports two approaches for handling model IDs. Choose the one that best fits your application's needs:

### 🔢 **Option 1: Hashed Integer IDs (Traditional)**
Perfect for obfuscating sequential database IDs while maintaining integer performance.

**When to use:**
- Working with existing integer-based systems
- Need smaller URL parameters
- Performance is critical for large datasets
- Sequential ID exposure is a minor concern

### 🔑 **Option 2: Native Laravel UUIDs (Recommended)**
Ideal for distributed systems, enhanced security, and when you need globally unique identifiers.

**When to use:**
- Building new applications
- Enhanced security is paramount
- Working with distributed systems or microservices
- Need offline ID generation capability
- Building public APIs where ID exposure matters

---

## Quick Start Guide

### Using Hashed Integer IDs

#### 1. Create a Model
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
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

#### 2. Create a Repository
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
        $this->relationships = ['roles', 'permissions'];
    }
    
    public function findByEmail(string $email)
    {
        return $this->handleError(function () use ($email) {
            return $this->model->where('email', $email)->first();
        }, 'find user by email');
    }
}
```

#### 3. Create a Service
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

#### 4. Create a Controller
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
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
}
```

### Using Native UUIDs

#### 1. Create a Model
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
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

#### 2. Create a Repository
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
        $this->relationships = ['posts', 'profile'];
    }
    
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

#### 3. Create Migration
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

## Core Components

### Models

#### BaseModel (Hashed Integer IDs)
```php
use TwenyCode\LaravelBlueprint\Models\BaseModel;

class YourModel extends BaseModel
{
    // Your model implementation
}
```

**Features:**
- ID hashing/encoding with HashIds
- Activity logging
- Date mutators and accessors
- Status management methods
- Common scopes (active, inactive, ordered)

#### BaseUuidModel (Native UUIDs)
```php
use TwenyCode\LaravelBlueprint\Models\BaseUuidModel;

class YourModel extends BaseUuidModel
{
    // Your model implementation
}
```

**Features:**
- Native Laravel UUID support
- UUID validation helpers
- Activity logging (inherited)
- Date mutators and accessors (inherited)
- Status management methods (inherited)

### Repositories

#### BaseRepository
```php
use TwenyCode\LaravelBlueprint\Repositories\BaseRepository;

class YourRepository extends BaseRepository
{
    protected array $relationships = ['relation1', 'relation2'];
    
    public function __construct(YourModel $model)
    {
        parent::__construct($model);
    }
}
```

**Available Methods:**
- `getAll()` - Get all records
- `getAllWithRelationships()` - Get all with relationships
- `getActiveData()` - Get active records
- `create(array $data)` - Create new record
- `findById($id)` - Find by ID
- `update($id, array $data)` - Update record
- `delete($id)` - Delete record
- `trashed()` - Get soft-deleted records
- `restore($id)` - Restore soft-deleted record
- `forceDelete($id)` - Permanently delete

### Services

#### BaseService
```php
use TwenyCode\LaravelBlueprint\Services\BaseService;

class YourService extends BaseService
{
    public function __construct(YourRepository $repository)
    {
        parent::__construct($repository);
    }
    
    public function customBusinessLogic(array $data)
    {
        return $this->transaction(function () use ($data) {
            // Your business logic here
            return $this->repository->create($data);
        });
    }
}
```

**Features:**
- Database transaction management
- Error handling
- Delegates to repository methods
- Business logic abstraction

### Controllers

#### BaseResourceController (Web)
```php
use TwenyCode\LaravelBlueprint\Controllers\BaseResourceController;

class YourController extends BaseResourceController
{
    public function __construct(YourService $service)
    {
        $this->layer = $service;
        $this->controllerName = 'YourModel';
        $this->baseViewName = 'your-views';
        $this->baseRouteName = 'your.routes';
        $this->resourceVariable = 'yourVariable';
        $this->hasRelationShips = true;
    }
}
```

#### BaseApiResourceController (API)
```php
use TwenyCode\LaravelBlueprint\Controllers\BaseApiResourceController;

class YourApiController extends BaseApiResourceController
{
    public function __construct(YourService $service)
    {
        $this->layer = $service;
        $this->controllerName = 'YourModel';
        $this->baseRouteName = 'api.your.routes';
        $this->resourceVariable = 'yourVariable';
        $this->hasRelationShips = true;
    }
}
```

### Form Requests

```php
use TwenyCode\LaravelBlueprint\Http\Requests\BaseFormRequest;

class YourRequest extends BaseFormRequest
{
    public function authorize()
    {
        return $this->checkPermission('create-your-model');
    }
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:your_table,email',
        ];
    }
}
```

---

## Helper Functions

### Date Helpers

```php
// Convert date formats
$formatted = dateTimeConversion('2023-01-01', 'd M Y'); // "01 Jan 2023"

// Calculate days between dates
$days = numberOfDays('2023-01-01', '2023-01-15'); // 14

// Calculate age in days
$age = calculateAge('1990-01-01');

// Human-readable date difference
$diff = dateDifference('2023-01-01', '2023-02-01'); // "1 months"

// Format time ago
$timeAgo = formatTimeAgo('2023-01-01 12:00:00'); // "3 months ago"

// Format date ranges
$range = formatDateDuration('2023-01-15', '2023-02-20'); // "15 Jan - 20 Feb, 2023"
```

### Number Helpers

```php
// Format file sizes
$size = formatFileSize(1024 * 1024); // "1.00 MB"

// Format currency
$decimal = formatCurrencyDecimal(1234.56); // "1,234.56"
$rounded = formatCurrency(1234.56); // "1,235"
$money = formatMoney(1234.56); // "$ 1,234.56"

// Calculate percentages
$percent = calculatePercentNumber(15, 200); // 30
```

### Text Helpers

```php
// String manipulation
$noUnderscore = removeUnderscore('hello_world'); // "hello world"
$withUnderscore = addUnderscore('hello world'); // "hello_world"
$snake = snake('HelloWorld'); // "hello_world"
$headline = headline('user_profile_settings'); // "User Profile Settings"

// Pluralization
$plural = pluralize('category'); // "categories"
$pluralVar = pluralizeVariableName('userProfile'); // "userProfiles"

// Text trimming
$trimmed = trimWords('Long text here...', 5); // "Long text here..."
$trimmedHtml = trimHtmlWords('<p>Long <strong>text</strong></p>', 3);
```

---

## Caching System

### Repository Caching

```php
// Automatic caching (24 hours default)
$users = $userRepository->getAll();

// Custom cache duration
$users = $userRepository->setCacheDuration(60)->getAll();

// Clear cache manually
$userRepository->clearCacheKey();

// Forget specific cache keys
$userRepository->forgetCache(['all', 'active']);
```

### Event Caching

```php
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

### Cache Configuration

```php
// config/tweny-blueprint.php
'enable_cache_observers' => true,
'cache_keys' => [
    'all', 'with_relationship', 'active_with_relationship',
    'inactive_with_relationship', 'trashed', 'paginated', 'active'
],
'cache_duration' => 1440, // 24 hours
'observable_models' => [
    \App\Models\User::class,
    \App\Models\Product::class,
],
```

---

## SweetAlert Integration

### Setup

1. Install SweetAlert:
```bash
composer require realrashid/sweet-alert
```

2. Publish SweetAlert assets:
```bash
php artisan vendor:publish --provider="RealRashid\SweetAlert\SweetAlertServiceProvider"
```

3. Add to your layout:
```html
<!-- In <head> -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Before </body> -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@include('sweetalert::alert')
```

### Usage

```php
// In controllers
$this->successMsg('Operation completed successfully');
$this->errorMsg('Something went wrong');

// With redirects
return $this->success('Changes saved');
return $this->successRoute('users.index', 'User created successfully');
return $this->error('Operation failed');
```

---

## Testing

### Example Test

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

### UUID Testing

```php
public function test_can_create_user_with_uuid()
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
}
```

---

## Migration Guide

### From Integer IDs to UUIDs

```php
// Step 1: Add UUID column
Schema::table('users', function (Blueprint $table) {
    $table->uuid('uuid')->nullable()->after('id');
    $table->index('uuid');
});

// Step 2: Populate UUIDs
User::whereNull('uuid')->chunk(100, function ($users) {
    foreach ($users as $user) {
        $user->update(['uuid' => \Str::uuid()]);
    }
});

// Step 3: Update models and repositories
// Change: BaseModel -> BaseUuidModel
// Change: BaseRepository -> BaseUuidRepository

// Step 4: Update foreign keys and make uuid primary
```

### From UUIDs to Integer IDs

```php
// Step 1: Add integer ID column
Schema::table('users', function (Blueprint $table) {
    $table->bigIncrements('new_id')->after('id');
});

// Step 2: Update models and repositories
// Change: BaseUuidModel -> BaseModel
// Change: BaseUuidRepository -> BaseRepository

// Step 3: Update foreign keys and make new_id primary
```

---

## Best Practices

### 🏗️ **Architecture**
1. **Consistent ID Approach**: Choose either hashed IDs or UUIDs for your entire application
2. **Repository Pattern**: Keep database logic in repositories
3. **Service Layer**: Business logic belongs in services
4. **Error Handling**: Use built-in error handling traits
5. **Caching**: Leverage automatic caching for better performance

### 🔒 **Security**
1. **ID Exposure**: UUIDs provide better security than hashed integer IDs
2. **Permission Checks**: Always use authorization features
3. **Input Validation**: Validate UUIDs in form requests
4. **Rate Limiting**: Consider rate limiting for API endpoints

### ⚡ **Performance**
1. **Eager Loading**: Define relationships in repository constructors
2. **Caching**: Use appropriate cache durations for your data
3. **Indexing**: Ensure proper database indexing for UUIDs
4. **Pagination**: Use pagination for large datasets

### 📝 **Code Organization**
1. **Naming Conventions**: Use consistent naming across components
2. **File Structure**: Organize files by feature/domain
3. **Documentation**: Document custom methods and business logic
4. **Testing**: Write tests for custom repository and service methods

---

## Advanced Usage

### Custom Validation Rules

```php
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

### Complex Service Logic

```php
class UserService extends BaseService
{
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
            $count = 0;
            foreach ($userIds as $userId) {
                $this->repository->update($userId, ['is_active' => $status]);
                $count++;
            }
            return $count;
        });
    }
}
```

---

## Troubleshooting

### Common Issues

1. **Cache not clearing**: Ensure model observers are registered in config
2. **Hash IDs not working**: Check `HASHIDS_SALT` environment variable
3. **UUID validation failing**: Ensure proper UUID format validation
4. **Permission errors**: Verify user roles and permissions setup
5. **SweetAlert not displaying**: Check JavaScript console for errors

### Debug Mode

```env
# .env
LOG_LEVEL=debug
```

### Performance Monitoring

```php
// Monitor cache operations
Log::info('Cache hit for key: ' . $cacheKey);

// Monitor query counts
DB::enableQueryLog();
// ... your operations
$queries = DB::getQueryLog();
Log::info('Query count: ' . count($queries));
```

---

## API Documentation

### Response Formats

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
- `204` - No Content
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes
4. Add tests for new functionality
5. Run tests: `vendor/bin/phpunit`
6. Follow PSR-12 coding standards
7. Commit your changes: `git commit -am 'Add amazing feature'`
8. Push to the branch: `git push origin feature/amazing-feature`
9. Create a Pull Request

### Development Setup

```bash
# Clone repository
git clone https://github.com/your-username/laravel-blueprint.git
cd laravel-blueprint

# Install dependencies
composer install

# Run tests
vendor/bin/phpunit
```

### Reporting Issues

When reporting issues, please include:

1. Laravel version
2. PHP version
3. Package version
4. Steps to reproduce
5. Expected vs actual behavior
6. Error messages/logs

---

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

---

## Support

- **Documentation**: This README and inline code documentation
- **Issues**: [GitHub Issues](https://github.com/twenycode/laravel-blueprint/issues)
- **Email**: twenycode@tweny.co.tz

---

## Acknowledgments

- Laravel team for the excellent framework
- HashIds for ID obfuscation
- SweetAlert for beautiful notifications
- All contributors and users of this package

---

**Made with ❤️ by [TWENY LIMITED](https://tweny.co.tz)**