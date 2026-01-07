<?php

namespace App\Http\Requests\Admin\Albums;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // No validation needed - this endpoint only restores album (sets deleted_at to null)
        ];
    }
}

