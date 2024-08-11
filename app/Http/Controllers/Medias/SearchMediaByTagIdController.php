<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\SearchMediaRequest;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;

class SearchMediaByTagIdController extends Controller
{
    public function __invoke($tagId, SearchMediaRequest $request)
    {
        $perPage = $request->input('per_page');
        $page = $request->input("page");

        $medias = Media::where([
            ["is_created", "=", true],
            ["privacy", "=", Privacy::PUBLIC]
        ])->whereHas('tags', function ($query) use ($tagId) {
            $query->where('tag_id', $tagId);
        })->paginate($perPage, ['*'], 'page', $page);

        return MediaCollection::make($medias);
    }
}
