<?php

namespace App\Http\Controllers\Users\Relationships;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Profiles\GetMyFollowerOrFolloweeRequest;
use App\Http\Resources\Users\Relationships\FollowCollection;
use App\Models\User;

class GetFollowerOrFollweeByIdController extends Controller
{
    public function __invoke($userId, GetMyFollowerOrFolloweeRequest $requests)
    {
        $relationship = $requests->input("relationship");
        $user = User::findOrFail($userId);
        $requests->merge(["user" => $user]);
        $followRelationship = $user->getAttribute($relationship);
        return new FollowCollection($followRelationship);
    }
}
