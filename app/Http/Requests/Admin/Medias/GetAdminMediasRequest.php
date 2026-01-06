<?php

namespace App\Http\Requests\Admin\Medias;

use App\Enums\Album_Media\MediaType;
use App\Enums\Album_Media\Privacy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAdminMediasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Search fields - always nullable
            'media_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'user_search' => ['nullable', 'string', 'max:255'],
            
            // Enum validation
            'type' => ['nullable', Rule::in(MediaType::getValues())],
            'privacy' => ['nullable', Rule::in(Privacy::getValues())],
            
            // Boolean filters
            'is_created' => ['nullable', 'boolean'],
            'is_comment' => ['nullable', 'boolean'],
            'is_policy_violation' => ['nullable', 'boolean'],
            
            // Filter by user_id
            'media_owner_id' => ['nullable', 'string', 'uuid'],
            
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

