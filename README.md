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
- [Architecture Overview](#architecture-overview)
- [ID Management Approaches](#id-management-approaches)
- [Quick Start Guide](#quick-start-guide)
- [Core Components](#core-components)
- [Helper Functions](#helper-functions)
- [Caching System](#caching-system)
- [SweetAlert Integration](#sweetalert-integration)
- [Blade Morph Integration](#blade-morph-integration)
- [Testing](#testing)
- [Migration Guide](#migration-guide)
- [Best Practices](#best-practices)
- [API Documentation](#api-documentation)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

## Introduction

Laravel Blueprint provides a solid foundation for building Laravel applications with a clean architecture, standardized patterns, and reusable components. It implements the repository pattern, service layer, and includes numerous utilities and base classes to streamline your development process.

### Why Laravel Blueprint?

- **Standardized Architecture**: Follow proven patterns out of the box
- **Time-Saving**: Pre-built components for common tasks
- **Flexible**: Choose between hashed IDs or UUIDs
- **Performance**: Built-in intelligent caching system
- **Maintainable**: Clear separation of concerns
- **Well-Tested**: Comprehensive error handling and logging

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
- **Activity Logging** - Integrated Spatie Activity Log for audit trails
- **Migration Helpers** - Standardized migration patterns for both ID types

### 🚀 **Performance & Caching**
- **Intelligent Caching** - Automatic cache management with model observers
- **User-Specific Caching** - Context-aware caching for multi-tenant applications
- **Cache Invalidation** - Automatic cache clearing on model changes
- **Tag-Based Cache** - Efficient cache management with Redis/Memcached

### 🎨 **UI & UX**
- **Flash Messaging** - Integrated SweetAlert for beautiful notifications
- **Error Handling** - Standardized error handling across all components
- **API Support** - JSON response handling for API controllers
- **Bootstrap Pagination** - Pre-configured pagination styling
- **Blade Morph** - Smooth UI transitions and morphing effects

### 🛠️ **Utilities & Helpers**
- **Helper Functions** - Extensive utility functions for dates, text, and numbers
- **Custom Validation Rules** - Composite key uniqueness validation
- **Text Processing** - Advanced text manipulation and formatting functions
- **File Size Formatting** - Human-readable file size conversion

## Requirements

- **PHP:** 8.2 or higher
- **Laravel:** 10.0, 11.0, or 12.0
- **Cache Driver:** Redis or Memcached (for tag-based cache invalidation)
- **Dependencies:**
    - vinkla/hashids: ^13.0
    - realrashid/sweet-alert: ^7.3
    - twenycode/blade-morph: ^1.3
    - spatie/laravel-activitylog: ^4.10
    - spatie/laravel-permission: ^6.21
    - rap2hpoutre/laravel-log-viewer: ^2.4
    - rappasoft/laravel-authentication-log: ^4.0

## Installation

### Step 1: Install the Package

Install the package via Composer:

```bash
composer require twenycode/laravel-blueprint
```

### Step 2: Publish Configuration

Publish the configuration files:

```bash
php artisan vendor:publish --provider="TwenyCode\LaravelBlueprint\TwenyLaravelBlueprintServiceProvider" --tag="tcb-config"
```

This publishes:
- `config/tweny-blueprint.php` - Main configuration settings

### Step 3: Configure Cache Driver

For optimal performance, ensure you're using Redis or Memcached:

```env
CACHE_DRIVER=redis
```

### Step 4: Install Dependencies (Optional)

If you want to use all features, install the optional dependencies:

```bash
# For activity logging
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate

# For permissions
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# For SweetAlert
php artisan vendor:publish --provider="RealRashid\SweetAlert\SweetAlertServiceProvider"

# For Blade Morph (smooth UI transitions)
php artisan vendor:publish --provider="TwenyCode\BladeMorph\BladeMorphServiceProvider"

# For Log Viewer
# Access logs at /log-viewer route

# For Authentication Logging
php artisan vendor:publish --provider="Rappasoft\LaravelAuthenticationLog\LaravelAuthenticationLogServiceProvider"
php artisan migrate
```

## Configuration

### Basic Configuration

Edit `config/tweny-blueprint.php`:

```php
return [
    // Cache Configuration
    'cache' => [
        'enabled' => env('BLUEPRINT_CACHE_ENABLED', true),
        'ttl' => env('BLUEPRINT_CACHE_TTL', 3600), // 1 hour
        'driver' => env('CACHE_DRIVER', 'redis'),
    ],

    // Model Observers
    'observers' => [
        'enabled' => env('BLUEPRINT_OBSERVERS_ENABLED', true),
        'models' => [
            App\Models\User::class,
            App\Models\Post::class,
            // Add your models here
        ],
    ],

    // Authorization
    'authorization' => [
        'enabled' => env('BLUEPRINT_AUTHORIZATION_ENABLED', true),
        'super_admin_role' => env('BLUEPRINT_SUPER_ADMIN_ROLE', 'superAdmin'),
    ],

    // Pagination
    'pagination' => [
        'per_page' => env('BLUEPRINT_PER_PAGE', 15),
        'max_per_page' => 100,
    ],

    // HashIDs Configuration
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
```

### Environment Variables

Add to your `.env` file:

```env
# Cache Configuration
BLUEPRINT_CACHE_ENABLED=true
BLUEPRINT_CACHE_TTL=3600
CACHE_DRIVER=redis

# Observers
BLUEPRINT_OBSERVERS_ENABLED=true

# Authorization
BLUEPRINT_AUTHORIZATION_ENABLED=true
BLUEPRINT_SUPER_ADMIN_ROLE=superAdmin

# Pagination
BLUEPRINT_PER_PAGE=15

# HashIDs (for integer ID obfuscation)
HASHIDS_SALT="${APP_KEY}"
```

## Architecture Overview

Laravel Blueprint follows a clean architecture pattern with clear separation of concerns:

```
┌─────────────────────────────────────────┐
│           Controllers                    │
│  (Handle HTTP & Route Logic)            │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│            Services                      │
│  (Business Logic & Transactions)         │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│          Repositories                    │
│  (Data Access & Caching)                 │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│            Models                        │
│  (Eloquent ORM & Relationships)          │
└─────────────────────────────────────────┘
```

### Layer Responsibilities

**Controllers**:
- Handle HTTP requests
- Validate input (via Form Requests)
- Call service methods
- Return views or JSON responses

**Services**:
- Implement business logic
- Manage database transactions
- Coordinate multiple repositories
- Handle complex operations

**Repositories**:
- Abstract data access
- Manage caching
- Query optimization
- CRUD operations

**Models**:
- Define database structure
- Manage relationships
- Provide accessors/mutators
- Implement model events

## ID Management Approaches

Laravel Blueprint supports two approaches for handling model IDs. Choose the one that best fits your application's needs:

### 🔢 **Option 1: Hashed Integer IDs (Traditional)**
Perfect for obfuscating sequential database IDs while maintaining integer performance.

**Advantages:**
- Smaller URL parameters
- Better database performance with integer indexes
- Easier debugging (can decode IDs)
- Lower storage requirements

**Disadvantages:**
- Sequential IDs can be guessed (even when hashed)
- Requires encoding/decoding overhead
- Limited security benefits

**When to use:**
- Working with existing integer-based systems
- Performance is critical for large datasets
- Sequential ID exposure is a minor concern
- Need smaller URL parameters

**Example:**
```
Original ID: 123
Hashed ID: xR9kL2
URL: /users/xR9kL2
```

### 🔑 **Option 2: Native Laravel UUIDs (Recommended)**
Ideal for distributed systems, enhanced security, and when you need globally unique identifiers.

**Advantages:**
- Globally unique (no collisions)
- Non-sequential (harder to guess)
- Better security for public APIs
- Works well in distributed systems
- Can be generated offline

**Disadvantages:**
- Larger URLs (36 characters)
- Slightly slower than integer indexes
- More storage space required
- Harder to read/debug

**When to use:**
- Building new applications
- Enhanced security is paramount
- Working with distributed systems or microservices
- Need offline ID generation capability
- Building public APIs where ID exposure matters
- Multi-tenant applications

**Example:**
```
UUID: 550e8400-e29b-41d4-a716-446655440000
URL: /users/550e8400-e29b-41d4-a716-446655440000
```

### Comparison Table

| Feature | Hashed Integer IDs | Native UUIDs |
|---------|-------------------|--------------|
| URL Length | Short (6-8 chars) | Long (36 chars) |
| Performance | Faster | Slightly slower |
| Security | Moderate | High |
| Guessable | Somewhat | No |
| Global Uniqueness | No | Yes |
| Storage | 4-8 bytes | 16 bytes |
| Readability | Good | Poor |
| Distribution | Centralized | Distributed |

---

## Quick Start Guide

### Using Hashed Integer IDs

#### 1. Create a Model

```php
<?php

namespace App\Models;

use TwenyCode\LaravelBlueprint\Models\BaseModel;
use TwenyCode\LaravelBlueprint\Traits\HashingIdsTrait;

class Product extends BaseModel
{
    use HashingIdsTrait;
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'is_active',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];
    
    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
```

#### 2. Create a Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'category_id']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
```

#### 3. Create a Repository

```php
<?php

namespace App\Repositories;

use App\Models\Product;
use TwenyCode\LaravelBlueprint\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
        $this->relationships = ['category', 'reviews'];
    }
    
    /**
     * Find products by category
     */
    public function findByCategory(int $categoryId)
    {
        return $this->handleError(function () use ($categoryId) {
            return $this->model
                ->with($this->relationships)
                ->where('category_id', $categoryId)
                ->where('is_active', true)
                ->get();
        }, 'find products by category');
    }
    
    /**
     * Find low stock products
     */
    public function findLowStock(int $threshold = 10)
    {
        return $this->handleError(function () use ($threshold) {
            return $this->model
                ->where('stock', '<=', $threshold)
                ->where('is_active', true)
                ->orderBy('stock', 'asc')
                ->get();
        }, 'find low stock products');
    }
    
    /**
     * Search products
     */
    public function searchProducts(string $query)
    {
        return $this->handleError(function () use ($query) {
            return $this->model
                ->with($this->relationships)
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->get();
        }, 'search products');
    }
}
```

#### 4. Create a Service

```php
<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use TwenyCode\LaravelBlueprint\Services\BaseService;
use Illuminate\Support\Facades\Log;

class ProductService extends BaseService
{
    public function __construct(ProductRepository $repository)
    {
        parent::__construct($repository);
    }
    
    /**
     * Create a new product with validation
     */
    public function createProduct(array $data)
    {
        return $this->transaction(function () use ($data) {
            // Set default values
            $data['is_active'] = $data['is_active'] ?? true;
            $data['stock'] = $data['stock'] ?? 0;
            
            // Create the product
            $product = $this->repository->create($data);
            
            // Log activity
            Log::info("Product created: {$product->name}", ['id' => $product->id]);
            
            return $product;
        });
    }
    
    /**
     * Update product stock
     */
    public function updateStock($id, int $quantity, string $operation = 'add')
    {
        return $this->transaction(function () use ($id, $quantity, $operation) {
            $product = $this->repository->find($id);
            
            $newStock = $operation === 'add' 
                ? $product->stock + $quantity 
                : $product->stock - $quantity;
            
            // Prevent negative stock
            if ($newStock < 0) {
                throw new \Exception('Insufficient stock');
            }
            
            $product->update(['stock' => $newStock]);
            
            Log::info("Stock updated for product: {$product->name}", [
                'operation' => $operation,
                'quantity' => $quantity,
                'new_stock' => $newStock
            ]);
            
            return $product;
        });
    }
    
    /**
     * Get products by category
     */
    public function getProductsByCategory(int $categoryId)
    {
        return $this->repository->findByCategory($categoryId);
    }
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $threshold = 10)
    {
        return $this->repository->findLowStock($threshold);
    }
}
```

#### 5. Create a Form Request

```php
<?php

namespace App\Http\Requests;

use TwenyCode\LaravelBlueprint\Http\Requests\AuthorizedFormRequest;

class ProductStoreRequest extends AuthorizedFormRequest
{
    public function authorize()
    {
        return $this->checkPermission('create-product');
    }
    
    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
    
    public function messages()
    {
        return [
            'category_id.required' => 'Please select a category',
            'category_id.exists' => 'Selected category is invalid',
            'name.required' => 'Product name is required',
            'price.required' => 'Price is required',
            'price.min' => 'Price cannot be negative',
        ];
    }
}
```

#### 6. Create a Controller

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Services\ProductService;
use TwenyCode\LaravelBlueprint\Http\Controllers\BaseResourceController;

class ProductController extends BaseResourceController
{
    public function __construct(ProductService $service)
    {
        $this->layer = $service;
        $this->controllerName = 'Product';
        $this->baseViewName = 'products';
        $this->baseRouteName = 'products';
        $this->resourceVariable = 'product';
        $this->hasRelationShips = true;
    }
    
    /**
     * Store a newly created product
     */
    public function store(ProductStoreRequest $request)
    {
        return $this->handleError(function () use ($request) {
            $this->layer->createProduct($request->validated());
            return $this->successRoute(
                $this->baseRouteName . '.index',
                'Product created successfully'
            );
        }, 'create product', $request->input());
    }
    
    /**
     * Update the specified product
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        return $this->handleError(function () use ($id, $request) {
            $this->layer->update($id, $request->validated());
            return $this->successRoute(
                $this->baseRouteName . '.index',
                'Product updated successfully'
            );
        }, 'update product', $request->input());
    }
    
    /**
     * Get products by category
     */
    public function byCategory($categoryId)
    {
        return $this->handleError(function () use ($categoryId) {
            $products = $this->layer->getProductsByCategory($categoryId);
            return view($this->baseViewName . '.index', [
                'products' => $products
            ]);
        }, 'get products by category');
    }
}
```

### Using Native UUIDs

The process is similar, but with a few key differences:

#### 1. Create a UUID Model

```php
<?php

namespace App\Models;

use TwenyCode\LaravelBlueprint\Models\BaseUuidModel;

class Order extends BaseUuidModel
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'is_active',
    ];
    
    protected $casts = [
        'total_amount' => 'decimal:2',
    ];
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

#### 2. Create UUID Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
```

#### 3. Create UUID Repository

```php
<?php

namespace App\Repositories;

use App\Models\Order;
use TwenyCode\LaravelBlueprint\Repositories\BaseUuidRepository;

class OrderRepository extends BaseUuidRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
        $this->relationships = ['user', 'items'];
    }
    
    /**
     * Find orders by user
     */
    public function findByUser(string $userId)
    {
        return $this->handleError(function () use ($userId) {
            return $this->model
                ->with($this->relationships)
                ->where('user_id', $userId)
                ->latest()
                ->get();
        }, 'find orders by user');
    }
}
```

---

## Core Components

### Models

Laravel Blueprint provides two base model classes with common functionality:

#### BaseModel (For Hashed Integer IDs)

```php
use TwenyCode\LaravelBlueprint\Models\BaseModel;

class YourModel extends BaseModel
{
    // Your model implementation
}
```

**Built-in Features:**
- Activity logging (via Spatie Activity Log)
- Date casting for common date fields
- Boolean casting for `is_active` and `is_boolean`
- Query scopes: `active()`, `inactive()`, `ordered()`
- Status management: `activate()`, `deactivate()`, `toggleActive()`

**Example Usage:**

```php
// Using scopes
$activeProducts = Product::active()->ordered()->get();

// Status management
$product = Product::find(1);
$product->activate();
$product->deactivate();
$product->toggleActive();
```

#### BaseUuidModel (For UUIDs)

```php
use TwenyCode\LaravelBlueprint\Models\BaseUuidModel;

class YourModel extends BaseUuidModel
{
    // Your model implementation
}
```

**Inherits all BaseModel features plus:**
- Automatic UUID generation
- UUID validation
- Non-incrementing primary key
- String-based primary key type

### Repositories

Repositories handle all data access logic and provide a consistent interface for querying models.

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

```php
// Basic retrieval
$repository->all();                          // Get all records
$repository->allWithRelations();             // Get all with relationships
$repository->active();                       // Get active records only
$repository->activeWithRelations();          // Get active with relationships
$repository->inactive();                     // Get inactive records
$repository->inactiveWithRelations();        // Get inactive with relationships

// Finding records
$repository->find($id);                      // Find by ID
$repository->findMany([1, 2, 3]);           // Find multiple IDs
$repository->search('term', ['name']);       // Search in columns

// Pagination
$repository->paginate(15);                   // Paginate results

// Creating/Updating
$repository->create($data);                  // Create new record
$repository->createMany($records);           // Bulk create
$repository->update($id, $data);            // Update record

// Deleting
$repository->delete($id);                    // Soft delete
$repository->deleteBy('column', 'value');   // Delete by criteria

// Soft deletes
$repository->trashed();                      // Get trashed records
$repository->findTrashed($id);              // Find trashed by ID
$repository->restore($id);                   // Restore trashed
$repository->forceDelete($id);              // Permanently delete

// Status management
$repository->toggleStatus($model);           // Toggle is_active

// Utilities
$repository->pluckActive('name', 'id');     // Pluck as key-value
$repository->orderBy('name', 'asc');        // Order results
$repository->query();                        // Get query builder
```

**Custom Repository Example:**

```php
<?php

namespace App\Repositories;

use App\Models\Post;
use TwenyCode\LaravelBlueprint\Repositories\BaseRepository;
use Carbon\Carbon;

class PostRepository extends BaseRepository
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
        $this->relationships = ['author', 'category', 'tags'];
    }
    
    /**
     * Find published posts
     */
    public function findPublished()
    {
        return $this->handleError(function () {
            return $this->model
                ->with($this->relationships)
                ->where('status', 'published')
                ->where('published_at', '<=', Carbon::now())
                ->latest('published_at')
                ->get();
        }, 'find published posts');
    }
    
    /**
     * Find posts by author
     */
    public function findByAuthor(int $authorId)
    {
        return $this->handleError(function () use ($authorId) {
            return $this->model
                ->with($this->relationships)
                ->where('author_id', $authorId)
                ->latest()
                ->get();
        }, 'find posts by author');
    }
    
    /**
     * Find trending posts
     */
    public function findTrending(int $days = 7, int $limit = 10)
    {
        return $this->handleError(function () use ($days, $limit) {
            return $this->model
                ->with($this->relationships)
                ->where('status', 'published')
                ->where('published_at', '>=', Carbon::now()->subDays($days))
                ->orderBy('views_count', 'desc')
                ->limit($limit)
                ->get();
        }, 'find trending posts');
    }
}
```

#### BaseUuidRepository

```php
use TwenyCode\LaravelBlueprint\Repositories\BaseUuidRepository;

class YourRepository extends BaseUuidRepository
{
    // Same as BaseRepository, but no ID encoding/decoding
}
```

### Services

Services contain your business logic and orchestrate operations across multiple repositories.

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
            // Multiple repository calls
            // Complex validations
            // External API calls
            
            return $this->repository->create($data);
        });
    }
}
```

**Available Methods:**

```php
// All repository methods are available through the service
$service->all();
$service->find($id);
$service->create($data);
$service->update($id, $data);
$service->delete($id);

// Plus additional service-specific methods
$service->activate($id);
$service->deactivate($id);
$service->toggleStatus($id);
```

**Complex Service Example:**

```php
<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use TwenyCode\LaravelBlueprint\Services\BaseService;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class OrderService extends BaseService
{
    protected ProductRepository $productRepository;
    protected UserRepository $userRepository;
    
    public function __construct(
        OrderRepository $repository,
        ProductRepository $productRepository,
        UserRepository $userRepository
    ) {
        parent::__construct($repository);
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }
    
    /**
     * Create a new order with validation and stock management
     */
    public function createOrder(array $data)
    {
        return $this->transaction(function () use ($data) {
            // Validate stock availability
            foreach ($data['items'] as $item) {
                $product = $this->productRepository->find($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
            }
            
            // Calculate total
            $total = 0;
            foreach ($data['items'] as $item) {
                $product = $this->productRepository->find($item['product_id']);
                $total += $product->price * $item['quantity'];
            }
            
            // Create order
            $order = $this->repository->create([
                'user_id' => $data['user_id'],
                'total_amount' => $total,
                'status' => 'pending',
            ]);
            
            // Create order items and update stock
            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $this->productRepository->find($item['product_id'])->price,
                ]);
                
                // Reduce stock
                $this->productRepository->update($item['product_id'], [
                    'stock' => $product->stock - $item['quantity']
                ]);
            }
            
            // Send confirmation email
            $user = $this->userRepository->find($data['user_id']);
            Mail::to($user->email)->send(new OrderConfirmation($order));
            
            return $order;
        });
    }
    
    /**
     * Cancel an order and restore stock
     */
    public function cancelOrder($orderId)
    {
        return $this->transaction(function () use ($orderId) {
            $order = $this->repository->find($orderId);
            
            if ($order->status === 'completed') {
                throw new \Exception('Cannot cancel completed orders');
            }
            
            // Restore stock
            foreach ($order->items as $item) {
                $product = $this->productRepository->find($item->product_id);
                $this->productRepository->update($item->product_id, [
                    'stock' => $product->stock + $item->quantity
                ]);
            }
            
            // Update order status
            $order->update(['status' => 'cancelled']);
            
            return $order;
        });
    }
}
```

### Controllers

#### BaseResourceController (Web)

For traditional web applications with views:

```php
use TwenyCode\LaravelBlueprint\Http\Controllers\BaseResourceController;

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

**Available Routes:**

```php
Route::resource('products', ProductController::class);
Route::get('products/trash', [ProductController::class, 'trashed'])->name('products.trash');
Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
Route::delete('products/{id}/force', [ProductController::class, 'forceDelete'])->name('products.forceDelete');
Route::post('products/{id}/toggle', [ProductController::class, 'toggleActive'])->name('products.toggle');
```

#### BaseApiResourceController (API)

For RESTful API applications:

```php
use TwenyCode\LaravelBlueprint\Http\Controllers\BaseApiResourceController;

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

**API Routes:**

```php
Route::apiResource('products', ProductApiController::class);
Route::get('products/trash', [ProductApiController::class, 'trashed']);
Route::post('products/{id}/restore', [ProductApiController::class, 'restore']);
Route::delete('products/{id}/force', [ProductApiController::class, 'forceDelete']);
Route::post('products/{id}/toggle', [ProductApiController::class, 'toggleActive']);
```

### Form Requests

```php
use TwenyCode\LaravelBlueprint\Http\Requests\AuthorizedFormRequest;

class YourRequest extends AuthorizedFormRequest
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
    
    public function messages()
    {
        return [
            'name.required' => 'The name field is required',
            'email.unique' => 'This email is already taken',
        ];
    }
}
```

---

## Helper Functions

Laravel Blueprint includes a comprehensive set of helper functions for common tasks.

### Date Helpers

```php
use TwenyCode\LaravelBlueprint\Helpers\DateHelper;

// Format date ranges
$range = formatDateDuration('2025-01-15', '2025-02-20');
// Output: "15 Jan - 20 Feb, 2025"

// Calculate days between dates
$days = DateHelper::numberOfDays('2025-01-01', '2025-01-15');
// Output: 14

// Calculate age in days
$age = DateHelper::calculateAge('1990-01-01');
// Output: 12784 (as of 2025)

// Human-readable time ago
$timeAgo = DateHelper::formatTimeAgo('2025-01-01 12:00:00');
// Output: "26 days ago"

// Date difference
$diff = DateHelper::dateDifference('2025-01-01', '2025-03-01');
// Output: "2 months"

// Remaining days
$remaining = DateHelper::calculateRemainingDays('2025-12-31');
// Output: 338
```

### Number Helpers

```php
use TwenyCode\LaravelBlueprint\Helpers\NumberHelper;

// Format file sizes
$size = formatFileSize(1024 * 1024);
// Output: "1.00 MB"

$size = formatFileSize(1536 * 1024);
// Output: "1.50 MB"

// Format currency with decimals
$formatted = formatCurrencyDecimal(1234.56);
// Output: "1,234.56"

// Format currency without decimals
$formatted = formatCurrency(1234.56);
// Output: "1,235"

// Format money with symbol
$formatted = formatMoney(1234.56);
// Output: "$ 1,234.56"

// Calculate percentage
$result = NumberHelper::calculatePercentNumber(15, 200);
// Output: 30 (15% of 200)
```

### Text Helpers

```php
use TwenyCode\LaravelBlueprint\Helpers\TextHelper;

// String manipulation
$text = TextHelper::removeUnderscore('hello_world');
// Output: "hello world"

$text = TextHelper::addUnderscore('hello world');
// Output: "hello_world"

$text = TextHelper::snake('HelloWorld');
// Output: "hello_world"

$text = TextHelper::headline('user_profile_settings');
// Output: "User Profile Settings"

// Pluralization
$plural = TextHelper::pluralize('category');
// Output: "categories"

$plural = TextHelper::pluralizeVariableName('userProfile');
// Output: "userProfiles"

// Trim words
$trimmed = trimWords('This is a long text that needs trimming', 5);
// Output: "This is a long text..."

// Trim HTML while preserving structure
$trimmed = trimHtmlWords('<p>This is <strong>bold</strong> text</p>', 3);
// Output: "<p>This is <strong>bold</strong>...</p>"
```

---

## Caching System

Laravel Blueprint includes a sophisticated caching system with automatic invalidation.

### Automatic Caching

All repository methods automatically cache their results:

```php
// First call - queries database
$users = $userRepository->all();

// Second call - returns from cache
$users = $userRepository->all();
```

### Cache Configuration

Configure caching in `config/tweny-blueprint.php`:

```php
'cache' => [
    'enabled' => true,
    'ttl' => 3600, // Cache for 1 hour
    'driver' => 'redis', // Must support tags
],
```

### Model Observers

Register models for automatic cache invalidation:

```php
'observers' => [
    'enabled' => true,
    'models' => [
        App\Models\User::class,
        App\Models\Product::class,
        App\Models\Post::class,
    ],
],
```

### Manual Cache Management

```php
// Set custom cache duration
$products = $productRepository
    ->setCacheDuration(7200) // 2 hours
    ->all();

// Clear specific cache
$productRepository->clearCache();

// Clear user-specific cache
$productRepository->clearUserCache($userId);

// Clear all users' cache
$productRepository->clearAllUsersCache();
```

### User-Specific Caching

For multi-tenant applications:

```php
class OrderRepository extends BaseRepository
{
    // Define models that need user-specific caching
    protected array $userSpecificModels = ['Order', 'Invoice'];
    
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}

// Cache key will be: user_123:order:all
$orders = $orderRepository->all();
```

### Event Caching

For specialized caching needs:

```php
use TwenyCode\LaravelBlueprint\Traits\CachingTrait;

class EventRepository extends BaseRepository
{
    use CachingTrait;
    
    public function getUpcomingEvents()
    {
        return $this->rememberEventCache('upcoming', function() {
            return $this->model
                ->where('start_date', '>', now())
                ->where('is_active', true)
                ->orderBy('start_date')
                ->get();
        });
    }
    
    public function getFeaturedEvents()
    {
        return $this->rememberEventCache('featured', function() {
            return $this->model
                ->where('is_featured', true)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }, 1800); // Cache for 30 minutes
    }
}
```

### Cache Best Practices

1. **Use Redis or Memcached**: These support tag-based cache invalidation
2. **Set appropriate TTL**: Balance between freshness and performance
3. **Monitor cache hit rate**: Use `php artisan cache:clear` if needed
4. **Cache expensive queries**: Focus on complex joins and aggregations
5. **Clear cache on deployment**: Ensure fresh data after code changes

---

## SweetAlert Integration

Beautiful, customizable alert messages powered by SweetAlert.

### Setup

1. Install SweetAlert:
```bash
composer require realrashid/sweet-alert
```

2. Publish assets:
```bash
php artisan vendor:publish --provider="RealRashid\SweetAlert\SweetAlertServiceProvider"
```

3. Add to your layout:
```html
<!DOCTYPE html>
<html>
<head>
    <title>Your App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <!-- Your content -->
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    @include('sweetalert::alert')
</body>
</html>
```

### Usage in Controllers

```php
// Success messages
$this->successMsg('User created successfully');
return $this->success('Changes saved');
return $this->successRoute('users.index', 'User updated');

// Error messages
$this->errorMsg('Something went wrong');
return $this->error('Unable to delete user');
return $this->errorRoute('users.index', 'Operation failed');

// Form errors with input
return $this->formInputError($request, 'Please check your input');
```

### Custom Alerts

```php
// In your blade views
@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            timerProgressBar: true,
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
        });
    </script>
