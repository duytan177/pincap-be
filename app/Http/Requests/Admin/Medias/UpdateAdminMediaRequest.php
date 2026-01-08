<?php

namespace App\Http\Requests\Admin\Medias;

use App\Enums\Album_Media\Privacy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_name' => 'nullable|string',
            'description' => 'nullable|string',
            'privacy' => ['nullable', Rule::in(Privacy::getValues())],
        ];
    }
}

