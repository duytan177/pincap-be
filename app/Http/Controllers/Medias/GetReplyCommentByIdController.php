<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Medias\Comments\CommentCollection;
use App\Models\Reply;

class GetReplyCommentByIdController extends Controller
{
    public function __invoke($commentId, PaginateRequest $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        $replies = Reply::withCount("feelings")
            ->with(["feelings", "userComment", "allFeelings"])
            ->where("comment_id", $commentId)->paginate($perPage, ['*'], 'page', $page);

        return CommentCollection::make($replies);
    }
}
