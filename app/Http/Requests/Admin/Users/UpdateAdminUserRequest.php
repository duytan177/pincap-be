<?php

namespace App\Http\Requests\Admin\Users;

use App\Enums\User\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:20'],
            'last_name' => ['sometimes', 'string', 'max:20'],
            'email' => ['sometimes', 'string', 'email', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['nullable', Rule::in(Role::getValues())],
            'avatar' => ['nullable', 'image', 'max:25600'],
            'background' => ['nullable', 'image', 'max:25600'],
        ];
    }
}

