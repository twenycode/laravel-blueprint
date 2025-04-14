<?php

namespace TwenyCode\LaravelBlueprint\Controllers;

use TwenyCode\LaravelBlueprint\Traits\ErrorHandlerTrait;
use TwenyCode\LaravelBlueprint\Helpers\TextHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;

/**
 * Base Resource Controller with standard CRUD operations
 * and message/flash functionality
 */
abstract class BaseResourceController extends Controller
{
    use ErrorHandlerTrait;

    /** @var string The controller's display name */
    protected $controllerName;

    /** @var mixed Service or Repository interface */
    protected $layer;

    /** @var string Base view name for the resource */
    protected $baseViewName;

    /** @var string Base route name for the resource */
    protected $baseRouteName;

    /** @var bool Whether to check authorization */
    protected $checkAuthorization = true;

    /** @var string Variable name for views */
    protected $resourceVariable;

    /** @var bool Whether the resource has relationships */
    protected $hasRelationShips = false;

    /**
     * Flash message types
     */
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_SUCCESS = 'success';

    /**
     * Default messages
     */
    const DEFAULT_ERROR_MESSAGE = 'Something went wrong';
    const DEFAULT_SUCCESS_MESSAGE = 'Operation successful';
    const DEFAULT_CREATION_MESSAGE = 'Successfully created';
    const DEFAULT_FORM_ERROR_MESSAGE = 'There was a problem with your input';

    /**
     * Display a flash message
     *
     * @param string $message The message to display
     * @param string $type The type of message (error/success)
     * @return mixed
     */
    protected function flashMessage(string $message, string $type)
    {
        // Use toast() if available (from a 3rd party library), or fall back to session flash
        if (function_exists('toast')) {
            return toast($message, $type);
        }

        return session()->flash($type, $message);
    }

    /**
     * Display an error flash message
     *
     * @param string $message The error message
     * @return mixed
     */
    protected function errorMsg(string $message = self::DEFAULT_ERROR_MESSAGE)
    {
        return $this->flashMessage($message, self::MESSAGE_TYPE_ERROR);
    }

    /**
     * Display a success flash message
     *
     * @param string $message The success message
     * @return mixed
     */
    protected function successMsg(string $message = self::DEFAULT_CREATION_MESSAGE)
    {
        return $this->flashMessage($message, self::MESSAGE_TYPE_SUCCESS);
    }

    /**
     * Redirect back with an error message
     *
     * @param string $message The error message
     * @return mixed
     */
    public function error(string $message = self::DEFAULT_ERROR_MESSAGE)
    {
        $this->errorMsg($message);
        return back();
    }

    /**
     * Redirect to a route with an error message
     *
     * @param string $route The route to redirect to
     * @param string $message The error message
     * @return mixed
     */
    public function errorRoute(string $route, string $message = self::DEFAULT_ERROR_MESSAGE)
    {
        $this->errorMsg($message);
        return redirect()->route($route);
    }

    /**
     * Redirect back with form input and an error message
     *
     * @param mixed $request The request containing input data
     * @param string $message The error message
     * @return mixed
     */
    public function formInputError($request, string $message = self::DEFAULT_FORM_ERROR_MESSAGE)
    {
        $this->errorMsg($message);
        return back()->withInput($request);
    }

    /**
     * Redirect back with a success message
     *
     * @param string $message The success message
     * @return mixed
     */
    public function success(string $message = self::DEFAULT_SUCCESS_MESSAGE)
    {
        $this->successMsg($message);
        return back();
    }

    /**
     * Redirect to a route with a success message
     *
     * @param string $route The route to redirect to
     * @param string $message The success message
     * @return mixed
     */
    public function successRoute(string $route, string $message = self::DEFAULT_SUCCESS_MESSAGE)
    {
        $this->successMsg($message);
        return redirect()->route($route);
    }

