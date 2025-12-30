<?php

namespace App\Http\Resources\Admin\UserReports;

use App\Components\Resources\BaseResource;

class AdminUserReportResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getAttribute('id'),
            'report_state' => $this->getAttribute('report_state'),
            'user_id' => $this->getAttribute('user_id'),
            'user_report_id' => $this->getAttribute('user_report_id'),
            'reason_report_id' => $this->getAttribute('reason_report_id'),
            'other_reasons' => $this->getAttribute('other_reasons'),
            'created_at' => $this->getAttribute('created_at'),
            'updated_at' => $this->getAttribute('updated_at'),
            'deleted_at' => $this->getAttribute('deleted_at'),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->getAttribute('id'),
                    'first_name' => $this->user->getAttribute('first_name'),
                    'last_name' => $this->user->getAttribute('last_name'),
                    'email' => $this->user->getAttribute('email'),
                    'avatar' => $this->user->getAttribute('avatar'),
                ];
            }),
            'reporter' => $this->whenLoaded('reporter', function () {
                return [
                    'id' => $this->reporter->getAttribute('id'),
                    'first_name' => $this->reporter->getAttribute('first_name'),
                    'last_name' => $this->reporter->getAttribute('last_name'),
                    'email' => $this->reporter->getAttribute('email'),
                    'avatar' => $this->reporter->getAttribute('avatar'),
                ];
            }),
            'reason_report' => $this->whenLoaded('reasonReport', function () {
                return [
                    'id' => $this->reasonReport->getAttribute('id'),
                    'title' => $this->reasonReport->getAttribute('title'),
                    'description' => $this->reasonReport->getAttribute('description'),
                ];
            }),
        ];
    }
}