@endif
```

### Confirmation Dialogs

```php
// In your blade views
<button onclick="confirmDelete({{ $user->id }})">Delete</button>

<script>
function confirmDelete(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + userId).submit();
        }
    });
}
</script>
```

---

## Blade Morph Integration

Blade Morph provides smooth UI transitions and morphing effects for your Laravel applications, creating a more dynamic and polished user experience.

### What is Blade Morph?

Blade Morph is a Laravel package that enables smooth transitions between different UI states without full page reloads. It's perfect for creating modern, app-like experiences in your web applications.

### Features

- **Smooth Transitions**: Morph between different views seamlessly
- **Dynamic Content**: Update page sections without full reload
- **Easy Integration**: Simple Blade directives
- **Lightweight**: Minimal overhead
- **Customizable**: Configure transition effects

### Basic Usage

#### In Your Blade Views

```blade
{{-- Wrap morphable content --}}
<div x-morph>
    @if($showDetails)
        <div class="details-panel">
            <h2>{{ $product->name }}</h2>
            <p>{{ $product->description }}</p>
        </div>
    @else
        <div class="summary-panel">
            <h3>{{ $product->name }}</h3>
        </div>
    @endif
</div>
```

#### With Alpine.js

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle Details</button>
    
    <div x-morph>
        <div x-show="open" x-transition>
            <div class="expanded-content">
                <!-- Your content -->
            </div>
        </div>
    </div>
</div>
```

