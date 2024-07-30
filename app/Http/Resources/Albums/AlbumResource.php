<?php

namespace App\Http\Resources\Albums;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class AlbumResource extends BaseResource
{
    private static $attributes = [
        'id',
        'image_cover',
        'album-name',
        "description",
        "privacy"
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
