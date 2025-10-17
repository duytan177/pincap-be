<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Feelings\UserFeelingCollection;
use App\Models\Media;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetAllUserFeelingMediaController extends Controller
{
    public function __invoke($mediaId, PaginateRequest $request)
    {
        $perPage = $request->input("per_page", 15);
        $page = $request->input("page", 1);
        $media = Media::findOrFail($mediaId);
        $reactionUser = $media->reactionUser();
        $userId = null;

        if ($token = $request->bearerToken()) {
            $userId = JWTAuth::setToken($token)->authenticate()->getAttribute("id");
            $reactionUser->with("followers");
        }

        $reactionUser = $reactionUser
            ->with("feelings")
            ->whereDoesntHave('blockedUsers', function ($query) use ($userId) {
                $query->where('follower_id', $userId);
            })
            ->paginateOrAll($request);


        return UserFeelingCollection::make($reactionUser);
    }
}