### Advanced Examples

#### Dynamic Product Cards

```blade
<div class="product-grid">
    @foreach($products as $product)
        <div x-data="{ expanded: false }" class="product-card">
            <div x-morph>
                <div x-show="!expanded" class="product-summary">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                    <h3>{{ $product->name }}</h3>
                    <p class="price">{{ formatMoney($product->price) }}</p>
                    <button @click="expanded = true">View Details</button>
                </div>
                
                <div x-show="expanded" class="product-details">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                    <h2>{{ $product->name }}</h2>
                    <p class="description">{{ $product->description }}</p>
                    <p class="price">{{ formatMoney($product->price) }}</p>
                    <div class="stock">Stock: {{ $product->stock }}</div>
                    <button @click="expanded = false">Close</button>
                    <button class="btn-primary">Add to Cart</button>
                </div>
            </div>
        </div>
    @endforeach
</div>
```

#### Collapsible Sections

```blade
<div x-data="{ activeTab: 'description' }">
    <div class="tabs">
        <button @click="activeTab = 'description'" 
                :class="{ 'active': activeTab === 'description' }">
            Description
        </button>
        <button @click="activeTab = 'reviews'" 
                :class="{ 'active': activeTab === 'reviews' }">
            Reviews
        </button>
        <button @click="activeTab = 'specs'" 
                :class="{ 'active': activeTab === 'specs' }">
            Specifications
        </button>
    </div>
    
    <div x-morph class="tab-content">
        <div x-show="activeTab === 'description'">
            <h3>Product Description</h3>
            <p>{{ $product->description }}</p>
        </div>
        
        <div x-show="activeTab === 'reviews'">
            <h3>Customer Reviews</h3>
            @foreach($product->reviews as $review)
                <div class="review">
                    <p>{{ $review->comment }}</p>
                    <span>- {{ $review->user->name }}</span>
                </div>
            @endforeach
        </div>
        
        <div x-show="activeTab === 'specs'">
            <h3>Specifications</h3>
            <dl>
                @foreach($product->specifications as $key => $value)
                    <dt>{{ $key }}</dt>
                    <dd>{{ $value }}</dd>
                @endforeach
            </dl>
        </div>
    </div>
</div>
```

