<?php

namespace App\Http\Resources\Feelings;

use App\Components\Resources\BaseResource;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

class UserFeelingResource extends BaseResource
{
    use SharedTrait;

    private static $attributes = [
        'id',
        'first_name',
        "last_name",
        "avatar",
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userReaction = $this->resource->userReaction;
        $data = $userReaction->only(self::$attributes);

        $currentUser = $request->user();

        if ($currentUser) {
            $isFollowing = $userReaction->followers->contains($currentUser->getAttribute("id"));
            $data['is_following'] = $isFollowing;
        }

        return $data;
    }
}
