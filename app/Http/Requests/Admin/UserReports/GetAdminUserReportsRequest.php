<?php

namespace App\Http\Requests\Admin\UserReports;

use App\Enums\Album_Media\StateReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAdminUserReportsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_state' => ['nullable', Rule::in(StateReport::getValues())],
            'user_id' => ['nullable', 'string', 'exists:users,id'],
            'user_report_id' => ['nullable', 'string', 'exists:users,id'],
            'deleted_at' => ['nullable', 'string', 'in:null,not_null'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'order_key' => ['nullable', 'string'],
            'order_type' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}