#### Modal Transitions

```blade
<div x-data="{ showModal: false }">
    <button @click="showModal = true">Open Modal</button>
    
    <div x-show="showModal" 
         x-morph
         class="modal-overlay"
         @click.self="showModal = false">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ $title }}</h2>
                <button @click="showModal = false">&times;</button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button @click="showModal = false">Cancel</button>
                <button class="btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>
```

### Configuration

Customize morph behavior in your configuration:

```php
// config/blade-morph.php
return [
    'duration' => 300, // Transition duration in milliseconds
    'easing' => 'ease-in-out', // CSS easing function
    'debug' => false, // Enable debug mode
];
```

### Best Practices

1. **Keep Transitions Short**: 200-400ms is ideal for most transitions
2. **Use Sparingly**: Not every element needs to morph
3. **Test Performance**: Monitor performance on slower devices
4. **Provide Fallbacks**: Ensure functionality without JavaScript
5. **Semantic HTML**: Maintain proper HTML structure during transitions

### Combining with SweetAlert

Create seamless user experiences by combining Blade Morph with SweetAlert:

```blade
<div x-data="{ 
    deleting: false,
    async confirmDelete() {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone',
            icon: 'warning',
            showCancelButton: true,
        });
        
        if (result.isConfirmed) {
            this.deleting = true;
            // Submit form or make AJAX request
            document.getElementById('delete-form').submit();
        }
    }
}">
    <div x-morph>
        <div x-show="!deleting">
            <h3>{{ $product->name }}</h3>
            <button @click="confirmDelete()">Delete Product</button>
        </div>
        
        <div x-show="deleting">
            <div class="spinner">Deleting...</div>
        </div>
    </div>
</div>
```

