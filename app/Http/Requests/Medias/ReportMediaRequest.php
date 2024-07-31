<?php

namespace App\Http\Requests\Medias;

use Illuminate\Foundation\Http\FormRequest;

class ReportMediaRequest extends FormRequest
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
            "media_id" => "required|string|exists:medias,id",
            "reason_report_id" => "nullable|string|exists:reasons_report,id|required_without:other_reasons",
            "other_reasons" => "nullable|string|required_without:reason_report_id"
        ];
    }
}
