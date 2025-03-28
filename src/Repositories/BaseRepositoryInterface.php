<?php

namespace TwenyCode\LaravelCore\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Repository Interface
 * Defines the contract for all repository implementations
 */
interface BaseRepositoryInterface
{
    /**
     * Get the model instance
     *
     * @return Model
     */
    public function model();

    /**
     * Decode a hashed model ID
     *
     * @param mixed $id ID to decode
     * @return mixed
     */
    public function decode($id);

    /**
     * Retrieve all records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveData();

    /**
     * Get all active records as key-value pairs
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluckActiveData();

    /**
     * Paginate records with relationships
     *
     * @param int $perPage Number of records per page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateWithRelationships(int $perPage);

    /**
     * Search records by query string
     *
     * @param string $searchTerm Term to search for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchByQuery(string $searchTerm);

    /**
     * Live search for records
     *
     * @param string $searchTerm Term to search for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function liveSearch(string $searchTerm);

    /**
     * Get filtered information
     *
     * @param string $filterTerm Term to filter by
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInformationBy(string $filterTerm);


    /**
     * Retrieve all records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Retrieve all records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithRelationships();

    /**
     * Get all active records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveDataWithRelations();

    /**
     * Get all inactive records with relationships
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInactiveDataWithRelations();

    /**
     * Create a new record
     *
     * @param array $data Data to create
     * @return Model
     */
    public function create(array $data);

    /**
     * Show a record by ID (alias for findById)
     *
     * @param mixed $id ID to find
     * @return Model
     */
    public function show($id);

    /**
     * Find a record by ID
     *
     * @param mixed $id ID to find
     * @return Model
     */
    public function findById($id);

    /**
     * Update an existing record
     *
     * @param mixed $id ID to update
     * @param array $data Data to update
     * @return Model
     */
    public function update($id, array $data);

    /**
     * Delete a record
     *
     * @param mixed $id ID to delete
     * @return bool
     */
    public function delete($id);

    /**
     * Get soft-deleted records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function trashed();

    /**
     * Find a soft-deleted record by ID
     *
     * @param mixed $id ID to find
     * @return Model
     */
    public function findTrashedById($id);

    /**
     * Restore a soft-deleted record
     *
     * @param mixed $id ID to restore
     * @return bool
     */
    public function restore($id);

    /**
     * Permanently delete a soft-deleted record
     *
     * @param mixed $id ID to permanently delete
     * @return bool
     */
    public function forceDelete($id);

    /**
     * Update the active status of a record
     *
     * @param Model $object The model to update
     * @param mixed $status Optional explicit status to set
     * @return string Status message
     */
    public function updateActiveStatus($object, $status = null);


}