### Resources

- **Package Repository**: [https://github.com/twenycode/blade-morph](https://github.com/twenycode/blade-morph)
- **Alpine.js Documentation**: [https://alpinejs.dev](https://alpinejs.dev)

---

## Testing

### Setting Up Tests

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    protected ProductRepository $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository(new Product());
    }
    
    public function test_can_create_product()
    {
        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ];
        
        $product = $this->repository->create($data);
        
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }
    
    public function test_can_find_active_products()
    {
        Product::factory()->count(3)->create(['is_active' => true]);
        Product::factory()->count(2)->create(['is_active' => false]);
        
        $active = $this->repository->active();
        
        $this->assertCount(3, $active);
    }
    
    public function test_can_search_products()
    {
        Product::factory()->create(['name' => 'Blue Widget']);
        Product::factory()->create(['name' => 'Red Widget']);
        Product::factory()->create(['name' => 'Green Gadget']);
        
        $results = $this->repository->search('Widget', ['name']);
        
        $this->assertCount(2, $results);
    }
}
```

### Service Testing

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_order_with_stock_validation()
    {
        $product = Product::factory()->create(['stock' => 10]);
        
        $orderData = [
            'user_id' => 1,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5]
            ]
        ];
        
        $order = $this->orderService->createOrder($orderData);
        
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(5, $order->items->first()->quantity);
        
        $product->refresh();
        $this->assertEquals(5, $product->stock);
    }
    
    public function test_cannot_create_order_with_insufficient_stock()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock');
        
        $product = Product::factory()->create(['stock' => 2]);
        
        $orderData = [
            'user_id' => 1,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5]
            ]
        ];
        
        $this->orderService->createOrder($orderData);
    }
}
```

