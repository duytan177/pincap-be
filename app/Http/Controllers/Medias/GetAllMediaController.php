<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\MediaCollection;
use App\Models\Media;
use Illuminate\Http\Request;

class GetAllMediaController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $medias = Media::where([
            ["is_created", true],
            ["privacy", Privacy::getValue("PUBLIC")]
        ])->paginate($perPage, ['*'], 'page', $page);

        return new MediaCollection($medias);
    }
}
