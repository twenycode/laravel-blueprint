<?php

namespace TwenyCode\LaravelBlueprint\Controllers;

use TwenyCode\LaravelBlueprint\Traits\ResponseJsonTrait;
use TwenyCode\LaravelBlueprint\Helpers\TextHelper;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * Base API Resource Controller with standard CRUD operations
 * Handles JSON responses for RESTful APIs
 */
abstract class BaseApiResourceController extends Controller
{
    use ResponseJsonTrait;

    protected string $controllerName;
    protected $layer;
    protected string $baseRouteName;
    protected string $resourceVariable;
    protected bool $checkAuthorization = true;
    protected bool $hasRelationShips = false;

    /**
     * Display listing of resources
     */
    public function index(): JsonResponse
    {
        $this->authorize('view');

        return $this->handleError(function () {
            return $this->successResponse(
                [$this->pluralizeVariable() => $this->getItems()],
                "Retrieved {$this->controllerName} list"
            );
        }, 'retrieve all resources');
    }

    /**
     * Store newly created resource
     */
    public function store($request): JsonResponse
    {
        $this->authorize('create');

        return $this->handleError(function () use ($request) {
            $item = $this->layer->create($request->validated());
            return $this->successResponse(
                [$this->resourceVariable => $item],
                "New {$this->controllerName} created",
                201
            );
        }, 'create new', $request->input());
    }

    /**
     * Display specified resource
     */
    public function show($id): JsonResponse
    {
        $this->authorize('view');

        return $this->handleError(function () use ($id) {
            return $this->successResponse(
                [$this->resourceVariable => $this->layer->find($id)],
                "{$this->controllerName} retrieved"
            );
        }, "show with ID {$id}");
    }

    /**
     * Update specified resource
     */
    public function update($request, $id): JsonResponse
    {
        $this->authorize('update');

        return $this->handleError(function () use ($id, $request) {
            $item = $this->layer->update($id, $request->validated());
            return $this->successResponse(
                [$this->resourceVariable => $item],
                "{$this->controllerName} updated"
            );
        }, 'update', $request->input());
    }

    /**
     * Delete specified resource
     */
    public function destroy($id): JsonResponse
    {
        $this->authorize('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->delete($id);
            return $this->successResponse(
                null,
                "{$this->controllerName} deleted",
                204
            );
        }, "delete with ID {$id}");
    }

    /**
     * Display trashed resources
     */
    public function trashed(): JsonResponse
    {
        $this->authorize('delete');

        return $this->handleError(function () {
            return $this->successResponse(
                [$this->pluralizeVariable() => $this->layer->trashed()],
                "Retrieved trashed {$this->controllerName} list"
            );
        }, 'get all soft deleted records');
    }

    /**
     * Restore soft deleted resource
     */
    public function restore($id): JsonResponse
    {
        $this->authorize('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->restore($id);
            return $this->successResponse(
                null,
                "{$this->controllerName} restored"
            );
        }, "restore record with ID {$id}");
    }

    /**
     * Permanently delete resource
     */
    public function forceDelete($id): JsonResponse
    {
        $this->authorize('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->forceDelete($id);
            return $this->successResponse(
                null,
                "{$this->controllerName} permanently deleted",
                204
            );
        }, "permanent delete record with ID {$id}");
    }

    /**
     * Toggle active status of resource
     */
    public function toggleActive($id): JsonResponse
    {
        $this->authorize('update');

        return $this->handleError(function () use ($id) {
            $status = $this->layer->updateActiveStatus($id);
            return $this->successResponse(
                null,
                "{$this->controllerName} has been {$status}"
            );
        }, 'change the is_active column status');
    }

    /**
     * Authorize an action
     */
    protected function authorize(string $action, $resource = null): bool
    {
        if (!$this->checkAuthorization) {
            return true;
        }

        if (Gate::check($action, $resource ?? $this->layer->model())) {
            return true;
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Get items with or without relationships
     */
    protected function getItems()
    {
        return $this->hasRelationShips
            ? $this->layer->allWithRelations()
            : $this->layer->all();
    }

    /**
     * Get pluralized variable name
     */
    protected function pluralizeVariable(): string
    {
        return TextHelper::pluralizeVariableName($this->resourceVariable);
    }
}