<?php

namespace App\Http\Resources\Feelings;

use App\Components\Resources\BaseResource;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;
use App\Http\Resources\Feelings\FeelingResource;

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
        $userReaction = $this->resource->userReaction ?? $this->resource;
        $data = $userReaction->only(self::$attributes);

        $currentUser = $request->user();

        if ($currentUser) {
            $isFollowing = $userReaction->followers->contains($currentUser->getAttribute("id"));
            $data['is_following'] = $isFollowing;
        }

        if (isset($this->resource->feelings)) {
            $data["feeling"] = FeelingResource::make($userReaction->feelings);
        }

        return $data;
    }
}
