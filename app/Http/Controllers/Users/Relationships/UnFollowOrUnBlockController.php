<?php

namespace App\Http\Controllers\Users\Relationships;

use App\Enums\User\UserStatus;
use App\Exceptions\Users\RelationshipException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Relationships\FollowOrBlockRequest;
use App\Models\UserRelationship;
use Tymon\JWTAuth\Facades\JWTAuth;

class UnFollowOrUnBlockController extends Controller
{
    public function __invoke(FollowOrBlockRequest $request)
    {
        $requestData = $request->validated();
        $followeeId = $requestData["followeeId"];
        $status = $requestData["status"];
        $followerId = JWTAuth::user()->getAttribute("id");

        if ($this->unFollowOrUnBlock($followerId, $followeeId, $status)) {
            return responseWithMessage("Un" . strtolower($status) . " successfully");
        }

        throw RelationshipException::unFollowOrBlock($status);
    }

    private function unFollowOrUnBlock($followerId, $followeeId, $status)
    {
        return UserRelationship::where([
            ["follower_id", $followerId],
            ["followee_id", $followeeId],
            ['user_status', UserStatus::getValue($status)]
        ])->delete();
    }
}
