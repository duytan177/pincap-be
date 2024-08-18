<?php

namespace App\Http\Resources\Users\Profiles;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class ProfileResource extends BaseResource
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
        "followees_count"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        if (($userAuth = $request->user()) && $userAuth->getAttribute("id") != $this->resource->id) {
            $data["isFollowing"] = $this->resource->followers->contains("id", $userAuth->getAttribute("id"));
        }

        return $data;
    }
}
