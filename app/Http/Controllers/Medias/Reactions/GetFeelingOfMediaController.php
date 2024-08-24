<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Feelings\FeelingCollection;
use App\Http\Resources\Feelings\UserFeelingCollection;
use App\Models\Media;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetFeelingOfMediaController extends Controller
{
    public function __invoke($mediaId, PaginateRequest $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");
        $media = Media::with("allFeelings")->findOrFail($mediaId);

        $reactionUser = $media->reactionUser()->with("feelings")->paginate($perPage, ['*'], 'page', $page);

        if ($token = $request->bearerToken()) {
            JWTAuth::setToken($token)->authenticate();
            $reactionUser->load("followers");
        }

        return[
            "all_user_feelings" => UserFeelingCollection::make($reactionUser),
            "feelings" => FeelingCollection::make($media->allFeelings)
        ];
    }
}
