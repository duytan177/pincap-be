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
        $relationship = $request->input("relationship", "followers");
        $userCurrent = $this->getUserFromToken($this->getBearerToken($request));
        $isMyProfile = $request->input("is_my_profile", false);

        // Nếu là followees trong my profile → isFollowing = true (vì đây là những người user hiện tại đang follow)
        if ($isMyProfile && $relationship === "followees" && $userCurrent) {
            $data['isFollowing'] = true;
        } 
        // Nếu là followers trong my profile → check xem user hiện tại có follow user được lấy ra không
        elseif ($isMyProfile && $relationship === "followers" && $userCurrent && $userCurrent->getAttribute("id") != $this->resource->id) {
            $data['isFollowing'] = $userCurrent->followees()->where('followee_id', $this->resource->id)->exists();
        }
        // Nếu xem profile người khác → luôn check xem user đang login có follow user được lấy ra không
        elseif (!$isMyProfile && $userCurrent && $userCurrent->getAttribute("id") != $this->resource->id) {
            $data['isFollowing'] = $userCurrent->followees()->where('followee_id', $this->resource->id)->exists();
        } else {
            $data['isFollowing'] = false;
        }

        return $data;
    }
}
