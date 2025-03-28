<?php

namespace TwenyCode\LaravelCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base Form Request with permission checking
 */
class BaseFormRequest extends FormRequest
{
    /**
     * Check if the user has the required permission
     *
     * @param string $permission The permission to check
     * @return bool
     */
    protected function checkPermission($permission)
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Check specific permission or super admin role
            if ($user->can($permission) || $user->hasRole('superAdmin')) {
                return true;
            }

            abort(403, 'You do not have the necessary permissions.');
        }

        abort(403, 'You must be logged in to perform this action.');
    }

}