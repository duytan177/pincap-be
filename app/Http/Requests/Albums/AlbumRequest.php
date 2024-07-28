<?php

namespace App\Http\Requests\Albums;

use App\Enums\Album_Media\Privacy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlbumRequest extends FormRequest
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
            "album_name" => "required|string",
            'image' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt',
                'max:25600' // 25 MB
            ],
            "description" => "nullable|string",
            "privacy" => ["nullable", Rule::in(Privacy::getValues())],
        ];
    }
}
