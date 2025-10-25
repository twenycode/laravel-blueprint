<?php

namespace TwenyCode\LaravelBlueprint\Repositories;

use Illuminate\Database\Eloquent\Model;
use TwenyCode\LaravelBlueprint\Traits\ErrorHandlingTrait;
use TwenyCode\LaravelBlueprint\Traits\RepositoryCacheTrait;
use Illuminate\Support\Str;

/**
 * Base Repository Class
 *
 * Provides standardized CRUD operations with caching support.
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    use ErrorHandlingTrait, RepositoryCacheTrait;

    protected Model $model;
    protected string $modelName;
    protected string $cacheKeyPrefix;
    protected array $relationships = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->modelName = class_basename($model);
        $this->cacheKeyPrefix = Str::snake($this->modelName);
    }

    /**
     * Get the model instance
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Decode a hashed/encoded ID
     */
    public function decodeId($id)
    {
        return $this->handleError(function () use ($id) {
            if (method_exists($this->model, 'decodeId')) {
                return $this->model->decodeId($id);
            }

            return $id;
        }, 'decode ID');
    }

    /**
     * Encode an ID to hash/obfuscate it
     */
    public function encodeId($id)
    {
        return $this->handleError(function () use ($id) {
            if (method_exists($this->model, 'encodeId')) {
                return $this->model->encodeId($id);
            }

            return $id;
        }, 'encode ID');
    }

    /**
     * Get all records
     */
    public function all()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('all');

            return $this->remember($cacheKey, function () {
                return $this->model->get();
            });
        }, 'retrieve all records');
    }

    /**
     * Get all records with relationships
     */
    public function allWithRelations()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('all_with_relations');

            return $this->remember($cacheKey, function () {
                return $this->model->with($this->relationships)->get();
            });
        }, 'retrieve all records with relationships');
    }

    /**
     * Get active records
     */
    public function active()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('active');

            return $this->remember($cacheKey, function () {
                return $this->model->where('is_active', true)->get();
            });
        }, 'retrieve active records');
    }

    /**
     * Get active records with relationships
     */
    public function activeWithRelations()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('active_with_relations');

            return $this->remember($cacheKey, function () {
                return $this->model
                    ->where('is_active', true)
                    ->with($this->relationships)
                    ->get();
            });
        }, 'retrieve active records with relationships');
    }

    /**
     * Get inactive records
     */
    public function inactive()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('inactive');

            return $this->remember($cacheKey, function () {
                return $this->model->where('is_active', false)->get();
            });
        }, 'retrieve inactive records');
    }

    /**
     * Get inactive records with relationships
     */
    public function inactiveWithRelations()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('inactive_with_relations');

            return $this->remember($cacheKey, function () {
                return $this->model
                    ->where('is_active', false)
                    ->with($this->relationships)
                    ->get();
            });
        }, 'retrieve inactive records with relationships');
    }

    /**
     * Pluck active records as key-value pairs
     */
    public function pluckActive(string $value = 'name', string $key = 'id')
    {
        return $this->handleError(function () use ($value, $key) {
            $cacheKey = $this->generateCacheKey('pluck_active', $value, $key);

            return $this->remember($cacheKey, function () use ($value, $key) {
                return $this->model
                    ->where('is_active', true)
                    ->pluck($value, $key);
            });
        }, 'pluck active records');
    }

    /**
     * Find a record by ID
     */
    public function find($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model
                ->with($this->relationships)
                ->findOrFail($this->decodeId($id));
        }, 'find record by ID');
    }

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15)
    {
        return $this->handleError(function () use ($perPage) {
            $maxPerPage = config('tweny-blueprint.pagination.max_per_page', 100);
            $perPage = min($perPage, $maxPerPage);

            return $this->model
                ->with($this->relationships)
                ->latest()
                ->paginate($perPage);
        }, 'paginate records');
    }

    /**
     * Create a new record
     */
    public function create(array $data)
    {
        return $this->handleError(function () use ($data) {
            return $this->model->create($data);
        }, 'create record');
    }

    /**
     * Update a record
     */
    public function update($id, array $data)
    {
        return $this->handleError(function () use ($id, $data) {
            $model = $this->find($id);
            $model->update($data);

            return $model->fresh($this->relationships);
        }, 'update record');
    }

    /**
     * Delete a record
     */
    public function delete($id)
    {
        return $this->handleError(function () use ($id) {
            $model = $this->find($id);

            return $model->delete();
        }, 'delete record');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Model $model)
    {
        return $this->handleError(function () use ($model) {
            $newStatus = !$model->is_active;
            $model->update(['is_active' => $newStatus]);

            return $newStatus;
        }, 'toggle active status');
    }

    /**
     * Get trashed records
     */
    public function trashed()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('trashed');

            return $this->remember($cacheKey, function () {
                return $this->model->onlyTrashed()->get();
            });
        }, 'retrieve trashed records');
    }

    /**
     * Find a trashed record by ID
     */
    public function findTrashed($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model
                ->onlyTrashed()
                ->findOrFail($this->decodeId($id));
        }, 'find trashed record by ID');
    }

    /**
     * Restore a trashed record
     */
    public function restore($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model
                ->onlyTrashed()
                ->findOrFail($this->decodeId($id))
                ->restore();
        }, 'restore record');
    }

    /**
     * Permanently delete a record
     */
    public function forceDelete($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model
                ->onlyTrashed()
                ->findOrFail($this->decodeId($id))
                ->forceDelete();
        }, 'force delete record');
    }

    /**
     * Delete records by column value
     */
    public function deleteBy(string $column, $value)
    {
        return $this->handleError(function () use ($column, $value) {
            return $this->model
                ->where($column, $value)
                ->delete();
        }, 'delete records by criteria');
    }

    /**
     * Order records by column
     */
    public function orderBy(string $column, string $direction = 'asc')
    {
        return $this->handleError(function () use ($column, $direction) {
            return $this->model
                ->orderBy($column, $direction)
                ->get();
        }, 'retrieve ordered records');
    }

}