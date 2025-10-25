<?php

namespace TwenyCode\LaravelBlueprint\Controllers;

use TwenyCode\LaravelBlueprint\Traits\ResponseJsonTrait;
use TwenyCode\LaravelBlueprint\Helpers\TextHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * Base API Resource Controller with standard CRUD operations
 * and JSON response formatting
 */
abstract class BaseApiResourceController extends Controller
{
    use ResponseJsonTrait;

    /** The controller's display name */
    protected $controllerName;

    /** Service or Repository interface */
    protected $layer;

    /** Base route name for the resource */
    protected $baseRouteName;

    /** Whether to check authorization */
    protected $checkAuthorization = true;

    /** Variable name for JSON responses */
    protected $resourceVariable;

    /** Whether the resource has relationships */
    protected $hasRelationShips = false;

    /**
     * Display a listing of the resources.
     */
    public function index(): JsonResponse
    {
        $this->authorizeAction('view');

        return $this->handleError(function () {
            $items = $this->hasRelationShips ? $this->layer->getAllWithRelationships() : $this->layer->getAll();
            $variableName = $this->getResourceVariableName(true);
            return $this->successResponse([$variableName => $items], "Retrieved {$this->controllerName} list");
        }, 'retrieve all resources');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function processStore($request): JsonResponse
    {
        $this->authorizeAction('create');

        return $this->handleError(function () use ($request) {
            $item = $this->layer->create($request->validated());
            $variableName = $this->getResourceVariableName();
            return $this->successResponse([$variableName => $item], "New {$this->controllerName} created", 201);
        }, "create new", $request->input());
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $this->authorizeAction('view');

        return $this->handleError(function () use ($id) {
            $item = $this->layer->findById($id);
            $variableName = $this->getResourceVariableName();
            return $this->successResponse([$variableName => $item], "{$this->controllerName} retrieved");
        }, "show with ID {$id}");
    }

    /**
     * Process update operation for a resource.
     */
    protected function processUpdate($request, $id): JsonResponse
    {
        $this->authorizeAction('update');

        return $this->handleError(function () use ($id, $request) {
            $item = $this->layer->update($id, $request->validated());
            $variableName = $this->getResourceVariableName();
            return $this->successResponse([$variableName => $item], "{$this->controllerName} updated");
        }, "update", $request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->delete($id);
            return $this->successResponse(null, "{$this->controllerName} deleted", 204);
        }, "delete with ID {$id}");
    }

    /**
     * Get all soft deleted records.
     */
    public function trashed(): JsonResponse
    {
        $this->authorizeAction('delete');

        return $this->handleError(function () {
            $items = $this->layer->trashed();
            $variableName = $this->getResourceVariableName(true);
            return $this->successResponse([$variableName => $items], "Retrieved trashed {$this->controllerName} list");
        }, "get all soft deleted records");
    }

    /**
     * Restore the specified soft deleted resource from storage.
     */
    public function restore($id): JsonResponse
    {
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->restore($id);
            return $this->successResponse(null, "{$this->controllerName} restored");
        }, "restore record with ID {$id}");
    }

    /**
     * Permanently delete the specified soft deleted resource from storage.
     */
    public function forceDelete($id): JsonResponse
    {
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->forceDelete($id);
            return $this->successResponse(null, "{$this->controllerName} permanently deleted", 204);
        }, "permanent delete record with ID {$id}");
    }

    /**
     * Update the active status of a resource.
     */
    public function updateActiveStatus($id): JsonResponse
    {
        $this->authorizeAction('update');

        return $this->handleError(function () use ($id) {
            $status = $this->layer->updateActiveStatus($id);
            return $this->successResponse(null, "{$this->controllerName} has been " . $status);
        }, "change the is_active column status");
    }

    /**
     * Get the resource variable name for JSON responses.
     */
    protected function getResourceVariableName($plural = false)
    {
        if ($plural) {
            return TextHelper::pluralizeVariableName($this->resourceVariable);
        }
        return $this->resourceVariable;
    }

    /**
     * Check if user has permission for an action.
     */
    public function authorizeAction($action, $object = null)
    {
        if ($this->checkAuthorization) {
            if (Gate::check($action, $object ?? $this->layer->model())) {
                return true;
            }
            abort(403, 'Unauthorized action.');
        }
        return true;
    }

    private function getClassName(): string
    {
        return $this->controllerName ?? class_basename($this);
    }
}