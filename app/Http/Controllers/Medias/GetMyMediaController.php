<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\MyMediaRequest;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyMediaController extends Controller
{
    public function __invoke(MyMediaRequest $request)
    {
        $userId = JWTAuth::user()->getAttribute("id");
        $isCreated = $request->validated("is_created") ?? true;

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $medias = Media::where([
            ['media_owner_id', $userId],
            ["is_created", $isCreated]
        ])->paginate($perPage, ['*'], 'page', $page);

        return MediaCollection::make($medias);
    }
}
