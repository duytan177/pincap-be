<?php

namespace App\Http\Resources\Medias\Media;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class MediaResource extends BaseResource
{
    private static $attributes = [
        'id',
        'media_url',
        'media_name',
        "description",
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
