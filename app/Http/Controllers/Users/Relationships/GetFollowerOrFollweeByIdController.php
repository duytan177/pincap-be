<?php

namespace App\Http\Controllers\Users\Relationships;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Profiles\GetMyFollowerOrFolloweeRequest;
use App\Http\Resources\Users\Profiles\FollowCollection;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetFollowerOrFollweeByIdController extends Controller
{
    public function __invoke($userId, GetMyFollowerOrFolloweeRequest $request)
    {
        $relationship = $request->input("relationship");

        $user = User::findOrFail($userId);
        $followRelationship = $user->$relationship();
        $userAuthId = null;
        if ($relationship == "followers" && $token = $request->bearerToken()) {
            $userAuthId = JWTAuth::setToken($token)->authenticate()->getAttribute('id');
            $followRelationship->with("followers");
        }

        $followRelationship = $followRelationship
            ->whereDoesntHave('blockedUsers', function ($query) use ($userAuthId) {
                $query->where('follower_id', $userAuthId);
            })
            ->paginateOrAll($request);

        return FollowCollection::make($followRelationship);
    }
}
