<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Medias\Comments\CommentCollection;
use App\Models\Media;

class GetCommentsOfMediaDetailByIdController extends Controller
{
    public function __invoke($mediaId, PaginateRequest $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        $media = Media::findOrFail($mediaId);
        $comments = $media->userComments()->paginate($perPage, ['*'], 'page', $page);

        return CommentCollection::make($comments);
    }
}
