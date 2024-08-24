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

        $reactionUser = $media->reactionUser()->with("feelings")->paginate($perPage, ['*'], 'page', $page);

        if ($token = $request->bearerToken()) {
            JWTAuth::setToken($token)->authenticate();
            $reactionUser->load("followers");
        }

        return UserFeelingCollection::make($reactionUser);
    }
}
