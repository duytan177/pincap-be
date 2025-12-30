<?php

namespace App\Http\Requests\Admin\ReportReasons;

use Illuminate\Foundation\Http\FormRequest;

class GetAdminReportReasonsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'deleted_at' => ['nullable', 'string', 'in:null,not_null'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'order_key' => ['nullable', 'string'],
            'order_type' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}

