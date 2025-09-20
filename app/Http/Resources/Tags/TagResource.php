<?php

namespace App\Http\Resources\Tags;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class TagResource extends BaseResource
{
    private static $attributes = [
        'id',
        'tag_name',
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        if (!filter_var($request->input('tag_flg'), FILTER_VALIDATE_BOOLEAN)) {
            $data["latestMediaUrl"] = $this->resource->latestMedia[0]->getAttribute("media_url");
        }

        return $data;
    }
}
