<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Medias\Comments\CommentCollection;
use App\Models\Reply;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetReplyCommentByIdController extends Controller
{
    public function __invoke($commentId, PaginateRequest $request)
    {

        $userId = null;
        if ($token = $request->bearerToken()) {
            $userId = JWTAuth::setToken($token)->authenticate()->getAttribute("id");
        }

        $replies = Reply::withCount("allFeelings")
            ->with(["feelings", "userComment.followers"])
            ->whereDoesntHave('userComment.blockedUsers', function ($query) use ($userId) {
                $query->where('followee_id', $userId);
            })
            ->where("comment_id", $commentId)->paginateOrAll($request);

        return CommentCollection::make($replies);
    }
}
