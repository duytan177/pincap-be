<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\MediaByUserIdRequest;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;

class GetListMediaByUserIdController extends Controller
{
    public function __invoke(MediaByUserIdRequest $request)
    {
        $userId = $request->input("user_id");
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        $medias = Media::where([
            ['media_owner_id', $userId],
            ["is_created", true],
            ["privacy", Privacy::PUBLIC]
        ])->paginate($perPage, ['*'], 'page', $page);

        return MediaCollection::make($medias);
    }
}
