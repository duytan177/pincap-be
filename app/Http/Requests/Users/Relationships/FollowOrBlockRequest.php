<?php

namespace App\Http\Requests\Users\Relationships;

use App\Enums\User\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FollowOrBlockRequest extends FormRequest
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
            "followeeId" => "required|string",
            "status" => ["required", Rule::in(UserStatus::getKeys())]
        ];
    }

    public function messages(): array
    {
        return [
            "status.in" => ":attribute field must be either " . implode(', ', UserStatus::getKeys())
        ];
    }
}
