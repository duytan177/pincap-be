<?php

namespace App\Http\Resources\Albums;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class AlbumResource extends BaseResource
{
    private static $attributes = [
        'id',
        'image_cover',
        'album_name',
        "description",
        "privacy",
        "medias_count",
        "created_at",
        "updated_at",
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        return $data;
    }
}
