<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Medias\Comments\CommentCollection;
use App\Models\Comment;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetCommentsOfMediaDetailByIdController extends Controller
{
    public function __invoke($mediaId, PaginateRequest $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");
        $userId = null;
        if ($token = $request->bearerToken()) {
            $userId = JWTAuth::setToken($token)->authenticate()->getAttribute("id");
        }

        $comments = Comment::withCount("allFeelings")
            ->with(["feelings", "userComment.followers", "replies"])
            ->where("media_id", $mediaId)
            ->whereDoesntHave('userComment.blockedUsers', function ($query) use ($userId) {
                $query->where('follower_id', $userId);
            })
            ->paginate($perPage, ['*'], 'page', $page);

        return CommentCollection::make($comments);
    }
}
