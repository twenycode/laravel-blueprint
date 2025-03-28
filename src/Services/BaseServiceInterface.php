<?php

namespace TwenyCode\LaravelBlueprint\Services;

/**
 * Base Service Interface
 * Defines the contract for all service implementations
 */
interface BaseServiceInterface
{
    /**
     * Get the model instance
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model();

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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data);

    /**
     * Show a record by ID
     *
     * @param mixed $id ID to find
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function show($id);

    /**
     * Find a record by ID
     *
     * @param mixed $id ID to find
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findById($id);

    /**
     * Update an existing record
     *
     * @param mixed $id ID to update
     * @param array $data Data to update
     * @return \Illuminate\Database\Eloquent\Model
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
     * @param mixed $modelId ID of the model to update
     * @param mixed $status Optional explicit status to set
     * @return string Status message
     */
    public function updateActiveStatus($modelId, $status = null);

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


}