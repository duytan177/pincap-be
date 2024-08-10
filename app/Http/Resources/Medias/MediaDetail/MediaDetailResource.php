<?php

namespace App\Http\Resources\Medias\MediaDetail;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Medias\Comments\CommentCollection;
use App\Http\Resources\Users\Profiles\FollowResource;
use App\Models\User;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

class MediaDetailResource extends BaseResource
{
    use SharedTrait;
    private static $attributes = [
        'id',
        'media_name',
        'media_url',
        'description',
        'type',
        'privacy',
        'is_created',
        'is_comment',
        "userComments"
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

        if ($this->getBearerToken($request)) {
            $request->merge(["relationship" => "followers"]);
        }

        $data["ownerUser"] = new FollowResource($user);
        $data["numberUserFollowers"] = $user->getAttribute("followers_count");
        $data["userComments"] =  CommentCollection::make($this->resource->userComments);
        return $data;
    }
}
