<?php

namespace App\Http\Resources\Users\Relationships;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

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
        $userCurrent = $request->user;

        $relationship = $request->input("relationship");
        if ($relationship === "followers") {
            $followeesIds = $userCurrent->getAttribute('followees')->pluck('id')->toArray();
            $data['isFollowing'] = in_array($this->resource->id, $followeesIds);
        }
        return $data;
    }
}
