<?php

namespace App\Http\Resources\Users\Shared;

class UserSharedResource
{
    public static function checkIsFollowing($user, $followerId)
    {
        $followeesIds = $user->getAttribute('followees')->pluck('id')->toArray();
        return in_array($followerId, $followeesIds);
    }

    public static function countFollowers($user)
    {
        return $user->followers()->count();
    }
}
