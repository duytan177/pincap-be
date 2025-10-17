<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Profiles\GetMyFollowerOrFolloweeRequest;
use App\Http\Resources\Users\Profiles\FollowCollection;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyFollowerOrFolloweeController extends Controller
{
    public function __invoke(GetMyFollowerOrFolloweeRequest $request)
    {
        $relationship = $request->input("relationship");
        $user = JWTAuth::user();

        $followRelationship = $user->$relationship();

        if ($relationship === "followers") {
            $followRelationship->with([
                'followers' => function ($query) use ($user) {
                    $query->where('follower_id', $user->getAttribute('id'));
                }
            ]);
        }

        $followRelationship = $followRelationship->paginateOrAll($request);

        return FollowCollection::make($followRelationship);
    }
}
