<?php

namespace App\Http\Resources\Medias\Comments;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Users\Profiles\FollowResource;
use App\Models\User;
use Illuminate\Http\Request;

class CommentResource extends BaseResource
{    private static $attributes = [
        "id",
        "first_name",
        "last_name",
        "avatar",
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        $data["content"] = $this->resource->pivot->content;
        $data["image"] = $this->resource->pivot->image_url;
        $data["created_at"] = $this->resource->pivot->created_at;

        return $data;
    }
}
