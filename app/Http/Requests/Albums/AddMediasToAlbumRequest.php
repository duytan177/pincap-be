<?php

namespace App\Http\Requests\Albums;

use Illuminate\Foundation\Http\FormRequest;

class AddMediasToAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "album_id" => "required",
            'medias_id' => 'required|array',
        ];
    }
}
