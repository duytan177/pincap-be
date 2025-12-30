<?php

namespace App\Http\Requests\Admin\UserReports;

use App\Enums\Album_Media\StateReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminUserReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_state' => ['sometimes', Rule::in(StateReport::getValues())],
            'reason_report_id' => ['nullable', 'string', 'exists:reasons_report,id'],
            'other_reasons' => ['nullable', 'string'],
        ];
    }
}

