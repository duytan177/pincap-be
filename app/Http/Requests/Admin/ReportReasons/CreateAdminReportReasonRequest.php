<?php

namespace App\Http\Requests\Admin\ReportReasons;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminReportReasonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }
}

