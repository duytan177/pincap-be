<?php

namespace App\Http\Resources\Medias\MediaDetail;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Users\Profiles\FollowResource as ProfilesFollowResource;
use App\Http\Resources\Users\Relationships\FollowResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaDetailResource extends BaseResource
{
    private static $attributes = [
        'id',
        'media_name',
        'media_url',
        'description',
        'type',
        'privacy',
        'is_created',
        'is_comment',
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        $user = User::withCount('followers')->find($this->resource->media_owner_id);

        if ((Auth::check())) {
            $request->merge(["relationship" => "followers"]);
            $data["ownerUser"] = new ProfilesFollowResource($user);
            $data["numberUserFollowers"] = $user->getAttribute("followers_count");
        } else {
            $data["ownerUser"] = new FollowResource($user);
            $data["numberUserFollowers"] = $user->getAttribute("followers_count");
        }

        return $data;
    }
}
