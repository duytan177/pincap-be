<?php

namespace App\Http\Requests\Admin\ReportReasons;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminReportReasonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
        ];
    }
}

