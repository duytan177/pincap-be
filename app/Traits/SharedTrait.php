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
        return $token ? JWTAuth::setToken($token)->toUser() : null;
    }

    public function checkReactionOfUserCurrent($request) {
        $token = $this->getBearerToken($request);
        $data = [];
        if ($token) {
            $request->merge(["relationship" => "followers"]);
        }

        if($token && !$this->resource->reactions->isEmpty() && $this->resource->reactions->contains("user_id", $this->getUserFromToken($token)->id)){
            $data["reaction"]["id"] = $this->resource->reactions[0]->id;
            $data["reaction"]["feeling_id"] = $this->resource->reactions[0]->feeling_id;
        }else{
            $data["reaction"] = null;
        }

        return [
            $request , $data
        ];
    }
}
