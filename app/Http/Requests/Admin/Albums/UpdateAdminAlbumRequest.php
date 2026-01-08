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
            'album_name' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }
}

