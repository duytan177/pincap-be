<?php

namespace App\Http\Requests\Users\SocialAccounts\Instagram;

use Illuminate\Foundation\Http\FormRequest;

class InstagramMediaSyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // chỉ cần user đã login
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|max:100',
            'ids.*' => 'required|string', // mỗi id phải là string
        ];
    }
}
