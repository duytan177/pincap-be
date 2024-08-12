<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Medias\Comments\CommentCollection;
use App\Models\Comment;

class GetReplyCommentByIdController extends Controller
{
    public function __invoke($commentId, PaginateRequest $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        $comment = Comment::with(["replies"])->findOrFail($commentId);
        $replies = $comment->replies()->paginate($perPage, ['*'], 'page', $page);

        return CommentCollection::make($replies);
    }
}
