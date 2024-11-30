<?php

namespace App\Http\Resources\Medias\MediaDetail;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Feelings\FeelingCollection;
use App\Http\Resources\Medias\Comments\CommentResource;
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
        "userComments",
        "feelings",
        "reaction_user_count"
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

        if($this->getBearerToken($request) && !$this->resource->reactions->isEmpty()){
            $data["reaction"]["id"] = $this->resource->reactions[0]->id;
            $data["reaction"]["feeling_id"] = $this->resource->reactions[0]->feeling_id;
        }else{
            $data["reaction"] = null;
        }

        $data["ownerUser"] = FollowResource::make($user);
        $data["numberUserFollowers"] = $user->getAttribute("followers_count");
        $data["userComments"] = CommentResource::make($this->resource->comments->first());
        $data["commentCount"] = $this->resource->comments->count();
        $data["feelings"] = FeelingCollection::make($this->resource->feelings);

        return $data;
    }
}
