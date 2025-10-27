<?php

namespace TwenyCode\LaravelBlueprint\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use TwenyCode\LaravelBlueprint\Repositories\BaseRepositoryInterface;
use TwenyCode\LaravelBlueprint\Traits\DatabaseTransactionTrait;
use TwenyCode\LaravelBlueprint\Traits\ErrorHandlingTrait;

/**
 * Base Service class that handles business logic and delegates to repository
 */
class BaseService implements BaseServiceInterface
{
    use ErrorHandlingTrait, DatabaseTransactionTrait;

    /**
     * Repository instance for data access operations
     */
    protected BaseRepositoryInterface $repository;

    /**
     * Service class name used for logging
     */
    protected string $serviceName;

    /**
     * Constructor - requires repository to be injected
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->serviceName = class_basename($this);
    }

    // ====================================
    // Core CRUD Operations
    // ====================================

    /**
     * Create a new record with the provided data
     */
    public function create(array $data)
    {
        return $this->handleError(function () use ($data) {
            return $this->repository->create($data);
        }, 'create record');
    }

    /**
     * Find a record by ID - returns null if not found
     */
    public function find($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->find($id);
        }, 'find record by ID');
    }

    /**
     * Update an existing record and return the fresh instance
     */
    public function update($id, array $data)
    {
        return $this->handleError(function () use ($id, $data) {
            return $this->repository->update($id, $data);
        }, 'update record');
    }

    /**
     * Delete a record (soft delete if model supports it)
     */
    public function delete($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->delete($id);
        }, 'delete record');
    }

    // ====================================
    // Retrieval Operations
    // ====================================

    /**
     * Get all records from the database
     */
    public function all()
    {
        return $this->handleError(function () {
            return $this->repository->all();
        }, 'retrieve all records');
    }

    /**
     * Get all records with relationships eager loaded
     */
    public function allWithRelations()
    {
        return $this->handleError(function () {
            return $this->repository->allWithRelations();
        }, 'retrieve all records with relationships');
    }

    /**
     * Get only active records (is_active = true)
     */
    public function active()
    {
        return $this->handleError(function () {
            return $this->repository->active();
        }, 'retrieve active records');
    }

    /**
     * Get active records with relationships loaded
     */
    public function activeWithRelations()
    {
        return $this->handleError(function () {
            return $this->repository->activeWithRelations();
        }, 'retrieve active records with relationships');
    }

    /**
     * Get only inactive records (is_active = false)
     */
    public function inactive()
    {
        return $this->handleError(function () {
            return $this->repository->inactive();
        }, 'retrieve inactive records');
    }

    /**
     * Get inactive records with relationships loaded
     */
    public function inactiveWithRelations()
    {
        return $this->handleError(function () {
            return $this->repository->inactiveWithRelations();
        }, 'retrieve inactive records with relationships');
    }

    /**
     * Get paginated records with latest-first ordering
     */
    public function paginate(int $perPage = 15)
    {
        return $this->handleError(function () use ($perPage) {
            return $this->repository->paginate($perPage);
        }, 'paginate records');
    }

    /**
     * Get soft-deleted records only
     */
    public function trashed()
    {
        return $this->handleError(function () {
            return $this->repository->trashed();
        }, 'retrieve trashed records');
    }

    // ====================================
    // Status Management
    // ====================================

    /**
     * Toggle is_active status - switches between true and false
     */
    public function toggleStatus($id)
    {
        return $this->handleError(function () use ($id) {
            $model = $this->repository->find($id);
            return $this->repository->toggleStatus($model);
        }, 'toggle active status');
    }

    /**
     * Activate a record - set is_active to true
     */
    public function activate($id)
    {
        return $this->handleError(function () use ($id) {
            $model = $this->repository->find($id);
            return $model->activate();
        }, 'activate record');
    }

    /**
     * Deactivate a record - set is_active to false
     */
    public function deactivate($id)
    {
        return $this->handleError(function () use ($id) {
            $model = $this->repository->find($id);
            return $model->deactivate();
        }, 'deactivate record');
    }

    // ====================================
    // Soft Delete Operations
    // ====================================

    /**
     * Restore a soft-deleted record back to active
     */
    public function restore($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->restore($id);
        }, 'restore trashed record');
    }

    /**
     * Permanently delete a soft-deleted record from database
     */
    public function forceDelete($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->forceDelete($id);
        }, 'permanently delete record');
    }

    // ====================================
    // Utility Operations
    // ====================================

    /**
     * Pluck active records as key-value pairs
     */
    public function pluckActive(string $value = 'name', string $key = 'id')
    {
        return $this->handleError(function () use ($value, $key) {
            return $this->repository->pluckActive($value, $key);
        }, 'pluck active records');
    }

    /**
     * Delete all records matching a specific column value
     */
    public function deleteBy(string $column, $value)
    {
        return $this->handleError(function () use ($column, $value) {
            return $this->repository->deleteBy($column, $value);
        }, 'delete records by criteria');
    }

    /**
     * Get records ordered by a specific column and direction
     */
    public function orderBy(string $column, string $direction = 'asc')
    {
        return $this->handleError(function () use ($column, $direction) {
            return $this->repository->orderBy($column, $direction);
        }, 'retrieve ordered records');
    }

    /**
     * Get the underlying model instance for advanced queries
     */
    public function model()
    {
        return $this->handleError(function () {
            return $this->repository->model();
        }, 'get model instance');
    }

}