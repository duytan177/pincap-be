<?php

namespace App\Http\Resources\Admin\Users;

use App\Components\Resources\BaseResource;

class AdminUserResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getAttribute('id'),
            'first_name' => $this->getAttribute('first_name'),
            'last_name' => $this->getAttribute('last_name'),
            'email' => $this->getAttribute('email'),
            'phone' => $this->getAttribute('phone'),
            'role' => $this->getAttribute('role'),
            'avatar' => $this->getAttribute('avatar'),
            'background' => $this->getAttribute('background'),
            'email_verified_at' => $this->getAttribute('email_verified_at'),
            'created_at' => $this->getAttribute('created_at'),
            'updated_at' => $this->getAttribute('updated_at'),
            'deleted_at' => $this->getAttribute('deleted_at'),
        ];
    }
}

