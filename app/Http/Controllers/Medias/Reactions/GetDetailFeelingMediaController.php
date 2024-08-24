<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Feelings\UserFeelingCollection;
use App\Models\ReactionMedia;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetDetailFeelingMediaController extends Controller
{
    public function __invoke($mediaId, $feelingId, PaginateRequest $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        if ($token = $request->bearerToken()) {
            JWTAuth::setToken($token)->authenticate();
        }

        $query = ReactionMedia::where([
            ["media_id", '=', $mediaId],
            ["feeling_id", '=', $feelingId]
        ]);

        if ($token) {
            $query->with('userReaction.followers:id');
        } else {
            $query->with('userReaction');
        }

        $reactionMedia = $query->paginate($perPage, ['*'], 'page', $page);
        return UserFeelingCollection::make($reactionMedia);
    }
}
