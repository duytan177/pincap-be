<?php

namespace App\Http\Resources\Users\Profiles;

use App\Components\Resources\BaseResource;
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
        if ($relationship === "followers") {
            $followeesIds = $userCurrent->getAttribute('followees')->pluck('id')->toArray();
            $data['is_following'] = in_array($this->resource->id, $followeesIds);
        }
        return $data;
    }
}
