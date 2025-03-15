<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Albums\AlbumCollection;
use App\Models\Album;
use Illuminate\Http\Request;

class GetAllAlbumController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        $searches = [];
        $query = $request->input("query");
        if (!empty($query)) {
            $searches = [
                "album_name" => $query,
                "description" => $query
            ];
        }
        $albums = Album::getList($searches, Privacy::PUBLIC);
        $albums = $albums->withCount("medias")->paginate($perPage, ['*'], 'page', $page);

        return AlbumCollection::make($albums);
    }
}
