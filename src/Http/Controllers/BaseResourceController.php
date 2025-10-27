<?php

namespace TwenyCode\LaravelBlueprint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use TwenyCode\LaravelBlueprint\Helpers\TextHelper;
use TwenyCode\LaravelBlueprint\Traits\ErrorHandlingTrait;
use TwenyCode\LaravelBlueprint\Traits\HandleResponseTrait;

/**
 * Base Resource Controller with standard CRUD operations
 * Handles web-based views and redirects
 */
abstract class BaseResourceController extends Controller
{
    use ErrorHandlingTrait, HandleResponseTrait;

    protected string $controllerName;
    protected $layer;
    protected string $baseViewName;
    protected string $baseRouteName;
    protected string $resourceVariable;
    protected bool $checkAuthorization = true;
    protected bool $hasRelationShips = false;

    /**
     * Display listing of resources
     */
    public function index()
    {
        $this->authorize('view');

        return $this->handleError(function () {
            $items = $this->getItems();
            return view($this->baseViewName . '.index', [
                $this->pluralizeVariable() => $items
            ]);
        }, 'retrieve all resources');
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create');

        return $this->handleError(function () {
            return view($this->baseViewName . '.create', [
                $this->resourceVariable => $this->layer->model()
            ]);
        }, 'show create form');
    }

    /**
     * Store newly created resource
     */
    public function store($request)
    {
        $this->authorize('create');

        return $this->handleError(function () use ($request) {
            $this->layer->create($request->validated());
            return $this->successRoute($this->baseRouteName . '.index',
                "New {$this->controllerName} added");
        }, 'create new', $request->input());
    }

    /**
     * Display specified resource
     */
    public function show($id)
    {
        $this->authorize('view');

        return $this->handleError(function () use ($id) {
            return view($this->baseViewName . '.show', [
                $this->resourceVariable => $this->layer->find($id)
            ]);
        }, "show with ID {$id}");
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $this->authorize('update');

        return $this->handleError(function () use ($id) {
            return view($this->baseViewName . '.edit', [
                $this->resourceVariable => $this->layer->find($id)
            ]);
        }, "edit with ID {$id}");
    }

    /**
     * Update specified resource
     */
    public function update($request, $id)
    {
        $this->authorize('update');

        return $this->handleError(function () use ($id, $request) {
            $this->layer->update($id, $request->validated());
            return $this->successRoute($this->baseRouteName . '.index',
                "{$this->controllerName} updated");
        }, 'update', $request->input());
    }

    /**
     * Delete specified resource
     */
    public function destroy($id)
    {
        $this->authorize('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->delete($id);
            return $this->successRoute($this->baseRouteName . '.index',
                "{$this->controllerName} deleted");
        }, "delete with ID {$id}");
    }

    /**
     * Display trashed resources
     */
    public function trashed()
    {
        $this->authorize('delete');

        return $this->handleError(function () {
            return view($this->baseViewName . '.trash', [
                $this->pluralizeVariable() => $this->layer->trashed()
            ]);
        }, 'get all soft deleted records');
    }

    /**
     * Restore soft deleted resource
     */
    public function restore($id)
    {
        $this->authorize('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->restore($id);
            return $this->successRoute($this->baseRouteName . '.trash',
                "{$this->controllerName} restored");
        }, "restore record with ID {$id}");
    }

    /**
     * Permanently delete resource
     */
    public function forceDelete($id)
    {
        $this->authorize('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->forceDelete($id);
            return $this->successRoute($this->baseRouteName . '.trash',
                "{$this->controllerName} permanently deleted");
        }, "permanent delete record with ID {$id}");
    }

    /**
     * Toggle active status of resource
     */
    public function toggleActive($id)
    {
        $this->authorize('update');

        return $this->handleError(function () use ($id) {
            $status = $this->layer->updateActiveStatus($id);
            return $this->successRoute($this->baseRouteName . '.index',
                "{$this->controllerName} has been {$status}");
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