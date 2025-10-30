<?php

namespace App\Http\Resources\Albums;

use App\Components\Resources\BaseResource;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

class AlbumMediaResource extends BaseResource
{
    use SharedTrait;

    private static $attributes = [
        'id',
        'media_url',
        'media_name',
        "description",
        "is_created",
        "type",
        "media_owner_id",
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get base media attributes (same as MediaResource)
        $data = $this->resource->only(self::$attributes);

        // Add user_created information from join
        $data['user_created'] = [
            'id' => $this->resource->user_created_id ?? null,
            'name' => $this->resource->name ?? null,
            'avatar' => $this->resource->avatar ?? null,
            'email' => $this->resource->email ?? null,
        ];

        return $data;
    }
}