### UUID Testing

```php
public function test_can_create_model_with_uuid()
{
    $data = [
        'name' => 'Test Order',
        'user_id' => Str::uuid(),
        'total_amount' => 99.99,
    ];
    
    $order = $this->orderService->create($data);
    
    $this->assertInstanceOf(Order::class, $order);
    $this->assertTrue(Str::isUuid($order->id));
}
```

---

## Migration Guide

### From Integer IDs to UUIDs

```php
// Step 1: Backup your database

// Step 2: Add UUID column to existing table
Schema::table('users', function (Blueprint $table) {
    $table->uuid('uuid')->nullable()->after('id');
    $table->index('uuid');
});

// Step 3: Generate UUIDs for existing records
User::whereNull('uuid')->chunk(100, function ($users) {
    foreach ($users as $user) {
        $user->update(['uuid' => Str::uuid()]);
    }
});

// Step 4: Update your model
// Change: BaseModel -> BaseUuidModel

// Step 5: Update your repository
// Change: BaseRepository -> BaseUuidRepository

// Step 6: Update foreign keys (create new migration)
Schema::table('posts', function (Blueprint $table) {
    $table->uuid('user_uuid')->nullable()->after('user_id');
});

// Populate foreign UUIDs
Post::chunk(100, function ($posts) {
    foreach ($posts as $post) {
        $user = User::find($post->user_id);
        if ($user) {
            $post->update(['user_uuid' => $user->uuid]);
        }
    }
});

// Step 7: Drop old columns and rename (final migration)
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn('id');
    $table->renameColumn('uuid', 'id');
    $table->primary('id');
});

Schema::table('posts', function (Blueprint $table) {
    $table->dropForeign(['user_id']);
    $table->dropColumn('user_id');
    $table->renameColumn('user_uuid', 'user_id');
    $table->foreign('user_id')->references('id')->on('users');
});
```

