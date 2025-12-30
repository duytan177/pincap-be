<?php

namespace App\Http\Requests\Admin\Users;

use App\Enums\User\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAdminUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Search fields - always nullable
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            
            // Enum validation
            'role' => ['nullable', Rule::in(Role::getValues())],
            
            // Filter for soft deletes
            'deleted_at' => ['nullable', 'string', 'in:null,not_null'],
            
            // Pagination
            'per_page' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
            
            // Ordering
            'order_key' => ['nullable', 'string'],
            'order_type' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}

