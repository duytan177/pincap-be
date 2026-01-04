<?php

namespace App\Http\Resources\Albums;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Users\Information\OwnerUserResource;
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
        "is_media_in_album",
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

        // Add ownerUser information
        if ($this->resource->relationLoaded('userOwner') && $this->resource->userOwner->isNotEmpty()) {
            $ownerUser = $this->resource->userOwner->first();
            $data['ownerUser'] = OwnerUserResource::make($ownerUser);
        } else {
            $data['ownerUser'] = null;
        }

        return $data;
    }
}