    /**
     * Display a listing of the resources.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check authorization
        $this->authorizeAction('view');

        return $this->handleError(function () {
            $items = $this->hasRelationShips ? $this->layer->getAllWithRelationships() : $this->layer->getAll();
            $variableName = $this->getResourceVariableName(true);
            return view($this->getView('index'), [$variableName => $items]);
        }, 'retrieve all resources');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Check authorization
        $this->authorizeAction('create');

        return $this->handleError(function () {
            $item = $this->layer->model();
            $variableName = $this->getResourceVariableName();
            return view($this->getView('create'), [$variableName => $item]);
        }, "show create form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param mixed $request The validated request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processStore($request)
    {
        // Check authorization
        $this->authorizeAction('create');

        return $this->handleError(function () use ($request) {
            $this->layer->create($request->validated());
            return $this->success("New {$this->controllerName} added");
        }, "create new", $request);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id The resource ID
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Check authorization
        $this->authorizeAction('view');

        return $this->handleError(function () use ($id) {
            $item = $this->layer->findById($id);
            $variableName = $this->getResourceVariableName();
            return view($this->getView('show'), [$variableName => $item]);
        }, "show with ID {$id}");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id The resource ID
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Check authorization
        $this->authorizeAction('update');

        return $this->handleError(function () use ($id) {
            $item = $this->layer->findById($id);
            $variableName = $this->getResourceVariableName();
            return view($this->getView('edit'), [$variableName => $item]);
        }, "edit with ID {$id}");
    }

    /**
     * Process update operation for a resource.
     *
     * @param \Illuminate\Http\Request $request The request
     * @param mixed $id The resource ID
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function processUpdate($request, $id)
    {
        // Check authorization
        $this->authorizeAction('update');

        return $this->handleError(function () use ($id, $request) {
            $this->layer->update($id, $request->validated());
            return $this->successRoute($this->baseRouteName . '.index', "{$this->controllerName} updated");
        }, "update", $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id The resource ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Check authorization
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->delete($id);
            return $this->success("{$this->controllerName} deleted");
        }, "delete with ID {$id}");
    }

    /**
     * Get all soft deleted records.
     *
     * @return \Illuminate\View\View
     */
    public function trashed()
    {
        // Check authorization
        $this->authorizeAction('delete');

        return $this->handleError(function () {
            $items = $this->layer->trashed();
            $variableName = $this->getResourceVariableName(true);
            return view($this->getView('trash'), [$variableName => $items]);
        }, "get all soft deleted records");
    }

    /**
     * Restore the specified soft deleted resource from storage.
     *
     * @param int $id The resource ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        // Check authorization
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->restore($id);
            return $this->success("{$this->controllerName} restored");
        }, "restore record with ID {$id}");
    }

    /**
     * Permanently delete the specified soft deleted resource from storage.
     *
     * @param int $id The resource ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        // Check authorization
        $this->authorizeAction('delete');

        return $this->handleError(function () use ($id) {
            $this->layer->forceDelete($id);
            return $this->success("{$this->controllerName} permanently deleted");
        }, "permanent delete record with ID {$id}");
    }

    /**
     * Update the active status of a resource.
     *
     * @param int $id The resource ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateActiveStatus($id)
    {
        // Check authorization
        $this->authorizeAction('update');

        return $this->handleError(function () use ($id) {
            $status = $this->layer->updateActiveStatus($id);
            return $this->success("{$this->controllerName} has been " . $status);
        }, "change the isActive column status");
    }

    /**
     * Get the resource variable name for view.
     *
     * @param bool $plural Whether to use plural form
     * @return string
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
     *
     * @param string $action The action name.
     * @return string The view path.
     */
    public function getView($action)
    {
        return $this->baseViewName . '.' . $action;
    }

    /**
     * Check if user has permission for an action.
     *
     * @param string $action The action to check
     * @param mixed $object Optional object to check against
     * @return bool True if authorized
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