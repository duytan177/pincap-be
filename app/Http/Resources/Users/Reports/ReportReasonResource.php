<?php

namespace App\Http\Resources\Users\Reports;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class ReportReasonResource extends BaseResource
{
    private static $attributes = [
        'id',
        'title',
        "description"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->resource->only(self::$attributes);
    }
}
