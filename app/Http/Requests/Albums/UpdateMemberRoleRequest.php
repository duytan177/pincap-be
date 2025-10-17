<?php

namespace App\Http\Requests\Albums;

use App\Enums\Album_Media\AlbumRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only validate payload here. Authorization is handled in the controller.
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => [
                'required',
                'string',
                Rule::in([AlbumRole::VIEW, AlbumRole::EDIT]), // Disallow OWNER via validation
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Role is required',
            'role.in' => 'Role must be VIEW or EDIT',
        ];
    }
}


