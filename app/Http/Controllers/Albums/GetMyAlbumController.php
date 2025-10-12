<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Resources\Albums\AlbumCollection;
use App\Models\Album;
use App\Traits\OrderableTrait;
use Illuminate\Http\Request;

class GetMyAlbumController extends Controller
{
    use OrderableTrait;
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

        if ($request->input("media_id")) {
            $searches["media_id"] = $request->input("media_id");
        }

        $order = $this->getAttributeOrder($request->input("order_key"), $request->input("order_type"));
        $albums = Album::getList($searches, "", true, $order);
        $albums = $albums->withCount("medias")->paginate($perPage, ['*'], 'page', $page);

        return AlbumCollection::make($albums);
    }
}
