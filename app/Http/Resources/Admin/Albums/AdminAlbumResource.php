<?php

namespace App\Http\Resources\Admin\Albums;

use App\Components\Resources\BaseResource;

class AdminAlbumResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getAttribute('id'),
            'album_name' => $this->getAttribute('album_name'),
            'image_cover' => $this->getAttribute('image_cover'),
            'description' => $this->getAttribute('description'),
            'privacy' => $this->getAttribute('privacy'),
            'created_at' => $this->getAttribute('created_at'),
            'updated_at' => $this->getAttribute('updated_at'),
            'deleted_at' => $this->getAttribute('deleted_at'),
            'medias_count' => $this->getAttribute('medias_count'),
            'user_owner' => $this->whenLoaded('userOwner', function () {
                $owner = $this->userOwner->first();
                if (!$owner) {
                    return null;
                }
                return [
                    'id' => $owner->getAttribute('id'),
                    'first_name' => $owner->getAttribute('first_name'),
                    'last_name' => $owner->getAttribute('last_name'),
                    'email' => $owner->getAttribute('email'),
                    'avatar' => $owner->getAttribute('avatar'),
                ];
            }),
        ];
    }
}

