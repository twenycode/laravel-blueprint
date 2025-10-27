<?php

namespace TwenyCode\LaravelBlueprint\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Base Service Interface
 *
 * Defines the contract for all service implementations.
 * Services handle business logic while repositories manage data access.
 */
interface BaseServiceInterface
{
    // ====================================
    // Core CRUD Operations
    // ====================================

    /**
     * Create a new record
     */
    public function create(array $data);

    /**
     * Find a record by ID
     */
    public function find($id);

    /**
     * Update a record
     */
    public function update($id, array $data);

    /**
     * Delete a record (soft delete)
     */
    public function delete($id);

    // ====================================
    // Retrieval Operations
    // ====================================

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
     * Paginate records
     */
    public function paginate(int $perPage = 15);

    /**
     * Get trashed records
     */
    public function trashed();

    // ====================================
    // Status Management
    // ====================================

    /**
     * Toggle active status of a record
     */
    public function toggleStatus($id);

    /**
     * Activate a record
     */
    public function activate($id);

    /**
     * Deactivate a record
     */
    public function deactivate($id);

    // ====================================
    // Soft Delete Operations
    // ====================================

    /**
     * Restore a trashed record
     */
    public function restore($id);

    /**
     * Permanently delete a record
     */
    public function forceDelete($id);

    // ====================================
    // Utility Operations
    // ====================================

    /**
     * Pluck active records as key-value pairs
     */
    public function pluckActive(string $value = 'name', string $key = 'id');

    /**
     * Delete records by column value
     */
    public function deleteBy(string $column, $value);

    /**
     * Get records ordered by column
     */
    public function orderBy(string $column, string $direction = 'asc');

    /**
     * Get the underlying model instance
     */
    public function model();
}