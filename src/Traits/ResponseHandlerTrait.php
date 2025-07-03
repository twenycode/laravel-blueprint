<?php

namespace TwenyCode\LaravelBlueprint\Traits;

/**
 * Trait for handling flash messages and redirects
 */
trait ResponseHandlerTrait
{
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
     */
    protected function errorMsg(string $message = self::DEFAULT_ERROR_MESSAGE)
    {
        return $this->flashMessage($message, self::MESSAGE_TYPE_ERROR);
    }

    /**
     * Display a success flash message
     */
    protected function successMsg(string $message = self::DEFAULT_CREATION_MESSAGE)
    {
        return $this->flashMessage($message, self::MESSAGE_TYPE_SUCCESS);
    }

    /**
     * Redirect back with an error message
     */
    public function error(string $message = self::DEFAULT_ERROR_MESSAGE)
    {
        $this->errorMsg($message);
        return back();
    }

    /**
     * Redirect to a route with an error message
     */
    public function errorRoute(string $route, string $message = self::DEFAULT_ERROR_MESSAGE)
    {
        $this->errorMsg($message);
        return redirect()->route($route);
    }

    /**
     * Redirect back with form input and an error message
     */
    public function formInputError($request, string $message = self::DEFAULT_FORM_ERROR_MESSAGE)
    {
        $this->errorMsg($message);
        return back()->withInput($request);
    }

    /**
     * Redirect back with a success message
     */
    public function success(string $message = self::DEFAULT_SUCCESS_MESSAGE)
    {
        $this->successMsg($message);
        return back();
    }

    /**
     * Redirect to a route with a success message
     */
    public function successRoute(string $route, string $message = self::DEFAULT_SUCCESS_MESSAGE)
    {
        $this->successMsg($message);
        return redirect()->route($route);
    }
}