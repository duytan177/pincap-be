<?php

namespace App\Http\Controllers\Users\Relationships;

use App\Enums\User\UserStatus;
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
        $userAuthId = null;
        if ($token = $request->bearerToken()) {
            $userAuthId = JWTAuth::setToken($token)->authenticate()->getAttribute('id');
        }

        // Build query từ User model thuần và join với user_relationship
        $query = User::query();

        if ($relationship === "followers") {
            // Lấy những user đang follow user được query
            $query->join('user_relationship', function ($join) use ($user) {
                $join->on('users.id', '=', 'user_relationship.follower_id')
                    ->where('user_relationship.followee_id', '=', $user->id)
                    ->where('user_relationship.user_status', '=', UserStatus::FOLLOWING);
            });
        } else {
            // Lấy những user mà user được query đang follow
            $query->join('user_relationship', function ($join) use ($user) {
                $join->on('users.id', '=', 'user_relationship.followee_id')
                    ->where('user_relationship.follower_id', '=', $user->id)
                    ->where('user_relationship.user_status', '=', UserStatus::FOLLOWING);
            });
        }

        // Filter out blocked users nếu có user đang login
        if ($userAuthId) {
            $query->whereNotExists(function ($subQuery) use ($userAuthId) {
                $subQuery->selectRaw('1')
                    ->from('user_relationship as blocked')
                    ->whereColumn('blocked.follower_id', 'users.id')
                    ->where('blocked.followee_id', '=', $userAuthId)
                    ->where('blocked.user_status', '=', UserStatus::BLOCK);
            });
        }

        // Select distinct users để tránh duplicate
        $query->select('users.*')->distinct();

        $followRelationship = $query->paginateOrAll($request);
        
        // Đánh dấu đây là profile người khác để resource xử lý đúng logic
        $request->merge(['is_my_profile' => false]);

        return FollowCollection::make($followRelationship);
    }
}
