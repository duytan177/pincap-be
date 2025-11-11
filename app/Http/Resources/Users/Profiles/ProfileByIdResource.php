<?php

namespace App\Http\Resources\Users\Profiles;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class ProfileByIdResource extends BaseResource
{
    private static $attributes = [
        'id',
        'first_name',
        "last_name",
        "email",
        "avatar",
        "background",
        "phone",
        "role",
        "followers_count",
        "followees_count",
        "medias_count",
        "reaction_media_count",
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);
        $userAuth = $request->user();
        if ($userAuth && $userAuth->getAttribute("id") != $this->resource->id) {
            $data["isFollowing"] = $this->resource->followers->contains("id", $userAuth->getAttribute("id"));
        }
        return $data;
    }
}
