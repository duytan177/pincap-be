<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\SearchMediaRequest;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use App\Models\Tag;

class SearchMediaController extends Controller
{
    public function __invoke(SearchMediaRequest $request)
    {
        $searchText = $request->input("search");
        $perPage = $request->input('per_page', 15);
        $page = $request->input("page", 1);
        $search = '%' . $searchText . '%';
        $tagIds = Tag::where('tag_name', "like", $search)->pluck("id");

        $medias = Media::where([
            ["is_created", "=", true],
            ["privacy", "=", Privacy::PUBLIC]
        ])->where(function ($query) use ($search, $tagIds) {
            $query->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tag_id', $tagIds);
            })->orWhere('media_name', 'like', $search)
                ->orWhere('description', 'like', $search);
        })->paginate($perPage, ['*'], 'page', $page);

        return new MediaCollection($medias);
    }
}
