<?php

namespace App\Http\Resources\Users\Information;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class SenderResource extends BaseResource
{
    private static $attributes = [
        'id',
        'first_name',
        "last_name",
        "avatar",
        "role",
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
