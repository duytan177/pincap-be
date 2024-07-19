<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Profiles\GetMyFollowerOrFolloweeRequest;
use App\Http\Resources\Users\Profiles\FollowCollection;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyFollowerOrFolloweeController extends Controller
{
    public function __invoke(GetMyFollowerOrFolloweeRequest $requests)
    {
        $relationship = $requests->input("relationship");
        $followRelationship = JWTAuth::user()->getAttribute($relationship);
        return new FollowCollection($followRelationship);
    }
}
