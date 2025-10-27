<?php

namespace TwenyCode\LaravelBlueprint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorizedFormRequest extends FormRequest
{
    /**
     * Check if the user has the required permission.
     */
    protected function checkPermission(string $permission): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->can($permission) || auth()->user()->hasRole(config('tweny-blueprint.authorization.super_admin_role'));
    }
}