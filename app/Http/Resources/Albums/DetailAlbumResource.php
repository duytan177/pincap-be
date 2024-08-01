<?php

namespace App\Http\Resources\Albums;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Http\Resources\Users\Information\UserInfoCollection;
use Illuminate\Http\Request;

class DetailAlbumResource extends BaseResource
{
    private static $attributes = [
        'id',
        'image_cover',
        'album-name',
        "description",
        "privacy",
        'allUser',
        'medias'
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);
        $data["allUser"] = UserInfoCollection::make($data["allUser"]);
        $data["medias"] = MediaCollection::make($data["medias"]);
        return $data;
    }
}
