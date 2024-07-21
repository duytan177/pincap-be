<?php

namespace App\Http\Requests\Medias;

use App\Enums\Album_Media\Privacy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMediaRequest extends FormRequest
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
            'media' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt',
                'max:25600' // 25 MB
            ],
            "media_name" => "nullable|string",
            "description" => "nullable|string",
            "privacy" => ["nullable", Rule::in(Privacy::getValues())],
            "is_comment" => "nullable|boolean",
            "is_created" => "nullable|boolean",
            "tags_name" => "nullable|array",
            "tags_name.*" => "nullable|string|max:255",
            "album_id" => "nullable|exists:albums,id"
        ];
    }
}
