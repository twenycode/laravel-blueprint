<?php

namespace TwenyCode\LaravelBlueprint\Repositories;

use TwenyCode\LaravelBlueprint\Traits\ErrorHandlerTrait;
use TwenyCode\LaravelBlueprint\Traits\RepositoryCacheTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

/**
 * Base Repository Class
 *
 * A standardized implementation of the repository pattern that provides:
 * - Dynamic relationship loading
 * - Advanced filtering and searching capabilities
 * - Caching with user-specific contexts
 * - Error handling and logging
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    use ErrorHandlerTrait, RepositoryCacheTrait;

    /** @var Model The model instance for this repository */
    protected $model;

    /** @var string The model name for logging and cache keys */
    protected $modelName;

    /** @var string The cache key prefix based on model name */
    protected string $cacheKeyPrefix;

    /** @var array Default relationships to eager load */
    protected array $relationships = [];

    /**
     * Constructor
     *
     * @param Model $model The model to use
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->modelName = class_basename($model);
        $this->cacheKeyPrefix = Str::snake($this->modelName);
    }

    /**
     * Decode model ID
     *
     * @param mixed $id ID to decode
     * @return mixed
     */
    public function decode($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model->decode($id);
        }, 'decode ID');
    }

    /**
     * Get model instance
     *
     * @return Model
     */
    public function model()
    {
        return $this->handleError(function () {
            return $this->model;
        }, 'get model object');
    }

    /**
     * Retrieve all records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('all');
            return $this->remember($cacheKey, function () {
                return $this->model->get();
            });
        }, 'retrieve all records');
    }

    /**
     * Retrieve all records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithRelationships()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('with_relationship');
            return $this->remember($cacheKey, function () {
                return $this->model
                    ->with($this->relationships)
                    ->get();
            });
        }, 'retrieve all records with relationships');
    }

    /**
     * Retrieve all active records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveDataWithRelations()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('active_with_relationship');
            return $this->remember($cacheKey, function () {
                return $this->model
                    ->where('isActive', 1)
                    ->with($this->relationships)
                    ->get();
            });
        }, 'retrieve all active records with relationships');
    }

    /**
     * Retrieve all inactive records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInactiveDataWithRelations()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('inactive_with_relationship');
            return $this->remember($cacheKey, function () {
                return $this->model
                    ->where('isActive', 0)
                    ->with($this->relationships)
                    ->get();
            });
        }, 'retrieve all inactive records with relationships');
    }

    /**
     * Create new record
     *
     * @param array $data Data to create
     * @return Model
     */
    public function create(array $data)
    {
        return $this->handleError(function () use ($data) {
            // Cache will be cleared automatically via model observer
            return $this->model->create($data);
        }, 'create new record');
    }

    /**
     * Show record by ID (alias for findById)
     *
     * @param mixed $id ID to find
     * @return Model
     */
    public function show($id)
    {
        return $this->findById($id);
    }

    /**
     * Find record by ID
     *
     * @param mixed $id ID to find
     * @return Model
     */
    public function findById($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model->findOrFail($this->decode($id));
        }, 'find a record by ID');
    }

    /**
     * Update existing record
     *
     * @param mixed $id ID to update
     * @param array $data Data to update
     * @return Model
     */
    public function update($id, array $data)
    {
        return $this->handleError(function () use ($id, $data) {
            $model = $this->findById($id);
            $model->update($data);
            // Cache will be cleared automatically via model observer
            return $model;
        }, 'update record');
    }

    /**
     * Delete record
     *
     * @param mixed $id ID to delete
     * @return bool
     */
    public function delete($id)
    {
        return $this->handleError(function () use ($id) {
            $model = $this->findById($id);
            $result = $model->delete();
            // Cache will be cleared automatically via model observer
            return $result;
        }, 'delete record');
    }

    /**
     * Get soft-deleted records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function trashed()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('trashed');
            return $this->remember($cacheKey, function () {
                return $this->model->onlyTrashed()->get();
            });
        }, 'get trashed records');
    }

    /**
     * Find soft-deleted record by ID
     *
     * @param mixed $id ID to find
     * @return Model
     */
    public function findTrashedById($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model->onlyTrashed()
                ->findOrFail($this->decode($id));
        }, 'find trashed record by ID');
    }

    /**
     * Restore soft-deleted record
     *
     * @param mixed $id ID to restore
     * @return bool
     */
    public function restore($id): bool
    {
        return $this->handleError(function () use ($id) {
            return $this->model->onlyTrashed()
                ->findOrFail($this->decode($id))
                ->restore();
        }, 'restore record');
    }

    /**
     * Permanently delete record
     *
     * @param mixed $id ID to permanently delete
     * @return bool
     */
    public function forceDelete($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model
                ->onlyTrashed()
                ->findOrFail($this->decode($id))
                ->forceDelete();
        }, 'force delete record');
    }

    /**
     * Toggle the active status of a model
     *
     * @param Model $object The model to update
     * @param mixed $status Optional explicit status to set
     * @return string Status message
     */
    public function updateActiveStatus($object, $status = null)
    {
        return $this->handleError(
            function () use ($object, $status) {
                $object->isActive = $this->getNewActiveState($object, $status);
                $object->save();
                return $object->isActive ? 'activated' : 'deactivated';
            },
            'change the active status'
        );
    }

    /**
     * Get the new active state based on input status
     *
     * @param Model $object The model being updated
     * @param mixed $status The explicit status to set, or null to toggle
     * @return bool The new active state
     */
    private function getNewActiveState($object, $status = null): bool
    {
        return $status !== null ? $status !== 'active' : !$object->isActive;
    }
    

    /**
     * List all active records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveData()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('active');
            return $this->remember($cacheKey, function () {
                return $this->model
                    ->where('isActive', 1)
                    ->get();
            });
        }, 'list all active records');
    }

    /**
     * Pluck all active records as name-id pairs
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluckActiveData()
    {
        return $this->handleError(function () {
            $cacheKey = $this->generateCacheKey('pluck_active');
            return $this->remember($cacheKey, function () {
                return $this->model
                    ->where('isActive', 1)
                    ->pluck('name', 'id');
            });
        }, 'pluck all active records');
    }

    /**
     * Paginate records with relationships
     *
     * @param int $perPage Number of records per page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateWithRelationships($perPage = 25)
    {
        return $this->handleError(function () use ($perPage) {
            return $this->model
                ->with($this->relationships)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        }, 'paginate all data with relationships');
    }

    /**
     * Search records by query string
     *
     * @param string $searchTerm Term to search for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchByQuery(string $searchTerm)
    {
        // Implemented in child classes
        return collect();
    }

    /**
     * Live search for records
     *
     * @param string $searchTerm Term to search for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function liveSearch(string $searchTerm)
    {
        // Implemented in child classes
        return collect();
    }

    /**
     * Get filtered information
     *
     * @param string $filterTerm Term to filter by
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInformationBy(string $filterTerm)
    {
        // Implemented in child classes
        return collect();
    }

    /**
     * Delete all a record on where condition
     */
    public function deleteWhere($column,$value)
    {
        return $this->handleError(function () use ($column,$value) {
            return $this->model
                ->where($column,$value)
                ->delete();
        }, 'delete a record on where condition in repository');
    }

    /**
     * Get all the record and order by specific column
     */
    public function orderBy($column,$value)
    {
        return $this->handleError(function () use ($column,$value) {
            if(!is_null($value) && !is_null($column)) {
                return $this->model
                    ->orderBy($column,$value)
                    ->get();
            }
            return null;
        }, 'get all the record and order by specific column');
    }



}