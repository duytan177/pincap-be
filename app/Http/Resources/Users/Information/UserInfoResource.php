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
        "followers_count"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = array_merge(
            $this->resource->only(self::$attributes),
            [
                'name' => trim(($this->resource->first_name ?? '') . ' ' . ($this->resource->last_name ?? ''))
            ]
        );

        // If album_id exists then have field status
        if ($this->resource->getAttributes() && array_key_exists('status', $this->resource->getAttributes())) {
            $data['status'] = $this->resource->status;
        }

        // If album_id exists then have field status
        if ($this->resource->getAttributes() && array_key_exists('album_role', $this->resource->getAttributes())) {
            $data['album_role'] = $this->resource->album_role;
        }

        return $data;
    }
}
