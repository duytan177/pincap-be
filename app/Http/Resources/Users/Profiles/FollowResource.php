<?php

namespace App\Http\Resources\Users\Profiles;

use App\Components\Resources\BaseResource;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

class FollowResource extends BaseResource
{
    use SharedTrait;

    private static $attributes = [
        'id',
        'first_name',
        "last_name",
        "email",
        "avatar"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);
        $relationship = $request->input("relationship");

        $userCurrent = $request->user();

        if ($relationship === "followers" && $userCurrent && $userCurrent->getAttribute("id") != $this->resource->id) {
            $data['isFollowing'] = $this->resource->followers->contains('id', $userCurrent->getAttribute("id"));
        }
        return $data;
    }
}
