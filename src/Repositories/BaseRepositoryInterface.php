<?php

namespace TwenyCode\LaravelBlueprint\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Repository Interface
 *
 * Standard contract for repository pattern implementation.
 */
interface BaseRepositoryInterface
{
    /**
     * Get the model instance
     */
    public function model();

    /**
     * Decode a hashed/encoded ID
     */
    public function decodeId($id);

    /**
     * Encode an ID to hash/obfuscate it
     */
    public function encodeId($id);

    /**
     * Get all records
     */
    public function all();

    /**
     * Get all records with relationships
     */
    public function allWithRelations();

    /**
     * Get active records
     */
    public function active();

    /**
     * Get active records with relationships
     */
    public function activeWithRelations();

    /**
     * Get inactive records
     */
    public function inactive();

    /**
     * Get inactive records with relationships
     */
    public function inactiveWithRelations();

    /**
     * Pluck active records as key-value pairs
     */
    public function pluckActive(string $value = 'name', string $key = 'id');

    /**
     * Create a new record
     */
    public function create(array $data);

    /**
     * Bulk create records
     */
    public function createMany(array $records);

    /**
     * Find a record by ID
     */
    public function find($id);

    /**
     * Update a record
     */
    public function update($id, array $data);

    /**
     * Delete a record
     */
    public function delete($id);

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15);

    /**
     * Toggle active status
     */
    public function toggleStatus(Model $model);

    /**
     * Get trashed records
     */
    public function trashed();

    /**
     * Find a trashed record by ID
     */
    public function findTrashed($id);

    /**
     * Restore a trashed record
     */
    public function restore($id);

    /**
     * Permanently delete a record
     */
    public function forceDelete($id);

    /**
     * Delete records by column value
     */
    public function deleteBy(string $column, $value);

    /**
     * Order records by column
     */
    public function orderBy(string $column, string $direction = 'asc');

    /**
     * Get query builder for custom queries
     */
    public function query();

    /**
     * Find by multiple IDs
     */
    public function findMany(array $ids);

    /**
     * Search records
     */
    public function search(string $term, array $columns = ['name']);


}