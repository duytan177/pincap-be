<?php

namespace App\Http\Controllers\Tags;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tags\TagCollection;
use App\Models\Tag;

class GetAllTagController extends Controller
{
    public function __invoke()
    {
        $tags = Tag::withCount("medias")
                ->orderBy("medias_count", 'desc')
                ->with(['latestMedia'])
                ->take(10)->get();

        return TagCollection::make($tags);
    }
}
