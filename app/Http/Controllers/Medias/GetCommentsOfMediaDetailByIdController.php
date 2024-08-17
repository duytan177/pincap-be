<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Medias\Comments\CommentCollection;
use App\Models\Comment;

class GetCommentsOfMediaDetailByIdController extends Controller
{
    public function __invoke($mediaId, PaginateRequest $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        $comments = Comment::withCount("feelings")
            ->with(["feelings", "userComment", "allFeelings"])
            ->where("media_id", $mediaId)->paginate($perPage, ['*'], 'page', $page);

        return CommentCollection::make($comments);
    }
}
