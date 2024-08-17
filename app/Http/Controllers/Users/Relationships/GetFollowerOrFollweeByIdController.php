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
        $perPage = $request->input("per_page");
        $page = $request->input("page");
        $relationship = $request->input("relationship");

        $user = User::findOrFail($userId);

        $followRelationship = $user->$relationship()->paginate($perPage, ['*'], 'page', $page);

        if ($relationship == "followers" && $token = $request->bearerToken()) {
            JWTAuth::setToken($token)->authenticate();
            $followRelationship->load("followers");
        }

        return FollowCollection::make($followRelationship);
    }
}
