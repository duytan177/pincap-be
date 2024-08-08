<?php

namespace App\Http\Requests\Medias\Reactions;

use Illuminate\Foundation\Http\FormRequest;

class CommentMediaRequest extends FormRequest
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
            'image' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:25600' // 25 MB
            ],
            "media_id" => ["required", "exists:medias,id"],
            "content" => ["required"]
        ];
    }
}
