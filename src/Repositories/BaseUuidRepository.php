<?php

namespace TwenyCode\LaravelBlueprint\Repositories;

class BaseUuidRepository extends BaseRepository
{

    /**
     * Override the decode method since we're using UUIDs directly
     */
    public function decode($id)
    {
        // Since we're using UUIDs, we don't need to decode
        // Just return the UUID string as-is
        return $id;
    }

    /**
     * Override the encode method since we're using UUIDs directly
     */
    protected function encode($id)
    {
        // Since we're using UUIDs, we don't need to encode
        // Just return the UUID string as-is
        return $id;
    }

    /**
     * Find record by ID without decoding
     */
    public function findById($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model
                ->with($this->relationships)
                ->findOrFail($id); // Use $id directly, no decoding needed
        }, 'find a record by ID');
    }

    /**
     * Update record by ID without decoding
     */
    public function update($id, array $data)
    {
        return $this->handleError(function () use ($id, $data) {
            $record = $this->model->findOrFail($id); // Use $id directly
            $record->update($data);
            return $record->fresh();
        }, 'update record');
    }

    /**
     * Delete record by ID without decoding
     */
    public function delete($id)
    {
        return $this->handleError(function () use ($id) {
            $record = $this->model->findOrFail($id); // Use $id directly
            return $record->delete();
        }, 'delete record');
    }

    /**
     * Show record by ID without decoding
     */
    public function show($id)
    {
        return $this->handleError(function () use ($id) {
            return $this->model
                ->with($this->relationships)
                ->findOrFail($id); // Use $id directly
        }, 'show record');
    }

}