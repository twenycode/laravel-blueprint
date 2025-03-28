# Laravel Core Package

A comprehensive set of core components for Laravel applications that provides a standardized architecture and common utilities to accelerate development.

## Features

- **Repository Pattern Implementation** - Clean data access layer implementation with caching
- **Service Layer** - Business logic abstraction with transaction management
- **Base Controllers** - Ready-to-use resource controllers with common CRUD operations
- **Helper Classes** - Well-structured helper methods for dates, text, and numbers
- **Trait-based Functionality** - Reusable traits for error handling, ID hashing, and more
- **Cache Management** - Automatic cache clearing with model observers
- **Base Models** - Feature-rich base model with common attributes, accessors, and relationships
- **Form Requests** - Permission-based form request validation

## Installation

You can install this package via Composer:

```bash
composer require yourcompany/laravel-core
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="YourCompany\LaravelCore\CoreServiceProvider" --tag="config"
```

## Usage

### Repositories

Extend the base repository to create your own repositories:

```php
<?php

namespace App\Repositories;

use App\Models\User;
use YourCompany\LaravelCore\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
        
        // Define default relationships to load
        $this->relationships = ['roles', 'permissions'];
    }
    
    // Add custom repository methods here
}
```

### Services

Create service classes by extending the base service:

```php
<?php

namespace App\Services;

use App\Repositories\UserRepository;
use YourCompany\LaravelCore\Services\BaseService;

class UserService extends BaseService
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }
    
    // Add custom service methods here
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
use YourCompany\LaravelCore\Controllers\BaseResourceController;

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

use YourCompany\LaravelCore\Models\BaseModel;

class User extends BaseModel
{
    protected $fillable = [
        'name', 'email', 'password',
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    // Add custom model methods, relationships, etc.
}
```

### Helper Methods

The package provides a wide range of helper methods that can be used in your application:

```php
// Date helpers
$formattedDate = dateTimeConversion('2023-01-01', 'd M Y');
$daysBetween = numberOfDays('2023-01-01', '2023-01-15');
$timeAgo = formatTimeAgo('2023-01-01 12:00:00');

// Number helpers
$fileSize = formatFileSize(1024 * 1024); // "1.00 MB"
$money = formatMoney(1234.56); // "$1,234.56"

// Text helpers
$pluralized = pluralize('category'); // "categories"
$snakeCase = snake('HelloWorld'); // "hello_world"
$trimmed = trimWords('This is a long text that needs trimming', 5); // "This is a long text..."
```

## Extending

You can extend and customize any part of this package to meet your specific requirements.

### Custom Repositories

You can implement your own repository methods for specialized queries:

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
```

### Custom Services

Implement complex business logic in your service classes:

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

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).