<?php

namespace TwenyCode\LaravelBlueprint\Controllers;

use TwenyCode\LaravelBlueprint\Traits\ErrorHandlerTrait;
use TwenyCode\LaravelBlueprint\Traits\ResponseHandlerTrait;
use TwenyCode\LaravelBlueprint\Helpers\TextHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;

/**
 * Base Resource Controller with standard CRUD operations
 */
abstract class BaseResourceController extends Controller
{
    use ErrorHandlerTrait, ResponseHandlerTrait;

    /** The controller's display name */
    protected $controllerName;

    /** Service or Repository interface */
    protected $layer;

    /** Base view name for the resource */
    protected $baseViewName;

    /** Base route name for the resource */
    protected $baseRouteName;

    /** Whether to check authorization */
    protected $checkAuthorization = true;

    /** Variable name for views */
    protected $resourceVariable;

    /** Whether the resource has relationships */
    protected $hasRelationShips = false;

    /**
     * Display a listing of the resources.
     */
    public function index()
    {
        $this->authorizeAction('view');

        return $this->handleError(function () {
            $items = $this->hasRelationShips ? $this->layer->getAllWithRelationships() : $this->layer->getAll();
            $variableName = $this->getResourceVariableName(true);
            return view($this->getView('index'), [$variableName => $items]);
        }, 'retrieve all resources');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAction('create');

        return $this->handleError(function () {
            $item = $this->layer->model();
            $variableName = $this->getResourceVariableName();
            return view($this->getView('create'), [$variableName => $item]);
        }, "show create form");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function processStore($request)
    {
        $this->authorizeAction('create');

        return $this->handleError(function () use ($request) {
            $this->layer->create($request->validated());
            return $this->success("New {$this->controllerName} added");
        }, "create new", $request->input());
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorizeAction('view');

        return $this->handleError(function () use ($id) {
            $item = $this->layer->show($id);
            $variableName = $this->getResourceVariableName();
            return view($this->getView('show'), [$variableName => $item]);
        }, "show with ID {$id}");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorizeAction('update');

        return $this->handleError(function () use ($id) {
            $item = $this->layer->findById($id);
            $variableName = $this->getResourceVariableName();
            return view($this->getView('edit'), [$variableName => $item]);
        }, "edit with ID {$id}");
    }

    /**
     * Process update operation for a resource.
     */
    protected function processUpdate($request, $id)
    {
        $this->authorizeAction('update');

        return $this->handleError(function () use ($id, $request) {
            $this->layer->update($id, $request->validated());
            return $this->successRoute($this->baseRouteName . '.index', "{$this->controllerName} updated");
        }, "update", $request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->delete($id);
            return $this->success("{$this->controllerName} deleted");
        }, "delete with ID {$id}");
    }

    /**
     * Get all soft deleted records.
     */
    public function trashed()
    {
        $this->authorizeAction('delete');

        return $this->handleError(function () {
            $items = $this->layer->trashed();
            $variableName = $this->getResourceVariableName(true);
            return view($this->getView('trash'), [$variableName => $items]);
        }, "get all soft deleted records");
    }

    /**
     * Restore the specified soft deleted resource from storage.
     */
    public function restore($id)
    {
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->restore($id);
            return $this->success("{$this->controllerName} restored");
        }, "restore record with ID {$id}");
    }

    /**
     * Permanently delete the specified soft deleted resource from storage.
     */
    public function forceDelete($id)
    {
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->forceDelete($id);
            return $this->success("{$this->controllerName} permanently deleted");
        }, "permanent delete record with ID {$id}");
    }

    /**
     * Update the active status of a resource.
     */
    public function updateActiveStatus($id)
    {
        $this->authorizeAction('update');

        return $this->handleError(function () use ($id) {
            $status = $this->layer->updateActiveStatus($id);
            return $this->success("{$this->controllerName} has been " . $status);
        }, "change the is_active column status");
    }

    /**
     * Get the resource variable name for view.
     */
    protected function getResourceVariableName($plural = false)
    {
        if ($plural) {
            return TextHelper::pluralizeVariableName($this->resourceVariable);
        }
        return $this->resourceVariable;
    }

    /**
     * Get view path for the specified action.
     */
    public function getView($action)
    {
        return $this->baseViewName . '.' . $action;
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
}