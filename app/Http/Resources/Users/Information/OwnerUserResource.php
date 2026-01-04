<?php

namespace App\Http\Resources\Users\Information;

use App\Components\Resources\BaseResource;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

class OwnerUserResource extends BaseResource
{
    use SharedTrait;

    private static $attributes = [
        'id',
        "email",
        "avatar",
        "followers_count"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = array_merge(
            $this->resource->only(self::$attributes),
            [
                'name' => trim(($this->resource->first_name ?? '') . ' ' . ($this->resource->last_name ?? ''))
            ]
        );

        // Check if current user is following this user
        $currentUser = $request->user();
        
        if ($currentUser && $currentUser->getAttribute("id") != $this->resource->id) {
            // Get following user IDs from collection additional data to avoid N+1 queries
            $followingUserIds = $request->input('following_user_ids', []);
            $data['isFollowing'] = in_array($this->resource->id, $followingUserIds);
        } else {
            $data['isFollowing'] = false;
        }

        return $data;
    }
}

