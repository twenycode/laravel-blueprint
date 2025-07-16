<?php

namespace TwenyCode\LaravelBlueprint\Services;

use TwenyCode\LaravelBlueprint\Repositories\BaseRepositoryInterface;
use TwenyCode\LaravelBlueprint\Traits\ErrorHandlerTrait;
use Illuminate\Support\Facades\DB;
use Closure;

/**
 * Base Service Implementation
 * Service layer for handling business logic and transaction management
 */
class BaseService implements BaseServiceInterface
{
    use ErrorHandlerTrait;

    /** @var string The service name for logging */
    protected $serviceName;

    /** @var BaseRepositoryInterface The repository implementation */
    protected $repository;

    /**
     * Constructor
     *
     * @param BaseRepositoryInterface|null $repository The repository implementation
     */
    public function __construct($repository = null)
    {
        $this->repository = $repository;
        $this->serviceName = class_basename($this);
    }

    /**
     * Get model instance
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return $this->handleError(function () {
            return $this->repository->model();
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
            return $this->repository->getAll();
        }, 'retrieve all records');
    }

    /**
     * Get all active records
     */
    public function getActiveData()
    {
        return $this->handleError(function () {
            return $this->repository->getActiveData();
        }, 'retrieve all active records');
    }

    /**
     * Get all active records
     */
    public function getInactiveData()
    {
        return $this->handleError(function () {
            return $this->repository->getInactiveData();
        }, 'retrieve all active records');
    }
    

    /**
     * Retrieve all records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithRelationships()
    {
        return $this->handleError(function () {
            return $this->repository->getAllWithRelationships();
        }, 'retrieve all records with relationships');
    }

    /**
     * Get all active records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveDataWithRelations()
    {
        return $this->handleError(function () {
            return $this->repository->getActiveDataWithRelations();
        }, 'retrieve all active records with relationships');
    }

    /**
     * Get all inactive records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInactiveDataWithRelations()
    {
        return $this->handleError(function () {
            return $this->repository->getInactiveDataWithRelations();
        }, 'retrieve all inactive records with relationships');
    }

    /**
     * Create a new record
     *
     * @param array $data Data to create
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->handleError(function () use ($data) {
            return $this->repository->create($data);
        }, 'create a new record');
    }

    /**
     * Show record by ID
     *
     * @param mixed $id ID to find
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function show($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->show($id);
        }, 'show a record by ID');
    }

    /**
     * Find a record by ID
     *
     * @param mixed $id ID to find
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findById($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->findById($id);
        }, 'find a record by ID');
    }

    /**
     * Update an existing record
     *
     * @param mixed $id ID to update
     * @param array $data Data to update
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $data)
    {
        return $this->handleError(function () use ($id, $data) {
            return $this->repository->update($id, $data);
        }, 'update existing record');
    }

    /**
     * Delete a record
     *
     * @param mixed $id ID to delete
     * @return bool
     */
    public function delete($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->delete($id);
        }, 'delete existing record');
    }

    /**
     * Get soft-deleted records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function trashed()
    {
        return $this->handleError(function () {
            return $this->repository->trashed();
        }, 'view trashed objects');
    }

    /**
     * Restore a soft-deleted record
     *
     * @param mixed $id ID to restore
     * @return bool
     */
    public function restore($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->restore($id);
        }, 'restore deleted object');
    }

    /**
     * Permanently delete a soft-deleted record
     *
     * @param mixed $id ID to permanently delete
     * @return bool
     */
    public function forceDelete($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->repository->forceDelete($id);
        }, 'permanent delete object');
    }

    /**
     * Search records by query string
     *
     * @param string $searchTerm Term to search for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchByQuery(string $searchTerm)
    {
        return $this->handleError(function () use ($searchTerm) {
            return $this->repository->searchByQuery($searchTerm);
        }, 'search query');
    }

    /**
     * Get filtered information
     *
     * @param string $filterTerm Term to filter by
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInformationBy(string $filterTerm)
    {
        return $this->handleError(function () use ($filterTerm) {
            return $this->repository->getInformationBy($filterTerm);
        }, 'get filtered information');
    }

    /**
     * Live search for records
     *
     * @param string $searchTerm Term to search for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function liveSearch(string $searchTerm)
    {
        return $this->handleError(function () use ($searchTerm) {
            return $this->repository->liveSearch($searchTerm);
        }, 'live search query');
    }

    /**
     * Update the active status of a record
     *
     * @param mixed $modelId ID of the model to update
     * @param mixed $status Optional explicit status to set
     * @return string Status message
     */
    public function updateActiveStatus($modelId, $status = null)
    {
        return $this->handleError(function () use ($modelId, $status) {
            $object = $this->repository->findById($modelId);
            return $this->repository->updateActiveStatus($object, $status);
        }, 'change of status.');
    }

    /**
     * Execute a Closure within a database transaction.
     *
     * @param \Closure $callback The callback to execute within the transaction
     * @param int $attempts Number of attempts
     * @return mixed The result of the callback
     */
    protected function transaction(Closure $callback, int $attempts = 1)
    {
        return DB::transaction($callback, $attempts);
    }
}