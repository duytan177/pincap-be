<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Enums\User\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Profiles\GetMyFollowerOrFolloweeRequest;
use App\Http\Resources\Users\Profiles\FollowCollection;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyFollowerOrFolloweeController extends Controller
{
    public function __invoke(GetMyFollowerOrFolloweeRequest $request)
    {
        $relationship = $request->input("relationship");
        $user = JWTAuth::user();

        // Build query từ User model thuần và join với user_relationship
        $query = User::query();

        if ($relationship === "followers") {
            // Lấy những user đang follow user hiện tại
            $query->join('user_relationship', function ($join) use ($user) {
                $join->on('users.id', '=', 'user_relationship.follower_id')
                    ->where('user_relationship.followee_id', '=', $user->id)
                    ->where('user_relationship.user_status', '=', UserStatus::FOLLOWING);
            });
        } else {
            // Lấy những user mà user hiện tại đang follow
            $query->join('user_relationship', function ($join) use ($user) {
                $join->on('users.id', '=', 'user_relationship.followee_id')
                    ->where('user_relationship.follower_id', '=', $user->id)
                    ->where('user_relationship.user_status', '=', UserStatus::FOLLOWING);
            });
        }

        // Select distinct users để tránh duplicate
        $query->select('users.*')->distinct();

        $followRelationship = $query->paginateOrAll($request);

        // Đánh dấu đây là my profile để resource xử lý đúng logic
        $request->merge(['is_my_profile' => true]);

        return FollowCollection::make($followRelationship);
    }
}
