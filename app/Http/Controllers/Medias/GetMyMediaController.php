<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\MyMediaRequest;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyMediaController extends Controller
{
    public function __invoke(MyMediaRequest $request)
    {
        $isCreated = $request->validated("is_created") ?? true;

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $query = $request->input("query");
        $searches = [];
        if (!empty($query)){
            $searches = [
                "title" => $query,
                "description" => $query,
                "user_name" => $query,
                "tag_name" => $query
            ];
        }
        $medias = Media::getList($searches, $isCreated, Privacy::PUBLIC,true)->with("reactions");

        $medias = $medias->paginate( $perPage, ['*'], 'page', $page);

        return MediaCollection::make($medias);
    }
}
