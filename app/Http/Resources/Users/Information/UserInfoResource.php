<?php

namespace App\Http\Resources\Users\Information;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class UserInfoResource extends BaseResource
{
    private static $attributes = [
        'id',
        'first_name',
        "last_name",
        "email",
        "avatar",
        "followers_count"
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
