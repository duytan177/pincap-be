<?php

namespace App\Http\Resources\Albums;

use App\Components\Resources\BaseResource;
use App\Enums\Album_Media\AlbumRole;
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
        $data = $this->resource->only(self::$attributes);
        $data["albumRole"] = AlbumRole::getKey($this->resource->members[0]->pivot->album_role);
        return $data;
    }
}
