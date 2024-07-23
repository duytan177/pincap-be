<?php

namespace App\Http\Resources\Users\Profiles;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Users\Shared\UserSharedResource;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class FollowResource extends BaseResource
{
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
        $userCurrent = JWTAuth::user();
        $relationship = $request->input("relationship");

        if ($relationship === "followers" && $userCurrent->getAttribute("id") != $this->resource->id) {
            $data['isFollowing'] = UserSharedResource::checkIsFollowing($userCurrent, $this->resource->id);
        }
        return $data;
    }
}
