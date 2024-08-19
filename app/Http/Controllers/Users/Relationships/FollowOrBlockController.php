<?php

namespace App\Http\Controllers\Users\Relationships;

use App\Enums\User\UserStatus;
use App\Events\UserFollowedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Relationships\FollowOrBlockRequest;
use App\Models\UserRelationship;
use Tymon\JWTAuth\Facades\JWTAuth;

class FollowOrBlockController extends Controller
{
    public function __invoke(FollowOrBlockRequest $request)
    {
        $requestData = $request->validated();
        $followeeId = $requestData["followeeId"];
        $status = $requestData["status"];
        $followerId = JWTAuth::user()->getAttribute("id");

        $this->followOrBlock($followerId, $followeeId, $status);

        if ($status == UserStatus::getKey("1")) {
            event(new UserFollowedEvent($followeeId, $followerId));
        }

        return responseWithMessage(strtolower($status) . " successfully");
    }

    private function followOrBlock($followerId, $followeeId, $status)
    {
        UserRelationship::updateOrCreate([
            'follower_id' => $followerId,
            'followee_id' => $followeeId
        ], [
            "user_status" => UserStatus::getValue($status)
        ]);
    }
}
