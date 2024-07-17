<?php

namespace App\Http\Resources\Users\Profiles;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class ProfileResource extends BaseResource
{
    private static $attributes = [
        'id',
        'firstName',
        "lastName",
        "email",
        "avatar",
        "background",
        "phone",
        "role",
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        $data["countFollowers"] = $this->resource->followers->count();
        $data["countFollowees"] = $this->resource->followees->count();

        return $data;
    }
}
