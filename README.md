# Laravel Blueprint

A comprehensive architecture and utilities package for Laravel applications that provides a standardized structure, core components, and common patterns to accelerate development.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/twenycode/laravel-blueprint.svg?style=flat-square)](https://packagist.org/packages/twenycode/laravel-blueprint)
[![Total Downloads](https://img.shields.io/packagist/dt/twenycode/laravel-blueprint.svg?style=flat-square)](https://packagist.org/packages/twenycode/laravel-blueprint)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Introduction

Laravel Blueprint provides a solid foundation for building Laravel applications with a clean architecture, standardized patterns, and reusable components. It implements the repository pattern, service layer, and includes numerous utilities and base classes to streamline your development process.

## Features

- **Repository Pattern** - Standardized data access layer with built-in caching support
- **Service Layer** - Business logic abstraction with database transaction management
- **Resource Controllers** - Base controllers with CRUD operations, error handling, and flash messaging
- **Model Enhancements** - Feature-rich base model with common attributes and methods
- **Caching System** - Automatic cache management with model observers
- **Helper Functions** - Extensive utility functions for dates, text, and numbers
- **Error Handling** - Standardized error handling traits across application components
- **ID Obfuscation** - HashIds implementation for obscuring database IDs in URLs
- **Form Request Validation** - Permission-based request validation
- **Soft Delete Support** - Ready-to-use methods for handling soft deletes
- **Flash Messaging** - Integrated SweetAlert for beautiful flash messages and notifications

## Requirements

- PHP 8.0+
- Laravel 8.0+

## Installation

You can install this package via Composer:

```bash
composer require twenycode/laravel-blueprint
```

## Configuration

Publish the configuration files:

```bash
php artisan vendor:publish --provider="TwenyCode\LaravelBlueprint\CoreServiceProvider" --tag="tcb-config"
```

This will publish the following configuration files:
- `config/tweny-blueprint.php` - Main configuration settings
- `config/tweny-hashids.php` - HashIds configuration

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

## Usage

### Repositories

Extend the base repository to create repository classes for your models:

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

### Services

Create service classes by extending the base service:

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
    
    // Add custom business logic
    public function registerUser(array $data)
    {
        return $this->transaction(function () use ($data) {
            // Process data and create user
            $data['password'] = Hash::make($data['password']);
            return $this->repository->create($data);
        });
    }
}
```

### Controllers

Create resource controllers by extending the base resource controller:

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
        return $this->processStore($request);
    }
    
    public function update(UserUpdateRequest $request, $id)
    {
        return $this->processUpdate($request, $id);
    }
}
```

### Models

Create models by extending the base model:

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

### Form Requests

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

## Helper Functions

The package provides a wide range of helper functions:

### Date Helpers

```php
// Convert date formats
$formattedDate = dateTimeConversion('2023-01-01', 'd M Y');  // "01 Jan 2023"

// Calculate days between dates
$daysBetween = numberOfDays('2023-01-01', '2023-01-15');  // 14

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
$money = formatMoney(1234.56);  // "$ 1,234.56"

// Calculate percentages
$value = calculatePercentNumber(15, 200);  // 30
```

### Text Helpers

```php
// Manipulate strings
$snakeCase = snake('HelloWorld');  // "hello_world"
$headlined = headline('user_profile_settings');  // "User Profile Settings"

// Work with plurals
$plural = pluralize('category');  // "categories"
$variableName = pluralizeVariableName('userProfile');  // "userProfiles"

// Trim text
$trimmed = trimWords('This is a long text that needs trimming', 5);  // "This is a long text..."
```

## Customization

### Cache Configuration

Configure caching behavior in `config/tweny-blueprint.php`:

```php
// Enable/disable model cache observers
'enable_cache_observers' => true,

// Common cache keys
'cache_keys' => [
    'all', 'active', 'inactive', 'with_relations', 'trashed', 'paginated'
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
    // Add your model classes here
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

## Extending The Package

### Custom Repositories

Add specialized queries and methods to your repository classes:

```php
public function findActiveByEmail($email)
{
    return $this->handleError(function () use ($email) {
        return $this->model
            ->where('email', $email)
            ->where('isActive', true)
            ->first();
    }, 'find active user by email');
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
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).