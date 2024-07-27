<?php

namespace App\Http\Resources\Users\Profiles;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Users\Shared\UserSharedResource;
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

        if ($token = $this->getBearerToken($request)) {
            $userCurrent = $this->getUserFromToken($token);
        } else {
            $userCurrent = $request->user;
        }

        if ($relationship === "followers" && $userCurrent->getAttribute("id") != $this->resource->id) {
            $data['isFollowing'] = UserSharedResource::checkIsFollowing($userCurrent, $this->resource->id);
        }
        return $data;
    }
}
