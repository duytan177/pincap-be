<?php

namespace App\Http\Resources\Admin\MediaReports;

use App\Components\Resources\BaseResource;
use App\Services\S3PresignedUrlService;

class AdminMediaReportResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getAttribute('id'),
            'report_state' => $this->getAttribute('report_state'),
            'user_id' => $this->getAttribute('user_id'),
            'media_id' => $this->getAttribute('media_id'),
            'reason_report_id' => $this->getAttribute('reason_report_id'),
            'other_reasons' => $this->getAttribute('other_reasons'),
            'created_at' => $this->getAttribute('created_at'),
            'updated_at' => $this->getAttribute('updated_at'),
            'deleted_at' => $this->getAttribute('deleted_at'),
            'user_report' => $this->whenLoaded('userReport', function () {
                return [
                    'id' => $this->userReport->getAttribute('id'),
                    'first_name' => $this->userReport->getAttribute('first_name'),
                    'last_name' => $this->userReport->getAttribute('last_name'),
                    'email' => $this->userReport->getAttribute('email'),
                    'avatar' => $this->userReport->getAttribute('avatar'),
                ];
            }),
            'report_media' => $this->whenLoaded('reportMedia', function () {
                $mediaUrl = $this->reportMedia->getAttribute('media_url');
                return [
                    'id' => $this->reportMedia->getAttribute('id'),
                    'media_name' => $this->reportMedia->getAttribute('media_name'),
                    'media_url' => $mediaUrl ? S3PresignedUrlService::convert($mediaUrl) : null,
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