---

## Best Practices

### Architecture

1. **Keep Controllers Thin**: Move business logic to services
2. **Use Services for Transactions**: Wrap complex operations in service methods
3. **Repository for Data Access**: Keep all database queries in repositories
4. **Single Responsibility**: Each class should have one primary purpose

```php
// ❌ Bad: Business logic in controller
public function store(Request $request)
{
    $user = User::create($request->all());
    $user->assignRole('user');
    Mail::to($user)->send(new Welcome($user));
    return redirect()->route('users.index');
}

// ✅ Good: Business logic in service
public function store(UserStoreRequest $request)
{
    $this->userService->registerUser($request->validated());
    return $this->successRoute('users.index', 'User registered');
}
```

### Security

1. **Use Form Requests**: Always validate input
2. **Check Permissions**: Use authorization in controllers
3. **Sanitize Input**: Leverage Laravel's validation
4. **Use UUIDs for Public APIs**: Better than exposing sequential IDs

```php
// Form Request with authorization
public function authorize()
{
    return $this->checkPermission('create-post');
}

public function rules()
{
    return [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category_id' => 'required|exists:categories,id',
    ];
}
```

### Performance

1. **Eager Load Relationships**: Avoid N+1 queries
2. **Use Caching**: Enable caching for frequently accessed data
3. **Index Database Columns**: Add indexes for searchable columns
4. **Paginate Large Results**: Don't load thousands of records

```php
// ❌ Bad: N+1 query problem
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->author->name;
}

// ✅ Good: Eager loading
$posts = Post::with('author')->get();
foreach ($posts as $post) {
    echo $post->author->name;
}
```

### Code Organization

1. **Consistent Naming**: Use descriptive, consistent names
2. **Document Complex Logic**: Add PHPDoc comments
3. **Group Related Files**: Organize by feature/domain
4. **Follow PSR Standards**: Use PSR-12 coding standards

```php
/**
 * Calculate and apply discount to order
 * 
 * @param Order $order
 * @param string $couponCode
 * @return Order
 * @throws InvalidCouponException
 */
public function applyDiscount(Order $order, string $couponCode): Order
{
    // Implementation
}
```

---

## API Documentation

### Response Formats

#### Success Response

```json
{
    "success": true,
    "message": "Product created successfully",
    "data": {
        "product": {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "name": "Awesome Product",
            "price": 99.99,
            "stock": 50,
            "is_active": true,
            "created_at": "2025-01-15T10:30:00.000000Z"
        }
    }
}
```

#### Error Response

```json
{
    "success": false,
    "message": "Product not found"
}
```

#### Validation Error Response

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "The name field is required."
        ],
        "price": [
            "The price must be a number.",
            "The price must be at least 0."
        ]
    }
}
```

### Status Codes

- `200 OK` - Successful GET, PUT, PATCH
- `201 Created` - Successful POST
- `204 No Content` - Successful DELETE
- `400 Bad Request` - Invalid request
- `401 Unauthorized` - Not authenticated
- `403 Forbidden` - Not authorized
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

### API Endpoints

```
GET    /api/products              - List all products
POST   /api/products              - Create product
GET    /api/products/{id}         - Show product
PUT    /api/products/{id}         - Update product
DELETE /api/products/{id}         - Delete product
GET    /api/products/trash        - List trashed
POST   /api/products/{id}/restore - Restore product
DELETE /api/products/{id}/force   - Force delete
POST   /api/products/{id}/toggle  - Toggle status
```

---

## Troubleshooting

### Common Issues

#### Cache Not Clearing

**Problem**: Changes not reflected after updates

**Solution**:
```bash
# Clear all cache
php artisan cache:clear

