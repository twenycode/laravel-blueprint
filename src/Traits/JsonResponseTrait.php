<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait for handling JSON responses in API controllers
 */
trait JsonResponseTrait
{
    /**
     * Return a success JSON response
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error JSON response
     */
    protected function errorResponse(string $message = 'Something went wrong', int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Override error handling to return JSON responses.
     */
    protected function handleError(callable $function, string $context, mixed $request = null, string $msg = 'Something went wrong'): JsonResponse
    {
        try {
            return $function();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse("{$this->controllerName} not found", 404);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->errorResponse('Unauthorized action', 403);
        } catch (\Exception $e) {
            \Log::error($this->getClassName() . " failed to {$context}: " . $e->getMessage());
            return $this->errorResponse($msg, 500);
        }
    }

    /**
     * Get the class name for logging purposes.
     */
    private function getClassName(): string
    {
        return $this->controllerName ?? class_basename($this);
    }
}