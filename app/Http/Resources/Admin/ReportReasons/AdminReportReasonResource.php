<?php

namespace App\Http\Resources\Admin\ReportReasons;

use App\Components\Resources\BaseResource;

class AdminReportReasonResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getAttribute('id'),
            'title' => $this->getAttribute('title'),
            'description' => $this->getAttribute('description'),
            'created_at' => $this->getAttribute('created_at'),
            'updated_at' => $this->getAttribute('updated_at'),
            'deleted_at' => $this->getAttribute('deleted_at'),
        ];
    }
}

