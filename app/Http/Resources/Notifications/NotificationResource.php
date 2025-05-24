<?php

namespace App\Http\Resources\Notifications;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class NotificationResource extends BaseResource
{
    private static $attributes = [
        'id',
        'title',
        "content",
        "is_read",
        "link",
        "notification_type",
        "created_at"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        $data['sender'] = $this->whenLoaded('sender', function () {
            return [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'avatar' => $this->sender->avatar,
            ];
        });

        return $data;
    }
}
