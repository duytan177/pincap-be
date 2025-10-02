<?php

namespace App\Http\Resources\Users\Information;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class UserInfoResource extends BaseResource
{
    private static $attributes = [
        'id',
        "email",
        "avatar",
        "followers_count",
        "invitation_status"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            $this->resource->only(self::$attributes),
            [
                'name' => trim(($this->resource->first_name ?? '') . ' ' . ($this->resource->last_name ?? ''))
            ]
        );
    }
}
