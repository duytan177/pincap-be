<?php

namespace App\Http\Requests\Admin\Medias;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // No validation needed - this endpoint only restores media (sets deleted_at to null)
        ];
    }
}

