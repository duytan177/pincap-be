<?php

namespace App\Traits;

use Tymon\JWTAuth\Facades\JWTAuth;

trait SharedTrait
{
    public function getBlockedUserIds($request)
    {
        $blockedUserIds = [];

        if (($token = $this->getBearerToken($request))) {
            $user =$this->getUserFromToken($token);
            $blockedUserIds = $user->blockedUsers()->get()->pluck("id");
        }

        return $blockedUserIds;
    }

    public function applyBlockedUsersFilter($query, $blockedUserIds)
    {
        return $query->whereNotIn('media_owner_id', $blockedUserIds);
    }


    public function getBearerToken($request)
    {
        return $request->bearerToken();
    }

    public function getUserFromToken($token)
    {
        return JWTAuth::setToken($token)->toUser();
    }
}
