<?php

namespace App\Http\Resources\Medias\MediaDetail;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Feelings\FeelingCollection;
use App\Http\Resources\Medias\Comments\CommentResource;
use App\Http\Resources\Tags\TagCollection;
use App\Http\Resources\Users\Profiles\FollowResource;
use App\Models\User;
use App\Services\S3PresignedUrlService;
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
        "reaction_user_count",
        "safe_search_data",
        "is_policy_violation",
        "permalink"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        // Convert media_url to presigned URLs
        if (isset($data['media_url'])) {
            $data['media_url'] = S3PresignedUrlService::convert($data['media_url']);
        }

        $user = User::withCount('followers')->find($this->resource->media_owner_id);

        [$request, $result]= $this->checkReactionOfUserCurrent($request);
        $data += $result;

        $data["ownerUser"] = FollowResource::make($user);
        $data["numberUserFollowers"] = $user->getAttribute("followers_count");
        $data["userComments"] = CommentResource::make($this->resource->comments->first());
        $data["commentCount"] = $this->resource->comments->count();
        $data["feelings"] = FeelingCollection::make($this->resource->feelings);

        // Optional include tags when tag_flg=true
        if (filter_var($request->input('tag_flg'), FILTER_VALIDATE_BOOLEAN)) {
            $data["tags"] = TagCollection::make($this->resource->tags);
        }

        return $data;
    }
}
