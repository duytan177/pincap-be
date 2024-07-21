<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\MediaCollection;
use App\Models\Media;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyMediaController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = JWTAuth::user()->getAttribute("id");

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $medias = Media::where([
            ['media_owner_id', $userId],
            ["is_created", true]
        ])->paginate($perPage, ['*'], 'page', $page);

        return new MediaCollection($medias);
    }
}
