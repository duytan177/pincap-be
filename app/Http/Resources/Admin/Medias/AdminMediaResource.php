<?php

namespace App\Http\Resources\Admin\Medias;

use App\Components\Resources\BaseResource;

class AdminMediaResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getAttribute('id'),
            'media_name' => $this->getAttribute('media_name'),
            'media_url' => $this->getAttribute('media_url'),
            'description' => $this->getAttribute('description'),
            'type' => $this->getAttribute('type'),
            'privacy' => $this->getAttribute('privacy'),
            'is_created' => $this->getAttribute('is_created'),
            'is_comment' => $this->getAttribute('is_comment'),
            'is_policy_violation' => $this->getAttribute('is_policy_violation'),
            'safe_search_data' => $this->getAttribute('safe_search_data'),
            'media_owner_id' => $this->getAttribute('media_owner_id'),
            'created_at' => $this->getAttribute('created_at'),
            'updated_at' => $this->getAttribute('updated_at'),
            'deleted_at' => $this->getAttribute('deleted_at'),
            'reactions_count' => $this->getAttribute('reactions_count'),
            'comments_count' => $this->getAttribute('comments_count'),
            'albums_count' => $this->getAttribute('albums_count'),
            'user_owner' => $this->whenLoaded('userOwner', function () {
                return [
                    'id' => $this->userOwner->getAttribute('id'),
                    'first_name' => $this->userOwner->getAttribute('first_name'),
                    'last_name' => $this->userOwner->getAttribute('last_name'),
                    'email' => $this->userOwner->getAttribute('email'),
                    'avatar' => $this->userOwner->getAttribute('avatar'),
                ];
            }),
            'albums' => $this->whenLoaded('albums', function () {
                return $this->albums->map(function ($album) {
                    return [
                        'id' => $album->getAttribute('id'),
                        'album_name' => $album->getAttribute('album_name'),
                        'description' => $album->getAttribute('description'),
                    ];
                });
            }),
        ];
    }
}

