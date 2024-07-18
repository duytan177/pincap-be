<?php

namespace App\Http\Requests\Users\Profiles;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMyProfileRequest extends FormRequest
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
            "firstName" => "required|string",
            "lastName" => "required|string",
            "email" => "required|email",
            'phone' => 'required|string|max:20',
            "password" => "nullable|confirmed|min:8",
            'avatar' => 'nullable|image|max:4096',
            'background' => 'nullable|image|max:4096',
        ];
    }
}