# Verify cache driver supports tags
# In .env, use redis or memcached
CACHE_DRIVER=redis
```

**Check Configuration**:
```php
// config/tweny-blueprint.php
'cache' => [
    'enabled' => true,
    'driver' => 'redis', // Must support tags
],
```

#### Hash IDs Not Working

**Problem**: Routes not resolving correctly

**Solution**:
```env
# Ensure HASHIDS_SALT is set in .env
HASHIDS_SALT="${APP_KEY}"
```

**Verify Model**:
```php
use TwenyCode\LaravelBlueprint\Traits\HashingIdsTrait;

class Product extends BaseModel
{
    use HashingIdsTrait;
}
```

#### UUID Validation Failing

**Problem**: "Invalid UUID" errors

**Solution**:
```php
// In your validation rules
'user_id' => 'required|uuid|exists:users,id',

// Verify migration
$table->uuid('id')->primary();
```

#### Permission Errors

**Problem**: "Unauthorized action" errors

**Solution**:
```php
// Verify user has permission
$user->givePermissionTo('create-product');

// Or disable authorization temporarily
protected bool $checkAuthorization = false;
```

#### SweetAlert Not Displaying

**Problem**: Flash messages not showing

**Solution**:
```html
<!-- Verify you have these in your layout -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@include('sweetalert::alert')
```

### Debug Mode

Enable detailed logging:

```env
LOG_LEVEL=debug
APP_DEBUG=true
```

Monitor logs:
```bash
tail -f storage/logs/laravel.log
```

### Performance Issues

Monitor cache operations:
```php
use Illuminate\Support\Facades\Log;

Log::info('Cache key accessed', ['key' => $cacheKey]);
```

Monitor database queries:
```php
DB::enableQueryLog();
// ... your operations
$queries = DB::getQueryLog();
Log::info('Queries executed', ['count' => count($queries), 'queries' => $queries]);
```

---

## Contributing

Contributions are welcome! Please follow these guidelines:

### Development Setup

```bash
# Fork and clone the repository
git clone https://github.com/your-username/laravel-blueprint.git
cd laravel-blueprint

# Install dependencies
composer install

# Run tests
vendor/bin/phpunit
```

### Contribution Process

1. **Fork the Repository**: Click "Fork" on GitHub
2. **Create a Branch**: `git checkout -b feature/amazing-feature`
3. **Make Changes**: Follow coding standards
4. **Add Tests**: Write tests for new functionality
5. **Run Tests**: Ensure all tests pass
6. **Commit Changes**: `git commit -m 'Add amazing feature'`
7. **Push to Branch**: `git push origin feature/amazing-feature`
8. **Create Pull Request**: Submit PR with clear description

### Coding Standards

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add PHPDoc comments for public methods
- Include tests for new features
- Update documentation as needed

### Reporting Issues

When reporting issues, please include:

1. **Laravel Version**: e.g., 11.0
2. **PHP Version**: e.g., 8.3
3. **Package Version**: e.g., 1.0.0
4. **Steps to Reproduce**: Clear, numbered steps
5. **Expected Behavior**: What should happen
6. **Actual Behavior**: What actually happens
7. **Error Messages**: Complete error messages/stack traces

**Example**:
```markdown
### Bug Report

**Laravel Version**: 11.0
**PHP Version**: 8.3
**Package Version**: 1.0.0

**Steps to Reproduce**:
1. Create a new Product
2. Try to update the product
3. Observe error

**Expected**: Product should update successfully
**Actual**: Error "Product not found"

**Error Message**:
```
ModelNotFoundException: No query results for model [Product]
at vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php:456
```
```

---

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

### MIT License Summary

- ✅ Commercial use
- ✅ Modification
- ✅ Distribution
- ✅ Private use
- ❌ Liability
- ❌ Warranty

---

## Support

### Documentation
- **README**: This comprehensive guide
- **Code Documentation**: Inline PHPDoc comments
- **Examples**: See Quick Start Guide section

### Community
- **GitHub Issues**: [Report bugs and request features](https://github.com/twenycode/laravel-blueprint/issues)
- **GitHub Discussions**: [Ask questions and share ideas](https://github.com/twenycode/laravel-blueprint/discussions)

### Professional Support
- **Email**: twenycode@tweny.co.tz
- **Website**: [https://tweny.co.tz](https://tweny.co.tz)

### Related Packages
- **Laravel**: [https://laravel.com](https://laravel.com)
- **Spatie Packages**: [https://spatie.be/open-source](https://spatie.be/open-source)
- **HashIds**: [https://hashids.org](https://hashids.org)
- **SweetAlert**: [https://sweetalert2.github.io](https://sweetalert2.github.io)
- **Blade Morph**: [https://github.com/twenycode/blade-morph](https://github.com/twenycode/blade-morph)
- **Alpine.js**: [https://alpinejs.dev](https://alpinejs.dev)

---

## Acknowledgments

Laravel Blueprint wouldn't be possible without:

- **Laravel Team** - For the excellent framework
- **Spatie** - For their amazing open-source packages
- **HashIds** - For ID obfuscation
- **SweetAlert** - For beautiful notifications
- **Blade Morph** - For smooth UI transitions
- **Laravel Log Viewer** - For easy log management
- **Laravel Authentication Log** - For security audit trails
- **All Contributors** - Who help improve this package

---

## Changelog

### Version 1.0.0 (2025-01-15)
- Initial release
- Repository pattern implementation
- Service layer
- Base controllers (Web & API)
- UUID and Hashed ID support
- Caching system with automatic invalidation
- Helper functions (Date, Number, Text)
- SweetAlert integration for notifications
- Blade Morph integration for smooth UI transitions
- Activity logging via Spatie Activity Log
- Permission-based authorization via Spatie Permission
- Laravel Log Viewer integration
- Authentication logging for security audits

---

**Made with ❤️ by [TWENY LIMITED](https://tweny.co.tz)**

For more information, visit our [GitHub repository](https://github.com/twenycode/laravel-blueprint).