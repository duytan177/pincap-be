<?php

namespace App\Http\Resources\Medias\Comments;

use App\Components\Resources\BaseResource;
use App\Http\Resources\Feelings\FeelingCollection;
use Illuminate\Http\Request;

class CommentResource extends BaseResource
{
    private static $attributes = [
        "id",
        "content",
        "image_url",
        "created_at",
        "feelings",
        'all_feelings_count'
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        if ($data["all_feelings_count"] === null && $data["all_feelings_count"] !== 0) {
            $data["all_feelings_count"] = $this->resource->allFeelings->count();
        }

        $data["name"] = $this->resource->userComment->first_name . " " . $this->resource->userComment->last_name;
        $currentUser = $request->user();

        if ($currentUser) {
            $isFollowing = $this->resource->userComment->followers->contains($currentUser->getAttribute("id"));
            $data['is_following'] = $isFollowing;
        }

        $data["user_id"] = $this->resource->userComment->id;
        $data["feelings"] = FeelingCollection::make($this->resource->feelings);

        if (isset($this->resource->replies)) {
            $data["replies_count"] = $this->resource->replies->count();
        }

        return $data;
    }
}
