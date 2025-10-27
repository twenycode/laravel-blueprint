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
    public function create(array $data): Model;

    /**
     * Find a record by ID
     */
    public function find($id): ?Model;

    /**
     * Update a record
     */
    public function update($id, array $data): Model;

    /**
     * Delete a record (soft delete)
     */
    public function delete($id): bool;

    // ====================================
    // Retrieval Operations
    // ====================================

    /**
     * Get all records
     */
    public function all(): Collection;

    /**
     * Get all records with relationships
     */
    public function allWithRelations(): Collection;

    /**
     * Get active records
     */
    public function active(): Collection;

    /**
     * Get active records with relationships
     */
    public function activeWithRelations(): Collection;

    /**
     * Get inactive records
     */
    public function inactive(): Collection;

    /**
     * Get inactive records with relationships
     */
    public function inactiveWithRelations(): Collection;

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15): Paginator;

    /**
     * Get trashed records
     */
    public function trashed(): Collection;

    // ====================================
    // Status Management
    // ====================================

    /**
     * Toggle active status of a record
     */
    public function toggleStatus($id): bool;

    /**
     * Activate a record
     */
    public function activate($id): bool;

    /**
     * Deactivate a record
     */
    public function deactivate($id): bool;

    // ====================================
    // Soft Delete Operations
    // ====================================

    /**
     * Restore a trashed record
     */
    public function restore($id): bool;

    /**
     * Permanently delete a record
     */
    public function forceDelete($id): bool;

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
    public function deleteBy(string $column, $value): bool;

    /**
     * Get records ordered by column
     */
    public function orderBy(string $column, string $direction = 'asc'): Collection;

    /**
     * Get the underlying model instance
     */
    public function model(): Model;
}