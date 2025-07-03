<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Trait for standardized error handling across application components
 */
trait ErrorHandlerTrait
{
    /**
     * Execute a function and handle any exceptions that occur.
     *
     * @param callable $function The function to execute
     * @param string $context Description of the operation
     * @param mixed $request Optional request data for form errors
     * @param string $msg Default error message
     * @return mixed
     * @throws Exception If error handling fails
     */
    protected function handleError(callable $function, string $context, mixed $request = null, string $msg = 'Something went wrong')
    {
        try {
            return $function();
        } catch (Exception $e) {
            // Check for specific exception types that need special handling
            if ($this->isSpecificException($e)) {
                return $this->handleSpecificException($e, $context, $request);
            }

            return $this->handleGenericException($e, $context, $msg, $request);
        }
    }

    /**
     * Check if the exception is a specific type that needs special handling.
     *
     * @param Exception $e The exception to check
     * @return bool
     */
    protected function isSpecificException(Exception $e): bool
    {
        // List of specific exception types to handle differently
        $specificExceptions = [
            // Add your specific exception classes here
            // Example: \App\Exceptions\CustomException::class,
        ];

        foreach ($specificExceptions as $exceptionClass) {
            if ($e instanceof $exceptionClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle specific custom exceptions.
     *
     * @param Exception $e The exception
     * @param string $context Description of the operation
     * @param mixed $request Optional request data
     * @return mixed
     * @throws Exception If handling fails
     */
    private function     handleSpecificException(Exception $e, string $context, $request = null)
    {
        Log::warning($this->getClassName() . ": {$context}: " . $e->getMessage());

        $errorMsg = $e->getMessage();

        if (method_exists($this, 'error')) {
            return is_null($request)
                ? $this->error($errorMsg, $e->getCode() ?: 500)
                : (method_exists($this, 'formInputError') ? $this->formInputError($request, $errorMsg) : null);
        }

        throw $e;
    }

    /**
     * Handle generic exceptions.
     *
     * @param Exception $e The exception
     * @param string $context Description of the operation
     * @param string $msg Default error message
     * @param mixed $request Optional request data
     * @return mixed
     * @throws Exception If handling fails
     */
    private function handleGenericException(Exception $e, string $context, string $msg, $request = null)
    {
        Log::error($this->getClassName() . " failed to {$context}: " . $e->getMessage());

        if (method_exists($this, 'formInputError') && $request !== null) {
            return $this->formInputError($request);
        }

        if (method_exists($this, 'error')) {
            return $this->error($msg);
        }

        throw $e;
    }

    /**
     * Get the class name for logging purposes.
     *
     * @return string
     */
    private function getClassName(): string
    {
        return $this->controllerName ?? $this->modelName ?? $this->serviceName ?? class_basename($this);
    }
}