<?php

namespace App\Http\Requests\Users\Profiles;

use Illuminate\Foundation\Http\FormRequest;

class GetMyFollowerOrFolloweeRequest extends FormRequest
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
            "per_page" => "nullable|integer|min:1",
            "page" => "nullable|integer|min:1",
            "relationship" => "required|in:followers,followees"
        ];
    }

    public function messages(): array
    {
        return [
            "relationship.in" => ":attribute field must be either 'followers' or 'followees'."
        ];
    }
}
